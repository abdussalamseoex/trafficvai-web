<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Traffic Packages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <p class="text-xl text-gray-500">
                    Choose from our result-driven website traffic packages & automated campaigns.
                </p>
                <a href="{{ route('client.traffic_campaign.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-gray-900 text-white font-bold text-sm hover:bg-brand transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Live Campaign Monitoring
                </a>
            </div>

            <!-- Premium Interactive Traffic Campaigns (surf.abguestpost.net integration) -->
            <div class="mb-16">
                <div class="flex items-center gap-3 mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-500/10 text-orange-600 border border-orange-500/20">Automated Engine</span>
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Interactive Traffic Builders</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Card 1: Real Website Traffic GOAT Package -->
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800 p-8 text-white shadow-2xl border border-gray-800 transition-all duration-300 hover:scale-[1.01] hover:border-orange-500/50 group">
                        <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-orange-500/10 blur-3xl group-hover:bg-orange-500/20 transition-all"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-500 text-white shadow-sm">Direct Visitors</span>
                                <span class="text-xs font-semibold text-gray-400">30-Day Point Validity</span>
                            </div>
                            <h3 class="text-2xl font-black mb-3 text-white group-hover:text-orange-400 transition">Real Website Traffic GOAT Package</h3>
                            <p class="text-gray-300 text-sm leading-relaxed mb-6">
                                Launch high-retention direct website traffic with custom hourly pacing, sub-page navigation, and multi-device targeting powered by our core automation engine.
                            </p>
                            <div class="flex flex-wrap gap-2 mb-8 text-xs font-medium text-gray-300">
                                <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/10">Up to 120s Duration</span>
                                <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/10">Sub-Page Navigation</span>
                                <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/10">Real-Time Delivery</span>
                            </div>
                            <a href="{{ route('client.traffic_campaign.builder', ['tab' => 'direct']) }}" class="inline-flex items-center justify-center w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm shadow-lg shadow-orange-500/25 hover:from-orange-600 hover:to-amber-600 transition-all">
                                Launch Direct Campaign
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Card 2: Real Google Search Click Booster -->
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800 p-8 text-white shadow-2xl border border-gray-800 transition-all duration-300 hover:scale-[1.01] hover:border-blue-500/50 group">
                        <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-blue-500/10 blur-3xl group-hover:bg-blue-500/20 transition-all"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-500 text-white shadow-sm">Organic Search CTR</span>
                                <span class="text-xs font-semibold text-gray-400">Normal / Premium Mode</span>
                            </div>
                            <h3 class="text-2xl font-black mb-3 text-white group-hover:text-blue-400 transition">Real Google Search Click Booster</h3>
                            <p class="text-gray-300 text-sm leading-relaxed mb-6">
                                Boost organic CTR by targeting search engine keywords (Google, Bing, Yahoo). Search bot locates your site up to page 10 and simulates authentic visitor behavior.
                            </p>
                            <div class="flex flex-wrap gap-2 mb-8 text-xs font-medium text-gray-300">
                                <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/10">Keyword Allocation %</span>
                                <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/10">Page 1-10 Crawling</span>
                                <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/10">Guaranteed Mode</span>
                            </div>
                            <a href="{{ route('client.traffic_campaign.builder', ['tab' => 'search']) }}" class="inline-flex items-center justify-center w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-sm shadow-lg shadow-blue-500/25 hover:from-blue-700 hover:to-indigo-700 transition-all">
                                Launch Google Search Campaign
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            @foreach ($categories as $category)
                @if($category->services->count() > 0)
                <div class="mb-16">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-6 tracking-tight">{{ $category->name }}</h2>
                    @if($category->description)
                        <p class="text-gray-500 mb-8">{{ $category->description }}</p>
                    @endif
                    <div class="space-y-6">
                        @foreach ($category->services as $service)
                        <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="max-w-xl">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-brand transition">{{ $service->name }}</h3>
                                    <p class="text-gray-500 leading-relaxed">{{ Str::limit($service->description, 120) }}</p>
                                    
                                    <div class="mt-6 flex flex-wrap gap-2">
                                        @if($service->packages->count() > 0)
                                        <span class="bg-orange-50 text-brand px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Starting from <span class="price-convert" data-base-price="{{ $service->packages->min('price') }}">${{ number_format($service->packages->min('price'), 0) }}</span></span>
                                        <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">{{ $service->packages->count() }} Plans</span>
                                        @endif
                                        @if($service->addons && $service->addons->count() > 0)
                                        <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">+{{ $service->addons->count() }} Addons</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="shrink-0 mt-6 md:mt-0">
                                    <a href="{{ route('client.traffic.show', $service) }}" class="inline-flex items-center justify-center w-full md:w-auto bg-brand hover:bg-brand text-white font-bold py-3 px-6 rounded-xl transition duration-150 shadow-md">
                                        View Details
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            @if ($uncategorizedServices->count() > 0)
                <div class="mb-16">
                    @if($categories->count() > 0)
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-6 tracking-tight">Other Packages</h2>
                    @endif
                    <div class="space-y-6">
                        @foreach ($uncategorizedServices as $service)
                        <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="max-w-xl">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-brand transition">{{ $service->name }}</h3>
                                    <p class="text-gray-500 leading-relaxed">{{ Str::limit($service->description, 120) }}</p>
                                    
                                    <div class="mt-6 flex flex-wrap gap-2">
                                        @if($service->packages->count() > 0)
                                        <span class="bg-orange-50 text-brand px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Starting from <span class="price-convert" data-base-price="{{ $service->packages->min('price') }}">${{ number_format($service->packages->min('price'), 0) }}</span></span>
                                        <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">{{ $service->packages->count() }} Plans</span>
                                        @endif
                                        @if($service->addons && $service->addons->count() > 0)
                                        <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">+{{ $service->addons->count() }} Addons</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="shrink-0 mt-6 md:mt-0">
                                    <a href="{{ route('client.traffic.show', $service) }}" class="inline-flex items-center justify-center w-full md:w-auto bg-brand hover:bg-brand text-white font-bold py-3 px-6 rounded-xl transition duration-150 shadow-md">
                                        View Details
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
