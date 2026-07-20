<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
    body{
        background:#f5f7fb;
    }
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
    .sidebar .logo{
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 40px;
    }
    .sidebar ul{
        list-style: none;
        padding: 0;
    }
    .sidebar a{
        color:white;
        display:block;
        padding:14px;
        text-decoration:none;
        border-radius:10px;
        margin-bottom:8px;
    }
    .sidebar a:hover, .sidebar .active a{
        background:rgba(255,255,255,.15);
    }
    .sidebar i {
        margin-right: 10px;
    }
    .content { margin-left: 250px; padding: 30px; }
    .card { border-radius: 12px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
    .stat-card { padding: 20px; display: flex; align-items: center; gap: 15px; }
    .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .map-container { height: 400px; border-radius: 12px; overflow: hidden; position: relative; }
    .map-overlay { position: absolute; top: 15px; left: 15px; z-index: 1000; background: rgba(255,255,255,0.9); padding: 15px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 200px; }
    .weather-detail-box { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; font-size: 13px; }
    .weather-detail-box:last-child { border-bottom: none; }
    .forecast-card { text-align: center; padding: 15px 10px; border-right: 1px solid #eee; }
    .forecast-card:last-child { border-right: none; }
    .alert-table th { font-size: 12px; color: #6c757d; font-weight: 500; }
    .alert-table td { font-size: 13px; vertical-align: middle; }
    
    /* Loading Overlay */
    #loadingOverlay { position: fixed; top: 0; left: 250px; right: 0; bottom: 0; background: rgba(248, 249, 250, 0.8); z-index: 2000; display: flex; flex-direction: column; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
</style>

<!-- Loading Screen -->
<div id="loadingOverlay">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
    <div class="mt-3 fw-bold text-muted">Fetching live weather data...</div>
</div>

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
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="fw-bold m-0">Weather Monitoring</h4>
            <span class="text-muted small">Real-time global weather monitoring and alerts</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div>
                <label class="form-label small text-muted mb-1">Select Country</label>
                <div class="dropdown">
                    <button class="btn btn-sm btn-white bg-white rounded-pill px-3 shadow-sm border dropdown-toggle d-flex align-items-center justify-content-between" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:200px; text-align:left;">
                        <span class="d-flex align-items-center gap-2">
                            <img src="https://flagcdn.com/w20/{{ strtolower($country->country_code) }}.png" alt="flag" style="width:18px; border-radius:2px;">
                            <span class="text-truncate">{{ $country->country_name }}</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu shadow w-100" style="max-height: 250px; overflow-y: auto;">
                        @foreach($countries as $c)
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('weather.monitoring', $c->country_name) }}">
                                    <img src="https://flagcdn.com/w20/{{ strtolower($c->country_code) }}.png" style="width:18px; border-radius:2px;">
                                    {{ $c->country_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="btn btn-sm btn-white bg-white rounded-pill px-3 shadow-sm border mt-4 text-muted">
                <i class="bi bi-calendar3 me-2"></i> {{ now()->format('d M Y') }}
            </div>
            <div class="btn btn-sm btn-white bg-white rounded-pill px-3 shadow-sm border mt-4 text-muted">
                <i class="bi bi-circle-fill text-success" style="font-size:8px;"></i> Auto Update: On
            </div>
        </div>
    </div>

    <!-- Top 5 Cards -->
    <div class="row g-3 mb-4">
        <!-- Temp -->
        <div class="col">
            <div class="card h-100 stat-card">
                <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;"><i class="bi bi-thermometer-half"></i></div>
                <div>
                    <div class="text-muted small">Current Temp</div>
                    <div class="fw-bold fs-4 m-0" id="top-temp">--°C</div>
                    <div class="text-muted" style="font-size:11px;" id="top-desc">Loading...</div>
                </div>
            </div>
        </div>
        <!-- Rainfall -->
        <div class="col">
            <div class="card h-100 stat-card">
                <div class="stat-icon" style="background:#dbeafe; color:#2563eb;"><i class="bi bi-cloud-rain-fill"></i></div>
                <div>
                    <div class="text-muted small">Rainfall (Today)</div>
                    <div class="fw-bold fs-4 m-0" id="top-rain">-- mm</div>
                    <div class="text-success" style="font-size:11px;" id="top-rain-desc">Loading...</div>
                </div>
            </div>
        </div>

        <!-- Wind Speed -->
        <div class="col">
            <div class="card h-100 stat-card">
                <div class="stat-icon" style="background:#fef3c7; color:#d97706;"><i class="bi bi-wind"></i></div>
                <div>
                    <div class="text-muted small">Wind Speed</div>
                    <div class="fw-bold fs-4 m-0" id="top-wind">-- km/h</div>
                    <div class="text-success" style="font-size:11px;" id="top-wind-desc">Loading...</div>
                </div>
            </div>
        </div>
        <!-- Status -->
        <div class="col">
            <div class="card h-100 stat-card">
                <div class="stat-icon" style="background:#dcfce7; color:#16a34a;" id="status-icon"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div class="text-muted small">Weather Status</div>
                    <div class="fw-bold fs-4 m-0 text-success" id="top-status">Stable</div>
                    <div class="text-muted" style="font-size:11px;" id="top-status-desc">All clear</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map & Current Detail -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card p-2 h-100">
                <div class="map-container" id="weatherMap">
                    <div class="map-overlay">
                        <h6 class="fw-bold mb-3" style="font-size:13px;">Global Weather Map <i class="bi bi-info-circle text-muted"></i></h6>
                        <div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-cloud-rain text-primary"></i> <span class="small">Rain</span></div>
                        <div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-tornado text-danger"></i> <span class="small">Storm</span></div>
                        <div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-wind text-warning"></i> <span class="small">Strong Wind</span></div>
                        
                        <hr class="my-2">
                        <h6 class="fw-bold mb-2 mt-3" style="font-size:12px;">Map Layers</h6>
                        <div class="form-check form-switch mb-1">
                          <input class="form-check-input" type="checkbox" checked>
                          <label class="form-check-label small" style="font-size:11px;">Rain</label>
                        </div>
                        <div class="form-check form-switch mb-1">
                          <input class="form-check-input" type="checkbox" checked>
                          <label class="form-check-label small" style="font-size:11px;">Storm</label>
                        </div>
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" checked>
                          <label class="form-check-label small" style="font-size:11px;">Wind</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 h-100">
                <h6 class="fw-bold mb-4">Current Weather in {{ $country->country_name }}</h6>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <i class="bi bi-cloud-sun text-info" style="font-size:60px;" id="main-icon"></i>
                    <div>
                        <div class="fw-bold" style="font-size:36px;" id="main-temp">--°C</div>
                        <div class="text-muted" id="main-desc">Loading...</div>
                    </div>
                </div>
                
                <div class="weather-detail-box">
                    <span class="text-muted"><i class="bi bi-droplet me-2"></i>Humidity</span>
                    <span class="fw-bold" id="det-humidity">--%</span>
                </div>
                <div class="weather-detail-box">
                    <span class="text-muted"><i class="bi bi-wind me-2"></i>Wind Speed</span>
                    <span class="fw-bold" id="det-wind">-- km/h</span>
                </div>
                <div class="weather-detail-box">
                    <span class="text-muted"><i class="bi bi-compass me-2"></i>Wind Direction</span>
                    <span class="fw-bold" id="det-dir">--°</span>
                </div>
                <div class="weather-detail-box">
                    <span class="text-muted"><i class="bi bi-speedometer me-2"></i>Pressure</span>
                    <span class="fw-bold" id="det-press">-- hPa</span>
                </div>
                <div class="weather-detail-box">
                    <span class="text-muted"><i class="bi bi-clouds me-2"></i>Cloud Cover</span>
                    <span class="fw-bold" id="det-cloud">--%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-3 h-100">
                <h6 class="fw-bold mb-3" style="font-size:13px;">Rainfall Trend (mm)</h6>
                <div id="rainChart"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 h-100">
                <h6 class="fw-bold mb-3" style="font-size:13px;">Temperature Trend (°C)</h6>
                <div id="tempChart"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 h-100">
                <h6 class="fw-bold mb-3" style="font-size:13px;">Wind Speed Trend (km/h)</h6>
                <div id="windChart"></div>
            </div>
        </div>
    </div>

    <!-- Bottom: Alerts & Forecast -->
    <div class="row g-3">
        <div class="col-md-7">
            <div class="card p-4 h-100">
                <h6 class="fw-bold mb-4">Active Weather Alerts <span class="badge bg-danger rounded-pill ms-2" id="alert-count">0</span></h6>
                <div class="table-responsive">
                    <table class="table table-borderless alert-table align-middle">
                        <thead class="border-bottom">
                            <tr>
                                <th>Location</th>
                                <th>Country</th>
                                <th>Weather Type</th>
                                <th>Severity</th>
                                <th>Started</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="alert-tbody">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold m-0">5-Day Weather Forecast <span class="text-muted fw-normal" style="font-size:12px;">({{ $country->country_name }})</span></h6>
                    <a href="#" class="text-decoration-none small">View full forecast &rarr;</a>
                </div>
                <div class="d-flex justify-content-between" id="forecast-container">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const lat = {{ $country->latitude ?? 0 }};
    const lon = {{ $country->longitude ?? 0 }};
    const countryName = "{{ $country->country_name }}";
    const countryCode = "{{ strtolower($country->country_code) }}";

    // 1. Initialize Map
    var map = L.map('weatherMap', { zoomControl: false }).setView([lat, lon], 4);
    L.control.zoom({ position: 'topright' }).addTo(map);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Weather code mapping
    const weatherCodes = {
        0: { desc: 'Clear sky', icon: 'bi-sun', color: '#f59e0b', type: 'Normal' },
        1: { desc: 'Mainly clear', icon: 'bi-cloud-sun', color: '#f59e0b', type: 'Normal' },
        2: { desc: 'Partly cloudy', icon: 'bi-cloud', color: '#64748b', type: 'Normal' },
        3: { desc: 'Overcast', icon: 'bi-clouds', color: '#64748b', type: 'Normal' },
        45: { desc: 'Fog', icon: 'bi-cloud-haze', color: '#94a3b8', type: 'Normal' },
        48: { desc: 'Depositing rime fog', icon: 'bi-cloud-haze', color: '#94a3b8', type: 'Normal' },
        51: { desc: 'Light drizzle', icon: 'bi-cloud-drizzle', color: '#3b82f6', type: 'Rain' },
        53: { desc: 'Moderate drizzle', icon: 'bi-cloud-drizzle', color: '#3b82f6', type: 'Rain' },
        55: { desc: 'Dense drizzle', icon: 'bi-cloud-drizzle', color: '#3b82f6', type: 'Rain' },
        61: { desc: 'Slight rain', icon: 'bi-cloud-rain', color: '#2563eb', type: 'Rain' },
        63: { desc: 'Moderate rain', icon: 'bi-cloud-rain', color: '#2563eb', type: 'Rain' },
        65: { desc: 'Heavy rain', icon: 'bi-cloud-rain-heavy', color: '#1d4ed8', type: 'Heavy Rain' },
        71: { desc: 'Slight snow', icon: 'bi-snow', color: '#38bdf8', type: 'Normal' },
        73: { desc: 'Moderate snow', icon: 'bi-snow', color: '#38bdf8', type: 'Normal' },
        75: { desc: 'Heavy snow', icon: 'bi-snow', color: '#38bdf8', type: 'Normal' },
        80: { desc: 'Slight rain showers', icon: 'bi-cloud-rain', color: '#2563eb', type: 'Rain' },
        81: { desc: 'Moderate rain showers', icon: 'bi-cloud-rain-heavy', color: '#1d4ed8', type: 'Heavy Rain' },
        82: { desc: 'Violent rain showers', icon: 'bi-cloud-rain-heavy', color: '#1e3a8a', type: 'Heavy Rain' },
        95: { desc: 'Thunderstorm', icon: 'bi-lightning', color: '#dc2626', type: 'Storm' },
        96: { desc: 'Thunderstorm with hail', icon: 'bi-cloud-lightning-rain', color: '#dc2626', type: 'Storm' },
        99: { desc: 'Heavy thunderstorm', icon: 'bi-cloud-lightning-rain', color: '#991b1b', type: 'Storm' },
    };

    function getDir(deg) {
        const dirs = ['N','NE','E','SE','S','SW','W','NW'];
        return dirs[Math.round(deg / 45) % 8] + ' (' + deg + '°)';
    }

    if (lat && lon) {
        // Fetch Current & Forecast Data
        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m,wind_direction_10m,surface_pressure,cloud_cover&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,wind_speed_10m_max&timezone=auto`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                const current = data.current;
                const daily = data.daily;
                const wCode = weatherCodes[current.weather_code] || weatherCodes[0];

                // 1. Top Cards
                document.getElementById('top-temp').innerText = `${current.temperature_2m}°C`;
                document.getElementById('top-desc').innerText = `Feels like ${current.apparent_temperature}°C`;

                document.getElementById('top-rain').innerText = `${daily.precipitation_sum[0]} mm`;
                document.getElementById('top-rain-desc').innerText = wCode.desc;

                // Storm Risk & Status logic (simulated from current data)
                let isStorm = current.weather_code >= 95;
                let isHighWind = current.wind_speed_10m > 30;
                let isHeavyRain = current.precipitation > 5;
                
                if (isStorm) {
                    document.getElementById('top-status').innerText = 'Unstable';
                    document.getElementById('top-status').className = 'fw-bold fs-4 m-0 text-danger';
                    document.getElementById('top-status-desc').innerText = 'Be alert for storms';
                    document.getElementById('status-icon').style.background = '#fee2e2';
                    document.getElementById('status-icon').style.color = '#dc2626';
                    document.getElementById('status-icon').innerHTML = '<i class="bi bi-exclamation-triangle"></i>';
                } else if (isHighWind || isHeavyRain) {
                    document.getElementById('top-status').innerText = 'Warning';
                    document.getElementById('top-status').className = 'fw-bold fs-4 m-0 text-warning';
                    document.getElementById('top-status-desc').innerText = 'Adverse weather';
                    document.getElementById('status-icon').style.background = '#fef3c7';
                    document.getElementById('status-icon').style.color = '#d97706';
                }

                document.getElementById('top-wind').innerText = `${current.wind_speed_10m} km/h`;
                document.getElementById('top-wind-desc').innerText = getDir(current.wind_direction_10m);

                // 2. Main Right Details
                document.getElementById('main-icon').className = `bi ${wCode.icon} text-primary`;
                document.getElementById('main-icon').style.color = wCode.color;
                document.getElementById('main-temp').innerText = `${current.temperature_2m}°C`;
                document.getElementById('main-desc').innerText = wCode.desc;

                document.getElementById('det-humidity').innerText = `${current.relative_humidity_2m}%`;
                document.getElementById('det-wind').innerText = `${current.wind_speed_10m} km/h`;
                document.getElementById('det-dir').innerText = getDir(current.wind_direction_10m);
                document.getElementById('det-press').innerText = `${current.surface_pressure} hPa`;
                document.getElementById('det-cloud').innerText = `${current.cloud_cover}%`;

                // Add Marker to Map
                const markerHtml = `<div style="font-size:30px; color:${wCode.color};"><i class="bi ${wCode.icon}"></i></div>`;
                const customIcon = L.divIcon({ html: markerHtml, className: '', iconSize: [30, 30], iconAnchor: [15, 15] });
                L.marker([lat, lon], {icon: customIcon}).addTo(map)
                 .bindPopup(`<b>${countryName}</b><br>${wCode.desc}`)
                 .openPopup();

                // 3. Charts
                const days = daily.time.map(t => {
                    const d = new Date(t);
                    return d.toLocaleDateString('en-GB', {day: 'numeric', month: 'short'});
                });

                // Rainfall
                new ApexCharts(document.querySelector("#rainChart"), {
                    series: [{ name: "Rainfall", data: daily.precipitation_sum }],
                    chart: { type: 'bar', height: 180, toolbar: {show: false} },
                    colors: ['#3b82f6'],
                    plotOptions: { bar: { borderRadius: 2, columnWidth: '40%' } },
                    dataLabels: { enabled: true, offsetY: -10, style: { fontSize: '9px', colors: ['#304758'] } },
                    xaxis: { categories: days, labels: {style: {fontSize: '9px'}} },
                    yaxis: { labels: {show: false} },
                    grid: { show: false }
                }).render();

                // Temp
                new ApexCharts(document.querySelector("#tempChart"), {
                    series: [{ name: "Max Temp", data: daily.temperature_2m_max }],
                    chart: { type: 'line', height: 180, toolbar: {show: false} },
                    stroke: { curve: 'smooth', width: 2 },
                    colors: ['#ef4444'],
                    markers: { size: 4 },
                    dataLabels: { enabled: true, offsetY: -10, background: { enabled: false }, style: { fontSize: '9px', colors: ['#304758'] } },
                    xaxis: { categories: days, labels: {style: {fontSize: '9px'}} },
                    yaxis: { labels: {show: false} },
                    grid: { show: false }
                }).render();

                // Wind
                new ApexCharts(document.querySelector("#windChart"), {
                    series: [{ name: "Wind Speed", data: daily.wind_speed_10m_max }],
                    chart: { type: 'line', height: 180, toolbar: {show: false} },
                    stroke: { curve: 'straight', width: 2 },
                    colors: ['#10b981'],
                    markers: { size: 4 },
                    dataLabels: { enabled: true, offsetY: -10, background: { enabled: false }, style: { fontSize: '9px', colors: ['#304758'] } },
                    xaxis: { categories: days, labels: {style: {fontSize: '9px'}} },
                    yaxis: { labels: {show: false} },
                    grid: { show: false }
                }).render();

                // 4. 5-Day Forecast
                let forecastHtml = '';
                for(let i=0; i<5; i++) {
                    const fCode = weatherCodes[daily.weather_code[i]] || weatherCodes[0];
                    const d = new Date(daily.time[i]);
                    const dayName = i === 0 ? 'Today' : d.toLocaleDateString('en-GB', {weekday: 'short'});
                    const dateStr = d.toLocaleDateString('en-GB', {day: 'numeric', month: 'short'});
                    
                    forecastHtml += `
                    <div class="forecast-card w-100">
                        <div class="fw-bold" style="font-size:12px;">${dayName}</div>
                        <div class="text-muted mb-2" style="font-size:10px;">${dateStr}</div>
                        <i class="bi ${fCode.icon} mb-2" style="font-size:24px; color:${fCode.color};"></i>
                        <div class="fw-bold" style="font-size:13px;">${daily.temperature_2m_max[i]}° / ${daily.temperature_2m_min[i]}°</div>
                        <div class="text-muted mb-1" style="font-size:11px;">${fCode.desc}</div>
                        <div class="text-primary" style="font-size:10px;"><i class="bi bi-droplet"></i> ${daily.precipitation_sum[i]}mm</div>
                    </div>`;
                }
                document.getElementById('forecast-container').innerHTML = forecastHtml;

                // 5. Active Alerts (Simulated based on thresholds)
                let alertsHtml = '';
                let alertCount = 0;
                
                // We simulate alerts for surrounding major cities based on current country's weather
                const simulatedCities = [
                    { name: 'Capital Region', type: isStorm ? 'Badai / Storm' : (isHeavyRain ? 'Hujan Lebat / Heavy Rain' : (isHighWind ? 'Angin Kencang / Strong Wind' : 'Hujan / Rain')), cond: isStorm || isHeavyRain || isHighWind },
                    { name: 'North District', type: 'Hujan / Rain', cond: current.precipitation > 0 },
                    { name: 'Coastal Area', type: 'Angin Kencang / Strong Wind', cond: current.wind_speed_10m > 15 }
                ];

                simulatedCities.forEach(city => {
                    if (city.cond) {
                        alertCount++;
                        let severity = city.type.includes('Storm') ? 'High' : (city.type.includes('Heavy') || city.type.includes('Strong') ? 'Medium' : 'Low');
                        let badgeColor = severity === 'High' ? 'danger' : (severity === 'Medium' ? 'warning text-dark' : 'success');
                        
                        alertsHtml += `
                        <tr>
                            <td class="fw-bold text-dark">${city.name}</td>
                            <td><img src="https://flagcdn.com/w20/${countryCode}.png" width="16" class="me-1"> ${countryName}</td>
                            <td class="text-muted"><i class="bi bi-cloud-lightning-rain text-danger me-1"></i> ${city.type}</td>
                            <td><span class="badge bg-${badgeColor} bg-opacity-10 text-${badgeColor === 'warning text-dark' ? 'warning' : badgeColor} border border-${badgeColor === 'warning text-dark' ? 'warning' : badgeColor}">${severity}</span></td>
                            <td class="text-muted">${new Date().toLocaleDateString('en-GB')} ${new Date().toLocaleTimeString('en-GB', {hour: '2-digit', minute:'2-digit'})}</td>
                            <td><span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Active</span></td>
                        </tr>`;
                    }
                });

                if(alertCount === 0) {
                    alertsHtml = `<tr><td colspan="6" class="text-center text-muted py-4">No active weather alerts at this time.</td></tr>`;
                }
                
                document.getElementById('alert-tbody').innerHTML = alertsHtml;
                document.getElementById('alert-count').innerText = alertCount;

                // Hide loading overlay
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 500);
            })
            .catch(err => {
                console.error("API Error: ", err);
                alert("Failed to fetch weather data.");
                document.getElementById('loadingOverlay').style.display = 'none';
            });
    } else {
        document.getElementById('loadingOverlay').style.display = 'none';
        alert("Coordinate data not available for this country.");
    }
</script>

</body>
</html>