<x-app-layout>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
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
}

.sidebar li.active a{
    color:#18864B;
    font-weight:bold;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
}

.sidebar a:hover{
    color:white;
}

.sidebar i{
    margin-right:10px;
}

.sidebar button{
    width:100%;
    border:none;
    background:none;
    color:white;
    text-align:left;
    padding:0;
}

.content{
    margin-left:260px;
    padding:35px;
}

.country-card{
    border-radius:18px;
    transition:.3s;
}

.country-card:hover{
    transform:translateY(-4px);
    box-shadow:0 .7rem 1.2rem rgba(0,0,0,.08)!important;
}

.country-card .card-body{
    min-height:170px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

</style>

    <div class="sidebar">

    <div class="logo">
        🌍 SCRI
    </div>

    <ul>

        <li>
            <a href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>

        <li class="active">
            <a href="{{ route('countries') }}">
                <i class="bi bi-globe2"></i>
                Countries
            </a>
        </li>

        <li>
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

<div class="content">

<div class="container-fluid py-4">

    <div class="mb-4">

        <span class="badge bg-primary mb-2">
            🌍 Global Export Analysis
        </span>

        <h1 class="fw-bold">
            Export Destination Analysis
        </h1>

        <p class="text-secondary">
            Evaluate export destinations through economic indicators,
            weather conditions,
            currency trends,
            and country risk assessments.
        </p>

    </div>

    <div class="card shadow border-0">

        <div class="card-body">

            <label class="form-label fw-bold">
                Select Country
            </label>

            <select
                class="form-select"
                onchange="if(this.value) window.location=this.value;">

                <option value="">Select Country</option>

                @foreach($countries as $c)

                <option
                    value="{{ route('countries.show',$c->country_name) }}"
                    {{ isset($country) && $country->id==$c->id ? 'selected':'' }}>

                    {{ $c->country_name }}

                </option>

                @endforeach

            </select>

        </div>

    </div>

    @if(isset($country))

<div class="card shadow border-0 mt-4">

    <div class="card-body">

        <h5 class="fw-bold mb-4">

            Total Countries :
            {{ $countries->count() }}

        </h5>

        <div class="row g-4">

            <!-- COUNTRY -->

            <div class="col-md-3">

                <div class="card shadow-sm border-0 h-100 country-card">

                    <div class="card-body">

                        <img
                            src="{{ $country->flag }}"
                            width="70"
                            class="mb-3">

                        <div class="text-secondary">

                            Country

                        </div>

                        <h3>

                            {{ $country->country_name }}

                        </h3>

                    </div>

                </div>

            </div>

            <!-- REGION -->

            <div class="col-md-3">

                <div class="card shadow-sm border-0 h-100 country-card">

                    <div class="card-body">

                        <div class="text-secondary">

                            Region

                        </div>

                        <h3>

                            {{ $country->region }}

                        </h3>

                    </div>

                </div>

            </div>

            <!-- POPULATION -->

            <div class="col-md-3">

                <div class="card shadow-sm border-0 h-100 country-card">

                    <div class="card-body">

                        <div class="text-secondary">

                            Population

                        </div>

                        <h3>

                            {{ number_format($country->population) }}

                        </h3>

                        People

                    </div>

                </div>

            </div>

            <!-- CURRENCY -->

            <div class="col-md-3">

                <div class="card shadow-sm border-0 h-100 country-card">

                    <div class="card-body">

                        <div class="text-secondary">

                            Currency

                        </div>

                        <h3>

                            {{ $country->currency }}

                        </h3>

                    </div>

                </div>

            </div>

        </div>

        @if(isset($country))

<div class="row mt-4">

    <!-- GDP -->

    <div class="col-md-3">

        <div class="card shadow-sm border-0 h-100 country-card">

            <div class="card-body">

                <small class="text-secondary">

                    GDP

                </small>

                <h3 class="fw-bold">

                    {{ number_format($economic->gdp ?? 0) }}

                </h3>

            </div>

        </div>

    </div>

    <!-- Inflation -->

    <div class="col-md-3">

        <div class="card shadow-sm border-0 h-100 country-card">

            <div class="card-body">

                <small class="text-secondary">

                    Inflation

                </small>

                <h3 class="fw-bold">

                    {{ $economic->inflation ?? '-' }} %

                </h3>

            </div>

        </div>

    </div>

    <!-- Temperature -->

    <div class="col-md-3">

        <div class="card shadow-sm border-0 h-100 country-card">

            <div class="card-body">

                <small class="text-secondary">

                    Temperature

                </small>

                <h3 class="fw-bold" id="temperature">

                    Loading...

                </h3>

            </div>

        </div>

    </div>

    <!-- Wind -->

    <div class="col-md-3">

        <div class="card shadow-sm border-0 h-100 country-card">

            <div class="card-body">

                <small class="text-secondary">

                    Wind Speed

                </small>

                <h3 class="fw-bold" id="wind">

                    Loading...

                </h3>

            </div>

        </div>

    </div>

</div>

@endif

<div class="row mt-4">

    <!-- Exchange Rate -->

    <div class="col-md-6">

        <div class="card shadow border-0 h-100 country-card">

            <div class="card-body">

                <div class="d-flex align-items-center mb-3">

                    <div class="icon-circle bg-success-subtle me-3">
                        <i class="bi bi-cash-coin"></i>
                    </div>

                    <div>

                        <small class="text-secondary">
                            Exchange Rate
                        </small>

                        <h4 class="fw-bold mb-0">

                            1 USD =
                            {{ number_format($exchangeRate,2) }}
                            {{ $country->currency }}

                        </h4>

                        <small class="text-muted">
                            Live Exchange Rate
                        </small>

                    </div>

                </div>

                <canvas id="exchangeChart" height="100"></canvas>

            </div>

        </div>

    </div>

    <!-- Risk Score -->

    <div class="col-md-6">

        <div class="card shadow border-0 h-100 border-start border-5 border-danger">

            <div class="card-body">

                <div class="d-flex align-items-center mb-3">

                    <div class="icon-circle bg-danger-subtle me-3">

                        <i class="bi bi-exclamation-triangle"></i>

                    </div>

                    <div>

                        <small class="text-secondary">

                            Risk Score

                        </small>

                        <h1 class="fw-bold text-danger">

                            {{ $riskScore }}

                        </h1>

                        <h4>

                            {{ $riskLevel }}

                        </h4>

                    </div>

                </div>

                <div class="progress" style="height:12px">

                    <div
                        class="progress-bar bg-success"
                        style="width:30%">
                    </div>

                    <div
                        class="progress-bar bg-warning"
                        style="width:30%">
                    </div>

                    <div
                        class="progress-bar bg-danger"
                        style="width:40%">
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="card shadow border-0 mt-5">

    <div class="card-body text-center">

        <h2 class="fw-bold mb-4">

            News Sentiment Analysis

        </h2>

        <canvas id="sentimentChart" height="100"></canvas>

    </div>

</div>

    </div>

</div>

@endif

</div>

<script>

@if(isset($country))

fetch("/weather/{{ $country->latitude }}/{{ $country->longitude }}")

.then(res=>res.json())

.then(data=>{

    document.getElementById("temperature").innerHTML =
        data.temperature_2m + " °C";

    document.getElementById("wind").innerHTML =
        data.wind_speed_10m + " km/h";

});

@endif

</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

new Chart(document.getElementById('exchangeChart'),{

    type:'line',

    data:{

        labels:[
            '3 Days Ago',
            '2 Days Ago',
            'Yesterday',
            'Today'
        ],

        datasets:[{

            label:'Exchange Rate',

            data:[
                {{ $exchangeRate-250 }},
                {{ $exchangeRate-120 }},
                {{ $exchangeRate+180 }},
                {{ $exchangeRate }}
            ],

            borderColor:'#3b82f6',

            fill:false,

            tension:.4

        }]

    }

});

new Chart(document.getElementById("sentimentChart"),{

    type:'pie',

    data:{

        labels:[
            'Positive',
            'Neutral',
            'Negative'
        ],

        datasets:[{

            data:[65,25,10],

            backgroundColor:[
                '#22c55e',
                '#facc15',
                '#ef4444'
            ]

        }]

    }

});

</script>

</div>
</div>

</x-app-layout>