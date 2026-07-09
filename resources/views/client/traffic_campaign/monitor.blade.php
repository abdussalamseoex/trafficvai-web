<x-app-layout>
    <div class="min-h-screen bg-[#0A0D14] text-gray-100 py-12 relative overflow-hidden">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-10 left-1/3 w-96 h-96 bg-brand/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 right-1/3 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 border-b border-gray-800/80 pb-8">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-500/10 text-orange-400 border border-orange-500/20">Live Monitoring</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-gray-800 text-gray-300">{{ $campaign->external_order_id }}</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Campaign Analytics & Live Progress</h1>
                    <p class="text-gray-400 mt-1 text-sm">Synchronizing in real-time with <code class="text-orange-400">surf.abguestpost.net</code> core engine.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('client.traffic_campaign.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-900 border border-gray-800 hover:bg-gray-800 text-gray-300 font-semibold text-sm transition">
                        All Campaigns
                    </a>
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm shadow-md transition">
                        + New Campaign
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-8 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Top Grid Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Status Card -->
                <div class="p-6 rounded-3xl bg-gray-900/60 backdrop-blur-xl border border-gray-800/80">
                    <div class="text-xs font-bold uppercase text-gray-400 mb-2">Engine Status</div>
                    <div class="flex items-center justify-between">
                        <span id="badgeStatus" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-black uppercase tracking-wide {{ $campaign->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                            {{ ucfirst($campaign->status) }}
                        </span>
                        <form action="{{ route('client.traffic_campaign.toggle', $campaign) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-gray-400 hover:text-white underline">
                                {{ $campaign->status === 'active' ? 'Pause Campaign' : 'Resume Campaign' }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Delivered Visits Card -->
                <div class="p-6 rounded-3xl bg-gray-900/60 backdrop-blur-xl border border-gray-800/80">
                    <div class="text-xs font-bold uppercase text-gray-400 mb-2">Delivered Hits</div>
                    <div class="text-3xl font-black text-white">
                        <span id="hitsDeliveredText">{{ number_format($campaign->hits_delivered) }}</span>
                        <span class="text-base font-normal text-gray-500">/ {{ number_format($campaign->total_limit) }}</span>
                    </div>
                </div>

                <!-- Campaign Type Card -->
                <div class="p-6 rounded-3xl bg-gray-900/60 backdrop-blur-xl border border-gray-800/80">
                    <div class="text-xs font-bold uppercase text-gray-400 mb-2">Traffic Engine</div>
                    <div class="text-xl font-bold text-white capitalize">
                        {{ $campaign->campaign_type === 'search' ? 'Google Search Organic' : 'Direct GOAT Traffic' }}
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Hourly Limit: {{ $campaign->hourly_limit }}/hr</div>
                </div>

                <!-- Validity / Expiry Card -->
                <div class="p-6 rounded-3xl bg-gray-900/60 backdrop-blur-xl border border-gray-800/80">
                    <div class="text-xs font-bold uppercase text-gray-400 mb-2">30-Day Point Expiry</div>
                    <div class="text-lg font-bold text-orange-400">
                        {{ $campaign->expires_at ? $campaign->expires_at->format('M d, Y') : '30 Days' }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $campaign->points_deducted }} points allocated</div>
                </div>
            </div>

            <!-- LIVE PROGRESS BAR CARD -->
            <div class="p-8 rounded-3xl bg-gray-900/80 backdrop-blur-2xl border border-gray-800/80 shadow-2xl mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-white">Live Delivery Progress</h3>
                        <p class="text-xs text-gray-400 mt-1">Updates automatically from Core Automation Engine</p>
                    </div>
                    <div class="text-2xl font-black text-orange-400" id="progressPercentageText">
                        {{ $campaign->delivery_percentage }}%
                    </div>
                </div>

                <!-- Progress Track -->
                <div class="w-full h-4 rounded-full bg-gray-950 overflow-hidden border border-gray-800 p-0.5">
                    <div id="progressBarFill" class="h-full rounded-full bg-gradient-to-r from-orange-500 to-amber-400 transition-all duration-700"
                        style="width: {{ $campaign->delivery_percentage }}%"></div>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-400 mt-3">
                    <span>Target URL: <a href="{{ $campaign->url }}" target="_blank" class="text-orange-400 underline">{{ $campaign->url }}</a></span>
                    <span id="syncStatusText" class="text-gray-500">Syncing live...</span>
                </div>
            </div>

            <!-- Configuration Summary Table -->
            <div class="p-8 rounded-3xl bg-gray-900/50 backdrop-blur-xl border border-gray-800/80">
                <h3 class="text-lg font-bold text-white mb-6">Campaign Technical Specification</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-sm">
                    <div>
                        <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Duration per Visit</span>
                        <span class="font-semibold text-gray-200">{{ $campaign->duration }}s Main + ({{ $campaign->sub_page_visits }} Sub-pages @ {{ $campaign->sub_page_duration }}s)</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Device Targeting</span>
                        <span class="font-semibold text-gray-200 uppercase">{{ $campaign->device_type }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Target Country</span>
                        <span class="font-semibold text-gray-200">{{ $campaign->target_country }}</span>
                    </div>
                    @if($campaign->campaign_type === 'search')
                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Search Engine</span>
                            <span class="font-semibold text-gray-200 uppercase">{{ $campaign->search_engine }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Max Crawl Pages</span>
                            <span class="font-semibold text-gray-200">Up to Page {{ $campaign->max_page }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase text-gray-500 mb-1">Quality Mode</span>
                            <span class="font-semibold text-orange-400 capitalize">{{ $campaign->captcha_mode }} Mode</span>
                        </div>
                    @endif
                </div>

                @if(!empty($campaign->keywords))
                <div class="mt-6 pt-6 border-t border-gray-800">
                    <span class="block text-xs font-bold uppercase text-gray-500 mb-2">Allocated Keywords</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($campaign->keywords as $kw)
                            <span class="px-3 py-1 rounded-lg bg-gray-950 border border-gray-800 text-xs font-medium text-gray-300">{{ $kw }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Live Polling Script -->
    <script>
        function pollCampaignStatus() {
            fetch("{{ route('client.traffic_campaign.live_status', $campaign) }}")
                .then(res => res.json())
                .then(data => {
                    document.getElementById('hitsDeliveredText').innerText = data.hits_delivered.toLocaleString();
                    document.getElementById('progressPercentageText').innerText = data.percentage + '%';
                    document.getElementById('progressBarFill').style.width = data.percentage + '%';
                    document.getElementById('syncStatusText').innerText = 'Last updated: just now';
                })
                .catch(err => console.log('Sync err:', err));
        }

        // Poll every 10 seconds
        setInterval(pollCampaignStatus, 10000);
    </script>
</x-app-layout>
