<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\Http;


class CountryController extends Controller
{
    public function index()
{
    $countries = Country::orderBy('country_name')->get();

    $country = $countries->first();

    $economic = $country?->economicData()
                    ->latest('year')
                    ->first();

    $exchangeRate = CurrencyController::getRate(
        $country->currency
    );

    $riskScore = $country->riskScore->total_score ?? 0;

    $riskLevel = $country->riskScore->risk_level ?? 'Low Risk';

    // Data tren riwayat 5 tahun
    $historicalEconomic = $country?->economicData()
        ->orderBy('year', 'desc')
        ->take(5)
        ->get()
        ->sortBy('year');

    $gdpTrend = json_encode($historicalEconomic?->pluck('gdp')->toArray() ?? []);
    $inflationTrend = json_encode($historicalEconomic?->pluck('inflation')->toArray() ?? []);
    $trendYears = json_encode($historicalEconomic?->pluck('year')->toArray() ?? []);

    return view('countries.index',compact(
        'countries',
        'country',
        'economic',
        'exchangeRate',
        'riskScore',
        'riskLevel',
        'gdpTrend',
        'inflationTrend',
        'trendYears'
    ));
}

public function sync()
{
    // ===========================
    // World Bank
    // ===========================

    $wb = Http::withoutVerifying()
        ->get('https://api.worldbank.org/v2/country?format=json&per_page=400')
        ->json();

    // ===========================
    // Rest Countries
    // ===========================

    $rest = Http::withoutVerifying()
    ->withHeaders([
        'Accept' => 'application/json',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36'
    ])
    ->get('https://api.allorigins.win/raw?url=https://restcountries.com/v3.1/all') 
    ->json();

    foreach($rest as $country){
        $code = $country['cca2'] ?? null;
        if(!$code){
            continue;
        }
        $restCountries[strtoupper($code)] = $country;
    }

    foreach($wb[1] as $item){

        if(
            empty($item['capitalCity']) ||
            $item['region']['value']=="Aggregates"
        ){
            continue;
        }

        $iso2 = strtoupper($item['iso2Code']);
        $extra = $restCountries[$iso2] ?? [];

        $updateData = [
            'country_name'=>$item['name'],
            'iso3'=>$item['id'],
            'capital'=>$item['capitalCity'],
            'region'=>$item['region']['value'],
        ];

        if (!empty($extra)) {
            $updateData['currency'] = array_key_first($extra['currencies'] ?? []);
            $updateData['currency_code'] = array_key_first($extra['currencies'] ?? []);
            $updateData['population'] = $extra['population'] ?? null;
            $updateData['flag'] = $extra['flags']['png'] ?? '';
            $updateData['latitude'] = $extra['latlng'][0] ?? null;
            $updateData['longitude'] = $extra['latlng'][1] ?? null;
        }

        Country::updateOrCreate(
            ['country_code' => $iso2],
            $updateData
        );
    }

    return redirect()->route('countries')
        ->with('success','Countries berhasil disinkronkan.');
}

public function show($country_name)
{
    $countries = Country::orderBy('country_name')->get();

    $country = Country::where('country_name', $country_name)
                ->with('economicData', 'riskScore')
                ->firstOrFail();

                if (empty($country->population)) {
        $wbController = new \App\Http\Controllers\WorldBankController();
        $pop = $wbController->getIndicator($country->country_code, 'SP.POP.TOTL');
        if ($pop) {
            $country->population = $pop;
            $country->save();
        }
    }

    $economic = $country->economicData()->latest('year')->first();

    $exchangeRate = CurrencyController::getRate($country->currency);

    $riskScore = $country->riskScore->total_score ?? 0;

    $riskLevel = $country->riskScore->risk_level ?? 'Low Risk';

    // Data tren riwayat 5 tahun
    $historicalEconomic = $country->economicData()
        ->orderBy('year', 'desc')
        ->take(5)
        ->get()
        ->sortBy('year');

    $gdpTrend = json_encode($historicalEconomic->pluck('gdp')->toArray());
    $inflationTrend = json_encode($historicalEconomic->pluck('inflation')->toArray());
    $trendYears = json_encode($historicalEconomic->pluck('year')->toArray());

    return view('countries.index', compact(
        'countries',
        'country',
        'economic',
        'exchangeRate',
        'riskScore',
        'riskLevel',
        'gdpTrend',
        'inflationTrend',
        'trendYears'
    ));
}

}