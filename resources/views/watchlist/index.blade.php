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

</head>

<body>

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
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 d-flex align-items-center gap-2 m-0">
                Favorite Monitoring List <span style="color: #f1c40f;">★</span>
            </h1>
            <p class="text-secondary mt-1 mb-0" style="font-size: 14px;">
                Save and monitor countries that matter most to you.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2.5">
            <!-- Search bar -->
            <div class="position-relative" style="width: 280px;">
                <input type="text" id="searchInput" class="form-control bg-white border-slate-200" placeholder="Search countries..." style="border-radius: 8px; padding-right: 38px; height: 42px; font-size: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <i class="bi bi-search position-absolute top-50 translate-middle-y text-slate-400" style="right: 12px; pointer-events: none;"></i>
            </div>
            
            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-white bg-white border border-slate-200 shadow-sm dropdown-toggle d-flex align-items-center gap-2" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px; height: 42px; font-size: 14px;">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="filterDropdown" style="border-radius: 8px;">
                    <li><a class="dropdown-item filter-opt active" href="#" data-level="all" style="font-size: 14px;">All Risk Levels</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item filter-opt text-danger" href="#" data-level="High" style="font-size: 14px; font-weight: 500;">High Risk</a></li>
                    <li><a class="dropdown-item filter-opt text-warning" href="#" data-level="Medium" style="font-size: 14px; font-weight: 500;">Medium Risk</a></li>
                    <li><a class="dropdown-item filter-opt text-success" href="#" data-level="Low" style="font-size: 14px; font-weight: 500;">Low Risk</a></li>
                </ul>
            </div>

            <!-- Add Country Button -->
            <button class="btn text-white d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCountryModal" style="background-color: #0A5C4E; border-radius: 8px; height: 42px; font-weight: 500; font-size: 14px; border: none;">
                <i class="bi bi-plus-lg"></i> Add Country
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-5">
        <!-- Total Countries -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10" style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="bi bi-globe2 text-success fs-3"></i>
                    </div>
                    <div>
                        <span class="text-secondary text-sm d-block mb-1" style="font-size: 13px;">Total Countries</span>
                        <h2 class="fw-bold mb-0 text-slate-800" style="font-size: 28px;">{{ $total }}</h2>
                        <span class="text-secondary" style="font-size: 11px;">Countries in watchlist</span>
                    </div>
                </div>
                <div class="mt-3 text-success fw-semibold d-flex align-items-center gap-1" style="font-size: 13px;">
                    <i class="bi bi-arrow-up"></i> 1 added this week
                </div>
            </div>
        </div>

        <!-- High Risk -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-15" style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    </div>
                    <div>
                        <span class="text-secondary text-sm d-block mb-1" style="font-size: 13px;">High Risk Countries</span>
                        <h2 class="fw-bold mb-0 text-slate-800" style="font-size: 28px;">{{ $high }}</h2>
                        <span class="text-secondary" style="font-size: 11px;">Require attention</span>
                    </div>
                </div>
                <div class="mt-3 text-danger fw-semibold d-flex align-items-center gap-1" style="font-size: 13px;">
                    <i class="bi bi-arrow-up"></i> {{ $high }} from last week
                </div>
            </div>
        </div>

        <!-- Weather Alerts -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10" style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="bi bi-cloud-rain-fill text-primary fs-3"></i>
                    </div>
                    <div>
                        <span class="text-secondary text-sm d-block mb-1" style="font-size: 13px;">Weather Alerts</span>
                        <h2 class="fw-bold mb-0 text-slate-800" style="font-size: 28px;">{{ $weatherAlerts }}</h2>
                        <span class="text-secondary" style="font-size: 11px;">Active weather alerts</span>
                    </div>
                </div>
                <div class="mt-3 text-danger fw-semibold d-flex align-items-center gap-1" style="font-size: 13px;">
                    <i class="bi bi-arrow-up"></i> 1 from last week
                </div>
            </div>
        </div>

        <!-- Avg Risk Score -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-radius: 16px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10" style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="bi bi-graph-up-arrow text-info fs-3"></i>
                    </div>
                    <div>
                        <span class="text-secondary text-sm d-block mb-1" style="font-size: 13px;">Avg. Risk Score</span>
                        <h2 class="fw-bold mb-0 text-slate-800" style="font-size: 28px;">{{ $avgRiskScore }}/100</h2>
                        <span class="text-secondary" style="font-size: 11px;">Across all countries</span>
                    </div>
                </div>
                <div class="mt-3 text-success fw-semibold d-flex align-items-center gap-1" style="font-size: 13px;">
                    <i class="bi bi-arrow-down"></i> -3 from last week
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm mb-5 bg-white" style="border-radius: 16px; overflow: hidden;">
        <div class="px-4 py-3.5 border-bottom border-slate-100 bg-white">
            <h5 class="fw-bold text-slate-800 mb-0" style="font-size: 16px;">My Watchlist</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                <thead class="bg-slate-50 border-bottom border-slate-100" style="background-color: #F8FAFC;">
                    <tr>
                        <th class="px-4 py-3 text-secondary fw-semibold border-0" style="font-size: 13px;">Country</th>
                        <th class="px-4 py-3 text-secondary fw-semibold border-0" style="font-size: 13px;">Region</th>
                        <th class="px-4 py-3 text-secondary fw-semibold border-0" style="font-size: 13px;">Risk Score</th>
                        <th class="px-4 py-3 text-secondary fw-semibold border-0" style="font-size: 13px;">Risk Level</th>
                        <th class="px-4 py-3 text-secondary fw-semibold border-0" style="font-size: 13px;">GDP (Nominal)</th>
                        <th class="px-4 py-3 text-secondary fw-semibold border-0" style="font-size: 13px;">Inflation (YoY)</th>
                        <th class="px-4 py-3 text-secondary fw-semibold border-0" style="font-size: 13px;">Weather</th>
                        <th class="px-4 py-3 text-secondary fw-semibold border-0" style="font-size: 13px;">Last Updated</th>
                        <th class="px-4 py-3 text-secondary fw-semibold border-0 text-center" style="font-size: 13px; width: 110px;">Action</th>
                    </tr>
                </thead>
                <tbody id="watchlistTableBody" class="border-0">
                    @forelse($watchlists as $watch)
                        @php
                            $economic = $watch->country->economicData->first();
                            $infValue = $economic->inflation ?? 0;
                        @endphp
                        <tr class="border-bottom border-slate-100" data-risk-level="{{ $watch->calculated_level }}">
                            <!-- Country -->
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    @if($watch->country->flag)
                                        <img src="{{ $watch->country->flag }}" class="rounded shadow-sm" style="width: 24px; height: 16px; object-fit: cover;">
                                    @endif
                                    <span class="fw-semibold text-slate-800 country-name">
                                        {{ $watch->country->country_name }}
                                    </span>
                                </div>
                            </td>
                            <!-- Region -->
                            <td class="px-4 py-3 text-secondary region-name">
                                {{ $watch->country->region }}
                            </td>
                            <!-- Risk Score -->
                            <td class="px-4 py-3">
                                <div class="d-flex flex-column" style="width: 120px;">
                                    <span class="fw-semibold text-slate-700 mb-1" style="font-size: 13px;">{{ $watch->calculated_score }}/100</span>
                                    <div class="progress" style="height: 5px; background-color: #F1F5F9; border-radius: 4px;">
                                        <div class="progress-bar {{ $watch->calculated_score >= 70 ? 'bg-danger' : ($watch->calculated_score >= 40 ? 'bg-warning' : 'bg-success') }}" 
                                             role="progressbar" 
                                             style="width: {{ $watch->calculated_score }}%; border-radius: 4px;">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <!-- Risk Level -->
                            <td class="px-4 py-3">
                                @if(str_contains(strtolower($watch->calculated_level), 'high'))
                                    <span class="badge border border-danger bg-danger bg-opacity-10 text-danger px-2.5 py-1.5 rounded" style="font-weight: 500; font-size: 12px; min-width: 68px; text-align: center;">High</span>
                                @elseif(str_contains(strtolower($watch->calculated_level), 'medium'))
                                    <span class="badge border border-warning bg-warning bg-opacity-10 text-warning px-2.5 py-1.5 rounded" style="font-weight: 500; font-size: 12px; min-width: 68px; text-align: center;">Medium</span>
                                @else
                                    <span class="badge border border-success bg-success bg-opacity-10 text-success px-2.5 py-1.5 rounded" style="font-weight: 500; font-size: 12px; min-width: 68px; text-align: center;">Low</span>
                                @endif
                            </td>
                            <!-- GDP (Nominal) -->
                            <td class="px-4 py-3 text-slate-700 font-semibold">
                                {{ $watch->gdp_formatted }}
                            </td>
                            <!-- Inflation (YoY) -->
                            <td class="px-4 py-3">
                                <span class="{{ $infValue >= 3.0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                    {{ $watch->inflation_formatted }}
                                </span>
                            </td>
                            <!-- Weather -->
                            <td class="px-4 py-3">
                                @php
                                    $weatherData = $watch->weather;
                                    $temp = $weatherData['temperature'] ?? null;
                                    $code = $weatherData['weather_code'] ?? null;
                                    
                                    $weatherIcon = 'bi-cloud';
                                    $weatherText = 'Unknown';
                                    
                                    if ($temp !== null) {
                                        if ($code === 0) {
                                            $weatherIcon = 'bi-sun-fill text-warning';
                                            $weatherText = 'Sunny';
                                        } elseif (in_array($code, [1, 2])) {
                                            $weatherIcon = 'bi-cloud-sun-fill text-secondary';
                                            $weatherText = 'Partly Cloudy';
                                        } elseif ($code === 3) {
                                            $weatherIcon = 'bi-cloud-fill text-secondary';
                                            $weatherText = 'Cloudy';
                                        } elseif (in_array($code, [45, 48])) {
                                            $weatherIcon = 'bi-cloud-fog-fill text-secondary';
                                            $weatherText = 'Foggy';
                                        } elseif (in_array($code, [51, 53, 55, 61, 63, 65])) {
                                            $weatherIcon = 'bi-cloud-rain-fill text-primary';
                                            $weatherText = 'Cloudy';
                                        } elseif (in_array($code, [80, 81, 82])) {
                                            $weatherIcon = 'bi-cloud-drizzle-fill text-primary';
                                            $weatherText = 'Light Rain';
                                        } elseif (in_array($code, [95, 96, 99])) {
                                            $weatherIcon = 'bi-cloud-lightning-rain-fill text-danger';
                                            $weatherText = 'Thunderstorm';
                                        } else {
                                            $weatherIcon = 'bi-cloud-fill text-secondary';
                                            $weatherText = 'Cloudy';
                                        }
                                    }
                                @endphp
                                @if ($temp !== null)
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi {{ $weatherIcon }} fs-4"></i>
                                        <div>
                                            <div class="fw-bold text-slate-800">{{ round($temp) }}°C</div>
                                            <small class="text-secondary" style="font-size: 11px;">{{ $weatherText }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                            <!-- Last Updated -->
                            <td class="px-4 py-3 text-secondary" style="font-size: 13px;">
                                {{ $watch->updated_at ? $watch->updated_at->format('d M Y H:i') : '-' }}
                            </td>
                            <!-- Action -->
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-1.5">
                                    <!-- View Details -->
                                    <a href="{{ route('countries.show', $watch->country->country_name) }}" 
                                       class="btn btn-light bg-white border border-slate-200 text-secondary hover:bg-slate-50 btn-sm rounded-lg d-flex align-items-center justify-content-center" 
                                       style="width: 34px; height: 34px;" 
                                       title="View Country Details">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Delete from Watchlist -->
                                    <form action="{{ route('watchlist.destroy', $watch) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Remove this country from watchlist?')" 
                                                class="btn btn-light bg-white border border-slate-200 text-danger hover:bg-red-50 btn-sm rounded-lg d-flex align-items-center justify-content-center" 
                                                style="width: 34px; height: 34px;" 
                                                title="Delete from Watchlist">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-secondary">
                                <div class="py-4">
                                    <i class="bi bi-bookmark-star text-slate-300 fs-1"></i>
                                    <p class="mt-2 mb-0">No countries in your watchlist.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Table Footer Pagination -->
        <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px; border-top: 1px solid #F1F5F9;">
            <span class="text-secondary text-sm" id="tablePaginationText" style="font-size: 13px;">Showing 1 to {{ $total }} of {{ $total }} countries</span>
            <nav>
                <ul class="pagination pagination-sm mb-0 gap-1">
                    <li class="page-item disabled"><a class="page-link border-0 text-slate-400 bg-light rounded" href="#" style="font-size: 11px; padding: 6px 10px;">&laquo;</a></li>
                    <li class="page-item disabled"><a class="page-link border-0 text-slate-400 bg-light rounded" href="#" style="font-size: 11px; padding: 6px 10px;">&lt;</a></li>
                    <li class="page-item active"><a class="page-link border-0 text-white rounded bg-primary" href="#" style="font-size: 11px; padding: 6px 10px;">1</a></li>
                    <li class="page-item disabled"><a class="page-link border-0 text-slate-400 bg-light rounded" href="#" style="font-size: 11px; padding: 6px 10px;">&gt;</a></li>
                    <li class="page-item disabled"><a class="page-link border-0 text-slate-400 bg-light rounded" href="#" style="font-size: 11px; padding: 6px 10px;">&raquo;</a></li>
                </ul>
            </nav>
        </div>
    </div>



</div>

<!-- Modal: Add Country -->
<div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="addCountryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800" id="addCountryModalLabel" style="font-size: 18px;">Add Country to Watchlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('watchlist.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    @if ($countries->isEmpty())
                        <div class="text-center text-secondary py-3">
                            <i class="bi bi-info-circle fs-3 text-slate-300"></i>
                            <p class="mt-2 mb-0" style="font-size: 14px;">All available countries are already in your watchlist.</p>
                        </div>
                    @else
                        <div class="mb-3">
                            <label for="countrySelect" class="form-label fw-semibold text-slate-700" style="font-size: 13.5px;">Select Country</label>
                            <select name="country_id" id="countrySelect" class="form-select border-slate-200 py-2.5" style="border-radius: 8px; font-size: 14px;">
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }} ({{ $country->region }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4 py-2.5" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 14px;">Cancel</button>
                    @if (!$countries->isEmpty())
                        <button type="submit" class="btn text-white px-4 py-2.5" style="background-color: #0A5C4E; border-radius: 8px; font-weight: 500; font-size: 14px; border: none;">Add Country</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Load Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Interactive client logic and chart initializations -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Real-time client-side search logic
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#watchlistTableBody tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const countryCell = row.querySelector('.country-name');
                    const regionCell = row.querySelector('.region-name');
                    if (countryCell && regionCell) {
                        const countryName = countryCell.textContent.toLowerCase();
                        const regionName = regionCell.textContent.toLowerCase();
                        if (countryName.includes(filter) || regionName.includes(filter)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
                
                const pagText = document.getElementById('tablePaginationText');
                if (pagText) {
                    pagText.textContent = `Showing 1 to ${visibleCount} of ${visibleCount} countries`;
                }
            });
        }

        // 2. Risk Level filtering logic
        const filterOpts = document.querySelectorAll('.filter-opt');
        filterOpts.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Toggle active classes
                filterOpts.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                
                const selectedLevel = this.getAttribute('data-level').toLowerCase();
                const rows = document.querySelectorAll('#watchlistTableBody tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const level = row.getAttribute('data-risk-level').toLowerCase();
                    if (selectedLevel === 'all' || level.includes(selectedLevel)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                const pagText = document.getElementById('tablePaginationText');
                if (pagText) {
                    pagText.textContent = `Showing 1 to ${visibleCount} of ${visibleCount} countries`;
                }
            });
        });


    });
</script>
</div>

</body>
</html>