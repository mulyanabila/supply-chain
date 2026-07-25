<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shipment Management - GSC Risk Intelligence</title>

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

    <!-- Leaflet JS -->
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

        /* Sidebar styles matching Dashboard */
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

    @php
        // Fetch real countries with their ports to pass to Javascript
        $dbCountries = \App\Models\Country::with('ports')->orderBy('country_name')->get();
    @endphp

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">
            🌍 GSC RISK<br>INTELLIGENCE
        </div>
        <ul>
            <li><a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="{{ route('countries') }}"><i class="bi bi-globe2 me-2"></i> Countries</a></li>
            <li><a href="{{ route('ports') }}"><i class="bi bi-geo-alt me-2"></i> Ports</a></li>
            <li class="active"><a href="{{ route('shipment') }}"><i class="bi bi-truck me-2"></i> Shipment</a></li>
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
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">Shipment Management</h2>
                <p class="text-slate-500 text-xs font-medium">Create new shipment and monitor your cargo in real-time.</p>
            </div>

            <!-- Header actions -->
            <div class="flex items-center gap-6">
            </div>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <main class="flex-1 p-8 space-y-8 overflow-y-auto">

            <!-- CREATE NEW SHIPMENT FORM -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center gap-2 mb-6">
                    <div class="h-6 w-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">1</div>
                    <h3 class="text-sm font-bold text-slate-900">Create New Shipment</h3>
                </div>
                
                <form id="createShipmentForm" onsubmit="event.preventDefault();" class="grid grid-cols-12 gap-6">
                    <!-- Fields (10 columns) -->
                    <div class="col-span-10 grid grid-cols-5 gap-4">
                        <!-- Row 1 -->
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Origin Country</label>
                            <div class="relative">
                                <select id="originCountry" required class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                    <option value="">Select Country</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-chevron-down text-[8px]"></i>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Destination Country</label>
                            <div class="relative">
                                <select id="destinationCountry" required class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                    <option value="">Select Country</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-chevron-down text-[8px]"></i>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Origin Port</label>
                            <div class="relative">
                                <select id="originPort" required class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                    <option value="">Select Port</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-chevron-down text-[8px]"></i>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Destination Port</label>
                            <div class="relative">
                                <select id="destinationPort" required class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                    <option value="">Select Port</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-chevron-down text-[8px]"></i>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Cargo Type</label>
                            <div class="relative">
                                <select id="cargoType" required class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                    <option value="Clothing">Clothing</option>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Steel">Steel</option>
                                    <option value="Textile">Textile</option>
                                    <option value="Machinery">Machinery</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-chevron-down text-[8px]"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Item Name</label>
                            <input type="text" id="itemName" required placeholder="Backpack" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Quantity</label>
                            <div class="relative">
                                <input type="number" id="quantity" required placeholder="200" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-4 pr-12 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-[10px] font-bold text-slate-400">Pcs</span>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Weight</label>
                            <div class="relative">
                                <input type="number" step="0.1" id="weight" required placeholder="2.5" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-4 pr-12 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-[10px] font-bold text-slate-400">Ton</span>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Shipping Method</label>
                            <div class="relative">
                                <select id="shippingMethod" required class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                    <option value="Sea Freight">Sea Freight</option>
                                    <option value="Air Freight">Air Freight</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-chevron-down text-[8px]"></i>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Estimated Departure</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-calendar text-xs"></i>
                                </span>
                                <input type="date" id="departureDate" required class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons (2 columns) -->
                    <div class="col-span-2 flex flex-col justify-end gap-3 pb-0.5">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm transition-all duration-200">
                            <i class="bi bi-send-fill"></i>
                            <span>Create Shipment</span>
                        </button>
                        <button type="button" id="resetBtn" class="w-full bg-white hover:bg-slate-50 text-slate-700 font-bold py-3 px-4 border border-slate-200 rounded-xl text-xs transition-all duration-200">
                            Reset Form
                        </button>
                    </div>
                </form>
            </div>

            <!-- SUCCESS ALERT BANNER -->
            <div id="successBanner" class="hidden bg-emerald-50 border border-emerald-100 rounded-2xl p-6 shadow-sm flex items-center justify-between transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl shadow-sm">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-emerald-800">Shipment Created Successfully!</h4>
                        <p class="text-xs font-medium text-emerald-600 mt-0.5">Your shipment has been created and is now being processed.</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-8 text-xs font-semibold">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Shipment ID</span>
                        <span id="bannerId" class="text-slate-800 font-bold font-mono"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Status</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-amber-50 text-amber-700 border border-amber-200">Preparing</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Estimated Arrival</span>
                        <span id="bannerETA" class="text-emerald-600 font-bold"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Shipping Method</span>
                        <span id="bannerMethod" class="text-slate-800 font-bold font-sans"></span>
                    </div>
                    <button id="bannerViewDetails" class="bg-white hover:bg-slate-50 text-slate-700 py-2 px-4 border border-slate-200 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-1.5 shadow-sm">
                        <i class="bi bi-eye"></i>
                        <span>View Details</span>
                    </button>
                </div>
            </div>

            <!-- MIDDLE SECTION: MAP, TIMELINE, INFORMATION -->
            <div class="grid grid-cols-12 gap-6">
                <!-- Map Panel (6 columns) -->
                <div class="col-span-6 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[480px] relative">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Live Shipment Tracking</h3>
                    </div>
                    <div class="flex-1 rounded-xl overflow-hidden border border-slate-100 relative">
                        <div id="shipmentMap" class="w-full h-full min-h-[350px]"></div>
                        
                        <!-- Floating legend -->
                        <div class="absolute bottom-4 left-4 z-[1000] bg-white/90 backdrop-blur-sm p-3 rounded-lg border border-slate-100 shadow-md text-[10px] font-bold text-slate-600 space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>
                                <span>Completed</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-blue-500 inline-block"></span>
                                <span>In Transit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-slate-300 inline-block"></span>
                                <span>Upcoming</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline Panel (3 columns) -->
                <div class="col-span-3 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <h3 class="text-sm font-bold text-slate-900 mb-6">Shipment Timeline</h3>
                    <div class="flex-1 relative pl-6 space-y-6 overflow-y-auto max-h-[380px]" id="timelineContainer">
                        <!-- Populated dynamically -->
                    </div>
                </div>

                <!-- Info Panel (3 columns) -->
                <div class="col-span-3 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-bold text-slate-900">Shipment Information</h3>
                            <button class="text-slate-400 hover:text-slate-600 transition-colors"><i class="bi bi-pencil"></i></button>
                        </div>
                        
                        <div class="space-y-3.5 text-xs font-semibold text-slate-700" id="infoContainer">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM SECTION: WEATHER, NEWS, HISTORY -->
            <div class="grid grid-cols-12 gap-6">
                <!-- Weather Impact Panel (3 columns) -->
                <div class="col-span-3 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[280px]">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Weather Impact</h3>
                    <div class="grid grid-cols-3 gap-2 flex-1 items-center" id="weatherContainer">
                        <!-- Populated dynamically -->
                    </div>
                </div>

                <!-- Related News Panel (4 columns) -->
                <div class="col-span-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[280px]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Related News</h3>
                        <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-755 transition-colors">View All</a>
                    </div>
                    <div class="space-y-3.5 flex-1 overflow-y-auto" id="newsContainer">
                        <!-- Populated dynamically -->
                    </div>
                </div>

                <!-- Shipment History Panel (5 columns) -->
                <div class="col-span-5 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col h-[280px]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Shipment History</h3>
                        <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-755 transition-colors">View All</a>
                    </div>
                    <div class="flex-1 overflow-x-auto min-h-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-2 pl-2">Shipment ID</th>
                                    <th class="py-2">Origin</th>
                                    <th class="py-2">Destination</th>
                                    <th class="py-2">Cargo</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2 text-center pr-2">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs font-semibold text-slate-700" id="historyTableBody">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs" id="historyPagination">
                        <!-- Populated dynamically -->
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // Database Countries serialized from Blade
        const databaseCountries = @json($dbCountries);

        // Flag emoji helper
        function getFlagEmoji(countryCode) {
            if (!countryCode || countryCode.length !== 2) return '🏳️';
            const codePoints = countryCode
                .toUpperCase()
                .split('')
                .map(char => 127397 + char.charCodeAt(0));
            return String.fromCodePoint(...codePoints);
        }

        // Build Countries & Ports list
        const countriesList = databaseCountries.length > 0 ? databaseCountries.map(c => ({
            id: c.id,
            name: c.country_name,
            code: c.country_code || '',
            flag: c.flag || getFlagEmoji(c.country_code),
            lat: parseFloat(c.latitude),
            lng: parseFloat(c.longitude),
            ports: (c.ports || []).map(p => ({
                id: p.id,
                name: p.port_name,
                city: p.city || '',
                lat: parseFloat(p.latitude),
                lng: parseFloat(p.longitude)
            }))
        })) : [
            {
                name: 'Japan', code: 'JP', flag: '🇯🇵', lat: 36.2048, lng: 138.2529,
                ports: [{ name: 'Port of Tokyo', city: 'Tokyo', lat: 35.6174, lng: 139.7744 }]
            },
            {
                name: 'Indonesia', code: 'ID', flag: '🇮🇩', lat: -6.2088, lng: 106.8456,
                ports: [{ name: 'Tanjung Priok, Jakarta', city: 'Jakarta', lat: -6.104, lng: 106.885 }]
            },
            {
                name: 'Singapore', code: 'SG', flag: '🇸🇬', lat: 1.3521, lng: 103.8198,
                ports: [{ name: 'Port of Singapore', city: 'Singapore', lat: 1.264, lng: 103.84 }]
            },
            {
                name: 'China', code: 'CN', flag: '🇨🇳', lat: 35.8617, lng: 104.1954,
                ports: [{ name: 'Port of Shanghai', city: 'Shanghai', lat: 31.230, lng: 121.490 }]
            },
            {
                name: 'United States', code: 'US', flag: '🇺🇸', lat: 37.0902, lng: -95.7129,
                ports: [{ name: 'Port of Los Angeles', city: 'Los Angeles', lat: 33.736, lng: -118.262 }]
            },
            {
                name: 'Germany', code: 'DE', flag: '🇩🇪', lat: 52.52, lng: 13.405,
                ports: [{ name: 'Hamburg Port', city: 'Hamburg', lat: 53.54, lng: 9.99 }]
            },
            {
                name: 'Australia', code: 'AU', flag: '🇦🇺', lat: -25.2744, lng: 133.7751,
                ports: [{ name: 'Sydney Port', city: 'Sydney', lat: -33.8688, lng: 151.2093 }]
            },
            {
                name: 'Thailand', code: 'TH', flag: '🇹🇭', lat: 15.87, lng: 100.99,
                ports: [{ name: 'Laem Chabang Port', city: 'Chonburi', lat: 13.08, lng: 100.89 }]
            },
            {
                name: 'Malaysia', code: 'MY', flag: '🇲🇾', lat: 4.21, lng: 101.97,
                ports: [{ name: 'Port Klang', city: 'Klang', lat: 3.0, lng: 101.4 }]
            }
        ];

        // Default shipments dataset
        const defaultShipments = [
            {
                id: 'SHP-20260725-001',
                origin: 'Japan',
                originPort: 'Port of Tokyo',
                originFlag: '🇯🇵',
                originLat: 35.6174,
                originLng: 139.7744,
                destination: 'Indonesia',
                destinationPort: 'Tanjung Priok, Jakarta',
                destinationFlag: '🇮🇩',
                destinationLat: -6.104,
                destinationLng: 106.885,
                transit: 'Singapore',
                transitPort: 'Port of Singapore',
                transitFlag: '🇸🇬',
                transitLat: 1.264,
                transitLng: 103.84,
                cargo: 'Backpack',
                cargoType: 'Clothing',
                quantity: 200,
                weight: 2.5,
                shippingMethod: 'Sea Freight',
                status: 'In Transit',
                eta: '3 Days',
                etaDate: '01 Aug 2026',
                riskLevel: 'Medium Risk',
                currentLocation: 'Port of Singapore',
                departureDate: '25 Jul 2026',
                timeline: [
                    { title: 'Warehouse', location: 'Tokyo, Japan', time: '25 Jul 2026 08:30', completed: true },
                    { title: 'Loaded', location: 'Port of Tokyo', time: '25 Jul 2026 15:45', completed: true },
                    { title: 'On Sea', location: 'In Transit', time: '26 Jul 2026 06:20', completed: true },
                    { title: 'Transit', location: 'Port of Singapore', time: '28 Jul 2026 12:00', completed: false, current: true },
                    { title: 'Arrival', location: 'Tanjung Priok Port', time: '01 Aug 2026 (ETA)', completed: false },
                    { title: 'Delivered', location: 'Indonesia', time: 'Pending', completed: false }
                ],
                weather: [
                    { place: 'Origin', name: 'Japan', temp: '22°C', type: 'Cloudy', icon: 'bi-cloud-sun', risk: 'Low' },
                    { place: 'Transit', name: 'Singapore', temp: '28°C', type: 'Storm', icon: 'bi-cloud-lightning-rain', risk: 'High' },
                    { place: 'Destination', name: 'Indonesia', temp: '30°C', type: 'Rain', icon: 'bi-cloud-rain', risk: 'Medium' }
                ],
                news: [
                    { title: 'Heavy Rain Hits Singapore Port', desc: 'Heavy rainfall causes delays in container handling...', category: 'Logistics', date: '25 Jul 2026 10:30', image: 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=150&auto=format&fit=crop&q=60' },
                    { title: 'Port Congestion at Tokyo Port', desc: 'Increased export activity leads to congestion...', category: 'Shipping', date: '25 Jul 2026 08:15', image: 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?w=150&auto=format&fit=crop&q=60' },
                    { title: 'Indonesia Customs Update', desc: 'New regulations for imported goods effective...', category: 'Trade', date: '24 Jul 2026 16:45', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=150&auto=format&fit=crop&q=60' }
                ]
            },
            {
                id: 'SHP-20260720-002',
                origin: 'China',
                originPort: 'Port of Shanghai',
                originFlag: '🇨🇳',
                originLat: 31.230,
                originLng: 121.490,
                destination: 'Australia',
                destinationPort: 'Sydney Port',
                destinationFlag: '🇦🇺',
                destinationLat: -33.8688,
                destinationLng: 151.2093,
                transit: '', transitPort: '', transitFlag: '',
                cargo: 'Shoes',
                cargoType: 'Clothing',
                quantity: 150,
                weight: 1.8,
                shippingMethod: 'Air Freight',
                status: 'Delivered',
                eta: 'Completed',
                etaDate: '23 Jul 2026',
                riskLevel: 'Low Risk',
                currentLocation: 'Sydney, Australia',
                departureDate: '20 Jul 2026',
                timeline: [
                    { title: 'Warehouse', location: 'Shanghai, China', time: '20 Jul 2026 09:00', completed: true },
                    { title: 'Loaded', location: 'Port of Shanghai', time: '20 Jul 2026 14:00', completed: true },
                    { title: 'In Transit', location: 'Air Transit', time: '21 Jul 2026 02:00', completed: true },
                    { title: 'Arrival', location: 'Sydney Airport', time: '22 Jul 2026 18:00', completed: true },
                    { title: 'Delivered', location: 'Sydney, Australia', time: '23 Jul 2026 10:00', completed: true }
                ],
                weather: [
                    { place: 'Origin', name: 'China', temp: '26°C', type: 'Sunny', icon: 'bi-sun', risk: 'Low' },
                    { place: 'Destination', name: 'Australia', temp: '16°C', type: 'Cloudy', icon: 'bi-cloud', risk: 'Low' }
                ],
                news: [
                    { title: 'Shanghai Port Trade Growth', desc: 'Export volumes hit record highs this quarter...', category: 'Trade', date: '21 Jul 2026 11:30', image: 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=150&auto=format&fit=crop&q=60' }
                ]
            },
            {
                id: 'SHP-20260718-003',
                origin: 'United States',
                originPort: 'Port of Los Angeles',
                originFlag: '🇺🇸',
                originLat: 33.736,
                originLng: -118.262,
                destination: 'Germany',
                destinationPort: 'Hamburg Port',
                destinationFlag: '🇩🇪',
                destinationLat: 53.54,
                destinationLng: 9.99,
                transit: '', transitPort: '', transitFlag: '',
                cargo: 'Jacket',
                cargoType: 'Clothing',
                quantity: 500,
                weight: 4.2,
                shippingMethod: 'Sea Freight',
                status: 'Delivered',
                eta: 'Completed',
                etaDate: '26 Jul 2026',
                riskLevel: 'Low Risk',
                currentLocation: 'Hamburg, Germany',
                departureDate: '18 Jul 2026',
                timeline: [
                    { title: 'Warehouse', location: 'Los Angeles, USA', time: '18 Jul 2026 08:00', completed: true },
                    { title: 'Loaded', location: 'Port of Los Angeles', time: '18 Jul 2026 17:00', completed: true },
                    { title: 'In Transit', location: 'Atlantic Ocean', time: '20 Jul 2026 12:00', completed: true },
                    { title: 'Arrival', location: 'Hamburg Port', time: '25 Jul 2026 09:00', completed: true },
                    { title: 'Delivered', location: 'Hamburg, Germany', time: '26 Jul 2026 11:00', completed: true }
                ],
                weather: [
                    { place: 'Origin', name: 'USA', temp: '25°C', type: 'Sunny', icon: 'bi-sun', risk: 'Low' },
                    { place: 'Destination', name: 'Germany', temp: '20°C', type: 'Cloudy', icon: 'bi-cloud', risk: 'Low' }
                ],
                news: [
                    { title: 'LA Port Congestion Clears', desc: 'Average waiting times reduced by 15% this week...', category: 'Logistics', date: '19 Jul 2026 09:00', image: 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=150&auto=format&fit=crop&q=60' }
                ]
            },
            {
                id: 'SHP-20260715-004',
                origin: 'South Korea',
                originPort: 'Busan Port',
                originFlag: '🇰🇷',
                originLat: 35.1796,
                originLng: 129.0756,
                destination: 'Indonesia',
                destinationPort: 'Tanjung Priok, Jakarta',
                destinationFlag: '🇮🇩',
                destinationLat: -6.104,
                destinationLng: 106.885,
                transit: 'Shanghai',
                transitPort: 'Port of Shanghai',
                transitFlag: '🇨🇳',
                transitLat: 31.230,
                transitLng: 121.490,
                cargo: 'T-Shirt',
                cargoType: 'Clothing',
                quantity: 1000,
                weight: 8.5,
                shippingMethod: 'Sea Freight',
                status: 'Delayed',
                eta: '5 Days',
                etaDate: '30 Jul 2026',
                riskLevel: 'High Risk',
                currentLocation: 'Port of Shanghai',
                departureDate: '15 Jul 2026',
                timeline: [
                    { title: 'Warehouse', location: 'Seoul, South Korea', time: '15 Jul 2026 08:00', completed: true },
                    { title: 'Loaded', location: 'Busan Port', time: '15 Jul 2026 18:00', completed: true },
                    { title: 'In Transit', location: 'East China Sea', time: '17 Jul 2026 10:00', completed: true },
                    { title: 'Transit', location: 'Port of Shanghai (Congested)', time: '20 Jul 2026 14:00', completed: false, current: true },
                    { title: 'Arrival', location: 'Tanjung Priok Port', time: '30 Jul 2026 (Delayed ETA)', completed: false },
                    { title: 'Delivered', location: 'Indonesia', time: 'Pending', completed: false }
                ],
                weather: [
                    { place: 'Origin', name: 'Korea', temp: '24°C', type: 'Sunny', icon: 'bi-sun', risk: 'Low' },
                    { place: 'Transit', name: 'Shanghai', temp: '32°C', type: 'Storm', icon: 'bi-cloud-lightning-rain', risk: 'High' },
                    { place: 'Destination', name: 'Indonesia', temp: '29°C', type: 'Rain', icon: 'bi-cloud-rain', risk: 'Medium' }
                ],
                news: [
                    { title: 'Typhoon Delays Shipping in East China Sea', desc: 'Typhoon activity triggers high risk warnings...', category: 'Weather', date: '18 Jul 2026 10:00', image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=150&auto=format&fit=crop&q=60' }
                ]
            },
            {
                id: 'SHP-20260712-005',
                origin: 'Thailand',
                originPort: 'Laem Chabang Port',
                originFlag: '🇹🇭',
                originLat: 13.08,
                originLng: 100.89,
                destination: 'Malaysia',
                destinationPort: 'Port Klang',
                destinationFlag: '🇲🇾',
                destinationLat: 3.0,
                destinationLng: 101.4,
                transit: '', transitPort: '', transitFlag: '',
                cargo: 'Hat',
                cargoType: 'Clothing',
                quantity: 300,
                weight: 2.0,
                shippingMethod: 'Sea Freight',
                status: 'In Transit',
                eta: '1 Day',
                etaDate: '26 Jul 2026',
                riskLevel: 'Medium Risk',
                currentLocation: 'Strait of Malacca',
                departureDate: '20 Jul 2026',
                timeline: [
                    { title: 'Warehouse', location: 'Bangkok, Thailand', time: '20 Jul 2026 09:00', completed: true },
                    { title: 'Loaded', location: 'Laem Chabang Port', time: '20 Jul 2026 16:00', completed: true },
                    { title: 'In Transit', location: 'Strait of Malacca', time: '22 Jul 2026 11:00', completed: false, current: true },
                    { title: 'Arrival', location: 'Port Klang', time: '26 Jul 2026 (ETA)', completed: false },
                    { title: 'Delivered', location: 'Kuala Lumpur, Malaysia', time: 'Pending', completed: false }
                ],
                weather: [
                    { place: 'Origin', name: 'Thailand', temp: '31°C', type: 'Sunny', icon: 'bi-sun', risk: 'Low' },
                    { place: 'Destination', name: 'Malaysia', temp: '28°C', type: 'Cloudy', icon: 'bi-cloud-sun', risk: 'Medium' }
                ],
                news: [
                    { title: 'Malacca Strait Security Brief', desc: 'Patrols increased along key trade channels...', category: 'Security', date: '21 Jul 2026 14:00', image: 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?w=150&auto=format&fit=crop&q=60' }
                ]
            }
        ];

        // State variables
        let shipments = [];
        let activeShipmentId = '';
        let historyPage = 1;
        const historyPageSize = 5;
        let searchQuery = '';

        // Leaflet Map state
        let map;
        let routeLayer;
        let markersLayer;

        // Date format helpers
        function formatDate(date) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const d = new Date(date);
            const day = d.getDate();
            const month = months[d.getMonth()];
            const year = d.getFullYear();
            return `${day} ${month} ${year}`;
        }

        function addDays(date, days) {
            const result = new Date(date);
            result.setDate(result.getDate() + days);
            return result;
        }

        function formatDateTime(date, timeStr) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const d = new Date(date);
            const day = d.getDate();
            const month = months[d.getMonth()];
            const year = d.getFullYear();
            return `${day} ${month} ${year} ${timeStr}`;
        }

        // Initialize elements
        const originCountrySelect = document.getElementById('originCountry');
        const destinationCountrySelect = document.getElementById('destinationCountry');
        const originPortSelect = document.getElementById('originPort');
        const destinationPortSelect = document.getElementById('destinationPort');

        // Populate Countries Select fields
        function populateCountries() {
            originCountrySelect.innerHTML = '<option value="">Select Country</option>';
            destinationCountrySelect.innerHTML = '<option value="">Select Country</option>';
            
            countriesList.forEach(c => {
                const flag = c.flag || '🏳️';
                originCountrySelect.innerHTML += `<option value="${c.name}">${flag} ${c.name}</option>`;
                destinationCountrySelect.innerHTML += `<option value="${c.name}">${flag} ${c.name}</option>`;
            });
        }

        // Filter ports based on selected country
        function handleCountryChange(type) {
            const countryName = type === 'origin' ? originCountrySelect.value : destinationCountrySelect.value;
            const portSelect = type === 'origin' ? originPortSelect : destinationPortSelect;
            
            portSelect.innerHTML = '<option value="">Select Port</option>';
            if (!countryName) return;
            
            const country = countriesList.find(c => c.name === countryName);
            if (country && country.ports.length > 0) {
                country.ports.forEach(p => {
                    portSelect.innerHTML += `<option value="${p.name}">${p.name}</option>`;
                });
            } else {
                portSelect.innerHTML += `<option value="Port of ${countryName}">Port of ${countryName}</option>`;
            }
        }

        // Setup Leaflet map
        function initMap() {
            if (map) return;
            map = L.map('shipmentMap', {
                zoomControl: false,
                attributionControl: false
            }).setView([20, 40], 1.5);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 18
            }).addTo(map);

            L.control.zoom({ position: 'topleft' }).addTo(map);

            routeLayer = L.layerGroup().addTo(map);
            markersLayer = L.layerGroup().addTo(map);
        }

        // Load shipments from localStorage or defaults
        function loadShipments() {
            const data = localStorage.getItem('gsc_shipments');
            if (data) {
                shipments = JSON.parse(data);
            } else {
                shipments = defaultShipments;
                localStorage.setItem('gsc_shipments', JSON.stringify(shipments));
            }
        }

        // Update tracking map visualization
        function updateMap(shipment) {
            if (!map) return;
            map.invalidateSize();
            routeLayer.clearLayers();
            markersLayer.clearLayers();

            const pathCoords = [];

            function addMarker(lat, lng, color, title, label) {
                if (lat === null || lng === null || isNaN(lat) || isNaN(lng)) return;
                const marker = L.circleMarker([lat, lng], {
                    radius: 8,
                    fillColor: color,
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.9
                });
                
                marker.bindTooltip(`<b>${label}</b><br><span style="font-size: 9px; color: #64748b;">${title}</span>`, {
                    permanent: true,
                    direction: 'top',
                    className: 'text-[9px] font-bold px-2 py-0.5 rounded bg-white shadow-sm border border-slate-100 text-slate-800'
                });
                
                markersLayer.addLayer(marker);
                pathCoords.push([lat, lng]);
            }

            // Origin
            if (shipment.originLat !== undefined) {
                addMarker(shipment.originLat, shipment.originLng, '#10B981', shipment.originPort || shipment.origin, shipment.origin);
            }

            // Transit
            if (shipment.transitLat !== undefined && shipment.transitLat !== null) {
                addMarker(shipment.transitLat, shipment.transitLng, '#3B82F6', shipment.transitPort || shipment.transit, shipment.transit);
            }

            // Destination
            if (shipment.destinationLat !== undefined) {
                addMarker(shipment.destinationLat, shipment.destinationLng, '#EF4444', shipment.destinationPort || shipment.destination, shipment.destination);
            }

            // Polyline route
            if (pathCoords.length >= 2) {
                const polyline = L.polyline(pathCoords, {
                    color: '#F59E0B',
                    weight: 3,
                    dashArray: '6, 8',
                    opacity: 0.8
                });
                routeLayer.addLayer(polyline);
                map.fitBounds(pathCoords, { padding: [50, 50] });
            }
        }

        // Update vertical timeline
        function updateTimeline(shipment) {
            const container = document.getElementById('timelineContainer');
            container.innerHTML = '';

            if (!shipment.timeline || shipment.timeline.length === 0) {
                container.innerHTML = '<p class="text-slate-400 text-xs mt-4">No timeline details available.</p>';
                return;
            }

            const line = document.createElement('div');
            line.className = 'absolute left-2.5 top-2.5 bottom-2.5 w-0.5 bg-slate-100';
            container.appendChild(line);

            shipment.timeline.forEach((step, index) => {
                const stepDiv = document.createElement('div');
                stepDiv.className = 'relative flex gap-3 min-w-0';

                let markerClass = '';
                let iconHTML = '';
                let textClass = 'text-slate-400';
                let titleClass = 'text-slate-400';

                if (step.completed) {
                    markerClass = 'bg-emerald-500 border-emerald-500 text-white';
                    iconHTML = '<i class="bi bi-check-lg text-[8px] leading-none"></i>';
                    titleClass = 'text-slate-800 font-bold';
                    textClass = 'text-slate-500';
                } else if (step.current) {
                    markerClass = 'bg-blue-600 border-blue-600 ring-4 ring-blue-500/20 text-white';
                    iconHTML = '<span class="h-1 w-1 rounded-full bg-white"></span>';
                    titleClass = 'text-blue-600 font-bold';
                    textClass = 'text-slate-600';
                } else {
                    markerClass = 'bg-white border-slate-200 text-slate-400';
                    titleClass = 'text-slate-450 font-medium';
                    textClass = 'text-slate-400';
                }

                stepDiv.innerHTML = `
                    <div class="absolute -left-[23px] h-[13px] w-[13px] rounded-full border flex items-center justify-center ${markerClass}">
                        ${iconHTML}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="text-xs ${titleClass}">${step.title}</h4>
                            <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">${step.time || ''}</span>
                        </div>
                        <p class="text-[10px] ${textClass} mt-0.5">${step.location || ''}</p>
                    </div>
                `;
                container.appendChild(stepDiv);
            });
        }

        // Update Shipment Information
        function updateInfoPanel(shipment) {
            const container = document.getElementById('infoContainer');
            container.innerHTML = '';

            let statusBadgeClass = '';
            if (shipment.status === 'In Transit') statusBadgeClass = 'bg-blue-50 text-blue-700 border border-blue-200';
            else if (shipment.status === 'Delivered') statusBadgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-250';
            else if (shipment.status === 'Delayed') statusBadgeClass = 'bg-red-50 text-red-750 border border-red-200';
            else statusBadgeClass = 'bg-amber-50 text-amber-700 border border-amber-250';

            let riskBadgeClass = '';
            if (shipment.riskLevel === 'High Risk' || shipment.riskLevel === 'High') riskBadgeClass = 'bg-red-55 text-red-600 border border-red-200 bg-red-50/50';
            else if (shipment.riskLevel === 'Medium Risk' || shipment.riskLevel === 'Medium') riskBadgeClass = 'bg-amber-55 text-amber-605 border border-amber-250 bg-amber-50/50';
            else riskBadgeClass = 'bg-emerald-55 text-emerald-600 border border-emerald-200 bg-emerald-50/50';

            const items = [
                { label: 'Shipment ID', value: shipment.id, isMono: true, icon: 'bi-hash' },
                { label: 'Cargo', value: shipment.cargo, icon: 'bi-box' },
                { label: 'Quantity', value: `${shipment.quantity} Pcs`, icon: 'bi-clipboard-data' },
                { label: 'Weight', value: `${shipment.weight} Ton`, icon: 'bi-speedometer' },
                { label: 'Origin', value: `${shipment.originFlag || ''} ${shipment.origin}`, icon: 'bi-geo-alt' },
                { label: 'Destination', value: `${shipment.destinationFlag || ''} ${shipment.destination}`, icon: 'bi-geo-alt-fill' },
                { label: 'Shipping Method', value: shipment.shippingMethod, icon: 'bi-truck' },
                { label: 'Current Location', value: shipment.currentLocation || '-', icon: 'bi-compass' },
                { label: 'Status', value: `<span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase ${statusBadgeClass}">${shipment.status}</span>`, isRaw: true, icon: 'bi-info-circle' },
                { label: 'ETA', value: shipment.eta || '-', icon: 'bi-clock' },
                { label: 'Risk Level', value: `<span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase ${riskBadgeClass}">${shipment.riskLevel}</span>`, isRaw: true, icon: 'bi-exclamation-triangle' }
            ];

            items.forEach(item => {
                const row = document.createElement('div');
                row.className = 'flex justify-between items-center py-1.5 border-b border-slate-50 last:border-0';
                
                let valueHTML = '';
                if (item.isRaw) {
                    valueHTML = item.value;
                } else {
                    const valClass = item.isMono ? 'font-mono font-bold text-slate-800' : 'text-slate-800 font-bold';
                    valueHTML = `<span class="${valClass}">${item.value}</span>`;
                }

                row.innerHTML = `
                    <div class="flex items-center gap-2 text-slate-450">
                        <i class="bi ${item.icon} text-sm"></i>
                        <span class="font-medium text-slate-400">${item.label}</span>
                    </div>
                    ${valueHTML}
                `;
                container.appendChild(row);
            });
        }

        // Update Weather impact panel
        function updateWeatherPanel(shipment) {
            const container = document.getElementById('weatherContainer');
            container.innerHTML = '';

            let weatherList = shipment.weather;
            if (!weatherList || weatherList.length === 0) {
                weatherList = [
                    { place: 'Origin', name: shipment.origin, temp: '22°C', type: 'Cloudy', icon: 'bi-cloud-sun', risk: 'Low' },
                    { place: 'Destination', name: shipment.destination, temp: '30°C', type: 'Rain', icon: 'bi-cloud-rain', risk: 'Medium' }
                ];
            }

            container.className = `grid grid-cols-${weatherList.length} gap-2 flex-1 items-center`;

            weatherList.forEach(w => {
                let riskBadgeClass = '';
                if (w.risk === 'High') riskBadgeClass = 'bg-red-50 text-red-650 border-red-100';
                else if (w.risk === 'Medium') riskBadgeClass = 'bg-amber-50 text-amber-650 border-amber-100';
                else riskBadgeClass = 'bg-emerald-50 text-emerald-650 border-emerald-100';

                const card = document.createElement('div');
                card.className = 'bg-slate-50 border border-slate-100 rounded-xl p-2.5 flex flex-col items-center justify-between text-center h-full min-h-[140px]';
                card.innerHTML = `
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">${w.place}</span>
                        <span class="text-xs font-bold text-slate-800 block truncate mt-0.5 max-w-[70px]" title="${w.name}">${w.name}</span>
                    </div>
                    
                    <div class="my-1.5">
                        <i class="bi ${w.icon} text-xl text-blue-500 leading-none"></i>
                        <span class="text-xs font-extrabold text-slate-800 block mt-1">${w.temp}</span>
                        <span class="text-[8px] font-semibold text-slate-400 block mt-0.5">${w.type}</span>
                    </div>
                    
                    <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase border ${riskBadgeClass}">${w.risk} Risk</span>
                `;
                container.appendChild(card);
            });
        }

        // Update news cards
        function updateNewsPanel(shipment) {
            const container = document.getElementById('newsContainer');
            container.innerHTML = '';

            const news = shipment.news && shipment.news.length > 0 ? shipment.news : [
                { title: 'Global Logistics Adjustments', desc: 'Freight rates fluctuating due to seasonal ocean current shifts...', category: 'Logistics', date: shipment.departureDate || '25 Jul 2026', image: 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=150&auto=format&fit=crop&q=60' }
            ];

            news.slice(0, 3).forEach(item => {
                const card = document.createElement('div');
                card.className = 'flex gap-3 items-start border-b border-slate-50 last:border-0 pb-3 last:pb-0';
                card.innerHTML = `
                    <img src="${item.image}" alt="${item.title}" class="h-10 w-14 object-cover rounded-lg border border-slate-100 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2 hover:text-blue-650 transition-colors cursor-pointer" title="${item.title}">${item.title}</h4>
                        <p class="text-[9px] text-slate-400 mt-1 flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded font-extrabold uppercase text-[7px]">${item.category}</span>
                            <span>•</span>
                            <span>${item.date}</span>
                        </p>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        // Set active tracking view
        function setActiveShipment(id) {
            activeShipmentId = id;
            const shipment = shipments.find(s => s.id === id);
            if (!shipment) return;

            updateMap(shipment);
            updateTimeline(shipment);
            updateInfoPanel(shipment);
            updateWeatherPanel(shipment);
            updateNewsPanel(shipment);

            updateHistoryTableHighlight();
        }

        // Render history table with client pagination/search
        function updateHistoryTable() {
            const tbody = document.getElementById('historyTableBody');
            tbody.innerHTML = '';

            const filteredShipments = shipments.filter(s => {
                if (!searchQuery) return true;
                const q = searchQuery.toLowerCase();
                return s.id.toLowerCase().includes(q) ||
                       s.origin.toLowerCase().includes(q) ||
                       s.destination.toLowerCase().includes(q) ||
                       (s.cargo && s.cargo.toLowerCase().includes(q)) ||
                       s.status.toLowerCase().includes(q);
            });

            const totalItems = filteredShipments.length;
            const totalPages = Math.ceil(totalItems / historyPageSize) || 1;
            
            if (historyPage > totalPages) historyPage = totalPages;
            if (historyPage < 1) historyPage = 1;

            const startIndex = (historyPage - 1) * historyPageSize;
            const endIndex = Math.min(startIndex + historyPageSize, totalItems);
            const paginatedShipments = filteredShipments.slice(startIndex, endIndex);

            if (paginatedShipments.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400 font-bold text-xs">No shipments found.</td>
                    </tr>
                `;
                updatePaginationControls(0, 0, 0, 1);
                return;
            }

            paginatedShipments.forEach(s => {
                let statusBadgeClass = '';
                if (s.status === 'In Transit') statusBadgeClass = 'bg-blue-50 text-blue-700 border border-blue-200';
                else if (s.status === 'Delivered') statusBadgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                else if (s.status === 'Delayed') statusBadgeClass = 'bg-red-50 text-red-750 border border-red-200';
                else statusBadgeClass = 'bg-amber-50 text-amber-700 border border-amber-200';

                const isActive = s.id === activeShipmentId;
                const trClass = isActive ? 'bg-blue-50/40 hover:bg-blue-50/50' : 'hover:bg-slate-50/50';

                const tr = document.createElement('tr');
                tr.className = `${trClass} transition-all duration-150 cursor-pointer`;
                tr.onclick = () => setActiveShipment(s.id);
                tr.innerHTML = `
                    <td class="py-2.5 pl-2 font-bold text-brand-blue font-mono">${s.id}</td>
                    <td class="py-2.5">
                        <span class="flex items-center gap-1">
                            <span>${s.originFlag || ''}</span>
                            <span class="truncate max-w-[60px]">${s.origin}</span>
                        </span>
                    </td>
                    <td class="py-2.5">
                        <span class="flex items-center gap-1">
                            <span>${s.destinationFlag || ''}</span>
                            <span class="truncate max-w-[60px]">${s.destination}</span>
                        </span>
                    </td>
                    <td class="py-2.5 text-slate-500 font-medium truncate max-w-[60px]" title="${s.cargo || ''}">${s.cargo || '-'}</td>
                    <td class="py-2.5">
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase ${statusBadgeClass}">
                            ${s.status}
                        </span>
                    </td>
                    <td class="py-2.5 text-center pr-2" onclick="event.stopPropagation()">
                        <button onclick="setActiveShipment('${s.id}')" class="p-1 text-slate-400 hover:text-slate-700 transition-colors" title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            updatePaginationControls(startIndex + 1, endIndex, totalItems, totalPages);
        }

        function updatePaginationControls(start, end, total, totalPages) {
            const paginationContainer = document.getElementById('historyPagination');
            paginationContainer.innerHTML = '';

            if (total === 0) {
                paginationContainer.innerHTML = `<span class="text-slate-400">Showing 0 to 0 of 0 shipments</span>`;
                return;
            }

            const textSpan = document.createElement('span');
            textSpan.className = 'text-slate-500 font-medium';
            textSpan.innerText = `Showing ${start} to ${end} of ${total} shipments`;
            paginationContainer.appendChild(textSpan);

            const btnDiv = document.createElement('div');
            btnDiv.className = 'flex items-center gap-1';

            const prevBtn = document.createElement('button');
            prevBtn.className = `h-7 px-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors ${historyPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}`;
            prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
            if (historyPage > 1) {
                prevBtn.onclick = () => { historyPage--; updateHistoryTable(); };
            }
            btnDiv.appendChild(prevBtn);

            for (let i = 1; i <= totalPages; i++) {
                const pgBtn = document.createElement('button');
                if (i === historyPage) {
                    pgBtn.className = 'h-7 w-7 bg-blue-600 text-white rounded-lg font-bold shadow-sm';
                } else {
                    pgBtn.className = 'h-7 w-7 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 font-medium transition-colors';
                }
                pgBtn.innerText = i;
                pgBtn.onclick = () => { historyPage = i; updateHistoryTable(); };
                btnDiv.appendChild(pgBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = `h-7 px-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors ${historyPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}`;
            nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
            if (historyPage < totalPages) {
                nextBtn.onclick = () => { historyPage++; updateHistoryTable(); };
            }
            btnDiv.appendChild(nextBtn);

            paginationContainer.appendChild(btnDiv);
        }

        function updateHistoryTableHighlight() {
            const rows = document.querySelectorAll('#historyTableBody tr');
            rows.forEach((row, i) => {
                const idCell = row.cells[0];
                if (idCell) {
                    const shpId = idCell.innerText.trim();
                    if (shpId === activeShipmentId) {
                        row.className = 'bg-blue-50/40 hover:bg-blue-50/50 transition-all duration-150 cursor-pointer';
                    } else {
                        row.className = 'hover:bg-slate-50/50 transition-all duration-150 cursor-pointer';
                    }
                }
            });
        }

        // Form submission: Create new shipment
        function handleFormSubmit() {
            const originName = originCountrySelect.value;
            const destName = destinationCountrySelect.value;
            const originPortName = originPortSelect.value;
            const destPortName = destinationPortSelect.value;
            const cargo = document.getElementById('cargoType').value;
            const itemNameVal = document.getElementById('itemName').value;
            const qtyVal = parseInt(document.getElementById('quantity').value);
            const weightVal = parseFloat(document.getElementById('weight').value);
            const method = document.getElementById('shippingMethod').value;
            const deptDateStr = document.getElementById('departureDate').value;
            
            if (!originName || !destName || !originPortName || !destPortName || !itemNameVal || !qtyVal || !weightVal || !deptDateStr) {
                alert('Please fill out all fields.');
                return;
            }

            if (originName === destName) {
                alert('Origin and Destination country must be different.');
                return;
            }

            const originCountry = countriesList.find(c => c.name === originName);
            const destCountry = countriesList.find(c => c.name === destName);
            
            const originPort = originCountry?.ports.find(p => p.name === originPortName) || { lat: originCountry?.lat, lng: originCountry?.lng };
            const destPort = destCountry?.ports.find(p => p.name === destPortName) || { lat: destCountry?.lat, lng: destCountry?.lng };

            // Format dates
            const departureDateObj = new Date(deptDateStr);
            const dateFormatted = formatDate(departureDateObj);
            const dateCompact = deptDateStr.replace(/-/g, ''); // YYYYMMDD

            // Unique ID
            const sameDateShipments = shipments.filter(s => s.id.startsWith(`SHP-${dateCompact}`));
            const seq = String(sameDateShipments.length + 1).padStart(3, '0');
            const newId = `SHP-${dateCompact}-${seq}`;

            // Transit logic
            let transit = '';
            let transitPort = '';
            let transitFlag = '';
            let transitLat = null;
            let transitLng = null;
            
            // Auto transit Singapore for intercontinental routes
            if (method === 'Sea Freight') {
                if ((originName === 'Germany' || originName === 'Japan' || originName === 'China') && destName === 'Indonesia') {
                    transit = 'Singapore';
                    transitPort = 'Port of Singapore';
                    transitFlag = '🇸🇬';
                    transitLat = 1.264;
                    transitLng = 103.84;
                } else if (originName === 'United States' && destName === 'Japan') {
                    transit = 'Shanghai';
                    transitPort = 'Port of Shanghai';
                    transitFlag = '🇨🇳';
                    transitLat = 31.230;
                    transitLng = 121.490;
                }
            }

            // ETA
            let etaDays = method === 'Sea Freight' ? 7 : 2;
            const etaDateObj = addDays(departureDateObj, etaDays);
            const etaDateFormatted = formatDate(etaDateObj);
            const etaStr = `${etaDays} Days`;

            // Weather Impact
            const weatherList = [];
            weatherList.push({
                place: 'Origin',
                name: originName,
                temp: '22°C',
                type: 'Cloudy',
                icon: 'bi-cloud-sun',
                risk: 'Low'
            });
            
            if (transit) {
                weatherList.push({
                    place: 'Transit',
                    name: transit,
                    temp: '28°C',
                    type: 'Storm',
                    icon: 'bi-cloud-lightning-rain',
                    risk: 'High'
                });
            }

            weatherList.push({
                place: 'Destination',
                name: destName,
                temp: '30°C',
                type: 'Rain',
                icon: 'bi-cloud-rain',
                risk: 'Medium'
            });

            // Risk Level
            let risk = 'Low Risk';
            if (weatherList.some(w => w.risk === 'High')) risk = 'High Risk';
            else if (weatherList.some(w => w.risk === 'Medium')) risk = 'Medium Risk';

            // Timeline
            const timeline = [];
            if (method === 'Sea Freight') {
                timeline.push({ title: 'Warehouse', location: `${originPortName}, ${originName}`, time: formatDateTime(departureDateObj, '08:30'), completed: true });
                timeline.push({ title: 'Loaded', location: originPortName, time: formatDateTime(departureDateObj, '15:45'), completed: true });
                timeline.push({ title: 'On Sea', location: 'In Transit', time: formatDateTime(addDays(departureDateObj, 1), '06:20'), completed: true });
                if (transit) {
                    timeline.push({ title: 'Transit', location: transitPort, time: formatDateTime(addDays(departureDateObj, 3), '12:00'), completed: false, current: true });
                    timeline.push({ title: 'Arrival', location: destPortName, time: `${formatDate(etaDateObj)} (ETA)`, completed: false });
                } else {
                    timeline.push({ title: 'Arrival', location: destPortName, time: `${formatDate(etaDateObj)} (ETA)`, completed: false, current: true });
                }
                timeline.push({ title: 'Delivered', location: destName, time: 'Pending', completed: false });
            } else { // Air Freight
                timeline.push({ title: 'Warehouse', location: `${originPortName}, ${originName}`, time: formatDateTime(departureDateObj, '08:30'), completed: true });
                timeline.push({ title: 'Loaded', location: originPortName, time: formatDateTime(departureDateObj, '12:00'), completed: true });
                timeline.push({ title: 'In Transit', location: 'Air Transit', time: formatDateTime(departureDateObj, '18:00'), completed: false, current: true });
                timeline.push({ title: 'Arrival', location: destPortName, time: `${formatDate(etaDateObj)} (ETA)`, completed: false });
                timeline.push({ title: 'Delivered', location: destName, time: 'Pending', completed: false });
            }

            // News
            const newsList = [
                {
                    title: `Port congestion check from ${originName} to ${destName}`,
                    desc: `Vessel arrivals monitoring report updates route efficiency.`,
                    category: 'Shipping',
                    date: dateFormatted,
                    image: 'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?w=150&auto=format&fit=crop&q=60'
                },
                {
                    title: `${method} security protocol checks`,
                    desc: `Inspections tightened on transshipment hub routes.`,
                    category: 'Logistics',
                    date: dateFormatted,
                    image: 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=150&auto=format&fit=crop&q=60'
                }
            ];

            const newShipment = {
                id: newId,
                origin: originName,
                originPort: originPortName,
                originFlag: originCountry?.flag || getFlagEmoji(originCountry?.code),
                originLat: originPort.lat,
                originLng: originPort.lng,
                destination: destName,
                destinationPort: destPortName,
                destinationFlag: destCountry?.flag || getFlagEmoji(destCountry?.code),
                destinationLat: destPort.lat,
                destinationLng: destPort.lng,
                transit: transit,
                transitPort: transitPort,
                transitFlag: transitFlag,
                transitLat: transitLat,
                transitLng: transitLng,
                cargo: itemNameVal,
                cargoType: cargo,
                quantity: qtyVal,
                weight: weightVal,
                shippingMethod: method,
                status: 'Preparing',
                eta: etaStr,
                etaDate: etaDateFormatted,
                riskLevel: risk,
                currentLocation: `Preparing at ${originPortName}`,
                departureDate: dateFormatted,
                timeline: timeline,
                weather: weatherList,
                news: newsList
            };

            // Prepend shipment and store
            shipments.unshift(newShipment);
            localStorage.setItem('gsc_shipments', JSON.stringify(shipments));

            // Populate success alert
            const banner = document.getElementById('successBanner');
            document.getElementById('bannerId').innerText = newId;
            document.getElementById('bannerETA').innerHTML = `${etaDateFormatted} <span class="text-slate-400 text-[10px] font-normal">(${etaDays} Days)</span>`;
            document.getElementById('bannerMethod').innerText = method;
            banner.classList.remove('hidden');

            document.getElementById('bannerViewDetails').onclick = () => {
                setActiveShipment(newId);
                banner.classList.add('hidden');
            };

            // Reset form details
            document.getElementById('itemName').value = '';
            document.getElementById('quantity').value = '';
            document.getElementById('weight').value = '';
            originPortSelect.innerHTML = '<option value="">Select Port</option>';
            destinationPortSelect.innerHTML = '<option value="">Select Port</option>';
            originCountrySelect.value = '';
            destinationCountrySelect.value = '';

            setActiveShipment(newId);
            updateHistoryTable();

            banner.scrollIntoView({ behavior: 'smooth', block: 'end' });
        }

        // On document load
        document.addEventListener("DOMContentLoaded", function() {
            initMap();
            populateCountries();
            loadShipments();

            // Set default date to today
            document.getElementById('departureDate').valueAsDate = new Date();

            // Handle active shipment
            if (shipments.length > 0) {
                setActiveShipment(shipments[0].id);
            }

            updateHistoryTable();

            // Listeners
            originCountrySelect.addEventListener('change', () => handleCountryChange('origin'));
            destinationCountrySelect.addEventListener('change', () => handleCountryChange('destination'));
            
            document.getElementById('createShipmentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                handleFormSubmit();
            });

            document.getElementById('resetBtn').addEventListener('click', () => {
                document.getElementById('createShipmentForm').reset();
                document.getElementById('departureDate').valueAsDate = new Date();
                originPortSelect.innerHTML = '<option value="">Select Port</option>';
                destinationPortSelect.innerHTML = '<option value="">Select Port</option>';
            });

            // Global Search handler
            document.getElementById('globalSearch').addEventListener('input', (e) => {
                searchQuery = e.target.value;
                historyPage = 1;
                updateHistoryTable();
            });
        });
    </script>
</body>
</html>
