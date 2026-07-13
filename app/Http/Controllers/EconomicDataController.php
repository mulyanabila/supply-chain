<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\EconomicData;

class EconomicDataController extends Controller
{
    public function sync()
    {
        return redirect()->route('dashboard')
            ->with('success', 'Gunakan perintah php artisan economic:sync untuk sinkronisasi data.');
    }

    public function index()
{
    $economicData = EconomicData::with('country')
        ->orderBy('year', 'desc')
        ->get();

    return view('economic.index', compact('economicData'));
}
}