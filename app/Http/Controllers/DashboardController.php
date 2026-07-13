<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\EconomicData;
use Illuminate\Support\Facades\Http;


class DashboardController extends Controller
{
   public function index()
{
    $countries = Country::with('economicData')
        ->orderBy('country_name')
        ->get();

    // Pilih negara pertama yang benar-benar punya data ekonomi
    $selectedCountry = Country::with('economicData')
        ->whereHas('economicData')
        ->first();

    $totalCountries = Country::count();

    $highRisk = 0;
    $mediumRisk = 0;
    $lowRisk = 0;

    return view('dashboard', compact(
        'countries',
        'selectedCountry',
        'totalCountries',
        'highRisk',
        'mediumRisk',
        'lowRisk'
    ));
}

    public function countryDetail($id)
{
    $country = Country::with('economicData')->findOrFail($id);

    $economic = $country->economicData()->latest('year')->first();

    $data = $country->economicData()->latest('year')->first();

$gdp = $data->gdp ?? 0;
$inflation = $data->inflation ?? 0;
$exports = $data->exports ?? 0;
$imports = $data->imports ?? 0;

/*
|--------------------------------------------------------------------------
| Hitung Risk Score
|--------------------------------------------------------------------------
*/

$risk = 0;

/*
GDP besar = lebih aman
*/

if($gdp < 10000000000){
    $risk += 30;
}elseif($gdp < 100000000000){
    $risk += 20;
}else{
    $risk += 10;
}

/*
Inflasi tinggi = lebih berisiko
*/

if($inflation > 8){
    $risk += 30;
}elseif($inflation > 4){
    $risk += 20;
}else{
    $risk += 10;
}

/*
Export kecil = lebih berisiko
*/

if($exports < 5000000000){
    $risk += 20;
}else{
    $risk += 10;
}

/*
Import terlalu besar = lebih berisiko
*/

if($imports > $exports){
    $risk += 20;
}else{
    $risk += 10;
}

if($risk >= 80){

    $status = "High Risk";

}elseif($risk >= 50){

    $status = "Medium Risk";

}else{

    $status = "Low Risk";

}

return response()->json([

    'country_name'=>$country->country_name,

    'country_code'=>$country->country_code,

    'capital'=>$country->capital,

    'region'=>$country->region,

    'latitude'=>$country->latitude,

    'longitude'=>$country->longitude,

    'population' => $country->population,

    'gdp'=>$gdp,

    'inflation'=>$inflation,

    'exports'=>$exports,

    'imports'=>$imports,

    'risk_score'=>$risk,

    'risk_status'=>$status

]);
}
}