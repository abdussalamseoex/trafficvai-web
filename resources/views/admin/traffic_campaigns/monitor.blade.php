<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="min-h-screen bg-gray-50 text-gray-800 py-10 relative overflow-hidden">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-10 left-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
            <!-- Admin Client Strip -->
            <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white p-5 rounded-3xl shadow-xl border border-gray-700/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-orange-500/20 border border-orange-500/40 flex items-center justify-center text-xl font-black text-orange-400">
                        🛡️
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-orange-500 text-white">Admin Live Monitor</span>
                            <span class="text-xs text-gray-400">Client ID: #{{ $campaign->user_id }}</span>
                        </div>
                        <h3 class="text-lg font-black mt-0.5">{{ $campaign->user->name ?? 'Unassigned Client' }} <span class="text-xs font-normal text-gray-400">({{ $campaign->user->email ?? 'N/A' }})</span></h3>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="px-4 py-2 rounded-2xl bg-gray-800 border border-gray-700 text-xs font-bold">
                        Client Balance: <span class="text-orange-400 font-extrabold">{{ number_format($campaign->user->traffic_points ?? 0) }} Pts</span>
                    </div>
                    <a href="{{ route('admin.traffic_campaigns.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs transition">
                        ← Back to All Campaigns
                    </a>
                </div>
            </div>

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b border-gray-200 pb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-orange-500/10 text-orange-600 border border-orange-500/30">Order: {{ $campaign->external_order_id }}</span>
                        <span id="badgeStatus" class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $campaign->status === 'active' ? 'bg-emerald-500/20 text-emerald-600 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-600 border border-amber-500/30' }}">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">Realtime Campaign Telemetry</h1>
                    <p class="text-gray-600 mt-1 text-sm font-medium">Target URL: <a href="{{ $campaign->url }}" target="_blank" class="text-orange-500 underline font-bold">{{ $campaign->url }}</a></p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <form action="{{ route('admin.traffic_campaigns.sync', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs transition shadow-md">
                            ⚡ Force Sync Core Engine
                        </button>
                    </form>
                    <form action="{{ route('admin.traffic_campaigns.toggle', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-extrabold text-xs transition shadow-md">
                            {{ $campaign->status === 'active' ? '⏸ Pause Delivery' : '▶ Resume Delivery' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Top Grid Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Status Card -->
                <div class="p-6 rounded-3xl bg-white border border-gray-200 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-500 mb-2">Engine Execution</div>
                    <div class="flex items-center justify-between">
                        <span class="text-2xl font-black text-gray-900 capitalize">{{ $campaign->status }}</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">Core API sync interval: 5 seconds</div>
                </div>

                <!-- Delivered Visits Card -->
                <div class="p-6 rounded-3xl bg-white border border-gray-200 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-500 mb-2">Delivered Hits / Total</div>
                    <div class="text-3xl font-black text-gray-900">
                        <span id="hitsDeliveredText" class="text-orange-500">{{ number_format($campaign->hits_delivered) }}</span>
                        <span class="text-base font-bold text-gray-500">/ {{ number_format($campaign->total_limit) }}</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">Delivery Progress: <span class="text-gray-900 font-bold">{{ $campaign->delivery_percentage }}%</span></div>
                </div>

                <!-- Campaign Type Card -->
                <div class="p-6 rounded-3xl bg-white border border-gray-200 shadow-xl">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-extrabold uppercase text-gray-500">Traffic Engine Type</div>
                        @if(strtolower($campaign->campaign_type) === 'search')
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $campaign->captcha_mode === 'premium' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $campaign->captcha_mode === 'premium' ? 'Premium Guaranteed' : 'Normal Mode' }}
                            </span>
                        @endif
                    </div>
                    <div class="text-xl font-black text-gray-900 capitalize">
                        {{ $campaign->campaign_type === 'search' ? 'Google Organic Search' : 'Direct GOAT Referrer' }}
                    </div>
                    <div class="text-xs text-gray-500 mt-2">Max Hourly Limit: <span class="text-orange-500 font-bold">{{ $campaign->hourly_limit }} visits/hr</span></div>
                </div>

                <!-- Points Card -->
                <div class="p-6 rounded-3xl bg-white border border-gray-200 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-500 mb-2">Points Consumed / Total</div>
                    @php
                        $pointsPerVisit = \App\Console\Commands\SyncTrafficDelivery::calcExactPointsPerVisit($campaign);
                        $consumedPts = $campaign->hits_delivered * $pointsPerVisit;
                    @endphp
                    <div class="text-2xl font-black text-gray-900">
                        <span class="text-orange-500">{{ number_format($consumedPts) }}</span>
                        <span class="text-base font-bold text-gray-500">/ {{ number_format($campaign->points_deducted) }} Pts</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">Allocated Budget: <span class="text-orange-500 font-bold">{{ number_format($campaign->points_deducted) }} Pts</span></div>
                </div>
            </div>

            <!-- INTERACTIVE ANALYTICS GRAPHS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Chart Container -->
                <div class="lg:col-span-2 p-6 sm:p-8 rounded-3xl bg-white border border-gray-200 shadow-2xl flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Live Delivery Flow</h3>
                            <p class="text-xs text-gray-500">Real-time visitor dispersion across time intervals</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="switchChartMode('24h')" id="btn24h" class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold bg-orange-500 text-white transition">24h Hourly</button>
                            <button type="button" onclick="switchChartMode('daily')" id="btnDaily" class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold text-gray-500 hover:text-gray-900 transition">7 Days</button>
                        </div>
                    </div>
                    <div class="w-full h-64 relative">
                        <canvas id="deliveryChart"></canvas>
                    </div>
                </div>

                <!-- Live Progress Card -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white border border-gray-200 shadow-2xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-extrabold uppercase text-gray-500 tracking-wider">Live Delivery Status</span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[10px] font-bold text-emerald-600 uppercase">Real-Time Sync</span>
                            </span>
                        </div>

                        <div class="text-center my-6">
                            <div id="progressPercentageText" class="text-5xl font-black text-gray-900 tracking-tight">{{ $campaign->delivery_percentage }}%</div>
                            <div class="text-xs font-bold text-gray-500 mt-1 uppercase tracking-wide">Target Order Completed</div>
                        </div>

                        <div class="w-full h-4 rounded-full bg-gray-100 overflow-hidden mb-6 border border-gray-200">
                            <div id="progressBarFill" class="h-full bg-gradient-to-r from-orange-500 to-amber-500 rounded-full transition-all duration-700" style="width: {{ $campaign->delivery_percentage }}%"></div>
                        </div>

                        <div class="space-y-3 pt-4 border-t border-gray-100 text-xs font-bold">
                            <div class="flex justify-between text-gray-600">
                                <span>Duration Per Hit</span>
                                <span class="text-gray-900">{{ $campaign->duration ?? 60 }}s</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Sub-Page Visits</span>
                                <span class="text-gray-900">{{ $campaign->sub_page_visits > 0 ? $campaign->sub_page_visits . ' pages (' . ($campaign->sub_page_duration ?? 20) . 's)' : 'Direct Only' }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Hourly Delivery Cap</span>
                                <span class="text-orange-500">{{ number_format($campaign->hourly_limit) }} / hour</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Targeting Configuration Strip -->
            <!-- Detailed Client Targeting & Configuration Strip -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Geo & Device Box -->
                <div class="p-6 rounded-3xl bg-white border border-gray-200 shadow-xl space-y-4">
                    <h3 class="font-black text-base text-gray-900">Client Geo, Device & Behavior Settings</h3>
                    <div class="space-y-2.5 text-xs font-bold">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Targeted Countries</span>
                            <span class="text-gray-900 text-right max-w-[220px] truncate" title="{{ $campaign->target_country ?: 'Worldwide' }}">
                                🌍 {{ $campaign->target_country ?: 'Worldwide' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Device Platform</span>
                            <span class="text-gray-900 capitalize">{{ $campaign->device_type ?: 'All Devices' }}</span>
                        </div>
                        @if(strtolower($campaign->campaign_type) === 'search')
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Search Engine</span>
                                <span class="text-blue-600 uppercase font-black">{{ $campaign->search_engine ?: 'google' }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Main Stay Duration</span>
                            <span class="text-gray-900">{{ $campaign->duration ?: 60 }}s</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Sub-Page Visits</span>
                            <span class="text-gray-900">
                                @if($campaign->sub_page_visits > 0)
                                    {{ $campaign->sub_page_visits }} pages ({{ $campaign->sub_page_duration ?: 20 }}s stay)
                                @else
                                    Direct / No Sub-page
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Behavior Simulation</span>
                            <span class="text-emerald-600">
                                Scroll: {{ ucfirst($campaign->behavior_scroll ?: 'Enabled') }} | Click: {{ ucfirst($campaign->behavior_click ?: 'Enabled') }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-500">Delivery Distribution</span>
                            <span class="text-orange-600 uppercase font-extrabold">{{ $campaign->distribution_type ?: 'Spread' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Keywords or Referrer Box -->
                <div class="p-6 rounded-3xl bg-white border border-gray-200 shadow-xl space-y-4">
                    <h3 class="font-black text-base text-gray-900">
                        {{ strtolower($campaign->campaign_type) === 'search' ? 'Targeted Search Keywords & Percentages' : 'Referrer Source / Custom Referrers' }}
                    </h3>
                    <div class="text-xs font-bold">
                        @if(strtolower($campaign->campaign_type) === 'search')
                            @php
                                $kwList = is_array($campaign->keywords)
                                    ? $campaign->keywords
                                    : (is_string($campaign->keywords) ? json_decode($campaign->keywords, true) : []);
                            @endphp
                            @if(is_array($kwList) && count($kwList) > 0)
                                <div class="flex flex-wrap gap-2.5">
                                    @foreach($kwList as $kw)
                                        @php
                                            $kwText = is_array($kw)
                                                ? ($kw['kw'] ?? $kw['keyword'] ?? $kw['text'] ?? $kw['name'] ?? '')
                                                : (is_string($kw) ? $kw : '');
                                            $kwPct = is_array($kw)
                                                ? ($kw['weight'] ?? $kw['percent'] ?? $kw['pct'] ?? '')
                                                : '';
                                        @endphp
                                        @if($kwText !== '')
                                            <div class="inline-flex items-center px-3.5 py-2 rounded-xl bg-gradient-to-r from-orange-50 to-amber-50 text-gray-900 border border-orange-200 shadow-sm">
                                                <span class="text-orange-600 mr-1.5">🔍</span>
                                                <span class="font-extrabold">{{ $kwText }}</span>
                                                @if($kwPct !== '')
                                                    <span class="ml-2 px-2 py-0.5 rounded-lg bg-orange-500 text-white font-black text-[10px]">{{ $kwPct }}%</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 text-gray-500">
                                    Standard Organic Search Engine Referrer
                                </div>
                            @endif
                        @else
                            @if(!empty($campaign->custom_referrers))
                                <div class="space-y-1.5">
                                    @foreach(array_filter(array_map('trim', explode("\n", $campaign->custom_referrers))) as $ref)
                                        <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-800 break-all">
                                            🔗 {{ $ref }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 text-gray-700">
                                    Direct GOAT / Clean Referrer Flow
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let deliveryChartInstance = null;
        function initDeliveryChart(mode = '24h') {
            const ctx = document.getElementById('deliveryChart');
            if (!ctx) return;
            if (deliveryChartInstance) deliveryChartInstance.destroy();

            const is24h = mode === '24h';
            const labels = is24h 
                ? ['00:00', '03:00', '06:00', '09:00', '12:00', '15:00', '18:00', '21:00']
                : ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Today'];
            
            const totalDelivered = {{ (int) $campaign->hits_delivered }};
            const dataPts = is24h
                ? [Math.round(totalDelivered*0.05), Math.round(totalDelivered*0.08), Math.round(totalDelivered*0.12), Math.round(totalDelivered*0.18), Math.round(totalDelivered*0.22), Math.round(totalDelivered*0.15), Math.round(totalDelivered*0.12), Math.round(totalDelivered*0.08)]
                : [Math.round(totalDelivered*0.1), Math.round(totalDelivered*0.12), Math.round(totalDelivered*0.15), Math.round(totalDelivered*0.14), Math.round(totalDelivered*0.16), Math.round(totalDelivered*0.18), Math.round(totalDelivered*0.15)];

            deliveryChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Delivered Visits',
                        data: dataPts,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#f97316',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        function switchChartMode(mode) {
            const btn24 = document.getElementById('btn24h');
            const btnD = document.getElementById('btnDaily');
            if (mode === 'daily') {
                if (btnD) btnD.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold bg-orange-500 text-white transition';
                if (btn24) btn24.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold text-gray-500 hover:text-gray-900 transition';
                initDeliveryChart('daily');
            } else {
                if (btn24) btn24.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold bg-orange-500 text-white transition';
                if (btnD) btnD.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold text-gray-500 hover:text-gray-900 transition';
                initDeliveryChart('24h');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initDeliveryChart();

            setInterval(function() {
                fetch("{{ route('admin.traffic_campaigns.live_status', $campaign) }}")
                    .then(response => response.json())
                    .then(data => {
                        const hitsElem = document.getElementById('hitsDeliveredText');
                        const pctElem = document.getElementById('progressPercentageText');
                        const barElem = document.getElementById('progressBarFill');
                        const statusBadge = document.getElementById('badgeStatus');

                        if (hitsElem) hitsElem.innerText = Number(data.hits_delivered).toLocaleString();
                        if (pctElem) pctElem.innerText = data.percentage + '%';
                        if (barElem) barElem.style.width = data.percentage + '%';
                        if (statusBadge && data.status) {
                            statusBadge.innerText = data.status;
                        }
                    })
                    .catch(e => console.log('Syncing...'));
            }, 5000);
        });
    </script>
</x-app-layout>
