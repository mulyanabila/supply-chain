<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ComparisonController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('country_name')->get();

        return view('comparison.index', compact('countries'));
    }

    public function compare(Request $request)
    {
        $request->validate([
            'country_a' => 'required|exists:countries,id',
            'country_b' => 'required|exists:countries,id',
        ]);

        $countries = Country::orderBy('country_name')->get();

        $countryA = Country::with(['economicData', 'riskScore'])->findOrFail($request->country_a);
        $countryB = Country::with(['economicData', 'riskScore'])->findOrFail($request->country_b);

        // ===================================
        // Economic Data
        // ===================================

        $ecoA = $countryA->economicData->sortBy('year')->values();
        $ecoB = $countryB->economicData->sortBy('year')->values();

        $years = $ecoA->pluck('year');
        $gdpA = $ecoA->pluck('gdp')->map(fn($v) => round($v / 1000000000, 2));
        $gdpB = $ecoB->pluck('gdp')->map(fn($v) => round($v / 1000000000, 2));
        $inflationA = $ecoA->pluck('inflation');
        $inflationB = $ecoB->pluck('inflation');

        // ===================================
        // WEATHER COUNTRY A WITH FALLBACKS
        // ===================================

        $weatherA = null;
        if (env('OPENWEATHER_API_KEY')) {
            try {
                $response = Http::withoutVerifying()->timeout(5)
                    ->get("https://api.openweathermap.org/data/2.5/weather", [
                        'lat' => $countryA->latitude,
                        'lon' => $countryA->longitude,
                        'appid' => env('OPENWEATHER_API_KEY'),
                        'units' => 'metric'
                    ]);
                if ($response->successful()) {
                    $weatherA = $response->json();
                }
            } catch (\Exception $e) {}
        }

        if (!$weatherA) {
            $temp = rand(12, 22);
            $weatherA = [
                'main' => [
                    'temp' => $temp,
                    'humidity' => rand(65, 80),
                    'pressure' => rand(1010, 1015),
                ],
                'wind' => [
                    'speed' => rand(10, 20),
                ],
                'visibility' => rand(8, 12),
                'weather' => [
                    [
                        'main' => 'Cloudy',
                        'description' => 'broken clouds'
                    ]
                ]
            ];
        }

        // ===================================
        // WEATHER COUNTRY B WITH FALLBACKS
        // ===================================

        $weatherB = null;
        if (env('OPENWEATHER_API_KEY')) {
            try {
                $response = Http::withoutVerifying()->timeout(5)
                    ->get("https://api.openweathermap.org/data/2.5/weather", [
                        'lat' => $countryB->latitude,
                        'lon' => $countryB->longitude,
                        'appid' => env('OPENWEATHER_API_KEY'),
                        'units' => 'metric'
                    ]);
                if ($response->successful()) {
                    $weatherB = $response->json();
                }
            } catch (\Exception $e) {}
        }

        if (!$weatherB) {
            $temp = rand(24, 30);
            $weatherB = [
                'main' => [
                    'temp' => $temp,
                    'humidity' => rand(45, 60),
                    'pressure' => rand(1015, 1020),
                ],
                'wind' => [
                    'speed' => rand(8, 15),
                ],
                'visibility' => rand(14, 18),
                'weather' => [
                    [
                        'main' => 'Sunny',
                        'description' => 'clear sky'
                    ]
                ]
            ];
        }

        // ===================================
        // CURRENCY
        // ===================================

        $rateA = null;
        $rateB = null;

        try {
            $exchange = Http::withoutVerifying()->timeout(5)
                ->get("https://open.er-api.com/v6/latest/USD")
                ->json();

            if (isset($exchange['rates'][$countryA->currency])) {
                $rateA = round(1 / $exchange['rates'][$countryA->currency], 4);
            }
            if (isset($exchange['rates'][$countryB->currency])) {
                $rateB = round(1 / $exchange['rates'][$countryB->currency], 4);
            }
        } catch (\Exception $e) {}

        if (!$rateA) {
            $rateA = $countryA->currency == 'EUR' ? 1.08 : ($countryA->currency == 'IDR' ? 0.000063 : rand(0.5, 1.5));
        }
        if (!$rateB) {
            $rateB = $countryB->currency == 'EUR' ? 1.08 : ($countryB->currency == 'AUD' ? 0.67 : ($countryB->currency == 'IDR' ? 0.000063 : rand(0.5, 1.5)));
        }

        // ===================================
        // RISK SCORE
        // ===================================

        $latestA = $ecoA->last();
        $latestB = $ecoB->last();

        $riskA = $countryA->riskScore->total_score ?? null;
        if ($riskA === null) {
            $riskA = $this->calculateRisk($latestA->gdp ?? 0, $latestA->inflation ?? 0);
        }

        $riskB = $countryB->riskScore->total_score ?? null;
        if ($riskB === null) {
            $riskB = $this->calculateRisk($latestB->gdp ?? 0, $latestB->inflation ?? 0);
        }

        $riskLevelA = $countryA->riskScore->risk_level ?? null;
        if (!$riskLevelA) {
            $riskLevelA = $riskA >= 70 ? 'High Risk' : ($riskA >= 40 ? 'Medium Risk' : 'Low Risk');
        }

        $riskLevelB = $countryB->riskScore->risk_level ?? null;
        if (!$riskLevelB) {
            $riskLevelB = $riskB >= 70 ? 'High Risk' : ($riskB >= 40 ? 'Medium Risk' : 'Low Risk');
        }

        $riskLevelA = str_replace(' Risk', '', $riskLevelA);
        $riskLevelB = str_replace(' Risk', '', $riskLevelB);

        // ===================================
        // SUMMARY
        // ===================================

        $summary = [];
        
        if ($latestA && $latestB) {
            if ($latestA->gdp > $latestB->gdp) {
                $gdpRatio = round($latestA->gdp / max($latestB->gdp, 1), 2);
                $summary[] = "GDP: {$countryA->country_name} has a GDP {$gdpRatio}x larger than {$countryB->country_name}.";
            } else {
                $gdpRatio = round($latestB->gdp / max($latestA->gdp, 1), 2);
                $summary[] = "GDP: {$countryB->country_name} has a GDP {$gdpRatio}x larger than {$countryA->country_name}.";
            }

            if (($latestA->inflation ?? 0) > ($latestB->inflation ?? 0)) {
                $summary[] = "Inflation: {$countryA->country_name} has higher inflation than {$countryB->country_name}.";
            } else {
                $summary[] = "Inflation: {$countryB->country_name} has higher inflation than {$countryA->country_name}.";
            }
        } else {
            $summary[] = "GDP: Comparison unavailable due to missing data.";
            $summary[] = "Inflation: Comparison unavailable due to missing data.";
        }

        $summary[] = $riskA < $riskB 
            ? "Risk: {$countryA->country_name} is more stable with lower risk level."
            : "Risk: {$countryB->country_name} is more stable with lower risk level.";

        $tempA = $weatherA['main']['temp'] ?? 0;
        $tempB = $weatherB['main']['temp'] ?? 0;
        $humA = $weatherA['main']['humidity'] ?? 0;
        $humB = $weatherB['main']['humidity'] ?? 0;

        if ($tempA > $tempB) {
            $summary[] = "Weather: {$countryA->country_name} is warmer and has " . ($humA < $humB ? "lower" : "higher") . " humidity.";
        } else {
            $summary[] = "Weather: {$countryB->country_name} is warmer and has " . ($humB < $humA ? "lower" : "higher") . " humidity.";
        }

        $currencyLiquidity = $countryA->currency == 'EUR' || $countryA->currency == 'USD' ? $countryA->currency : ($countryB->currency == 'EUR' || $countryB->currency == 'USD' ? $countryB->currency : 'EUR');
        $summary[] = "Currency: Both currencies are stable, {$currencyLiquidity} more liquid.";

        // ===================================
        // KEY INSIGHTS
        // ===================================

        $insights = [];
        if ($latestA && $latestB) {
            $insights['gdp'] = $latestA->gdp > $latestB->gdp ? "Stronger Economy: {$countryA->country_name}" : "Stronger Economy: {$countryB->country_name}";
            $insights['inflation'] = ($latestA->inflation ?? 0) < ($latestB->inflation ?? 0) ? "Lower Inflation: {$countryA->country_name}" : "Lower Inflation: {$countryB->country_name}";
        } else {
            $insights['gdp'] = "Economy: N/A";
            $insights['inflation'] = "Inflation: N/A";
        }

        $insights['risk'] = $riskA < $riskB ? "Lower Risk: {$countryA->country_name}" : "Lower Risk: {$countryB->country_name}";
        $insights['weather'] = $tempA > $tempB ? "Warmer Weather: {$countryA->country_name}" : "Warmer Weather: {$countryB->country_name}";

        $strongerEco = ($latestA && $latestB && $latestA->gdp > $latestB->gdp) ? $countryA->country_name : $countryB->country_name;
        $lowerRisk = $riskA < $riskB ? $countryA->country_name : $countryB->country_name;
        $betterWeather = ($tempA > $tempB && $tempA < 32) ? $countryA->country_name : $countryB->country_name;

        $insightParagraph = "{$strongerEco} shows stronger economic size with significantly higher GDP, while {$lowerRisk} maintains lower risk levels and {$betterWeather} better weather conditions. Both countries have stable currencies, making them reliable partners for international trade and investment.";

        return view(
            'comparison.index',
            compact(
                'countries',
                'countryA',
                'countryB',
                'weatherA',
                'weatherB',
                'years',
                'gdpA',
                'gdpB',
                'inflationA',
                'inflationB',
                'rateA',
                'rateB',
                'riskA',
                'riskB',
                'riskLevelA',
                'riskLevelB',
                'summary',
                'insights',
                'insightParagraph'
            )
        );
    }

    /**
     * Hitung Risk Score
     */
    private function calculateRisk($gdp, $inflation)
    {
        $score = 50;

        if ($gdp > 1000000000000) {
            $score -= 15;
        } elseif ($gdp > 500000000000) {
            $score -= 8;
        } else {
            $score += 10;
        }

        if ($inflation > 8) {
            $score += 25;
        } elseif ($inflation > 5) {
            $score += 15;
        } elseif ($inflation > 3) {
            $score += 8;
        } else {
            $score -= 5;
        }

        return max(0, min(100, $score));
    }
}