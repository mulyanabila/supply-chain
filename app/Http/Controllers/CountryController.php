<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\Http;


class CountryController extends Controller
{
    public function index()
{
    $countries = Country::all();

    return view('countries.index', compact('countries'));
}

public function sync()
{
    $response = Http::withoutVerifying()
        ->get('https://api.worldbank.org/v2/country?format=json&per_page=400');

    if (!$response->successful()) {
        return back()->with('error', 'Gagal mengambil data negara.');
    }

    $data = $response->json();

    // Data negara berada pada index ke-1
    if (!isset($data[1])) {
        return back()->with('error', 'Format data World Bank tidak sesuai.');
    }

    foreach ($data[1] as $item) {

    // Lewati data yang bukan negara
    if (
        empty($item['capitalCity']) ||
        $item['region']['value'] === 'Aggregates'
    ) {
        continue;
    }

    Country::updateOrCreate(
        [
            'country_code' => $item['iso2Code']
        ],
        [
            'country_name' => $item['name'],
            'iso3' => $item['id'],
            'capital' => $item['capitalCity'],
            'region' => $item['region']['value'],
            'currency' => '',
            'currency_code' => '',
            'population' => null,
            'flag' => '',
            'latitude' => null,
            'longitude' => null,
        ]
    );

}

    return redirect()->route('countries')
        ->with('success', 'Data negara berhasil diambil dari World Bank.');
}

}