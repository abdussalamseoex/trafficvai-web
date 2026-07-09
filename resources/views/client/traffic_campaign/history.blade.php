<x-app-layout>
    <div class="min-h-screen bg-gray-950 text-gray-100 py-10 relative overflow-hidden">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-10 left-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b border-gray-800 pb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/30">Ledger & History</span>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-gray-900 text-gray-300 border border-gray-800">30-Day Validity</span>
                    </div>
                    <h1 class="text-3xl font-black text-white tracking-tight">Traffic Points Wallet & Ledger</h1>
                    <p class="text-gray-300 mt-1 text-sm">Complete transaction record for Point Top-ups & Pay-As-You-Go campaign deductions.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('client.traffic_campaign.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-900 border border-gray-800 hover:bg-gray-800 text-gray-200 font-bold text-sm transition">
                        All Campaigns
                    </a>
                    <a href="{{ route('client.traffic_campaign.topup') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white font-extrabold text-sm shadow-lg shadow-orange-500/20 transition">
                        ⚡ Top Up Points
                    </a>
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-white font-bold text-sm transition">
                        + New Campaign
                    </a>
                </div>
            </div>

            <!-- Balance Summary Banner -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="p-6 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-400 mb-2">Available Traffic Points</div>
                    <div class="text-3xl font-black text-orange-400">{{ number_format($pointsBalance) }} <span class="text-sm font-bold text-gray-400">Pts</span></div>
                    <div class="text-xs text-gray-400 mt-2">Ready for Pay-As-You-Go delivery</div>
                </div>

                <div class="p-6 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-400 mb-2">Deduction Model</div>
                    <div class="text-xl font-black text-white">Pay-As-You-Go Active</div>
                    <div class="text-xs text-emerald-400 mt-2">● Only charges per delivered visit</div>
                </div>

                <div class="p-6 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-400 mb-2">Point Expiry Guarantee</div>
                    <div class="text-xl font-black text-white">30 Days Rolling</div>
                    <div class="text-xs text-gray-400 mt-2">Extended automatically on new top-up</div>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="p-8 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-2xl space-y-6">
                <h3 class="text-xl font-black text-white">Transaction & Usage Ledger</h3>

                @if($logs->isEmpty())
                    <div class="text-center py-12 text-gray-400">
                        <p class="text-base font-bold">No point transactions recorded yet.</p>
                        <p class="text-xs mt-1">Top up points or launch a Pay-As-You-Go campaign to start seeing ledger activity.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-gray-800">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-800 text-xs font-extrabold uppercase tracking-wider text-gray-400 bg-gray-950/70">
                                    <th class="p-4">Date & Time</th>
                                    <th class="p-4">Transaction Type</th>
                                    <th class="p-4">Amount</th>
                                    <th class="p-4">Description</th>
                                    <th class="p-4 text-right">Validity / Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800/60 text-sm">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-gray-800/30 transition">
                                        <td class="p-4 font-bold text-gray-300 text-xs">
                                            {{ $log->created_at ? $log->created_at->format('M d, Y h:i A') : 'N/A' }}
                                        </td>
                                        <td class="p-4">
                                            @if($log->type === 'purchase')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                    ➕ Point Top-up
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-orange-500/10 text-orange-400 border border-orange-500/20">
                                                    🚀 Campaign Usage
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 font-black {{ $log->points >= 0 ? 'text-emerald-400' : 'text-orange-400' }}">
                                            {{ $log->points >= 0 ? '+' : '' }}{{ number_format($log->points) }} Pts
                                        </td>
                                        <td class="p-4 text-gray-300 font-medium">
                                            {{ $log->description }}
                                        </td>
                                        <td class="p-4 text-right">
                                            <span class="text-xs font-bold text-gray-400">
                                                {{ $log->created_at ? 'Valid till ' . $log->created_at->addDays(30)->format('M d, Y') : '30 Days' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
