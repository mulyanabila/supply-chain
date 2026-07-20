<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shipment Monitoring - GSC Risk Intelligence</title>

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
                            sidebar: '#123F63',
                            blue: '#1A56DB',
                            bg: '#F8FAFC'
                        }
                    }
                }
            }
        }
    </script>

    <!-- ApexCharts & Leaflet -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
    </style>
</head>
<body class="bg-brand-bg text-slate-800 min-h-screen font-sans flex antialiased">

    @php
        // Resolve active shipment from query param if provided, else default
        $requestedId = request('active_shp');
        $activeShipment = $filtered->firstWhere('id', $requestedId) ?? $activeShipment;

        // Dynamic counts from the filtered collection
        $totalShipmentCount = $filtered->count();
        $inTransitCount = $filtered->where('status', 'In Transit')->count();
        $deliveredCount = $filtered->where('status', 'Delivered')->count();
        $delayedCount = $filtered->where('status', 'Delayed')->count();
        $highRiskCount = $filtered->where('risk_level', 'High')->count();

        // Dynamic region counts
        $asiaCount = 0;
        $europeCount = 0;
        $naCount = 0;
        $africaCount = 0;
        $saCount = 0;

        foreach($filtered as $s) {
            $c = $s['origin'];
            if (in_array($c, ['China', 'South Korea', 'India', 'Singapore'])) {
                $asiaCount++;
            } elseif (in_array($c, ['Germany'])) {
                $europeCount++;
            } elseif (in_array($c, ['United States'])) {
                $naCount++;
            }
        }
    @endphp

    <!-- Sidebar -->
<aside class="fixed top-0 left-0 h-screen w-64 bg-[#123F63] text-white flex flex-col">

    <!-- Logo -->
    <div class="px-8 py-8">
        <h1 class="text-3xl mb-2">🌍</h1>

        <h2 class="font-bold text-2xl leading-tight">
            GSC RISK
        </h2>

        <h3 class="font-bold text-2xl leading-tight">
            INTELLIGENCE
        </h3>
    </div>

    <!-- Menu -->
    <nav class="flex-1 mt-6">
        <ul class="space-y-3">

            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-4 px-8 py-3 hover:bg-white/10">
                    <i class="bi bi-speedometer2 text-2xl"></i>
                    <span class="text-xl">Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('countries') }}"
                    class="flex items-center gap-4 px-8 py-3 hover:bg-white/10">
                    <i class="bi bi-globe text-2xl"></i>
                    <span class="text-xl">Countries</span>
                </a>
            </li>

            <li>
                <a href="{{ route('ports') }}"
                    class="flex items-center gap-4 px-8 py-3 hover:bg-white/10">
                    <i class="bi bi-geo-alt text-2xl"></i>
                    <span class="text-xl">Ports</span>
                </a>
            </li>

            <li>
                <a href="{{ route('shipment') }}"
                    class="flex items-center gap-4 px-8 py-3 bg-white/10 rounded-lg">
                    <i class="bi bi-truck text-2xl"></i>
                    <span class="text-xl font-semibold">Shipment</span>
                </a>
            </li>

            <li>
                <a href="{{ route('weather.monitoring') }}"
                    class="flex items-center gap-4 px-8 py-3 hover:bg-white/10">
                    <i class="bi bi-cloud-sun text-2xl"></i>
                    <span class="text-xl">Weather</span>
                </a>
            </li>

            <li>
                <a href="{{ route('news.index') }}"
                    class="flex items-center gap-4 px-8 py-3 hover:bg-white/10">
                    <i class="bi bi-newspaper text-2xl"></i>
                    <span class="text-xl">News</span>
                </a>
            </li>

            <li>
                <a href="{{ route('watchlist.index') }}"
                    class="flex items-center gap-4 px-8 py-3 hover:bg-white/10">
                    <i class="bi bi-bookmark text-2xl"></i>
                    <span class="text-xl">Watchlist</span>
                </a>
            </li>

            <li>
                <a href="{{ route('comparison.index') }}"
                    class="flex items-center gap-4 px-8 py-3 hover:bg-white/10">
                    <i class="bi bi-bar-chart text-2xl"></i>
                    <span class="text-xl">Country Comparison</span>
                </a>
            </li>

        </ul>
    </nav>

    <!-- Logout -->
    <div class="p-8">
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                class="flex items-center gap-4 text-xl hover:text-red-300">
                <i class="bi bi-box-arrow-right text-2xl"></i>
                Logout
            </button>
        </form>
    </div>

