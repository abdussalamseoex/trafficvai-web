<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-seo-tags />
    <title>Website Traffic Services — Real Visitors, Real Analytics | TrafficVai</title>
    <meta name="description" content="Drive high-retention Direct, Referral & Organic Search traffic to your website. GA4-verified visitors. Fully customizable. Loved by SEO experts worldwide.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-hero { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); }
        .card-glow { box-shadow: 0 0 40px rgba(99,102,241,0.08); }
        .tab-active { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 4px 20px rgba(99,102,241,0.35); }
        .tab-inactive { background: rgba(255,255,255,0.05); color: #94a3b8; }
        .faq-tab-active { border-bottom: 3px solid #f97316; color: #f97316; }
        .range-thumb::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; border-radius: 50%; background: #6366f1; cursor: pointer; box-shadow: 0 0 0 4px rgba(99,102,241,0.2); }
        .range-track { -webkit-appearance: none; height: 6px; border-radius: 3px; background: linear-gradient(to right, #6366f1 var(--val, 50%), #334155 var(--val, 50%)); }
        .popular-badge { background: linear-gradient(135deg, #f97316, #ef4444); }
        .section-divider { background: linear-gradient(90deg, transparent, rgba(99,102,241,0.3), transparent); }
        .feature-icon-wrap { background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(79,70,229,0.05)); }
        .step-connector::before { content: ''; position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, #6366f1, #4f46e5); transform: translateX(-50%); }
    </style>
</head>
<body class="font-sans antialiased bg-[#0f172a] text-white">
    <!-- Navigation -->
    <x-frontend-header />

    {{-- ================================================
         SECTION 1: HERO
    ================================================ --}}
    <section class="gradient-hero relative overflow-hidden pt-20 pb-32">
        <!-- Background blobs -->
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-orange-500/15 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center gap-2 bg-indigo-500/10 border border-indigo-500/30 rounded-full px-4 py-2 text-sm text-indigo-300 font-semibold mb-8">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                Live Campaign Delivery · GA4 Verified Traffic
            </div>

            <h1 class="text-4xl md:text-6xl font-black leading-tight mb-6">
                High-Retention Website Traffic<br class="hidden md:block">
                <span class="bg-gradient-to-r from-orange-400 to-orange-600 bg-clip-text text-transparent">Direct &amp; Organic Search Visitors</span><br>
                That Show Up in Analytics
            </h1>
            <p class="text-lg md:text-xl text-slate-300 max-w-3xl mx-auto mb-10 leading-relaxed">
                Drive real, targeted visitors to your website—whether as high-volume <strong class="text-white">Direct/Referral Traffic</strong> to boost your overall engagement metrics, or as <strong class="text-white">Organic Search Traffic</strong> from Google or Bing using specific keywords to improve your CTR and search visibility signals.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#calculator" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 shadow-lg shadow-indigo-500/30 text-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Calculate Traffic Cost
                </a>
                @auth
                <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 shadow-lg shadow-orange-500/30 text-lg">
                    Launch Campaign →
                </a>
                @else
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 shadow-lg shadow-orange-500/30 text-lg">
                    Get Started Free →
                </a>
                @endauth
            </div>

            <!-- Trust Stats -->
            <div class="grid grid-cols-3 gap-8 max-w-2xl mx-auto mt-16 pt-12 border-t border-white/10">
                <div>
                    <div class="text-3xl font-black text-white">100%</div>
                    <div class="text-sm text-slate-400 mt-1">GA4 Verified</div>
                </div>
                <div>
                    <div class="text-3xl font-black text-white">30s</div>
                    <div class="text-sm text-slate-400 mt-1">Setup Time</div>
                </div>
                <div>
                    <div class="text-3xl font-black text-white">Real-time</div>
                    <div class="text-sm text-slate-400 mt-1">Dashboard Control</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 2: DUAL-TAB INTERACTIVE CALCULATOR
    ================================================ --}}
    @php
        $bdtRate = $bdtRate ?? 120;
    @endphp
    <section id="calculator" class="bg-[#0f172a] py-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="inline-block bg-indigo-500/10 border border-indigo-500/25 text-indigo-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">Live Estimator Tool</div>
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Calculate Your Traffic Cost Instantly</h2>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto">Adjust the sliders and options below to estimate the Traffic Points and cost for your campaign. Switch tabs to compare Direct and Organic Search traffic pricing.</p>
            </div>

            <div x-data="{
                activeCalc: 'direct',
                /* Direct Traffic */
                dVisitors: 5000,
                dDuration: 60,
                dCountry: 'worldwide',
                /* Search Traffic */
                sVisitors: 3000,
                sDuration: 60,
                sEngine: 'google',
                sCountry: 'worldwide',
                /* Currency */
                currency: localStorage.getItem('selected_currency') || 'BDT',
                bdtRate: {{ $bdtRate }},
                get dPoints() {
                    const blocks = Math.ceil(this.dDuration / 60);
                    return this.dVisitors * blocks;
                },
                get sPoints() {
                    const blocks = Math.ceil(this.sDuration / 60);
                    return this.sVisitors * blocks;
                },
                get activePoints() {
                    return this.activeCalc === 'direct' ? this.dPoints : this.sPoints;
                },
                get activeUsd() {
                    return (this.activePoints / 1000).toFixed(2);
                },
                get activeBdt() {
                    return (this.activePoints / 1000 * this.bdtRate).toFixed(0);
                },
                get displayCost() {
                    return this.currency === 'BDT'
                        ? '৳' + Number(this.activeBdt).toLocaleString()
                        : '$' + this.activeUsd;
                }
            }" class="bg-[#1e293b] rounded-3xl border border-slate-700/50 overflow-hidden card-glow">

                <!-- Tab Switcher -->
                <div class="grid grid-cols-2 gap-0 border-b border-slate-700/50">
                    <button @click="activeCalc='direct'"
                        :class="activeCalc==='direct' ? 'bg-indigo-600/20 border-b-2 border-indigo-500 text-white' : 'text-slate-400 hover:text-slate-200'"
                        class="flex items-center justify-center gap-3 py-5 px-6 font-bold text-sm transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Direct &amp; Referral Traffic
                    </button>
                    <button @click="activeCalc='search'"
                        :class="activeCalc==='search' ? 'bg-orange-500/20 border-b-2 border-orange-500 text-white' : 'text-slate-400 hover:text-slate-200'"
                        class="flex items-center justify-center gap-3 py-5 px-6 font-bold text-sm transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Organic Search Traffic
                    </button>
                </div>

                <div class="p-8 md:p-12">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
                        <!-- Controls Column -->
                        <div class="lg:col-span-3 space-y-8">

                            <!-- Direct Traffic Controls -->
                            <div x-show="activeCalc==='direct'" x-transition class="space-y-8">
                                <!-- Visitors -->
                                <div>
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="text-slate-300 font-semibold text-sm">Number of Visitors</label>
                                        <span class="text-white font-black text-lg" x-text="Number(dVisitors).toLocaleString()"></span>
                                    </div>
                                    <input type="range" min="500" max="100000" step="500" x-model="dVisitors"
                                        class="w-full h-2 rounded-full cursor-pointer accent-indigo-500"
                                        style="appearance: auto;">
                                    <div class="flex justify-between text-xs text-slate-500 mt-1"><span>500</span><span>100,000+</span></div>
                                </div>
                                <!-- Duration -->
                                <div>
                                    <label class="text-slate-300 font-semibold text-sm block mb-3">Visit Duration (per visitor)</label>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach([30=>'30 sec', 60=>'1 min', 120=>'2 min', 180=>'3 min'] as $val => $label)
                                        <button type="button" @click="dDuration={{ $val }}"
                                            :class="dDuration==={{ $val }} ? 'bg-indigo-600 text-white border-indigo-500 shadow-lg shadow-indigo-500/30' : 'bg-slate-800 text-slate-400 border-slate-600 hover:border-slate-500'"
                                            class="py-2 px-3 rounded-xl border text-xs font-bold transition-all duration-200">{{ $label }}</button>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Country Targeting -->
                                <div>
                                    <label class="text-slate-300 font-semibold text-sm block mb-3">Country Targeting</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" @click="dCountry='worldwide'"
                                            :class="dCountry==='worldwide' ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-slate-800 text-slate-400 border-slate-600'"
                                            class="py-2.5 px-4 rounded-xl border text-xs font-bold transition-all">🌍 Worldwide (All)</button>
                                        <button type="button" @click="dCountry='targeted'"
                                            :class="dCountry==='targeted' ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-slate-800 text-slate-400 border-slate-600'"
                                            class="py-2.5 px-4 rounded-xl border text-xs font-bold transition-all">🎯 Specific Country</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Traffic Controls -->
                            <div x-show="activeCalc==='search'" x-transition class="space-y-8">
                                <!-- Visitors -->
                                <div>
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="text-slate-300 font-semibold text-sm">Keyword Search Visitors</label>
                                        <span class="text-white font-black text-lg" x-text="Number(sVisitors).toLocaleString()"></span>
                                    </div>
                                    <input type="range" min="500" max="50000" step="500" x-model="sVisitors"
                                        class="w-full h-2 rounded-full cursor-pointer accent-orange-500"
                                        style="appearance: auto;">
                                    <div class="flex justify-between text-xs text-slate-500 mt-1"><span>500</span><span>50,000+</span></div>
                                </div>
                                <!-- Search Engine -->
                                <div>
                                    <label class="text-slate-300 font-semibold text-sm block mb-3">Search Engine</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach(['google'=>'Google', 'bing'=>'Bing', 'yahoo'=>'Yahoo'] as $k=>$v)
                                        <button type="button" @click="sEngine='{{ $k }}'"
                                            :class="sEngine==='{{ $k }}' ? 'bg-orange-500 text-white border-orange-500 shadow-lg shadow-orange-500/30' : 'bg-slate-800 text-slate-400 border-slate-600 hover:border-slate-500'"
                                            class="py-2 px-3 rounded-xl border text-xs font-bold transition-all duration-200">{{ $v }}</button>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Duration -->
                                <div>
                                    <label class="text-slate-300 font-semibold text-sm block mb-3">Visit Duration (per visitor)</label>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach([30=>'30 sec', 60=>'1 min', 120=>'2 min', 180=>'3 min'] as $val => $label)
                                        <button type="button" @click="sDuration={{ $val }}"
                                            :class="sDuration==={{ $val }} ? 'bg-orange-500 text-white border-orange-500 shadow-lg shadow-orange-500/30' : 'bg-slate-800 text-slate-400 border-slate-600 hover:border-slate-500'"
                                            class="py-2 px-3 rounded-xl border text-xs font-bold transition-all duration-200">{{ $label }}</button>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Country Targeting -->
                                <div>
                                    <label class="text-slate-300 font-semibold text-sm block mb-3">Country Targeting</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" @click="sCountry='worldwide'"
                                            :class="sCountry==='worldwide' ? 'bg-orange-500 text-white border-orange-500' : 'bg-slate-800 text-slate-400 border-slate-600'"
                                            class="py-2.5 px-4 rounded-xl border text-xs font-bold transition-all">🌍 Worldwide (All)</button>
                                        <button type="button" @click="sCountry='targeted'"
                                            :class="sCountry==='targeted' ? 'bg-orange-500 text-white border-orange-500' : 'bg-slate-800 text-slate-400 border-slate-600'"
                                            class="py-2.5 px-4 rounded-xl border text-xs font-bold transition-all">🎯 Specific Country</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Point Calculation Logic Info -->
                            <div class="bg-slate-800/50 rounded-2xl p-4 border border-slate-700/50">
                                <p class="text-xs text-slate-400 leading-relaxed">
                                    <span class="text-indigo-400 font-bold">How Points Work:</span>
                                    Each visitor consumes <strong class="text-white">1 Traffic Point per 60 seconds</strong> of visit duration. A 2-minute visit = 2 Points per visitor. 1,000 Points = $1.00 USD.
                                </p>
                            </div>
                        </div>

                        <!-- Live Output Panel -->
                        <div class="lg:col-span-2">
                            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-7 border border-slate-600/50 h-full flex flex-col justify-between">
                                <div>
                                    <div class="text-xs text-slate-400 uppercase tracking-widest font-bold mb-6">Estimated Cost</div>

                                    <!-- Points Required -->
                                    <div class="mb-6">
                                        <div class="text-slate-400 text-xs mb-1">Traffic Points Required</div>
                                        <div class="text-4xl font-black text-white" x-text="Number(activePoints).toLocaleString() + ' Pts'"></div>
                                    </div>

                                    <!-- Cost -->
                                    <div class="bg-indigo-600/15 border border-indigo-500/25 rounded-2xl p-5 mb-6">
                                        <div class="text-xs text-indigo-300 mb-1">Total Cost</div>
                                        <div class="text-3xl font-black text-white" x-text="displayCost"></div>
                                        <div class="text-xs text-slate-400 mt-1" x-text="currency === 'BDT' ? '≈ $' + activeUsd + ' USD' : '≈ ৳' + Number(activeBdt).toLocaleString() + ' BDT'"></div>
                                    </div>

                                    <!-- Currency Toggle -->
                                    <div class="flex gap-2 mb-6">
                                        <button @click="currency='USD'" :class="currency==='USD' ? 'bg-white text-slate-900' : 'bg-slate-700 text-slate-400'" class="flex-1 text-xs font-bold py-2 rounded-xl transition-all">$ USD</button>
                                        <button @click="currency='BDT'" :class="currency==='BDT' ? 'bg-white text-slate-900' : 'bg-slate-700 text-slate-400'" class="flex-1 text-xs font-bold py-2 rounded-xl transition-all">৳ BDT</button>
                                    </div>
                                </div>

                                <!-- CTA -->
                                @auth
                                <a href="{{ route('client.traffic_campaign.builder') }}" class="block w-full text-center bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 shadow-lg shadow-orange-500/25 text-sm">
                                    Launch This Campaign Now →
                                </a>
                                @else
                                <a href="{{ route('register') }}" class="block w-full text-center bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-300 shadow-lg shadow-orange-500/25 text-sm">
                                    Sign Up Free & Launch →
                                </a>
                                <a href="{{ route('login') }}" class="block w-full text-center mt-3 text-slate-400 hover:text-white text-xs font-semibold transition-colors">
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
         SECTION 3: WHY SEO EXPERTS CHOOSE US (Benefits)
    ================================================ --}}
    <section class="bg-[#0d1526] py-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="inline-block bg-orange-500/10 border border-orange-500/25 text-orange-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">For SEO Professionals & Digital Marketers</div>
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Why SEO Experts & Agencies Choose TrafficVai</h2>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto">Our traffic is specifically designed to improve real analytics metrics that matter to SEO campaigns—not just vanity numbers.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Benefit 1 -->
                <div class="bg-[#1e293b] rounded-2xl p-8 border border-slate-700/50 hover:border-indigo-500/30 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-indigo-500/15 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-indigo-500/25 transition-colors">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-3">Improve Organic CTR Signals</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Our Organic Search Traffic service simulates real users searching your exact target keyword on Google or Bing and clicking your result. This sends positive Click-Through Rate (CTR) engagement signals which can positively influence your search position over time.</p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-[#1e293b] rounded-2xl p-8 border border-slate-700/50 hover:border-indigo-500/30 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-green-500/15 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-green-500/25 transition-colors">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-3">Boost GA4 Engagement Metrics</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Control exact visit duration and scroll behavior per campaign. Reduce your bounce rate and raise your Average Engagement Time in Google Analytics 4. Choose from 30 seconds to 3+ minutes per visit—all showing up as real, measurable sessions in your GA4 dashboard.</p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-[#1e293b] rounded-2xl p-8 border border-slate-700/50 hover:border-indigo-500/30 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-purple-500/15 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-purple-500/25 transition-colors">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-3">Diversify Your Traffic Sources</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">A healthy website has balanced traffic from multiple sources. Use our Direct, Social, Custom Referrer, and Organic Search options together to build a natural, diversified traffic profile that looks authentic in Google Analytics and strengthens your overall domain authority signals.</p>
                </div>

                <!-- Benefit 4 -->
                <div class="bg-[#1e293b] rounded-2xl p-8 border border-slate-700/50 hover:border-indigo-500/30 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-orange-500/15 rounded-2xl flex items-center justify-center mb-5 group-hover:bg-orange-500/25 transition-colors">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <h3 class="text-white font-bold text-lg mb-3">Test Funnels & Analytics Setup</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">Before launching a real campaign, send targeted test traffic to validate your GA4 event tracking, conversion funnel, and landing page performance. Instantly pause, resume, or edit any campaign from your dashboard in real-time without losing your configuration.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 4: DIRECT vs SEARCH — SIMPLE EXPLANATION
    ================================================ --}}
    <section class="bg-[#0f172a] py-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="inline-block bg-slate-700/50 border border-slate-600 text-slate-300 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">Two Services, One Platform</div>
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Direct Traffic vs. Organic Search Traffic</h2>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto">Not sure which type you need? Here's a clear breakdown of how each service works and what it achieves.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Direct Traffic Card -->
                <div class="bg-[#1e293b] rounded-3xl p-10 border border-indigo-500/20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 bg-indigo-500/15 text-indigo-300 text-xs font-bold px-3 py-1.5 rounded-full mb-6">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Direct & Referral Traffic
                        </div>
                        <h3 class="text-2xl font-black text-white mb-4">Volume, Engagement & Brand Reach</h3>
                        <p class="text-slate-400 mb-6 leading-relaxed">Visitors arrive at your website directly via its URL or from social media and referral sources (like Reddit, Twitter, or custom websites you specify). This type of traffic is ideal for:</p>
                        <ul class="space-y-3 text-sm text-slate-300">
                            <li class="flex items-start gap-3"><span class="text-indigo-400 mt-0.5">✓</span> Increasing total session volume in GA4</li>
                            <li class="flex items-start gap-3"><span class="text-indigo-400 mt-0.5">✓</span> Improving Average Session Duration and scroll depth</li>
                            <li class="flex items-start gap-3"><span class="text-indigo-400 mt-0.5">✓</span> Building a natural "direct" traffic baseline for new websites</li>
                            <li class="flex items-start gap-3"><span class="text-indigo-400 mt-0.5">✓</span> Simulating social media referral traffic from any platform</li>
                            <li class="flex items-start gap-3"><span class="text-indigo-400 mt-0.5">✓</span> Targeting specific countries and device types (Mobile/Desktop)</li>
                        </ul>
                        <div class="mt-8 p-4 bg-indigo-600/10 border border-indigo-500/20 rounded-2xl">
                            <div class="text-xs text-indigo-300 font-semibold">Pricing: 1 Point = 1 Visitor per 60 sec · 1,000 Points = $1.00 USD</div>
                        </div>
                    </div>
                </div>

                <!-- Search Traffic Card -->
                <div class="bg-[#1e293b] rounded-3xl p-10 border border-orange-500/20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-orange-500/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 bg-orange-500/15 text-orange-300 text-xs font-bold px-3 py-1.5 rounded-full mb-6">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Organic Search Traffic
                        </div>
                        <h3 class="text-2xl font-black text-white mb-4">Keyword CTR, Search Signals & Organic Growth</h3>
                        <p class="text-slate-400 mb-6 leading-relaxed">Visitors first search for your specific keyword on Google, Bing, or Yahoo, then click your website from the search results—just like a real organic visitor. This service is ideal for:</p>
                        <ul class="space-y-3 text-sm text-slate-300">
                            <li class="flex items-start gap-3"><span class="text-orange-400 mt-0.5">✓</span> Boosting Click-Through Rate (CTR) for target keywords</li>
                            <li class="flex items-start gap-3"><span class="text-orange-400 mt-0.5">✓</span> Increasing organic search session volume in GA4</li>
                            <li class="flex items-start gap-3"><span class="text-orange-400 mt-0.5">✓</span> Sending positive user engagement signals to search engines</li>
                            <li class="flex items-start gap-3"><span class="text-orange-400 mt-0.5">✓</span> Improving keyword ranking velocity alongside other SEO efforts</li>
                            <li class="flex items-start gap-3"><span class="text-orange-400 mt-0.5">✓</span> Targeting Google, Bing, or Yahoo with precise keyword control</li>
                        </ul>
                        <div class="mt-8 p-4 bg-orange-600/10 border border-orange-500/20 rounded-2xl">
                            <div class="text-xs text-orange-300 font-semibold">Pricing: 1 Point = 1 Keyword Visitor per 60 sec · 1,000 Points = $1.00 USD</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 5: POINT BUNDLES
    ================================================ --}}
    <section class="bg-[#0d1526] py-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="inline-block bg-green-500/10 border border-green-500/25 text-green-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">Pay As You Go · No Monthly Fees</div>
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Flexible Traffic Point Bundles</h2>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto">Buy a bundle of Traffic Points and use them for any campaign type—Direct, Search, or both. Points are valid for 30 days and never expire before that.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($pointBundles as $bundle)
                @php $isPopular = $bundle['popular']; @endphp
                <div class="relative bg-[#1e293b] rounded-2xl p-7 border {{ $isPopular ? 'border-orange-500/60 ring-2 ring-orange-500/25' : 'border-slate-700/50' }} flex flex-col hover:border-indigo-500/40 transition-all duration-300">
                    @if($isPopular)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="popular-badge text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">Most Popular</span>
                    </div>
                    @endif
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-3">{{ $bundle['name'] }} Pack</div>
                    <div class="text-3xl font-black text-white mb-1">{{ number_format($bundle['points']) }} <span class="text-base font-bold text-indigo-400">Pts</span></div>
                    <div class="text-slate-400 text-xs mb-6">≈ {{ number_format($bundle['points']) }} visitor-minutes of traffic</div>

                    <div class="mt-auto">
                        <div class="text-2xl font-black text-white">${{ number_format($bundle['usd'], 2) }}</div>
                        <div class="text-sm text-slate-400 mb-5">≈ ৳{{ number_format($bundle['usd'] * $bdtRate, 0) }} BDT</div>

                        @auth
                        <a href="{{ route('client.traffic_campaign.topup') }}" class="{{ $isPopular ? 'bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 shadow-orange-500/25' : 'bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 shadow-indigo-500/25' }} block w-full text-center text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 shadow-lg text-sm">
                            Buy {{ $bundle['name'] }} Pack
                        </a>
                        @else
                        <a href="{{ route('register') }}" class="{{ $isPopular ? 'bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 shadow-orange-500/25' : 'bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 shadow-indigo-500/25' }} block w-full text-center text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 shadow-lg text-sm">
                            Get Started →
                        </a>
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>
            <p class="text-center text-slate-500 text-xs mt-6">Need more? Use our <a href="#calculator" class="text-indigo-400 hover:underline">calculator above</a> to estimate a custom amount. You can also top up any custom amount from your dashboard.</p>
        </div>
    </section>

    {{-- ================================================
         SECTION 6: HOW IT WORKS (3 STEPS)
    ================================================ --}}
    <section class="bg-[#0f172a] py-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="inline-block bg-slate-700/50 border border-slate-600 text-slate-300 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">Simple 3-Step Process</div>
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4">From Sign Up to Live Traffic in Minutes</h2>
                <p class="text-slate-400 text-lg max-w-xl mx-auto">No complex setup. No waiting. Create a free account, add balance, and launch your first campaign in under 3 minutes.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-4 relative">
                <!-- Connecting line (desktop) -->
                <div class="hidden md:block absolute top-14 left-1/6 right-1/6 h-0.5 bg-gradient-to-r from-indigo-600 via-purple-500 to-orange-500 opacity-30"></div>

                @foreach([
                    ['step'=>'01','icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z','color'=>'indigo','title'=>'Create Free Account & Top-Up','desc'=>'Sign up in 30 seconds. Add wallet balance using bKash, Nagad, Rocket, Crypto, or Credit/Debit Card. Or buy a Traffic Point Bundle directly.'],
                    ['step'=>'02','icon'=>'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4','color'=>'purple','title'=>'Configure & Launch Campaign','desc'=>'In your Client Dashboard, enter your website URL, choose Direct or Search Traffic, set your target keywords, country, device type, and visit duration. Click Launch.'],
                    ['step'=>'03','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','color'=>'orange','title'=>'Watch Results in GA4 Live','desc'=>'Your traffic campaign begins delivery in real-time. Open Google Analytics 4 and watch sessions, engagement time, and traffic sources update live. Pause or edit anytime from your dashboard.']
                ] as $step)
                <div class="relative bg-[#1e293b] rounded-2xl p-8 border border-slate-700/50 text-center">
                    <div class="w-16 h-16 bg-{{ $step['color'] }}-500/15 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-7 h-7 text-{{ $step['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/></svg>
                    </div>
                    <div class="text-{{ $step['color'] }}-500 text-xs font-black uppercase tracking-widest mb-3">Step {{ $step['step'] }}</div>
                    <h3 class="text-white font-bold text-base mb-3">{{ $step['title'] }}</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-10">
                @auth
                <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold py-4 px-10 rounded-2xl transition-all duration-300 shadow-lg shadow-indigo-500/25">
                    Go to Campaign Builder →
                </a>
                @else
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold py-4 px-10 rounded-2xl transition-all duration-300 shadow-lg shadow-indigo-500/25">
                    Create Free Account →
                </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 7: KEY FEATURES & SAFETY
    ================================================ --}}
    <section class="bg-[#0d1526] py-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="inline-block bg-green-500/10 border border-green-500/25 text-green-400 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">Platform Capabilities</div>
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Everything You Need to Run a Successful Campaign</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach([
                    ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','color'=>'green','title'=>'GA4 & Search Console Safe','desc'=>'All visitor sessions appear naturally in Google Analytics 4. Sessions, engagement time, and acquisition source show up exactly as configured.'],
                    ['icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z','color'=>'blue','title'=>'Keyword Search Simulation','desc'=>'For Search campaigns, visitors arrive via a real keyword search on Google or Bing—just as a real organic visitor would, improving search CTR signals.'],
                    ['icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z','color'=>'orange','title'=>'Full Payment Flexibility','desc'=>'Add wallet balance using bKash, Nagad, Rocket (BDT), major credit/debit cards (USD), or crypto. Convert your balance to Traffic Points instantly.'],
                    ['icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z','color'=>'purple','title'=>'Device & Country Targeting','desc'=>'Target visitors from any specific country or worldwide. Choose Mobile, Desktop, or random device mix to match your real audience profile.'],
                    ['icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15','color'=>'indigo','title'=>'Instant Pause & Resume','desc'=>'Need to stop your campaign? Pause it instantly from your dashboard. Resume with a single click. Edit visit duration, country, or device type mid-campaign.'],
                    ['icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','color'=>'teal','title'=>'Real-Time Delivery Monitoring','desc'=>'Monitor live delivery progress from your client dashboard. Track points consumed, visits delivered, and estimated completion time in real-time.'],
                ] as $feature)
                <div class="bg-[#1e293b] rounded-2xl p-6 border border-slate-700/50 hover:border-{{ $feature['color'] }}-500/30 transition-all duration-300 flex gap-4">
                    <div class="shrink-0 w-10 h-10 bg-{{ $feature['color'] }}-500/15 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-{{ $feature['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-sm mb-1.5">{{ $feature['title'] }}</h3>
                        <p class="text-slate-400 text-xs leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================================================
         SECTION 8: DUAL-TAB FAQ
    ================================================ --}}
    <section class="bg-[#0f172a] py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="inline-block bg-slate-700/50 border border-slate-600 text-slate-300 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full mb-4">Got Questions?</div>
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Frequently Asked Questions</h2>
                <p class="text-slate-400 text-lg max-w-xl mx-auto">Everything you need to know about our Direct Traffic and Organic Search Traffic services.</p>
            </div>

            <div x-data="{ faqTab: 'direct', openIndex: null }">
                <!-- FAQ Tab Switcher -->
                <div class="flex border-b border-slate-700 mb-10">
                    <button @click="faqTab='direct'; openIndex=null"
                        :class="faqTab==='direct' ? 'border-b-2 border-indigo-500 text-white' : 'text-slate-500 hover:text-slate-300'"
                        class="flex-1 py-4 font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Direct & Referral Traffic
                    </button>
                    <button @click="faqTab='search'; openIndex=null"
                        :class="faqTab==='search' ? 'border-b-2 border-orange-500 text-white' : 'text-slate-500 hover:text-slate-300'"
                        class="flex-1 py-4 font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Organic Search Traffic
                    </button>
                </div>

                <!-- DIRECT TRAFFIC FAQs -->
                <div x-show="faqTab==='direct'" x-transition class="space-y-3">
                    @php $directFaqs = [
                        ['q'=>'What is Direct & Referral Traffic and how does it benefit my website?', 'a'=>'Direct traffic refers to visitors who arrive at your website by typing your URL directly, clicking a bookmark, or from a referral source like social media or another website. Our service simulates this by sending real browsing sessions to your URL from the source you specify. This helps increase your total session count, improves Average Engagement Time in GA4, and builds a healthy, diversified traffic profile for your domain.'],
                        ['q'=>'Is this traffic safe for Google Analytics 4 (GA4) and my website metrics?', 'a'=>'Yes. Each visit is delivered as a real, natural browser session that appears in your GA4 dashboard under the correct acquisition channel (Direct, Social, or Referral). Sessions include the visit duration and scrolling behavior you configure, so your engagement metrics reflect the settings you choose. We do not use techniques that inflate false data—all sessions are delivered via real browser interactions.'],
                        ['q'=>'Can I target visitors from specific countries and devices?', 'a'=>'Yes. You can choose to send traffic from all countries worldwide (random geo), or you can target a specific country such as the United States, United Kingdom, Bangladesh, etc. You can also select the device type: Mobile, Desktop, or a random mix. All targeting options are available directly from your Client Dashboard when you configure your campaign.'],
                        ['q'=>'How do I control the session duration and bounce rate?', 'a'=>'When creating your campaign in the Client Dashboard, you choose the Visit Duration: 30 seconds, 1 minute, 2 minutes, or 3 minutes per visitor. Each visitor stays on your website for that duration, which directly controls the bounce rate and Average Engagement Time reported in GA4. A longer duration = lower bounce rate and higher engagement time.'],
                        ['q'=>'Can I specify custom referrer URLs like Facebook, Reddit, or my own website?', 'a'=>'Yes. For Referral and Social Traffic campaigns, you can enter any referral URL (e.g., https://facebook.com, https://reddit.com/r/yourtopic, or any custom domain). This traffic will then show up in GA4 under the Referral or Social acquisition source matching the URL you provided.'],
                        ['q'=>'How long does delivery take after I launch a campaign?', 'a'=>'Delivery begins within minutes of launching. The total delivery time depends on your visitor volume and visit duration settings. A campaign of 5,000 visitors at 60 seconds per visit typically completes within a few hours to a day, delivered continuously at a natural rate to avoid sudden traffic spikes.'],
                    ]; @endphp

                    @foreach($directFaqs as $i => $faq)
                    <div class="bg-[#1e293b] rounded-2xl border border-slate-700/50 overflow-hidden">
                        <button @click="openIndex === {{ $i }} ? openIndex = null : openIndex = {{ $i }}" class="w-full flex items-center justify-between p-6 text-left">
                            <span class="text-white font-semibold text-sm pr-4">{{ $faq['q'] }}</span>
                            <span class="shrink-0 text-indigo-400">
                                <svg x-show="openIndex !== {{ $i }}" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <svg x-show="openIndex === {{ $i }}" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/></svg>
                            </span>
                        </button>
                        <div x-show="openIndex === {{ $i }}" x-transition class="px-6 pb-6">
                            <p class="text-slate-400 text-sm leading-relaxed border-t border-slate-700/50 pt-4">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- ORGANIC SEARCH TRAFFIC FAQs -->
                <div x-show="faqTab==='search'" x-transition class="space-y-3">
                    @php $searchFaqs = [
                        ['q'=>'How does Organic Search Keyword Traffic work?', 'a'=>'When you launch an Organic Search Traffic campaign, our system opens real browser sessions, navigates to Google, Bing, or Yahoo, searches for your exact target keyword, finds your website in the search results, and clicks on it—just as a real human searcher would. The visitor then spends the configured visit duration on your website, scrolling and interacting with the page.'],
                        ['q'=>'Will these visits appear as organic search traffic in Google Analytics 4?', 'a'=>'Yes. Because each visitor arrives via a real search engine results page (SERP), Google Analytics 4 will record these sessions under the "Organic Search" acquisition channel. The keyword may appear as "(not provided)" in GA4 (as is the case for most organic search visits due to SSL encryption), but the traffic source will correctly show as "google / organic", "bing / organic", etc.'],
                        ['q'=>'How does keyword search traffic improve my Click-Through Rate (CTR) signals?', 'a'=>'Search engines like Google track how often users click on results for specific keywords (Click-Through Rate). When more users search a keyword and click your website result, it sends a positive engagement signal to the search engine. Our service replicates this behavior with real browser sessions, which can contribute positively to your keyword\'s CTR history alongside your other SEO efforts.'],
                        ['q'=>'Can I target specific keywords on Google or Bing?', 'a'=>'Yes. When setting up your campaign in the Client Dashboard, you enter the exact keyword(s) you want visitors to search before arriving at your site. You can also select which search engine to use: Google, Bing, or Yahoo. Each keyword visitor will search exactly the term you provide and click through to your website from the search results page.'],
                        ['q'=>'Why do SEO agencies and digital marketers use keyword search traffic?', 'a'=>'SEO professionals use keyword search traffic to strengthen CTR signals for specific target keywords, improve the organic engagement metrics for pages they are trying to rank, and build a consistent baseline of organic search sessions in GA4. It\'s also used to test whether GA4 event tracking and conversion goals are firing correctly when traffic arrives via organic search, without waiting weeks for real organic volume to grow.'],
                        ['q'=>'How long are purchased Traffic Points valid?', 'a'=>'All purchased Traffic Points are valid for 30 days from the date of purchase. If you do not use them within 30 days, they will expire. We recommend planning your campaigns within your active 30-day window. You can purchase additional points at any time from your Client Dashboard. There are no monthly subscription fees—you only pay for the points you buy.'],
                    ]; @endphp

                    @foreach($searchFaqs as $i => $faq)
                    <div class="bg-[#1e293b] rounded-2xl border border-slate-700/50 overflow-hidden">
                        <button @click="openIndex === {{ $i }} ? openIndex = null : openIndex = {{ $i }}" class="w-full flex items-center justify-between p-6 text-left">
                            <span class="text-white font-semibold text-sm pr-4">{{ $faq['q'] }}</span>
                            <span class="shrink-0 text-orange-400">
                                <svg x-show="openIndex !== {{ $i }}" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <svg x-show="openIndex === {{ $i }}" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/></svg>
                            </span>
                        </button>
                        <div x-show="openIndex === {{ $i }}" x-transition class="px-6 pb-6">
                            <p class="text-slate-400 text-sm leading-relaxed border-t border-slate-700/50 pt-4">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Still have questions CTA -->
            <div class="mt-14 bg-gradient-to-r from-indigo-900/40 to-purple-900/40 border border-indigo-500/20 rounded-3xl p-10 text-center">
                <h3 class="text-white text-xl font-black mb-3">Still Have Questions?</h3>
                <p class="text-slate-400 mb-6">Our support team is available to help you plan the right traffic strategy for your website.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                    <a href="{{ route('client.traffic_campaign.builder') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-bold py-3 px-8 rounded-2xl transition-all duration-300">
                        Launch Your Campaign Now →
                    </a>
                    @else
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-bold py-3 px-8 rounded-2xl transition-all duration-300">
                        Create Free Account →
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white font-bold py-3 px-8 rounded-2xl transition-all duration-300">
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
