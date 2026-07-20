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
        <li><a href="{{ route('shipment') }}"><i class="bi bi-truck"></i> Shipment</a></li>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Global Supply Chain Risk Intelligence Platform</h4>
        <div class="d-flex align-items-center gap-3">
        </div>
    </div>

<!-- Row 1 -->
    <div class="row g-3 mb-3">
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary text-white"><i class="bi bi-globe2"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">Total Countries</div>
                        <h5 class="fw-bold mb-0">{{ $totalCountries }}</h5>
                        <div class="text-muted" style="font-size:10px;">Countries Monitored</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-danger text-white"><i class="bi bi-exclamation-lg fs-4"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">High Risk</div>
                        <h5 class="fw-bold mb-0">{{ $highRiskCount }}</h5>
                        <div class="text-muted" style="font-size:10px;">{{ round(($highRiskCount/max($totalCountries,1))*100,1) }}% of total</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-warning text-dark"><i class="bi bi-exclamation-lg fs-4"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">Medium Risk</div>
                        <h5 class="fw-bold mb-0">{{ $mediumRiskCount }}</h5>
                        <div class="text-muted" style="font-size:10px;">{{ round(($mediumRiskCount/max($totalCountries,1))*100,1) }}% of total</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box bg-success text-white"><i class="bi bi-check-lg fs-4"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">Low Risk</div>
                        <h5 class="fw-bold mb-0">{{ $lowRiskCount }}</h5>
                        <div class="text-muted" style="font-size:10px;">{{ round(($lowRiskCount/max($totalCountries,1))*100,1) }}% of total</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-box" style="background:#6f42c1; color:white;"><i class="bi bi-newspaper"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">News Today</div>
                        <h5 class="fw-bold mb-0">{{ $newsCount }}</h5>
                        <div class="text-muted" style="font-size:10px;">Articles collected</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Row 2 -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="text-success"><i class="bi bi-graph-up-arrow fs-1"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">Average Inflation</div>
                        <h5 class="fw-bold mb-0">{{ number_format($avgInflation, 2) }}%</h5>
                        <div class="text-muted" style="font-size:10px;">Global Average</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="text-primary"><i class="bi bi-bar-chart-line-fill fs-1"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">Average GDP Growth</div>
                        <h5 class="fw-bold mb-0">{{ number_format($avgGdpGrowth, 2) }}%</h5>
                        <div class="text-muted" style="font-size:10px;">Global Average</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="color:#6f42c1;"><i class="bi bi-currency-exchange fs-1"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">Avg Exchange Rate Change</div>
                        <h5 class="fw-bold mb-0 text-success">+0.35%</h5>
                        <div class="text-muted" style="font-size:10px;">24h Average Change</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="text-danger"><i class="bi bi-cloud-lightning-rain-fill fs-1"></i></div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">Active Storm Alerts</div>
                        <h5 class="fw-bold mb-0">{{ $weatherAlerts }}</h5>
                        <div class="text-muted" style="font-size:10px;">Active Alerts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Row 3: Map & Charts -->
    <div class="row g-4 mb-4">
        <!-- Map -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Global Risk Map</h6>
                    <div id="riskMap" style="height: 400px; border-radius:10px; z-index:1;"></div>
                </div>
            </div>
        </div>
        <!-- Charts Right side -->
        <div class="col-md-5 d-flex flex-column gap-3">
            <div class="card border-0 shadow-sm flex-fill">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-0" style="font-size:13px;">Risk Score Trend (Global Average)</h6>
                    <div id="riskScoreTrendChart" style="height: 180px;"></div>
                </div>
            </div>
            <div class="row g-3 flex-fill">
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-0" style="font-size:12px;">GDP Trend (Global)</h6>
                            <div id="gdpTrendChart" style="height: 150px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-0" style="font-size:12px;">Inflation Trend (Global)</h6>
                            <div id="inflationTrendChart" style="height: 150px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Row 4: Tables & Lists -->
    <div class="row g-4 mb-4">
        <!-- Currency -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3" style="font-size:13px;">Currency Impact (vs USD)</h6>
                    <table class="table table-borderless table-sm mb-0" style="font-size:11px;">
                        <thead class="border-bottom">
                            <tr><th>Currency</th><th>Rate</th><th>24h Change</th></tr>
                        </thead>
                        <tbody>
                            @forelse($exchangeRates as $rate)
                            <tr>
                                <td>{{ $rate->currency_code }} / USD</td>
                                <td>{{ number_format($rate->exchange_rate_to_usd, 4) }}</td>
                                <td class="{{ $rate->change_24h >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="bi {{ $rate->change_24h >= 0 ? 'bi-caret-up-fill' : 'bi-caret-down-fill' }}"></i> {{ abs($rate->change_24h) }}%
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-muted text-center py-2">No currency data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Latest News -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="font-size:13px;">Latest Global News</h6>
                    </div>
                    <ul class="list-unstyled mb-0">
                        @foreach(array_slice($latestNews, 0, 4) as $news)
                        <li class="mb-2">
                            <a href="{{ $news['link'] }}" target="_blank" class="text-dark fw-semibold text-decoration-none" style="font-size:12px; display:block; line-height:1.2;">
                                {{ Str::limit($news['title'], 40) }}
                            </a>
                            <small class="text-muted" style="font-size:10px;">{{ $news['date'] }}</small>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Top 5 -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3" style="font-size:13px;">Top 5 High Risk Countries</h6>
                    <table class="table table-borderless table-sm mb-0" style="font-size:11px;">
                        <thead class="border-bottom">
                            <tr><th>No</th><th>Country</th><th class="text-end">Risk Score</th></tr>
                        </thead>
                        <tbody>
                            @foreach($topHighRisk as $index => $hr)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $hr->country->country_name }}</td>
                                <td class="text-end"><span class="badge bg-danger rounded-pill">{{ $hr->total_score }} High</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Row 5: Extreme Weather, Ports, Distribution -->
    <div class="row g-4 mb-4">
        <!-- Extreme Weather -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3" style="font-size:13px;">Extreme Weather Alerts</h6>
                    <ul class="list-unstyled mb-0">
                        @forelse($extremeWeather as $weather)
                        <li class="mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-cloud-rain-heavy-fill text-primary fs-3"></i>
                            <div>
                                <div class="fw-semibold" style="font-size:12px;">{{ $weather->country->country_name ?? 'Unknown' }}</div>
                                <div class="text-muted" style="font-size:10px;">{{ $weather->storm_risk ?? 'Extreme' }}</div>
                            </div>
                            <span class="badge {{ $weather->storm_risk == 'High Risk' ? 'bg-danger' : 'bg-warning text-dark' }} ms-auto rounded-pill">{{ $weather->storm_risk == 'High Risk' ? 'Warning' : 'Alert' }}</span>
                        </li>
                        @empty
                        <li class="text-muted text-center py-2" style="font-size:11px;">No extreme weather alerts</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3" style="font-size:13px;">World Ports Overview</h6>
                    <table class="table table-borderless table-sm mb-0" style="font-size:11px;">
                        <thead class="border-bottom">
                            <tr><th>Port</th><th>Country</th></tr>
                        </thead>
                        <tbody>
                            @foreach($topPorts as $port)
                            <tr>
                                <td><i class="bi bi-geo-alt text-primary"></i> <span class="fw-semibold">{{ $port->port_name }}</span></td>
                                <td>{{ $port->country->country_name ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3" style="font-size:13px;">Risk Score Distribution</h6>
                    <div id="riskDistributionChart" style="height: 150px;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    try {
        // Leaflet Map
        var map = L.map('riskMap').setView([20, 0], 2);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; CARTO'
        }).addTo(map);

        var mapData = @json($mapData);
        var riskDict = {};
        if (Array.isArray(mapData)) {
            mapData.forEach(function(c) {
                riskDict[c.name] = c;
            });
        }

        // Legend
        var legend = L.control({position: 'bottomleft'});
        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend');
            div.style.background = 'white';
            div.style.padding = '10px';
            div.style.borderRadius = '8px';
            div.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
            div.style.fontSize = '12px';
            div.innerHTML = `
                <div style="margin-bottom:5px;"><span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#dc3545; margin-right:5px;"></span> High Risk</div>
                <div style="margin-bottom:5px;"><span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#ffc107; margin-right:5px;"></span> Medium Risk</div>
                <div style="margin-bottom:5px;"><span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#198754; margin-right:5px;"></span> Low Risk</div>
                <div><span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:#cccccc; margin-right:5px;"></span> No Data</div>
            `;
            return div;
        };
        legend.addTo(map);

        if (Array.isArray(mapData)) {
            mapData.forEach(function(country) {
                if (country.lat && country.lng) {
                    var color = '#198754'; 
                    if(country.risk_level === 'High Risk') color = '#dc3545';
                    else if(country.risk_level === 'Medium Risk') color = '#ffc107';

                    L.circleMarker([country.lat, country.lng], {
                        radius: 5, fillColor: color, color: '#fff', weight: 1.5, opacity: 1, fillOpacity: 1
                    }).addTo(map).bindPopup(`
                        <div style="min-width:200px; font-family:'Inter', sans-serif;">
                            <div style="display:flex; align-items:center; margin-bottom:10px;">
                                <b>${country.name}</b>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                                <span>Risk Level</span>
                                <span class="badge ${country.risk_level === 'High Risk' ? 'bg-danger' : (country.risk_level === 'Medium Risk' ? 'bg-warning text-dark' : 'bg-success')}">${country.risk_level}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                                <span>Risk Score</span>
                                <b>${country.risk_score} / 100</b>
                            </div>
                        </div>
                    `);
                }
            });
        }

        // Fetch GeoJSON from a more stable CDN (jsDelivr) for the colored polygons
        fetch('https://cdn.jsdelivr.net/gh/johan/world.geo.json@master/countries.geo.json')
        .then(res => res.json())
        .then(data => {
            L.geoJson(data, {
                style: function(feature) {
                    var countryName = feature.properties.name;
                    var color = '#e2e8f0'; 
                    var fillOpacity = 0.5;
                    var matchedCountry = null;
                    
                    if (riskDict[countryName]) {
                        matchedCountry = riskDict[countryName];
                    } else {
                        for(var key in riskDict) {
                            if(key.toLowerCase() === countryName.toLowerCase()) {
                                matchedCountry = riskDict[key];
                                break;
                            }
                        }
                    }

                    if (matchedCountry) {
                        var level = matchedCountry.risk_level;
                        if(level === 'High Risk') color = '#dc3545';
                        else if(level === 'Medium Risk') color = '#ffc107';
                        else color = '#198754';
                        fillOpacity = 0.7;
                    }
                    return {
                        fillColor: color,
                        weight: 1,
                        opacity: 1,
                        color: '#ffffff',
                        fillOpacity: fillOpacity
                    };
                }
            }).addTo(map);
        })
        .catch(err => console.error('Map fetch error:', err));
    } catch(e) { console.error('Map init error:', e); }
