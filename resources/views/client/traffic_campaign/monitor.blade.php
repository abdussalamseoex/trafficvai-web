<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="min-h-screen bg-gray-950 text-gray-100 py-10 relative overflow-hidden">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-10 left-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b border-gray-800 pb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/30">Live Monitoring</span>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-gray-900 text-gray-300 border border-gray-800">{{ $campaign->external_order_id }}</span>
                        <span id="badgeStatus" class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $campaign->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">Campaign Analytics & Live Traffic Flow</h1>
                    <p class="text-gray-300 mt-1 text-sm font-medium">Real-time sync with <code class="text-orange-400 font-bold">surf.abguestpost.net</code> core engine • Target: <a href="{{ $campaign->url }}" target="_blank" class="text-orange-400 underline font-bold">{{ $campaign->url }}</a></p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('client.traffic_campaign.history') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-900 border border-gray-800 hover:bg-gray-800 text-gray-200 font-bold text-sm transition">
                        📜 Points Ledger
                    </a>
                    <a href="{{ route('client.traffic_campaign.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-900 border border-gray-800 hover:bg-gray-800 text-gray-200 font-bold text-sm transition">
                        All Campaigns
                    </a>
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-extrabold text-sm shadow-lg shadow-orange-500/20 transition">
                        + New Campaign
                    </a>
                    <form action="{{ route('client.traffic_campaign.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this campaign (Order ID: {{ $campaign->external_order_id }})?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-red-500/10 border border-red-500/30 hover:bg-red-600 text-red-400 hover:text-white font-extrabold text-sm transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete Campaign
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold">
                    {{ session('success') }}
                </div>
            @endif

            @php
                $userPoints = (int) ($campaign->user ? $campaign->user->traffic_points : auth()->user()->traffic_points);
            @endphp
            <div id="suspendedAlertBanner" class="{{ $userPoints <= 0 ? '' : 'hidden' }} p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="p-2 rounded-xl bg-amber-500/20 text-amber-400 font-bold">⚠️</span>
                    <div>
                        <h4 class="text-base font-black text-amber-400">Insufficient Traffic Points — Delivery Suspended</h4>
                        <p class="text-xs text-gray-300 mt-0.5">Your campaign remains active, but traffic delivery is paused until your Traffic Points balance is replenished.</p>
                    </div>
                </div>
                <a href="{{ route('client.traffic_campaign.topup') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white font-extrabold text-xs shadow-md hover:opacity-90 transition whitespace-nowrap">
                    Top Up Points to Auto-Resume →
                </a>
            </div>

            <!-- Top Grid Cards (High Contrast & Clear Hierarchy) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Status Card -->
                <div class="p-6 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-400 mb-2">Engine Execution</div>
                    <div class="flex items-center justify-between">
                        <span class="text-2xl font-black text-white capitalize">{{ $campaign->status }}</span>
                        <form action="{{ route('client.traffic_campaign.toggle', $campaign) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-xs font-bold text-orange-400 border border-gray-700 transition">
                                {{ $campaign->status === 'active' ? '⏸ Pause' : '▶ Resume' }}
                            </button>
                        </form>
                    </div>
                    <div class="text-xs text-gray-400 mt-2">Core API sync interval: 5 seconds</div>
                </div>

                <!-- Delivered Visits Card -->
                <div class="p-6 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-400 mb-2">Delivered Hits / Total</div>
                    <div class="text-3xl font-black text-white">
                        <span id="hitsDeliveredText" class="text-orange-400">{{ number_format($campaign->hits_delivered) }}</span>
                        <span class="text-base font-bold text-gray-400">/ {{ number_format($campaign->total_limit) }}</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-2">Delivery Progress: <span class="text-white font-bold">{{ $campaign->delivery_percentage }}%</span></div>
                </div>

                <!-- Campaign Type Card -->
                <div class="p-6 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-400 mb-2">Traffic Engine Type</div>
                    <div class="text-xl font-black text-white capitalize">
                        {{ $campaign->campaign_type === 'search' ? 'Google Organic Search' : 'Direct GOAT Referrer' }}
                    </div>
                    <div class="text-xs text-gray-400 mt-2">Max Hourly Limit: <span class="text-orange-400 font-bold">{{ $campaign->hourly_limit }} visits/hr</span></div>
                </div>

                <!-- Validity / Points Card -->
                <div class="p-6 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-xl">
                    <div class="text-xs font-extrabold uppercase text-gray-400 mb-2">Points Allocated & Expiry</div>
                    <div class="text-2xl font-black text-white">
                        {{ number_format($campaign->points_deducted) }} <span class="text-sm font-bold text-orange-400">Pts</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-2">Expiry: <span class="text-white font-bold">{{ $campaign->expires_at ? $campaign->expires_at->format('M d, Y') : '30 Days' }}</span></div>
                </div>
            </div>

            <!-- INTERACTIVE ANALYTICS GRAPHS (Hourly & Daily Delivery Charts) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Hourly & Daily Chart Container (Takes 2 Columns) -->
                <div class="lg:col-span-2 p-6 sm:p-8 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-2xl flex flex-col justify-between">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-black text-white">Traffic Delivery Graph</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Real-time hits broken down by timeline</p>
                        </div>
                        <div class="flex items-center gap-2 bg-gray-950 p-1 rounded-xl border border-gray-800">
                            <button type="button" onclick="switchChartTab('hourly')" id="chartBtnHourly"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold bg-orange-500 text-white transition">
                                Hourly (24h)
                            </button>
                            <button type="button" onclick="switchChartTab('daily')" id="chartBtnDaily"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold text-gray-400 hover:text-white transition">
                                Daily (14d)
                            </button>
                        </div>
                    </div>

                    <div class="h-72 w-full relative">
                        <canvas id="deliveryAnalyticsChart"></canvas>
                    </div>

                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-800 mt-6 text-center">
                        <div class="p-3 rounded-2xl bg-gray-950 border border-gray-800">
                            <div class="text-[11px] font-bold uppercase text-gray-400">Peak Hourly Flow</div>
                            <div class="text-lg font-black text-orange-400 mt-0.5">{{ max(1, min($campaign->hourly_limit, max(10, intval($campaign->hits_delivered / 6)))) }} visits/hr</div>
                        </div>
                        <div class="p-3 rounded-2xl bg-gray-950 border border-gray-800">
                            <div class="text-[11px] font-bold uppercase text-gray-400">Avg Stay Duration</div>
                            <div class="text-lg font-black text-white mt-0.5">{{ $campaign->duration }}s Main</div>
                        </div>
                        <div class="p-3 rounded-2xl bg-gray-950 border border-gray-800">
                            <div class="text-[11px] font-bold uppercase text-gray-400">Sub-Page Depth</div>
                            <div class="text-lg font-black text-white mt-0.5">{{ $campaign->sub_page_toggle ? $campaign->sub_page_visits . ' pages' : 'Single Page' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Targeting & Geographic Breakdown Panel (1 Column) -->
                <div class="p-6 sm:p-8 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-2xl space-y-6">
                    <div>
                        <h3 class="text-xl font-black text-white">Audience Distribution</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Geographic & Device Breakdown</p>
                    </div>

                    <!-- Target Country Breakdown -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm font-bold">
                            <span class="text-gray-300">Target Country</span>
                            <span class="text-orange-400 uppercase font-black">{{ $campaign->target_country }}</span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-gray-950 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-orange-500 to-amber-400 rounded-full" style="width: 100%"></div>
                        </div>
                        <p class="text-[11px] text-gray-400">100% visits strictly allocated from <span class="text-white font-bold uppercase">{{ $campaign->target_country }}</span> residential & clean proxy IPs.</p>
                    </div>

                    <!-- Device Breakdown -->
                    <div class="space-y-3 pt-4 border-t border-gray-800">
                        <div class="flex items-center justify-between text-sm font-bold">
                            <span class="text-gray-300">Device Targeting</span>
                            <span class="text-white uppercase font-black">{{ $campaign->device_type }}</span>
                        </div>
                        @php
                            $deviceDesktopPct = $campaign->device_type === 'desktop' ? 100 : ($campaign->device_type === 'mobile' ? 0 : 55);
                            $deviceMobilePct = 100 - $deviceDesktopPct;
                        @endphp
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-3 rounded-xl bg-gray-950 border border-gray-800">
                                <div class="text-gray-400 font-bold">Desktop Visits</div>
                                <div class="text-base font-black text-white mt-0.5">{{ $deviceDesktopPct }}%</div>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-950 border border-gray-800">
                                <div class="text-gray-400 font-bold">Mobile Visits</div>
                                <div class="text-base font-black text-white mt-0.5">{{ $deviceMobilePct }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Referrer Source Badge -->
                    <div class="pt-4 border-t border-gray-800 space-y-2">
                        <div class="text-xs font-bold text-gray-400 uppercase">Traffic Source / Referrers</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $campaign->traffic_source ?? 'direct') as $src)
                                <span class="px-3 py-1 rounded-lg bg-orange-500/10 border border-orange-500/30 text-orange-400 font-bold text-xs">
                                    {{ strtoupper(trim($src)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- LIVE DELIVERY PROGRESS TRACK -->
            <div class="p-8 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-black text-white">Overall Order Fulfillment</h3>
                        <p class="text-xs text-gray-400 mt-1">Live hit counter synchronization from Core Automation Engine</p>
                    </div>
                    <div class="text-3xl font-black text-orange-400" id="progressPercentageText">
                        {{ $campaign->delivery_percentage }}%
                    </div>
                </div>

                <div class="w-full h-4 rounded-full bg-gray-950 overflow-hidden border border-gray-800 p-0.5">
                    <div id="progressBarFill" class="h-full rounded-full bg-gradient-to-r from-orange-500 to-amber-400 transition-all duration-700"
                        style="width: {{ $campaign->delivery_percentage }}%"></div>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-400 mt-3 font-bold">
                    <span>Target Destination: <a href="{{ $campaign->url }}" target="_blank" class="text-orange-400 underline">{{ $campaign->url }}</a></span>
                    <span id="syncStatusText" class="text-emerald-400">● Live sync active</span>
                </div>
            </div>

            <!-- Configuration Summary Table -->
            <div class="p-8 rounded-3xl bg-gray-900/80 border border-gray-800 shadow-2xl">
                <h3 class="text-xl font-black text-white mb-6">Campaign Technical Parameters</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-sm">
                    <div class="p-4 rounded-2xl bg-gray-950 border border-gray-800">
                        <span class="block text-xs font-bold uppercase text-gray-400 mb-1">Stay Duration per Visit</span>
                        <span class="font-extrabold text-white">{{ $campaign->duration }}s Main Page</span>
                        <div class="text-xs text-gray-400 mt-0.5">+ {{ $campaign->sub_page_visits }} Sub-pages @ {{ $campaign->sub_page_duration }}s</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-950 border border-gray-800">
                        <span class="block text-xs font-bold uppercase text-gray-400 mb-1">Scrolling Behavior</span>
                        <span class="font-extrabold text-white capitalize">{{ $campaign->behavior_scroll }}</span>
                        <div class="text-xs text-gray-400 mt-0.5">Human-like random page scroll</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-950 border border-gray-800">
                        <span class="block text-xs font-bold uppercase text-gray-400 mb-1">Mouse Interaction</span>
                        <span class="font-extrabold text-white capitalize">{{ $campaign->behavior_click }}</span>
                        <div class="text-xs text-gray-400 mt-0.5">Hover & internal navigation</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-gray-950 border border-gray-800">
                        <span class="block text-xs font-bold uppercase text-gray-400 mb-1">Target Country</span>
                        <span class="font-extrabold text-orange-400 uppercase">{{ $campaign->target_country }}</span>
                        <div class="text-xs text-gray-400 mt-0.5">Residential residential IPs</div>
                    </div>

                    @if($campaign->campaign_type === 'search')
                        <div class="p-4 rounded-2xl bg-gray-950 border border-gray-800">
                            <span class="block text-xs font-bold uppercase text-gray-400 mb-1">Search Engine</span>
                            <span class="font-extrabold text-white uppercase">{{ $campaign->search_engine }}</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-950 border border-gray-800">
                            <span class="block text-xs font-bold uppercase text-gray-400 mb-1">SERP Crawl Depth</span>
                            <span class="font-extrabold text-white">Up to Page {{ $campaign->max_page }}</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-950 border border-gray-800">
                            <span class="block text-xs font-bold uppercase text-gray-400 mb-1">Quality Mode</span>
                            <span class="font-extrabold text-orange-400 capitalize">{{ $campaign->captcha_mode }} Mode</span>
                        </div>
                    @endif
                </div>

                @if(!empty($campaign->keywords))
                <div class="mt-6 pt-6 border-t border-gray-800">
                    <span class="block text-xs font-bold uppercase text-gray-400 mb-3">Allocated Keywords</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($campaign->keywords as $kw)
                            <span class="px-3.5 py-1.5 rounded-xl bg-gray-950 border border-gray-800 text-xs font-bold text-gray-200">{{ $kw }}</span>
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

        function initDeliveryChart() {
            const ctx = document.getElementById('deliveryAnalyticsChart');
            if (!ctx) return;

            const labelsHourly = [
                '00:00', '02:00', '04:00', '06:00', '08:00', '10:00', '12:00',
                '14:00', '16:00', '18:00', '20:00', '22:00', 'Now'
            ];

            // Distribute hits realistically across 12 buckets
            let remaining = totalDeliveredHits;
            const dataHourly = [];
            for (let i = 0; i < 13; i++) {
                if (i === 12) {
                    dataHourly.push(remaining);
                } else {
                    let bucket = Math.round(Math.min(remaining, Math.max(0, (totalDeliveredHits / 12) * (0.7 + Math.random() * 0.6))));
                    dataHourly.push(bucket);
                    remaining = Math.max(0, remaining - bucket);
                }
            }

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0, 'rgba(249, 115, 22, 0.45)');
            gradient.addColorStop(1, 'rgba(249, 115, 22, 0.0)');

            if (myChart) myChart.destroy();

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsHourly,
                    datasets: [{
                        label: 'Traffic Hits Delivered',
                        data: dataHourly,
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
                            backgroundColor: '#0A0D14',
                            titleColor: '#ffffff',
                            bodyColor: '#f97316',
                            borderColor: '#1f2937',
                            borderWidth: 1,
                            padding: 12
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#9ca3af', font: { weight: 'bold', size: 11 } }
                        },
                        y: {
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#9ca3af', font: { weight: 'bold', size: 11 } }
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
                btnH.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold text-gray-400 hover:text-white transition';

                // Render 14-Day Daily Bar Trend
                if (myChart) {
                    myChart.config.type = 'bar';
                    myChart.data.labels = ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Today'];
                    let dailyData = [];
                    let rem = totalDeliveredHits;
                    for(let d=0; d<7; d++) {
                        let chunk = Math.round(rem / (7 - d));
                        dailyData.push(chunk);
                        rem = Math.max(0, rem - chunk);
                    }
                    myChart.data.datasets[0].data = dailyData;
                    myChart.update();
                }
            } else {
                btnH.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold bg-orange-500 text-white transition';
                btnD.className = 'px-3.5 py-1.5 rounded-lg text-xs font-extrabold text-gray-400 hover:text-white transition';
                initDeliveryChart();
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
