<x-app-layout>
    <div class="min-h-screen bg-gray-50 text-gray-800 py-10 relative overflow-hidden">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-10 left-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b border-gray-200 pb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-orange-500/10 text-orange-600 border border-orange-500/30">Ledger & History</span>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-gray-200 text-gray-800 border border-gray-300">30-Day Validity</span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Traffic Points Wallet & Ledger</h1>
                    <p class="text-gray-600 mt-1 text-sm">Complete transaction record for Point Top-ups & Pay-As-You-Go campaign deductions.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('client.traffic_campaign.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-sm transition">
                        All Campaigns
                    </a>
                    <a href="{{ route('client.traffic_campaign.topup') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-orange-400 text-gray-950 font-black text-sm shadow-lg shadow-orange-500/20 transition">
                        ⚡ Top Up Points
                    </a>
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-sm transition">
                        + New Campaign
                    </a>
                </div>
            </div>

            @php
                $userId = auth()->id();
                $userCamps = auth()->user()->trafficCampaigns ?? collect();
                $totalPointsPurchased = \App\Models\TrafficPointLog::where('user_id', $userId)->where('points', '>', 0)->sum('points');
                $totalPointsSpentLogs = abs(\App\Models\TrafficPointLog::where('user_id', $userId)->where('points', '<', 0)->sum('points'));
                $totalCampaignsCost = $userCamps->sum('points_deducted');
                $totalUsdSpent = \App\Models\TrafficPointLog::where('user_id', $userId)->where('cost_usd', '>', 0)->sum('cost_usd');
                $usdBalance = auth()->user()->balance ?? 0;

                $dailySummary = \App\Models\TrafficPointLog::where('user_id', $userId)
                    ->where('type', 'usage')
                    ->where('created_at', '>=', now()->subDays(7)->startOfDay())
                    ->selectRaw('DATE(created_at) as date_str, SUM(hits_count) as total_hits, SUM(ABS(points)) as total_pts')
                    ->groupBy('date_str')
                    ->orderByDesc('date_str')
                    ->get();
            @endphp

            <!-- Balance Summary Banner -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-3xl bg-gradient-to-br from-orange-50 to-white border border-orange-200 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-orange-600 mb-2">Available Points Balance</div>
                    <div class="text-3xl font-black text-orange-600">{{ number_format($pointsBalance) }} <span class="text-sm font-bold text-gray-500">Pts</span></div>
                    <div class="text-[11px] text-gray-500 font-bold mt-2">Ready for Pay-As-You-Go delivery</div>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-gray-200 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-indigo-600 mb-2">Total Points Bought</div>
                    <div class="text-2xl font-black text-gray-900">+{{ number_format($totalPointsPurchased) }} <span class="text-xs font-bold text-gray-400">Pts</span></div>
                    <div class="text-[11px] text-gray-500 mt-2">Traffic USD Spent: <strong class="text-indigo-600">${{ number_format($totalUsdSpent, 2) }}</strong></div>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-gray-200 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-red-500 mb-2">Total Points Consumed</div>
                    <div class="text-2xl font-black text-gray-900">-{{ number_format($totalPointsSpentLogs) }} <span class="text-xs font-bold text-gray-400">Pts</span></div>
                    <div class="text-[11px] text-gray-500 mt-2">All Campaigns Cost: <strong class="text-gray-900">{{ number_format($totalCampaignsCost) }} Pts</strong></div>
                </div>

                <div class="p-6 rounded-3xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-200 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-emerald-600 mb-2">USD Wallet Balance</div>
                    <div class="text-3xl font-black text-emerald-700">${{ number_format($usdBalance, 2) }}</div>
                    <div class="text-[11px] text-emerald-600 font-bold mt-2">● Active Wallet Account</div>
                </div>
            </div>

            <!-- Daily Traffic & Points Consumption Report (Last 7 Days) -->
            <div class="p-8 rounded-3xl bg-white border border-gray-200 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-xl font-black text-gray-900">📊 Daily Traffic & Points Consumption Report</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Transparent day-by-day record of visits delivered and points spent over the last 7 days.</p>
                    </div>
                    <span class="px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-xl border border-purple-200">Daily Breakdown</span>
                </div>

                @if($dailySummary->isEmpty())
                    <div class="py-6 text-center text-gray-500 text-xs font-bold">
                        No usage deductions recorded in the past 7 days.
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                        @foreach($dailySummary as $day)
                            @php
                                $dayDate = \Carbon\Carbon::parse($day->date_str);
                                $isToday = $dayDate->isToday();
                                $isYesterday = $dayDate->isYesterday();
                                $label = $isToday ? 'Today' : ($isYesterday ? 'Yesterday' : $dayDate->format('M d'));
                            @endphp
                            <div class="p-3.5 rounded-2xl border {{ $isToday ? 'border-orange-300 bg-orange-50/40 shadow-sm' : 'border-gray-100 bg-gray-50/50' }}">
                                <span class="text-[11px] font-black uppercase {{ $isToday ? 'text-orange-600' : ($isYesterday ? 'text-blue-600' : 'text-gray-500') }} block mb-1">
                                    {{ $label }}
                                </span>
                                <div class="text-base font-black text-gray-900">{{ number_format($day->total_hits) }} <span class="text-[10px] font-bold text-gray-400">Hits</span></div>
                                <div class="text-xs font-extrabold text-orange-500 mt-0.5">{{ number_format($day->total_pts) }} <span class="text-[10px] font-bold text-gray-400">Pts</span></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Ledger Table with Tabs -->
            <div class="p-8 rounded-3xl bg-white border border-gray-200 shadow-2xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
                    <h3 class="text-xl font-black text-gray-900">Transaction & Usage Ledger</h3>
                    
                    <div class="flex flex-wrap items-center gap-2 bg-gray-100 p-1.5 rounded-2xl">
                        <a href="{{ route('client.traffic_campaign.history', ['tab' => 'all']) }}"
                           class="px-4 py-1.5 rounded-xl text-xs transition {{ ($tab ?? 'all') === 'all' ? 'bg-white text-gray-900 shadow-sm font-black' : 'text-gray-600 hover:text-gray-900 font-bold' }}">
                            All Activity ({{ $counts['all'] ?? 0 }})
                        </a>
                        <a href="{{ route('client.traffic_campaign.history', ['tab' => 'topups']) }}"
                           class="px-4 py-1.5 rounded-xl text-xs transition {{ ($tab ?? 'all') === 'topups' ? 'bg-emerald-600 text-white shadow-sm font-black' : 'text-gray-600 hover:text-gray-900 font-bold' }}">
                            Top-up Purchases (+) ({{ $counts['topups'] ?? 0 }})
                        </a>
                        <a href="{{ route('client.traffic_campaign.history', ['tab' => 'usage']) }}"
                           class="px-4 py-1.5 rounded-xl text-xs transition {{ ($tab ?? 'all') === 'usage' ? 'bg-orange-600 text-white shadow-sm font-black' : 'text-gray-600 hover:text-gray-900 font-bold' }}">
                            Usage Deductions (-) ({{ $counts['usage'] ?? 0 }})
                        </a>
                    </div>
                </div>

                @if($logs->isEmpty())
                    <div class="text-center py-12 text-gray-500">
                        <p class="text-base font-bold">No point transactions recorded yet.</p>
                        <p class="text-xs mt-1">Top up points or launch a Pay-As-You-Go campaign to start seeing ledger activity.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-gray-200">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 text-xs font-extrabold uppercase tracking-wider text-gray-500 bg-gray-50">
                                    <th class="p-4">Date & Time</th>
                                    <th class="p-4">Transaction Type</th>
                                    <th class="p-4">Amount</th>
                                    <th class="p-4">Description</th>
                                    <th class="p-4 text-right">Validity / Cost</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @foreach($logs as $log)
                                    @php
                                        $isCredit = in_array(strtolower(trim($log->type)), ['credit', 'purchase', 'topup']) || $log->points > 0;
                                        $rowCategory = $isCredit ? 'topups' : 'usage';
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-4 font-bold text-gray-700 text-xs">
                                            {{ $log->created_at ? $log->created_at->format('M d, Y h:i A') : 'N/A' }}
                                        </td>
                                        <td class="p-4">
                                            @if($isCredit)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                                    ➕ Point Top-up
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-orange-500/10 text-orange-600 border border-orange-500/20">
                                                    🚀 Campaign Usage
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 whitespace-nowrap">
                                            <div class="font-black {{ $isCredit ? 'text-emerald-600' : 'text-orange-500' }}">
                                                {{ $isCredit ? '+' : '' }}{{ number_format($log->points) }} Pts
                                            </div>
                                            @if(($log->hits_count ?? 0) > 0)
                                                <span class="inline-flex items-center text-[11px] font-extrabold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded mt-0.5 border border-blue-100">
                                                    🎯 {{ number_format($log->hits_count) }} Visits
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-gray-700 font-medium">
                                            {{ $log->description }}
                                        </td>
                                        <td class="p-4 text-right">
                                            <span class="text-xs font-bold {{ $isCredit ? 'text-emerald-600' : 'text-gray-500' }}">
                                                {{ $log->cost_usd > 0 ? '$' . number_format($log->cost_usd, 2) : ($log->created_at ? 'Valid till ' . $log->created_at->addDays(30)->format('M d, Y') : '30 Days') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
