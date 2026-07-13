<x-app-layout>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

body{
    background:#f5f7fb;
}

/* =======================
        SIDEBAR
======================= */

.sidebar{
    width:240px;
    height:100vh;
    background:#18864B;
    color:white;
    position:fixed;
    left:0;
    top:0;
    padding:30px 20px;
}

.logo{
    text-align:center;
    font-size:28px;
    font-weight:bold;
    margin-bottom:40px;
}

.sidebar ul{
    list-style:none;
    padding:0;
}

.sidebar li{
    padding:15px;
    margin-bottom:10px;
    border-radius:10px;
    transition:.3s;
    cursor:pointer;
}

.sidebar li:hover{
    background:rgba(255,255,255,.15);
}

.sidebar li.active{
    background:white;
    color:#18864B;
    font-weight:bold;
}

.sidebar i{
    margin-right:10px;
}

.sidebar a{

    display:block;

    color:white;

    text-decoration:none;

}

.sidebar a:hover{

    color:white;

}

.sidebar li.active a{

    color:#18864B;

    font-weight:bold;

}

.sidebar button{

    width:100%;

    border:none;

    background:none;

    color:white;

    text-align:left;

    padding:0;

}

.sidebar button:hover{

    color:white;

}

/* =======================
        CONTENT
======================= */

.main-content{
    margin-left:240px;
    padding:30px;
}

/* =======================
        TOPBAR
======================= */

.topbar{
    background:white;
    border-radius:15px;
    padding:18px 30px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    margin-bottom:25px;
}

.card-modern{
    background:white;
    border:none;
    border-radius:20px;
    box-shadow:0 5px 18px rgba(0,0,0,.08);
}

#map{
    height:520px;
    border-radius:18px;
}

</style>


<div class="sidebar">

    <div class="logo">
        🌍 SCRI
    </div>

    <ul>

        <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>

        <li class="{{ request()->routeIs('countries') ? 'active' : '' }}">
            <a href="{{ route('countries') }}">
                <i class="bi bi-globe2"></i>
                Countries
            </a>
        </li>

        <li class="{{ request()->routeIs('economic.*') ? 'active' : '' }}">
            <a href="{{ route('economic.index') }}">
                <i class="bi bi-graph-up"></i>
                Economic
            </a>
        </li>

        <li>
            <a href="#">
                <i class="bi bi-cloud-sun"></i>
                Weather
            </a>
        </li>

        <li>
            <a href="#">
                <i class="bi bi-newspaper"></i>
                News
            </a>
        </li>

        <li>

            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button type="submit">

                    <i class="bi bi-box-arrow-right"></i>

                    Logout

                </button>

            </form>

        </li>

    </ul>

</div>

<div class="main-content">

    <!-- TOPBAR -->

    <div class="topbar d-flex justify-content-between align-items-center">

        <div>

            <h3 class="fw-bold mb-0">
                Global Supply Chain Risk Intelligence
            </h3>

            <small class="text-secondary">
                Dashboard Monitoring
            </small>

        </div>

        <div>

            <span class="me-3 fw-bold">

                {{ Auth::user()->name }}

            </span>

            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}"

            width="45"

            class="rounded-circle">

        </div>

    </div>


    <!-- ISI DASHBOARD -->

    <div class="row g-4">

    <!-- MAP -->
    <div class="col-lg-7">

        <div class="card-modern p-4">

            <h5 class="fw-bold mb-3">
                🌍 Live Tracking Area
            </h5>

            <div id="map"></div>

        </div>

    </div>

    <div class="score-card">

    <i class="bi bi-shield-fill-check"></i>

    <small class="text-light">
        OVERALL RISK SCORE
    </small>

    <h1 class="display-2 fw-bold" id="riskScore">
        45
    </h1>

    <span id="riskStatus" class="badge bg-warning mb-3">
        Medium Risk
    </span>

    <h4 id="countryName">
        {{ $selectedCountry->country_name }}
    </h4>

    <p>
        {{ $selectedCountry->country_code }}
    </p>

</div>

<div class="row mt-3">

    <div class="col-md-6">

        <div class="info-card">

            <div class="info-icon green">

                <i class="bi bi-currency-dollar"></i>

            </div>

            <span class="badge bg-success">
                Economic
            </span>

            <h2 id="inflation">

                {{ optional($selectedCountry->economicData->first())->inflation ?? '-' }}%

            </h2>

            <p class="text-secondary">

                Inflation Status

            </p>

        </div>

    </div>

    <div class="col-md-6">

        <div class="info-card">

            <div class="info-icon blue">

                <i class="bi bi-graph-up-arrow"></i>

            </div>

            <span class="badge bg-primary">

                GDP

            </span>

            <h2 id="gdp">

                {{ number_format(optional($selectedCountry->economicData->first())->gdp ?? 0) }}

            </h2>

            <p class="text-secondary">

                Gross Domestic Product

            </p>

        </div>

    </div>

</div>

<div class="mt-3">

    <div class="info-card">

        <div class="info-icon red">

            <i class="bi bi-newspaper"></i>

        </div>

        <span class="badge bg-danger">

            Geopolitics

        </span>

        <h2>

            Negative

        </h2>

        <p class="text-secondary">

            News Sentiment

        </p>

    </div>

</div>

<div class="mt-4">

    <label class="fw-bold mb-2">

        Country

    </label>

    <select id="countrySelect" class="form-select">

        @foreach($countries as $country)

            <option value="{{ $country->id }}">

                {{ $country->country_name }}

            </option>

        @endforeach

    </select>

</div>

<div class="info-card mt-4">

<table class="table table-borderless align-middle">

    <tr>

<th>Population</th>

<td id="population">

