<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\EconomicData;

class GDPController extends Controller
{
    public function index($country)
    {
        $country = Country::where(
            'country_name',
            $country
        )->firstOrFail();

        $gdp = EconomicData::where(
                'country_id',
                $country->id
            )
            ->orderBy('year')
            ->get();

        return view(
            'countries.gdp',
            compact(
                'country',
                'gdp'
            )
        );
    }
}