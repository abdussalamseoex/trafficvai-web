<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-seo-tags />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <x-frontend-header />

        <!-- Hero Section -->
        <x-page-hero
            :badge="$page->hero_badge ?? 'Premium Solutions'"
            :title="$page->title ?? ($title . ' Services')"
            :description="$page->hero_description ?? ('Choose from our result-driven ' . strtolower($title) . ' packages tailored for your specific needs.')"
            cta-label="Browse Packages"
            cta-scroll="campaign-list"
        />

        <!-- Main Content -->
        <main id="campaign-list" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            
            @foreach ($categories as $category)
                @if($category->services->count() > 0)
                <div class="mb-16">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-3 tracking-tight">{{ $category->name }}</h2>
                    @if($category->description)
                        <p class="text-gray-500 mb-8 text-base">{{ $category->description }}</p>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($category->services as $service)
                        <div class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col" style="border-top: 3px solid transparent;" onmouseenter="this.style.borderTop='3px solid #ff6b00'" onmouseleave="this.style.borderTop='3px solid transparent'">
                            <h3 class="text-lg font-extrabold text-blue-700 mb-2 leading-tight">{{ $service->name }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed flex-1 mb-5">{{ Str::limit($service->description, 100) }}</p>
                            <div class="flex flex-wrap gap-2 mb-5">
                                @if($service->packages->count() > 0)
                                <span class="border border-brand text-brand text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">FROM <span class="price-convert" data-base-price="{{ $service->packages->min('price') }}">${{ number_format($service->packages->min('price'), 0) }}</span></span>
                                <span class="border border-green-500 text-green-600 text-xs font-bold px-3 py-1 rounded-full">{{ $service->packages->count() }} PLANS</span>
                                @endif
                            </div>
                            @php
                                $showRoute = ($type === 'link-building') ? route('link_building.show', $service->slug) : (preg_match('/^(seo-campaigns|keyword-research|on-page-seo|technical-seo|local-seo|content-seo|seo-audit|monthly-seo|e-commerce-seo)$/', $type) ? route('seo_campaigns.show', ['type' => $type, 'service' => $service->slug]) : route('campaigns.show', ['type' => $type, 'service' => $service->slug]));
                            @endphp
                            <a href="{{ $showRoute }}" class="w-full inline-flex items-center justify-center bg-brand hover:opacity-90 text-white font-bold text-sm py-3 px-4 rounded-xl transition-all duration-200 shadow-md">
                                View Packages
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            @if ($uncategorizedServices->count() > 0)
                <div class="mb-16">
                    @if($categories->count() > 0)
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-8 tracking-tight">Other Services</h2>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($uncategorizedServices as $service)
                        <div class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col" style="border-top: 3px solid transparent;" onmouseenter="this.style.borderTop='3px solid #ff6b00'" onmouseleave="this.style.borderTop='3px solid transparent'">
                            <h3 class="text-lg font-extrabold text-blue-700 mb-2 leading-tight">{{ $service->name }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed flex-1 mb-5">{{ Str::limit($service->description, 100) }}</p>
                            <div class="flex flex-wrap gap-2 mb-5">
                                @if($service->packages->count() > 0)
                                <span class="border border-brand text-brand text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">FROM <span class="price-convert" data-base-price="{{ $service->packages->min('price') }}">${{ number_format($service->packages->min('price'), 0) }}</span></span>
                                <span class="border border-green-500 text-green-600 text-xs font-bold px-3 py-1 rounded-full">{{ $service->packages->count() }} PLANS</span>
                                @endif
                            </div>
                            @php
                                $showRoute = ($type === 'link-building') ? route('link_building.show', $service->slug) : (preg_match('/^(seo-campaigns|keyword-research|on-page-seo|technical-seo|local-seo|content-seo|seo-audit|monthly-seo|e-commerce-seo)$/', $type) ? route('seo_campaigns.show', ['type' => $type, 'service' => $service->slug]) : route('campaigns.show', ['type' => $type, 'service' => $service->slug]));
                            @endphp
                            <a href="{{ $showRoute }}" class="w-full inline-flex items-center justify-center bg-brand hover:opacity-90 text-white font-bold text-sm py-3 px-4 rounded-xl transition-all duration-200 shadow-md">
                                View Packages
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($categories->isEmpty() && $uncategorizedServices->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-gray-100">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900">No Services Yet</h3>
                    <p class="mt-2 text-gray-500">Coming soon. Check back later!</p>
                </div>
            @endif
        </main>
    </div>
    <x-frontend-footer />
    <x-currency-script />
</body>
</html>
