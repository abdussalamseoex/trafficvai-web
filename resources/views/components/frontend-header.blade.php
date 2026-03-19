<nav x-data="{ mobileMenuOpen: false, servicesDropdownOpen: false }" @keydown.escape="servicesDropdownOpen = false" @click.away="servicesDropdownOpen = false" class="bg-blue-600 sticky top-0 z-50 shadow-md transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <div class="flex items-center shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    @php $logoPath = \App\Models\Setting::get('site_logo') ? asset(\App\Models\Setting::get('site_logo')) : asset('images/logo.png'); @endphp
                    <img src="{{ $logoPath }}" alt="TrafficVai" class="h-8 md:h-12 w-auto object-contain transition-transform duration-300 hover:scale-105" />
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-1 lg:space-x-4">
                
                <!-- Currency Switcher (Desktop) -->
                <div class="relative" x-data="{ currencyDropdownOpen: false }" @keydown.escape="currencyDropdownOpen = false" @click.away="currencyDropdownOpen = false">
                    <button @click="currencyDropdownOpen = !currencyDropdownOpen" type="button" class="flex items-center gap-x-1 px-3 py-2 rounded-lg text-sm font-semibold text-blue-100 hover:text-white hover:bg-white/10 transition outline-none" :aria-expanded="currencyDropdownOpen">
                        <span x-text="$store.currency ? $store.currency.current : 'USD'">USD</span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180 text-white': currencyDropdownOpen, 'text-blue-200': !currencyDropdownOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="currencyDropdownOpen" 
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         x-transition:leave="transition ease-in duration-150" 
                         x-transition:leave-start="opacity-100 translate-y-0" 
                         x-transition:leave-end="opacity-0 translate-y-2" 
                         class="absolute right-0 z-50 mt-2 w-32 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" x-cloak style="display: none;">
                        <div class="py-1">
                            <button @click="$store.currency ? $store.currency.setCurrency('USD') : null; currencyDropdownOpen = false" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 font-medium">🇺🇸 USD ($)</button>
                            <button @click="$store.currency ? $store.currency.setCurrency('BDT') : null; currencyDropdownOpen = false" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 font-medium">🇧🇩 BDT (৳)</button>
                            <button @click="$store.currency ? $store.currency.setCurrency('EUR') : null; currencyDropdownOpen = false" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 font-medium">🇪🇺 EUR (€)</button>
                        </div>
                    </div>
                </div>

                <a href="{{ \App\Models\Setting::get('header_home_url', route('home')) }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->url() == \App\Models\Setting::get('header_home_url', route('home')) || request()->routeIs('home') ? 'text-white bg-white/20' : 'text-blue-100 hover:text-white hover:bg-white/10' }} transition">{{ \App\Models\Setting::get('header_home_text', 'Home') }}</a>
                
                <!-- Services Dropdown (Desktop) -->
                <div class="relative">
                    <button @click="servicesDropdownOpen = !servicesDropdownOpen" type="button" class="flex items-center gap-x-1 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->is('services*') || request()->is('campaigns*') || request()->is('seo-campaigns*') || request()->is('link-building*') || request()->is('guest-posts*') || request()->is('website-traffic*') ? 'text-white bg-white/20' : 'text-blue-100 hover:text-white hover:bg-white/10' }} transition outline-none" :aria-expanded="servicesDropdownOpen">
                        Services
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180 text-white': servicesDropdownOpen, 'text-blue-200': !servicesDropdownOpen }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown Panel -->
                    <div x-show="servicesDropdownOpen" 
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         x-transition:leave="transition ease-in duration-150" 
                         x-transition:leave-start="opacity-100 translate-y-0" 
                         x-transition:leave-end="opacity-0 translate-y-2" 
                         class="absolute left-1/2 z-50 mt-4 flex w-screen max-w-max -translate-x-1/2 px-4" x-cloak style="display: none;">
                        <div class="w-screen max-w-md flex-auto overflow-hidden rounded-3xl bg-white text-sm leading-6 shadow-2xl shadow-indigo-100 ring-1 ring-gray-900/5">
                            <div class="p-4 grid grid-cols-1 gap-2">
                                @php
                                    $servicesMenuStr = \App\Models\Setting::get('header_services_menu', '[]');
                                    $servicesMenu = is_string($servicesMenuStr) ? json_decode($servicesMenuStr, true) : $servicesMenuStr;
                                    $servicesMenu = is_array($servicesMenu) ? $servicesMenu : [];
                                @endphp
                                
                                @if(count($servicesMenu) > 0)
                                    @foreach($servicesMenu as $serviceLink)
                                        @if(isset($serviceLink['name']) && isset($serviceLink['url']))
                                            <a href="{{ $serviceLink['url'] }}" class="group relative flex gap-x-6 rounded-2xl p-4 hover:bg-indigo-50 transition">
                                                @if(isset($serviceLink['icon']) && !empty($serviceLink['icon']))
                                                    <div class="mt-1 flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-gray-50 group-hover:bg-white group-hover:shadow-sm transition text-gray-600 group-hover:text-indigo-600">
                                                        {!! $serviceLink['icon'] !!}
                                                    </div>
                                                @else
                                                    <div class="mt-1 flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-gray-50 group-hover:bg-white group-hover:shadow-sm transition">
                                                        <svg class="h-6 w-6 text-gray-600 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition">{{ $serviceLink['name'] }} <span class="absolute inset-0"></span></div>
                                                    @if(isset($serviceLink['description']))
                                                        <p class="mt-1 text-gray-500 font-medium text-xs">{{ $serviceLink['description'] }}</p>
                                                    @endif
                                                </div>
                                            </a>
                                        @endif
                                    @endforeach
                                @else
                                    <a href="{{ route('seo_campaigns.index', 'seo-campaigns') }}" class="group relative flex gap-x-6 rounded-2xl p-4 hover:bg-indigo-50 transition">
                                        <div class="mt-1 flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-gray-50 group-hover:bg-white group-hover:shadow-sm transition">
                                            <svg class="h-6 w-6 text-gray-600 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition">SEO Campaigns <span class="absolute inset-0"></span></div>
                                            <p class="mt-1 text-gray-500 font-medium text-xs">Complete managed ranking packages</p>
                                        </div>
                                    </a>
                                    <a href="{{ route('services.index') }}" class="group relative flex gap-x-6 rounded-2xl p-4 hover:bg-indigo-50 transition">
                                        <div class="mt-1 flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-gray-50 group-hover:bg-white group-hover:shadow-sm transition">
                                            <svg class="h-6 w-6 text-gray-600 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition">High DA Link Building <span class="absolute inset-0"></span></div>
                                            <p class="mt-1 text-gray-500 font-medium text-xs">Powerful contextual editorial links</p>
                                        </div>
                                    </a>
                                    <a href="{{ route('guest_posts.index') }}" class="group relative flex gap-x-6 rounded-2xl p-4 hover:bg-indigo-50 transition">
                                        <div class="mt-1 flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-gray-50 group-hover:bg-white group-hover:shadow-sm transition">
                                            <svg class="h-6 w-6 text-gray-600 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition">Guest Post Inventory <span class="absolute inset-0"></span></div>
                                            <p class="mt-1 text-gray-500 font-medium text-xs">Browse real partner websites instantly</p>
                                        </div>
                                    </a>
                                    <a href="{{ route('traffic.index') }}" class="group relative flex gap-x-6 rounded-2xl p-4 hover:bg-indigo-50 transition">
                                        <div class="mt-1 flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-gray-50 group-hover:bg-white group-hover:shadow-sm transition">
                                            <svg class="h-6 w-6 text-gray-600 group-hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition">Website Traffic <span class="absolute inset-0"></span></div>
                                            <p class="mt-1 text-gray-500 font-medium text-xs">Boost your organic analytics signals</p>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $headerMenuStr = \App\Models\Setting::get('header_menu', '[]');
                    $headerMenu = is_string($headerMenuStr) ? json_decode($headerMenuStr, true) : $headerMenuStr;
                    $headerMenu = is_array($headerMenu) ? $headerMenu : [];
                @endphp
                @foreach($headerMenu as $link)
                    @if(isset($link['name']) && isset($link['url']))
                        <a href="{{ $link['url'] }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->url() == $link['url'] ? 'text-white bg-white/20' : 'text-blue-100 hover:text-white hover:bg-white/10' }} transition">{{ $link['name'] }}</a>
                    @endif
                @endforeach

                <a href="{{ \App\Models\Setting::get('header_blog_url', route('blog.index')) }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->url() == \App\Models\Setting::get('header_blog_url', route('blog.index')) || request()->routeIs('blog.*') ? 'text-white bg-white/20' : 'text-blue-100 hover:text-white hover:bg-white/10' }} transition">{{ \App\Models\Setting::get('header_blog_text', 'Blog') }}</a>
                <a href="{{ \App\Models\Setting::get('header_about_url', route('about')) }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->url() == \App\Models\Setting::get('header_about_url', route('about')) || request()->routeIs('about') ? 'text-white bg-white/20' : 'text-blue-100 hover:text-white hover:bg-white/10' }} transition">{{ \App\Models\Setting::get('header_about_text', 'About') }}</a>
                <a href="{{ \App\Models\Setting::get('header_contact_url', route('contact')) }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->url() == \App\Models\Setting::get('header_contact_url', route('contact')) || request()->routeIs('contact') ? 'text-white bg-white/20' : 'text-blue-100 hover:text-white hover:bg-white/10' }} transition">{{ \App\Models\Setting::get('header_contact_text', 'Contact') }}</a>
                
                <div class="h-6 w-px bg-blue-500 mx-2"></div>

                @auth
                    <a href="{{ route('dashboard') }}" class="bg-brand text-white hover:bg-brand-600 px-5 py-2.5 rounded-md font-bold transition flex items-center gap-2">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:text-blue-100 px-3 py-2 rounded-lg text-sm font-semibold transition">Log in</a>
                    <a href="{{ route('register') }}" class="bg-brand text-white text-sm px-6 py-2.5 rounded-md font-bold hover:bg-brand-600 shadow-sm transition transform hover:-translate-y-0.5 ml-2">
                        Start Growth
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex md:hidden items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-blue-100 hover:text-white focus:outline-none p-2 rounded-lg hover:bg-blue-700 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Drawer -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden bg-blue-700 py-4 px-4 shadow-xl max-h-[85vh] overflow-y-auto" x-cloak style="display: none;">
        
        <div class="space-y-1 mb-4">
            
            <!-- Currency Switcher (Mobile) -->
            <div class="px-4 py-3 bg-blue-800/50 rounded-lg flex items-center justify-between mb-2">
                <span class="text-blue-200 text-sm font-semibold">Currency</span>
                <select @change="$store.currency ? $store.currency.setCurrency($event.target.value) : null" :value="$store.currency ? $store.currency.current : 'USD'" class="bg-blue-900 border-none text-white text-sm font-bold rounded focus:ring-0 py-1 pl-2 pr-8 cursor-pointer">
                    <option value="USD">USD ($)</option>
                    <option value="BDT">BDT (৳)</option>
                    <option value="EUR">EUR (€)</option>
                </select>
            </div>

            <a href="{{ \App\Models\Setting::get('header_home_url', route('home')) }}" class="block px-4 py-3 {{ request()->url() == \App\Models\Setting::get('header_home_url', route('home')) || request()->routeIs('home') ? 'text-white bg-white/20 font-bold' : 'text-blue-100 hover:text-white hover:bg-white/10 font-semibold' }} rounded-lg transition">{{ \App\Models\Setting::get('header_home_text', 'Home') }}</a>
            
            <div class="pt-2 pb-1 px-4 text-xs font-bold text-blue-300 uppercase tracking-wider">Services</div>
            
            @php
                $servicesMenuStr = \App\Models\Setting::get('header_services_menu', '[]');
                $servicesMenu = is_string($servicesMenuStr) ? json_decode($servicesMenuStr, true) : $servicesMenuStr;
                $servicesMenu = is_array($servicesMenu) ? $servicesMenu : [];
            @endphp
            
            @if(count($servicesMenu) > 0)
                @foreach($servicesMenu as $serviceLink)
                    @if(isset($serviceLink['name']) && isset($serviceLink['url']))
                        <a href="{{ $serviceLink['url'] }}" class="block px-4 py-3 {{ request()->url() == $serviceLink['url'] ? 'text-white bg-white/20 font-bold' : 'text-blue-100 hover:text-white hover:bg-white/10 font-semibold' }} rounded-lg transition pl-6 select-none flex items-center gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-brand"></div> {{ $serviceLink['name'] }}
                        </a>
                    @endif
                @endforeach
            @else
                <a href="{{ route('seo_campaigns.index', 'seo-campaigns') }}" class="block px-4 py-3 {{ request()->is('seo-campaigns*') ? 'text-white bg-white/20 font-bold' : 'text-blue-100 hover:text-white hover:bg-white/10 font-semibold' }} rounded-lg transition pl-6 select-none flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-brand"></div> SEO Campaigns
                </a>
                <a href="{{ route('services.index') }}" class="block px-4 py-3 {{ request()->is('services*') ? 'text-white bg-white/20 font-bold' : 'text-blue-100 hover:text-white hover:bg-white/10 font-semibold' }} rounded-lg transition pl-6 select-none flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-brand"></div> High DA Link Building
                </a>
                <a href="{{ route('guest_posts.index') }}" class="block px-4 py-3 {{ request()->routeIs('guest_posts.*') ? 'text-white bg-white/20 font-bold' : 'text-blue-100 hover:text-white hover:bg-white/10 font-semibold' }} rounded-lg transition pl-6 select-none flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-brand"></div> Guest Post Inventory
                </a>
                <a href="{{ route('traffic.index') }}" class="block px-4 py-3 {{ request()->is('website-traffic*') ? 'text-white bg-white/20 font-bold' : 'text-blue-100 hover:text-white hover:bg-white/10 font-semibold' }} rounded-lg transition pl-6 select-none flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-brand"></div> Website Traffic
                </a>
            @endif

            <div class="pt-2 pb-1 px-4 text-xs font-bold text-blue-300 uppercase tracking-wider mt-2">More</div>
            
            @php
                $headerMenuStr = \App\Models\Setting::get('header_menu', '[]');
                $headerMenu = is_string($headerMenuStr) ? json_decode($headerMenuStr, true) : $headerMenuStr;
                $headerMenu = is_array($headerMenu) ? $headerMenu : [];
            @endphp
            @foreach($headerMenu as $link)
                @if(isset($link['name']) && isset($link['url']))
                    <a href="{{ $link['url'] }}" class="block px-4 py-3 {{ request()->url() == $link['url'] ? 'text-white bg-white/20 font-bold' : 'text-blue-100 hover:text-white hover:bg-white/10 font-semibold' }} rounded-lg transition">{{ $link['name'] }}</a>
                @endif
            @endforeach
            
             <a href="{{ \App\Models\Setting::get('header_blog_url', route('blog.index')) }}" class="block px-4 py-3 {{ request()->url() == \App\Models\Setting::get('header_blog_url', route('blog.index')) || request()->routeIs('blog.*') ? 'text-white bg-white/20 font-bold' : 'text-blue-100 hover:text-white hover:bg-white/10 font-semibold' }} rounded-lg transition">{{ \App\Models\Setting::get('header_blog_text', 'Blog') }}</a>
             <a href="{{ \App\Models\Setting::get('header_about_url', route('about')) }}" class="block px-4 py-3 {{ request()->url() == \App\Models\Setting::get('header_about_url', route('about')) || request()->routeIs('about') ? 'text-white bg-white/20 font-bold' : 'text-blue-100 hover:text-white hover:bg-white/10 font-semibold' }} rounded-lg transition">{{ \App\Models\Setting::get('header_about_text', 'About Us') }}</a>
             <a href="{{ \App\Models\Setting::get('header_contact_url', route('contact')) }}" class="block px-4 py-3 {{ request()->url() == \App\Models\Setting::get('header_contact_url', route('contact')) || request()->routeIs('contact') ? 'text-white bg-white/20 font-bold' : 'text-blue-100 hover:text-white hover:bg-white/10 font-semibold' }} rounded-lg transition">{{ \App\Models\Setting::get('header_contact_text', 'Contact Us') }}</a>
        </div>
        
        <div class="border-t border-blue-600 pt-4 mt-2 mb-2 px-2 flex flex-col gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="block w-full py-4 bg-brand text-white text-center rounded-lg font-bold shadow-md shadow-brand/20">Go to Dashboard</a>
            @else
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('login') }}" class="block w-full py-3 bg-blue-800 text-blue-100 text-center rounded-lg font-bold transition hover:bg-blue-900 hover:text-white">Log in</a>
                    <a href="{{ route('register') }}" class="block w-full py-3 bg-brand text-white text-center rounded-lg font-bold shadow-md shadow-brand/20 transition hover:bg-brand-600">Start Growth</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
