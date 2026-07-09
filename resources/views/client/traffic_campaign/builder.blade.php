<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-[#0A0D14] text-gray-900 dark:text-gray-100 py-12 relative overflow-hidden transition-colors duration-300">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-10 left-1/4 w-96 h-96 bg-brand/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 right-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b border-gray-200 dark:border-gray-800/80 pb-8">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20">Core Automation Engine</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">Smart Auto-Convert & Deduction</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">Traffic Campaign Builder</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm sm:text-base">Launch Direct or Google Search organic traffic with full core engine features.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('client.traffic_campaign.topup') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm shadow transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Buy Points & History
                    </a>
                    <a href="{{ route('client.traffic_campaign.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-300 font-semibold text-sm border border-gray-300 dark:border-gray-800 transition">
                        My Campaigns
                    </a>
                    <a href="{{ route('client.traffic.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-300 font-semibold text-sm border border-gray-300 dark:border-gray-800 transition">
                        Back to Packages
                    </a>
                </div>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-300 dark:border-red-500/30 text-red-700 dark:text-red-400 font-bold">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-300 dark:border-red-500/30 text-red-700 dark:text-red-400 font-bold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('client.traffic_campaign.launch') }}" method="POST" id="campaignForm">
                @csrf
                <input type="hidden" name="campaign_type" id="campaignTypeInput" value="{{ $activeTab }}">
                <input type="hidden" name="duration" id="durationInput" value="{{ old('duration', 60) }}">
                <input type="hidden" name="sub_page_toggle" id="subPageToggleInput" value="0">
                <input type="hidden" name="sub_page_visits" id="subPageVisitsInput" value="1">
                <input type="hidden" name="sub_page_duration" id="subPageDurationInput" value="20">
                <input type="hidden" name="captcha_mode" id="captchaModeInput" value="{{ old('captcha_mode', 'normal') }}">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- LEFT COLUMN: Campaign Configuration -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Tab Header -->
                        <div class="p-1.5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-800 flex gap-2 shadow-sm">
                            <button type="button" onclick="switchTab('direct')" id="tabBtnDirect" 
                                class="flex-1 py-3 px-6 rounded-xl font-bold text-sm flex items-center justify-center gap-2.5 transition-all {{ $activeTab === 'direct' ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-500/25' : 'text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
                                <span>🌐</span>
                                <span>Direct Traffic (GOAT Package)</span>
                            </button>
                            <button type="button" onclick="switchTab('search')" id="tabBtnSearch" 
                                class="flex-1 py-3 px-6 rounded-xl font-bold text-sm flex items-center justify-center gap-2.5 transition-all {{ $activeTab === 'search' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25' : 'text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-white' }}">
                                <span>🔍</span>
                                <span>Google Search (Click Booster)</span>
                            </button>
                        </div>

                        <!-- STEP 1: TARGET WEBSITE & SOURCE CONFIGURATION -->
                        <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800/80 shadow-xl space-y-6">
                            <div class="flex items-center gap-3 border-b border-gray-200 dark:border-gray-800 pb-4">
                                <span class="w-8 h-8 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 font-extrabold flex items-center justify-center text-sm">1</span>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Target Website & Traffic Source</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Configure where visitors land and how they reach your page</p>
                                </div>
                            </div>

                            <!-- TARGET URL -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Target Website URL <span class="text-orange-500">*</span></label>
                                <input type="url" name="url" id="targetUrl" value="{{ old('url') }}" required placeholder="https://client-website.com" 
                                    class="w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-3.5 text-gray-900 dark:text-white placeholder-gray-400 focus:border-brand focus:ring-1 focus:ring-brand transition font-bold">
                            </div>

                            <!-- DIRECT TRAFFIC REFERRERS SECTION (MULTI-SELECTABLE CARDS / PILLS) -->
                            <div id="directFieldsBox" class="{{ $activeTab === 'direct' ? '' : 'hidden' }} space-y-4 pt-2">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">Traffic Sources / Referrers (Multi-Selectable)</label>
                                        <span class="text-xs text-orange-500 font-bold">Click multiple sources to combine referrers</span>
                                    </div>
                                    
                                    <input type="hidden" name="traffic_source" id="trafficSourceInput" value="direct">

                                    <!-- Category 1: Direct -->
                                    <div class="mb-3">
                                        <div class="text-[11px] font-extrabold uppercase text-gray-400 mb-1.5">Primary Source</div>
                                        <div class="flex flex-wrap gap-2">
                                            <div onclick="toggleSourceCard('direct')" id="sourceCard_direct"
                                                class="cursor-pointer px-3.5 py-2 rounded-xl border-2 font-bold text-xs transition border-orange-500 bg-orange-500/10 text-orange-600 dark:text-orange-400">
                                                ✓ Direct URL (No Referrer)
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category 2: Search Engine Referrers -->
                                    <div class="mb-3">
                                        <div class="text-[11px] font-extrabold uppercase text-gray-400 mb-1.5">Search Engine Referrers (Organic Source)</div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['google' => 'Google', 'bing' => 'Bing', 'yahoo' => 'Yahoo', 'yandex' => 'Yandex', 'duckduckgo' => 'DuckDuckGo'] as $sKey => $sLabel)
                                                <div onclick="toggleSourceCard('{{ $sKey }}')" id="sourceCard_{{ $sKey }}"
                                                    class="cursor-pointer px-3.5 py-2 rounded-xl border-2 font-bold text-xs transition border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 text-gray-800 dark:text-gray-200">
                                                    🔍 {{ $sLabel }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Category 3: Social Media Referrers -->
                                    <div class="mb-3">
                                        <div class="text-[11px] font-extrabold uppercase text-gray-400 mb-1.5">Social Media Referrers</div>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['facebook' => 'Facebook', 'twitter' => 'Twitter / X', 'reddit' => 'Reddit', 'linkedin' => 'LinkedIn', 'pinterest' => 'Pinterest', 'quora' => 'Quora', 'instagram' => 'Instagram', 'youtube' => 'YouTube'] as $socKey => $socLabel)
                                                <div onclick="toggleSourceCard('{{ $socKey }}')" id="sourceCard_{{ $socKey }}"
                                                    class="cursor-pointer px-3.5 py-2 rounded-xl border-2 font-bold text-xs transition border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 text-gray-800 dark:text-gray-200">
                                                    🌐 {{ $socLabel }}
                                                </div>
                                            @endforeach
                                            <div onclick="toggleSourceCard('custom')" id="sourceCard_custom"
                                                class="cursor-pointer px-3.5 py-2 rounded-xl border-2 font-bold text-xs transition border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 text-gray-800 dark:text-gray-200">
                                                🔗 + Custom Referrer URL
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="customReferrerBox" class="hidden">
                                    <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Custom Referrer URL(s)</label>
                                    <textarea name="custom_referrers" rows="2" placeholder="https://example-referrer.com/page&#10;https://anothersite.com"
                                        class="w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 focus:border-brand transition font-medium">{{ old('custom_referrers') }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">Enter custom referrer URLs one per line.</p>
                                </div>
                            </div>

                            <!-- SEARCH ENGINE FIELDS (ONLY IN SEARCH TAB) -->
                            <div id="searchFieldsBox" class="{{ $activeTab === 'search' ? '' : 'hidden' }} space-y-5 pt-2">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Search Engine</label>
                                        <select name="search_engine" id="searchEngine" 
                                            class="w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-3.5 text-gray-900 dark:text-white focus:border-brand transition font-bold">
                                            <option value="google" selected>Google Search</option>
                                            <option value="bing">Bing Search</option>
                                            <option value="yahoo">Yahoo Search</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Max Search Crawl Pages</label>
                                        <select name="max_page" id="maxPage" 
                                            class="w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-3.5 text-gray-900 dark:text-white focus:border-brand transition font-bold">
                                            <option value="1">Page 1 (Top 10 Results)</option>
                                            <option value="3">Up to Page 3</option>
                                            <option value="5" selected>Up to Page 5</option>
                                            <option value="10">Up to Page 10</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Traffic Quality Mode -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Traffic Quality Mode</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div onclick="selectQualityMode('normal')" id="qualityCardNormal"
                                            class="cursor-pointer p-4 rounded-xl border-2 transition border-orange-500 bg-orange-500/10">
                                            <div class="font-bold text-gray-900 dark:text-white text-sm">Normal Free Mode</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Standard rate (Base 20 pts / 60s visit)</div>
                                        </div>
                                        <div onclick="selectQualityMode('premium')" id="qualityCardPremium"
                                            class="cursor-pointer p-4 rounded-xl border-2 transition border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-gray-900 dark:text-white text-sm">Premium Guaranteed Mode</span>
                                                <span class="text-[10px] uppercase font-bold bg-orange-500 text-white px-2 py-0.5 rounded">High Quality</span>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Guaranteed Search Click (Base 30 pts / 60s visit)</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- DYNAMIC KEYWORDS & PERCENTAGE SPLIT BUILDER -->
                                <div class="space-y-3 pt-2 border-t border-gray-200 dark:border-gray-800">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">Search Keywords & Traffic Split (%)</label>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Total Split:</span>
                                            <span id="keywordTotalPercentBadge" class="px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">100%</span>
                                        </div>
                                    </div>

                                    <div id="keywordsListContainer" class="space-y-2.5">
                                        <!-- Initial Keyword Row -->
                                        <div class="keyword-row flex items-center gap-2">
                                            <input type="text" name="keyword_texts[]" placeholder="Enter keyword (e.g. best seo agency)"
                                                class="flex-1 bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white font-medium focus:border-orange-500">
                                            <div class="relative w-24 sm:w-28">
                                                <input type="number" name="keyword_percents[]" value="100" min="1" max="100"
                                                    oninput="updateKeywordPercents()"
                                                    class="keyword-percent-input w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-3 py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white font-bold text-center focus:border-orange-500">
                                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">%</span>
                                            </div>
                                            <button type="button" onclick="removeKeywordRow(this)"
                                                class="p-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition">
                                                🗑️
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-1">
                                        <button type="button" onclick="addKeywordRow()"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-500/10 hover:bg-orange-500/20 text-orange-500 font-extrabold text-xs transition border border-orange-500/30">
                                            <span>+ Add Another Keyword</span>
                                        </button>
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">Total allocation across keywords must equal 100%</span>
                                    </div>
                                </div>

                                <!-- MAX SEARCH PAGES TO SCAN -->
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">Max Search Pages to Scan</label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">How deep should our engine scan Google results?</p>
                                        </div>
                                        <div>
                                            <select name="max_page" id="maxPageSelect"
                                                class="w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white font-bold focus:border-orange-500">
                                                <option value="1">Top 1 Page (Top 10 Google Results)</option>
                                                <option value="3">Top 3 Pages (Top 30 Google Results)</option>
                                                <option value="5">Top 5 Pages (Top 50 Google Results)</option>
                                                <option value="10" selected>Top 10 Pages (Top 100 Results - Recommended)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- MANDATORY RANKING REQUIREMENT NOTICE -->
                                    <div class="mt-4 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-start gap-3">
                                        <span class="text-xl">⚠️</span>
                                        <div class="text-xs text-amber-800 dark:text-amber-300 space-y-1">
                                            <p class="font-extrabold text-amber-900 dark:text-amber-200 uppercase tracking-wider">Mandatory Google Ranking Requirement</p>
                                            <p>Your target website URL <strong class="text-amber-700 dark:text-white">MUST BE RANKED</strong> on Google search within the selected scan range (e.g. Top 10 Pages / Top 100 Results) for every targeted keyword. If the URL is not found within the scan limit, traffic cannot be delivered.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: VISIT DURATION & VISITOR BEHAVIOR -->
                        <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800/80 shadow-xl space-y-6">
                            <div class="flex items-center gap-3 border-b border-gray-200 dark:border-gray-800 pb-4">
                                <span class="w-8 h-8 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 font-extrabold flex items-center justify-center text-sm">2</span>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Visit Duration & Visitor Behavior</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Control time spent on page, scrolling, and internal sub-page navigation</p>
                                </div>
                            </div>

                            <!-- MAIN DURATION SELECTION (Presets 20s, 30s, 60s, 90s, 120s + Custom) -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-bold text-gray-800 dark:text-gray-200">Main Page Duration (Seconds)</label>
                                    <span class="text-xs text-orange-500 font-bold">Select Preset or Type Custom Seconds</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5">
                                    @foreach([20, 30, 60, 90, 120] as $dur)
                                        <div onclick="selectDuration({{ $dur }})" id="durationCard{{ $dur }}"
                                            class="cursor-pointer p-3 text-center rounded-xl border-2 transition font-bold text-xs sm:text-sm {{ old('duration', 60) == $dur ? 'border-orange-500 bg-orange-500/10 text-orange-600 dark:text-orange-400' : 'border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 text-gray-800 dark:text-gray-200' }}">
                                            {{ $dur }}s
                                        </div>
                                    @endforeach
                                    <div>
                                        <input type="number" id="customDurationInput" placeholder="Custom Sec" min="10" max="600"
                                            oninput="setCustomDuration(this.value)"
                                            class="w-full bg-white dark:bg-gray-950 border-2 border-gray-300 dark:border-gray-800 rounded-xl px-2 py-2.5 text-center text-gray-900 dark:text-white font-bold text-xs sm:text-sm focus:border-orange-500 transition">
                                    </div>
                                </div>
                            </div>

                            <!-- BEHAVIOR CLICKS: SCROLL & CLICK LINK ON/OFF BUTTON CARD -->
                            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-950/60 border border-gray-200 dark:border-gray-800 transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <label class="font-extrabold text-gray-900 dark:text-white text-sm">Behavior Clicks: Scroll & Click Internal Link</label>
                                            <span id="behaviorStatusBadge" class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20">ON</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Simulate human-like page scrolling and clicking internal links</p>
                                    </div>
                                    <div>
                                        <input type="hidden" name="behavior_scroll" id="behaviorScrollInput" value="enabled">
                                        <input type="hidden" name="behavior_click" id="behaviorClickInput" value="enabled">
                                        <button type="button" onclick="toggleBehaviorClicks()" id="behaviorToggleBtn"
                                            class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition border-2 border-orange-500 bg-orange-500 text-white shadow-lg shadow-orange-500/20">
                                            ENABLED (ON)
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- SUB-PAGE VISITS ON/OFF BUTTON CARD -->
                            <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-950/60 border border-gray-200 dark:border-gray-800 transition space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <label class="font-extrabold text-gray-900 dark:text-white text-sm">Sub-Page Visits (Multi-Page Navigation)</label>
                                            <span id="subPageStatusBadge" class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-gray-500/10 text-gray-500 border border-gray-500/20">OFF</span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Visitor navigates deeper into your website sub-pages</p>
                                    </div>
                                    <div>
                                        <button type="button" onclick="toggleSubPagesBtn()" id="subPageToggleBtn"
                                            class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition border-2 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300">
                                            DISABLED (OFF)
                                        </button>
                                    </div>
                                </div>

                                <!-- SUB-PAGE OPTIONS (VISIBLE ONLY WHEN TOGGLED ON) -->
                                <div id="subPageOptionsBox" class="hidden pt-4 border-t border-gray-200 dark:border-gray-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Number of Sub-Pages to Visit</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            @foreach([1, 2, 3] as $sp)
                                                <div onclick="selectSubPageCount({{ $sp }})" id="subPageCard{{ $sp }}"
                                                    class="cursor-pointer p-2.5 text-center rounded-xl border-2 transition font-bold text-xs {{ $sp == 1 ? 'border-orange-500 bg-orange-500/10 text-orange-600 dark:text-orange-400' : 'border-gray-300 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200' }}">
                                                    {{ $sp }} {{ $sp == 1 ? 'Page' : 'Pages' }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Sub-Page Stay Duration (Per Page)</label>
                                            <span class="text-[10px] text-orange-500 font-bold">Preset or Custom Sec</span>
                                        </div>
                                        <div class="grid grid-cols-4 gap-2">
                                            @foreach([10, 20, 30] as $spd)
                                                <div onclick="selectSubPageDuration({{ $spd }})" id="subPageDurCard{{ $spd }}"
                                                    class="cursor-pointer p-2.5 text-center rounded-xl border-2 transition font-bold text-xs {{ $spd == 20 ? 'border-orange-500 bg-orange-500/10 text-orange-600 dark:text-orange-400' : 'border-gray-300 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200' }}">
                                                    {{ $spd }}s
                                                </div>
                                            @endforeach
                                            <div>
                                                <input type="number" id="customSubPageDurationInput" placeholder="Custom s" min="5" max="300"
                                                    oninput="setCustomSubPageDuration(this.value)"
                                                    class="w-full bg-white dark:bg-gray-950 border-2 border-gray-300 dark:border-gray-800 rounded-xl px-2 py-2 text-center text-gray-900 dark:text-white font-bold text-xs focus:border-orange-500 transition">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- STEP 3: DEVICE, COUNTRY & VISIT QUANTITY LIMITS -->
                        <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800/80 shadow-xl space-y-6">
                            <div class="flex items-center gap-3 border-b border-gray-200 dark:border-gray-800 pb-4">
                                <span class="w-8 h-8 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 font-extrabold flex items-center justify-center text-sm">3</span>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base">Targeting & Delivery Limits</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Device filtering, country geolocation, and delivery caps</p>
                                </div>
                            </div>

                            <!-- DEVICE & TARGET COUNTRY -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Device Targeting</label>
                                    <select name="device_type" id="deviceType" 
                                        class="w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-3.5 text-gray-900 dark:text-white focus:border-brand transition font-bold">
                                        <option value="All">All Devices (Desktop + Mobile)</option>
                                        <option value="Desktop">Desktop Only</option>
                                        <option value="Mobile">Mobile Only</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Target Country (Select Multiple)</label>
                                    <div class="grid grid-cols-2 gap-2 h-36 overflow-y-auto p-2 bg-gray-50 dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl">
                                        @php
                                            $fallbackList = ['Worldwide', 'United States', 'United Kingdom', 'Canada', 'Australia', 'Germany', 'France', 'Netherlands', 'Singapore', 'India', 'Bangladesh', 'Brazil', 'Japan', 'Spain', 'Italy', 'Switzerland', 'Sweden', 'Poland', 'Mexico', 'South Korea', 'Turkey', 'United Arab Emirates', 'Saudi Arabia', 'South Africa', 'Indonesia', 'Malaysia', 'Vietnam', 'Thailand', 'Philippines', 'Pakistan', 'Egypt', 'Nigeria', 'Argentina', 'Colombia', 'Chile', 'New Zealand'];
                                            $countryItems = [];
                                            if (!empty($availableCountries) && is_array($availableCountries)) {
                                                $countryItems[] = ['name' => 'Worldwide', 'count' => null];
                                                foreach ($availableCountries as $ac) {
                                                    $name = is_array($ac) ? ($ac['country'] ?? '') : $ac;
                                                    $cnt = is_array($ac) ? ($ac['count'] ?? null) : null;
                                                    if ($name && strtolower($name) !== 'worldwide') {
                                                        $countryItems[] = ['name' => $name, 'count' => $cnt];
                                                    }
                                                }
                                            } else {
                                                foreach ($fallbackList as $fc) {
                                                    $countryItems[] = ['name' => $fc, 'count' => null];
                                                }
                                            }
                                        @endphp
                                        @foreach($countryItems as $cItem)
                                            @php $countryName = $cItem['name']; @endphp
                                            <label class="flex items-center justify-between gap-2 p-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-900 cursor-pointer transition">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" name="target_country[]" value="{{ $countryName }}" {{ $countryName === 'Worldwide' ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-500 bg-white dark:bg-gray-800 w-4 h-4">
                                                    <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $countryName }}</span>
                                                </div>
                                                @if($cItem['count'] !== null)
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 font-bold">{{ $cItem['count'] }}</span>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- VISIT QUANTITY: TOTAL, HOURLY & DAILY LIMIT -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                                <div>
                                    <label class="block text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Total Visits Required <span class="text-orange-500">*</span></label>
                                    <input type="number" name="total_limit" id="totalVisits" value="{{ old('total_limit', 1000) }}" min="10" max="100000" step="10" required 
                                        oninput="triggerRecalculate()"
                                        class="w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-3.5 text-gray-900 dark:text-white focus:border-brand transition font-bold">
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Hourly Visit Limit <span class="text-orange-500">*</span></label>
                                    <input type="number" name="hourly_limit" id="hourlyLimit" value="{{ old('hourly_limit', 100) }}" min="1" max="5000" required 
                                        class="w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-3.5 text-gray-900 dark:text-white focus:border-brand transition font-bold">
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Daily Visit Limit <span class="text-orange-500">*</span></label>
                                    <input type="number" name="daily_limit" id="dailyLimit" value="{{ old('daily_limit', 1000) }}" min="10" max="50000" required 
                                        class="w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-3.5 text-gray-900 dark:text-white focus:border-brand transition font-bold">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Live Dynamic Point Calculator Box -->
                    <div class="lg:col-span-5">
                        <div class="sticky top-8 rounded-3xl bg-white dark:bg-gray-900/90 border border-gray-200 dark:border-gray-800/80 p-6 sm:p-8 shadow-2xl">
                            <div class="flex items-center justify-between pb-6 border-b border-gray-200 dark:border-gray-800">
                                <div>
                                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white">Live Point Calculator</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Real-time estimate formula</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20">Auto USD Convert</span>
                            </div>

                            <!-- Breakdown List -->
                            <div class="py-6 space-y-4 border-b border-gray-200 dark:border-gray-800">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Service Mode</span>
                                    <span class="font-bold text-gray-900 dark:text-white" id="calcModeText">1. Direct Traffic</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Target Visits</span>
                                    <span class="font-bold text-gray-900 dark:text-white" id="calcVisitsText">1,000 Visits</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Total Duration / Visit</span>
                                    <span class="font-bold text-gray-900 dark:text-white" id="calcDurationText">60 Seconds</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Rate per Visit</span>
                                    <span class="font-black text-orange-600 dark:text-orange-400" id="calcPointsPerVisitText">1.00 Pts</span>
                                </div>
                            </div>

                            <!-- TOTAL SUMMARY -->
                            <div class="py-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-base font-bold text-gray-700 dark:text-gray-300">Estimated Total Cost</span>
                                    <span class="text-3xl font-black text-orange-600 dark:text-orange-400" id="calcTotalPointsText">1,000</span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                                    <span>Your Available Traffic Points:</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ number_format($balance, 0) }} Points</span>
                                </div>
                                <div class="p-3 mt-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-400 font-bold">
                                    ⚡ Pay-As-You-Go Mode: Points are deducted incrementally as visits are delivered.
                                </div>
                            </div>

                            <!-- OFFICIAL RATE CHART REFERENCE TABLE -->
                            <div class="pt-2 pb-6 border-b border-gray-200 dark:border-gray-800">
                                <div class="text-xs font-extrabold uppercase text-gray-500 dark:text-gray-400 mb-3 flex items-center justify-between">
                                    <span>📊 Official Rate Chart (Per Visit)</span>
                                    <span class="text-[10px] text-orange-500 font-bold">Active Mode Highlighted</span>
                                </div>
                                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/50">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead>
                                            <tr class="border-b border-gray-200 dark:border-gray-800 text-[11px] text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-950/80">
                                                <th class="p-2.5 font-bold">Service Mode</th>
                                                <th class="p-2.5 font-bold text-center">60s</th>
                                                <th class="p-2.5 font-bold text-center">90s</th>
                                                <th class="p-2.5 font-bold text-center">120s</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800/60 text-gray-700 dark:text-gray-300">
                                            <tr id="rateRowDirect" class="transition">
                                                <td class="p-2.5 font-bold text-gray-900 dark:text-white">1. Direct Traffic</td>
                                                <td class="p-2.5 text-center font-bold text-orange-500">1.0 Pt</td>
                                                <td class="p-2.5 text-center font-bold">1.5 Pts</td>
                                                <td class="p-2.5 text-center font-bold">2.0 Pts</td>
                                            </tr>
                                            <tr id="rateRowSearchNormal" class="transition">
                                                <td class="p-2.5 font-bold text-gray-900 dark:text-white">2. Search (⚡ Normal)</td>
                                                <td class="p-2.5 text-center font-bold text-orange-500">20.0 Pts</td>
                                                <td class="p-2.5 text-center font-bold">30.0 Pts</td>
                                                <td class="p-2.5 text-center font-bold">40.0 Pts</td>
                                            </tr>
                                            <tr id="rateRowSearchPremium" class="transition">
                                                <td class="p-2.5 font-bold text-gray-900 dark:text-white">3. Search (💎 Premium)</td>
                                                <td class="p-2.5 text-center font-bold text-orange-500">30.0 Pts</td>
                                                <td class="p-2.5 text-center font-bold">45.0 Pts</td>
                                                <td class="p-2.5 text-center font-bold">60.0 Pts</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-2 text-center">
                                    Formula: <span class="text-gray-900 dark:text-white font-bold">Total Duration</span> = Main Duration + (Sub-pages × Sub-page Duration)
                                </p>
                            </div>

                            <!-- ACTION BUTTON -->
                            <div class="pt-4 space-y-3">
                                <button type="submit" id="launchBtn" 
                                    class="w-full py-4 px-6 rounded-2xl font-bold text-sm text-white bg-gradient-to-r from-orange-500 via-amber-500 to-orange-500 hover:opacity-95 shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2">
                                    <span>Launch Traffic Campaign</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </button>

                                <a href="{{ route('client.traffic_campaign.topup') }}" class="w-full py-3 px-6 rounded-xl font-bold text-xs text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-800 hover:bg-orange-100 dark:hover:bg-orange-900/40 transition flex items-center justify-center gap-2">
                                    <span>Need More Points? Buy Points Here</span>
                                </a>

                                <p class="text-[11px] text-center text-gray-500 leading-normal">
                                    If points are insufficient, the exact shortage amount ($1/1,000 Pts) is deducted seamlessly from your Main Account USD balance.
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- INTERACTIVE CLICK SELECTION & LIVE DYNAMIC POINT CALCULATOR JAVASCRIPT -->
    <script>
        let selectedTrafficSources = ['direct'];

        function toggleSourceCard(key) {
            const idx = selectedTrafficSources.indexOf(key);
            if (idx > -1) {
                if (selectedTrafficSources.length > 1) {
                    selectedTrafficSources.splice(idx, 1);
                }
            } else {
                selectedTrafficSources.push(key);
            }

            document.getElementById('trafficSourceInput').value = selectedTrafficSources.join(',');

            // Update badge cards UI
            const allSourceKeys = ['direct', 'google', 'bing', 'yahoo', 'yandex', 'duckduckgo', 'facebook', 'twitter', 'reddit', 'linkedin', 'pinterest', 'quora', 'instagram', 'youtube', 'custom'];
            allSourceKeys.forEach(k => {
                const card = document.getElementById('sourceCard_' + k);
                if (card) {
                    if (selectedTrafficSources.includes(k)) {
                        card.className = 'cursor-pointer px-3.5 py-2 rounded-xl border-2 font-bold text-xs transition border-orange-500 bg-orange-500/10 text-orange-600 dark:text-orange-400';
                    } else {
                        card.className = 'cursor-pointer px-3.5 py-2 rounded-xl border-2 font-bold text-xs transition border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 text-gray-800 dark:text-gray-200';
                    }
                }
            });

            const customBox = document.getElementById('customReferrerBox');
            if (customBox) {
                if (selectedTrafficSources.includes('custom')) {
                    customBox.classList.remove('hidden');
                } else {
                    customBox.classList.add('hidden');
                }
            }
        }

        let isBehaviorOn = true;
        function toggleBehaviorClicks() {
            isBehaviorOn = !isBehaviorOn;
            document.getElementById('behaviorScrollInput').value = isBehaviorOn ? 'enabled' : 'disabled';
            document.getElementById('behaviorClickInput').value = isBehaviorOn ? 'enabled' : 'disabled';
            const btn = document.getElementById('behaviorToggleBtn');
            const badge = document.getElementById('behaviorStatusBadge');
            if (isBehaviorOn) {
                btn.innerText = 'ENABLED (ON)';
                btn.className = 'px-5 py-2.5 rounded-xl font-extrabold text-xs transition border-2 border-orange-500 bg-orange-500 text-white shadow-lg shadow-orange-500/20';
                badge.innerText = 'ON';
                badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20';
            } else {
                btn.innerText = 'DISABLED (OFF)';
                btn.className = 'px-5 py-2.5 rounded-xl font-extrabold text-xs transition border-2 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300';
                badge.innerText = 'OFF';
                badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-gray-500/10 text-gray-500 border border-gray-500/20';
            }
        }

        let isSubPageOn = false;
        function toggleSubPagesBtn() {
            isSubPageOn = !isSubPageOn;
            document.getElementById('subPageToggleInput').value = isSubPageOn ? '1' : '0';
            const btn = document.getElementById('subPageToggleBtn');
            const badge = document.getElementById('subPageStatusBadge');
            const box = document.getElementById('subPageOptionsBox');
            if (isSubPageOn) {
                btn.innerText = 'ENABLED (ON)';
                btn.className = 'px-5 py-2.5 rounded-xl font-extrabold text-xs transition border-2 border-orange-500 bg-orange-500 text-white shadow-lg shadow-orange-500/20';
                badge.innerText = 'ON';
                badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20';
                if (box) box.classList.remove('hidden');
            } else {
                btn.innerText = 'DISABLED (OFF)';
                btn.className = 'px-5 py-2.5 rounded-xl font-extrabold text-xs transition border-2 border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300';
                badge.innerText = 'OFF';
                badge.className = 'px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-gray-500/10 text-gray-500 border border-gray-500/20';
                if (box) box.classList.add('hidden');
            }
            triggerRecalculate();
        }

        function selectSubPageCount(cnt) {
            document.getElementById('subPageVisitsInput').value = cnt;
            [1, 2, 3].forEach(c => {
                const card = document.getElementById('subPageCard' + c);
                if (card) {
                    card.className = (c === cnt)
                        ? 'cursor-pointer p-2.5 text-center rounded-xl border-2 transition font-bold text-xs border-orange-500 bg-orange-500/10 text-orange-600 dark:text-orange-400'
                        : 'cursor-pointer p-2.5 text-center rounded-xl border-2 transition font-bold text-xs border-gray-300 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200';
                }
            });
            triggerRecalculate();
        }

        function selectSubPageDuration(spd) {
            document.getElementById('subPageDurationInput').value = spd;
            [10, 20, 30].forEach(d => {
                const card = document.getElementById('subPageDurCard' + d);
                if (card) {
                    card.className = (d === spd)
                        ? 'cursor-pointer p-2.5 text-center rounded-xl border-2 transition font-bold text-xs border-orange-500 bg-orange-500/10 text-orange-600 dark:text-orange-400'
                        : 'cursor-pointer p-2.5 text-center rounded-xl border-2 transition font-bold text-xs border-gray-300 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200';
                }
            });
            triggerRecalculate();
        }

        function selectDuration(dur) {
            document.getElementById('durationInput').value = dur;
            [20, 30, 60, 90, 120].forEach(d => {
                const card = document.getElementById('durationCard' + d);
                if (card) {
                    card.className = (d === dur)
                        ? 'cursor-pointer p-3 text-center rounded-xl border-2 transition font-bold text-xs sm:text-sm border-orange-500 bg-orange-500/10 text-orange-600 dark:text-orange-400'
                        : 'cursor-pointer p-3 text-center rounded-xl border-2 transition font-bold text-xs sm:text-sm border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 text-gray-800 dark:text-gray-200';
                }
            });
            const customInput = document.getElementById('customDurationInput');
            if (customInput) customInput.value = '';
            triggerRecalculate();
        }

        function setCustomDuration(val) {
            let dur = parseInt(val) || 60;
            if (dur < 10) dur = 10;
            if (dur > 600) dur = 600;
            document.getElementById('durationInput').value = dur;
            [20, 30, 60, 90, 120].forEach(d => {
                const card = document.getElementById('durationCard' + d);
                if (card) {
                    card.className = 'cursor-pointer p-3 text-center rounded-xl border-2 transition font-bold text-xs sm:text-sm border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40 text-gray-800 dark:text-gray-200';
                }
            });
            triggerRecalculate();
        }

        function selectQualityMode(mode) {
            document.getElementById('captchaModeInput').value = mode;
            const cardNorm = document.getElementById('qualityCardNormal');
            const cardPrem = document.getElementById('qualityCardPremium');

            if (mode === 'premium') {
                if (cardPrem) cardPrem.className = 'cursor-pointer p-4 rounded-xl border-2 transition border-orange-500 bg-orange-500/10';
                if (cardNorm) cardNorm.className = 'cursor-pointer p-4 rounded-xl border-2 transition border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40';
            } else {
                if (cardNorm) cardNorm.className = 'cursor-pointer p-4 rounded-xl border-2 transition border-orange-500 bg-orange-500/10';
                if (cardPrem) cardPrem.className = 'cursor-pointer p-4 rounded-xl border-2 transition border-gray-300 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/40';
            }
            triggerRecalculate();
        }

        function switchTab(tab) {
            const typeInput = document.getElementById('campaignTypeInput');
            typeInput.value = tab;

            const btnDirect = document.getElementById('tabBtnDirect');
            const btnSearch = document.getElementById('tabBtnSearch');
            const searchBox = document.getElementById('searchFieldsBox');
            const directBox = document.getElementById('directFieldsBox');

            if (tab === 'search') {
                btnSearch.className = 'flex-1 py-3 px-6 rounded-xl font-bold text-sm flex items-center justify-center gap-2.5 transition-all bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25';
                btnDirect.className = 'flex-1 py-3 px-6 rounded-xl font-bold text-sm flex items-center justify-center gap-2.5 transition-all text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-white';
                searchBox.classList.remove('hidden');
                if (directBox) directBox.classList.add('hidden');
            } else {
                btnDirect.className = 'flex-1 py-3 px-6 rounded-xl font-bold text-sm flex items-center justify-center gap-2.5 transition-all bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-500/25';
                btnSearch.className = 'flex-1 py-3 px-6 rounded-xl font-bold text-sm flex items-center justify-center gap-2.5 transition-all text-gray-700 dark:text-gray-400 hover:text-black dark:hover:text-white';
                searchBox.classList.add('hidden');
                if (directBox) directBox.classList.remove('hidden');
            }

            triggerRecalculate();
        }

        function addKeywordRow() {
            const container = document.getElementById('keywordsListContainer');
            if (!container) return;
            const row = document.createElement('div');
            row.className = 'keyword-row flex items-center gap-2';
            row.innerHTML = `
                <input type="text" name="keyword_texts[]" placeholder="Enter keyword (e.g. best seo agency)"
                    class="flex-1 bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white font-medium focus:border-orange-500">
                <div class="relative w-24 sm:w-28">
                    <input type="number" name="keyword_percents[]" value="100" min="1" max="100"
                        oninput="updateKeywordPercents()"
                        class="keyword-percent-input w-full bg-white dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-3 py-2.5 text-xs sm:text-sm text-gray-900 dark:text-white font-bold text-center focus:border-orange-500">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">%</span>
                </div>
                <button type="button" onclick="removeKeywordRow(this)"
                    class="p-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition">
                    🗑️
                </button>
            `;
            container.appendChild(row);
            updateKeywordPercents();
        }

        function removeKeywordRow(btn) {
            const container = document.getElementById('keywordsListContainer');
            if (container && container.children.length > 1) {
                btn.closest('.keyword-row').remove();
                updateKeywordPercents();
            }
        }

        function updateKeywordPercents() {
            const inputs = document.querySelectorAll('.keyword-percent-input');
            let total = 0;
            inputs.forEach(inp => {
                total += parseInt(inp.value) || 0;
            });
            const badge = document.getElementById('keywordTotalPercentBadge');
            if (badge) {
                badge.innerText = total + '%';
                if (total === 100) {
                    badge.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                } else {
                    badge.className = 'px-2.5 py-1 rounded-lg text-xs font-black bg-amber-500/10 text-amber-400 border border-amber-500/20';
                }
            }
        }

        function triggerRecalculate() {
            const typeInput = document.getElementById('campaignTypeInput').value;
            const totalVisits = parseInt(document.getElementById('totalVisits').value) || 0;
            
            const durationSec = parseInt(document.getElementById('durationInput').value) || 60;
            const isSubPageOn = document.getElementById('subPageToggleInput').value === '1';
            const subPageVisits = isSubPageOn ? (parseInt(document.getElementById('subPageVisitsInput').value) || 1) : 0;
            const subPageDur = isSubPageOn ? (parseInt(document.getElementById('subPageDurationInput').value) || 20) : 0;

            const captchaMode = document.getElementById('captchaModeInput').value;

            const totalSeconds = durationSec + (subPageVisits * subPageDur);
            
            let baseRate60s = 1.0;
            let modeLabel = '1. Direct Traffic';
            let activeRowId = 'rateRowDirect';

            if (typeInput === 'search') {
                if (captchaMode === 'premium') {
                    baseRate60s = 30.0;
                    modeLabel = '3. Search Traffic (Premium)';
                    activeRowId = 'rateRowSearchPremium';
                } else {
                    baseRate60s = 20.0;
                    modeLabel = '2. Search Traffic (Normal)';
                    activeRowId = 'rateRowSearchNormal';
                }
            } else {
                baseRate60s = 1.0;
                modeLabel = '1. Direct Traffic';
                activeRowId = 'rateRowDirect';
            }

            const pointsPerVisit = baseRate60s * (totalSeconds / 60.0);
            const totalPoints = Math.ceil(pointsPerVisit * totalVisits);

            const modeElem = document.getElementById('calcModeText');
            if (modeElem) modeElem.innerText = modeLabel;

            const visitsElem = document.getElementById('calcVisitsText');
            if (visitsElem) visitsElem.innerText = totalVisits.toLocaleString() + ' Visits';

            const durElem = document.getElementById('calcDurationText');
            if (durElem) durElem.innerText = totalSeconds + 's (' + durationSec + 's main' + (isSubPageOn ? ' + ' + (subPageVisits * subPageDur) + 's sub)' : ')');

            const ppvElem = document.getElementById('calcPointsPerVisitText');
            if (ppvElem) ppvElem.innerText = pointsPerVisit.toFixed(2) + ' Pts';

            const totalElem = document.getElementById('calcTotalPointsText');
            if (totalElem) totalElem.innerText = totalPoints.toLocaleString();

            // Highlight Active Mode Row in Rate Chart
            ['rateRowDirect', 'rateRowSearchNormal', 'rateRowSearchPremium'].forEach(rowId => {
                const el = document.getElementById(rowId);
                if (el) {
                    if (rowId === activeRowId) {
                        el.className = 'bg-orange-500/15 dark:bg-orange-500/20 font-extrabold border-l-4 border-orange-500 transition';
                    } else {
                        el.className = 'opacity-50 transition';
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            triggerRecalculate();
            updateKeywordPercents();

            ['totalVisits', 'durationInput', 'subPageVisitsInput', 'subPageDurationInput', 'hourlyLimit', 'dailyLimit'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', triggerRecalculate);
                    el.addEventListener('change', triggerRecalculate);
                }
            });
        });
    </script>
</x-app-layout>
