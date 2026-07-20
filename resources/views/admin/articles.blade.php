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
                <!-- Breadcrumbs -->
                <div class="hidden md:flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-600">Dashboard</a>
                    <span>/</span>
                    <span>Content</span>
                    <span>/</span>
                    <span class="text-brand-blue">Article Analyst</span>
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
                    
                    <!-- Filters form -->
                    <form method="GET" action="{{ route('admin.articles') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Search -->
                            <div class="relative w-64">
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
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2">
                            <button type="button" class="bg-slate-50 hover:bg-slate-100 text-slate-700 px-3.5 py-2 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-200">
                                <i class="bi bi-cloud-arrow-down text-sm"></i>
                                <span>Import News API</span>
                            </button>
                            <button type="button" class="bg-slate-50 hover:bg-slate-100 text-slate-700 px-3.5 py-2 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all duration-200">
                                <i class="bi bi-cpu text-sm"></i>
                                <span>Analyze AI</span>
                            </button>
                            <button type="button" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all duration-200">
                                <i class="bi bi-plus text-base leading-none"></i>
                                <span>Create Article</span>
                            </button>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3 pl-4 w-20">Thumbnail</th>
                                    <th class="py-3 pl-4">Article Title</th>
                                    <th class="py-3">Category</th>
                                    <th class="py-3">Country</th>
                                    <th class="py-3">Author</th>
                                    <th class="py-3">Published Date</th>
                                    <th class="py-3">Sentiment</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 text-center pr-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs font-semibold text-slate-700">
                                @forelse($formattedArticles as $art)
                                <tr class="hover:bg-slate-50/50 transition-all duration-150">
                                    <td class="py-3 pl-4">
                                        <img src="{{ $art['thumbnail'] }}" alt="article thumb" class="w-14 h-10 object-cover rounded-lg border border-slate-100 shadow-sm">
                                    </td>
                                    <td class="py-3 pl-4 max-w-xs">
                                        <h4 class="font-bold text-slate-800 leading-snug line-clamp-1 hover:text-brand-blue cursor-pointer">{{ $art['title'] }}</h4>
                                        <p class="text-[10px] text-slate-450 font-medium line-clamp-1 mt-0.5">{{ $art['content_summary'] }}</p>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold inline-block border bg-blue-50 text-blue-750 border-blue-100">
                                            {{ $art['category'] }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="flex items-center gap-1 font-bold text-slate-600">
                                            <span>{{ $art['flag'] }}</span>
                                            <span>{{ $art['country'] }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3 text-slate-500 font-medium leading-tight">
                                        {{ $art['author'] }}
                                    </td>
                                    <td class="py-3 text-slate-450 font-medium">{{ $art['published_date'] }}</td>
                                    <td class="py-3">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold inline-block border
                                            @if($art['sentiment'] === 'Positive') bg-emerald-50/50 text-emerald-700 border-emerald-100
                                            @elseif($art['sentiment'] === 'Negative') bg-red-50/50 text-red-700 border-red-100
                                            @else bg-amber-50/50 text-amber-700 border-amber-100 @endif">
                                            {{ $art['sentiment'] }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold inline-block
                                            @if($art['status'] === 'Published') bg-emerald-50 text-emerald-700
                                            @else bg-amber-50 text-amber-700 @endif">
                                            {{ $art['status'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center pr-4">
                                        <div class="flex items-center justify-center gap-1">
                                            <button type="button" class="p-1 text-slate-400 hover:text-slate-700 transition-all duration-150"><i class="bi bi-eye"></i></button>
                                            <button type="button" class="p-1 text-slate-400 hover:text-blue-600 transition-all duration-150"><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="p-1 text-slate-400 hover:text-red-600 transition-all duration-150"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="py-10 text-center text-slate-400 font-semibold">
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

                <!-- Right Column (Latest News Analysis Feed) -->
                <div class="flex flex-col gap-6 lg:col-span-1">
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col flex-1">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-slate-900">Latest News Analysis</h3>
                            <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 transition-all duration-150">View all</a>
                        </div>

                        <div class="space-y-4 flex-1">
                            @forelse($formattedArticles as $art)
                            <!-- Feed Item -->
                            <div class="flex gap-3 pb-3 border-b border-slate-50 last:border-0">
                                <img src="{{ $art['thumbnail'] }}" alt="art" class="w-12 h-10 object-cover rounded-lg border border-slate-100">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-xs font-bold text-slate-800 truncate">{{ $art['title'] }}</h4>
                                        <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">
                                            @if($art['sentiment'] === 'Positive')
                                                <i class="bi bi-arrow-up-right text-emerald-500 font-extrabold text-xs"></i>
                                            @elseif($art['sentiment'] === 'Negative')
                                                <i class="bi bi-arrow-down-right text-red-500 font-extrabold text-xs"></i>
                                            @else
                                                <i class="bi bi-arrow-right text-amber-500 font-extrabold text-xs"></i>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">{{ $art['country'] }}</span>
                                        <span class="h-1 w-1 bg-slate-200 rounded-full inline-block"></span>
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase
                                            @if($art['sentiment'] === 'Positive') bg-emerald-50 text-emerald-600
                                            @elseif($art['sentiment'] === 'Negative') bg-red-50 text-red-600
                                            @else bg-amber-50 text-amber-600 @endif">
                                            {{ $art['sentiment'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="py-10 text-center text-slate-400 text-xs font-semibold">
                                No news analysis available.
                            </div>
                            @endforelse
                        </div>

                        <button class="w-full mt-4 py-2 border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl transition-all duration-200">
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
                    colors: ['#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#06B6D4'],
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

            // 3. ARTICLES PUBLISHED TREND LINE
            try {
                const trendOpt = {
                    series: [{
                        name: 'Published',
                        data: {!! $publishedTrendJson !!}
                    }],
                    chart: {
                        type: 'line',
                        height: 180,
                        toolbar: { show: false },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    colors: ['#1A56DB'],
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
</body>
</html>
