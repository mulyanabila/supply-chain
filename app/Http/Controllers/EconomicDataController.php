<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Country;
use App\Models\EconomicData;

class EconomicDataController extends Controller
{
    public function sync()
    {
        // Ambil semua negara yang punya iso3
        $countries = Country::whereNotNull('iso3')->get();

        foreach ($countries as $country) {

            // Ambil data GDP 5 tahun terakhir
            $url = "https://api.worldbank.org/v2/country/{$country->iso3}/indicator/NY.GDP.MKTP.CD?format=json&per_page=5";

            $response = Http::withoutVerifying()
                ->timeout(60)
                ->get($url);

            if (!$response->successful()) {
                continue;
            }

            $data = $response->json();

            if (!isset($data[1])) {
                continue;
            }

            foreach ($data[1] as $item) {

                // Skip jika tidak ada nilai GDP
                if ($item['value'] === null) {
                    continue;
                }

                EconomicData::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'year'       => $item['date'],
                    ],
                    [
                        'gdp' => $item['value'],
                    ]
                );
            }
        }

        return redirect()
            ->route('economic.index')
            ->with('success', 'GDP 5 tahun terakhir berhasil disinkronisasi.');
    }

    public function index()
    {
        $economicData = EconomicData::with('country')
            ->orderBy('year', 'desc')
            ->get();

        return view('economic.index', compact('economicData'));
    }
}