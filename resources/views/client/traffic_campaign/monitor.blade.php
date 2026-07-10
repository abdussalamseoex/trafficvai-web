<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="min-h-screen bg-gray-50 text-gray-800 py-10 relative overflow-hidden">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-10 left-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b border-gray-200 pb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-orange-500/10 text-orange-600 border border-orange-500/30">Live Monitoring</span>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-gray-200 text-gray-800 border border-gray-300">{{ $campaign->external_order_id }}</span>
                        <span id="badgeStatus" class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $campaign->status === 'active' ? 'bg-emerald-500/20 text-emerald-600 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-600 border border-amber-500/30' }}">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">Campaign Analytics & Live Traffic Flow</h1>
                    <p class="text-gray-600 mt-1 text-sm font-medium">Target URL: <a href="{{ $campaign->url }}" target="_blank" class="text-orange-500 underline font-bold">{{ $campaign->url }}</a></p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('client.traffic_campaign.history') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-sm transition">
                        📜 Points Ledger
                    </a>
                    <a href="{{ route('client.traffic_campaign.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-sm transition">
                        All Campaigns
                    </a>
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-extrabold text-sm shadow-lg shadow-orange-500/20 transition">
                        + New Campaign
                    </a>
                    <form action="{{ route('client.traffic_campaign.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this campaign (Order ID: {{ $campaign->external_order_id }})?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-red-500/10 border border-red-500/30 hover:bg-red-600 text-red-600 hover:text-white font-extrabold text-sm transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete Campaign
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 font-bold">
                    {{ session('success') }}
                </div>
            @endif

            @php
                $userPoints = (int) ($campaign->user ? $campaign->user->traffic_points : auth()->user()->traffic_points);
            @endphp
            <div id="suspendedAlertBanner" class="{{ $userPoints <= 0 ? '' : 'hidden' }} p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="p-2 rounded-xl bg-amber-500/20 text-amber-600 font-bold">⚠️</span>
                    <div>
                        <h4 class="text-base font-black text-amber-600">Insufficient Traffic Points — Delivery Suspended</h4>
                        <p class="text-xs text-gray-700 mt-0.5">Your campaign remains active, but traffic delivery is paused until your Traffic Points balance is replenished.</p>
                    </div>
                </div>
                <a href="{{ route('client.traffic_campaign.topup') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white font-extrabold text-xs shadow-md hover:opacity-90 transition whitespace-nowrap">
                    Top Up Points to Auto-Resume →
                </a>
            </div>

            <!-- Top Grid Cards (High Contrast & Clear Hierarchy) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Status Card -->
                <div class="p-6 rounded-3xl bg-white border border-gray-200 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-500 mb-2">Engine Execution</div>
                    <div class="flex items-center justify-between">
                        <span class="text-2xl font-black text-gray-900 capitalize">{{ $campaign->status }}</span>
                        <form action="{{ route('client.traffic_campaign.toggle', $campaign) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-bold text-orange-500 border border-gray-200 transition">
                                {{ $campaign->status === 'active' ? '⏸ Pause' : '▶ Resume' }}
                            </button>
                        </form>
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
                    <div class="text-xs font-extrabold uppercase text-gray-500 mb-2">Traffic Engine Type</div>
                    <div class="text-xl font-black text-gray-900 capitalize">
                        {{ $campaign->campaign_type === 'search' ? 'Google Organic Search' : 'Direct GOAT Referrer' }}
                    </div>
                    <div class="text-xs text-gray-500 mt-2">Max Hourly Limit: <span class="text-orange-500 font-bold">{{ $campaign->hourly_limit }} visits/hr</span></div>
                </div>

                <!-- Validity / Points Card -->
                <div class="p-6 rounded-3xl bg-white border border-gray-200 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-500 mb-2">Points Consumed / Total</div>
                    @php
                        $consumedPts = $campaign->total_limit > 0 ? (int) round(($campaign->hits_delivered / max(1, $campaign->total_limit)) * $campaign->points_deducted) : 0;
                    @endphp
                    <div class="text-2xl font-black text-gray-900">
                        <span class="text-orange-500">{{ number_format($consumedPts) }}</span>
                        <span class="text-base font-bold text-gray-500">/ {{ number_format($campaign->points_deducted) }} Pts</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">Consumed: <span class="text-orange-500 font-bold">{{ number_format($consumedPts) }} Pts</span></div>
                </div>
            </div>

            <!-- INTERACTIVE ANALYTICS GRAPHS (Hourly & Daily Delivery Charts) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Hourly & Daily Chart Container (Takes 2 Columns) -->
                <div class="lg:col-span-2 p-6 sm:p-8 rounded-3xl bg-white border border-gray-200 shadow-2xl flex flex-col justify-between">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-black text-gray-900">Traffic Delivery Graph</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Real-time hits broken down by timeline</p>
                        </div>
                        <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-xl border border-gray-200">
                            <button type="button" onclick="switchChartTab('hourly')" id="chartBtnHourly"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold bg-orange-500 text-white transition">
                                Hourly (24h)
                            </button>
                            <button type="button" onclick="switchChartTab('daily')" id="chartBtnDaily"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold text-gray-500 hover:text-gray-900 transition">
                                Daily (14d)
                            </button>
                        </div>
                    </div>

                    <div class="h-72 w-full relative">
                        <canvas id="deliveryAnalyticsChart"></canvas>
                    </div>

                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-200 mt-6 text-center">
                        <div class="p-3 rounded-2xl bg-gray-50 border border-gray-200">
                            <div class="text-[11px] font-bold uppercase text-gray-500">Hourly Speed Limit</div>
                            <div class="text-lg font-black text-orange-500 mt-0.5">{{ number_format($campaign->hourly_limit) }} visits/hr</div>
                        </div>
                        <div class="p-3 rounded-2xl bg-gray-50 border border-gray-200">
                            <div class="text-[11px] font-bold uppercase text-gray-500">Avg Stay Duration</div>
                            <div class="text-lg font-black text-gray-900 mt-0.5">{{ $campaign->duration }}s Main</div>
                        </div>
                        <div class="p-3 rounded-2xl bg-gray-50 border border-gray-200">
                            <div class="text-[11px] font-bold uppercase text-gray-500">Sub-Page Depth</div>
                            <div class="text-lg font-black text-gray-900 mt-0.5">{{ $campaign->sub_page_toggle ? $campaign->sub_page_visits . ' pages' : 'Single Page' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Targeting & Geographic Breakdown Panel (1 Column) -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white border border-gray-200 shadow-2xl space-y-6">
                    <div>
                        <h3 class="text-xl font-black text-gray-900">Audience Distribution</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Geographic & Device Breakdown</p>
                    </div>

                    <!-- Target Country Breakdown -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm font-bold">
                            <span class="text-gray-700">Target Country</span>
                            <span class="text-orange-500 uppercase font-black">{{ $campaign->target_country }}</span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-gray-200 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-orange-500 to-amber-400 rounded-full" style="width: 100%"></div>
                        </div>
                        <p class="text-[11px] text-gray-500">100% visits strictly allocated from <span class="text-gray-900 font-bold uppercase">{{ $campaign->target_country }}</span> targeted regions.</p>
                    </div>

                    <!-- Device Breakdown -->
                    <div class="space-y-3 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm font-bold">
                            <span class="text-gray-700">Device Targeting</span>
                            <span class="text-gray-900 uppercase font-black">{{ $campaign->device_type }}</span>
                        </div>
                        @php
                            $deviceDesktopPct = $campaign->device_type === 'desktop' ? 100 : ($campaign->device_type === 'mobile' ? 0 : 55);
                            $deviceMobilePct = 100 - $deviceDesktopPct;
                        @endphp
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-3 rounded-xl bg-gray-50 border border-gray-200">
                                <div class="text-gray-500 font-bold">Desktop Visits</div>
                                <div class="text-base font-black text-gray-900 mt-0.5">{{ $deviceDesktopPct }}%</div>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 border border-gray-200">
                                <div class="text-gray-500 font-bold">Mobile Visits</div>
                                <div class="text-base font-black text-gray-900 mt-0.5">{{ $deviceMobilePct }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Referrer Source Badge -->
                    <div class="pt-4 border-t border-gray-200 space-y-2">
                        <div class="text-xs font-bold text-gray-500 uppercase">Traffic Source / Referrers</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $campaign->traffic_source ?? 'direct') as $src)
                                <span class="px-3 py-1 rounded-lg bg-orange-500/10 border border-orange-500/30 text-orange-600 font-bold text-xs">
                                    {{ strtoupper(trim($src)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- LIVE DELIVERY PROGRESS TRACK -->
            <div class="p-8 rounded-3xl bg-white border border-gray-200 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-black text-gray-900">Overall Order Fulfillment</h3>
                        <p class="text-xs text-gray-500 mt-1">Live hit counter synchronization from Real-Time Delivery Server</p>
                    </div>
                    <div class="text-3xl font-black text-orange-500" id="progressPercentageText">
                        {{ $campaign->delivery_percentage }}%
                    </div>
                </div>

                <div class="w-full h-4 rounded-full bg-gray-200 overflow-hidden border border-gray-200 p-0.5">
                    <div id="progressBarFill" class="h-full rounded-full bg-gradient-to-r from-orange-500 to-amber-400 transition-all duration-700"
                        style="width: {{ $campaign->delivery_percentage }}%"></div>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-500 mt-3 font-bold">
                    <span>Target Destination: <a href="{{ $campaign->url }}" target="_blank" class="text-orange-500 underline">{{ $campaign->url }}</a></span>
                    <span id="syncStatusText" class="text-emerald-600">● Live sync active</span>
                </div>
            </div>

            <!-- Configuration Summary Table -->
            <div class="p-8 rounded-3xl bg-white border border-gray-200 shadow-2xl">
                <h3 class="text-xl font-black text-gray-900 mb-6">Campaign Technical Parameters</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-sm">
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200">
                        <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Stay Duration per Visit</span>
                        <span class="font-extrabold text-gray-900">{{ $campaign->duration }}s Main Page</span>
                        <div class="text-xs text-gray-500 mt-0.5">+ {{ $campaign->sub_page_visits }} Sub-pages @ {{ $campaign->sub_page_duration }}s</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200">
                        <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Scrolling Behavior</span>
                        <span class="font-extrabold text-gray-900 capitalize">{{ $campaign->behavior_scroll }}</span>
                        <div class="text-xs text-gray-500 mt-0.5">Human-like random page scroll</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200">
                        <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Mouse Interaction</span>
                        <span class="font-extrabold text-gray-900 capitalize">{{ $campaign->behavior_click }}</span>
                        <div class="text-xs text-gray-500 mt-0.5">Hover & internal navigation</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200">
                        <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Target Country</span>
                        <span class="font-extrabold text-orange-500 uppercase">{{ $campaign->target_country }}</span>
                        <div class="text-xs text-gray-500 mt-0.5">Targeted real audience</div>
                    </div>

                    @if($campaign->campaign_type === 'search')
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200">
                            <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Search Engine</span>
                            <span class="font-extrabold text-gray-900 uppercase">{{ $campaign->search_engine }}</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200">
                            <span class="block text-xs font-bold uppercase text-gray-500 mb-1">SERP Crawl Depth</span>
                            <span class="font-extrabold text-gray-900">Up to Page {{ $campaign->max_page }}</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200">
                            <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Quality Mode</span>
                            <span class="font-extrabold text-orange-500 capitalize">{{ $campaign->captcha_mode }} Mode</span>
                        </div>
                    @endif
                </div>

                @if(!empty($campaign->keywords))
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <span class="block text-xs font-bold uppercase text-gray-500 mb-3">Allocated Keywords</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($campaign->keywords as $kw)
                            <span class="px-3.5 py-1.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-800">{{ is_array($kw) ? ($kw['kw'] . ' (' . ($kw['weight'] ?? 100) . '%)') : $kw }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- DYNAMIC CHART.JS & LIVE SYNC SCRIPT -->
    <script>
        let myChart = null;
        let activeChartMode = 'hourly';
        const totalDeliveredHits = {{ (int) $campaign->hits_delivered }};
        const hourlySpeedLimit = {{ (int) $campaign->hourly_limit }};

        async function initDeliveryChart(viewMode = '24h') {
            const ctx = document.getElementById('deliveryAnalyticsChart');
            if (!ctx) return;

            let labels = ['0:00', '4:00', '8:00', '12:00', '16:00', '20:00'];
            let hitData = [0, 0, 0, 0, 0, 0];

            try {
                const res = await fetch(`{{ route('client.traffic_campaign.live_graph', $campaign->id) }}?view=${viewMode}`);
                if (res.ok) {
                    const json = await res.json();
                    if (json && json.labels && json.data) {
                        labels = json.labels;
                        hitData = json.data;
                    }
                }
            } catch (e) {
                console.warn('Could not fetch graph data, using fallback');
            }

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0, 'rgba(249, 115, 22, 0.45)');
            gradient.addColorStop(1, 'rgba(249, 115, 22, 0.0)');

            if (myChart) myChart.destroy();

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Traffic Hits Delivered',
                        data: hitData,
                        borderColor: '#f97316',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#f97316',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#ffffff',
                            titleColor: '#1f2937',
                            bodyColor: '#f97316',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 12
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: { color: '#6b7280', font: { weight: 'bold', size: 11 } }
                        },
                        y: {
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: { color: '#6b7280', font: { weight: 'bold', size: 11 } }
                        }
                    }
                }
            });
        }

        function switchChartTab(mode) {
            activeChartMode = mode;
            const btnH = document.getElementById('chartBtnHourly');
            const btnD = document.getElementById('chartBtnDaily');

            if (mode === 'daily') {
                btnD.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold bg-orange-500 text-white transition';
                btnH.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold text-gray-500 hover:text-gray-900 transition';
                initDeliveryChart('daily');
            } else {
                btnH.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold bg-orange-500 text-white transition';
                btnD.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold text-gray-500 hover:text-gray-900 transition';
                initDeliveryChart('24h');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initDeliveryChart();

            // Auto poll live status every 5 seconds
            setInterval(function() {
                fetch("{{ route('client.traffic_campaign.live_status', $campaign) }}")
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
                        const alertBanner = document.getElementById('suspendedAlertBanner');
                        if (alertBanner) {
                            if (data.delivery_suspended) {
                                alertBanner.classList.remove('hidden');
                            } else {
                                alertBanner.classList.add('hidden');
                            }
                        }
                    })
                    .catch(e => console.log('Syncing next cycle...'));
            }, 5000);
        });
    </script>
</x-app-layout>
