<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Country;

class WeatherController extends Controller
{
    public function index($country_name = 'Germany')
    {
        $countries = Country::orderBy('country_name')->get();

        $country = Country::where('country_name', $country_name)->first();
        if (!$country) {
            $country = Country::first();
        }

        return view('weather.index', compact('countries', 'country'));
    }

    public function getWeather($lat,$lon)
    {
        $url="https://api.open-meteo.com/v1/forecast?latitude=$lat&longitude=$lon&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m";

        $response = Http::withoutVerifying()
        ->timeout(20)
        ->get($url);

        if(!$response->successful()){
            return response()->json([]);
        }

        return response()->json(
            $response->json()['current']
        );
    }

    public static function getWeatherData($lat, $lon)
{
    $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,weather_code";

    $response = Http::withoutVerifying()
        ->timeout(20)
        ->get($url);

    if (!$response->successful()) {
        return null;
    }

    $current = $response->json()['current'];

    return [
        'temperature' => $current['temperature_2m'] ?? null,
        'weather_code' => $current['weather_code'] ?? null,
    ];
}
}