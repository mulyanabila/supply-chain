<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management - Admin Dashboard</title>

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

    <!-- ApexCharts -->
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

    @php
        $analystsCount = \App\Models\User::where('role', 'analyst')->count();
        $normalUsersCount = \App\Models\User::where('role', 'user')->count();
    @endphp

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

                    <a href="{{ route('admin.articles') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 text-sm font-medium transition-all duration-200">
                        <i class="bi bi-blockquote-left text-base"></i>
                        <span>Article Analyst</span>
                    </a>
                    
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-emerald-700 text-white font-medium text-sm transition-all duration-200 shadow-md shadow-emerald-700/20">
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
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">User Management</h2>
                <p class="text-slate-500 text-xs font-medium">Manage platform users, roles, and account activity.</p>
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
                <!-- Total Users -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-xl font-bold border border-blue-100">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Total Users</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalUsersCount }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-base leading-none"></i> 3 new this month
                        </span>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Active Users</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $activeUsersCount }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            100% of total
                        </span>
                    </div>
                </div>

                <!-- Admins -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold border border-purple-100">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">Admins</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $adminsCount }}</h3>
                        <span class="text-[11px] font-bold text-purple-600 flex items-center gap-1 mt-1">
                            {{ round(($adminsCount/max($totalUsersCount, 1))*100, 1) }}% of total
                        </span>
                    </div>
                </div>

                <!-- New Users -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all duration-200">
                    <div class="h-12 w-12 rounded-xl bg-orange-50 text-orange-650 flex items-center justify-center text-xl font-bold border border-orange-100">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block">New Users This Month</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $newUsersCount }}</h3>
                        <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-base leading-none"></i> {{ $newUsersCount }} from last month
                        </span>
                    </div>
                </div>
            </div>

            <!-- MIDDLE SECTION (User Table, Recent Activity & Donut Chart) -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- User Table (col-span-3) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-3">
                    <!-- Filters and Search row -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Search -->
                            <div class="relative w-64">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-search text-xs"></i>
                                </span>
                                <input type="text" placeholder="Search user by name or email..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue transition-all duration-200 text-slate-700 placeholder-slate-400">
                            </div>

                            <!-- Role Dropdown -->
                            <div class="relative">
                                <select class="appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 pr-8 text-xs font-semibold text-slate-655 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                    <option>All Roles</option>
                                    <option>Admin</option>
                                    <option>Analyst</option>
                                    <option>User</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <i class="bi bi-chevron-down text-[8px]"></i>
                                </span>
                            </div>

                            <!-- Status Dropdown -->
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
                            <button class="bg-[#0f766e] hover:bg-[#0d6058] text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all duration-200">
                                <i class="bi bi-plus text-base leading-none"></i>
                                <span>Add User</span>
                            </button>
                            <button class="p-2 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl transition-all duration-200" title="Export CSV">
                                <i class="bi bi-download text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3 pl-4">#</th>
                                    <th class="py-3">User</th>
                                    <th class="py-3">Email</th>
                                    <th class="py-3">Role</th>
                                    <th class="py-3">Country</th>
                                    <th class="py-3">Last Login</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Created Date</th>
                                    <th class="py-3 text-center pr-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs font-semibold text-slate-700">
                                @foreach($usersList as $user)
                                <tr class="hover:bg-slate-50/50 transition-all duration-150">
                                    <td class="py-3 pl-4 font-bold text-slate-400">{{ $user['id'] }}</td>
                                    <td class="py-3 flex items-center gap-3">
                                        <!-- Color Avatar -->
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center font-bold text-white shadow-sm border border-slate-100
                                            @if($user['role'] === 'Admin') bg-purple-500
                                            @elseif($user['role'] === 'Analyst') bg-blue-500
                                            @else bg-emerald-500 @endif">
                                            {{ substr($user['name'], 0, 1) }}
                                        </div>
                                        <span class="font-bold text-slate-800">{{ $user['name'] }}</span>
                                    </td>
                                    <td class="py-3 text-slate-550">{{ $user['email'] }}</td>
                                    <td class="py-3">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold inline-block border
                                            @if($user['role'] === 'Admin') bg-purple-50 text-purple-700 border-purple-100
                                            @elseif($user['role'] === 'Analyst') bg-blue-50 text-blue-700 border-blue-100
                                            @else bg-emerald-50 text-emerald-700 border-emerald-100 @endif">
                                            {{ $user['role'] }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="flex items-center gap-1.5 font-medium">
                                            <span>{{ $user['flag'] }}</span>
                                            <span>{{ $user['country'] }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3 text-slate-500 font-medium">{{ $user['last_login'] }}</td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold inline-block
                                            @if($user['status'] === 'Active') bg-emerald-50 text-emerald-700
                                            @else bg-rose-50 text-rose-700 @endif">
                                            {{ $user['status'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-slate-500 font-medium">{{ $user['created_date'] }}</td>
                                    <td class="py-3 text-center pr-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button class="p-1 text-slate-400 hover:text-slate-700 transition-all duration-150" title="View details"><i class="bi bi-eye"></i></button>
                                            <button class="p-1 text-slate-400 hover:text-blue-600 transition-all duration-150" title="Edit user"><i class="bi bi-pencil"></i></button>
                                            <button class="p-1 text-slate-400 hover:text-red-600 transition-all duration-150" title="Delete user"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <span class="text-xs font-semibold text-slate-500">Showing 1 to {{ count($usersList) }} of {{ $totalUsersCount }} users</span>
                        
                        <div class="flex items-center gap-1 text-xs">
                            <button class="h-8 px-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-all duration-150"><i class="bi bi-chevron-double-left"></i></button>
                            <button class="h-8 px-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-all duration-150"><i class="bi bi-chevron-left"></i></button>
                            <button class="h-8 w-8 bg-brand-blue text-white rounded-lg font-bold">1</button>
                            <button class="h-8 px-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-all duration-150"><i class="bi bi-chevron-right"></i></button>
                            <button class="h-8 px-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-all duration-150"><i class="bi bi-chevron-double-right"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Right Column ( Recent Activity & User Role Overview ) -->
                <div class="flex flex-col gap-6 lg:col-span-1">
                    <!-- Recent Activity -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col flex-1">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-sm font-bold text-slate-900">Recent Activity</h3>
                            <a href="#" class="text-xs font-bold text-brand-blue hover:text-blue-700 transition-all duration-150">View all</a>
                        </div>

                        <div class="space-y-5 flex-1">
                            @foreach($recentActivities as $act)
                            <div class="flex gap-3">
                                <div class="h-8 w-8 rounded-full border flex items-center justify-center flex-shrink-0 mt-0.5
                                    @if($act['type'] === 'register') bg-emerald-50 text-emerald-600 border-emerald-100
                                    @else bg-blue-50 text-brand-blue border-blue-100 @endif">
                                    <i class="bi @if($act['type'] === 'register') bi-person-plus-fill @else bi-box-arrow-in-right @endif text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-xs font-bold text-slate-800 truncate">{{ $act['title'] }}</h4>
                                        <span class="text-[9px] font-semibold text-slate-400 whitespace-nowrap">{{ $act['time'] }}</span>
                                    </div>
                                    <p class="text-[11px] font-semibold text-slate-505 mt-0.5">{{ $act['name'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <button class="w-full py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition-all duration-200">
                                View all activity
                            </button>
                        </div>
                    </div>

                    <!-- User Role Overview (Mini Donut) -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                        <h3 class="text-sm font-bold text-slate-900 mb-4">User Role Overview</h3>

                        <div class="flex items-center gap-4 justify-center">
                            <!-- Donut chart -->
                            <div class="relative w-28 h-28 flex items-center justify-center">
                                <div id="roleOverviewChart" class="w-full h-full"></div>
                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-lg font-extrabold text-slate-800">{{ $totalUsersCount }}</span>
                                    <span class="text-[8px] font-semibold text-slate-400 leading-none uppercase">Total</span>
                                </div>
                            </div>

                            <!-- Legend details -->
                            <div class="flex-1 space-y-2">
                                <div>
                                    <div class="flex items-center justify-between gap-1.5 text-[10px] font-bold text-slate-600">
                                        <div class="flex items-center gap-1">
                                            <span class="h-2 w-2 rounded-full bg-purple-500 inline-block"></span>
                                            <span>Admin</span>
                                        </div>
                                        <span>{{ $adminsCount }} <span class="text-slate-400 font-normal">({{ round(($adminsCount/max($totalUsersCount, 1))*100, 1) }}%)</span></span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between gap-1.5 text-[10px] font-bold text-slate-600">
                                        <div class="flex items-center gap-1">
                                            <span class="h-2 w-2 rounded-full bg-blue-500 inline-block"></span>
                                            <span>Analyst</span>
                                        </div>
                                        <span>{{ $analystsCount }} <span class="text-slate-400 font-normal">({{ round(($analystsCount/max($totalUsersCount, 1))*100, 1) }}%)</span></span>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between gap-1.5 text-[10px] font-bold text-slate-600">
                                        <div class="flex items-center gap-1">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500 inline-block"></span>
                                            <span>User</span>
                                        </div>
                                        <span>{{ $normalUsersCount }} <span class="text-slate-400 font-normal">({{ round(($normalUsersCount/max($totalUsersCount, 1))*100, 1) }}%)</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM SECTION (User Registration Trend, Users by Role) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Registration Trend (col-span-2) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-bold text-slate-900">User Registration Trend</h3>
                        <div class="relative inline-block text-left">
                            <select class="appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-1.5 pr-8 text-xs font-semibold text-slate-655 focus:outline-none focus:ring-1 focus:ring-brand-blue/30 focus:border-brand-blue">
                                <option>7 Days</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="bi bi-chevron-down text-[9px]"></i>
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 relative">
                        <div id="registrationTrendChart" class="w-full h-64"></div>
                    </div>
                </div>

                <!-- Users by Role (Large Donut, col-span-1) -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col lg:col-span-1">
                    <h3 class="text-sm font-bold text-slate-900 mb-6">Users by Role</h3>

                    <div class="flex-1 flex flex-col justify-center">
                        <div class="flex items-center gap-8 justify-center">
                            <!-- Large donut chart -->
                            <div class="relative w-40 h-40 flex items-center justify-center">
                                <div id="usersByRoleChart" class="w-full h-full"></div>
                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-2xl font-extrabold text-slate-800">{{ $totalUsersCount }}</span>
                                    <span class="text-xs font-bold text-slate-400 uppercase leading-none">Total Users</span>
                                </div>
                            </div>

                            <!-- Legend details -->
                            <div class="flex-1 space-y-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full bg-purple-500 inline-block"></span>
                                        <span class="text-xs font-bold text-slate-600">Admin</span>
                                    </div>
                                    <p class="text-sm font-extrabold pl-5 text-slate-850">{{ $adminsCount }} <span class="text-slate-400 font-normal">({{ round(($adminsCount/max($totalUsersCount, 1))*100, 1) }}%)</span></p>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full bg-blue-500 inline-block"></span>
                                        <span class="text-xs font-bold text-slate-600">Analyst</span>
                                    </div>
                                    <p class="text-sm font-extrabold pl-5 text-slate-850">{{ $analystsCount }} <span class="text-slate-400 font-normal">({{ round(($analystsCount/max($totalUsersCount, 1))*100, 1) }}%)</span></p>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full bg-emerald-500 inline-block"></span>
                                        <span class="text-xs font-bold text-slate-600">User</span>
                                    </div>
                                    <p class="text-sm font-extrabold pl-5 text-slate-850">{{ $normalUsersCount }} <span class="text-slate-400 font-normal">({{ round(($normalUsersCount/max($totalUsersCount, 1))*100, 1) }}%)</span></p>
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
            // 1. MINI ROLE OVERVIEW CHART
            try {
                const miniOpt = {
                    series: [{{ $adminsCount }}, {{ $analystsCount }}, {{ $normalUsersCount }}],
                    chart: {
                        type: 'donut',
                        height: '100%',
                        width: '100%',
                        sparkline: {
                            enabled: true
                        }
                    },
                    labels: ['Admin', 'Analyst', 'User'],
                    colors: ['#A855F7', '#3B82F6', '#10B981'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '78%',
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
                                return val + " Users";
                            }
                        }
                    }
                };
                new ApexCharts(document.querySelector("#roleOverviewChart"), miniOpt).render();
            } catch (err) {
                console.error("Mini Chart Error: ", err);
            }

            // 2. LARGE USERS BY ROLE CHART
            try {
                const largeOpt = {
                    series: [{{ $adminsCount }}, {{ $analystsCount }}, {{ $normalUsersCount }}],
                    chart: {
                        type: 'donut',
                        height: '100%',
                        width: '100%',
                        sparkline: {
                            enabled: true
                        }
                    },
                    labels: ['Admin', 'Analyst', 'User'],
                    colors: ['#A855F7', '#3B82F6', '#10B981'],
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
                                return val + " Users";
                            }
                        }
                    }
                };
                new ApexCharts(document.querySelector("#usersByRoleChart"), largeOpt).render();
            } catch (err) {
                console.error("Large Chart Error: ", err);
            }

            // 3. REGISTRATION TREND LINE CHART
            try {
                const trendOpt = {
                    series: [
                        {
                            name: "New users",
                            data: {!! $regTrendCounts !!}
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
                        width: 3,
                        colors: ['#1A56DB']
                    },
                    colors: ['#1A56DB'],
                    xaxis: {
                        categories: {!! $regTrendDates !!},
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
                        max: 5,
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
                        colors: ['#1A56DB'],
                        hover: {
                            size: 6
                        }
                    },
                    legend: {
                        show: false
                    }
                };
                new ApexCharts(document.querySelector("#registrationTrendChart"), trendOpt).render();
            } catch (err) {
                console.error("Trend Chart Error: ", err);
            }
        });
    </script>
</body>
</html>
