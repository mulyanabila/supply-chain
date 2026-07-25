<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class PortController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::orderBy('country_name')->get();

        // Get distinct types and statuses for dropdown filters
        $portTypes = Port::select('type')->distinct()->whereNotNull('type')->orderBy('type')->pluck('type');
        $portStatuses = ['Active', 'Busy', 'Congested', 'Maintenance', 'Closed'];

        $portsQuery = Port::with('country');

        // Filter by country
        if ($request->filled('country')) {
            $country = Country::where('country_name', $request->country)->first();
            if ($country) {
                $portsQuery->where('country_id', $country->id);
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $portsQuery->where('type', $request->type);
        }

        // Filter by status mapped from operational status to database size status
        if ($request->filled('status')) {
            $statusMap = [
                'Active' => 'Large',
                'Busy' => 'Medium',
                'Congested' => 'Small',
                'Maintenance' => 'Very Small',
                'Closed' => 'Closed'
            ];
            $mappedStatus = $statusMap[$request->status] ?? $request->status;
            $portsQuery->where('status', $mappedStatus);
        }

        // Search port name
        if ($request->filled('search')) {
            $portsQuery->where('port_name', 'LIKE', '%' . $request->search . '%');
        }

        $ports = $portsQuery->get();
        
        // Calculate dynamic KPIs based on filtered ports
        $totalPorts = $ports->count();
        $activePorts = $ports->filter(fn($p) => in_array($p->status, ['Large', 'Active', 'Normal']))->count();
        $busyPorts = $ports->filter(fn($p) => in_array($p->status, ['Medium', 'Busy']))->count();
        
        // We define "High Risk" as Busy (Medium), Closed (Closed), or Maintenance (Very Small) status
        $highRiskPorts = $ports->filter(fn($p) => in_array($p->status, ['Medium', 'Very Small', 'Closed', 'Busy', 'Maintenance']))->count();
        
        $countriesCovered = $ports->pluck('country_id')->unique()->count();

        return view('ports.index', compact(
            'ports',
            'countries',
            'portTypes',
            'portStatuses',
            'totalPorts',
            'activePorts',
            'busyPorts',
            'highRiskPorts',
            'countriesCovered'
        ));
    }

    public function sync()
{
    $file = storage_path('app/ports/WPI.csv');

    if (!file_exists($file)) {
        return back()->with('error','File WPI.csv tidak ditemukan.');
    }

    DB::table('ports')->truncate();

    $handle = fopen($file,'r');

    // Skip Header
    fgetcsv($handle);

    while(($row = fgetcsv($handle)) !== false){

        // CSV punya 109 kolom
        if(count($row) < 109){
            continue;
        }

        $countryName = trim($row[6]);

        $country = Country::where('country_name',$countryName)->first();

        if(!$country){
            continue;
        }

        Port::create([

            'country_id' => $country->id,

            'port_name' => trim($row[3]),

            'city' => null,

            'latitude' => $row[107],

            'longitude' => $row[108],

            'type' => trim($row[29]),

            'status' => trim($row[28]),

        ]);
    }

    fclose($handle);

    return redirect()
            ->route('ports')
            ->with('success','Port berhasil diimport.');
}
}