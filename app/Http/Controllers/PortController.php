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

        $ports = Port::with('country');

        // Filter berdasarkan negara
        if ($request->filled('country')) {

            $country = Country::where(
                'country_name',
                $request->country
            )->first();

            if ($country) {

                $ports->where(
                    'country_id',
                    $country->id
                );
            }
        }

        // Search nama pelabuhan
        if ($request->filled('search')) {

            $ports->where(
                'port_name',
                'LIKE',
                '%' . $request->search . '%'
            );
        }

        $ports = $ports->get();
        
$totalPorts = $ports->count();

$totalCountries = $ports
    ->pluck('country_id')
    ->unique()
    ->count();

$totalLocations = $ports
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->count();

        return view('ports.index', compact(
    'ports',
    'countries',
    'totalPorts',
    'totalCountries',
    'totalLocations'
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