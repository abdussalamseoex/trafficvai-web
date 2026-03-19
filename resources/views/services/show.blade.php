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
        <section class="w-full">
            <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 relative overflow-hidden">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute -top-32 -left-32 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
                </div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
                    <nav class="flex items-center space-x-2 text-sm text-gray-400 mb-10">
                        <a href="{{ route('services.index') }}" class="hover:text-orange-400 transition-colors">Services</a>
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-gray-300 font-medium">{{ $service->name }}</span>
                    </nav>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div>
                            <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-6">
                                <div class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                                <span class="text-orange-400 text-xs font-bold uppercase tracking-widest">Premium Service</span>
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-white leading-tight mb-6">{{ $service->name }}</h1>
                            @if($service->description)
                            <p class="text-lg text-gray-300 leading-relaxed mb-8">{{ $service->description }}</p>
                            @endif
                            <div class="flex flex-wrap gap-4">
                                <a href="#pricing-section"
                                   onclick="event.preventDefault(); document.getElementById('pricing-section').scrollIntoView({behavior:'smooth',block:'start'});"
                                   class="inline-flex items-center gap-2 bg-[#E8470A] hover:bg-orange-600 text-white font-black px-8 py-4 rounded-2xl transition-all duration-300 shadow-xl shadow-orange-600/30 hover:scale-105 active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Order Now
                                </a>
                                @if($service->sample_link)
                                <a href="{{ $service->sample_link }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold px-8 py-4 rounded-2xl transition-all duration-300 hover:scale-105 active:scale-95 backdrop-blur-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Sample / Download
                                </a>
                                @endif
                            </div>
                            @php
                                $badge1 = \App\Models\Setting::get('service_hero_badge_1', 'Secure Payment');
                                $badge2 = \App\Models\Setting::get('service_hero_badge_2', 'Professional Team');
                                $badge3 = \App\Models\Setting::get('service_hero_badge_3', 'Results Guaranteed');
                            @endphp
                            <div class="flex flex-wrap gap-5 mt-8 pt-8 border-t border-white/10">
                                <span class="flex items-center gap-2 text-gray-400 text-sm"><svg class="w-4 h-4 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ $badge1 }}</span>
                                <span class="flex items-center gap-2 text-gray-400 text-sm"><svg class="w-4 h-4 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ $badge2 }}</span>
                                <span class="flex items-center gap-2 text-gray-400 text-sm"><svg class="w-4 h-4 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ $badge3 }}</span>
                            </div>
                        </div>
                        <div>
                            @php
                                $embedUrl = null;
                                if ($service->hero_video_url) {
                                    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $service->hero_video_url, $m)) {
                                        $embedUrl = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
                                    } elseif (preg_match('/vimeo\.com\/(\d+)/', $service->hero_video_url, $m)) {
                                        $embedUrl = 'https://player.vimeo.com/video/' . $m[1] . '?byline=0&portrait=0';
                                    }
                                }
                                $isDirectVideo = $service->hero_video_url && !$embedUrl;
                            @endphp
                            <div class="rounded-3xl overflow-hidden shadow-2xl shadow-black/50 border border-white/10">
                                @if($embedUrl)
                                    <div class="aspect-video bg-gray-900">
                                        <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy" title="{{ $service->name }}"></iframe>
                                    </div>
                                @elseif($isDirectVideo)
                                    <div class="aspect-video bg-gray-900">
                                        <video class="w-full h-full object-cover" controls preload="metadata" @if($service->hero_image) poster="{{ Storage::disk('public')->url($service->hero_image) }}" @endif>
                                            <source src="{{ $service->hero_video_url }}">
                                        </video>
                                    </div>
                                @elseif($service->hero_image)
                                    <div class="aspect-video bg-gray-800">
                                        <img src="{{ Storage::disk('public')->url($service->hero_image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="aspect-video bg-gradient-to-br from-gray-800 to-gray-900 flex flex-col items-center justify-center gap-4 relative">
                                        <div class="w-20 h-20 rounded-2xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center">
                                            <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-white font-bold text-lg">{{ $service->name }}</p>
                                            <p class="text-gray-500 text-sm mt-1">Premium Quality Service</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Pricing / Packages Section -->
            <div id="pricing-section" class="mb-20 scroll-mt-8" x-data="{}">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                    @foreach ($service->packages->sortBy('price') as $package)
                    <div 
                        class="bg-white border-2 rounded-3xl p-8 transition-all duration-300 relative flex flex-col h-full border-gray-100 hover:border-gray-200 shadow-sm hover:shadow-xl group"
                    >
                        @if($loop->iteration == 2)
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg">
                            Most Popular
                        </div>
                        @endif

                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-2xl font-bold text-gray-900">{{ $package->name }}</h3>
                            </div>
                            <p class="text-gray-500 text-sm leading-relaxed min-h-[48px]">{{ $package->description }}</p>
                        </div>

                        <div class="mb-8">
                            <div class="flex items-baseline mb-2">
                                <span class="text-4xl font-black text-gray-900 leading-none" x-text="$store.currency ? $store.currency.format({{ $package->price }}) : '$' + {{ $package->price }}"></span>
                            </div>
                            <span class="text-gray-500 text-sm font-medium">/ package</span>
                        </div>

                        <ul class="space-y-4 flex-1 mb-8">
                            @if($package->features)
                                @foreach($package->features as $feature)
                                <li class="flex items-start text-gray-700">
                                    <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ $feature }}</span>
                                </li>
                                @endforeach
                            @endif
                        </ul>

                        <div class="mt-auto">
                            <a href="{{ route('client.services.show', $service->slug) }}?package_id={{ $package->id }}" 
                               class="block w-full text-center bg-gray-900 text-white font-bold py-4 rounded-2xl hover:bg-[#E8470A] transition-all duration-300 shadow-lg group-hover:scale-[1.02]">
                                Order {{ $package->name }}
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Info Section about benefits -->
                <div class="max-w-4xl mx-auto bg-gray-900 rounded-[2.5rem] p-10 shadow-2xl overflow-hidden relative border border-gray-800">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-600/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-purple-600/10 rounded-full blur-3xl"></div>

                    <div class="relative z-10 text-center">
                        <h2 class="text-3xl font-bold text-white mb-4">Ready to elevate your digital presence?</h2>
                        <p class="text-gray-400 mb-8 max-w-2xl mx-auto">Get started today with our professional {{ $service->name }} services. All projects are managed through our secure client dashboard for maximum transparency and results.</p>
                        
                        <div class="flex flex-wrap justify-center gap-6">
                            <div class="flex items-center gap-3 text-white/80">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-medium text-sm">Secure Payment</span>
                            </div>
                            <div class="flex items-center gap-3 text-white/80">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-medium text-sm">Real-time Tracking</span>
                            </div>
                            <div class="flex items-center gap-3 text-white/80">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-medium text-sm">Expert Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            @if(!empty($service->faqs) && is_array($service->faqs))
            <div class="max-w-4xl mx-auto my-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Frequently Asked Questions</h2>
                    <p class="text-lg text-gray-600">Everything you need to know about our {{ $service->name }} service.</p>
                </div>
                
                <div class="space-y-4" x-data="{ activeFaq: null }">
                    @foreach($service->faqs as $index => $faq)
                    @if(!empty($faq['question']) && !empty($faq['answer']))
                    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden transition-all duration-300" 
                         :class="activeFaq === {{ $index }} ? 'shadow-md border-indigo-100 ring-1 ring-indigo-100' : 'shadow-sm hover:border-gray-200'">
                        <button 
                            @click="activeFaq = activeFaq === {{ $index }} ? null : {{ $index }}" 
                            class="w-full text-left px-8 py-6 focus:outline-none flex justify-between items-center"
                        >
                            <span class="font-bold text-gray-900 pr-8" :class="activeFaq === {{ $index }} ? 'text-indigo-600' : ''">{{ $faq['question'] }}</span>
                            <span class="rounded-full flex items-center justify-center transition-transform duration-300" :class="activeFaq === {{ $index }} ? 'rotate-180 text-indigo-600 bg-indigo-50 w-8 h-8' : 'text-gray-400 w-8 h-8'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </span>
                        </button>
                        <div 
                            x-show="activeFaq === {{ $index }}" 
                            x-collapse 
                            class="px-8 pb-6 text-gray-600 leading-relaxed prose prose-sm max-w-none"
                            style="display: none;"
                        >
                            {!! nl2br(e($faq['answer'])) !!}
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- SEO Process / Trust Section -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 py-12 border-t border-gray-100 mt-12">
                <div class="text-center">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h5 class="font-bold text-gray-900 mb-2">Secure Payments</h5>
                    <p class="text-xs text-gray-500">Industry standard encryption for all transactions.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h5 class="font-bold text-gray-900 mb-2">Fast Execution</h5>
                    <p class="text-xs text-gray-500">Most orders started within 24-48 hours.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2M7 5h2M11 9h2M7 9h2M11 13h2M7 13h2M11 17h2M7 17h2M5 3a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <h5 class="font-bold text-gray-900 mb-2">Detailed Reports</h5>
                    <p class="text-xs text-gray-500">Transparent updates and white-label reporting.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h5 class="font-bold text-gray-900 mb-2">24/7 Support</h5>
                    <p class="text-xs text-gray-500">Always available to help with your campaigns.</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <x-frontend-footer />
    </div>
    <x-currency-script />
</body>
</html>
