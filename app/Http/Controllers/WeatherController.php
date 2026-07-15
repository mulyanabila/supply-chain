<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
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

    public static function getWeatherData($lat,$lon)
{
    $url="https://api.open-meteo.com/v1/forecast?latitude=$lat&longitude=$lon&current=weather_code";

    $response = Http::withoutVerifying()->get($url);

    if(!$response->successful()){
        return null;
    }

    return $response->json()['current'];
}
}