</script>

<script>
    try {
        var riskTrendOpt = {
            series: [{ name: "Score", data: {!! $riskTrendScores !!} }],
            chart: { type: 'line', height: 180, toolbar: {show: false} },
            stroke: { curve: 'smooth', width: 2, colors: ['#0d6efd'] },
            xaxis: { categories: {!! $riskTrendDates !!}, labels: {style: {fontSize: '9px'}} },
            yaxis: { max: 100, min: 0, labels: {style: {fontSize: '9px'}} },
            markers: { size: 3, colors: ['#0d6efd'] }
        };
        new ApexCharts(document.querySelector("#riskScoreTrendChart"), riskTrendOpt).render();
    } catch(e) { console.error('Risk Chart error:', e); }
</script>

<script>
    try {
        var gdpTrendOpt = {
            series: [{ name: "GDP", data: {!! $gdpTrendData !!} }],
            chart: { type: 'line', height: 130, toolbar: {show: false} },
            stroke: { curve: 'straight', width: 2, colors: ['#0d6efd'] },
            xaxis: { categories: {!! $trendYears !!}, labels: {style: {fontSize: '9px'}} },
            yaxis: { labels: {show: false} },
            markers: { size: 3, colors: ['#0d6efd'] }
        };
        new ApexCharts(document.querySelector("#gdpTrendChart"), gdpTrendOpt).render();
    } catch(e) { console.error('GDP Chart error:', e); }
