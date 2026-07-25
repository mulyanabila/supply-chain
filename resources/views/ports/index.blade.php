<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ports Intelligence - GSC Risk Intelligence</title>

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#0A1128',
                            sidebar: '#0B3C5D',
                            blue: '#1A56DB',
                            bg: '#F8FAFC'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Leaflet JS & ApexCharts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .leaflet-container {
            font-family: 'Plus Jakarta Sans', sans-serif;
            border-radius: 12px;
        }

        /* Sidebar styles matching Dashboard & Shipment */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: #0B3C5D;
            color: white;
            padding: 30px;
            z-index: 50;
            box-sizing: border-box;
        }
        .sidebar .logo {
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 40px;
            line-height: 1.2;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 14px;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.2s;
            font-size: 0.95rem;
        }
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .sidebar li.active a {
            background: #0d6efd;
            font-weight: bold;
        }

        /* Content spacing */
        .content-container {
            margin-left: 240px;
            min-height: 100vh;
            background: #F8FAFC;
        }
    </style>
</head>
<body class="bg-brand-bg text-slate-800 min-h-screen font-sans flex flex-col antialiased">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">
            🌍 GSC RISK<br>INTELLIGENCE
        </div>
        <ul>
            <li><a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="{{ route('countries') }}"><i class="bi bi-globe2 me-2"></i> Countries</a></li>
            <li class="active"><a href="{{ route('ports') }}"><i class="bi bi-geo-alt me-2"></i> Ports</a></li>
            <li><a href="{{ route('shipment') }}"><i class="bi bi-truck me-2"></i> Shipment</a></li>
            <li><a href="{{ route('weather.monitoring') }}"><i class="bi bi-cloud-sun me-2"></i> Weather</a></li>
            <li><a href="{{ route('news.index') }}"><i class="bi bi-newspaper me-2"></i> News</a></li>
            <li><a href="{{ route('watchlist.index') }}"><i class="bi bi-bookmark-star me-2"></i> Watchlist country</a></li>
            <li><a href="{{ route('comparison.index') }}"><i class="bi bi-bar-chart me-2"></i> Country Comparison</a></li>
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

    <!-- MAIN BODY SECTION -->
    <div class="content-container flex-1 flex flex-col min-w-0">

        <!-- TOP BAR HEADER -->
        <header class="bg-white border-b border-slate-100 sticky top-0 z-20 px-8 py-4 flex items-center justify-between shadow-sm">
            <div>
                <div class="flex items-center gap-2">
                    <i class="bi bi-anchor text-2xl text-blue-600"></i>
                    <h2 class="text-2xl font-bold text-slate-900 leading-tight">Ports Intelligence</h2>
                </div>
                <p class="text-slate-500 text-xs font-medium mt-0.5">Real-time overview of global ports, capacity, weather and operational risk.</p>
            </div>

            <!-- Header actions -->
            <div class="flex items-center gap-6">
            </div>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <main class="flex-1 p-8 space-y-8 overflow-y-auto">

            <!-- FILTERS ROW -->
            <form method="GET" action="{{ route('ports') }}" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-end gap-4 justify-between flex-wrap md:flex-nowrap">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 flex-1">
                    <!-- Country -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Country</label>
                        <div class="relative">
                            <select name="country" class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                <option value="">All Countries</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->country_name }}" {{ request('country') === $c->country_name ? 'selected' : '' }}>{{ $c->country_name }}</option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="bi bi-chevron-down text-[8px]"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Port Type -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Port Type</label>
                        <div class="relative">
                            <select name="type" class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                <option value="">All Types</option>
                                @foreach($portTypes as $t)
                                    <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="bi bi-chevron-down text-[8px]"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Status</label>
                        <div class="relative">
                            <select name="status" class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                <option value="">All Status</option>
                                @foreach($portStatuses as $s)
                                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="bi bi-chevron-down text-[8px]"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Search Port input -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Search Port</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <i class="bi bi-search text-xs"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by port name..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-4 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm transition-colors">
                        <i class="bi bi-search text-xs"></i>
                        <span>Search</span>
                    </button>
                    <a href="{{ route('ports') }}" class="bg-slate-50 hover:bg-slate-100 text-slate-700 py-2.5 px-3 border border-slate-200 rounded-xl text-xs font-bold transition-colors text-center">
                        Reset Filter
                    </a>
                    <a href="{{ route('ports.sync') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 px-3 rounded-xl text-xs font-bold transition-colors text-center flex items-center gap-1.5 shadow-sm">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Sync Ports</span>
                    </a>
                </div>
            </form>

            <!-- KPI CARDS ROW -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <!-- KPI 1: Total Ports -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold border border-blue-100 flex-shrink-0">
                        <i class="bi bi-anchor"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Ports</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $totalPorts }}</h3>
                        <span class="text-[9px] font-bold text-slate-400 block mt-0.5">Across {{ $countriesCovered }} countries</span>
                    </div>
                </div>

                <!-- KPI 2: Active Ports -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100 flex-shrink-0">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Active Ports</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $activePorts }}</h3>
                        <span class="text-[9px] font-bold text-emerald-500 block mt-0.5">{{ $totalPorts > 0 ? round(($activePorts / $totalPorts) * 100, 1) : 0 }}% of total ports</span>
                    </div>
                </div>

                <!-- KPI 3: Busy Ports -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                    <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold border border-amber-100 flex-shrink-0">
                        <i class="bi bi-ship"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Busy Ports</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $busyPorts }}</h3>
                        <span class="text-[9px] font-bold text-amber-500 block mt-0.5">High traffic today</span>
                    </div>
                </div>

                <!-- KPI 4: High Risk Ports -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                    <div class="h-12 w-12 rounded-xl bg-red-50 text-red-650 flex items-center justify-center text-xl font-bold border border-red-100 flex-shrink-0">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">High Risk Ports</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $highRiskPorts }}</h3>
                        <span class="text-[9px] font-bold text-red-500 block mt-0.5">Require attention</span>
                    </div>
                </div>

                <!-- KPI 5: Countries Covered -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all">
                    <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold border border-purple-100 flex-shrink-0">
                        <i class="bi bi-globe"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Countries Covered</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $countriesCovered }}</h3>
                        <span class="text-[9px] font-bold text-purple-500 block mt-0.5">Global coverage</span>
                    </div>
                </div>
            </div>

            <!-- MIDDLE SECTION: MAP, PORT INFO, STATISTICS -->
            <div class="grid grid-cols-12 gap-6">
                <!-- Map Panel (6 columns) -->
                <div class="col-span-6 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[480px] relative">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Global Port Map</h3>
                    </div>
                    <div class="flex-1 rounded-xl overflow-hidden border border-slate-100 relative">
                        <div id="portsMap" class="w-full h-full min-h-[350px]"></div>
                        
                        <!-- Legend overlay -->
                        <div class="absolute bottom-4 left-4 z-[1000] bg-white/90 backdrop-blur-sm p-3 rounded-lg border border-slate-100 shadow-md text-[10px] font-bold text-slate-600 space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>
                                <span>Low Risk</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-500 inline-block"></span>
                                <span>Medium Risk</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-red-500 inline-block"></span>
                                <span>High Risk</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Port Information Panel (3 columns) -->
                <div class="col-span-3 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-bold text-slate-900">Port Information</h3>
                            <span id="portInfoStatusBadge" class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-250">Active</span>
                        </div>
                        
                        <div class="space-y-4 text-xs font-semibold text-slate-700" id="portInfoContainer">
                            <!-- Populated dynamically -->
                        </div>
                    </div>

                    <button class="w-full py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all shadow-sm">
                        View Full Details
                    </button>
                </div>

                <!-- Port Statistics Panel (3 columns) -->
                <div class="col-span-3 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col overflow-y-auto max-h-[480px]">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Port Statistics</h3>
                    
                    <div class="space-y-4">
                        <!-- Stat 1: Capacity utilization -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-450 uppercase block mb-1">Cargo Capacity Utilization (%)</span>
                            <div id="utilizationChart" class="w-full"></div>
                        </div>
                        <!-- Stat 2: Daily Ship Traffic -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-450 uppercase block mb-1">Daily Ship Traffic</span>
                            <div id="trafficChart" class="w-full"></div>
                        </div>
                        <!-- Stat 3: Port Status distribution -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-450 uppercase block mb-1">Port Status</span>
                            <div id="statusDonutChart" class="w-full"></div>
                        </div>
                        <!-- Stat 4: Average Dwell time -->
                        <div>
                            <span class="text-[10px] font-bold text-slate-450 uppercase block mb-2">Average Dwell Time (Days)</span>
                            <div class="space-y-1.5 text-[10px]" id="dwellTimeContainer">
                                <!-- Horizontal progress bars populated dynamically -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM SECTION: PORT LIST, WEATHER, NEWS, CONGESTION -->
            <div class="grid grid-cols-12 gap-6">
                <!-- Port List Table (5 columns) -->
                <div class="col-span-5 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[320px]">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Port List</h3>
                    <div class="flex-1 overflow-x-auto min-h-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-2 pl-2">Port Name</th>
                                    <th class="py-2">Country</th>
                                    <th class="py-2">Type</th>
                                    <th class="py-2">Capacity</th>
                                    <th class="py-2">Weather</th>
                                    <th class="py-2">Risk</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2 text-center pr-2">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs font-semibold text-slate-700" id="portListTableBody">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs" id="portListPagination">
                        <!-- Populated dynamically -->
                    </div>
                </div>

                <!-- Weather Impact Panel (2 columns) -->
                <div class="col-span-2 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[320px]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Weather Impact</h3>
                        <a href="#" class="text-[10px] font-bold text-blue-600 hover:text-blue-755 transition-colors">View All</a>
                    </div>
                    <div class="space-y-2.5 flex-1 overflow-y-auto" id="weatherImpactList">
                        <!-- Populated dynamically -->
                    </div>
                </div>

                <!-- Latest Port News Panel (3 columns) -->
                <div class="col-span-3 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[320px]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Latest Port News</h3>
                        <a href="#" class="text-[10px] font-bold text-blue-600 hover:text-blue-755 transition-colors">View All</a>
                    </div>
                    <div class="space-y-3.5 flex-1 overflow-y-auto" id="portNewsList">
                        <!-- Populated dynamically -->
                    </div>
                </div>

                <!-- Port Congestion Panel (2 columns) -->
                <div class="col-span-2 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[320px]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Port Congestion</h3>
                        <a href="#" class="text-[10px] font-bold text-blue-600 hover:text-blue-755 transition-colors">View All</a>
                    </div>
                    <div class="space-y-4 flex-1 flex flex-col justify-center" id="congestionContainer">
                        <!-- Circular progress rings populated dynamically -->
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // Ports array passed from Controller
        const portsData = @json($ports);

        // Country flag emoji generator
        function getFlagEmoji(countryCode) {
            if (!countryCode || countryCode.length !== 2) return '🏳️';
            const codePoints = countryCode
                .toUpperCase()
                .split('')
                .map(char => 127397 + char.charCodeAt(0));
            return String.fromCodePoint(...codePoints);
        }

        // Map database status (sizes) to operational condition
        const statusMap = {
            'Large': 'Active',
            'Medium': 'Busy',
            'Small': 'Congested',
            'Very Small': 'Maintenance',
            'Closed': 'Closed',
            'Normal': 'Active',
            'Active': 'Active'
        };

        // Enrich database ports with deterministic stats for completeness
        const enrichedPorts = portsData.map(p => {
            const seed = p.id * 17;
            
            let capacity = 0;
            let shipsToday = 0;
            let berths = 12 + (seed % 80);
            let dwellTime = (1.2 + (seed % 25) / 10).toFixed(1);

            let status = statusMap[p.status] || p.status || 'Active';

            if (status === 'Busy') {
                capacity = 85 + (seed % 12);
                shipsToday = 50 + (seed % 30);
            } else if (status === 'Congested') {
                capacity = 92 + (seed % 7);
                shipsToday = 65 + (seed % 20);
            } else if (status === 'Maintenance') {
                capacity = 10 + (seed % 15);
                shipsToday = 2 + (seed % 6);
            } else if (status === 'Closed') {
                capacity = 0;
                shipsToday = 0;
            } else { // Active
                capacity = 60 + (seed % 25);
                shipsToday = 20 + (seed % 30);
            }

            const weatherList = [
                { type: 'Rainy', icon: 'bi-cloud-rain-fill', temp: '28°C', risk: 'Medium' },
                { type: 'Cloudy', icon: 'bi-cloud-sun-fill', temp: '27°C', risk: 'Low' },
                { type: 'Sunny', icon: 'bi-sun-fill', temp: '21°C', risk: 'Low' },
                { type: 'Rainy', icon: 'bi-cloud-rain-fill', temp: '18°C', risk: 'Medium' },
                { type: 'Cloudy', icon: 'bi-cloud-sun-fill', temp: '30°C', risk: 'Medium' },
                { type: 'Stormy', icon: 'bi-cloud-lightning-rain-fill', temp: '25°C', risk: 'High' }
            ];

            const weather = weatherList[seed % weatherList.length];

            let riskLevel = 'Low';
            if (status === 'Busy' || status === 'Congested' || status === 'Closed' || weather.risk === 'High') {
                riskLevel = 'Medium';
            }
            if (status === 'Closed' || status === 'Congested' || weather.type === 'Stormy') {
                riskLevel = 'High';
            }

            return {
                id: p.id,
                name: p.port_name,
                countryName: p.country?.country_name || 'Unknown',
                countryCode: p.country?.country_code || '',
                flag: p.country?.flag || getFlagEmoji(p.country?.country_code),
                lat: (p.latitude && !isNaN(parseFloat(p.latitude))) ? parseFloat(p.latitude) : 0,
                lng: (p.longitude && !isNaN(parseFloat(p.longitude))) ? parseFloat(p.longitude) : 0,
                type: p.type || 'Sea Port',
                status: status,
                capacity: capacity,
                shipsToday: shipsToday,
                berths: berths,
                dwellTime: parseFloat(dwellTime),
                weatherType: weather.type,
                weatherIcon: weather.icon,
                weatherTemp: weather.temp,
                riskLevel: riskLevel
            };
        });

        // State variables
        let activePortId = enrichedPorts[0]?.id || '';
        let portListPage = 1;
        const portListPageSize = 5;
        let searchQuery = '';

        // Leaflet Map state
        let map;
        let markersGroup;

        // Setup Leaflet map
        function initMap() {
            if (map) return;
            map = L.map('portsMap', {
                zoomControl: false,
                attributionControl: false
            }).setView([20, 20], 1.5);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 18
            }).addTo(map);

            L.control.zoom({ position: 'topleft' }).addTo(map);

            markersGroup = L.featureGroup().addTo(map);

            // Populate Map Markers
            enrichedPorts.forEach(p => {
                if (p.lat === 0 && p.lng === 0) return;
                if (isNaN(p.lat) || isNaN(p.lng)) return;

                let markerColor = '#10B981'; // Green for Low Risk
                if (p.riskLevel === 'Medium') markerColor = '#F59E0B'; // Orange
                if (p.riskLevel === 'High') markerColor = '#EF4444'; // Red

                const marker = L.circleMarker([p.lat, p.lng], {
                    radius: 7,
                    fillColor: markerColor,
                    color: '#ffffff',
                    weight: 1.5,
                    opacity: 1,
                    fillOpacity: 0.9
                });

                // Popup binding
                marker.bindPopup(`
                    <div class="text-xs space-y-1">
                        <div class="font-bold text-slate-900 leading-tight">${p.name}</div>
                        <div class="text-[10px] text-slate-450">${p.flag} ${p.countryName}</div>
                        <div class="flex justify-between items-center gap-4 border-t border-slate-100 pt-1 mt-1">
                            <span class="text-[10px] text-slate-400 font-semibold">Capacity</span>
                            <span class="font-bold text-slate-800 font-sans">${p.capacity}%</span>
                        </div>
                    </div>
                `);

                // Update detail card on marker click
                marker.on('click', () => {
                    setActivePort(p.id);
                });

                markersGroup.addLayer(marker);
            });

            // Adjust bounds if markers present
            if (markersGroup.getLayers().length > 0) {
                map.fitBounds(markersGroup.getBounds().pad(0.15));
            }
        }

        // Set active port view details
        function setActivePort(id) {
            activePortId = id;
            const p = enrichedPorts.find(x => x.id === id);
            if (!p) return;

            // Highlight status badge
            const statusBadge = document.getElementById('portInfoStatusBadge');
            statusBadge.innerText = p.status;
            statusBadge.className = `px-2 py-0.5 rounded text-[9px] font-extrabold uppercase border `;
            if (p.status === 'Active') statusBadge.className += 'bg-emerald-50 text-emerald-700 border-emerald-250';
            else if (p.status === 'Busy') statusBadge.className += 'bg-amber-50 text-amber-700 border-amber-250';
            else if (p.status === 'Congested') statusBadge.className += 'bg-red-50 text-red-750 border-red-200';
            else if (p.status === 'Maintenance') statusBadge.className += 'bg-blue-50 text-blue-700 border-blue-200';
            else statusBadge.className += 'bg-red-50 text-red-700 border-red-250';

            // Populate text fields
            const container = document.getElementById('portInfoContainer');
            
            let riskBadgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
            if (p.riskLevel === 'Medium') riskBadgeClass = 'bg-amber-50 text-amber-750 border border-amber-200';
            if (p.riskLevel === 'High') riskBadgeClass = 'bg-red-50 text-red-700 border border-red-200';

            container.innerHTML = `
                <div class="flex justify-between py-1.5 border-b border-slate-50">
                    <span class="text-slate-400">Port Name</span>
                    <span class="text-slate-800 font-bold max-w-[140px] truncate block text-right" title="${p.name}">${p.name}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-50">
                    <span class="text-slate-400">Country</span>
                    <span class="text-slate-800 font-bold">${p.flag} ${p.countryName}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-50">
                    <span class="text-slate-400">Port Type</span>
                    <span class="text-slate-800 font-bold">${p.type}</span>
                </div>
                <div class="py-1.5 border-b border-slate-50">
                    <div class="flex justify-between mb-1">
                        <span class="text-slate-400">Capacity Utilization</span>
                        <span class="text-slate-800 font-bold">${p.capacity}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: ${p.capacity}%"></div>
                    </div>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-50">
                    <span class="text-slate-400">Weather Condition</span>
                    <span class="text-slate-800 font-bold flex items-center gap-1">
                        <i class="bi ${p.weatherIcon} text-blue-500"></i> ${p.weatherType}, ${p.weatherTemp}
                    </span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-50">
                    <span class="text-slate-400">Risk Level</span>
                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase ${riskBadgeClass}">${p.riskLevel}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-50">
                    <span class="text-slate-400">Coordinates</span>
                    <span class="text-slate-800 font-bold font-mono">${(p.lat || 0).toFixed(4)}° N, ${(p.lng || 0).toFixed(4)}° E</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-50">
                    <span class="text-slate-400">Total Berths</span>
                    <span class="text-slate-800 font-bold">${p.berths}</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-slate-400">Ships Today</span>
                    <span class="text-slate-800 font-bold">${p.shipsToday}</span>
                </div>
            `;

            // Active highlighting in table
            updateTableHighlight();
        }

        // Render port statistics (ApexCharts)
        function initStatistics() {
            // 1. Cargo Capacity Utilization Chart
            const top5 = enrichedPorts.slice(0, 5);
            const categories = top5.map(p => p.name.replace('Port of ', '').substring(0, 3).toUpperCase());
            const capacities = top5.map(p => p.capacity);

            const utilOpt = {
                series: [{
                    name: 'Utilization',
                    data: capacities.length > 0 ? capacities : [85, 91, 76, 88, 70]
                }],
                chart: { type: 'bar', height: 110, toolbar: { show: false } },
                plotOptions: { bar: { columnWidth: '40%', borderRadius: 2 } },
                colors: ['#2563EB'],
                dataLabels: { enabled: false },
                xaxis: {
                    categories: categories.length > 0 ? categories : ['SGP', 'PVG', 'HAM', 'RTM', 'TPK'],
                    labels: { style: { fontSize: '8px', colors: '#94a3b8' } }
                },
                yaxis: { max: 100, tickAmount: 2, labels: { style: { fontSize: '8px', colors: '#94a3b8' } } },
                grid: { show: false }
            };
            new ApexCharts(document.querySelector("#utilizationChart"), utilOpt).render();

            // 2. Daily Ship Traffic Chart
            const trafficOpt = {
                series: [{
                    name: 'Ships',
                    data: [25, 42, 64, 49, 58, 70, 52]
                }],
                chart: { type: 'line', height: 110, toolbar: { show: false } },
                stroke: { curve: 'smooth', width: 2 },
                colors: ['#2563EB'],
                markers: { size: 3 },
                xaxis: {
                    categories: ['19 Jul', '20 Jul', '21 Jul', '22 Jul', '23 Jul', '24 Jul', '25 Jul'],
                    labels: { style: { fontSize: '8px', colors: '#94a3b8' } }
                },
                yaxis: { show: false },
                grid: { show: false }
            };
            new ApexCharts(document.querySelector("#trafficChart"), trafficOpt).render();

            // 3. Status Donut Chart
            const activeCount = enrichedPorts.filter(p => p.status === 'Active').length;
            const maintCount = enrichedPorts.filter(p => p.status === 'Maintenance').length;
            const closedCount = enrichedPorts.filter(p => p.status === 'Closed').length;

            const donutOpt = {
                series: [activeCount || 10, maintCount || 2, closedCount || 1],
                chart: { type: 'donut', height: 115 },
                labels: ['Active', 'Maint.', 'Closed'],
                colors: ['#10B981', '#F59E0B', '#EF4444'],
                dataLabels: { enabled: false },
                legend: { position: 'right', fontSize: '9px', labels: { colors: '#64748b' } },
                plotOptions: { pie: { donut: { size: '65%' } } }
            };
            new ApexCharts(document.querySelector("#statusDonutChart"), donutOpt).render();

            // 4. Average Dwell Time List
            const dwellContainer = document.getElementById('dwellTimeContainer');
            dwellContainer.innerHTML = '';
            top5.forEach(p => {
                const percent = (p.dwellTime / 4.0) * 100;
                dwellContainer.innerHTML += `
                    <div>
                        <div class="flex justify-between mb-0.5 text-[9px]">
                            <span class="text-slate-500 font-bold">${p.name.replace('Port of ', '')}</span>
                            <span class="text-slate-800 font-extrabold">${p.dwellTime} Days</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden">
                            <div class="bg-blue-600 h-1 rounded-full" style="width: ${percent}%"></div>
                        </div>
                    </div>
                `;
            });
        }

        // Render port list table
        function updatePortListTable() {
            const tbody = document.getElementById('portListTableBody');
            tbody.innerHTML = '';

            const filtered = enrichedPorts.filter(p => {
                if (!searchQuery) return true;
                const q = searchQuery.toLowerCase();
                return p.name.toLowerCase().includes(q) ||
                       p.countryName.toLowerCase().includes(q) ||
                       p.type.toLowerCase().includes(q) ||
                       p.status.toLowerCase().includes(q);
            });

            const total = filtered.length;
            const totalPages = Math.ceil(total / portListPageSize) || 1;

            if (portListPage > totalPages) portListPage = totalPages;
            if (portListPage < 1) portListPage = 1;

            const startIdx = (portListPage - 1) * portListPageSize;
            const endIdx = Math.min(startIdx + portListPageSize, total);
            const paginated = filtered.slice(startIdx, endIdx);

            if (paginated.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="py-6 text-center text-slate-400 font-bold text-xs">No ports found.</td>
                    </tr>
                `;
                updatePagination(0, 0, 0, 1);
                return;
            }

            paginated.forEach(p => {
                let statusBadge = '';
                if (p.status === 'Active') statusBadge = 'bg-emerald-50 text-emerald-700 border-emerald-250';
                else if (p.status === 'Busy') statusBadge = 'bg-amber-50 text-amber-700 border-amber-250';
                else if (p.status === 'Congested') statusBadge = 'bg-red-50 text-red-750 border-red-200';
                else if (p.status === 'Maintenance') statusBadge = 'bg-blue-50 text-blue-700 border-blue-200';
                else statusBadge = 'bg-red-50 text-red-755 border-red-200';

                let riskBadge = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                if (p.riskLevel === 'Medium') riskBadge = 'bg-amber-50 text-amber-600 border-amber-100';
                if (p.riskLevel === 'High') riskBadge = 'bg-red-50 text-red-600 border-red-100';

                const isActive = p.id === activePortId;
                const rowClass = isActive ? 'bg-blue-50/40 hover:bg-blue-50/50' : 'hover:bg-slate-50/50';

                tbody.innerHTML += `
                    <tr class="${rowClass} transition-all cursor-pointer" onclick="setActivePort(${p.id})">
                        <td class="py-2 pl-2 font-bold text-slate-800 max-w-[100px] truncate" title="${p.name}">${p.name}</td>
                        <td class="py-2 truncate max-w-[60px]">${p.flag} ${p.countryName}</td>
                        <td class="py-2 text-[10px] text-slate-400">${p.type}</td>
                        <td class="py-2 font-bold text-slate-800">${p.capacity}%</td>
                        <td class="py-2 text-slate-500 font-medium">
                            <span class="flex items-center gap-1">
                                <i class="bi ${p.weatherIcon} text-blue-500"></i> ${p.weatherTemp}
                            </span>
                        </td>
                        <td class="py-2">
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase border ${riskBadge}">
                                ${p.riskLevel}
                            </span>
                        </td>
                        <td class="py-2">
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase border ${statusBadge}">
                                ${p.status}
                            </span>
                        </td>
                        <td class="py-2 text-center pr-2" onclick="event.stopPropagation()">
                            <button onclick="setActivePort(${p.id})" class="p-1 text-slate-400 hover:text-slate-700 transition-colors" title="View details">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            updatePagination(startIdx + 1, endIdx, total, totalPages);
        }

        // Pagination helper
        function updatePagination(start, end, total, totalPages) {
            const container = document.getElementById('portListPagination');
            container.innerHTML = '';

            if (total === 0) {
                container.innerHTML = `<span class="text-slate-400">Showing 0 to 0 of 0 ports</span>`;
                return;
            }

            const text = document.createElement('span');
            text.className = 'text-slate-550 font-medium';
            text.innerText = `Showing ${start} to ${end} of ${total} ports`;
            container.appendChild(text);

            const btnDiv = document.createElement('div');
            btnDiv.className = 'flex items-center gap-1';

            const prevBtn = document.createElement('button');
            prevBtn.className = `h-7 px-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors ${portListPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}`;
            prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
            if (portListPage > 1) {
                prevBtn.onclick = () => { portListPage--; updatePortListTable(); };
            }
            btnDiv.appendChild(prevBtn);

            for (let i = 1; i <= totalPages; i++) {
                const pgBtn = document.createElement('button');
                if (i === portListPage) {
                    pgBtn.className = 'h-7 w-7 bg-blue-600 text-white rounded-lg font-bold shadow-sm';
                } else {
                    pgBtn.className = 'h-7 w-7 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-655 font-medium transition-colors';
                }
                pgBtn.innerText = i;
                pgBtn.onclick = () => { portListPage = i; updatePortListTable(); };
                btnDiv.appendChild(pgBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = `h-7 px-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-655 transition-colors ${portListPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}`;
            nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
            if (portListPage < totalPages) {
                nextBtn.onclick = () => { portListPage++; updatePortListTable(); };
            }
            btnDiv.appendChild(nextBtn);

            container.appendChild(btnDiv);
        }

        // Highlight active table row
        function updateTableHighlight() {
            const rows = document.querySelectorAll('#portListTableBody tr');
            rows.forEach(row => {
                const viewBtn = row.querySelector('button');
                if (viewBtn) {
                    // Extract ID from onclick
                    const match = viewBtn.getAttribute('onclick').match(/\d+/);
                    if (match) {
                        const portId = parseInt(match[0]);
                        if (portId === activePortId) {
                            row.className = 'bg-blue-50/40 hover:bg-blue-50/50 transition-all cursor-pointer';
                        } else {
                            row.className = 'hover:bg-slate-50/50 transition-all cursor-pointer';
                        }
                    }
                }
            });
        }

        // Render weather impact list (left column)
        function initWeatherImpactList() {
            const list = document.getElementById('weatherImpactList');
            list.innerHTML = '';
            
            // Get 5 representative ports
            const ports = enrichedPorts.slice(0, 5);
            ports.forEach(p => {
                let riskBadge = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                if (p.riskLevel === 'Medium') riskBadge = 'bg-amber-50 text-amber-600 border-amber-100';
                if (p.riskLevel === 'High') riskBadge = 'bg-red-50 text-red-650 border-red-100';

                list.innerHTML += `
                    <div class="flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div class="truncate max-w-[80px]">
                            <span class="text-xs font-bold text-slate-800 block truncate" title="${p.name}">${p.name.replace('Port of ', '')}</span>
                            <span class="text-[9px] text-slate-450 block mt-0.5">${p.flag} ${p.countryName}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="text-right">
                                <span class="text-[10px] font-extrabold text-slate-800 block">${p.weatherTemp}</span>
                                <span class="text-[8px] font-semibold text-slate-400 block mt-0.5 flex items-center gap-0.5">
                                    <i class="bi ${p.weatherIcon} text-[9px] text-blue-500"></i> ${p.weatherType}
                                </span>
                            </div>
                            <span class="px-1.5 py-0.5 rounded text-[7px] font-extrabold uppercase border ${riskBadge}">${p.riskLevel}</span>
                        </div>
                    </div>
                `;
            });
        }

        // Render news list
        function initNewsList() {
            const container = document.getElementById('portNewsList');
            container.innerHTML = '';

            const newsData = [
                { title: 'Heavy Rain Causes Delay at Singapore Port', desc: 'Container handling operations affected...', category: 'Logistics', date: '25 Jul 2026 10:30', image: 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=150&auto=format&fit=crop&q=60' },
                { title: 'Port Congestion in Shanghai', desc: 'Increased cargo volume leads to congestion...', category: 'Shipping', date: '25 Jul 2026 09:15', image: 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?w=150&auto=format&fit=crop&q=60' },
                { title: 'Rotterdam Increases Cargo Capacity', desc: 'Expansion project expected to be completed...', category: 'Trade', date: '24 Jul 2026 16:45', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=150&auto=format&fit=crop&q=60' }
            ];

            newsData.forEach(item => {
                container.innerHTML += `
                    <div class="flex gap-2.5 items-start border-b border-slate-50 last:border-0 pb-3 last:pb-0">
                        <img src="${item.image}" alt="${item.title}" class="h-10 w-14 object-cover rounded-lg border border-slate-100 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 hover:text-blue-600 transition-colors cursor-pointer" title="${item.title}">${item.title}</h4>
                            <p class="text-[8px] text-slate-400 mt-1 flex items-center gap-1.5">
                                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded font-extrabold uppercase text-[6px]">${item.category}</span>
                                <span>•</span>
                                <span>${item.date}</span>
                            </p>
                        </div>
                    </div>
                `;
            });
        }

        // Render circular progress rings for congestion
        function initCongestion() {
            const container = document.getElementById('congestionContainer');
            container.innerHTML = '';

            const data = [
                { name: 'Singapore', val: 85, color: '#F59E0B', label: 'Busy' },
                { name: 'Shanghai', val: 96, color: '#EF4444', label: 'Very Busy' },
                { name: 'Hamburg', val: 45, color: '#10B981', label: 'Normal' }
            ];

            data.forEach((d, idx) => {
                const chartId = `congestionRing_${idx}`;
                
                let badgeClass = '';
                if (d.label === 'Very Busy') badgeClass = 'bg-red-50 text-red-700 border-red-250';
                else if (d.label === 'Busy') badgeClass = 'bg-amber-50 text-amber-700 border-amber-250';
                else badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-250';

                container.innerHTML += `
                    <div class="flex items-center justify-between border-b border-slate-50 last:border-0 pb-2 last:pb-0">
                        <div class="flex items-center gap-2">
                            <div id="${chartId}" class="w-14 h-14"></div>
                            <div>
                                <span class="text-xs font-extrabold text-slate-800 block">${d.name}</span>
                                <span class="text-[9px] text-slate-400 font-semibold block mt-0.5">Capacity Utilization</span>
                            </div>
                        </div>
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase border ${badgeClass}">${d.label}</span>
                    </div>
                `;

                // Render RadialBar Chart asynchronously
                setTimeout(() => {
                    const opt = {
                        series: [d.val],
                        chart: { type: 'radialBar', height: 65, sparkline: { enabled: true } },
                        colors: [d.color],
                        plotOptions: {
                            radialBar: {
                                hollow: { size: '55%' },
                                dataLabels: {
                                    name: { show: false },
                                    value: {
                                        offsetY: 4,
                                        fontSize: '9px',
                                        fontWeight: 'bold',
                                        formatter: (v) => `${v}%`
                                    }
                                }
                            }
                        }
                    };
                    new ApexCharts(document.querySelector(`#${chartId}`), opt).render();
                }, 50);
            });
        }

        // On document load
        document.addEventListener("DOMContentLoaded", function() {
            initMap();
            
            if (enrichedPorts.length > 0) {
                setActivePort(enrichedPorts[0].id);
            }

            initStatistics();
            updatePortListTable();
            initWeatherImpactList();
            initNewsList();
            initCongestion();

            // Header Search handler
            document.getElementById('globalSearch').addEventListener('input', (e) => {
                searchQuery = e.target.value;
                portListPage = 1;
                updatePortListTable();
            });
        });
    </script>
</body>
</html>