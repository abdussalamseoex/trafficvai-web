<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-500/10 text-orange-600 border border-orange-500/20">Core Automation Engine</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-600 border border-blue-500/20">surf.abguestpost.net</span>
                </div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Website Traffic Engine Management') }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.traffic_campaigns.index') }}" class="px-4 py-2 rounded-xl bg-gray-900 text-white font-bold text-xs shadow hover:bg-gray-800 transition">
                    All Campaigns
                </a>
                <a href="{{ route('admin.traffic_campaigns.active') }}" class="px-4 py-2 rounded-xl bg-orange-500 text-white font-bold text-xs shadow hover:bg-orange-600 transition">
                    Active Running
                </a>
                <a href="{{ route('admin.traffic_campaigns.clients') }}" class="px-4 py-2 rounded-xl bg-purple-600 text-white font-bold text-xs shadow hover:bg-purple-700 transition">
                    👥 Clients Overview
                </a>
                <a href="{{ route('admin.traffic_campaigns.ledger') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs shadow hover:bg-blue-700 transition">
                    Points Ledger & Topups
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-medium text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-red-50 text-red-800 border border-red-200 font-medium text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Stat Cards -->
            @if(request('search'))
                @if(isset($filterClient) && $filterClient)
                    <div class="mb-4 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="bg-indigo-50 border-b border-indigo-100 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-black text-gray-900">🔍 {{ $filterClient->name }}</h3>
                                <p class="text-sm font-bold text-indigo-600">{{ $filterClient->email }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.users.show', $filterClient->id) }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl shadow-sm hover:bg-indigo-700 transition">View Full Profile</a>
                                <a href="{{ route('admin.traffic_campaigns.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-200 text-xs font-bold rounded-xl shadow-sm hover:bg-gray-50 transition">Clear Filter ✖</a>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100 bg-gray-50/50">
                            <div class="p-4 text-center">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">USD Wallet Balance</div>
                                <div class="text-lg font-black text-emerald-600">${{ number_format($filterClient->balance ?? 0, 2) }}</div>
                                <div class="text-[10px] text-gray-500 font-bold mt-1">Traffic USD Spend: ${{ number_format($filterClient->total_usd_spent ?? 0, 2) }}</div>
                            </div>
                            <div class="p-4 text-center">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Total Points Bought</div>
                                <div class="text-lg font-black text-indigo-600">+{{ number_format($filterClient->total_points_purchased ?? 0) }}</div>
                            </div>
                            <div class="p-4 text-center">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1">Total Points Spent</div>
                                <div class="text-lg font-black text-red-500">-{{ number_format($filterClient->total_points_spent_logs ?? 0) }}</div>
                                <div class="text-[10px] text-gray-500 font-bold mt-1">All Campaigns Cost: {{ number_format($filterClient->total_campaign_points_spent ?? 0) }} Pts</div>
                            </div>
                            <div class="p-4 text-center bg-orange-50/30">
                                <div class="text-[10px] font-black text-orange-400 uppercase tracking-wider mb-1">Available Points</div>
                                <div class="text-2xl font-black text-orange-600">{{ number_format($filterClient->traffic_points ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-4 flex items-center justify-between bg-purple-50 border border-purple-200 p-4 rounded-2xl text-purple-950 font-medium text-sm">
                        <span>🔍 Displaying dynamically filtered metrics for: <strong>{{ request('search') }}</strong></span>
                        <a href="{{ route('admin.traffic_campaigns.index') }}" class="text-xs font-bold text-purple-700 hover:underline">Clear Filter ✖</a>
                    </div>
                @endif
            @endif

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Total Orders</span>
                    <span class="text-2xl font-black text-gray-900">{{ number_format($stats['total'] ?? 0) }}</span>
                    <span class="text-[11px] text-gray-500 font-semibold block mt-1">Direct: {{ number_format($stats['direct_hits'] ?? 0) }} | Search: {{ number_format($stats['search_hits'] ?? 0) }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block mb-1">Active Running</span>
                    <span class="text-2xl font-black text-emerald-700">{{ number_format($stats['active'] ?? 0) }} <span class="text-xs text-emerald-500">Live</span></span>
                    <span class="text-[11px] text-emerald-600/80 font-semibold block mt-1">Executing via automation engine</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block mb-1">Paused</span>
                    <span class="text-2xl font-black text-amber-700">{{ number_format($stats['paused'] ?? 0) }}</span>
                    <span class="text-[11px] text-amber-600/80 font-semibold block mt-1">Temporarily stopped</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block mb-1">Delivered Hits</span>
                    <span class="text-2xl font-black text-blue-700">{{ number_format($stats['total_hits'] ?? 0) }}</span>
                    <span class="text-[11px] text-blue-600/80 font-semibold block mt-1">Today: +{{ number_format($stats['today_hits'] ?? 0) }} | Yd: +{{ number_format($stats['yesterday_hits'] ?? 0) }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-orange-600 uppercase tracking-wider block mb-1">Points Allocated</span>
                    <span class="text-2xl font-black text-orange-700">{{ number_format($stats['total_points'] ?? 0) }}</span>
                    <span class="text-[11px] text-orange-600/80 font-semibold block mt-1">Today Used: {{ number_format($stats['today_points'] ?? 0) }} Pts</span>
                </div>
            </div>

            <!-- Traffic Type & Time Analytics Breakdown Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-gradient-to-br from-orange-500/10 via-amber-500/5 to-white p-4 rounded-2xl border border-orange-500/20 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-black text-orange-600 uppercase tracking-wider block">🚀 Direct GOAT Traffic</span>
                        <span class="text-lg font-black text-gray-900">{{ number_format($stats['direct_hits'] ?? 0) }} <span class="text-xs font-bold text-gray-500">Visits Delivered</span></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-gray-400 block uppercase">Allocated Budget</span>
                        <span class="text-sm font-extrabold text-orange-700">{{ number_format($stats['direct_points'] ?? 0) }} Pts</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-500/10 via-indigo-500/5 to-white p-4 rounded-2xl border border-blue-500/20 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-black text-blue-600 uppercase tracking-wider block">🔍 Google Search Traffic</span>
                        <span class="text-lg font-black text-gray-900">{{ number_format($stats['search_hits'] ?? 0) }} <span class="text-xs font-bold text-gray-500">Visits Delivered</span></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-gray-400 block uppercase">Allocated Budget</span>
                        <span class="text-sm font-extrabold text-blue-700">{{ number_format($stats['search_points'] ?? 0) }} Pts</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-500/10 via-pink-500/5 to-white p-4 rounded-2xl border border-purple-500/20 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-black text-purple-600 uppercase tracking-wider block">📅 Daily Delivery Trend</span>
                        <div class="flex items-center gap-3 mt-1">
                            <div>
                                <span class="text-[10px] font-extrabold text-gray-500 block uppercase">Today</span>
                                <span class="text-sm font-black text-purple-700">+{{ number_format($stats['today_hits'] ?? 0) }} Hits <span class="text-[10px] text-gray-500">({{ number_format($stats['today_points'] ?? 0) }} Pts)</span></span>
                            </div>
                            <div class="border-l border-purple-200 pl-2">
                                <span class="text-[10px] font-extrabold text-gray-400 block uppercase">Yesterday</span>
                                <span class="text-sm font-black text-gray-700">+{{ number_format($stats['yesterday_hits'] ?? 0) }} Hits <span class="text-[10px] text-gray-400">({{ number_format($stats['yesterday_points'] ?? 0) }} Pts)</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Bar -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mb-8">
                <form action="{{ route('admin.traffic_campaigns.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Order ID, Client Name, Email or URL..." 
                        class="flex-1 rounded-xl border-gray-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand">
                    
                    <select name="status" class="rounded-xl border-gray-200 px-4 py-2.5 text-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Running</option>
                        <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                    </select>

                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gray-900 text-white font-bold text-sm hover:bg-gray-800 transition">
                        Filter Orders
                    </button>
                </form>
            </div>

            <!-- Main Table -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                @if($campaigns->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-gray-500 font-medium">No traffic campaigns found.</p>
                    </div>
                @else
                    <div>
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-500">
                                    <th class="p-3 w-8 text-center">#</th>
                                    <th class="p-3">Order / Client</th>
                                    <th class="p-3">URL & Type</th>
                                    <th class="p-3">Delivery</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($campaigns as $camp)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="p-3 text-center text-xs text-gray-400 font-bold">{{ $campaigns->firstItem() + $loop->index }}</td>
                                        <td class="p-3">
                                            <div class="font-black text-gray-900 text-xs">{{ $camp->external_order_id }}</div>
                                            <div class="text-xs font-bold text-gray-700 mt-0.5">{{ $camp->user->name ?? 'N/A' }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $camp->user->email ?? '' }}</div>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-orange-50 text-orange-700 border border-orange-200 mt-0.5">
                                                {{ number_format($camp->user->traffic_points ?? 0) }} Pts
                                            </span>
                                        </td>
                                        <td class="p-3 max-w-[180px]">
                                            <a href="{{ $camp->url }}" target="_blank" class="text-blue-600 hover:underline font-bold block truncate text-xs" title="{{ $camp->url }}">{{ $camp->url }}</a>
                                            <div class="flex flex-wrap items-center gap-1 mt-1">
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ strtolower($camp->campaign_type) === 'search' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-orange-50 text-orange-700 border border-orange-200' }}">
                                                    {{ strtolower($camp->campaign_type) === 'search' ? 'Search' : 'Direct' }}
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-100 text-gray-600">
                                                    {{ $camp->hourly_limit }}/hr
                                                </span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-100 text-gray-600 max-w-[80px] truncate" title="{{ $camp->target_country ?: 'Worldwide' }}">
                                                    🌍 {{ Str::limit($camp->target_country ?: 'WW', 8) }}
                                                </span>
                                            </div>
                                        </td>
                                        @php
                                            $consumedPts = $camp->total_limit > 0
                                                ? (int) round(($camp->hits_delivered / max(1, $camp->total_limit)) * $camp->points_deducted)
                                                : 0;
                                        @endphp
                                        <td class="p-3 whitespace-nowrap">
                                            <div class="text-xs font-bold text-gray-900">{{ number_format($camp->hits_delivered) }}/{{ number_format($camp->total_limit) }}</div>
                                            <div class="text-[10px] text-orange-600 font-bold">{{ number_format($consumedPts) }}/{{ number_format($camp->points_deducted) }} Pts</div>
                                            <div class="w-full h-1 rounded-full bg-gray-200 mt-1 overflow-hidden max-w-[120px]">
                                                <div class="h-full bg-orange-500" style="width: {{ $camp->delivery_percentage }}%"></div>
                                            </div>
                                        </td>
                                        <td class="p-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold uppercase w-fit {{ $camp->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ ucfirst($camp->status) }}
                                            </span>
                                            <div class="text-[9px] text-gray-400 mt-0.5">{{ $camp->expires_at ? $camp->expires_at->format('M d, Y') : '30 Days' }}</div>
                                        </td>
                                        <td class="p-3 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1 flex-wrap">
                                                <a href="{{ route('admin.traffic_campaigns.edit', $camp) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs hover:bg-indigo-100 transition" title="Edit Campaign">
                                                    ✏️ Edit
                                                </a>
                                                <a href="{{ route('admin.traffic_campaigns.monitor', $camp) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-gradient-to-r from-orange-500 to-amber-500 text-white font-extrabold text-xs shadow hover:opacity-95 transition" title="Live Dashboard">
                                                    📊 Live
                                                </a>
                                                <form action="{{ route('admin.traffic_campaigns.sync', $camp) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs hover:bg-blue-100 transition">Sync</button>
                                                </form>
                                                <form action="{{ route('admin.traffic_campaigns.toggle', $camp) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs hover:bg-gray-200 transition">
                                                        {{ $camp->status === 'active' ? 'Pause' : 'Resume' }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.traffic_campaigns.destroy', $camp) }}" method="POST" class="inline" onsubmit="return confirm('Delete this campaign?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-red-100 text-red-700 font-bold text-xs hover:bg-red-200 transition">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 border-t border-gray-100">
                        {{ $campaigns->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
