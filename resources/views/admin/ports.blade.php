<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Port Dataset Management - Admin Dashboard</title>

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
                            sidebar: '#0B132C',
                            blue: '#1A56DB',
                            bg: '#F8FAFC'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Chart and Map Scripts -->
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

    <!-- LEFT SIDEBAR -->
    <aside class="w-64 bg-brand-sidebar text-slate-300 flex flex-col fixed top-0 left-0 h-screen z-30 shadow-xl border-r border-slate-800">
        <!-- Logo Header -->
        <div class="p-6 flex items-center gap-3 border-b border-slate-800">
            <div class="bg-brand-blue/15 text-blue-400 p-2 rounded-xl border border-blue-500/20">
                <i class="bi bi-globe2 text-xl"></i>
            </div>
            <div>
                <h1 class="text-white font-bold leading-tight tracking-wider text-sm">GSC RISK</h1>
                <p class="text-slate-400 text-xs font-semibold tracking-widest uppercase">Intelligence</p>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
            <!-- MAIN MENU -->
            <div>
                <span class="px-3 text-[10px] font-bold text-slate-500 tracking-wider uppercase block mb-3">Main Menu</span>
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 text-sm font-medium transition-all duration-200">
                        <i class="bi bi-speedometer2 text-base"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="{{ route('admin.ports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-emerald-700 text-white font-medium text-sm transition-all duration-200 shadow-md shadow-emerald-700/20">
                        <i class="bi bi-anchor text-base"></i>
                        <span>Port Dataset</span>
                    </a>

                    <a href="{{ route('admin.articles') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 text-sm font-medium transition-all duration-200">
                        <i class="bi bi-blockquote-left text-base"></i>
                        <span>Article Analyst</span>
                    </a>

                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 text-sm font-medium transition-all duration-200">
                        <i class="bi bi-people text-base"></i>
                        <span>Users & Roles</span>
                    </a>
                    
                </div>
            </div>
    </aside>

    <!-- MAIN BODY SECTION -->
    <div class="flex-1 pl-64 flex flex-col min-w-0">

        <!-- TOP BAR HEADER -->
        <header class="bg-white border-b border-slate-100 sticky top-0 z-20 px-8 py-4 flex items-center justify-between shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">Port Dataset Management</h2>
                <p class="text-slate-500 text-xs font-medium">Manage and monitor global port dataset used for supply chain intelligence.</p>
            </div>

            <!-- Breadcrumbs, Notify and Profile actions -->
            <div class="flex items-center gap-6">
                <!-- Breadcrumbs -->
                <div class="hidden md:flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-600">Dashboard</a>
                    <span>/</span>
                    <span>Dataset</span>
                    <span>/</span>
                    <span class="text-brand-blue">Port Dataset</span>
                </div>

                <!-- Notifications -->
                <button class="relative p-2 bg-slate-50 hover:bg-slate-100 rounded-xl text-slate-600 transition-all duration-200 border border-slate-150">
                    <i class="bi bi-bell text-lg"></i>
                    <span class="absolute top-0.5 right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">3</span>
                </button>

                <!-- Profile avatar -->
                <div class="flex items-center gap-3 border-l border-slate-200 pl-6">
                    <div class="h-10 w-10 rounded-full bg-slate-800 text-white font-bold flex items-center justify-center border-2 border-brand-blue/20">
                        AD
                    </div>
                    <div class="text-left">
                        <h4 class="text-sm font-bold text-slate-800 leading-4">{{ Auth::user()->name ?? 'Admin User' }}</h4>
                        <span class="text-[11px] font-medium text-slate-400 uppercase tracking-wider block mt-0.5">{{ Auth::user()->role ?? 'Super Administrator' }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <main class="flex-1 p-8 space-y-8 overflow-y-auto">

            <!-- METRICS ROW (4 Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Ports -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-xl font-bold border border-blue-100">
                        <i class="bi bi-anchor"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Total Ports</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalPorts) }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-base leading-none"></i> 18 from last month
                        </span>
                    </div>
                </div>

                <!-- Active Ports -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100">
                        <i class="bi bi-ship"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Active Ports</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($activePorts) }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            100% of total ports
                        </span>
                    </div>
                </div>

                <!-- Countries Covered -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold border border-purple-100">
                        <i class="bi bi-globe"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Countries Covered</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $countriesCovered }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-base leading-none"></i> 4 new countries
                        </span>
                    </div>
                </div>

                <!-- Last Sync Status -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-orange-50 text-orange-655 flex items-center justify-center text-xl font-bold border border-orange-100">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Last Data Sync</span>
                        <h3 class="text-base font-extrabold text-slate-900 mt-1">18 Jul 2026 22:15</h3>
                        <span class="text-[10px] font-semibold text-slate-400 block mt-0.5">
                            API World Ports Index
                        </span>
                    </div>
                </div>
            </div>

            <!-- MIDDLE SECTION (Filters, Table & Map Grid) -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Port Table (col-span-3) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-3">
                    
                    <!-- Filters and Actions row -->
                    <form method="GET" action="{{ route('admin.ports') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Search -->
                            <div class="relative w-64">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-search text-xs"></i>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search port name, code or country..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200 text-slate-700 placeholder-slate-450">
                            </div>

                            <!-- Country Filter -->
                            <div class="relative">
                                <select name="country" onchange="this.form.submit()" class="appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 pr-8 text-xs font-semibold text-slate-655 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                    <option value="All Countries">All Countries</option>
                                    @foreach($countriesList as $cName)
                                        <option value="{{ $cName }}" {{ request('country') === $cName ? 'selected' : '' }}>{{ $cName }}</option>
                                    @endforeach
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-chevron-down text-[8px]"></i>
                                </span>
                            </div>

                            <!-- Status Filter -->
                            <div class="relative">
                                <select class="appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 pr-8 text-xs font-semibold text-slate-655 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                    <option>All Status</option>
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-chevron-down text-[8px]"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('ports.sync') }}" class="bg-slate-50 hover:bg-slate-100 text-slate-700 px-3.5 py-2 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-200">
                                <i class="bi bi-file-earmark-arrow-up text-sm"></i>
                                <span>Import CSV</span>
                            </a>
                            <button type="button" class="p-2 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl transition-all duration-200" title="Export CSV">
                                <i class="bi bi-download text-sm"></i>
                            </button>
                            <a href="{{ route('ports.sync') }}" class="bg-slate-50 hover:bg-slate-100 text-slate-700 px-3.5 py-2 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-200">
                                <i class="bi bi-arrow-repeat text-sm"></i>
                                <span>Sync API</span>
                            </a>
                            <button type="button" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all duration-200">
                                <i class="bi bi-plus text-base leading-none"></i>
                                <span>Add Port</span>
                            </button>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3 pl-4">Port ID</th>
                                    <th class="py-3">Port Name</th>
                                    <th class="py-3">Country</th>
                                    <th class="py-3">City</th>
                                    <th class="py-3">Latitude</th>
                                    <th class="py-3">Longitude</th>
                                    <th class="py-3">Port Type</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Last Updated</th>
                                    <th class="py-3 text-center pr-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs font-semibold text-slate-700">
                                @forelse($formattedPorts as $port)
                                <tr class="hover:bg-slate-50/50 transition-all duration-150">
                                    <td class="py-3 pl-4">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold font-mono border border-emerald-250 text-emerald-700 bg-emerald-50/50">
                                            {{ $port['id'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 font-bold text-slate-800">{{ $port['name'] }}</td>
                                    <td class="py-3 text-slate-600">
                                        <span class="flex items-center gap-1.5 font-bold">
                                            <span>{{ $port['flag'] }}</span>
                                            <span>{{ $port['country'] }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3 text-slate-500 font-medium">{{ $port['city'] }}</td>
                                    <td class="py-3 font-mono font-medium text-slate-500">{{ number_format($port['latitude'], 4) }}</td>
                                    <td class="py-3 font-mono font-medium text-slate-500">{{ number_format($port['longitude'], 4) }}</td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold inline-block border
                                            @if($port['type'] === 'Sea Port') bg-blue-50 text-blue-700 border-blue-100
                                            @else bg-amber-50 text-amber-700 border-amber-100 @endif">
                                            {{ $port['type'] }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold inline-block bg-emerald-50 text-emerald-700">
                                            {{ $port['status'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-slate-500 font-medium">{{ $port['last_updated'] }}</td>
                                    <td class="py-3 text-center pr-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button class="p-1 text-slate-400 hover:text-slate-700 transition-all duration-150"><i class="bi bi-eye"></i></button>
                                            <button class="p-1 text-slate-400 hover:text-blue-600 transition-all duration-150"><i class="bi bi-pencil"></i></button>
                                            <button class="p-1 text-slate-400 hover:text-red-600 transition-all duration-150"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="py-6 text-center text-slate-400 font-semibold">No ports found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <span class="text-xs font-semibold text-slate-500">
                            Showing {{ $portsList->firstItem() ?? 0 }} to {{ $portsList->lastItem() ?? 0 }} of {{ number_format($portsList->total()) }} ports
                        </span>
                        
                        <div class="flex items-center gap-1 text-xs">
                            {{ $portsList->links('pagination::tailwind') }}
                        </div>
                    </div>
                </div>

                <!-- Right Column (Map, Legend, Recent Updates) -->
                <div class="flex flex-col gap-6 lg:col-span-1">
                    <!-- Port Locations Map -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[380px]">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-slate-900">Port Locations</h3>
                            <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 transition-all duration-150">View full map</a>
                        </div>
                        <!-- Leaflet map -->
                        <div class="flex-1 rounded-xl overflow-hidden border border-slate-100 relative z-10">
                            <div id="portMap" class="w-full h-full min-h-[220px]"></div>
                        </div>
                        
                        <!-- Mini Legend stats -->
                        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                            <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sea Ports</span>
                                <h4 class="text-xs font-extrabold text-slate-800 mt-0.5">{{ number_format($seaPortsCount) }}</h4>
                                <span class="text-[9px] font-semibold text-slate-500 block mt-0.5">{{ round(($seaPortsCount/max($totalPorts, 1))*100, 1) }}%</span>
                            </div>
                            <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">River Ports</span>
                                <h4 class="text-xs font-extrabold text-slate-800 mt-0.5">{{ number_format($riverPortsCount) }}</h4>
                                <span class="text-[9px] font-semibold text-slate-500 block mt-0.5">{{ round(($riverPortsCount/max($totalPorts, 1))*100, 1) }}%</span>
                            </div>
                            <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dry Ports</span>
                                <h4 class="text-xs font-extrabold text-slate-800 mt-0.5">{{ number_format($dryPortsCount) }}</h4>
                                <span class="text-[9px] font-semibold text-slate-500 block mt-0.5">{{ round(($dryPortsCount/max($totalPorts, 1))*100, 1) }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Updates -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col flex-1">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-slate-900">Recent Updates</h3>
                            <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 transition-all duration-150">View all</a>
                        </div>

                        <div class="space-y-4">
                            <!-- Update 1 -->
                            <div class="flex gap-3">
                                <div class="h-8 w-8 rounded-lg bg-blue-50 text-brand-blue border border-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="bi bi-plus-circle text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-xs font-bold text-slate-800 truncate">New port added</h4>
                                        <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">18 Jul 21:30</span>
                                    </div>
                                    <p class="text-[11px] font-medium text-slate-500 mt-0.5">Port of Chancay <span class="text-slate-400 font-normal">(Peru | Sea Port)</span></p>
                                </div>
                            </div>

                            <!-- Update 2 -->
                            <div class="flex gap-3">
                                <div class="h-8 w-8 rounded-lg bg-blue-50 text-brand-blue border border-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="bi bi-arrow-repeat text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-xs font-bold text-slate-800 truncate">Port data updated</h4>
                                        <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">18 Jul 20:10</span>
                                    </div>
                                    <p class="text-[11px] font-medium text-slate-500 mt-0.5">Port of Hamburg <span class="text-slate-400 font-normal">(Germany | Sea Port)</span></p>
                                </div>
                            </div>

                            <!-- Update 3 -->
                            <div class="flex gap-3">
                                <div class="h-8 w-8 rounded-lg bg-blue-50 text-brand-blue border border-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="bi bi-plus-circle text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-xs font-bold text-slate-800 truncate">New port added</h4>
                                        <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">18 Jul 18:45</span>
                                    </div>
                                    <p class="text-[11px] font-medium text-slate-500 mt-0.5">Vadhavan Port <span class="text-slate-400 font-normal">(India | Sea Port)</span></p>
                                </div>
                            </div>

                            <!-- Update 4 -->
                            <div class="flex gap-3">
                                <div class="h-8 w-8 rounded-lg bg-red-50 text-red-500 border border-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="bi bi-exclamation-triangle text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-xs font-bold text-slate-800 truncate">Port status changed</h4>
                                        <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">18 Jul 17:25</span>
                                    </div>
                                    <p class="text-[11px] font-medium text-slate-500 mt-0.5">Port of Haifa <span class="text-red-500 font-bold">(Inactive)</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM GRID (Top Countries, Types Distribution Chart) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Ports by Country Top 10 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-2">
                    <h3 class="text-sm font-bold text-slate-900 mb-6">Ports by Country (Top 10)</h3>
                    <div class="flex-1 relative">
                        <div id="portsByCountryChart" class="w-full h-64"></div>
                    </div>
                </div>

                <!-- Port Types Distribution Chart -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-1">
                    <h3 class="text-sm font-bold text-slate-900 mb-6">Port Types Distribution</h3>
                    <div class="flex-1 flex flex-col justify-center">
                        <div class="flex items-center gap-8 justify-center">
                            <!-- Donut Chart -->
                            <div class="relative w-40 h-40 flex items-center justify-center">
                                <div id="portTypesChart" class="w-full h-full"></div>
                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-2xl font-extrabold text-slate-800">{{ number_format($totalPorts) }}</span>
                                    <span class="text-xs font-bold text-slate-400 uppercase leading-none">Total Ports</span>
                                </div>
                            </div>

                            <!-- Legend details -->
                            <div class="flex-1 space-y-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full bg-blue-500 inline-block"></span>
                                        <span class="text-xs font-bold text-slate-600">Sea Ports</span>
                                    </div>
                                    <p class="text-sm font-extrabold pl-5 text-slate-850">{{ number_format($seaPortsCount) }} <span class="text-slate-450 font-normal">({{ round(($seaPortsCount/max($totalPorts, 1))*100, 1) }}%)</span></p>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full bg-amber-500 inline-block"></span>
                                        <span class="text-xs font-bold text-slate-600">River Ports</span>
                                    </div>
                                    <p class="text-sm font-extrabold pl-5 text-slate-850">{{ number_format($riverPortsCount) }} <span class="text-slate-450 font-normal">({{ round(($riverPortsCount/max($totalPorts, 1))*100, 1) }}%)</span></p>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full bg-emerald-500 inline-block"></span>
                                        <span class="text-xs font-bold text-slate-600">Dry Ports</span>
                                    </div>
                                    <p class="text-sm font-extrabold pl-5 text-slate-850">{{ number_format($dryPortsCount) }} <span class="text-slate-450 font-normal">({{ round(($dryPortsCount/max($totalPorts, 1))*100, 1) }}%)</span></p>
                                </div>
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
            // 1. TOP 10 COUNTRIES BAR CHART
            try {
                const countryOpt = {
                    series: [{
                        name: "Ports Count",
                        data: {!! json_encode($topCountriesCounts) !!}
                    }],
                    chart: {
                        type: 'bar',
                        height: 250,
                        toolbar: { show: false },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '45%',
                            distributed: true
                        }
                    },
                    colors: ['#3B82F6', '#10B981', '#6366F1', '#EC4899', '#F59E0B', '#8B5CF6', '#14B8A6', '#F43F5E', '#06B6D4', '#64748B'],
                    xaxis: {
                        categories: {!! json_encode($topCountriesNames) !!},
                        labels: {
                            style: {
                                colors: '#64748b',
                                fontSize: '10px',
                                fontWeight: 700
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#64748b',
                                fontSize: '10px',
                                fontWeight: 700
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4
                    },
                    legend: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    }
                };
                new ApexCharts(document.querySelector("#portsByCountryChart"), countryOpt).render();
            } catch (err) {
                console.error("Top Countries Chart Error: ", err);
            }

            // 2. PORT TYPES DONUT CHART
            try {
                const typeOpt = {
                    series: [{{ $seaPortsCount }}, {{ $riverPortsCount }}, {{ $dryPortsCount }}],
                    chart: {
                        type: 'donut',
                        height: '100%',
                        width: '100%',
                        sparkline: { enabled: true }
                    },
                    labels: ['Sea Ports', 'River Ports', 'Dry Ports'],
                    colors: ['#3B82F6', '#F59E0B', '#10B981'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '75%',
                                labels: { show: false }
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    legend: { show: false },
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(val) {
                                return val + " Ports";
                            }
                        }
                    }
                };
                new ApexCharts(document.querySelector("#portTypesChart"), typeOpt).render();
            } catch (err) {
                console.error("Types Donut Chart Error: ", err);
            }

            // 3. LEAFLET PORTS MAP
            try {
                const map = L.map('portMap', {
                    zoomControl: false,
                    attributionControl: false
                }).setView([20, 0], 1);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 18
                }).addTo(map);

                const mapPoints = @json($mapPoints);

                mapPoints.forEach(function(point) {
                    if (point.lat && point.lng) {
                        let color = '#3B82F6'; // default blue (Sea Port)
                        if (point.type === 'River Port') {
                            color = '#F59E0B'; // orange
                        }

                        L.circleMarker([point.lat, point.lng], {
                            radius: 5,
                            fillColor: color,
                            color: '#ffffff',
                            weight: 1,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).addTo(map).bindPopup(`
                            <div class="p-1 leading-snug">
                                <h4 class="font-bold text-xs text-slate-800">${point.name}</h4>
                                <p class="text-[10px] text-slate-500 mt-0.5">${point.country} | ${point.type}</p>
                            </div>
                        `);
                    }
                });
            } catch (err) {
                console.error("Leaflet Map Error: ", err);
            }
        });
    </script>
</body>
</html>