{{ number_format($selectedCountry->population) }}

</td>

</tr>

<tr>

<th style="width:35%">Capital</th>

<td id="capital">

{{ $selectedCountry->capital }}

</td>

</tr>

<tr>

<th>Region</th>

<td id="region">

{{ $selectedCountry->region }}

</td>

</tr>

<tr>

<th>GDP</th>

<td id="gdpTable">
    {{ number_format(optional($selectedCountry->economicData->first())->gdp ?? 0) }}
</td>

</tr>

<tr>

<th>Inflation</th>

<td id="inflationTable">
    {{ optional($selectedCountry->economicData->first())->inflation ?? '-' }}%
</td>

</tr>

<tr>

<th>Exports</th>

<td id="exports">

{{ optional($selectedCountry->economicData->first())->exports }}

</td>

</tr>

<tr>

<th>Imports</th>

<td id="imports">

{{ optional($selectedCountry->economicData->first())->imports }}

</td>

</tr>

</table>

</div>

    <div class="row mt-4">

        <div class="col-lg-8">

            <div class="card-modern p-4">

    <h5 class="fw-bold mb-3">
        Economic Trend
    </h5>

    <canvas id="trendChart"></canvas>

</div>

        </div>

        <div class="col-lg-4">

            <div class="card-modern p-4">

    <h5 class="fw-bold mb-3">
        ☁ Weather
    </h5>

    <h2 id="temperature">
        -
    </h2>

    <p id="weatherText">
        Loading...
    </p>

    <hr>

    <div class="row text-center">

        <div class="col">

            <small>Humidity</small>

            <h6 id="humidity">-</h6>

        </div>

        <div class="col">

            <small>Wind</small>

            <h6 id="wind">-</h6>

        </div>

    </div>

</div>

        </div>

    </div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

var map = L.map('map').setView([
{{ $selectedCountry->latitude ?? 0 }},
{{ $selectedCountry->longitude ?? 0 }}
],3);

L.tileLayer(
'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
{
attribution:'© OpenStreetMap'
}).addTo(map);


// =======================
// MARKER
// =======================

var marker=L.marker([
{{ $selectedCountry->latitude ?? 0 }},
{{ $selectedCountry->longitude ?? 0 }}
]).addTo(map);


// =======================
// WEATHER
// =======================

function loadWeather(lat,lon){

    if(lat==null || lon==null) return;

    fetch('/weather/'+lat+'/'+lon)

    .then(res=>res.json())

    .then(weather=>{

        if(document.getElementById("temperature"))
        document.getElementById("temperature").innerHTML=
        weather.temperature_2m+" °C";

        if(document.getElementById("humidity"))
        document.getElementById("humidity").innerHTML=
        weather.relative_humidity_2m+"%";

        if(document.getElementById("wind"))
        document.getElementById("wind").innerHTML=
        weather.wind_speed_10m+" km/h";

    });

}

loadWeather(
{{ $selectedCountry->latitude ?? 0 }},
{{ $selectedCountry->longitude ?? 0 }}
);


// =======================
// CHART
// =======================

const ctx=document.getElementById("trendChart");

const chart=new Chart(ctx,{

type:'bar',

data:{

labels:[
"GDP",
"Inflation",
"Exports",
"Imports"
],

datasets:[{

label:"Economic",

data:[

{{ optional($selectedCountry->economicData->first())->gdp ?? 0 }},

{{ optional($selectedCountry->economicData->first())->inflation ?? 0 }},

{{ optional($selectedCountry->economicData->first())->exports ?? 0 }},

{{ optional($selectedCountry->economicData->first())->imports ?? 0 }}

]

}]

},

options:{

responsive:true,

plugins:{
legend:{
display:false
}
}

}

});


// =======================
// DROPDOWN
// =======================

document.getElementById("countrySelect").addEventListener("change",function(){

let id=this.value;

fetch("/country/"+id)

.then(res=>res.json())

.then(data=>{

console.log(data);


// COUNTRY

if(document.getElementById("countryName"))
document.getElementById("countryName").innerHTML=data.country_name;

if(document.getElementById("capital"))
document.getElementById("capital").innerHTML=data.capital;

if(document.getElementById("region"))
document.getElementById("region").innerHTML=data.region;


// EXPORT IMPORT

if(document.getElementById("exports"))
document.getElementById("exports").innerHTML=
Number(data.exports ?? 0).toLocaleString();

if(document.getElementById("imports"))
document.getElementById("imports").innerHTML=
Number(data.imports ?? 0).toLocaleString();


// MAP

if(data.latitude!=null && data.longitude!=null){

marker.setLatLng([

data.latitude,

data.longitude

]);

map.setView([

data.latitude,

data.longitude

],5);

loadWeather(

data.latitude,

data.longitude

);

}


// WORLD BANK

fetch("/worldbank/"+data.country_code)

.then(res=>res.json())

.then(eco=>{

    document.getElementById("population").innerHTML =
    Number(eco.population ?? 0).toLocaleString();

    document.getElementById("gdp").innerHTML =
    Number(eco.gdp ?? 0).toLocaleString();

    document.getElementById("gdpTable").innerHTML =
    Number(eco.gdp ?? 0).toLocaleString();

    document.getElementById("inflation").innerHTML =
    (eco.inflation ?? "-")+"%";

    document.getElementById("inflationTable").innerHTML =
    (eco.inflation ?? "-")+"%";

    chart.data.datasets[0].data=[

        eco.gdp ?? 0,

        eco.inflation ?? 0,

        data.exports ?? 0,

        data.imports ?? 0

    ];

    chart.update();

})

.catch(function(){

    console.log("WorldBank gagal");

});

</script>

</x-app-layout>