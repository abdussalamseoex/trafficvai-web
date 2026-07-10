<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-seo-tags />
    <title>Website Traffic — Real Direct & Organic Search Visitors | TrafficVai</title>
    <meta name="description" content="Drive high-retention Direct, Referral & Organic Search traffic to your website. GA4-verified real visitor sessions. Custom targeting. Loved by SEO experts worldwide.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-white">

    {{-- HEADER --}}
    <x-frontend-header />

    {{-- ================================================
         SECTION 1: HERO  (matches site's page-hero style)
    ================================================ --}}
    <section class="w-full">
        <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 relative overflow-hidden">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-32 -left-32 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
            </div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
                <div class="text-center max-w-3xl mx-auto">
                    <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-6">
                        <div class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                        <span class="text-orange-400 text-xs font-bold uppercase tracking-widest">Direct & Organic Search Traffic</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                        Real Website Traffic That<br>
                        <span class="text-orange-400">Shows Up in Analytics</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-300 leading-relaxed mb-10 max-w-2xl mx-auto">
                        Send targeted <strong class="text-white">Direct & Referral visitors</strong> or <strong class="text-white">Organic Search visitors</strong> from Google & Bing to your website. Every session appears in GA4 with the engagement metrics you control.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <a href="#calculator"
                           onclick="event.preventDefault(); document.getElementById('calculator').scrollIntoView({behavior:'smooth',block:'start'});"
                           class="inline-flex items-center gap-2 bg-[#E8470A] hover:bg-orange-600 text-white font-black px-8 py-4 rounded-2xl transition-all duration-300 shadow-xl shadow-orange-600/30 hover:scale-105 active:scale-95">
                            Calculate Traffic Cost
                        </a>
                        @auth
                        <a href="{{ route('client.traffic_campaign.builder') }}"
                           class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold px-8 py-4 rounded-2xl transition-all duration-300 hover:scale-105 active:scale-95 backdrop-blur-sm">
                            Launch Campaign →
                        </a>
                        @else
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold px-8 py-4 rounded-2xl transition-all duration-300 hover:scale-105 active:scale-95 backdrop-blur-sm">
                            Get Started Free →
                        </a>
                        @endauth
                    </div>
                    {{-- Trust stats --}}
                    <div class="grid grid-cols-3 gap-8 max-w-xl mx-auto mt-10 pt-10 border-t border-white/10">
                        <div><div class="text-2xl font-black text-white">100%</div><div class="text-xs text-gray-400 mt-1">GA4 Verified</div></div>
                        <div><div class="text-2xl font-black text-white">< 3 min</div><div class="text-xs text-gray-400 mt-1">Setup Time</div></div>
                        <div><div class="text-2xl font-black text-white">Real-time</div><div class="text-xs text-gray-400 mt-1">Dashboard Control</div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 2: DUAL-TAB INTERACTIVE CALCULATOR
    ================================================ --}}
    @php $bdtRate = $bdtRate ?? 120; @endphp
    <section id="calculator" class="bg-gray-50 py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-orange-500 text-xs font-bold uppercase tracking-widest">Live Estimator Tool</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Calculate Your Traffic Cost Instantly</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">Adjust the sliders and options below. Switch tabs to compare <strong class="text-gray-700">Direct</strong> and <strong class="text-gray-700">Organic Search</strong> pricing side by side.</p>
            </div>

            <div x-data="{
                activeCalc: 'direct',
                dVisitors: 5000, dDuration: 60, dCountry: 'worldwide',
                sVisitors: 3000, sDuration: 60, sEngine: 'google', sCountry: 'worldwide',
                currency: localStorage.getItem('selected_currency') || 'BDT',
                bdtRate: {{ $bdtRate }},
                get dPoints() { return this.dVisitors * Math.ceil(this.dDuration / 60); },
                get sPoints() { return this.sVisitors * Math.ceil(this.sDuration / 60); },
                get activePoints() { return this.activeCalc === 'direct' ? this.dPoints : this.sPoints; },
                get activeUsd() { return (this.activePoints / 1000).toFixed(2); },
                get activeBdt() { return Math.round(this.activePoints / 1000 * this.bdtRate); },
                get displayCost() { return this.currency === 'BDT' ? '৳' + Number(this.activeBdt).toLocaleString() : '$' + this.activeUsd; }
            }" class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">

                {{-- Tab Switcher --}}
                <div class="grid grid-cols-2 border-b border-gray-200">
                    <button @click="activeCalc='direct'"
                        :class="activeCalc==='direct' ? 'bg-orange-50 border-b-2 border-orange-500 text-orange-700 font-black' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex items-center justify-center gap-2 py-5 px-6 text-sm font-semibold transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Direct & Referral Traffic
                    </button>
                    <button @click="activeCalc='search'"
                        :class="activeCalc==='search' ? 'bg-indigo-50 border-b-2 border-indigo-600 text-indigo-700 font-black' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex items-center justify-center gap-2 py-5 px-6 text-sm font-semibold transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Organic Search Traffic
                    </button>
                </div>

                <div class="p-8 md:p-10">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
                        {{-- Controls --}}
                        <div class="lg:col-span-3 space-y-7">

                            {{-- DIRECT CONTROLS --}}
                            <div x-show="activeCalc==='direct'" x-transition class="space-y-7">
                                <div>
                                    <div class="flex justify-between mb-3">
                                        <label class="text-gray-700 font-semibold text-sm">Number of Visitors</label>
                                        <span class="text-orange-600 font-black" x-text="Number(dVisitors).toLocaleString()"></span>
                                    </div>
                                    <input type="range" min="500" max="100000" step="500" x-model="dVisitors" class="w-full accent-orange-500 cursor-pointer" style="appearance: auto; height: 6px;">
                                    <div class="flex justify-between text-xs text-gray-400 mt-1"><span>500</span><span>100,000+</span></div>
                                </div>
                                <div>
                                    <label class="text-gray-700 font-semibold text-sm block mb-3">Visit Duration per Visitor</label>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach([30=>'30 sec', 60=>'1 min', 120=>'2 min', 180=>'3 min'] as $val => $label)
                                        <button type="button" @click="dDuration={{ $val }}"
                                            :class="dDuration==={{ $val }} ? 'bg-orange-500 text-white border-orange-500 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-orange-300'"
                                            class="py-2 px-2 rounded-xl border text-xs font-bold transition-all duration-200">{{ $label }}</button>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="text-gray-700 font-semibold text-sm block mb-3">Country Targeting</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" @click="dCountry='worldwide'"
                                            :class="dCountry==='worldwide' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:border-orange-300'"
                                            class="py-2.5 px-4 rounded-xl border text-xs font-bold transition-all">🌍 Worldwide</button>
                                        <button type="button" @click="dCountry='targeted'"
                                            :class="dCountry==='targeted' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-600 border-gray-200 hover:border-orange-300'"
                                            class="py-2.5 px-4 rounded-xl border text-xs font-bold transition-all">🎯 Specific Country</button>
                                    </div>
                                </div>
                            </div>

                            {{-- SEARCH CONTROLS --}}
                            <div x-show="activeCalc==='search'" x-transition class="space-y-7">
                                <div>
                                    <div class="flex justify-between mb-3">
                                        <label class="text-gray-700 font-semibold text-sm">Keyword Search Visitors</label>
                                        <span class="text-indigo-600 font-black" x-text="Number(sVisitors).toLocaleString()"></span>
                                    </div>
                                    <input type="range" min="500" max="50000" step="500" x-model="sVisitors" class="w-full accent-indigo-600 cursor-pointer" style="appearance: auto; height: 6px;">
                                    <div class="flex justify-between text-xs text-gray-400 mt-1"><span>500</span><span>50,000+</span></div>
                                </div>
                                <div>
                                    <label class="text-gray-700 font-semibold text-sm block mb-3">Search Engine</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach(['google'=>'Google', 'bing'=>'Bing', 'yahoo'=>'Yahoo'] as $k=>$v)
                                        <button type="button" @click="sEngine='{{ $k }}'"
                                            :class="sEngine==='{{ $k }}' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'"
                                            class="py-2 px-3 rounded-xl border text-xs font-bold transition-all duration-200">{{ $v }}</button>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="text-gray-700 font-semibold text-sm block mb-3">Visit Duration per Visitor</label>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach([30=>'30 sec', 60=>'1 min', 120=>'2 min', 180=>'3 min'] as $val => $label)
                                        <button type="button" @click="sDuration={{ $val }}"
                                            :class="sDuration==={{ $val }} ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'"
                                            class="py-2 px-2 rounded-xl border text-xs font-bold transition-all duration-200">{{ $label }}</button>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="text-gray-700 font-semibold text-sm block mb-3">Country Targeting</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" @click="sCountry='worldwide'"
                                            :class="sCountry==='worldwide' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'"
                                            class="py-2.5 px-4 rounded-xl border text-xs font-bold transition-all">🌍 Worldwide</button>
                                        <button type="button" @click="sCountry='targeted'"
                                            :class="sCountry==='targeted' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'"
                                            class="py-2.5 px-4 rounded-xl border text-xs font-bold transition-all">🎯 Specific Country</button>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100">
                                <p class="text-xs text-blue-700 leading-relaxed">
                                    <span class="font-bold">How Points Work:</span> 1 Traffic Point = 1 visitor per 60 seconds. A 2-min visit costs 2 Points/visitor. 1,000 Points = $1.00 USD.
                                </p>
                            </div>
                        </div>

                        {{-- Live Output Panel --}}
                        <div class="lg:col-span-2">
                            <div class="bg-gray-900 rounded-2xl p-7 h-full flex flex-col justify-between">
                                <div>
                                    <div class="text-xs text-gray-400 uppercase tracking-widest font-bold mb-6">Estimated Cost</div>
                                    <div class="mb-5">
                                        <div class="text-gray-400 text-xs mb-1">Traffic Points Required</div>
                                        <div class="text-3xl font-black text-white" x-text="Number(activePoints).toLocaleString() + ' Pts'"></div>
                                    </div>
                                    <div class="bg-orange-500/15 border border-orange-500/30 rounded-2xl p-5 mb-5">
                                        <div class="text-xs text-orange-300 mb-1">Total Cost</div>
                                        <div class="text-3xl font-black text-white" x-text="displayCost"></div>
                                        <div class="text-xs text-gray-400 mt-1" x-text="currency === 'BDT' ? '≈ $' + activeUsd + ' USD' : '≈ ৳' + Number(activeBdt).toLocaleString() + ' BDT'"></div>
                                    </div>
                                    <div class="flex gap-2 mb-5">
                                        <button @click="currency='USD'" :class="currency==='USD' ? 'bg-white text-gray-900 font-black' : 'bg-gray-700 text-gray-400 hover:bg-gray-600'" class="flex-1 text-xs font-bold py-2 rounded-xl transition-all">$ USD</button>
                                        <button @click="currency='BDT'" :class="currency==='BDT' ? 'bg-white text-gray-900 font-black' : 'bg-gray-700 text-gray-400 hover:bg-gray-600'" class="flex-1 text-xs font-bold py-2 rounded-xl transition-all">৳ BDT</button>
                                    </div>
                                </div>
                                @auth
                                <a href="{{ route('client.traffic_campaign.builder') }}" class="block w-full text-center bg-[#E8470A] hover:bg-orange-600 text-white font-black py-4 px-6 rounded-2xl transition-all duration-300 text-sm shadow-lg shadow-orange-600/30">
                                    Launch This Campaign →
                                </a>
                                @else
                                <a href="{{ route('register') }}" class="block w-full text-center bg-[#E8470A] hover:bg-orange-600 text-white font-black py-4 px-6 rounded-2xl transition-all duration-300 text-sm shadow-lg shadow-orange-600/30">
                                    Sign Up Free & Launch →
                                </a>
                                <a href="{{ route('login') }}" class="block w-full text-center mt-3 text-gray-400 hover:text-white text-xs font-semibold transition-colors">
                                    Already have an account? Log In
                                </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 3: SEO BENEFITS
    ================================================ --}}
    <section class="bg-white py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-orange-500 text-xs font-bold uppercase tracking-widest">For SEO Professionals & Digital Marketers</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Why SEO Experts & Agencies Choose TrafficVai</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">Our traffic is built to improve real analytics metrics that matter to SEO campaigns—not just vanity numbers.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach([
                    ['icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6','color'=>'orange','title'=>'Improve Organic CTR Signals','desc'=>'Our Organic Search Traffic service simulates real users searching your exact target keyword on Google or Bing and clicking your result. This sends positive Click-Through Rate (CTR) engagement signals which can positively influence your search position over time.'],
                    ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','color'=>'blue','title'=>'Boost GA4 Engagement Metrics','desc'=>'Control exact visit duration and scroll behavior per campaign. Reduce your bounce rate and raise your Average Engagement Time in Google Analytics 4—all showing up as real, measurable sessions.'],
                    ['icon'=>'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064','color'=>'green','title'=>'Diversify Your Traffic Sources','desc'=>'A healthy website has balanced traffic from multiple sources. Use Direct, Social, Referral, and Organic Search together to build a natural traffic profile that looks authentic in Google Analytics.'],
                    ['icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z','color'=>'purple','title'=>'Test Funnels & Analytics Setup','desc'=>'Send targeted test traffic to validate your GA4 event tracking, conversion funnel, and landing page performance before launching a real campaign. Pause, resume, or edit any campaign from your dashboard anytime.'],
                ] as $b)
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:border-orange-200 hover:shadow-md transition-all duration-300 group flex gap-5">
                    <div class="shrink-0 w-12 h-12 bg-{{ $b['color'] }}-100 rounded-2xl flex items-center justify-center group-hover:bg-{{ $b['color'] }}-200 transition-colors">
                        <svg class="w-6 h-6 text-{{ $b['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $b['icon'] }}"/></svg>
                    </div>
                    <div>
                        <h3 class="text-gray-900 font-bold text-base mb-2">{{ $b['title'] }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $b['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 4: DIRECT vs SEARCH EXPLAINED
    ================================================ --}}
    <section class="bg-gray-50 py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-indigo-100 border border-indigo-200 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-indigo-600 text-xs font-bold uppercase tracking-widest">Two Services, One Platform</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Direct Traffic vs. Organic Search Traffic</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">Not sure which type you need? Here's a clear breakdown of how each service works.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Direct Traffic --}}
                <div class="bg-white rounded-3xl p-10 border border-orange-100 shadow-sm">
                    <div class="inline-flex items-center gap-2 bg-orange-100 text-orange-700 text-xs font-bold px-3 py-1.5 rounded-full mb-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Direct & Referral Traffic
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">Volume, Engagement & Brand Reach</h3>
                    <p class="text-gray-500 mb-6 leading-relaxed text-sm">Visitors arrive at your website directly via URL, or from social media and custom referral sources. Ideal for:</p>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-start gap-3"><span class="text-orange-500 font-black mt-0.5">✓</span> Increasing total session volume in GA4</li>
                        <li class="flex items-start gap-3"><span class="text-orange-500 font-black mt-0.5">✓</span> Improving Average Session Duration & scroll depth</li>
                        <li class="flex items-start gap-3"><span class="text-orange-500 font-black mt-0.5">✓</span> Building a natural "direct" traffic baseline</li>
                        <li class="flex items-start gap-3"><span class="text-orange-500 font-black mt-0.5">✓</span> Simulating social media referral traffic from any platform</li>
                        <li class="flex items-start gap-3"><span class="text-orange-500 font-black mt-0.5">✓</span> Targeting specific countries and devices (Mobile/Desktop)</li>
                    </ul>
                    <div class="mt-8 p-4 bg-orange-50 border border-orange-100 rounded-2xl">
                        <div class="text-xs text-orange-700 font-semibold">Pricing: 1 Point = 1 Visitor per 60 sec · 1,000 Points = $1.00 USD</div>
                    </div>
                </div>

                {{-- Search Traffic --}}
                <div class="bg-white rounded-3xl p-10 border border-indigo-100 shadow-sm">
                    <div class="inline-flex items-center gap-2 bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1.5 rounded-full mb-6">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Organic Search Traffic
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">Keyword CTR, Search Signals & Organic Growth</h3>
                    <p class="text-gray-500 mb-6 leading-relaxed text-sm">Visitors search your target keyword on Google, Bing, or Yahoo, then click your site from the results—just like a real organic visitor. Ideal for:</p>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-start gap-3"><span class="text-indigo-600 font-black mt-0.5">✓</span> Boosting Click-Through Rate (CTR) for target keywords</li>
                        <li class="flex items-start gap-3"><span class="text-indigo-600 font-black mt-0.5">✓</span> Increasing organic search session volume in GA4</li>
                        <li class="flex items-start gap-3"><span class="text-indigo-600 font-black mt-0.5">✓</span> Sending positive user engagement signals to search engines</li>
                        <li class="flex items-start gap-3"><span class="text-indigo-600 font-black mt-0.5">✓</span> Improving keyword ranking velocity alongside other SEO efforts</li>
                        <li class="flex items-start gap-3"><span class="text-indigo-600 font-black mt-0.5">✓</span> Targeting Google, Bing, or Yahoo with precise keyword control</li>
                    </ul>
                    <div class="mt-8 p-4 bg-indigo-50 border border-indigo-100 rounded-2xl">
                        <div class="text-xs text-indigo-700 font-semibold">Pricing: 1 Point = 1 Keyword Visitor per 60 sec · 1,000 Points = $1.00 USD</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 5: POINT BUNDLES
    ================================================ --}}
    <section class="bg-white py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-green-100 border border-green-200 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-green-700 text-xs font-bold uppercase tracking-widest">Pay As You Go · No Monthly Fees</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Flexible Traffic Point Bundles</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">Buy Traffic Points and use them for any campaign. Points are valid for 30 days from purchase.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($pointBundles as $bundle)
                @php $pop = $bundle['popular']; @endphp
                <div class="relative bg-white rounded-2xl p-7 border {{ $pop ? 'border-orange-400 ring-2 ring-orange-200 shadow-lg' : 'border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300' }} flex flex-col transition-all duration-300">
                    @if($pop)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="bg-gradient-to-r from-orange-500 to-red-500 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">Most Popular</span>
                    </div>
                    @endif
                    <div class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-3">{{ $bundle['name'] }} Pack</div>
                    <div class="text-2xl font-black text-gray-900 mb-1">{{ number_format($bundle['points']) }} <span class="text-sm font-bold text-indigo-600">Pts</span></div>
                    <div class="text-gray-400 text-xs mb-6">≈ {{ number_format($bundle['points']) }} visitor-minutes</div>
                    <div class="mt-auto">
                        <div class="text-2xl font-black text-gray-900">${{ number_format($bundle['usd'], 2) }}</div>
                        <div class="text-sm text-gray-400 mb-5">≈ ৳{{ number_format($bundle['usd'] * $bdtRate, 0) }} BDT</div>
                        @auth
                        <a href="{{ route('client.traffic_campaign.topup') }}" class="{{ $pop ? 'bg-[#E8470A] hover:bg-orange-600 shadow-orange-200' : 'bg-gray-900 hover:bg-gray-700' }} block w-full text-center text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 shadow-sm text-sm">
                            Buy {{ $bundle['name'] }} Pack
                        </a>
                        @else
                        <a href="{{ route('register') }}" class="{{ $pop ? 'bg-[#E8470A] hover:bg-orange-600 shadow-orange-200' : 'bg-gray-900 hover:bg-gray-700' }} block w-full text-center text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 shadow-sm text-sm">
                            Get Started →
                        </a>
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>
            <p class="text-center text-gray-400 text-xs mt-6">Need a custom amount? Use the <a href="#calculator" class="text-orange-500 hover:underline">calculator above</a> to estimate, then top up any custom amount from your dashboard.</p>
        </div>
    </section>

    {{-- ================================================
         SECTION 6: HOW IT WORKS (3 STEPS)
    ================================================ --}}
    <section class="bg-gray-50 py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-orange-500 text-xs font-bold uppercase tracking-widest">Simple 3-Step Process</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">From Sign Up to Live Traffic in Minutes</h2>
                <p class="text-gray-500 text-lg max-w-xl mx-auto">No complex setup. Create a free account, add balance, and launch your first campaign in under 3 minutes.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['step'=>'01','icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z','color'=>'orange','title'=>'Create Free Account & Top-Up','desc'=>'Sign up in 30 seconds. Add wallet balance using bKash, Nagad, Rocket, Crypto, or Credit/Debit Card. Or buy a Traffic Point Bundle directly.'],
                    ['step'=>'02','icon'=>'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4','color'=>'indigo','title'=>'Configure & Launch Campaign','desc'=>'In your Client Dashboard, enter your website URL, choose Direct or Search Traffic, set keywords, country, device type, and visit duration. Click Launch.'],
                    ['step'=>'03','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','color'=>'green','title'=>'Watch Results in GA4 Live','desc'=>'Your traffic campaign begins delivery in real-time. Open Google Analytics 4 and watch sessions, engagement time, and traffic sources update live. Pause or edit anytime.']
                ] as $step)
                <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm text-center hover:shadow-md transition-all duration-300">
                    <div class="w-14 h-14 bg-{{ $step['color'] }}-100 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-6 h-6 text-{{ $step['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg>
                    </div>
                    <div class="text-{{ $step['color'] }}-600 text-xs font-black uppercase tracking-widest mb-3">Step {{ $step['step'] }}</div>
                    <h3 class="text-gray-900 font-bold text-base mb-3">{{ $step['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-10">
                @auth
                <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center gap-2 bg-[#E8470A] hover:bg-orange-600 text-white font-black py-4 px-10 rounded-2xl transition-all duration-300 shadow-xl shadow-orange-600/30 hover:scale-105">
                    Go to Campaign Builder →
                </a>
                @else
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-[#E8470A] hover:bg-orange-600 text-white font-black py-4 px-10 rounded-2xl transition-all duration-300 shadow-xl shadow-orange-600/30 hover:scale-105">
                    Create Free Account →
                </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 7: KEY FEATURES
    ================================================ --}}
    <section class="bg-white py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-green-100 border border-green-200 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-green-700 text-xs font-bold uppercase tracking-widest">Platform Capabilities</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Everything You Need for a Successful Campaign</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach([
                    ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','color'=>'green','title'=>'GA4 & Search Console Safe','desc'=>'All visitor sessions appear naturally in Google Analytics 4. Sessions, engagement time, and acquisition source show up exactly as configured.'],
                    ['icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z','color'=>'blue','title'=>'Keyword Search Simulation','desc'=>'For Search campaigns, visitors arrive via a real keyword search on Google or Bing—just as a real organic visitor would, improving search CTR signals.'],
                    ['icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z','color'=>'orange','title'=>'Full Payment Flexibility','desc'=>'Add wallet balance using bKash, Nagad, Rocket (BDT), credit/debit cards (USD), or crypto. Convert your balance to Traffic Points instantly.'],
                    ['icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z','color'=>'purple','title'=>'Device & Country Targeting','desc'=>'Target visitors from any specific country or worldwide. Choose Mobile, Desktop, or random device mix to match your real audience profile.'],
                    ['icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15','color'=>'indigo','title'=>'Instant Pause & Resume','desc'=>'Pause your campaign instantly from your dashboard and resume with a single click. Edit visit duration, country, or device type at any time.'],
                    ['icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','color'=>'gray','title'=>'Real-Time Delivery Monitoring','desc'=>'Monitor live delivery progress from your client dashboard. Track points consumed, visits delivered, and estimated completion in real-time.'],
                ] as $f)
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:border-{{ $f['color'] }}-200 hover:bg-{{ $f['color'] }}-50/30 transition-all duration-300 flex gap-4">
                    <div class="shrink-0 w-10 h-10 bg-{{ $f['color'] }}-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-{{ $f['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/></svg>
                    </div>
                    <div>
                        <h3 class="text-gray-900 font-bold text-sm mb-1.5">{{ $f['title'] }}</h3>
                        <p class="text-gray-500 text-xs leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 8: DUAL-TAB FAQ
    ================================================ --}}
    <section class="bg-gray-50 py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-4">
                    <span class="text-orange-500 text-xs font-bold uppercase tracking-widest">Got Questions?</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Frequently Asked Questions</h2>
                <p class="text-gray-500 text-lg max-w-xl mx-auto">Everything you need to know about our Direct Traffic and Organic Search Traffic services.</p>
            </div>

            <div x-data="{ faqTab: 'direct', openIndex: null }">
                {{-- FAQ Tab Switcher --}}
                <div class="flex border-b border-gray-200 mb-8">
                    <button @click="faqTab='direct'; openIndex=null"
                        :class="faqTab==='direct' ? 'border-b-2 border-orange-500 text-orange-600 font-black' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-4 text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Direct & Referral Traffic
                    </button>
                    <button @click="faqTab='search'; openIndex=null"
                        :class="faqTab==='search' ? 'border-b-2 border-indigo-600 text-indigo-600 font-black' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-4 text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Organic Search Traffic
                    </button>
                </div>

                {{-- DIRECT FAQs --}}
                <div x-show="faqTab==='direct'" x-transition class="space-y-3">
                    @php $directFaqs = [
                        ['q'=>'What is Direct & Referral Traffic and how does it benefit my website?','a'=>'Direct traffic refers to visitors who arrive at your website by typing your URL directly, clicking a bookmark, or from a referral source like social media or another website. Our service simulates this by sending real browsing sessions to your URL from the source you specify. This increases your total session count, improves Average Engagement Time in GA4, and builds a healthy, diversified traffic profile for your domain.'],
                        ['q'=>'Is this traffic safe for Google Analytics 4 (GA4) and my website metrics?','a'=>'Yes. Each visit is delivered as a real, natural browser session that appears in your GA4 dashboard under the correct acquisition channel (Direct, Social, or Referral). Sessions include the visit duration and scroll behavior you configure, so your engagement metrics reflect the settings you choose.'],
                        ['q'=>'Can I target visitors from specific countries and devices?','a'=>'Yes. You can choose to send traffic from all countries worldwide, or target a specific country such as the United States, United Kingdom, Bangladesh, etc. You can also select the device type: Mobile, Desktop, or a random mix. All targeting options are available from your Client Dashboard when you configure your campaign.'],
                        ['q'=>'How do I control session duration and reduce bounce rate?','a'=>'When creating your campaign in the Client Dashboard, you choose the Visit Duration: 30 seconds, 1 minute, 2 minutes, or 3 minutes per visitor. Each visitor stays on your website for that duration, which directly controls the bounce rate and Average Engagement Time reported in GA4. A longer duration = lower bounce rate and higher engagement time.'],
                        ['q'=>'Can I specify custom referrer URLs like Facebook, Reddit, or my own website?','a'=>'Yes. For Referral and Social Traffic campaigns, you can enter any referral URL (e.g., https://facebook.com, https://reddit.com/r/yourtopic, or any custom domain). This traffic will then appear in GA4 under the Referral or Social acquisition source matching the URL you provided.'],
                        ['q'=>'How long does delivery take after I launch a campaign?','a'=>'Delivery begins within minutes of launching. Total delivery time depends on your visitor volume and visit duration settings. A campaign of 5,000 visitors at 60 seconds per visit typically completes within a few hours to a day, delivered at a natural rate to avoid sudden spikes.'],
                    ]; @endphp

                    @foreach($directFaqs as $i => $faq)
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-orange-200 transition-colors">
                        <button @click="openIndex === {{ $i }} ? openIndex = null : openIndex = {{ $i }}" class="w-full flex items-center justify-between p-6 text-left">
                            <span class="text-gray-900 font-semibold text-sm pr-4">{{ $faq['q'] }}</span>
                            <span class="shrink-0 text-orange-500">
                                <svg x-show="openIndex !== {{ $i }}" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <svg x-show="openIndex === {{ $i }}" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/></svg>
                            </span>
                        </button>
                        <div x-show="openIndex === {{ $i }}" x-transition class="px-6 pb-6">
                            <p class="text-gray-500 text-sm leading-relaxed border-t border-gray-100 pt-4">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- SEARCH FAQs --}}
                <div x-show="faqTab==='search'" x-transition class="space-y-3">
                    @php $searchFaqs = [
                        ['q'=>'How does Organic Search Keyword Traffic work?','a'=>'When you launch an Organic Search Traffic campaign, real browser sessions navigate to Google, Bing, or Yahoo, search for your exact target keyword, find your website in the search results, and click on it—just as a real human searcher would. The visitor then spends the configured visit duration on your website, scrolling and interacting with the page.'],
                        ['q'=>'Will these visits appear as organic search traffic in Google Analytics 4?','a'=>'Yes. Because each visitor arrives via a real search engine results page (SERP), Google Analytics 4 records these sessions under the "Organic Search" acquisition channel. The traffic source will correctly show as "google / organic", "bing / organic", etc. in your GA4 reports.'],
                        ['q'=>'How does keyword search traffic improve my Click-Through Rate (CTR) signals?','a'=>'Search engines track how often users click on results for specific keywords (Click-Through Rate). When more users search a keyword and click your website result, it sends a positive engagement signal. Our service replicates this behavior with real browser sessions, which can contribute positively to your keyword\'s CTR history alongside your other SEO efforts.'],
                        ['q'=>'Can I target specific keywords on Google or Bing?','a'=>'Yes. When setting up your campaign in the Client Dashboard, you enter the exact keyword(s) you want visitors to search before arriving at your site. You also select which search engine to use: Google, Bing, or Yahoo. Each visitor will search exactly the term you provide and click through to your website from the search results page.'],
                        ['q'=>'Why do SEO agencies and digital marketers use keyword search traffic?','a'=>'SEO professionals use keyword search traffic to strengthen CTR signals for target keywords, improve organic engagement metrics for pages they are trying to rank, and build a consistent baseline of organic search sessions in GA4. It\'s also used to test whether GA4 event tracking and conversion goals are firing correctly when traffic arrives via organic search.'],
                        ['q'=>'How long are purchased Traffic Points valid?','a'=>'All purchased Traffic Points are valid for 30 days from the date of purchase. If you do not use them within 30 days, they will expire. We recommend planning your campaigns within your active 30-day window. You can purchase additional points at any time from your Client Dashboard. There are no monthly subscription fees—you only pay for the points you buy.'],
                    ]; @endphp

                    @foreach($searchFaqs as $i => $faq)
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-indigo-200 transition-colors">
                        <button @click="openIndex === {{ $i }} ? openIndex = null : openIndex = {{ $i }}" class="w-full flex items-center justify-between p-6 text-left">
                            <span class="text-gray-900 font-semibold text-sm pr-4">{{ $faq['q'] }}</span>
                            <span class="shrink-0 text-indigo-600">
                                <svg x-show="openIndex !== {{ $i }}" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <svg x-show="openIndex === {{ $i }}" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/></svg>
                            </span>
                        </button>
                        <div x-show="openIndex === {{ $i }}" x-transition class="px-6 pb-6">
                            <p class="text-gray-500 text-sm leading-relaxed border-t border-gray-100 pt-4">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bottom CTA --}}
            <div class="mt-14 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-3xl p-10 text-center">
                <h3 class="text-white text-xl font-black mb-3">Ready to Drive Real Traffic to Your Website?</h3>
                <p class="text-gray-400 mb-8">Create a free account and launch your first campaign in minutes. No subscription, no contracts. Pay only for what you use.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center gap-2 bg-[#E8470A] hover:bg-orange-600 text-white font-black py-4 px-8 rounded-2xl transition-all duration-300 shadow-xl shadow-orange-600/30 hover:scale-105">
                        Launch Your Campaign Now →
                    </a>
                    @else
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-[#E8470A] hover:bg-orange-600 text-white font-black py-4 px-8 rounded-2xl transition-all duration-300 shadow-xl shadow-orange-600/30 hover:scale-105">
                        Create Free Account →
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300">
                        Log In to Dashboard
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <x-frontend-footer />
    <x-currency-script />
</body>
</html>
