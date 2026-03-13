<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Site Settings & SEO Configurations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.site-settings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @php
                    $defaultServices = [
                        [
                            'name' => 'SEO Campaigns', 
                            'url' => route('campaigns.index', 'seo-campaigns'), 
                            'description' => 'Complete managed ranking packages', 
                            'icon' => '<svg class="h-6 w-6 text-gray-600 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" /><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" /></svg>'
                        ],
                        [
                            'name' => 'High DA Link Building', 
                            'url' => route('services.index'), 
                            'description' => 'Powerful contextual editorial links', 
                            'icon' => '<svg class="h-6 w-6 text-gray-600 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg>'
                        ],
                        [
                            'name' => 'Guest Post Inventory', 
                            'url' => route('guest_posts.index'), 
                            'description' => 'Browse real partner websites instantly', 
                            'icon' => '<svg class="h-6 w-6 text-gray-600 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'
                        ],
                        [
                            'name' => 'Website Traffic', 
                            'url' => route('traffic.index'), 
                            'description' => 'Boost your organic analytics signals', 
                            'icon' => '<svg class="h-6 w-6 text-gray-600 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>'
                        ]
                    ];

                    $defaultCol1 = [
                        ['name' => 'SEO Campaigns', 'url' => route('campaigns.index', 'seo-campaigns')],
                        ['name' => 'Link Building', 'url' => route('services.index')],
                        ['name' => 'Guest Posts', 'url' => route('guest_posts.index')],
                        ['name' => 'Website Traffic', 'url' => route('traffic.index')]
                    ];

                    $defaultCol2 = [
                        ['name' => 'Latest News', 'url' => route('blog.index')],
                        ['name' => 'About TrafficVai', 'url' => route('about')],
                        ['name' => 'Contact Support', 'url' => route('contact')],
                        ['name' => 'Client Portal', 'url' => route('login')]
                    ];

                    $defaultCol3 = [
                        ['name' => 'Privacy Policy', 'url' => route('privacy')],
                        ['name' => 'Terms of Service', 'url' => route('terms')],
                        ['name' => 'Refund Policy', 'url' => '#']
                    ];

                    $savedServicesStr = \App\Models\Setting::get('header_services_menu');
                    $savedServices = is_string($savedServicesStr) ? json_decode($savedServicesStr, true) : [];
                    $servicesData = (is_array($savedServices) && count($savedServices) > 0) ? json_encode($savedServices) : json_encode($defaultServices);

                    $savedCol1Str = \App\Models\Setting::get('footer_col_1_links');
                    $savedCol1 = is_string($savedCol1Str) ? json_decode($savedCol1Str, true) : [];
                    $col1Data = (is_array($savedCol1) && count($savedCol1) > 0) ? json_encode($savedCol1) : json_encode($defaultCol1);

                    $savedCol2Str = \App\Models\Setting::get('footer_col_2_links');
                    $savedCol2 = is_string($savedCol2Str) ? json_decode($savedCol2Str, true) : [];
                    $col2Data = (is_array($savedCol2) && count($savedCol2) > 0) ? json_encode($savedCol2) : json_encode($defaultCol2);

                    $savedCol3Str = \App\Models\Setting::get('footer_col_3_links');
                    $savedCol3 = is_string($savedCol3Str) ? json_decode($savedCol3Str, true) : [];
                    $col3Data = (is_array($savedCol3) && count($savedCol3) > 0) ? json_encode($savedCol3) : json_encode($defaultCol3);
                @endphp

                <div x-data="{ activeTab: 'general' }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                    
                    <!-- Tabs Header -->
                    <div class="border-b border-gray-100 bg-gray-50/50 flex flex-wrap gap-2 p-4">
                        <button type="button" @click="activeTab = 'general'" :class="{'bg-white shadow-sm ring-1 ring-gray-200 text-indigo-600': activeTab === 'general', 'text-gray-500 hover:text-gray-700 hover:bg-white/50': activeTab !== 'general'}" class="px-5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center focus:outline-none">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            General Settings
                        </button>
                        <button type="button" @click="activeTab = 'seo'" :class="{'bg-white shadow-sm ring-1 ring-gray-200 text-indigo-600': activeTab === 'seo', 'text-gray-500 hover:text-gray-700 hover:bg-white/50': activeTab !== 'seo'}" class="px-5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center focus:outline-none">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            SEO Metadata
                        </button>
                        <button type="button" @click="activeTab = 'homepage'" :class="{'bg-white shadow-sm ring-1 ring-gray-200 text-indigo-600': activeTab === 'homepage', 'text-gray-500 hover:text-gray-700 hover:bg-white/50': activeTab !== 'homepage'}" class="px-5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center focus:outline-none">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            Homepage Content
                        </button>
                        <button type="button" @click="activeTab = 'appearance'" :class="{'bg-white shadow-sm ring-1 ring-gray-200 text-indigo-600': activeTab === 'appearance', 'text-gray-500 hover:text-gray-700 hover:bg-white/50': activeTab !== 'appearance'}" class="px-5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center focus:outline-none">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Appearance & Navigation
                        </button>
                        <button type="button" @click="activeTab = 'footer'" :class="{'bg-white shadow-sm ring-1 ring-gray-200 text-indigo-600': activeTab === 'footer', 'text-gray-500 hover:text-gray-700 hover:bg-white/50': activeTab !== 'footer'}" class="px-5 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 flex items-center focus:outline-none">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3" /></svg>
                            Footer Details
                        </button>
                    </div>

                    <div class="p-6 md:p-8">
                        <!-- Tab 1: General Settings -->
                        <div x-show="activeTab === 'general'" x-cloak class="space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Company Identity</h3>
                                <p class="text-sm text-gray-500 mb-6">Manage how your company name and contact info is displayed globally across the site.</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                                    <input type="text" name="site_name" value="{{ $settings['general']->where('key', 'site_name')->first()->value ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Support Email Address</label>
                                    <input type="email" name="contact_email" value="{{ $settings['general']->where('key', 'contact_email')->first()->value ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Public Phone Number</label>
                                    <input type="text" name="contact_phone" value="{{ $settings['general']->where('key', 'contact_phone')->first()->value ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Business Hours</label>
                                    <input type="text" name="contact_hours" value="{{ $settings['general']->where('key', 'contact_hours')->first()->value ?? 'Monday - Friday: 9:00 AM - 6:00 PM (EST)' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Office Headquarters (HTML allowed)</label>
                                    <textarea name="contact_address" rows="2" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ $settings['general']->where('key', 'contact_address')->first()->value ?? "123 Search Engine Blvd, Suite 400\nNew York, NY 10001" }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">BDT Exchange Rate (1 USD = ? BDT)</label>
                                    <input type="number" step="0.01" name="bdt_exchange_rate" value="{{ $settings['general']->where('key', 'bdt_exchange_rate')->first()->value ?? '120.00' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                            </div>

                            <!-- Favicon Upload -->
                            <div class="pt-6 border-t border-gray-100 mt-2">
                                <h3 class="text-base font-bold text-gray-900 mb-1">Favicon</h3>
                                <p class="text-sm text-gray-500 mb-4">Upload a favicon to display in browser tabs and bookmarks. Recommended size: 32×32 px. Accepted formats: .ico, .png, .svg.</p>
                                <div class="flex items-start gap-6">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Favicon</label>
                                        <input type="file" name="site_favicon" accept=".ico,.png,.svg,.jpg,.jpeg"
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                        <p class="text-xs text-gray-400 mt-1">Leave empty to keep the current favicon. Max 2MB.</p>
                                    </div>
                                    @if(\App\Models\Setting::get('site_favicon'))
                                    <div class="flex-shrink-0 text-center">
                                        <p class="text-xs font-medium text-gray-500 mb-2">Current Favicon</p>
                                        <div class="w-16 h-16 bg-gray-100 border border-gray-200 rounded-xl flex items-center justify-center overflow-hidden">
                                            @php 
                                                $favicon = \App\Models\Setting::get('site_favicon');
                                                $faviconUrl = $favicon ? Storage::disk('public')->url(str_replace('storage/', '', $favicon)) : null;
                                            @endphp
                                            @if($faviconUrl)
                                            <img src="{{ $faviconUrl }}?v={{ file_exists(public_path(str_replace('storage/', '', $favicon))) ? filemtime(public_path(str_replace('storage/', '', $favicon))) : '1' }}" alt="Favicon" class="max-w-full max-h-full object-contain">
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <div class="flex-shrink-0 text-center">
                                        <p class="text-xs font-medium text-gray-500 mb-2">Current Favicon</p>
                                        <div class="w-16 h-16 bg-gray-100 border border-dashed border-gray-300 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-1">Not set</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: SEO -->
                        <div x-show="activeTab === 'seo'" x-cloak class="space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Search Engine Optimization</h3>
                                <p class="text-sm text-gray-500 mb-6">Configure the primary Meta Title and Description that controls how you look on Google.</p>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Global Meta Title</label>
                                    <input type="text" name="home_seo_title" value="{{ $settings['seo']->where('key', 'home_seo_title')->first()->value ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <p class="text-xs text-gray-500 mt-2">Optimal length: 50–60 characters.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Global Meta Description</label>
                                    <textarea name="home_seo_description" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ $settings['seo']->where('key', 'home_seo_description')->first()->value ?? '' }}</textarea>
                                    <p class="text-xs text-gray-500 mt-2">Optimal length: 150-160 characters. Write a compelling summary of the platform.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Homepage Content -->
                        <div x-show="activeTab === 'homepage'" x-cloak class="space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Frontend Design Variables</h3>
                                <p class="text-sm text-gray-500 mb-6">Modify the Hero copy and Trust Statistics rendered on the primary landing page.</p>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hero Headline</label>
                                    <input type="text" name="home_hero_headline" value="{{ $settings['homepage']->where('key', 'home_hero_headline')->first()->value ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <p class="text-xs text-gray-500 mt-2">You can use standard HTML like &lt;br/&gt; to force line breaks in the hero section.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hero Subheadline</label>
                                    <textarea name="home_hero_subheadline" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ $settings['homepage']->where('key', 'home_hero_subheadline')->first()->value ?? '' }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Placements Built</label>
                                        <input type="text" name="home_trust_stat_1" value="{{ $settings['homepage']->where('key', 'home_trust_stat_1')->first()->value ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Active Agency Partners</label>
                                        <input type="text" name="home_trust_stat_2" value="{{ $settings['homepage']->where('key', 'home_trust_stat_2')->first()->value ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono">
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-gray-100">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-1">Service Page Hero — Trust Badges</h4>
                                    <p class="text-xs text-gray-500 mb-4">These three labels appear below the buttons at the bottom of the hero section on every service page.</p>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Badge 1</label>
                                            <input type="text" name="service_hero_badge_1" value="{{ \App\Models\Setting::get('service_hero_badge_1', 'Secure Payment') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Badge 2</label>
                                            <input type="text" name="service_hero_badge_2" value="{{ \App\Models\Setting::get('service_hero_badge_2', 'Professional Team') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Badge 3</label>
                                            <input type="text" name="service_hero_badge_3" value="{{ \App\Models\Setting::get('service_hero_badge_3', 'Results Guaranteed') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 4: Appearance & Navigation -->
                        <div x-show="activeTab === 'appearance'" x-cloak class="space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Appearance & Navigation</h3>
                                <p class="text-sm text-gray-500 mb-6">Update the site logo and manage custom header menu links.</p>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-4">Site Logo</label>
                                    <div class="flex items-center space-x-6">
                                        <div class="h-24 w-48 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center p-4">
                                            @php $logoPath = \App\Models\Setting::get('site_logo') ? asset(\App\Models\Setting::get('site_logo')) : asset('images/logo.png'); @endphp
                                            <img src="{{ $logoPath }}" alt="Current Logo" class="max-h-full max-w-full object-contain">
                                        </div>
                                        <div class="flex-1">
                                            <label class="block w-full max-w-sm rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer text-center transition">
                                                <span>Upload New Logo</span>
                                                <input type="file" name="site_logo" accept="image/*" class="hidden">
                                            </label>
                                            <p class="text-xs text-gray-500 mt-2">Recommended format: PNG with transparent background. Max height: 80px.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pt-6 border-t border-gray-100" x-data="{ 
                                    links: {{ \App\Models\Setting::get('header_menu', '[]') }} 
                                }">
                                    <div class="flex items-center justify-between mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Custom Header Links</label>
                                        <button type="button" @click="links.push({name: '', url: ''})" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg transition flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Add Link
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        <template x-for="(link, index) in links" :key="index">
                                            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                                <div class="flex-1">
                                                    <input type="text" x-model="link.name" :name="'header_menu['+index+'][name]'" placeholder="Link Name (e.g., Pricing)" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                </div>
                                                <div class="flex-1">
                                                    <input type="url" x-model="link.url" :name="'header_menu['+index+'][url]'" placeholder="URL (e.g., https://example.com/pricing)" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                </div>
                                                <button type="button" @click="links.splice(index, 1)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Remove Link">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                        <div x-show="links.length === 0" class="text-center py-6 bg-gray-50 rounded-xl border border-gray-100 border-dashed">
                                            <p class="text-sm text-gray-500">No custom links added yet.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pt-6 border-t border-gray-100">
                                    <h4 class="text-sm font-bold text-gray-900 mb-4">Standard Header Links Configuration</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                            <label class="block text-xs font-bold text-gray-700 mb-2">Home Link</label>
                                            <div class="flex gap-2">
                                                <input type="text" name="header_home_text" value="{{ \App\Models\Setting::get('header_home_text', 'Home') }}" placeholder="Text" class="w-1/2 rounded border-gray-300 text-sm">
                                                <input type="text" name="header_home_url" value="{{ \App\Models\Setting::get('header_home_url', route('home')) }}" placeholder="URL" class="w-1/2 rounded border-gray-300 text-sm">
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                            <label class="block text-xs font-bold text-gray-700 mb-2">Blog Link</label>
                                            <div class="flex gap-2">
                                                <input type="text" name="header_blog_text" value="{{ \App\Models\Setting::get('header_blog_text', 'Blog') }}" placeholder="Text" class="w-1/2 rounded border-gray-300 text-sm">
                                                <input type="text" name="header_blog_url" value="{{ \App\Models\Setting::get('header_blog_url', route('blog.index')) }}" placeholder="URL" class="w-1/2 rounded border-gray-300 text-sm">
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                            <label class="block text-xs font-bold text-gray-700 mb-2">About Link</label>
                                            <div class="flex gap-2">
                                                <input type="text" name="header_about_text" value="{{ \App\Models\Setting::get('header_about_text', 'About') }}" placeholder="Text" class="w-1/2 rounded border-gray-300 text-sm">
                                                <input type="text" name="header_about_url" value="{{ \App\Models\Setting::get('header_about_url', route('about')) }}" placeholder="URL" class="w-1/2 rounded border-gray-300 text-sm">
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                            <label class="block text-xs font-bold text-gray-700 mb-2">Contact Link</label>
                                            <div class="flex gap-2">
                                                <input type="text" name="header_contact_text" value="{{ \App\Models\Setting::get('header_contact_text', 'Contact') }}" placeholder="Text" class="w-1/2 rounded border-gray-300 text-sm">
                                                <input type="text" name="header_contact_url" value="{{ \App\Models\Setting::get('header_contact_url', route('contact')) }}" placeholder="URL" class="w-1/2 rounded border-gray-300 text-sm">
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-2">The standard top-bar links behaviour.</p>
                                </div>
                                
                                <div class="pt-6 border-t border-gray-100" x-data="{ 
                                    serviceLinks: {{ $servicesData }} 
                                }">
                                    <div class="flex flex-col mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-sm font-bold text-gray-900">Services Dropdown Menu</h4>
                                            <button type="button" @click="serviceLinks.push({name: '', url: '', description: '', icon: ''})" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg transition flex items-center">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                Add Service Item
                                            </button>
                                        </div>
                                        <p class="text-[11px] text-gray-500">Configure the nested items that appear when hovering over or tapping the "Services" link.</p>
                                    </div>
                                    <div class="space-y-4">
                                        <template x-for="(link, index) in serviceLinks" :key="index">
                                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-sm relative pr-10">
                                                <button type="button" @click="serviceLinks.splice(index, 1)" class="absolute top-4 right-4 text-red-400 hover:text-red-600 hover:bg-red-50 p-1 rounded transition" title="Remove Service">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Service Title</label>
                                                        <input type="text" x-model="link.name" :name="'header_services_menu['+index+'][name]'" placeholder="e.g., SEO Campaigns" class="w-full rounded border-gray-300 text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">Destination URL</label>
                                                        <input type="text" x-model="link.url" :name="'header_services_menu['+index+'][url]'" placeholder="URL or Route" class="w-full rounded border-gray-300 text-sm">
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Short Description (for Desktop Dropdown panel)</label>
                                                    <input type="text" x-model="link.description" :name="'header_services_menu['+index+'][description]'" placeholder="e.g., Complete managed ranking packages" class="w-full rounded border-gray-300 text-sm">
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Raw SVG Icon Code (Desktop Only)</label>
                                                    <textarea x-model="link.icon" :name="'header_services_menu['+index+'][icon]'" rows="3" placeholder='<svg class="h-6 w-6"...></svg>' class="w-full rounded border-gray-300 text-sm font-mono text-xs"></textarea>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="serviceLinks.length === 0" class="text-center py-6 bg-gray-50 rounded-xl border border-gray-200 border-dashed">
                                            <p class="text-sm text-gray-500">No custom services added yet. The system will fall back to default hardcoded items.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 5: Footer Content -->
                        <div x-show="activeTab === 'footer'" x-cloak class="space-y-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Footer Configurations</h3>
                                <p class="text-sm text-gray-500 mb-6">Manage footer description, social links, copyright, and three custom link columns.</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Footer Description</label>
                                    <textarea name="footer_description" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ \App\Models\Setting::get('footer_description', 'Elevating brands through data-driven SEO strategies, premium guest posts, and targeted digital marketing solutions designed for scalable growth.') }}</textarea>
                                </div>
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Copyright Text</label>
                                        <input type="text" name="footer_copyright_text" value="{{ \App\Models\Setting::get('footer_copyright_text', '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <p class="text-[11px] text-gray-500 mt-1">Example: "&copy; 2026 TrafficVai. All rights reserved."</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Bottom Right Attribution</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" name="footer_attribution_1" value="{{ \App\Models\Setting::get('footer_attribution_1', 'Designed for Growth') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Designed for Growth">
                                            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                                            <input type="text" name="footer_attribution_2" value="{{ \App\Models\Setting::get('footer_attribution_2', 'by SEO Experts') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="by SEO Experts">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-100">
                                <h4 class="text-sm font-bold text-gray-900 mb-4">Social Media Links</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Twitter URL</label>
                                        <input type="url" name="footer_social_twitter" value="{{ \App\Models\Setting::get('footer_social_twitter') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Instagram URL</label>
                                        <input type="url" name="footer_social_instagram" value="{{ \App\Models\Setting::get('footer_social_instagram') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">LinkedIn URL</label>
                                        <input type="url" name="footer_social_linkedin" value="{{ \App\Models\Setting::get('footer_social_linkedin') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Facebook URL</label>
                                        <input type="url" name="footer_social_facebook" value="{{ \App\Models\Setting::get('footer_social_facebook') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-100 grid grid-cols-1 xl:grid-cols-3 gap-6">
                                <!-- Col 1 -->
                                <div x-data="{ links: {{ $col1Data }} }" class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Column 1 Title</label>
                                    <input type="text" name="footer_col_1_title" value="{{ \App\Models\Setting::get('footer_col_1_title', 'Solutions') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm mb-4">
                                    
                                    <div class="flex items-center justify-between mb-2 pb-2 border-b border-gray-200">
                                        <label class="block text-xs font-bold text-gray-700 uppercase">Links</label>
                                        <button type="button" @click="links.push({name: '', url: ''})" class="text-[10px] font-bold text-indigo-600 bg-indigo-100 hover:bg-indigo-200 px-2 py-1.5 rounded transition">Add Link</button>
                                    </div>
                                    <div class="space-y-2 max-h-48 overflow-y-auto">
                                        <template x-for="(link, index) in links" :key="index">
                                            <div class="flex items-center gap-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm">
                                                <input type="text" x-model="link.name" :name="'footer_col_1_links['+index+'][name]'" placeholder="Name" class="w-full rounded border-gray-200 text-xs py-1.5">
                                                <input type="text" x-model="link.url" :name="'footer_col_1_links['+index+'][url]'" placeholder="URL" class="w-full rounded border-gray-200 text-xs py-1.5">
                                                <button type="button" @click="links.splice(index, 1)" class="text-red-500 hover:text-red-700 p-1 bg-red-50 rounded">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Col 2 -->
                                <div x-data="{ links: {{ $col2Data }} }" class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Column 2 Title</label>
                                    <input type="text" name="footer_col_2_title" value="{{ \App\Models\Setting::get('footer_col_2_title', 'Resources') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm mb-4">
                                    
                                    <div class="flex items-center justify-between mb-2 pb-2 border-b border-gray-200">
                                        <label class="block text-xs font-bold text-gray-700 uppercase">Links</label>
                                        <button type="button" @click="links.push({name: '', url: ''})" class="text-[10px] font-bold text-indigo-600 bg-indigo-100 hover:bg-indigo-200 px-2 py-1.5 rounded transition">Add Link</button>
                                    </div>
                                    <div class="space-y-2 max-h-48 overflow-y-auto">
                                        <template x-for="(link, index) in links" :key="index">
                                            <div class="flex items-center gap-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm">
                                                <input type="text" x-model="link.name" :name="'footer_col_2_links['+index+'][name]'" placeholder="Name" class="w-full rounded border-gray-200 text-xs py-1.5">
                                                <input type="text" x-model="link.url" :name="'footer_col_2_links['+index+'][url]'" placeholder="URL" class="w-full rounded border-gray-200 text-xs py-1.5">
                                                <button type="button" @click="links.splice(index, 1)" class="text-red-500 hover:text-red-700 p-1 bg-red-50 rounded">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Col 3 -->
                                <div x-data="{ links: {{ $col3Data }} }" class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Column 3 Title</label>
                                    <input type="text" name="footer_col_3_title" value="{{ \App\Models\Setting::get('footer_col_3_title', 'Legal') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm mb-4">
                                    
                                    <div class="flex items-center justify-between mb-2 pb-2 border-b border-gray-200">
                                        <label class="block text-xs font-bold text-gray-700 uppercase">Links</label>
                                        <button type="button" @click="links.push({name: '', url: ''})" class="text-[10px] font-bold text-indigo-600 bg-indigo-100 hover:bg-indigo-200 px-2 py-1.5 rounded transition">Add Link</button>
                                    </div>
                                    <div class="space-y-2 max-h-48 overflow-y-auto">
                                        <template x-for="(link, index) in links" :key="index">
                                            <div class="flex items-center gap-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm">
                                                <input type="text" x-model="link.name" :name="'footer_col_3_links['+index+'][name]'" placeholder="Name" class="w-full rounded border-gray-200 text-xs py-1.5">
                                                <input type="text" x-model="link.url" :name="'footer_col_3_links['+index+'][url]'" placeholder="URL" class="w-full rounded border-gray-200 text-xs py-1.5">
                                                <button type="button" @click="links.splice(index, 1)" class="text-red-500 hover:text-red-700 p-1 bg-red-50 rounded">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <!-- Footer Actions -->
                    <div class="bg-gray-50/50 px-6 py-4 border-t border-gray-100 flex items-center justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-colors duration-200">
                            Save Configuration
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
