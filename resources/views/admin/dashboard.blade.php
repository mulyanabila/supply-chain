<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GSC Risk Intelligence - Admin Dashboard</title>

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Tailwind CSS (via Play CDN for instant, bulletproof rendering) -->
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
        /* Custom styles for Leaflet and Scrollbar */
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
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-brand-blue text-white font-medium text-sm transition-all duration-200 shadow-md shadow-brand-blue/20">
                        <i class="bi bi-speedometer2 text-base"></i>
                        <span>Dashboard</span>
                    </a>
        
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 text-sm font-medium transition-all duration-200">
                        <i class="bi bi-people text-base"></i>
                        <span>Users</span>
                    </a>

                    <a href="{{ route('admin.ports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 text-sm font-medium transition-all duration-200">
                        <i class="bi bi-anchor text-base"></i>
                        <span>Port Dataset</span>
                    </a>

                    <a href="{{ route('admin.articles') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 text-sm font-medium transition-all duration-200">
                        <i class="bi bi-blockquote-left text-base"></i>
                        <span>Article Analyst</span>
                    </a>
                </div>
            </div>

        <!-- Sidebar Footer / System Status -->
    </aside>

    <!-- MAIN BODY SECTION -->
    <div class="flex-1 pl-64 flex flex-col min-w-0">

        <!-- TOP BAR HEADER -->
        <header class="bg-white border-b border-slate-100 sticky top-0 z-20 px-8 py-4 flex items-center justify-between shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">Welcome back, Admin! </h2>
                <p class="text-slate-500 text-xs font-medium">Global Supply Chain Risk Intelligence Platform</p>
            </div>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <main class="flex-1 p-8 space-y-8 overflow-y-auto">

            <!-- Subheader Bar (Date and Sync information) -->
            <div class="flex items-center justify-end">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 bg-white border border-slate-100 rounded-lg px-3 py-1.5 shadow-sm">
                    <i class="bi bi-calendar3"></i>
                    <span>18 Jul 2026, 22:15 (UTC+7)</span>
                    <i class="bi bi-arrow-repeat text-slate-400 cursor-pointer ml-1 hover:text-slate-900 transition-all duration-150"></i>
                </div>
            </div>

            <!-- METRICS ROW (5 Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <!-- Total Countries -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-xl font-bold border border-blue-100">
                        <i class="bi bi-globe"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Total Countries</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalCountries }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-base leading-none"></i> 2 new this month
                        </span>
                    </div>
                </div>

                <!-- Ports Tracked -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100">
                        <i class="bi bi-anchor"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Ports Tracked</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($portsTracked) }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-base leading-none"></i> 23 new this month
                        </span>
                    </div>
                </div>

                <!-- High Risk Countries -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold border border-purple-100">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">High Risk Countries</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $highRiskCount }}</h3>
                        <span class="text-[11px] font-bold text-red-500 flex items-center gap-1 mt-1">
                            15.2% of total
                        </span>
                    </div>
                </div>

                <!-- Analyzed Articles -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl font-bold border border-orange-100">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Analyzed Articles</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($newsCount) }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-base leading-none"></i> 118 this week
                        </span>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold border border-indigo-100">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Total Users</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalUsers }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-base leading-none"></i> 12 this month
                        </span>
                    </div>
                </div>
            </div>

            <!-- VISUALIZATION ROW (Global Risk Overview, Risk Trend, System Info) -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Global Risk Overview (Donut Chart) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-1">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-bold text-slate-900">Global Risk Overview</h3>
                        <i class="bi bi-info-circle text-slate-400 text-xs" title="Overview of global risks"></i>
                    </div>

                    <div class="flex-1 flex flex-col justify-between">
                        <!-- Chart and Details container -->
                        <div class="flex items-center gap-4 justify-center">
                            <!-- Apex Donut Chart Container -->
                            <div class="relative w-36 h-36 flex items-center justify-center">
                                <div id="donutChart" class="w-full h-full"></div>
                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-[10px] font-bold uppercase text-slate-400">Risk Dist.</span>
                                    <span class="text-lg font-extrabold text-slate-800">{{ $totalCountries }}</span>
                                    <span class="text-[9px] font-semibold text-slate-400 leading-none">Countries</span>
                                </div>
                            </div>

                            <!-- Legend details -->
                            <div class="flex-1 space-y-3">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="h-2.5 w-2.5 rounded-full bg-red-500 inline-block"></span>
                                        <span class="text-[11px] font-bold text-slate-600">High Risk</span>
                                    </div>
                                    <p class="text-xs font-bold pl-4 text-slate-850">{{ $highRiskCount }} <span class="text-slate-400 font-normal">({{ round(($highRiskCount/$totalCountries)*100, 1) }}%)</span></p>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="h-2.5 w-2.5 rounded-full bg-amber-500 inline-block"></span>
                                        <span class="text-[11px] font-bold text-slate-600">Medium Risk</span>
                                    </div>
                                    <p class="text-xs font-bold pl-4 text-slate-850">{{ $mediumRiskCount }} <span class="text-slate-400 font-normal">({{ round(($mediumRiskCount/$totalCountries)*100, 1) }}%)</span></p>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>
                                        <span class="text-[11px] font-bold text-slate-600">Low Risk</span>
                                    </div>
                                    <p class="text-xs font-bold pl-4 text-slate-850">{{ $lowRiskCount }} <span class="text-slate-400 font-normal">({{ round(($lowRiskCount/$totalCountries)*100, 1) }}%)</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Description Alert Box -->
                        <div class="mt-6 p-4 rounded-xl bg-blue-50/50 border border-blue-100/50">
                            <p class="text-[11px] text-blue-800 leading-relaxed font-medium">
                                Risk assessment is based on economic, weather, geopolitical, and infrastructure indicators. Updated daily at 22:15 UTC+7
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Risk Trend Line Chart -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Risk Trend <span class="text-slate-400 font-medium">(Last 7 Days)</span></h3>
                        </div>

                        <!-- Dropdown select -->
                        <div class="relative inline-block text-left">
                            <select class="appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-1.5 pr-8 text-xs font-semibold text-slate-600 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                <option>All Countries</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="bi bi-chevron-down text-[9px]"></i>
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 relative">
                        <!-- Chart container -->
                        <div id="trendLineChart" class="w-full h-64"></div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-1">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-bold text-slate-900">System Information</h3>
                        <i class="bi bi-cpu text-slate-400 text-sm"></i>
                    </div>

                    <div class="flex-1 space-y-4">
                        <!-- Last Data Sync -->
                        <div class="flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl transition-all duration-200">
                            <div class="h-9 w-9 rounded-lg bg-blue-50 text-brand-blue flex items-center justify-center border border-blue-100/50 flex-shrink-0 mt-0.5">
                                <i class="bi bi-arrow-repeat text-base"></i>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Last Data Sync</span>
                                <h4 class="text-xs font-bold text-slate-900 mt-0.5">18 Jul 2026 22:15</h4>
                                <p class="text-[10px] font-medium text-slate-500 mt-0.5">World Bank, Weather API, News API</p>
                            </div>
                        </div>

                        <!-- Next Data Sync -->
                        <div class="flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl transition-all duration-200">
                            <div class="h-9 w-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100/50 flex-shrink-0 mt-0.5">
                                <i class="bi bi-clock text-base"></i>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Next Data Sync</span>
                                <h4 class="text-xs font-bold text-slate-900 mt-0.5">19 Jul 2026 02:00</h4>
                                <p class="text-[10px] font-bold text-purple-600 mt-0.5 flex items-center gap-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-purple-500 inline-block"></span> In 3h 45m
                                </p>
                            </div>
                        </div>

                        <!-- Active Alerts -->
                        <div class="flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl transition-all duration-200">
                            <div class="h-9 w-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100/50 flex-shrink-0 mt-0.5">
                                <i class="bi bi-exclamation-triangle text-base"></i>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Active Alerts</span>
                                <h4 class="text-xs font-bold text-slate-900 mt-0.5">8 Alerts</h4>
                                <p class="text-[10px] font-medium text-slate-500 mt-0.5">Require attention</p>
                            </div>
                        </div>

                        <!-- System Status -->
                        <div class="flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl transition-all duration-200">
                            <div class="h-9 w-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100/50 flex-shrink-0 mt-0.5">
                                <i class="bi bi-check-circle text-base"></i>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">System Status</span>
                                <h4 class="text-xs font-bold text-emerald-600 mt-0.5">All Systems Operational</h4>
                                <p class="text-[10px] font-medium text-slate-500 mt-0.5">100% uptime</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAP & TABLES ROW (Risk Level by Region, Top 5 High Risk Countries, Recent Alerts) -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Risk Level Map -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Risk Level by Region</h3>

                        <!-- Dropdown select -->
                        <div class="relative inline-block text-left">
                            <select class="appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-1.5 pr-8 text-xs font-semibold text-slate-600 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                <option>All Regions</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="bi bi-chevron-down text-[9px]"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div class="flex-1 min-h-[300px] relative rounded-xl border border-slate-100 overflow-hidden z-10">
                        <div id="regionMap" class="w-full h-full min-h-[300px]"></div>

                        <!-- Custom Map Legend in Container Bottom -->
                        <div class="absolute bottom-4 left-4 z-[400] bg-white border border-slate-150 rounded-xl p-3 shadow-md">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Legend</h4>
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 bg-red-500 rounded border border-white"></span>
                                    <span class="text-[11px] font-semibold text-slate-600">High</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 bg-amber-500 rounded border border-white"></span>
                                    <span class="text-[11px] font-semibold text-slate-600">Medium</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 bg-emerald-500 rounded border border-white"></span>
                                    <span class="text-[11px] font-semibold text-slate-600">Low</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 bg-slate-200 rounded border border-white"></span>
                                    <span class="text-[11px] font-semibold text-slate-600">No Data</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top 5 High Risk Countries -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-1">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Top 5 High Risk Countries</h3>
                    </div>

                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-2.5">Country</th>
                                    <th class="py-2.5">Region</th>
                                    <th class="py-2.5">Risk Score</th>
                                    <th class="py-2.5 text-right">Trend</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs font-semibold text-slate-700">
                                @foreach($topHighRisk as $country)
                                <tr>
                                    <td class="py-3 flex items-center gap-2">
                                        <span class="text-lg">
                                            @if($country['code'] === 'ye') 🇾🇪
                                            @elseif($country['code'] === 'sy') 🇸🇾
                                            @elseif($country['code'] === 'ht') 🇭🇹
                                            @elseif($country['code'] === 've') 🇻🇪
                                            @elseif($country['code'] === 'af') 🇦🇫
                                            @else 🏳️ @endif
                                        </span>
                                        <span class="text-slate-800 font-bold">{{ $country['name'] }}</span>
                                    </td>
                                    <td class="py-3 text-slate-500 font-medium">{{ $country['region'] }}</td>
                                    <td class="py-3 font-bold text-slate-800">{{ $country['score'] }} <span class="text-[10px] font-medium text-slate-400">/ 100</span></td>
                                    <td class="py-3 text-right text-red-500 font-bold">
                                        <span class="flex items-center justify-end gap-0.5">
                                            <i class="bi bi-arrow-up-short text-sm leading-none"></i>
                                            <span>{{ $country['trend'] }}</span>
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 flex items-center gap-1 transition-all duration-150">
                            <span>View all risk analysis</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Recent Alerts -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-1">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Recent Alerts</h3>
                        <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 transition-all duration-150">
                            View all alerts →
                        </a>
                    </div>

                    <div class="flex-1 space-y-4">
                        <!-- Alert 1 -->
                        <div class="flex gap-3">
                            <div class="h-8 w-8 rounded-lg bg-red-50 text-red-500 border border-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-exclamation-triangle-fill text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">High risk increase detected</h4>
                                    <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">10 min ago</span>
                                </div>
                                <p class="text-[11px] font-medium text-slate-500 mt-0.5">Yemen risk score increased to 85</p>
                            </div>
                        </div>

                        <!-- Alert 2 -->
                        <div class="flex gap-3">
                            <div class="h-8 w-8 rounded-lg bg-amber-50 text-amber-500 border border-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-exclamation-triangle-fill text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">Severe weather warning</h4>
                                    <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">1 hour ago</span>
                                </div>
                                <p class="text-[11px] font-medium text-slate-500 mt-0.5">Typhoon expected in Philippines region</p>
                            </div>
                        </div>

                        <!-- Alert 3 -->
                        <div class="flex gap-3">
                            <div class="h-8 w-8 rounded-lg bg-amber-50 text-amber-500 border border-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-exclamation-triangle-fill text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">Port congestion detected</h4>
                                    <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">2 hours ago</span>
                                </div>
                                <p class="text-[11px] font-medium text-slate-500 mt-0.5">Shanghai Port congestion above 80%</p>
                            </div>
                        </div>

                        <!-- Alert 4 -->
                        <div class="flex gap-3">
                            <div class="h-8 w-8 rounded-lg bg-blue-50 text-brand-blue border border-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-info-circle-fill text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">Trade policy update</h4>
                                    <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">5 hours ago</span>
                                </div>
                                <p class="text-[11px] font-medium text-slate-500 mt-0.5">New export restrictions from United States</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QUICK ACCESS & SYNC STATUS ROW -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Quick Access -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <h3 class="text-sm font-bold text-slate-900 mb-5">Quick Access</h3>
                    <div class="grid grid-cols-5 gap-4">
                        <!-- Add Country -->
                        <a href="{{ route('countries') }}" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100 transition-all duration-200 text-center hover:scale-102 group">
                            <div class="h-10 w-10 bg-blue-50 text-brand-blue rounded-xl flex items-center justify-center text-lg mb-2 shadow-sm border border-blue-100 group-hover:scale-110 transition-transform duration-200">
                                <i class="bi bi-plus-circle-fill"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-800">Add Country</span>
                        </a>

                        <!-- Sync Data -->
                        <a href="{{ route('countries.sync') }}" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100 transition-all duration-200 text-center hover:scale-102 group">
                            <div class="h-10 w-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg mb-2 shadow-sm border border-emerald-100 group-hover:scale-110 transition-transform duration-200">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-800">Sync Data</span>
                        </a>

                        <!-- Manage Users -->
                        <a href="#" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100 transition-all duration-200 text-center hover:scale-102 group">
                            <div class="h-10 w-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-lg mb-2 shadow-sm border border-purple-100 group-hover:scale-110 transition-transform duration-200">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-800">Manage Users</span>
                        </a>

                        <!-- View Logs -->
                        <a href="#" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100 transition-all duration-200 text-center hover:scale-102 group">
                            <div class="h-10 w-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-lg mb-2 shadow-sm border border-amber-100 group-hover:scale-110 transition-transform duration-200">
                                <i class="bi bi-file-text-fill"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-800">View Logs</span>
                        </a>

                        <!-- System Settings -->
                        <a href="#" class="flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100 transition-all duration-200 text-center hover:scale-102 group">
                            <div class="h-10 w-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg mb-2 shadow-sm border border-indigo-100 group-hover:scale-110 transition-transform duration-200">
                                <i class="bi bi-gear-fill"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-800">System Settings</span>
                        </a>
                    </div>
                </div>

                <!-- Data Sync Status -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-sm font-bold text-slate-900">Data Sync Status</h3>
                        <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 transition-all duration-150">
                            View all sync logs →
                        </a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 flex-1">
                        <!-- World Bank API -->
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-800">World Bank API</span>
                                <span class="h-5 w-5 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xs">
                                    <i class="bi bi-check"></i>
                                </span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Success</span>
                        </div>

                        <!-- Weather API -->
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-800">Weather API</span>
                                <span class="h-5 w-5 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xs">
                                    <i class="bi bi-check"></i>
                                </span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Success</span>
                        </div>

                        <!-- News API -->
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-800">News API</span>
                                <span class="h-5 w-5 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xs">
                                    <i class="bi bi-check"></i>
                                </span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Success</span>
                        </div>

                        <!-- Exchange Rate API -->
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-slate-800">Exchange Rate API</span>
                                <span class="h-5 w-5 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xs">
                                    <i class="bi bi-check"></i>
                                </span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Success</span>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- SCRIPT FOR CHARTS & MAPS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. GLOBAL RISK OVERVIEW DONUT CHART
            try {
                const donutOptions = {
                    series: [{{ $highRiskCount }}, {{ $mediumRiskCount }}, {{ $lowRiskCount }}],
                    chart: {
                        type: 'donut',
                        height: '100%',
                        width: '105%',
                        sparkline: {
                            enabled: true
                        }
                    },
                    labels: ['High Risk', 'Medium Risk', 'Low Risk'],
                    colors: ['#EF4444', '#F59E0B', '#10B981'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '75%',
                                labels: {
                                    show: false
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false
                    },
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(val) {
                                return val + " Countries";
                            }
                        }
                    }
                };

                const donutChart = new ApexCharts(document.querySelector("#donutChart"), donutOptions);
                donutChart.render();
            } catch (err) {
                console.error("Donut Chart Init Error: ", err);
            }

            // 2. RISK TREND (LAST 7 DAYS) LINE CHART
            try {
                const lineOptions = {
                    series: [
                        {
                            name: "High Risk",
                            data: [35, 30, 38, 30, 32, 36, 32]
                        },
                        {
                            name: "Medium Risk",
                            data: [65, 70, 68, 70, 75, 82, 80]
                        },
                        {
                            name: "Low Risk",
                            data: [15, 12, 5, 12, 10, 10, 10]
                        }
                    ],
                    chart: {
                        type: 'line',
                        height: 240,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    colors: ['#EF4444', '#F59E0B', '#10B981'],
                    xaxis: {
                        categories: ['12 Jul', '13 Jul', '14 Jul', '15 Jul', '16 Jul', '17 Jul', '18 Jul'],
                        labels: {
                            style: {
                                colors: '#64748b',
                                fontSize: '10px',
                                fontWeight: 600
                            }
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        max: 100,
                        min: 0,
                        tickAmount: 5,
                        labels: {
                            style: {
                                colors: '#64748b',
                                fontSize: '10px',
                                fontWeight: 600
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        }
                    },
                    markers: {
                        size: 4,
                        strokeWidth: 2,
                        hover: {
                            size: 6
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left',
                        fontSize: '11px',
                        fontWeight: 700,
                        labels: {
                            colors: '#475569'
                        },
                        markers: {
                            radius: 12
                        }
                    }
                };

                const lineChart = new ApexCharts(document.querySelector("#trendLineChart"), lineOptions);
                lineChart.render();
            } catch (err) {
                console.error("Line Chart Init Error: ", err);
            }

            // 3. LEAFLET WORLD MAP
            try {
                // Initialize map centered on global view
                const map = L.map('regionMap', {
                    zoomControl: false,
                    attributionControl: false
                }).setView([20, 0], 2);

                // Add clean background map tile layer (CartoDB Light without labels for high-fidelity)
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
                    maxZoom: 18
                }).addTo(map);

                // Add nice zoom control to bottom right
                L.control.zoom({
                    position: 'bottomright'
                }).addTo(map);

                // Database Map Data
                const mapPoints = @json($mapData);

                // Bind circle markers for active monitored countries with risk scores
                mapPoints.forEach(function(point) {
                    if (point.lat && point.lng) {
                        let color = '#10B981'; // green (Low Risk)
                        if (point.risk_level === 'High Risk') {
                            color = '#EF4444'; // red
                        } else if (point.risk_level === 'Medium Risk') {
                            color = '#F59E0B'; // orange
                        }

                        // Create circle marker
                        const marker = L.circleMarker([point.lat, point.lng], {
                            radius: 7,
                            fillColor: color,
                            color: '#ffffff',
                            weight: 1.5,
                            opacity: 1,
                            fillOpacity: 0.95
                        }).addTo(map);

                        // Popup on hover / click
                        marker.bindPopup(`
                            <div class="p-1 leading-snug">
                                <h4 class="font-bold text-xs text-slate-800">${point.name}</h4>
                                <div class="flex justify-between items-center mt-1.5 gap-4">
                                    <span class="text-[10px] text-slate-500 font-semibold uppercase">${point.risk_level}</span>
                                    <span class="text-[11px] font-extrabold text-slate-800">${point.risk_score} / 100</span>
                                </div>
                            </div>
                        `);
                    }
                });

                // Fetch World GeoJSON to color countries on map
                fetch('https://cdn.jsdelivr.net/gh/johan/world.geo.json@master/countries.geo.json')
                    .then(response => response.json())
                    .then(geoData => {
                        const riskDict = {};
                        mapPoints.forEach(p => {
                            riskDict[p.name.toLowerCase()] = p;
                        });

                        L.geoJson(geoData, {
                            style: function(feature) {
                                const name = feature.properties.name.toLowerCase();
                                let fillColor = '#e2e8f0'; // No Data / Grey
                                let fillOpacity = 0.4;
                                let weight = 0.5;

                                if (riskDict[name]) {
                                    const matched = riskDict[name];
                                    fillOpacity = 0.65;
                                    weight = 1;
                                    if (matched.risk_level === 'High Risk') {
                                        fillColor = '#EF4444';
                                    } else if (matched.risk_level === 'Medium Risk') {
                                        fillColor = '#F59E0B';
                                    } else {
                                        fillColor = '#10B981';
                                    }
                                }

                                return {
                                    fillColor: fillColor,
                                    weight: weight,
                                    opacity: 0.8,
                                    color: '#ffffff',
                                    fillOpacity: fillOpacity
                                };
                            }
                        }).addTo(map);
                    })
                    .catch(err => console.error("GeoJSON Fetch Error: ", err));

            } catch (err) {
                console.error("Map Init Error: ", err);
            }
        });
    </script>
</body>
</html>
