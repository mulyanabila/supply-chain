<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\Port;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardNewsController;

class DashboardController extends Controller
{
  public function index()
{
    $totalCountries = Country::count();

    $totalPorts = Port::count();

    $averageRisk = RiskScore::avg('total_score') ?? 0;

    $highRiskCountry = RiskScore::with('country')
        ->orderByDesc('total_score')
        ->first();

    $lowRiskCountry = RiskScore::with('country')
        ->orderBy('total_score')
        ->first();

    // tambahkan ini
    $economicRecords = \App\Models\EconomicData::count();
    $weatherAlerts = 0;
    $newsCount = 0;
    $latestNews = DashboardNewsController::latest();

    return view('dashboard', compact(
    'totalCountries',
    'totalPorts',
    'averageRisk',
    'highRiskCountry',
    'lowRiskCountry',
    'economicRecords',
    'weatherAlerts',
    'newsCount',
    'latestNews'
));
}

    public function countryDetail($id)
    {
        $country = Country::with([
            'economicData',
            'ports'
        ])->findOrFail($id);

        $data = $country->economicData()
            ->latest('year')
            ->first();

        $gdp = $data->gdp ?? 0;
        $inflation = $data->inflation ?? 0;
        $exports = $data->exports ?? 0;
        $imports = $data->imports ?? 0;

        /*
        ==========================================
        WEATHER
        ==========================================
        */

        if (
            empty($country->latitude) ||
            empty($country->longitude)
        ) {

            $weather = [
                'weather_code' => 0
            ];

        } else {

            try {

                $weather = WeatherController::getWeatherData(
                    $country->latitude,
                    $country->longitude
                );

                if (!$weather) {
                    $weather = [
                        'weather_code' => 0
                    ];
                }

            } catch (\Exception $e) {

                $weather = [
                    'weather_code' => 0
                ];

            }

        }

        /*
        ==========================================
        NEWS
        ==========================================
        */

        try {

            $sentiment = NewsController::getSentiment(
                $country->country_name
            );

        } catch (\Exception $e) {

            $sentiment = "Neutral";

        }

        /*
        ==========================================
        CURRENCY
        ==========================================
        */

        try {

            $exchangeRate = CurrencyController::getRate(
                $country->currency
            );

        } catch (\Exception $e) {

            $exchangeRate = 1;

        }

        /*
        ==========================================
        HITUNG RISK
        ==========================================
        */

        $risk = $this->calculateRisk(
            $country,
            $weather,
            $sentiment,
            $exchangeRate
        );

        return response()->json([

            'country_name' => $country->country_name,

            'country_code' => $country->country_code,

            'currency' => $country->currency,

            'capital' => $country->capital,

            'region' => $country->region,

            'latitude' => $country->latitude,

            'longitude' => $country->longitude,

            'population' => (int) $country->population,

            'gdp' => $gdp,

            'inflation' => $inflation,

            'exports' => $exports,

            'imports' => $imports,

            'weather_code' => $weather['weather_code'],

            'sentiment' => $sentiment,

            'exchange_rate' => $exchangeRate,

            'risk_score' => $risk['total'],

            'risk_status' => $risk['level'],

            'ports' => $country->ports,

        ]);
    }

    /*
    =====================================================
    WEIGHTED RISK MODEL
    =====================================================
    */

    public function calculateRisk(
        $country,
        $weather,
        $sentiment,
        $exchangeRate
    )
    {
        $data = $country->economicData()
            ->latest('year')
            ->first();

        $inflation = $data->inflation ?? 0;

        /*
        ==========================================
        WEATHER
        ==========================================
        */

        $weatherCode = $weather['weather_code'] ?? 0;

        switch ($weatherCode) {

            case 61:
            case 63:
            case 65:

                $weatherScore = 20;
                break;

            case 95:
            case 96:
            case 99:

                $weatherScore = 30;
                break;

            default:

                $weatherScore = 5;
        }

        /*
        ==========================================
        INFLATION
        ==========================================
        */

        if ($inflation < 2) {

            $inflationScore = 5;

        } elseif ($inflation < 5) {

            $inflationScore = 10;

        } elseif ($inflation < 8) {

            $inflationScore = 15;

        } else {

            $inflationScore = 20;

        }

        /*
        ==========================================
        NEWS
        ==========================================
        */

        switch ($sentiment) {

            case "Positive":
                $newsScore = 5;
                break;

            case "Neutral":
                $newsScore = 20;
                break;

            default:
                $newsScore = 40;
        }

        /*
        ==========================================
        CURRENCY
        ==========================================
        */

        if ($exchangeRate > 100) {

            $currencyScore = 20;

        } elseif ($exchangeRate > 10) {

            $currencyScore = 15;

        } elseif ($exchangeRate > 1) {

            $currencyScore = 10;

        } else {

            $currencyScore = 5;

        }

        /*
        ==========================================
        TOTAL
        ==========================================
        */

        $total =
            $weatherScore +
            $inflationScore +
            $newsScore +
            $currencyScore;

        if ($total >= 70) {

            $level = "High Risk";

        } elseif ($total >= 40) {

            $level = "Medium Risk";

        } else {

            $level = "Low Risk";

        }

        /*
        ==========================================
        SIMPAN KE DATABASE
        ==========================================
        */

        RiskScore::updateOrCreate(

            [
                'country_id' => $country->id
            ],

            [
                'weather_score' => $weatherScore,
                'inflation_score' => $inflationScore,
                'news_score' => $newsScore,
                'currency_score' => $currencyScore,
                'total_score' => $total,
                'risk_level' => $level
            ]

        );

        return [

            'total' => $total,

            'level' => $level

        ];
    }
}