</aside>

    <!-- MAIN BODY SECTION -->
    <div class="flex-1 pl-64 flex flex-col min-w-0">

        <!-- TOP BAR HEADER -->
        <header class="bg-white border-b border-slate-100 sticky top-0 z-20 px-8 py-4 flex items-center justify-between shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">Shipment Monitoring</h2>
                <p class="text-slate-500 text-xs font-medium">Real-time monitoring of global shipments and supply chain visibility.</p>
            </div>

            <!-- Header actions -->
            <div class="flex items-center gap-6">
                <!-- Search bar -->
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="bi bi-search text-sm"></i>
                    </span>
                    <input type="text" placeholder="Search anything..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200 text-slate-700 placeholder-slate-400">
                </div>

                <!-- Notifications -->
                <button class="relative p-2 bg-slate-50 hover:bg-slate-100 rounded-xl text-slate-600 transition-all duration-200 border border-slate-150">
                    <i class="bi bi-bell text-lg"></i>
                    <span class="absolute top-0.5 right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">5</span>
                </button>

                <!-- Profile avatar -->
                <div class="flex items-center gap-3 border-l border-slate-200 pl-6">
                    <div class="h-10 w-10 rounded-full bg-slate-800 text-white font-bold flex items-center justify-center border-2 border-brand-blue/20">
                        JD
                    </div>
                    <div class="text-left">
                        <h4 class="text-sm font-bold text-slate-800 leading-4">John Doe</h4>
                        <span class="text-[11px] font-medium text-slate-400 uppercase tracking-wider block mt-0.5">User</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <main class="flex-1 p-8 space-y-8 overflow-y-auto">

            <!-- FILTERS ROW -->
            <form method="GET" action="{{ route('shipment') }}" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <!-- Origin -->
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Origin</label>
                    <div class="relative">
                        <select name="origin" class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                            <option value="All Origins">All Origins</option>
                            @foreach($originsList as $orig)
                                <option value="{{ $orig }}" {{ request('origin') === $orig ? 'selected' : '' }}>{{ $orig }}</option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <i class="bi bi-chevron-down text-[8px]"></i>
                        </span>
                    </div>
                </div>

                <!-- Destination -->
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Destination</label>
                    <div class="relative">
                        <select name="destination" class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                            <option value="All Destinations">All Destinations</option>
                            @foreach($destinationsList as $dest)
                                <option value="{{ $dest }}" {{ request('destination') === $dest ? 'selected' : '' }}>{{ $dest }}</option>
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
                            <option value="All Status">All Status</option>
                            <option value="In Transit" {{ request('status') === 'In Transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="Delayed" {{ request('status') === 'Delayed' ? 'selected' : '' }}>Delayed</option>
                            <option value="Delivered" {{ request('status') === 'Delivered' ? 'selected' : '' }}>Delivered</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <i class="bi bi-chevron-down text-[8px]"></i>
                        </span>
                    </div>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Date Range</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <i class="bi bi-calendar text-xs"></i>
                        </span>
                        <input type="text" placeholder="13 Jul 2026 - 20 Jul 2026" class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 placeholder-slate-450 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-4 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm transition-all duration-200">
                        <i class="bi bi-search text-xs"></i>
                        <span>Search Shipment</span>
                    </button>
                    <a href="{{ route('shipment') }}" class="bg-slate-50 hover:bg-slate-100 text-slate-700 py-2.5 px-3 border border-slate-200 rounded-xl text-xs font-bold transition-all duration-200 text-center">
                        Reset Filter
                    </a>
                </div>
            </form>

            <!-- METRICS ROW (5 Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <!-- Card 1: Total Shipment -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-xl font-bold border border-blue-100 flex-shrink-0">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Shipment</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($totalShipmentCount) }}</h3>
                        <span class="text-[9px] font-bold text-emerald-500 flex items-center gap-0.5 mt-0.5">
                            <i class="bi bi-arrow-up-short text-xs leading-none"></i> 12.5% vs last 7d
                        </span>
                    </div>
                </div>

                <!-- Card 2: In Transit -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100 flex-shrink-0">
                        <i class="bi bi-truck-flatbed"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">In Transit</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($inTransitCount) }}</h3>
                        <span class="text-[9px] font-bold text-emerald-500 flex items-center gap-0.5 mt-0.5">
                            <i class="bi bi-arrow-up-short text-xs leading-none"></i> 8.3% vs last 7d
                        </span>
                    </div>
                </div>

                <!-- Card 3: Delivered -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold border border-purple-100 flex-shrink-0">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Delivered</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($deliveredCount) }}</h3>
                        <span class="text-[9px] font-bold text-emerald-500 flex items-center gap-0.5 mt-0.5">
                            <i class="bi bi-arrow-up-short text-xs leading-none"></i> 15.7% vs last 7d
                        </span>
                    </div>
                </div>

                <!-- Card 4: Delayed -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl font-bold border border-orange-100 flex-shrink-0">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Delayed</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($delayedCount) }}</h3>
                        <span class="text-[9px] font-bold text-red-500 flex items-center gap-0.5 mt-0.5">
                            <i class="bi bi-arrow-down-short text-xs leading-none"></i> 3.2% vs last 7d
                        </span>
                    </div>
                </div>

                <!-- Card 5: High Risk Route -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl font-bold border border-red-100 flex-shrink-0">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">High Risk Route</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($highRiskCount) }}</h3>
                        <span class="text-[9px] font-bold text-emerald-500 flex items-center gap-0.5 mt-0.5">
                            <i class="bi bi-arrow-up-short text-xs leading-none"></i> 2.1% vs last 7d
                        </span>
                    </div>
                </div>
            </div>

            <!-- MIDDLE SECTION (Route Map & Timeline Grid) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Shipment Route Map (col-span-2) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[400px] lg:col-span-2 relative">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Global Shipment Route Map</h3>
                        <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 transition-all duration-150">View Full Map</a>
                    </div>

                    <!-- Leaflet map -->
                    <div class="flex-1 rounded-xl overflow-hidden border border-slate-100 relative z-10">
                        <div id="shipmentMap" class="w-full h-full min-h-[250px]"></div>
                    </div>

                    <!-- Floating detail overlay -->
                    <div class="absolute bottom-10 right-10 z-20 bg-white/95 backdrop-blur-md p-4 rounded-xl border border-slate-150 shadow-lg w-56 text-xs font-semibold space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-1.5 mb-1.5">
                            <span class="text-slate-400 text-[10px] font-bold uppercase">Shipment Detail</span>
                            <span class="text-brand-blue font-extrabold text-[10px] font-mono bg-blue-50 px-1.5 py-0.5 rounded">{{ $activeShipment['id'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-semibold">Origin</span>
                            <span class="text-slate-800 font-bold">{{ $activeShipment['origin'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-semibold">Destination</span>
                            <span class="text-slate-800 font-bold">{{ $activeShipment['destination'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-semibold">Cargo</span>
                            <span class="text-slate-800 font-bold">{{ $activeShipment['cargo'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-semibold">Status</span>
                            <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $activeShipment['status_class'] }}">{{ $activeShipment['status'] }}</span>
                        </div>
                        <div class="flex justify-between border-t border-slate-100 pt-1.5 mt-1.5">
                            <span class="text-slate-400 font-semibold">ETA</span>
                            <span class="text-slate-800 font-extrabold">{{ $activeShipment['eta'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipment Timeline (col-span-1) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-1">
                    <h3 class="text-sm font-bold text-slate-900 mb-6">Shipment Timeline</h3>

                    <div class="flex-1 relative pl-6 space-y-6">
                        <!-- Vertical line indicator -->
                        <div class="absolute left-2.5 top-2.5 bottom-2.5 w-0.5 bg-slate-100"></div>

                        @foreach($activeShipment['timeline'] as $step)
                        <!-- Step -->
                        <div class="relative flex gap-3 min-w-0">
                            <!-- Bullet marker -->
                            <div class="absolute -left-[23px] h-[13px] w-[13px] rounded-full border-2 flex items-center justify-center
                                @if($step['completed']) bg-blue-600 border-blue-600 text-white @else bg-white border-slate-300 @endif">
                                @if($step['completed'])
                                    <i class="bi bi-check-lg text-[8px] leading-none"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-bold @if($step['completed']) text-slate-800 @else text-slate-400 @endif">{{ $step['title'] }}</h4>
                                    <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">{{ $step['time'] }}</span>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $step['location'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- TABLE ROW (List & Risk Alerts) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Shipment List (col-span-2) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-2">
                    <h3 class="text-sm font-bold text-slate-900 mb-6">Shipment List</h3>

                    <!-- Table -->
                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3 pl-4">Shipment ID</th>
                                    <th class="py-3">Origin</th>
                                    <th class="py-3">Destination</th>
                                    <th class="py-3">Transit Port</th>
                                    <th class="py-3">Cargo</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Risk Level</th>
                                    <th class="py-3">ETA</th>
                                    <th class="py-3 text-center pr-4">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs font-semibold text-slate-700">
                                @forelse($filtered as $shp)
                                <tr class="hover:bg-slate-50/50 transition-all duration-150 cursor-pointer @if($shp['id'] === $activeShipment['id']) bg-blue-50/30 @endif"
                                    onclick="window.location.href='?origin={{ request('origin') }}&destination={{ request('destination') }}&status={{ request('status') }}&active_shp={{ $shp['id'] }}'">
                                    <td class="py-3.5 pl-4 font-bold text-brand-blue font-mono">{{ $shp['id'] }}</td>
                                    <td class="py-3.5">
                                        <span class="flex items-center gap-1.5">
                                            <span>{{ $shp['origin_flag'] }}</span>
                                            <span>{{ $shp['origin'] }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3.5">
                                        <span class="flex items-center gap-1.5">
                                            <span>{{ $shp['destination_flag'] }}</span>
                                            <span>{{ $shp['destination'] }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3.5">
                                        <span class="flex items-center gap-1.5">
                                            <span>{{ $shp['transit_flag'] }}</span>
                                            <span>{{ $shp['transit'] }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3.5 text-slate-500 font-medium">{{ $shp['cargo'] }}</td>
                                    <td class="py-3.5">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $shp['status_class'] }}">
                                            {{ $shp['status'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5">
                                        <span class="px-2 py-0.5 rounded-lg border text-[9px] font-extrabold uppercase {{ $shp['risk_class'] }}">
                                            {{ $shp['risk_level'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 text-slate-500 font-bold">{{ $shp['eta'] }}</td>
                                    <td class="py-3.5 text-center pr-4" onclick="event.stopPropagation()">
                                        <div class="flex items-center justify-center gap-1 text-slate-400">
                                            <button class="p-1 hover:text-slate-700 transition-all duration-150"><i class="bi bi-eye"></i></button>
                                            <button class="p-1 hover:text-slate-700 transition-all duration-150"><i class="bi bi-printer"></i></button>
                                            <button class="p-1 hover:text-slate-700 transition-all duration-150"><i class="bi bi-three-dots"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center text-slate-400 font-bold">No shipments match the filter query.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <span class="text-xs font-semibold text-slate-500">Showing 1 to {{ $filtered->count() }} of 1,248 shipments</span>
                        <div class="flex items-center gap-1 text-xs">
                            <button class="h-8 px-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-all duration-150"><i class="bi bi-chevron-left"></i></button>
                            <button class="h-8 w-8 bg-brand-blue text-white rounded-lg font-bold">1</button>
                            <button class="h-8 w-8 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-655 font-bold transition-all duration-150">2</button>
                            <button class="h-8 w-8 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-655 font-bold transition-all duration-150">3</button>
                            <button class="h-8 px-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-all duration-150"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Risk Alerts (col-span-1) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-1">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-sm font-bold text-slate-900">Risk Alerts</h3>
                        <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 transition-all duration-150">View All</a>
                    </div>

                    <div class="space-y-4 flex-1">
                        <!-- Alert 1 -->
                        <div class="flex gap-3 pb-3.5 border-b border-slate-50 last:border-0">
                            <div class="h-8 w-8 bg-amber-50 text-amber-600 rounded-full border border-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-cloud-rain-fill text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">Heavy Rain</h4>
                                    <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">20 Jul 09:00</span>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5">Singapore Port</p>
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase bg-amber-50 text-amber-600 mt-1 inline-block">Medium</span>
                            </div>
                        </div>

                        <!-- Alert 2 -->
                        <div class="flex gap-3 pb-3.5 border-b border-slate-50 last:border-0">
                            <div class="h-8 w-8 bg-red-50 text-red-650 rounded-full border border-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-exclamation-octagon-fill text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">Port Congestion</h4>
                                    <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">20 Jul 08:30</span>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5">Shanghai Port</p>
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase bg-red-50 text-red-600 mt-1 inline-block">High</span>
                            </div>
                        </div>

                        <!-- Alert 3 -->
                        <div class="flex gap-3 pb-3.5 border-b border-slate-50 last:border-0">
                            <div class="h-8 w-8 bg-red-50 text-red-650 rounded-full border border-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-wind text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">Typhoon Warning</h4>
                                    <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">20 Jul 07:45</span>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5">Okinawa, Japan</p>
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase bg-red-50 text-red-600 mt-1 inline-block">High</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM ROW (Charts & Weather Impact Grid) -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Shipment Status Chart -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <h3 class="text-sm font-bold text-slate-900 mb-6">Shipment Status</h3>
                    <div class="flex-1 flex flex-col justify-center">
                        <div class="flex items-center gap-6 justify-center">
                            <div class="relative w-28 h-28 flex items-center justify-center flex-shrink-0">
                                <div id="shipmentStatusChart" class="w-full h-full"></div>
                                 <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-lg font-extrabold text-slate-800">{{ $totalShipmentCount }}</span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase leading-none">Total</span>
                                </div>
                            </div>
                            <div class="flex-1 space-y-2 text-[10px] font-bold text-slate-600">
                                <div class="flex justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="h-2.5 w-2.5 rounded-full bg-blue-600 inline-block"></span>
                                        <span>In Transit</span>
                                    </div>
                                    <span>{{ $inTransitCount }} ({{ $totalShipmentCount > 0 ? round(($inTransitCount / $totalShipmentCount) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="flex justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>
                                        <span>Delivered</span>
                                    </div>
                                    <span>{{ $deliveredCount }} ({{ $totalShipmentCount > 0 ? round(($deliveredCount / $totalShipmentCount) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="flex justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="h-2.5 w-2.5 rounded-full bg-orange-500 inline-block"></span>
                                        <span>Delayed</span>
                                    </div>
                                    <span>{{ $delayedCount }} ({{ $totalShipmentCount > 0 ? round(($delayedCount / $totalShipmentCount) * 100, 1) : 0 }}%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipment Trend Chart -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-bold text-slate-900">Shipment Trend</h3>
                        <span class="text-[10px] font-bold text-slate-400">Last 7 Days</span>
                    </div>
                    <div class="flex-1 relative">
                        <div id="shipmentTrendChart" class="w-full h-36"></div>
                    </div>
                </div>

                <!-- Shipment by Region Chart -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-bold text-slate-900">Shipment by Region</h3>
                        <span class="text-[10px] font-bold text-slate-400">This Month</span>
                    </div>
                    <div class="flex-1 relative">
                        <div id="regionChart" class="w-full h-36"></div>
                    </div>
                </div>

                <!-- Weather Impact Details -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Weather Impact</h3>
                        <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 transition-all duration-150">View All</a>
                    </div>

                    <div class="space-y-4 flex-1 flex flex-col justify-center">
                        <!-- Origin Weather -->
                        <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Origin</span>
                                <h4 class="text-xs font-bold text-slate-800 mt-0.5">{{ $activeShipment['origin'] }}</h4>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="text-right">
                                    <h4 class="text-xs font-extrabold text-slate-800">18°C</h4>
                                    <span class="text-[9px] font-semibold text-slate-450 block mt-0.5">Cloudy</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase bg-emerald-50 text-emerald-600">Low</span>
                            </div>
                        </div>

                        <!-- Destination Weather -->
                        <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Destination</span>
                                <h4 class="text-xs font-bold text-slate-800 mt-0.5">{{ $activeShipment['destination'] }}</h4>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="text-right">
                                    <h4 class="text-xs font-extrabold text-slate-800">31°C</h4>
                                    <span class="text-[9px] font-semibold text-slate-450 block mt-0.5">Rainy</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase bg-amber-50 text-amber-600">Medium</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- SCRIPTS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. SHIPMENT STATUS DONUT
            try {
                const statusOpt = {
                    series: [482, 698, 68],
                    chart: {
                        type: 'donut',
                        height: '100%',
                        width: '100%',
                        sparkline: { enabled: true }
                    },
                    labels: ['In Transit', 'Delivered', 'Delayed'],
                    colors: ['#2563EB', '#10B981', '#F59E0B'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '75%',
                                labels: { show: false }
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    legend: { show: false }
                };
                new ApexCharts(document.querySelector("#shipmentStatusChart"), statusOpt).render();
            } catch (err) {
                console.error("Status Chart Error: ", err);
            }

            // 2. SHIPMENT TREND LINE CHART
            try {
                const trendOpt = {
                    series: [{
                        name: 'Shipments',
                        data: [420, 360, 480, 450, 490, 520, 482]
                    }],
                    chart: {
                        type: 'line',
                        height: '100%',
                        toolbar: { show: false },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2.5
                    },
                    colors: ['#2563EB'],
                    xaxis: {
                        categories: ['14 Jul', '15 Jul', '16 Jul', '17 Jul', '18 Jul', '19 Jul', '20 Jul'],
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '8px',
                                fontWeight: 600
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '8px',
                                fontWeight: 600
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4
                    },
                    markers: {
                        size: 3,
                        colors: ['#2563EB'],
                        strokeWidth: 1.5,
                        hover: { size: 5 }
                    }
                };
                new ApexCharts(document.querySelector("#shipmentTrendChart"), trendOpt).render();
            } catch (err) {
                console.error("Trend Chart Error: ", err);
            }

            // 3. SHIPMENT BY REGION BAR CHART
            try {
                const regionOpt = {
                    series: [{
                        name: 'Shipments',
                        data: [652, 312, 148, 84, 52]
                    }],
                    chart: {
                        type: 'bar',
                        height: '100%',
                        toolbar: { show: false },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 3,
                            columnWidth: '40%',
                            distributed: true
                        }
                    },
                    colors: ['#2563EB', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444'],
                    xaxis: {
                        categories: ['Asia', 'Europe', 'N.America', 'Africa', 'S.America'],
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '8px',
                                fontWeight: 600
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '8px',
                                fontWeight: 600
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4
                    },
                    legend: { show: false }
                };
                new ApexCharts(document.querySelector("#regionChart"), regionOpt).render();
            } catch (err) {
                console.error("Region Chart Error: ", err);
            }

            // 4. LEAFLET SHIPMENT MAP
            try {
                const map = L.map('shipmentMap', {
                    zoomControl: false,
                    attributionControl: false
                }).setView([20, 40], 1.5);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 18
                }).addTo(map);

                const activeShp = @json($activeShipment);

                if (activeShp.origin_lat && activeShp.destination_lat) {
                    // Pins for Origin, Transit, Destination
                    const originMarker = L.circleMarker([activeShp.origin_lat, activeShp.origin_lng], {
                        radius: 6,
                        fillColor: '#10B981', // green
                        color: '#ffffff',
                        weight: 1.5,
                        opacity: 1,
                        fillOpacity: 0.9
                    }).addTo(map).bindPopup(`<b>Origin: ${activeShp.origin}</b>`);

                    const destinationMarker = L.circleMarker([activeShp.destination_lat, activeShp.destination_lng], {
                        radius: 6,
                        fillColor: '#EF4444', // red
                        color: '#ffffff',
                        weight: 1.5,
                        opacity: 1,
                        fillOpacity: 0.9
                    }).addTo(map).bindPopup(`<b>Destination: ${activeShp.destination}</b>`);

                    let pathCoords = [
                        [activeShp.origin_lat, activeShp.origin_lng]
                    ];

                    if (activeShp.transit_lat) {
                        const transitMarker = L.circleMarker([activeShp.transit_lat, activeShp.transit_lng], {
                            radius: 6,
                            fillColor: '#F59E0B', // orange
                            color: '#ffffff',
                            weight: 1.5,
                            opacity: 1,
                            fillOpacity: 0.9
                        }).addTo(map).bindPopup(`<b>Transit: ${activeShp.transit}</b>`);

                        pathCoords.push([activeShp.transit_lat, activeShp.transit_lng]);
                    }

                    pathCoords.push([activeShp.destination_lat, activeShp.destination_lng]);

                    // Draw route polyline
                    L.polyline(pathCoords, {
                        color: '#10B981',
                        weight: 3,
                        dashArray: '6, 8',
                        opacity: 0.8
                    }).addTo(map);

                    // Fit bounds to show route
                    map.fitBounds(pathCoords, { padding: [40, 40] });
                }
            } catch (err) {
                console.error("Map Error: ", err);
            }
        });
    </script>
</body>
</html>