</script>

<script>
    try {
        var inflTrendOpt = {
            series: [{ name: "Inflation", data: {!! $inflationTrendData !!} }],
            chart: { type: 'bar', height: 130, toolbar: {show: false} },
            colors: ['#dc3545'],
            dataLabels: { enabled: false },
            plotOptions: { bar: { borderRadius: 2, columnWidth: '50%' } },
            xaxis: { categories: {!! $trendYears !!}, labels: {style: {fontSize: '9px'}} },
            yaxis: { labels: {show: false} }
        };
        new ApexCharts(document.querySelector("#inflationTrendChart"), inflTrendOpt).render();
    } catch(e) { console.error('Inflation Chart error:', e); }
</script>

<script>
    try {
        var distOpt = {
            series: [{{ $highRiskCount }}, {{ $mediumRiskCount }}, {{ $lowRiskCount }}],
            chart: { type: 'donut', height: 150 },
            labels: ['High Risk', 'Medium Risk', 'Low Risk'],
            colors: ['#dc3545', '#ffc107', '#198754'],
            dataLabels: { enabled: false },
            legend: { position: 'right', fontSize: '9px' }
        };
        new ApexCharts(document.querySelector("#riskDistributionChart"), distOpt).render();
    } catch(e) { console.error('Distribution Chart error:', e); }
</script>

</body>
</html>