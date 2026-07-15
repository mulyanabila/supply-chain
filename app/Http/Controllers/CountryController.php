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

    return view('countries.index',compact(
        'countries',
        'country',
        'economic',
        'exchangeRate',
        'riskScore',
        'riskLevel'
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
    ->get('https://restcountries.com/v3.1/all?fields=name,cca2,currencies,population,flags,latlng')
    ->json();
        dd($rest);

    $restCountries = [];

    foreach($rest as $country){

        $code = $country['cca2'] ?? null;

        if(!$code){
            continue;
        }

        $restCountries[$code] = $country;
    }

    foreach($wb[1] as $item){

        if(
            empty($item['capitalCity']) ||
            $item['region']['value']=="Aggregates"
        ){
            continue;
        }

        $extra = $restCountries[$item['iso2Code']] ?? [];

        Country::updateOrCreate(

            [
                'country_code'=>$item['iso2Code']
            ],

            [

                'country_name'=>$item['name'],

                'iso3'=>$item['id'],

                'capital'=>$item['capitalCity'],

                'region'=>$item['region']['value'],

                'currency'=>array_key_first(
                    $extra['currencies'] ?? []
                ),

                'currency_code'=>array_key_first(
                    $extra['currencies'] ?? []
                ),

                'population'=>$extra['population'] ?? null,

                'flag'=>$extra['flags']['png'] ?? '',

                'latitude'=>$extra['latlng'][0] ?? null,

                'longitude'=>$extra['latlng'][1] ?? null,

            ]

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

    $economic = $country->economicData()->latest('year')->first();

    $exchangeRate = CurrencyController::getRate($country->currency);

    $riskScore = $country->riskScore->total_score ?? 0;

    $riskLevel = $country->riskScore->risk_level ?? 'Low Risk';

    return view('countries.index', compact(
        'countries',
        'country',
        'economic',
        'exchangeRate',
        'riskScore',
        'riskLevel'
    ));
}

}