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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
body{ background:#f5f7fb; }

/* Sidebar */
.sidebar{
    position:fixed; left:0; top:0; width:240px; height:100vh;
    background:#0B3C5D; color:white; padding:30px;
}
.sidebar h3{ font-weight:bold; margin-bottom:40px; }
.sidebar a{
    color:white; display:block; padding:14px; text-decoration:none;
    border-radius:10px; margin-bottom:8px;
}
.sidebar a:hover{ background:rgba(255,255,255,.15); }
.sidebar li.active a{ background:#0d6efd; font-weight:bold; }
.sidebar ul{ list-style:none; padding:0; }

/* Content */
.content{
    margin-left:240px; padding:30px 40px; font-family: 'Inter', sans-serif;
}

/* Custom UI */
.card { border-radius: 12px; }
.icon-box {
    width: 40px; height: 40px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
}
</style>

<div class="sidebar">
    <div class="logo fw-bold fs-5 mb-4">🌍 GSC RISK 
        INTELLIGENCE</div>
    <ul>
        <li><a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li class="active"><a href="{{ route('countries') }}"><i class="bi bi-globe2 me-2"></i> Countries</a></li>
         <li><a href="{{ route('ports') }}"><i class="bi bi-geo-alt"></i> Ports</a></li>
         <li><a href="{{ route('shipment') }}"><i class="bi bi-truck"></i> Shipment</a></li>
        <li><a href="{{ route('weather.monitoring') }}"><i class="bi bi-cloud-sun me-2"></i> Weather</a></li>
        <li><a href="{{ route('news.index') }}"><i class="bi bi-newspaper me-2"></i> News</a></li>
        <li><a href="{{ route('comparison.index') }}"><i class="bi bi-bar-chart"></i>Country Comparison</a></li>
        <li><a href="{{ route('watchlist.index') }}"><i class="bi bi-bookmark-star"></i> Watchlist country</a></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none; border:none; color:white; padding:14px; text-align:left; width:100%; border-radius:10px;">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>

<div class="content">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="fw-bold m-0">Country Profile</h4>
            <span class="text-muted small">Detailed overview and key indicators of the selected country</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div>
                <label class="form-label small text-muted mb-1">Select Country</label>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light bg-white rounded-pill px-3 shadow-sm border-0 dropdown-toggle d-flex align-items-center justify-content-between" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:220px; text-align:left;">
                        <span class="d-flex align-items-center gap-2">
                            <img src="https://flagcdn.com/w20/{{ strtolower($country->country_code) }}.png" alt="flag" style="width:18px; border-radius:2px; border:1px solid #ddd;">
                            <span class="text-truncate">{{ $country->country_name }}</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu shadow-sm border-0 w-100" style="max-height: 250px; overflow-y: auto; font-size:13px;">
                        @foreach($countries as $c)
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 {{ $country->id == $c->id ? 'bg-primary bg-opacity-10 text-primary fw-bold' : '' }}" href="/countries/{{ $c->country_name }}">
                                    <img src="https://flagcdn.com/w20/{{ strtolower($c->country_code) }}.png" alt="flag" style="width:18px; border-radius:2px; border:1px solid #ddd;">
                                    {{ $c->country_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="d-flex flex-column align-items-end justify-content-end h-100">
                <span class="text-muted small mt-4"><i class="bi bi-calendar3"></i> {{ now()->format('d M Y') }}</span>
            </div>
        </div>

 <!-- Add to Watchlist -->
    <div class="mt-4">

        <form action="{{ route('watchlist.store') }}" method="POST">

            @csrf

            <input
                type="hidden"
                name="country_id"
                value="{{ $country->id }}">

            <button
                type="submit"
                class="btn btn-warning rounded-pill shadow-sm">

                ⭐ Add to Watchlist

            </button>

        </form>

    </div>

    </div>

    <!-- Row 1: Stats Cards -->
    <div class="row g-2 mb-4">
        <!-- Identity -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <img src="https://flagcdn.com/w40/{{ strtolower($country->country_code) }}.png" alt="flag" style="width:40px; border-radius:4px; border:1px solid #ddd;">
                        <div>
                            <h5 class="fw-bold mb-0">{{ $country->country_name }}</h5>
                            <small class="text-muted">{{ $country->iso3 }}</small>
                        </div>
                    </div>
                    <div class="text-muted small"><i class="bi bi-geo-alt"></i> {{ $country->capital }}, {{ $country->region }}</div>
                </div>
            </div>
        </div>
        
        <!-- Risk -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body p-3 d-flex flex-column justify-content-center">
                    <div class="text-muted" style="font-size:10px;">Risk Level</div>
                    <div class="my-1"><span class="badge {{ $riskLevel == 'High Risk' ? 'bg-danger' : ($riskLevel == 'Medium Risk' ? 'bg-warning text-dark' : 'bg-success') }} rounded-pill">{{ $riskLevel }}</span></div>
                    <div class="text-muted" style="font-size:10px;">Risk Score</div>
                    <h6 class="fw-bold mb-0">{{ $riskScore }} <span class="fw-normal text-muted" style="font-size:10px;">/ 100</span></h6>
                </div>
            </div>
        </div>

        <!-- GDP -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex align-items-center gap-2 mb-2 w-100">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary" style="width:30px;height:30px;"><i class="bi bi-graph-up"></i></div>
                        <div class="text-muted" style="font-size:10px;">GDP (Nominal)</div>
                    </div>
                    <h6 class="fw-bold mb-0 w-100">${{ number_format(($economic->gdp ?? 0) / 1000000000000, 2) }} <span class="fw-normal text-muted" style="font-size:10px;">Trillion</span></h6>
                </div>
            </div>
        </div>

        <!-- Inflation -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex align-items-center gap-2 mb-2 w-100">
                        <div class="icon-box bg-success bg-opacity-10 text-success" style="width:30px;height:30px;"><i class="bi bi-percent"></i></div>
                        <div class="text-muted" style="font-size:10px;">Inflation Rate</div>
                    </div>
                    <h6 class="fw-bold mb-0 w-100">{{ $economic->inflation ?? 0 }}%</h6>
                </div>
            </div>
        </div>

        <!-- Population -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex align-items-center gap-2 mb-2 w-100">
                        <div class="icon-box" style="background:#e0e7ff; color:#4f46e5; width:30px;height:30px;"><i class="bi bi-people-fill"></i></div>
                        <div class="text-muted" style="font-size:10px;">Population</div>
                    </div>
                    <h6 class="fw-bold mb-0 w-100">{{ number_format(($country->population ?? 0) / 1000000, 2) }} <span class="fw-normal text-muted" style="font-size:10px;">Million</span></h6>
                </div>
            </div>
        </div>

        <!-- Currency -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex align-items-center gap-2 mb-2 w-100">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning" style="width:30px;height:30px;"><i class="bi bi-coin"></i></div>
                        <div class="text-muted" style="font-size:10px;">Currency</div>
                    </div>
                    <h6 class="fw-bold mb-0 w-100 text-truncate" title="{{ $country->currency }}">{{ $country->currency }}</h6>
                    <div class="text-muted w-100" style="font-size:10px;">{{ $country->currency_code }}</div>
                </div>
            </div>
        </div>

        <!-- Weather -->
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center text-center" id="top-weather-box">
                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                    <div class="text-muted" style="font-size:10px;">Loading weather...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Map & Trend Charts -->
    <div class="row g-4 mb-4">
        <!-- Map -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3" style="font-size:13px;">Location on Map</h6>
                    <div id="countryMap" style="height: 250px; border-radius:10px; z-index:1;"></div>
                </div>
            </div>
        </div>

        <!-- GDP Trend -->
        <div class="col-md-7">
            <div class="row g-4 h-100">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-0" style="font-size:13px;">GDP Trend (Nominal)</h6>
                            <div class="text-muted mb-2" style="font-size:10px;">(Trillion USD)</div>
                            <div id="gdpChart" style="height: 200px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-0" style="font-size:13px;">Inflation Rate Trend</h6>
                            <div class="text-muted mb-2" style="font-size:10px;">(%)</div>
                            <div id="inflationChart" style="height: 200px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Bottom Sections -->
    <div class="row g-4">
        <!-- Current Weather & Forecast -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3" id="current-weather-box">
                    <h6 class="fw-bold mb-3" style="font-size:13px;">Current Weather</h6>
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <div class="text-muted small">Loading weather data...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Economy Snapshot -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3" style="font-size:13px;">Economic Snapshot ({{ $economic->year ?? 'N/A' }})</h6>
                    <table class="table table-borderless table-sm" style="font-size:11px;">
                        <tbody>
                            <tr><td class="text-muted">GDP (Nominal)</td><td class="text-end fw-semibold">${{ number_format(($economic->gdp ?? 0) / 1000000000000, 2) }} Trillion</td></tr>
                            <tr><td class="text-muted">Exports</td><td class="text-end fw-semibold">${{ number_format(($economic->exports ?? 0) / 1000000000, 2) }} Billion</td></tr>
                            <tr><td class="text-muted">Imports</td><td class="text-end fw-semibold">${{ number_format(($economic->imports ?? 0) / 1000000000, 2) }} Billion</td></tr>
                            <tr><td class="text-muted">Trade Balance</td><td class="text-end fw-semibold">${{ number_format((($economic->exports ?? 0) - ($economic->imports ?? 0)) / 1000000000, 2) }} Billion</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Facts -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3" style="font-size:13px;">Quick Facts</h6>
                    <table class="table table-borderless table-sm" style="font-size:11px;">
                        <tbody>
                            <tr><td class="text-muted">Capital</td><td class="text-end fw-semibold">{{ $country->capital }}</td></tr>
                            <tr><td class="text-muted">Region</td><td class="text-end fw-semibold">{{ $country->region }}</td></tr>
                            <tr><td class="text-muted">Country Code</td><td class="text-end fw-semibold">{{ $country->iso3 }}</td></tr>
                            <tr><td class="text-muted">Currency</td><td class="text-end fw-semibold">{{ $country->currency }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // 1. Map Initialization
    var lat = {{ $country->latitude ?? 0 }};
    var lon = {{ $country->longitude ?? 0 }};
    var map = L.map('countryMap').setView([lat, lon], 4);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap & CARTO'
    }).addTo(map);
    L.marker([lat, lon]).addTo(map).bindPopup("<b>{{ $country->country_name }}</b><br>{{ $country->capital }}").openPopup();

    // 2. ApexCharts for GDP and Inflation
    var rawGdp = {!! $gdpTrend !!};
    var gdpInTrillions = rawGdp.map(val => (val / 1000000000000).toFixed(2));
    var years = {!! $trendYears !!};

    var gdpOpt = {
        series: [{ name: "GDP", data: gdpInTrillions }],
        chart: { type: 'line', height: 200, toolbar: {show: false} },
        stroke: { curve: 'smooth', width: 3, colors: ['#0d6efd'] },
        xaxis: { categories: years, labels: {style: {fontSize: '10px'}} },
        yaxis: { labels: {style: {fontSize: '10px'}} },
        markers: { size: 4, colors: ['#0d6efd'] },
        dataLabels: { enabled: true, style: { fontSize: '9px' }, offsetY: -5, background: { enabled: false } }
    };
    new ApexCharts(document.querySelector("#gdpChart"), gdpOpt).render();

    var inflOpt = {
        series: [{ name: "Inflation", data: {!! $inflationTrend !!} }],
        chart: { type: 'bar', height: 200, toolbar: {show: false} },
        colors: ['#198754'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        dataLabels: { enabled: true, style: { fontSize: '10px', colors: ['#000'] }, formatter: function (val) { return val + "%" } },
        xaxis: { categories: years, labels: {style: {fontSize: '10px'}} },
        yaxis: { labels: {show: false} }
    };
    new ApexCharts(document.querySelector("#inflationChart"), inflOpt).render();

    // 3. Fetch Weather Data (AJAX)
    if(lat && lon) {
        var weatherUrl = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m`;
        fetch(weatherUrl)
            .then(res => res.json())
            .then(data => {
                if(data && data.current && data.current.temperature_2m !== undefined) {
                    var current = data.current;
                    // Update Top Box
                    document.getElementById('top-weather-box').innerHTML = `
                        <div class="d-flex align-items-center gap-2 mb-2 w-100 justify-content-center">
                            <div class="icon-box bg-info bg-opacity-10 text-info" style="width:30px;height:30px;"><i class="bi bi-cloud-sun"></i></div>
                            <div class="text-muted" style="font-size:10px;">Weather (Now)</div>
                        </div>
                        <h6 class="fw-bold mb-0 w-100">${current.temperature_2m}°C</h6>
                    `;
                    
                    // Update Current Weather Bottom Box
                    document.getElementById('current-weather-box').innerHTML = `
                        <h6 class="fw-bold mb-3" style="font-size:13px;">Current Weather</h6>
                        <div class="text-center mb-3">
                            <i class="bi bi-cloud-sun text-info" style="font-size:48px;"></i>
                            <h3 class="fw-bold mb-0 mt-2">${current.temperature_2m}°C</h3>
                        </div>
                        <div class="d-flex justify-content-around text-muted mt-4" style="font-size:11px;">
                            <div class="text-center"><i class="bi bi-wind fs-5"></i><br>${current.wind_speed_10m ?? 0} km/h</div>
                            <div class="text-center"><i class="bi bi-droplet fs-5"></i><br>${current.relative_humidity_2m ?? 0}%</div>
                        </div>
                    `;
                } else {
                    document.getElementById('top-weather-box').innerHTML = `<span class="text-muted small">Weather N/A</span>`;
                    document.getElementById('current-weather-box').innerHTML = `<span class="text-muted small">Weather data unavailable</span>`;
                }
            }).catch(e => {
                console.error('Weather fetch error:', e);
                document.getElementById('top-weather-box').innerHTML = `<span class="text-danger small">Error</span>`;
                document.getElementById('current-weather-box').innerHTML = `<span class="text-danger small">Error fetching weather</span>`;
            });
    }
</script>

</body>
</html>