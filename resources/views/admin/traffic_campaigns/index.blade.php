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
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Total Orders</span>
                    <span class="text-2xl font-black text-gray-900">{{ number_format($stats['total'] ?? 0) }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block mb-1">Active Running</span>
                    <span class="text-2xl font-black text-emerald-700">{{ number_format($stats['active'] ?? 0) }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block mb-1">Paused</span>
                    <span class="text-2xl font-black text-amber-700">{{ number_format($stats['paused'] ?? 0) }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block mb-1">Total Delivered Hits</span>
                    <span class="text-2xl font-black text-blue-700">{{ number_format($stats['total_hits'] ?? 0) }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-orange-600 uppercase tracking-wider block mb-1">Points Allocated</span>
                    <span class="text-2xl font-black text-orange-700">{{ number_format($stats['total_points'] ?? 0) }}</span>
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
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-500">
                                    <th class="p-5">Order ID / Client</th>
                                    <th class="p-5">Target URL</th>
                                    <th class="p-5">Engine Type</th>
                                    <th class="p-5">Delivery Progress</th>
                                    <th class="p-5">Points Deducted</th>
                                    <th class="p-5">Status</th>
                                    <th class="p-5">30-Day Expiry</th>
                                    <th class="p-5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($campaigns as $camp)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="p-5">
                                            <div class="font-black text-gray-900">{{ $camp->external_order_id }}</div>
                                            <div class="text-xs text-gray-500">{{ $camp->user->name ?? 'N/A' }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $camp->user->email ?? '' }}</div>
                                        </td>
                                        <td class="p-5 max-w-xs truncate">
                                            <a href="{{ $camp->url }}" target="_blank" class="text-blue-600 hover:underline font-medium">{{ $camp->url }}</a>
                                            <div class="text-xs text-gray-400 mt-0.5">Rate: {{ $camp->hourly_limit }}/hr | {{ $camp->points_deducted }} pts</div>
                                        </td>
                                        <td class="p-5">
                                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold uppercase {{ $camp->campaign_type === 'search' ? 'bg-blue-50 text-blue-700' : 'bg-orange-50 text-orange-700' }}">
                                                {{ $camp->campaign_type === 'search' ? 'Google Search' : 'Direct GOAT' }}
                                            </span>
                                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 uppercase">{{ $camp->device_type ?? 'All' }}</span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 uppercase">{{ $camp->target_country ?? 'All' }}</span>
                                            </div>
                                        </td>
                                        <td class="p-5">
                                            <div class="font-bold text-gray-900">{{ number_format($camp->hits_delivered) }} / {{ number_format($camp->total_limit) }}</div>
                                            <div class="w-28 h-2 rounded-full bg-gray-100 mt-1.5 overflow-hidden">
                                                <div class="h-full bg-orange-500" style="width: {{ $camp->delivery_percentage }}%"></div>
                                            </div>
                                        </td>
                                        <td class="p-5">
                                            <span class="inline-flex items-center px-3 py-1 rounded-xl bg-orange-50 text-orange-700 font-extrabold text-xs">
                                                {{ number_format($camp->points_deducted) }} Pts
                                            </span>
                                        </td>
                                        <td class="p-5">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $camp->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ ucfirst($camp->status) }}
                                            </span>
                                        </td>
                                        <td class="p-5 text-xs text-gray-500 font-medium">
                                            {{ $camp->expires_at ? $camp->expires_at->format('M d, Y') : '30 Days' }}
                                        </td>
                                        <td class="p-5 text-right space-x-2">
                                            <form action="{{ route('admin.traffic_campaigns.sync', $camp) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs hover:bg-blue-100 transition" title="Sync from Core Engine">
                                                    Sync
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.traffic_campaigns.toggle', $camp) }}" method="POST" class="inline">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 font-bold text-xs hover:bg-gray-200 transition">
                                                    {{ $camp->status === 'active' ? 'Pause' : 'Resume' }}
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.traffic_campaigns.destroy', $camp) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-red-100 text-red-700 font-bold text-xs hover:bg-red-200 transition" title="Delete Campaign">
                                                    Delete
                                                </button>
                                            </form>
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
