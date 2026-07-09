<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-[#0A0D14] text-gray-900 dark:text-gray-100 py-12 relative overflow-hidden transition-colors duration-300">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-10 left-1/4 w-96 h-96 bg-brand/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 right-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b border-gray-200 dark:border-gray-800/80 pb-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20">Core Server Embed</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">Plug-and-Play Widget</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">Traffic Campaign Builder</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm sm:text-base">Instant real-time connection to <code class="text-orange-500 font-bold">surf.abguestpost.net</code> core server.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="px-4 py-2.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-800 text-sm font-bold">
                        Available Points: <span class="text-orange-500">{{ number_format(auth()->user()->traffic_points ?? 0) }} Pts</span>
                    </div>
                    <a href="{{ route('client.traffic_campaign.topup') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm shadow transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Buy Points & History
                    </a>
                    <a href="{{ route('client.traffic_campaign.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-300 font-semibold text-sm border border-gray-300 dark:border-gray-800 transition">
                        My Campaigns
                    </a>
                </div>
            </div>

            <!-- INSTANT IFRAME EMBED SYSTEM (Core Server Ready Widget) -->
            <div class="relative w-full rounded-2xl overflow-hidden bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-800">
                @php
                    $apiKey = config('services.surf_engine.key', env('SURF_ENGINE_API_KEY', 'TV_CORE_API_KEY'));
                    $clientName = auth()->user()->name ?? 'TrafficVai_Client';
                    $embedUrl = "https://surf.abguestpost.net/embed/campaign.html?apikey=" . urlencode($apiKey) . "&client_name=" . urlencode($clientName);
                @endphp

                <iframe 
                    src="{{ $embedUrl }}" 
                    width="100%" 
                    height="840px" 
                    style="border:none; border-radius:16px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); min-height: 840px;"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</x-app-layout>
