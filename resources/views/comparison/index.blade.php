<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>

body{
    background:#F8FAFC;
}

/* Sidebar */

.sidebar{

    position:fixed;

    left:0;

    top:0;

    width:240px;

    height:100vh;

    background:#0B3C5D;

    color:white;

    padding:30px;

}

.sidebar h3{

    font-weight:bold;

    margin-bottom:40px;

}

.sidebar a{

    color:white;

    display:block;

    padding:14px;

    text-decoration:none;

    border-radius:10px;

    margin-bottom:8px;

}

.sidebar a:hover{

    background:rgba(255,255,255,.15);

}

/* Content */

.content{

    margin-left:260px;

    padding:35px;

}

/* Custom UI */
.card {
    border-radius: 12px;
}

.icon-box {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table-sm th, .table-sm td {
    padding: 10px 8px;
    vertical-align: middle;
}


</style>

<div class="sidebar">
    <div class="logo">
        🌍 GSC RISK 
        INTELLIGENCE
    </div>
    <ul>
        <li><a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="{{ route('countries') }}"><i class="bi bi-globe2"></i> Countries</a></li>
        <li><a href="{{ route('ports') }}"><i class="bi bi-geo-alt"></i> Ports</a></li>
        <li class="active"><a href="{{ route('weather.monitoring') }}"><i class="bi bi-cloud-sun"></i> Weather</a></li>
        <li><a href="{{ route('news.index') }}"><i class="bi bi-newspaper"></i> News</a></li>
        <li><a href="{{ route('watchlist.index') }}"><i class="bi bi-bookmark-star"></i> Watchlist country</a></li>
        <li><a href="{{ route('comparison.index') }}"><i class="bi bi-bar-chart"></i>Country Comparison</a></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none; border:none; color:white; padding:14px; text-align:left; width:100%; border-radius:10px;">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>
<div class="content">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 m-0">
                Country Comparison Engine
            </h1>
            <p class="text-secondary mt-1 mb-0" style="font-size: 14px;">
                Compare key economic, risk, weather and currency indicators between two countries.
            </p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-white bg-white border border-slate-200 shadow-sm d-flex align-items-center gap-2" style="border-radius: 8px; font-size: 14px; font-weight: 500; height: 42px;">
                <i class="bi bi-question-circle"></i> How it works
            </button>
            <div class="text-secondary" style="font-size: 13px;">
                <i class="bi bi-clock"></i> Last updated: {{ now()->format('d M Y H:i') }}
            </div>
        </div>
    </div>

    <!-- Compare Form -->
    <div class="card border-0 shadow-sm p-4 mb-5 bg-white" style="border-radius: 16px;">
        <form action="{{ route('comparison.compare') }}" method="POST">
            @csrf
            <div class="row align-items-end g-3">
                <!-- Country A Dropdown -->
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-slate-700" style="font-size: 13.5px; margin-bottom: 8px;">Country A</label>
                    <select name="country_a" class="form-select border-slate-200 py-2.5" style="border-radius: 8px; font-size: 14px;">
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ isset($countryA) && $countryA->id == $country->id ? 'selected' : '' }}>
                                {{ $country->country_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- VS Badge -->
                <div class="col-md-1 text-center py-2">
                    <div class="d-inline-flex align-items-center justify-content-center border rounded-circle bg-light fw-bold text-slate-500" style="width: 42px; height: 42px; font-size: 15px;">
                        VS
                    </div>
                </div>

                <!-- Country B Dropdown -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-slate-700" style="font-size: 13.5px; margin-bottom: 8px;">Country B</label>
                    <select name="country_b" class="form-select border-slate-200 py-2.5" style="border-radius: 8px; font-size: 14px;">
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ isset($countryB) && $countryB->id == $country->id ? 'selected' : '' }}>
                                {{ $country->country_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Compare Button -->
                <div class="col-md-2">
                    <button type="submit" class="btn text-white w-100 d-flex align-items-center justify-content-center gap-2" style="background-color: #007A78; font-weight: 500; height: 44px; border-radius: 8px; border: none;">
                        <i class="bi bi-arrow-left-right"></i> Compare
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(isset($countryA) && isset($countryB))
        @php
            $latestA = $countryA->economicData->sortByDesc('year')->first();
            $latestB = $countryB->economicData->sortByDesc('year')->first();
            
            $prevA = $countryA->economicData->sortByDesc('year')->skip(1)->first();
            $prevB = $countryB->economicData->sortByDesc('year')->skip(1)->first();
            
            // Format GDP Values
            $gdpValA = $latestA->gdp ?? 0;
            $gdpValB = $latestB->gdp ?? 0;
            
            $gdpFormattedA = $gdpValA >= 1000000000000 ? '$' . number_format($gdpValA / 1000000000000, 2) . ' Trillion' : '$' . number_format($gdpValA / 100000000, 2) . ' Billion';
            $gdpFormattedB = $gdpValB >= 1000000000000 ? '$' . number_format($gdpValB / 1000000000000, 2) . ' Trillion' : '$' . number_format($gdpValB / 100000000, 2) . ' Billion';

            // GDP YoY Changes
            $gdpChangeA = 0;
            if ($prevA && $prevA->gdp > 0) {
                $gdpChangeA = round((($latestA->gdp - $prevA->gdp) / $prevA->gdp) * 100, 1);
            }
            $gdpChangeB = 0;
            if ($prevB && $prevB->gdp > 0) {
                $gdpChangeB = round((($latestB->gdp - $prevB->gdp) / $prevB->gdp) * 100, 1);
            }

            // Inflation YoY Changes
            $infChangeA = 0;
            if ($prevA) {
                $infChangeA = round(($latestA->inflation ?? 0) - ($prevA->inflation ?? 0), 1);
            }
            $infChangeB = 0;
            if ($prevB) {
                $infChangeB = round(($latestB->inflation ?? 0) - ($prevB->inflation ?? 0), 1);
            }

            // Weather Variables
            $tempA = $weatherA['main']['temp'] ?? 0;
            $tempB = $weatherB['main']['temp'] ?? 0;
            $humA = $weatherA['main']['humidity'] ?? 0;
            $humB = $weatherB['main']['humidity'] ?? 0;
            
            $weatherIconA = 'bi-cloud';
            $weatherIconB = 'bi-sun';
            
            $weatherMainA = $weatherA['weather'][0]['main'] ?? 'Cloudy';
            $weatherMainB = $weatherB['weather'][0]['main'] ?? 'Sunny';

            if (strtolower($weatherMainA) == 'clear' || strtolower($weatherMainA) == 'sunny') $weatherIconA = 'bi-sun-fill text-warning';
            elseif (strtolower($weatherMainA) == 'clouds' || strtolower($weatherMainA) == 'cloudy') $weatherIconA = 'bi-cloud-fill text-secondary';
            elseif (strtolower($weatherMainA) == 'rain' || strtolower($weatherMainA) == 'rainy') $weatherIconA = 'bi-cloud-rain-fill text-primary';
            else $weatherIconA = 'bi-cloud-fill text-secondary';

            if (strtolower($weatherMainB) == 'clear' || strtolower($weatherMainB) == 'sunny') $weatherIconB = 'bi-sun-fill text-warning';
            elseif (strtolower($weatherMainB) == 'clouds' || strtolower($weatherMainB) == 'cloudy') $weatherIconB = 'bi-cloud-fill text-secondary';
            elseif (strtolower($weatherMainB) == 'rain' || strtolower($weatherMainB) == 'rainy') $weatherIconB = 'bi-cloud-rain-fill text-primary';
            else $weatherIconB = 'bi-cloud-fill text-secondary';

            // Static World GDP Rank estimation
            function getGdpRank($countryName) {
                $ranks = [
                    'United States' => '#1 World', 'China' => '#2 World', 'Japan' => '#3 World',
                    'Germany' => '#4 World', 'India' => '#5 World', 'United Kingdom' => '#6 World',
                    'France' => '#7 World', 'Italy' => '#8 World', 'Brazil' => '#9 World',
                    'Canada' => '#10 World', 'Russian Federation' => '#11 World', 'Mexico' => '#12 World',
                    'Australia' => '#13 World', 'Korea, Rep.' => '#14 World', 'Spain' => '#15 World',
                    'Indonesia' => '#16 World', 'Saudi Arabia' => '#17 World', 'Netherlands' => '#18 World',
                    'Turkiye' => '#19 World', 'Switzerland' => '#20 World',
                ];
                return $ranks[$countryName] ?? 'Top 50 World';
            }
            $rankA = getGdpRank($countryA->country_name);
            $rankB = getGdpRank($countryB->country_name);
        @endphp

        <!-- Results Header Section -->
        <div class="row mb-4 align-items-center">
            <!-- Country A Name & Flag -->
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-3">
                    @if($countryA->flag)
                        <img src="{{ $countryA->flag }}" class="rounded shadow-sm" style="width: 38px; height: 26px; object-fit: cover;">
                    @endif
                    <div>
                        <h3 class="fw-bold text-slate-800 m-0" style="font-size: 20px;">{{ $countryA->country_name }}</h3>
                        <small class="text-secondary">{{ $countryA->region }}</small>
                    </div>
                </div>
            </div>

            <!-- VS Badge -->
            <div class="col-md-2 text-center text-secondary fw-bold" style="font-size: 18px;">
                VS
            </div>

            <!-- Country B Name & Flag -->
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-3 justify-content-end">
                    <div class="text-end">
                        <h3 class="fw-bold text-slate-800 m-0" style="font-size: 20px;">{{ $countryB->country_name }}</h3>
                        <small class="text-secondary">{{ $countryB->region }}</small>
                    </div>
                    @if($countryB->flag)
                        <img src="{{ $countryB->flag }}" class="rounded shadow-sm" style="width: 38px; height: 26px; object-fit: cover;">
                    @endif
                </div>
            </div>
        </div>

        <!-- 5 Parameter Key Cards side-by-side -->
        <div class="row g-4 mb-5">
            <!-- Country A Cards -->
            <div class="col-md-6">
                <div class="row g-2.5">
                    <!-- GDP -->
                    <div class="col">
                        <div class="card border border-slate-100 shadow-sm p-3 bg-white h-100" style="border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="bi bi-globe text-primary" style="font-size: 14px;"></i>
                                </div>
                                <span class="text-secondary" style="font-size: 11px; font-weight: 500;">GDP (Nominal)</span>
                            </div>
                            <h6 class="fw-bold text-slate-800 mb-0.5" style="font-size: 14px; white-space: nowrap;">{{ $gdpFormattedA }}</h6>
                            <span class="text-secondary d-block" style="font-size: 10px;">Rank {{ $rankA }}</span>
                            <div class="mt-2 text-success" style="font-size: 11px; font-weight: 600;">
                                <i class="bi bi-arrow-up"></i> {{ $gdpChangeA }}% vs last year
                            </div>
                        </div>
                    </div>
                    <!-- Inflation -->
                    <div class="col">
                        <div class="card border border-slate-100 shadow-sm p-3 bg-white h-100" style="border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="bi bi-graph-up-arrow text-warning" style="font-size: 14px;"></i>
                                </div>
                                <span class="text-secondary" style="font-size: 11px; font-weight: 500;">Inflation (YoY)</span>
                            </div>
                            <h6 class="fw-bold text-slate-800 mb-2" style="font-size: 14px;">{{ $latestA->inflation ?? '-' }}%</h6>
                            <div class="mt-2 {{ $infChangeA >= 0 ? 'text-danger' : 'text-success' }}" style="font-size: 11px; font-weight: 600;">
                                <i class="bi {{ $infChangeA >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i> {{ $infChangeA >= 0 ? '+' : '' }}{{ $infChangeA }}% vs last year
                            </div>
                        </div>
                    </div>
                    <!-- Risk -->
                    <div class="col">
                        <div class="card border border-slate-100 shadow-sm p-3 bg-white h-100" style="border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-opacity-10 d-flex align-items-center justify-content-center {{ strtolower($riskLevelA) == 'low' ? 'bg-success text-success' : 'bg-warning text-warning' }}" style="width: 30px; height: 30px;">
                                    <i class="bi bi-shield-check" style="font-size: 14px;"></i>
                                </div>
                                <span class="text-secondary" style="font-size: 11px; font-weight: 500;">Risk Level</span>
                            </div>
                            <h6 class="fw-bold mb-1 {{ strtolower($riskLevelA) == 'low' ? 'text-success' : 'text-warning' }}" style="font-size: 14px;">{{ $riskLevelA }}</h6>
                            <span class="text-secondary" style="font-size: 10px;">Risk Score: {{ $riskA }}/100</span>
                        </div>
                    </div>
                    <!-- Currency -->
                    <div class="col">
                        <div class="card border border-slate-100 shadow-sm p-3 bg-white h-100" style="border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <span class="text-info fw-bold" style="font-size: 12px;">{{ $countryA->currency }}</span>
                                </div>
                                <span class="text-secondary" style="font-size: 11px; font-weight: 500;">Currency</span>
                            </div>
                            <h6 class="fw-bold text-slate-800 mb-1" style="font-size: 14px;">{{ $countryA->currency }}</h6>
                            <span class="badge border border-success bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-0.5" style="font-size: 10px; font-weight: 500; display: inline-block;">Stable</span>
                        </div>
                    </div>
                    <!-- Weather -->
                    <div class="col">
                        <div class="card border border-slate-100 shadow-sm p-3 bg-white h-100" style="border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="bi {{ $weatherIconA }}" style="font-size: 14px;"></i>
                                </div>
                                <span class="text-secondary" style="font-size: 11px; font-weight: 500;">Weather (Current)</span>
                            </div>
                            <h6 class="fw-bold text-slate-800 mb-0.5" style="font-size: 14px;">{{ round($tempA) }}°C</h6>
                            <span class="text-secondary d-block" style="font-size: 10px;">{{ $weatherMainA }}</span>
                            <span class="text-secondary d-block mt-1.5" style="font-size: 10px;">Humidity {{ $humA }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Country B Cards -->
            <div class="col-md-6">
                <div class="row g-2.5">
                    <!-- GDP -->
                    <div class="col">
                        <div class="card border border-slate-100 shadow-sm p-3 bg-white h-100" style="border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="bi bi-globe text-primary" style="font-size: 14px;"></i>
                                </div>
                                <span class="text-secondary" style="font-size: 11px; font-weight: 500;">GDP (Nominal)</span>
                            </div>
                            <h6 class="fw-bold text-slate-800 mb-0.5" style="font-size: 14px; white-space: nowrap;">{{ $gdpFormattedB }}</h6>
                            <span class="text-secondary d-block" style="font-size: 10px;">Rank {{ $rankB }}</span>
                            <div class="mt-2 text-success" style="font-size: 11px; font-weight: 600;">
                                <i class="bi bi-arrow-up"></i> {{ $gdpChangeB }}% vs last year
                            </div>
                        </div>
                    </div>
                    <!-- Inflation -->
                    <div class="col">
                        <div class="card border border-slate-100 shadow-sm p-3 bg-white h-100" style="border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="bi bi-graph-up-arrow text-warning" style="font-size: 14px;"></i>
                                </div>
                                <span class="text-secondary" style="font-size: 11px; font-weight: 500;">Inflation (YoY)</span>
                            </div>
                            <h6 class="fw-bold text-slate-800 mb-2" style="font-size: 14px;">{{ $latestB->inflation ?? '-' }}%</h6>
                            <div class="mt-2 {{ $infChangeB >= 0 ? 'text-danger' : 'text-success' }}" style="font-size: 11px; font-weight: 600;">
                                <i class="bi {{ $infChangeB >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i> {{ $infChangeB >= 0 ? '+' : '' }}{{ $infChangeB }}% vs last year
                            </div>
                        </div>
                    </div>
                    <!-- Risk -->
                    <div class="col">
                        <div class="card border border-slate-100 shadow-sm p-3 bg-white h-100" style="border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-opacity-10 d-flex align-items-center justify-content-center {{ strtolower($riskLevelB) == 'low' ? 'bg-success text-success' : 'bg-warning text-warning' }}" style="width: 30px; height: 30px;">
                                    <i class="bi bi-shield-check" style="font-size: 14px;"></i>
                                </div>
                                <span class="text-secondary" style="font-size: 11px; font-weight: 500;">Risk Level</span>
                            </div>
                            <h6 class="fw-bold mb-1 {{ strtolower($riskLevelB) == 'low' ? 'text-success' : 'text-warning' }}" style="font-size: 14px;">{{ $riskLevelB }}</h6>
                            <span class="text-secondary" style="font-size: 10px;">Risk Score: {{ $riskB }}/100</span>
                        </div>
                    </div>
                    <!-- Currency -->
                    <div class="col">
                        <div class="card border border-slate-100 shadow-sm p-3 bg-white h-100" style="border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <span class="text-info fw-bold" style="font-size: 12px;">{{ $countryB->currency }}</span>
                                </div>
                                <span class="text-secondary" style="font-size: 11px; font-weight: 500;">Currency</span>
                            </div>
                            <h6 class="fw-bold text-slate-800 mb-1" style="font-size: 14px;">{{ $countryB->currency }}</h6>
                            <span class="badge border border-success bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-0.5" style="font-size: 10px; font-weight: 500; display: inline-block;">Stable</span>
                        </div>
                    </div>
                    <!-- Weather -->
                    <div class="col">
                        <div class="card border border-slate-100 shadow-sm p-3 bg-white h-100" style="border-radius: 12px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="bi {{ $weatherIconB }}" style="font-size: 14px;"></i>
                                </div>
                                <span class="text-secondary" style="font-size: 11px; font-weight: 500;">Weather (Current)</span>
                            </div>
                            <h6 class="fw-bold text-slate-800 mb-0.5" style="font-size: 14px;">{{ round($tempB) }}°C</h6>
                            <span class="text-secondary d-block" style="font-size: 10px;">{{ $weatherMainB }}</span>
                            <span class="text-secondary d-block mt-1.5" style="font-size: 10px;">Humidity {{ $humB }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Visual Charts Row -->
        <div class="row g-4 mb-5">
            <!-- Radar Chart: Overall Comparison -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                    <h5 class="fw-bold text-slate-800 mb-4" style="font-size: 15px;">Overall Comparison</h5>
                    <div style="height: 300px;">
                        <div id="radarComparisonChart"></div>
                    </div>
                </div>
            </div>

            <!-- Line Chart: GDP Trend -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold text-slate-800 mb-1" style="font-size: 15px;">GDP Trend (Nominal)</h5>
                            <small class="text-secondary" style="font-size: 11px;">in Billion USD</small>
                        </div>
                        <select class="form-select form-select-sm border-slate-200" style="width: auto; border-radius: 6px; font-size: 12px; height: 30px; font-weight: 500;">
                            <option>5 Years</option>
                        </select>
                    </div>
                    <div style="height: 300px;">
                        <div id="gdpComparisonChart"></div>
                    </div>
                </div>
            </div>

            <!-- Line Chart: Inflation Trend -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-slate-800 mb-0" style="font-size: 15px;">Inflation Trend (YoY %)</h5>
                        <select class="form-select form-select-sm border-slate-200" style="width: auto; border-radius: 6px; font-size: 12px; height: 30px; font-weight: 500;">
                            <option>5 Years</option>
                        </select>
                    </div>
                    <div style="height: 300px;">
                        <div id="inflationComparisonChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Columns Detail Sections -->
        <div class="row g-4 mb-5">
            <!-- Weather Comparison -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                    <h5 class="fw-bold text-slate-800 mb-4" style="font-size: 15px;">Weather Comparison <span class="text-secondary font-normal" style="font-size: 11px;">(Current)</span></h5>
                    
                    <div class="row align-items-center g-3">
                        <!-- Country A -->
                        <div class="col-6 border-end border-slate-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi {{ $weatherIconA }} fs-3"></i>
                                <div>
                                    <h4 class="fw-bold text-slate-800 m-0" style="font-size: 22px;">{{ round($tempA) }}°C</h4>
                                    <small class="text-secondary d-block">{{ $weatherMainA }}</small>
                                </div>
                            </div>
                            <div class="space-y-1.5 mt-3" style="font-size: 12px;">
                                <div class="text-secondary d-flex justify-content-between"><span>Humidity:</span> <strong class="text-slate-700">{{ $humA }}%</strong></div>
                                <div class="text-secondary d-flex justify-content-between"><span>Wind:</span> <strong class="text-slate-700">{{ $weatherA['wind']['speed'] ?? '-' }} km/h</strong></div>
                                <div class="text-secondary d-flex justify-content-between"><span>Pressure:</span> <strong class="text-slate-700">{{ $weatherA['main']['pressure'] ?? '-' }} hPa</strong></div>
                                <div class="text-secondary d-flex justify-content-between"><span>Visibility:</span> <strong class="text-slate-700">{{ $weatherA['visibility'] ?? '-' }} km</strong></div>
                            </div>
                        </div>

                        <!-- Country B -->
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi {{ $weatherIconB }} fs-3"></i>
                                <div>
                                    <h4 class="fw-bold text-slate-800 m-0" style="font-size: 22px;">{{ round($tempB) }}°C</h4>
                                    <small class="text-secondary d-block">{{ $weatherMainB }}</small>
                                </div>
                            </div>
                            <div class="space-y-1.5 mt-3" style="font-size: 12px;">
                                <div class="text-secondary d-flex justify-content-between"><span>Humidity:</span> <strong class="text-slate-700">{{ $humB }}%</strong></div>
                                <div class="text-secondary d-flex justify-content-between"><span>Wind:</span> <strong class="text-slate-700">{{ $weatherB['wind']['speed'] ?? '-' }} km/h</strong></div>
                                <div class="text-secondary d-flex justify-content-between"><span>Pressure:</span> <strong class="text-slate-700">{{ $weatherB['main']['pressure'] ?? '-' }} hPa</strong></div>
                                <div class="text-secondary d-flex justify-content-between"><span>Visibility:</span> <strong class="text-slate-700">{{ $weatherB['visibility'] ?? '-' }} km</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currency Comparison -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                    <h5 class="fw-bold text-slate-800 mb-4" style="font-size: 15px;">Currency Comparison</h5>
                    
                    <div class="row align-items-center g-3">
                        <!-- Country A -->
                        <div class="col-6 border-end border-slate-100">
                            <div class="mb-3">
                                <h3 class="fw-bold text-slate-800 m-0" style="font-size: 20px;">{{ $countryA->currency }}</h3>
                                <small class="text-secondary" style="font-size: 11px;">Euro</small>
                                <span class="badge border border-success bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-0.5 d-inline-block mt-1.5" style="font-size: 9px; font-weight: 500;">Stable</span>
                            </div>
                            <div class="space-y-1.5 mt-3" style="font-size: 12px;">
                                <div class="text-secondary">Exchange Rate (USD):</div>
                                <strong class="text-slate-800 d-block mt-0.5" style="font-size: 13px;">1 {{ $countryA->currency }} = {{ $rateA }} USD</strong>
                                <div class="text-secondary mt-2">Volatility (30D):</div>
                                <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                    <strong class="text-slate-800">2.1%</strong> 
                                    <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 9px;">Low</span>
                                </div>
                            </div>
                        </div>

                        <!-- Country B -->
                        <div class="col-6">
                            <div class="mb-3">
                                <h3 class="fw-bold text-slate-800 m-0" style="font-size: 20px;">{{ $countryB->currency }}</h3>
                                <small class="text-secondary" style="font-size: 11px;">Australian Dollar</small>
                                <span class="badge border border-success bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-0.5 d-inline-block mt-1.5" style="font-size: 9px; font-weight: 500;">Stable</span>
                            </div>
                            <div class="space-y-1.5 mt-3" style="font-size: 12px;">
                                <div class="text-secondary">Exchange Rate (USD):</div>
                                <strong class="text-slate-800 d-block mt-0.5" style="font-size: 13px;">1 {{ $countryB->currency }} = {{ $rateB }} USD</strong>
                                <div class="text-secondary mt-2">Volatility (30D):</div>
                                <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                    <strong class="text-slate-800">3.4%</strong> 
                                    <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 9px;">Low</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risk Comparison -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                    <h5 class="fw-bold text-slate-800 mb-4" style="font-size: 15px;">Risk Comparison</h5>
                    
                    <div class="row align-items-center g-3">
                        <!-- Country A -->
                        <div class="col-6 border-end border-slate-100">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-opacity-10 {{ strtolower($riskLevelA) == 'low' ? 'bg-success text-success' : 'bg-warning text-warning' }}" style="width: 38px; height: 38px;">
                                    <i class="bi bi-shield-check" style="font-size: 18px;"></i>
                                </div>
                                <h6 class="fw-bold text-slate-800 m-0" style="font-size: 13.5px;">{{ $riskLevelA }} Risk</h6>
                            </div>
                            <div class="space-y-1.5" style="font-size: 12px;">
                                <div class="text-secondary">Risk Score:</div>
                                <strong class="text-slate-800 d-block mt-0.5" style="font-size: 14px;">{{ $riskA }}/100</strong>
                                <div class="text-secondary mt-2.5">Trend:</div>
                                <strong class="text-success d-block mt-0.5"><i class="bi bi-arrow-down-right"></i> -3 <span class="text-secondary fw-normal" style="font-size: 11px;">vs last month</span></strong>
                            </div>
                        </div>

                        <!-- Country B -->
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-opacity-10 {{ strtolower($riskLevelB) == 'low' ? 'bg-success text-success' : 'bg-warning text-warning' }}" style="width: 38px; height: 38px;">
                                    <i class="bi bi-shield-check" style="font-size: 18px;"></i>
                                </div>
                                <h6 class="fw-bold text-slate-800 m-0" style="font-size: 13.5px;">{{ $riskLevelB }} Risk</h6>
                            </div>
                            <div class="space-y-1.5" style="font-size: 12px;">
                                <div class="text-secondary">Risk Score:</div>
                                <strong class="text-slate-800 d-block mt-0.5" style="font-size: 14px;">{{ $riskB }}/100</strong>
                                <div class="text-secondary mt-2.5">Trend:</div>
                                <strong class="text-success d-block mt-0.5"><i class="bi bi-arrow-down-right"></i> -5 <span class="text-secondary fw-normal" style="font-size: 11px;">vs last month</span></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary & Insights Section -->
        <div class="row g-4 mb-4">
            <!-- Comparison Summary -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                    <h5 class="fw-bold text-slate-800 mb-3" style="font-size: 15px;">Comparison Summary</h5>
                    <ul class="d-flex flex-column gap-3.5 list-unstyled mb-0" style="padding-left: 0;">
                        @foreach ($summary as $item)
                            @php
                                $parts = explode(': ', $item, 2);
                                $title = $parts[0] ?? '';
                                $desc = $parts[1] ?? '';
                                
                                $summaryIcon = 'bi-check-circle-fill text-success';
                                if ($title == 'GDP') $summaryIcon = 'bi-globe-asia-australia text-primary';
                                elseif ($title == 'Inflation') $summaryIcon = 'bi-graph-up-arrow text-warning';
                                elseif ($title == 'Risk') $summaryIcon = 'bi-shield-check text-info';
                                elseif ($title == 'Weather') $summaryIcon = 'bi-cloud-sun text-secondary';
                            @endphp
                            <li class="d-flex align-items-start gap-3" style="font-size: 13.5px;">
                                <div class="d-flex align-items-center justify-content-center rounded bg-slate-50 flex-shrink-0" style="width: 32px; height: 32px; border: 1px solid #F1F5F9; background-color: #FAFAFA;">
                                    <i class="bi {{ $summaryIcon }}" style="font-size: 14px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong class="text-slate-800">{{ $title }}:</strong>
                                    <span class="text-secondary d-block mt-0.5" style="line-height: 1.4;">{{ $desc }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Key Insights -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                    <h5 class="fw-bold text-slate-800 mb-3" style="font-size: 15px;">Key Insights</h5>
                    
                    <p class="text-secondary mb-4" style="font-size: 13.5px; line-height: 1.6;">
                        {{ $insightParagraph }}
                    </p>

                    <div class="d-flex flex-wrap gap-2.5 mt-auto">
                        <span class="badge border-0 bg-blue-100 text-blue-700 px-3 py-2" style="border-radius: 20px; font-weight: 500; font-size: 11.5px;">
                            {{ $insights['gdp'] }}
                        </span>
                        <span class="badge border-0 bg-success bg-opacity-10 text-success px-3 py-2" style="border-radius: 20px; font-weight: 500; font-size: 11.5px;">
                            {{ $insights['risk'] }}
                        </span>
                        <span class="badge border-0 bg-warning bg-opacity-15 text-warning px-3 py-2" style="border-radius: 20px; font-weight: 500; font-size: 11.5px;">
                            {{ $insights['inflation'] }}
                        </span>
                        <span class="badge border-0 bg-info bg-opacity-10 text-info px-3 py-2" style="border-radius: 20px; font-weight: 500; font-size: 11.5px;">
                            {{ $insights['weather'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @php
            // Computations for Radar chart [GDP, Inflation, Risk, Weather, Currency Stability]
            $gdpScoreA = $latestA && $latestB ? ($latestA->gdp > 1000000000000 ? 70 + min(25, round($latestA->gdp / 1000000000000)) : 30 + min(40, round($latestA->gdp / 100000000000))) : 40;
            $infScoreA = max(20, min(95, 100 - ($latestA->inflation ?? 0) * 8));
            $riskScoreA = max(20, min(95, 100 - $riskA));
            $weatherScoreA = max(20, min(95, 100 - abs(22 - $tempA) * 4));
            $currScoreA = $countryA->currency == 'EUR' || $countryA->currency == 'USD' ? 95 : ($countryA->currency == 'AUD' || $countryA->currency == 'GBP' ? 90 : 75);

            $gdpScoreB = $latestA && $latestB ? ($latestB->gdp > 1000000000000 ? 70 + min(25, round($latestB->gdp / 1000000000000)) : 30 + min(40, round($latestB->gdp / 100000000000))) : 40;
            $infScoreB = max(20, min(95, 100 - ($latestB->inflation ?? 0) * 8));
            $riskScoreB = max(20, min(95, 100 - $riskB));
            $weatherScoreB = max(20, min(95, 100 - abs(22 - $tempB) * 4));
            $currScoreB = $countryB->currency == 'EUR' || $countryB->currency == 'USD' ? 95 : ($countryB->currency == 'AUD' || $countryB->currency == 'GBP' ? 90 : 75);
        @endphp

        <!-- Chart scripts for apexcharts -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // 1. Radar Chart initialization
                const radarOptions = {
                    series: [
                        {
                            name: '{{ $countryA->country_name }}',
                            data: [{{ $gdpScoreA }}, {{ $infScoreA }}, {{ $riskScoreA }}, {{ $weatherScoreA }}, {{ $currScoreA }}]
                        },
                        {
                            name: '{{ $countryB->country_name }}',
                            data: [{{ $gdpScoreB }}, {{ $infScoreB }}, {{ $riskScoreB }}, {{ $weatherScoreB }}, {{ $currScoreB }}]
                        }
                    ],
                    chart: {
                        type: 'radar',
                        height: 300,
                        toolbar: { show: false }
                    },
                    colors: ['#007A78', '#3B82F6'],
                    xaxis: {
                        categories: ['GDP', 'Inflation', 'Risk Score', 'Weather', 'Currency Stability'],
                        labels: {
                            style: {
                                colors: '#64748B',
                                fontSize: '11px',
                                fontFamily: 'Inter, sans-serif'
                            }
                        }
                    },
                    fill: {
                        opacity: 0.15
                    },
                    stroke: {
                        width: 2
                    },
                    markers: {
                        size: 4
                    }
                };
                const radarChart = new ApexCharts(document.querySelector("#radarComparisonChart"), radarOptions);
                radarChart.render();

                // 2. GDP Trend Line Chart
                const gdpOptions = {
                    series: [
                        { name: '{{ $countryA->country_name }}', data: @json($gdpA) },
                        { name: '{{ $countryB->country_name }}', data: @json($gdpB) }
                    ],
                    chart: {
                        type: 'line',
                        height: 280,
                        toolbar: { show: false }
                    },
                    colors: ['#007A78', '#3B82F6'],
                    stroke: { curve: 'smooth', width: 3 },
                    grid: { borderColor: '#F1F5F9', strokeDashArray: 4 },
                    markers: { size: 4 },
                    xaxis: {
                        categories: @json($years),
                        labels: { style: { colors: '#94A3B8', fontSize: '11px', fontFamily: 'Inter, sans-serif' } }
                    },
                    yaxis: {
                        labels: { style: { colors: '#94A3B8', fontSize: '11px', fontFamily: 'Inter, sans-serif' } }
                    }
                };
                const gdpChart = new ApexCharts(document.querySelector("#gdpComparisonChart"), gdpOptions);
                gdpChart.render();

                // 3. Inflation Trend Line Chart
                const infOptions = {
                    series: [
                        { name: '{{ $countryA->country_name }}', data: @json($inflationA) },
                        { name: '{{ $countryB->country_name }}', data: @json($inflationB) }
                    ],
                    chart: {
                        type: 'line',
                        height: 280,
                        toolbar: { show: false }
                    },
                    colors: ['#007A78', '#3B82F6'],
                    stroke: { curve: 'smooth', width: 3 },
                    grid: { borderColor: '#F1F5F9', strokeDashArray: 4 },
                    markers: { size: 4 },
                    xaxis: {
                        categories: @json($years),
                        labels: { style: { colors: '#94A3B8', fontSize: '11px', fontFamily: 'Inter, sans-serif' } }
                    },
                    yaxis: {
                        labels: { 
                            formatter: function(val) { return val + "%"; },
                            style: { colors: '#94A3B8', fontSize: '11px', fontFamily: 'Inter, sans-serif' } 
                        }
                    }
                };
                const infChart = new ApexCharts(document.querySelector("#inflationComparisonChart"), infOptions);
                infChart.render();
            });
        </script>
    @endif

</div>

</body>
</html>