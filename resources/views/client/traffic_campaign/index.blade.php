<x-app-layout>
    <div class="min-h-screen bg-gray-50 text-gray-800 py-12 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header -->
            <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 border-b border-gray-200 pb-8">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">My Traffic Campaigns</h1>
                    <p class="text-gray-500 mt-2 text-sm sm:text-base">Real-time status and delivery logs for your active automated traffic orders.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('client.traffic_campaign.history') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-sm transition">
                        📜 Points Ledger
                    </a>
                    <a href="{{ route('client.traffic_campaign.topup') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-orange-500 font-bold text-sm transition">
                        ⚡ Top Up
                    </a>
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm shadow-lg shadow-orange-500/25 transition">
                        + Launch New Campaign
                    </a>
                </div>
            </div>

            @if($campaigns->isEmpty())
                <div class="p-12 text-center rounded-3xl bg-white border border-gray-200 shadow-sm">
                    <div class="text-4xl mb-4">🚀</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Active Traffic Campaigns Yet</h3>
                    <p class="text-gray-500 text-sm max-w-md mx-auto mb-6">You haven't launched an interactive direct or Google search traffic campaign yet.</p>
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm shadow-md">
                        Launch Your First Campaign
                    </a>
                </div>
            @else
                <div class="overflow-x-auto rounded-3xl border border-gray-200 bg-white shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-bold uppercase tracking-wider text-gray-500 bg-gray-50">
                                <th class="p-5">#</th>
                                <th class="p-5">Order ID</th>
                                <th class="p-5">Target URL</th>
                                <th class="p-5">Type</th>
                                <th class="p-5">Delivered / Total</th>
                                <th class="p-5">Status</th>
                                <th class="p-5">Expiry</th>
                                <th class="p-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($campaigns as $camp)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-5 font-bold text-gray-500">{{ $campaigns->firstItem() + $loop->index }}</td>
                                    <td class="p-5 font-bold text-gray-900">{{ $camp->external_order_id }}</td>
                                    <td class="p-5 text-gray-600 max-w-xs truncate">{{ $camp->url }}</td>
                                    <td class="p-5 capitalize font-medium">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $camp->campaign_type === 'search' ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-orange-50 text-orange-600 border border-orange-200' }}">
                                            {{ $camp->campaign_type === 'search' ? 'Google Search' : 'Direct GOAT' }}
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-bold text-gray-900">{{ number_format($camp->hits_delivered) }} / {{ number_format($camp->total_limit) }}</div>
                                        <div class="w-24 h-1.5 rounded-full bg-gray-200 mt-1 overflow-hidden">
                                            <div class="h-full bg-orange-500" style="width: {{ $camp->delivery_percentage }}%"></div>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $camp->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ ucfirst($camp->status) }}
                                        </span>
                                    </td>
                                    <td class="p-5 text-gray-500 text-xs">
                                        {{ $camp->expires_at ? $camp->expires_at->format('M d, Y') : '30 Days' }}
                                    </td>
                                    <td class="p-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('client.traffic_campaign.toggle', $camp) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-2 rounded-xl {{ $camp->status === 'active' ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' }} font-bold text-xs transition" title="Toggle Pause/Resume">
                                                    {{ $camp->status === 'active' ? '⏸ Pause' : '▶ Resume' }}
                                                </button>
                                            </form>
                                            <a href="{{ route('client.traffic_campaign.edit', $camp) }}" class="inline-flex items-center px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition border border-gray-200" title="Edit Campaign Limits">
                                                Edit
                                            </a>
                                            <a href="{{ route('client.traffic_campaign.monitor', $camp) }}" class="inline-flex items-center px-3.5 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-600 font-bold text-xs transition border border-orange-200">
                                                Live Dash
                                            </a>
                                            <form action="{{ route('client.traffic_campaign.destroy', $camp) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-2.5 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold text-xs transition" title="Delete Campaign">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
