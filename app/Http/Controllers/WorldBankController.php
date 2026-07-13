<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WorldBankController extends Controller
{
    private function getIndicator($countryCode, $indicator)
    {
        $url = "https://api.worldbank.org/v2/country/$countryCode/indicator/$indicator?format=json&mrv=10";

        $response = Http::withoutVerifying()
            ->timeout(10)
            ->get($url);

        if (!$response->successful()) {
            return null;
        }

        $json = $response->json();

        if(isset($json[1])){

    foreach($json[1] as $row){

        if($row['value'] !== null){

            return $row['value'];

        }

    }

}

return null;
    }

    public function getEconomicData($countryCode)
    {
        return response()->json([

            'gdp' => $this->getIndicator($countryCode,'NY.GDP.MKTP.CD'),

            'inflation' => $this->getIndicator($countryCode,'FP.CPI.TOTL.ZG'),

            'population' => $this->getIndicator($countryCode,'SP.POP.TOTL'),

        ]);
    }
}