<x-app-layout>
    <div class="min-h-screen bg-[#0A0D14] text-gray-100 py-12 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header -->
            <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 border-b border-gray-800/80 pb-8">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">My Traffic Campaigns</h1>
                    <p class="text-gray-400 mt-2 text-sm sm:text-base">Real-time status and delivery logs for your active automated traffic orders.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('client.traffic_campaign.history') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-900 border border-gray-800 hover:bg-gray-800 text-gray-200 font-bold text-sm transition">
                        📜 Points Ledger
                    </a>
                    <a href="{{ route('client.traffic_campaign.topup') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-900 border border-gray-800 hover:bg-gray-800 text-orange-400 font-bold text-sm transition">
                        ⚡ Top Up
                    </a>
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm shadow-lg shadow-orange-500/25 transition">
                        + Launch New Campaign
                    </a>
                </div>
            </div>

            @if($campaigns->isEmpty())
                <div class="p-12 text-center rounded-3xl bg-gray-900/40 border border-gray-800/80">
                    <div class="text-4xl mb-4">🚀</div>
                    <h3 class="text-xl font-bold text-white mb-2">No Active Traffic Campaigns Yet</h3>
                    <p class="text-gray-400 text-sm max-w-md mx-auto mb-6">You haven't launched an interactive direct or Google search traffic campaign yet.</p>
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm shadow-md">
                        Launch Your First Campaign
                    </a>
                </div>
            @else
                <div class="overflow-x-auto rounded-3xl border border-gray-800/80 bg-gray-900/50 backdrop-blur-xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-800 text-xs font-bold uppercase tracking-wider text-gray-400 bg-gray-950/50">
                                <th class="p-5">Order ID</th>
                                <th class="p-5">Target URL</th>
                                <th class="p-5">Type</th>
                                <th class="p-5">Delivered / Total</th>
                                <th class="p-5">Status</th>
                                <th class="p-5">Expiry</th>
                                <th class="p-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/60 text-sm">
                            @foreach($campaigns as $camp)
                                <tr class="hover:bg-gray-800/30 transition">
                                    <td class="p-5 font-bold text-white">{{ $camp->external_order_id }}</td>
                                    <td class="p-5 text-gray-300 max-w-xs truncate">{{ $camp->url }}</td>
                                    <td class="p-5 capitalize font-medium">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $camp->campaign_type === 'search' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-orange-500/10 text-orange-400 border border-orange-500/20' }}">
                                            {{ $camp->campaign_type === 'search' ? 'Google Search' : 'Direct GOAT' }}
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-bold text-white">{{ number_format($camp->hits_delivered) }} / {{ number_format($camp->total_limit) }}</div>
                                        <div class="w-24 h-1.5 rounded-full bg-gray-950 mt-1 overflow-hidden">
                                            <div class="h-full bg-orange-500" style="width: {{ $camp->delivery_percentage }}%"></div>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $camp->status === 'active' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                                            {{ ucfirst($camp->status) }}
                                        </span>
                                    </td>
                                    <td class="p-5 text-gray-400 text-xs">
                                        {{ $camp->expires_at ? $camp->expires_at->format('M d, Y') : '30 Days' }}
                                    </td>
                                    <td class="p-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('client.traffic_campaign.edit', $camp) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white font-bold text-xs transition border border-gray-700" title="Edit Campaign Limits">
                                                Edit
                                            </a>
                                            <a href="{{ route('client.traffic_campaign.monitor', $camp) }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-orange-500/10 hover:bg-orange-500 text-orange-400 hover:text-white font-bold text-xs transition border border-orange-500/20">
                                                Live Dashboard
                                            </a>
                                            <form action="{{ route('client.traffic_campaign.destroy', $camp) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-2 rounded-xl bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/30 font-bold text-xs transition" title="Delete Campaign">
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
