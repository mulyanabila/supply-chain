<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Article Analyst Management - Admin Dashboard</title>

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

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

    <!-- Chart Script -->
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

                    <a href="{{ route('admin.ports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 text-sm font-medium transition-all duration-200">
                        <i class="bi bi-anchor text-base"></i>
                        <span>Port Dataset</span>
                    </a>

                     <a href="{{ route('admin.articles') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-emerald-700 text-white font-medium text-sm transition-all duration-200 shadow-md shadow-emerald-700/20">
                        <i class="bi bi-blockquote-left text-base"></i>
                        <span>Article Analyst</span>
                    </a>

                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 text-sm font-medium transition-all duration-200">
                        <i class="bi bi-people text-base"></i>
                        <span>Users & Roles</span>
                    </a>
                   
                </div>
            </div>

            <!-- BOTTOM MENU -->
            <div class="mt-auto pt-6 border-t border-slate-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-400 hover:bg-red-600 hover:text-white transition-all duration-200">
                        <i class="bi bi-box-arrow-right text-base"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>

    </aside>

    <!-- MAIN BODY SECTION -->
    <div class="flex-1 pl-64 flex flex-col min-w-0">

        <!-- TOP BAR HEADER -->
        <header class="bg-white border-b border-slate-100 sticky top-0 z-20 px-8 py-4 flex items-center justify-between shadow-sm">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">Article Analyst Management</h2>
                <p class="text-slate-500 text-xs font-medium">Manage and analyze global supply chain risk articles and news intelligence.</p>
            </div>

            <!-- Breadcrumbs, Notify and Profile actions -->
            <div class="flex items-center gap-6">

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
            @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-semibold rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 focus:outline-none"><i class="bi bi-x"></i></button>
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-100 text-red-700 text-sm font-semibold rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 focus:outline-none"><i class="bi bi-x"></i></button>
            </div>
            @endif

            <!-- METRICS ROW (4 Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Articles -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-xl font-bold border border-blue-100">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Total Articles</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalArticles) }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-base leading-none"></i> 18.4% from last month
                        </span>
                    </div>
                </div>

                <!-- Published Articles -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Published Articles</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($publishedArticles) }}</h3>
                        <span class="text-[11px] font-semibold text-slate-455 block mt-1">
                            {{ round(($publishedArticles/max($totalArticles, 1))*100, 1) }}% of total articles
                        </span>
                    </div>
                </div>

                <!-- Draft Articles -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold border border-amber-100">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Draft Articles</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($draftArticles) }}</h3>
                        <span class="text-[11px] font-semibold text-slate-455 block mt-1">
                            {{ round(($draftArticles/max($totalArticles, 1))*100, 1) }}% of total articles
                        </span>
                    </div>
                </div>

                <!-- AI Sentiment Analyzed -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold border border-purple-100">
                        <i class="bi bi-cpu"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">AI Sentiment Analyzed</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($aiSentimentAnalyzedCount) }}</h3>
                        <span class="text-[11px] font-semibold text-slate-455 block mt-1">
                            100% of total articles
                        </span>
                    </div>
                </div>
            </div>

            <!-- MIDDLE SECTION (Filters, Table & Activity Feed) -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Articles Table (col-span-3) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-3">
                    
                    <!-- Filters form container -->
                    <div class="flex flex-col gap-3 mb-6">
                        <!-- Top Row: Search/Category Filter (form) and Action Buttons (separate forms) -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <!-- Search & Category Form -->
                            <form method="GET" action="{{ route('admin.articles') }}" id="searchFilterForm" class="flex flex-wrap items-center gap-3 m-0">
                                <!-- Search -->
                                <div class="relative w-80">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                        <i class="bi bi-search text-xs"></i>
                                    </span>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search article title, keyword..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200 text-slate-700 placeholder-slate-450">
                                </div>

                                <!-- Category Filter -->
                                <div class="relative">
                                    <select name="category" onchange="this.form.submit()" class="appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 pr-8 text-xs font-semibold text-slate-655 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                        <option value="All Categories">All Categories</option>
                                        @foreach($categoriesList as $cName)
                                            <option value="{{ $cName }}" {{ request('category') === $cName ? 'selected' : '' }}>{{ $cName }}</option>
                                        @endforeach
                                    </select>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                        <i class="bi bi-chevron-down text-[8px]"></i>
                                    </span>
                                </div>

                                @if(request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                            </form>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.articles.import') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="border border-blue-600 text-blue-600 bg-white hover:bg-blue-50 px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-200">
                                        <i class="bi bi-cloud-arrow-down text-sm"></i>
                                        <span>Import News API</span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.articles.analyze') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="border border-purple-600 text-purple-600 bg-white hover:bg-purple-50 px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-200">
                                        <i class="bi bi-cpu text-sm"></i>
                                        <span>Analyze AI</span>
                                    </button>
                                </form>
                                <button type="button" onclick="openCreateModal()" class="bg-emerald-800 hover:bg-emerald-900 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all duration-200">
                                    <i class="bi bi-plus text-base leading-none"></i>
                                    <span>Create Article</span>
                                </button>
                            </div>
                        </div>

                        <!-- Bottom Row: Status Filter Form -->
                        <div class="flex items-center">
                            <form method="GET" action="{{ route('admin.articles') }}" class="m-0">
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('category'))
                                    <input type="hidden" name="category" value="{{ request('category') }}">
                                @endif

                                <!-- Status Filter -->
                                <div class="relative">
                                    <select name="status" onchange="this.form.submit()" class="appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 pr-8 text-xs font-semibold text-slate-655 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                        <option value="">All Status</option>
                                        <option value="Published" {{ request('status') === 'Published' ? 'selected' : '' }}>Published</option>
                                        <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                                    </select>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                        <i class="bi bi-chevron-down text-[8px]"></i>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3 pl-4 w-20">THUMBNAIL</th>
                                    <th class="py-3 pl-4">ARTICLE TITLE</th>
                                    <th class="py-3">CATEGORY</th>
                                    <th class="py-3">COUNTRY</th>
                                    <th class="py-3">SOURCE</th>
                                    <th class="py-3">PUBLISHED DATE</th>
                                    <th class="py-3">SENTIMENT</th>
                                    <th class="py-3">RISK LEVEL</th>
                                    <th class="py-3">STATUS</th>
                                    <th class="py-3 text-center pr-4">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs font-semibold text-slate-700">
                                @forelse($formattedArticles as $art)
                                <tr class="hover:bg-slate-50/50 transition-all duration-150 border-b border-slate-100/50">
                                    <td class="py-3 pl-4">
                                        <img src="{{ $art['thumbnail'] }}" alt="article thumb" class="w-14 h-10 object-cover rounded-lg border border-slate-100 shadow-sm" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?auto=format&fit=crop&q=80&w=150';">
                                    </td>
                                    <td class="py-3 pl-4 max-w-xs">
                                        <h4 class="font-bold text-slate-800 leading-snug line-clamp-2 hover:text-brand-blue cursor-pointer" onclick="openViewModal({{ json_encode($art) }})">{{ $art['title'] }}</h4>
                                        <p class="text-[10px] text-slate-455 font-medium line-clamp-1 mt-0.5">{{ $art['content_summary'] }}</p>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold inline-block border
                                            @if($art['category'] === 'Logistics') bg-blue-50 text-blue-600 border-blue-100
                                            @elseif($art['category'] === 'Trade') bg-emerald-50 text-emerald-600 border-emerald-100
                                            @elseif($art['category'] === 'Shipping') bg-purple-50 text-purple-600 border-purple-100
                                            @elseif($art['category'] === 'Economy') bg-amber-50 text-amber-600 border-amber-100
                                            @else bg-slate-50 text-slate-700 border-slate-200 @endif">
                                            {{ $art['category'] }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="flex items-center gap-1 font-bold text-slate-600">
                                            <span>{{ $art['flag'] }}</span>
                                            <span>{{ $art['country'] }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3 text-slate-500 font-medium">
                                        {{ $art['source'] }}
                                    </td>
                                    <td class="py-3 text-slate-450 font-medium whitespace-nowrap">{{ $art['published_date'] }}</td>
                                    <td class="py-3">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold inline-block border
                                            @if($art['sentiment'] === 'Positive') bg-emerald-50 text-emerald-600 border-emerald-100
                                            @elseif($art['sentiment'] === 'Negative') bg-red-50 text-red-650 border-red-100
                                            @else bg-amber-50 text-amber-600 border-amber-100 @endif">
                                            {{ $art['sentiment'] }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold inline-block border
                                            @if($art['risk_level'] === 'High') bg-red-50 text-red-650 border-red-100
                                            @elseif($art['risk_level'] === 'Medium') bg-amber-50 text-amber-650 border-amber-100
                                            @else bg-emerald-50 text-emerald-600 border-emerald-100 @endif">
                                            {{ $art['risk_level'] }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold inline-block border
                                            @if($art['status'] === 'Published') bg-emerald-50 text-emerald-600 border-emerald-100
                                            @else bg-amber-50 text-amber-655 border-amber-100 @endif">
                                            {{ $art['status'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center pr-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" onclick="openViewModal({{ json_encode($art) }})" class="p-1.5 text-blue-500 bg-blue-50/30 hover:bg-blue-50 border border-blue-100 rounded-lg transition-all" title="View"><i class="bi bi-eye"></i></button>
                                            <button type="button" onclick="openEditModal({{ json_encode($art) }})" class="p-1.5 text-amber-500 bg-amber-50/30 hover:bg-amber-50 border border-amber-100 rounded-lg transition-all" title="Edit"><i class="bi bi-pencil"></i></button>
                                            <form action="{{ route('admin.articles.destroy', $art['id']) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-500 bg-red-50/30 hover:bg-red-50 border border-red-100 rounded-lg transition-all" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="py-10 text-center text-slate-400 font-semibold">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i class="bi bi-folder2-open text-3xl"></i>
                                            <span>No articles found in the database.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <span class="text-xs font-semibold text-slate-500">
                            Showing {{ $articlesList->firstItem() ?? 0 }} to {{ $articlesList->lastItem() ?? 0 }} of {{ number_format($articlesList->total()) }} articles
                        </span>
                        
                        <div class="flex items-center gap-1 text-xs">
                            {{ $articlesList->links('pagination::tailwind') }}
                        </div>
                    </div>
                </div>

                <!-- Right Column (Latest AI News Analysis Feed) -->
                <div class="flex flex-col gap-6 lg:col-span-1">
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col flex-1">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-slate-900">Latest AI News Analysis</h3>
                            <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 transition-all duration-150">View all</a>
                        </div>

                        <div class="space-y-4 flex-1">
                            @forelse(array_slice($formattedArticles, 0, 5) as $art)
                            <!-- Feed Item -->
                            <div class="flex gap-3 pb-3 border-b border-slate-50 last:border-0 hover:bg-slate-50/50 p-1.5 rounded-lg transition-colors cursor-pointer" onclick="openViewModal({{ json_encode($art) }})">
                                <img src="{{ $art['thumbnail'] }}" alt="art" class="w-16 h-12 object-cover rounded-lg border border-slate-100 flex-shrink-0" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?auto=format&fit=crop&q=80&w=150';">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug mb-1">{{ $art['title'] }}</h4>
                                    <div class="flex flex-wrap gap-1 mb-1">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase
                                            @if($art['sentiment'] === 'Positive') bg-emerald-50 text-emerald-600
                                            @elseif($art['sentiment'] === 'Negative') bg-red-50 text-red-650
                                            @else bg-amber-50 text-amber-600 @endif">
                                            {{ $art['sentiment'] }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase
                                            @if($art['risk_level'] === 'High') bg-red-50 text-red-650
                                            @elseif($art['risk_level'] === 'Medium') bg-amber-50 text-amber-650
                                            @else bg-emerald-50 text-emerald-600 @endif">
                                            {{ $art['risk_level'] }} Risk
                                        </span>
                                    </div>
                                    <span class="text-[9px] text-slate-400 font-semibold block">{{ $art['published_date'] }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="py-10 text-center text-slate-400 text-xs font-semibold">
                                No news analysis available.
                            </div>
                            @endforelse
                        </div>

                        <button class="w-full mt-4 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition-all duration-200 shadow-sm">
                            View all news analysis
                        </button>
                    </div>
                </div>
            </div>

            <!-- BOTTOM GRID (Category Distribution, Sentiment, publication trend) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Articles by Category -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <h3 class="text-sm font-bold text-slate-900 mb-6">Articles by Category</h3>
                    <div class="flex-1 flex flex-col justify-center">
                        <div class="flex items-center gap-6 justify-center">
                            <!-- Donut chart -->
                            <div class="relative w-36 h-36 flex items-center justify-center">
                                <div id="categoryChart" class="w-full h-full"></div>
                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-xl font-extrabold text-slate-800">{{ number_format($totalArticles) }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase leading-none">Total</span>
                                </div>
                            </div>

                            <!-- Legend details -->
                            <div class="flex-1 space-y-2">
                                @php
                                    $catColors = ['Shipping' => '#3B82F6', 'Trade' => '#10B981', 'Economy' => '#8B5CF6', 'Risk' => '#F59E0B', 'Technology' => '#06B6D4'];
                                    $idx = 0;
                                @endphp
                                @forelse($articlesByCategory as $catName => $cCount)
                                <div>
                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-600">
                                        <div class="flex items-center gap-1.5">
                                            <span class="h-2 w-2 rounded-full inline-block" style="background-color: {{ $catColors[$catName] ?? '#64748B' }}"></span>
                                            <span>{{ $catName }}</span>
                                        </div>
                                        <span>{{ $cCount }} <span class="text-slate-400 font-normal">({{ round(($cCount/max($totalArticles, 1))*100, 1) }}%)</span></span>
                                    </div>
                                </div>
                                @empty
                                <div class="text-[10px] text-slate-400 text-center py-4">No data.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sentiment Distribution -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <h3 class="text-sm font-bold text-slate-900 mb-6">Sentiment Distribution</h3>
                    <div class="flex-1 flex flex-col justify-center">
                        <div class="flex items-center gap-6 justify-center">
                            <!-- Donut chart -->
                            <div class="relative w-36 h-36 flex items-center justify-center">
                                <div id="sentimentChart" class="w-full h-full"></div>
                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-xl font-extrabold text-slate-800">{{ number_format($aiSentimentAnalyzedCount) }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase leading-none">Analyzed</span>
                                </div>
                            </div>

                            <!-- Legend details -->
                            <div class="flex-1 space-y-2">
                                <div>
                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-600">
                                        <div class="flex items-center gap-1.5">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500 inline-block"></span>
                                            <span>Positive</span>
                                        </div>
                                        <span>{{ $sentimentDistribution['Positive'] }} <span class="text-slate-400 font-normal">({{ round(($sentimentDistribution['Positive']/max($aiSentimentAnalyzedCount, 1))*100, 1) }}%)</span></span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-600">
                                        <div class="flex items-center gap-1.5">
                                            <span class="h-2 w-2 rounded-full bg-amber-500 inline-block"></span>
                                            <span>Neutral</span>
                                        </div>
                                        <span>{{ $sentimentDistribution['Neutral'] }} <span class="text-slate-400 font-normal">({{ round(($sentimentDistribution['Neutral']/max($aiSentimentAnalyzedCount, 1))*100, 1) }}%)</span></span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-600">
                                        <div class="flex items-center gap-1.5">
                                            <span class="h-2 w-2 rounded-full bg-red-500 inline-block"></span>
                                            <span>Negative</span>
                                        </div>
                                        <span>{{ $sentimentDistribution['Negative'] }} <span class="text-slate-400 font-normal">({{ round(($sentimentDistribution['Negative']/max($aiSentimentAnalyzedCount, 1))*100, 1) }}%)</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Articles Published per Month -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-bold text-slate-900">Articles Published per Month</h3>
                        <div class="relative">
                            <select class="appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-1.5 pr-8 text-xs font-semibold text-slate-655 focus:outline-none">
                                <option>This Year</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="bi bi-chevron-down text-[9px]"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 relative">
                        <div id="publishedTrendChart" class="w-full h-44"></div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- SCRIPTS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. ARTICLES BY CATEGORY DONUT
            try {
                const catData = @json(array_values($articlesByCategory));
                const catLabels = @json(array_keys($articlesByCategory));
                
                const catOpt = {
                    series: catData.length > 0 ? catData : [0],
                    chart: {
                        type: 'donut',
                        height: '100%',
                        width: '100%',
                        sparkline: { enabled: true }
                    },
                    labels: catLabels.length > 0 ? catLabels : ['No Data'],
                    colors: (function() {
                        const catColorsMap = {
                            'Logistics': '#3B82F6',
                            'Trade': '#10B981',
                            'Shipping': '#8B5CF6',
                            'Economy': '#F59E0B',
                            'Technology': '#06B6D4',
                            'Port': '#06B6D4',
                            'General': '#64748B',
                            'Others': '#94A3B8'
                        };
                        return catLabels.map(label => catColorsMap[label] || '#94A3B8');
                    })(),
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
                new ApexCharts(document.querySelector("#categoryChart"), catOpt).render();
            } catch (err) {
                console.error("Category Chart Error: ", err);
            }

            // 2. SENTIMENT DISTRIBUTION DONUT
            try {
                const sentOpt = {
                    series: [{{ $sentimentDistribution['Positive'] }}, {{ $sentimentDistribution['Neutral'] }}, {{ $sentimentDistribution['Negative'] }}],
                    chart: {
                        type: 'donut',
                        height: '100%',
                        width: '100%',
                        sparkline: { enabled: true }
                    },
                    labels: ['Positive', 'Neutral', 'Negative'],
                    colors: ['#10B981', '#F59E0B', '#EF4444'],
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
                new ApexCharts(document.querySelector("#sentimentChart"), sentOpt).render();
            } catch (err) {
                console.error("Sentiment Chart Error: ", err);
            }

            // 3. ARTICLES PUBLISHED TREND LINE (AREA)
            try {
                const trendOpt = {
                    series: [{
                        name: 'Published',
                        data: {!! $publishedTrendJson !!}
                    }],
                    chart: {
                        type: 'area',
                        height: 180,
                        toolbar: { show: false },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    colors: ['#3B82F6'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '9px',
                            colors: ['#1A56DB'],
                            fontWeight: 700
                        },
                        background: {
                            enabled: false
                        },
                        offsetY: -5
                    },
                    xaxis: {
                        categories: {!! $publishedMonthsJson !!},
                        labels: {
                            style: {
                                colors: '#64748b',
                                fontSize: '9px',
                                fontWeight: 700
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        min: 0,
                        tickAmount: 4,
                        labels: {
                            style: {
                                colors: '#64748b',
                                fontSize: '9px',
                                fontWeight: 700
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4
                    },
                    markers: {
                        size: 4,
                        strokeWidth: 2,
                        hover: { size: 6 }
                    }
                };
                new ApexCharts(document.querySelector("#publishedTrendChart"), trendOpt).render();
            } catch (err) {
                console.error("Published Trend Chart Error: ", err);
            }
        });
    </script>

    <!-- CREATE/EDIT ARTICLE MODAL -->
    <div id="articleModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeArticleModal()"></div>
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-2xl mx-4 overflow-hidden relative z-10 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-bold text-slate-900">Create New Article</h3>
                <button type="button" onclick="closeArticleModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="bi bi-x text-2xl"></i>
                </button>
            </div>
            
            <!-- Body / Form -->
            <form id="articleForm" method="POST" action="{{ route('admin.articles.store') }}" class="flex flex-col flex-1 overflow-hidden m-0">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="">
                
                <div class="p-6 overflow-y-auto space-y-4 flex-1 text-left">
                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Title</label>
                        <input type="text" id="formTitle" name="title" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Author -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Author</label>
                            <input type="text" id="formAuthor" name="author" placeholder="Staff Writer" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200">
                        </div>
                        
                        <!-- Source -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Source</label>
                            <input type="text" id="formSource" name="source" placeholder="GSC Staff" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Category -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Category</label>
                            <select id="formCategory" name="category" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200">
                                <option value="Logistics">Logistics</option>
                                <option value="Trade">Trade</option>
                                <option value="Shipping">Shipping</option>
                                <option value="Economy">Economy</option>
                                <option value="Risk">Risk</option>
                                <option value="Technology">Technology</option>
                                <option value="General">General</option>
                            </select>
                        </div>
                        
                        <!-- Country -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Country</label>
                            <select id="formCountry" name="country" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200">
                                <option value="Global">Global</option>
                                <option value="United States">United States</option>
                                <option value="Germany">Germany</option>
                                <option value="Japan">Japan</option>
                                <option value="Egypt">Egypt</option>
                                <option value="Taiwan">Taiwan</option>
                                <option value="India">India</option>
                                <option value="China">China</option>
                                <option value="Australia">Australia</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Canada">Canada</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Brazil">Brazil</option>
                                <option value="France">France</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Sentiment -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sentiment</label>
                            <select id="formSentiment" name="sentiment" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200">
                                <option value="Neutral">Neutral</option>
                                <option value="Positive">Positive</option>
                                <option value="Negative">Negative</option>
                            </select>
                        </div>

                        <!-- Risk Level -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Risk Level</label>
                            <select id="formRiskLevel" name="risk_level" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status</label>
                            <select id="formStatus" name="status" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200">
                                <option value="Draft">Draft</option>
                                <option value="Published">Published</option>
                            </select>
                        </div>
                    </div>

                    <!-- Thumbnail URL -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Thumbnail Image URL</label>
                        <input type="text" id="formThumbnail" name="thumbnail" placeholder="https://images.unsplash.com/..." class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200">
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Content</label>
                        <textarea id="formContent" name="content" required rows="5" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200 resize-none"></textarea>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="p-6 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeArticleModal()" class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition-all duration-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-brand-blue hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/10 transition-all duration-200">Save Article</button>
                </div>
            </form>
        </div>
    </div>

    <!-- VIEW ARTICLE DETAILS MODAL -->
    <div id="viewArticleModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeViewArticleModal()"></div>
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-2xl mx-4 overflow-hidden relative z-10 flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between border-t-8 border-brand-blue">
                <h3 class="text-lg font-bold text-slate-900">Article Details</h3>
                <button type="button" onclick="closeViewArticleModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="bi bi-x text-2xl"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 overflow-y-auto space-y-4 flex-1 text-left">
                <!-- Thumbnail Cover -->
                <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-100 shadow-sm relative bg-slate-50">
                    <img id="viewThumbnail" src="" alt="cover" class="w-full h-full object-cover">
                    <!-- Status Badge -->
                    <span id="viewStatus" class="absolute top-4 right-4 px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-md"></span>
                </div>

                <!-- Title -->
                <h2 id="viewTitle" class="text-xl font-bold text-slate-900 leading-snug"></h2>

                <!-- Metadata Row -->
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 font-semibold border-y border-slate-50 py-3">
                    <div class="flex items-center gap-1">
                        <i class="bi bi-person text-sm"></i>
                        <span>Author:</span> <strong id="viewAuthor" class="text-slate-800"></strong>
                    </div>
                    <span class="text-slate-200">|</span>
                    <div class="flex items-center gap-1">
                        <i class="bi bi-link-45deg text-sm"></i>
                        <span>Source:</span> <strong id="viewSource" class="text-slate-800"></strong>
                    </div>
                    <span class="text-slate-200">|</span>
                    <div class="flex items-center gap-1">
                        <i class="bi bi-calendar text-sm"></i>
                        <span id="viewDate"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Category</span>
                        <strong id="viewCategory" class="text-xs text-slate-700 block mt-1"></strong>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Country</span>
                        <strong id="viewCountry" class="text-xs text-slate-700 block mt-1"></strong>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sentiment</span>
                        <strong id="viewSentiment" class="text-xs block mt-1"></strong>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Risk Level</span>
                        <strong id="viewRiskLevel" class="text-xs block mt-1"></strong>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Content</label>
                    <p id="viewContent" class="text-slate-700 text-sm whitespace-pre-line leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100"></p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex items-center justify-end">
                <button type="button" onclick="closeViewArticleModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition-all duration-200">Close</button>
            </div>
        </div>
    </div>

    <!-- MODAL SCRIPTS -->
    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').innerText = 'Create New Article';
            document.getElementById('articleForm').action = "{{ route('admin.articles.store') }}";
            document.getElementById('methodField').value = '';
            
            // Reset fields
            document.getElementById('formTitle').value = '';
            document.getElementById('formAuthor').value = '';
            document.getElementById('formSource').value = '';
            document.getElementById('formCategory').value = 'General';
            document.getElementById('formCountry').value = 'Global';
            document.getElementById('formSentiment').value = 'Neutral';
            document.getElementById('formRiskLevel').value = 'Low';
            document.getElementById('formStatus').value = 'Draft';
            document.getElementById('formThumbnail').value = '';
            document.getElementById('formContent').value = '';
            
            document.getElementById('articleModal').classList.remove('hidden');
        }

        function openEditModal(art) {
            document.getElementById('modalTitle').innerText = 'Edit Article';
            document.getElementById('articleForm').action = "/admin/articles/" + art.id;
            document.getElementById('methodField').value = 'PUT';
            
            // Populate fields
            document.getElementById('formTitle').value = art.title;
            document.getElementById('formAuthor').value = art.author;
            document.getElementById('formSource').value = art.source;
            document.getElementById('formCategory').value = art.category;
            document.getElementById('formCountry').value = art.country;
            document.getElementById('formSentiment').value = art.sentiment;
            document.getElementById('formRiskLevel').value = art.risk_level;
            document.getElementById('formStatus').value = art.status;
            document.getElementById('formThumbnail').value = art.thumbnail;
            document.getElementById('formContent').value = art.content;
            
            document.getElementById('articleModal').classList.remove('hidden');
        }

        function closeArticleModal() {
            document.getElementById('articleModal').classList.add('hidden');
        }

        function openViewModal(art) {
            document.getElementById('viewTitle').innerText = art.title;
            document.getElementById('viewAuthor').innerText = art.author;
            document.getElementById('viewSource').innerText = art.source;
            document.getElementById('viewDate').innerText = 'Published: ' + art.published_date;
            document.getElementById('viewCategory').innerText = art.category;
            document.getElementById('viewCountry').innerText = art.flag + ' ' + art.country;
            
            // Content
            document.getElementById('viewContent').innerText = art.content;
            
            // Thumbnail
            const defaultThumb = 'https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?auto=format&fit=crop&q=80&w=150';
            document.getElementById('viewThumbnail').src = art.thumbnail ? art.thumbnail : defaultThumb;

            // Sentiment styling
            const sentEl = document.getElementById('viewSentiment');
            sentEl.innerText = art.sentiment;
            sentEl.className = 'text-xs font-bold block mt-1 ';
            if(art.sentiment === 'Positive') sentEl.classList.add('text-emerald-600');
            else if(art.sentiment === 'Negative') sentEl.classList.add('text-red-600');
            else sentEl.classList.add('text-amber-600');

            // Risk level styling
            const riskEl = document.getElementById('viewRiskLevel');
            riskEl.innerText = art.risk_level;
            riskEl.className = 'text-xs font-bold block mt-1 ';
            if(art.risk_level === 'High') riskEl.classList.add('text-red-650');
            else if(art.risk_level === 'Medium') riskEl.classList.add('text-amber-650');
            else riskEl.classList.add('text-emerald-600');

            // Status Badge
            const statusEl = document.getElementById('viewStatus');
            statusEl.innerText = art.status;
            if(art.status === 'Published') {
                statusEl.className = 'absolute top-4 right-4 px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-md bg-emerald-600';
            } else {
                statusEl.className = 'absolute top-4 right-4 px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-md bg-amber-500';
            }

            document.getElementById('viewArticleModal').classList.remove('hidden');
        }

        function closeViewArticleModal() {
            document.getElementById('viewArticleModal').classList.add('hidden');
        }
    </script>
</body>
</html>
