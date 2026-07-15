<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function getCurrency($code)
    {
        $response = Http::withoutVerifying()
            ->get("https://open.er-api.com/v6/latest/USD");

        if(!$response->successful()){
            return response()->json([]);
        }

        $json = $response->json();

        return response()->json([

            "currency"=>$code,

            "rate"=>$json["rates"][$code] ?? null

        ]);
    }

    public static function getRate($currency)
{
    $response = Http::withoutVerifying()
        ->get("https://open.er-api.com/v6/latest/USD");

    if(!$response->successful()){
        return 1;
    }

    $json = $response->json();

    return $json['rates'][$currency] ?? 1;
}
}