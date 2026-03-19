<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0F1117] text-white transition-transform duration-300 ease-in-out transform"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'md:translate-x-0': true}"
    x-cloak>
    
    <div class="flex items-center justify-center h-16 bg-[#0F1117] border-b border-gray-800/50">
        <a href="{{ route('home') }}" class="flex items-center justify-center w-full px-4">
            <img src="{{ asset('images/logo.png') }}" alt="TrafficVai" class="h-10 w-auto object-contain" />
        </a>
    </div>

    <!-- Mobile Close Button -->
    <div class="absolute top-0 right-0 p-4 md:hidden">
        <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="overflow-y-auto h-full pb-20">
        <nav class="mt-6 px-4 space-y-2">
            
            @if(Auth::user()->is_admin)
                <!-- Admin Navigation -->
                <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    {{ __('Dashboard') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('inbox')" :active="request()->routeIs('inbox')">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <span class="flex-1">{{ __('Inbox') }}</span>
                    <span 
                        class="ml-2 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"
                        x-data="{ count: {{ $unreadMessagesCount ?? 0 }} }"
                        x-show="count > 0"
                        style="display: none;"
                        :style="count > 0 ? 'display: inline-block;' : 'display: none;'"
                        @message-read.window="count = Math.max(0, count - $event.detail.count)"
                        x-text="count">
                    </span>
                </x-sidebar-link>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Services</p>
                <x-sidebar-dropdown title="Service Management" :active="request()->routeIs('admin.categories.*') || request()->routeIs('admin.services.*') || request()->routeIs('admin.guest-posts.*') || request()->routeIs('admin.traffic.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                        {{ __('Categories') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')">
                        {{ __('Services') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.guest-posts.index')" :active="request()->routeIs('admin.guest-posts.*')">
                        {{ __('Guest Posts') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.traffic.index')" :active="request()->routeIs('admin.traffic.*')">
                        {{ __('Website Traffic') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.link-building.index')" :active="request()->routeIs('admin.link-building.*')">
                        {{ __('Link Building') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">SEO & Content</p>
                <x-sidebar-dropdown title="SEO & Campaigns" :active="request()->routeIs('admin.campaigns.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </x-slot>
                    @php
                        $seoCampaigns = [
                            'keyword-research' => 'Keyword Research',
                            'on-page-seo' => 'On-Page SEO',
                            'technical-seo' => 'Technical SEO',
                            'local-seo' => 'Local SEO',
                            'content-seo' => 'Content SEO',
                            'seo-audit' => 'SEO Audit',
                            'monthly-seo' => 'Monthly SEO',
                            'e-commerce-seo' => 'E-Commerce SEO'
                        ];
                    @endphp
                    @foreach($seoCampaigns as $slug => $name)
                        <x-sidebar-link :href="route('admin.campaigns.index', $slug)" :active="request()->route('type') == $slug">
                            {{ __($name) }}
                        </x-sidebar-link>
                    @endforeach
                </x-sidebar-dropdown>

                <x-sidebar-dropdown title="SEO Manager" :active="request()->routeIs('admin.seo.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('admin.seo.index')" :active="request()->routeIs('admin.seo.index')">
                        {{ __('Overview') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.seo.pages')" :active="request()->routeIs('admin.seo.pages')">
                        {{ __('Page SEO') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.seo.services')" :active="request()->routeIs('admin.seo.services')">
                        {{ __('Service SEO') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.seo.posts')" :active="request()->routeIs('admin.seo.posts')">
                        {{ __('Blog SEO') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.seo.redirects.index')" :active="request()->routeIs('admin.seo.redirects.*')">
                        {{ __('Redirects') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.seo.settings')" :active="request()->routeIs('admin.seo.settings')">
                        {{ __('SEO Settings') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <x-sidebar-dropdown title="Blog & Content" :active="request()->routeIs('admin.posts.*') || request()->routeIs('admin.pages.*') || request()->routeIs('admin.home-sections.*') || request()->routeIs('admin.announcements.*') || request()->routeIs('admin.site-faqs.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('admin.posts.index')" :active="request()->routeIs('admin.posts.*')">
                        {{ __('Blog Posts') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.pages.index')" :active="request()->routeIs('admin.pages.*')">
                        {{ __('Static Pages') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.home-sections.index')" :active="request()->routeIs('admin.home-sections.*')">
                        {{ __('Home Page') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.announcements.index')" :active="request()->routeIs('admin.announcements.*')">
                        {{ __('Announcements') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.site-faqs.index')" :active="request()->routeIs('admin.site-faqs.*')">
                        {{ __('FAQs') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.media.index')" :active="request()->routeIs('admin.media.*')">
                        {{ __('Media Library') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Business</p>
                <x-sidebar-dropdown title="Payments Hub" :active="request()->routeIs('admin.payments.*') || request()->routeIs('admin.gateway-settings.*') || request()->routeIs('admin.finance.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.index')">
                        {{ __('Overview') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.payments.transactions')" :active="request()->routeIs('admin.payments.transactions')">
                        {{ __('All Transactions') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.payments.topups')" :active="request()->routeIs('admin.payments.topups')">
                        <span class="flex-1">{{ __('Top-up Requests') }}</span>
                        @if(($pendingTopupsCount ?? \App\Models\TopupRequest::where('status', 'pending')->count()) > 0)
                            <span class="ml-2 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $pendingTopupsCount ?? \App\Models\TopupRequest::where('status', 'pending')->count() }}
                            </span>
                        @endif
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.gateway-settings.index')" :active="request()->routeIs('admin.gateway-settings.*')">
                        {{ __('Gateway Settings') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.invoices.index')" :active="request()->routeIs('admin.invoices.*')">
                        {{ __('Invoices') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.invoice-services.index')" :active="request()->routeIs('admin.invoice-services.*')">
                        {{ __('Predefined Services') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.finance.index')" :active="request()->routeIs('admin.finance.*')">
                        {{ __('Revenue Reports') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <x-sidebar-dropdown title="Sales & Marketing" :active="request()->routeIs('admin.orders.*') || request()->routeIs('admin.coupons.*') || request()->routeIs('admin.analytics.*') || request()->routeIs('admin.affiliates.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.index')">
                        <span class="flex-1">{{ __('All Orders') }}</span>
                        @if(($unreadOrdersCount ?? 0) > 0)
                            <span class="ml-2 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full" title="New Orders">
                                {{ $unreadOrdersCount }}
                            </span>
                        @endif
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.orders.running')" :active="request()->routeIs('admin.orders.running')">
                        {{ __('Running Orders') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.coupons.index')" :active="request()->routeIs('admin.coupons.*')">
                        {{ __('Coupons & Discounts') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.analytics.index')" :active="request()->routeIs('admin.analytics.*')">
                        {{ __('Website Analytics') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.affiliates.index')" :active="request()->routeIs('admin.affiliates.*')">
                        {{ __('Affiliate Management') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Team</p>
                <x-sidebar-dropdown title="Clients & Team" :active="request()->routeIs('admin.users.*') || request()->routeIs('admin.staff.*') || request()->routeIs('admin.leads.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        {{ __('Clients') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.leads.index')" :active="request()->routeIs('admin.leads.*')">
                        <span class="flex-1">{{ __('Leads') }}</span>
                        @if(($unreadLeadsCount ?? 0) > 0)
                            <span class="ml-2 bg-yellow-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $unreadLeadsCount }}
                            </span>
                        @endif
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.staff.index')" :active="request()->routeIs('admin.staff.*')">
                        {{ __('Team Members') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">System</p>
                <x-sidebar-dropdown title="Notification Hub" :active="request()->routeIs('admin.notifications.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('admin.notifications.index')" :active="request()->routeIs('admin.notifications.index')">
                        {{ __('Overview') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.notifications.templates.index')" :active="request()->routeIs('admin.notifications.templates.*')">
                        {{ __('Email Templates') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.notifications.logs')" :active="request()->routeIs('admin.notifications.logs')">
                        {{ __('Delivery Logs') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.notifications.settings')" :active="request()->routeIs('admin.notifications.settings')">
                        {{ __('Global Settings') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.notifications.toggles.index')" :active="request()->routeIs('admin.notifications.toggles.*')">
                        {{ __('Email Toggles') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <x-sidebar-dropdown title="Settings & Support" :active="request()->routeIs('admin.site-settings.*') || request()->routeIs('admin.support.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('admin.support.index')" :active="request()->routeIs('admin.support.*')">
                        {{ __('Support Tickets') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.site-settings.index')" :active="request()->routeIs('admin.site-settings.*')">
                        {{ __('Website Settings') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <x-sidebar-link :href="route('admin.updates.index')" :active="request()->routeIs('admin.updates.*')">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    {{ __('System Update') }}
                    @if(session('update_available'))
                        <span class="ml-2 flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    @endif
                </x-sidebar-link>
            @else
                <!-- Client Navigation -->
                <div class="px-5 py-5 mb-4 bg-gradient-to-br from-brand-600 to-brand text-white rounded-2xl mx-4 mt-4 shadow-lg shadow-brand/20 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="h-full w-full" fill="currentColor"><pattern id="dots" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="2"></circle></pattern><rect width="100%" height="100%" fill="url(#dots)"></rect></svg>
                    </div>
                    <div class="relative z-10 flex flex-col">
                        <p class="text-[10px] font-bold text-white/70 uppercase tracking-widest mb-1">Available Balance</p>
                        <div class="flex items-center justify-between mt-1">
                            <h4 class="text-2xl font-heading font-extrabold">
                                <span class="price-convert" data-base-price="{{ Auth::user()->balance }}">${{ number_format(Auth::user()->balance, 2) }}</span>
                            </h4>
                            <a href="{{ route('client.payments.topup') }}" class="w-8 h-8 flex items-center justify-center bg-white/20 rounded-lg hover:bg-white/30 transition backdrop-blur-sm" title="Top-up">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <x-sidebar-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    {{ __('Dashboard') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('inbox')" :active="request()->routeIs('inbox')">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <span class="flex-1">{{ __('Messages') }}</span>
                    <span 
                        class="ml-2 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full dropdown-badge"
                        x-data="{ count: {{ $unreadClientMessagesCount ?? 0 }} }"
                        x-show="count > 0"
                        style="display: none;"
                        :style="count > 0 ? 'display: inline-block;' : 'display: none;'"
                        @message-read.window="count = Math.max(0, count - $event.detail.count)"
                        x-text="count">
                    </span>
                </x-sidebar-link>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Management</p>
                <x-sidebar-dropdown title="Payments & Billing" :active="request()->routeIs('client.payments.*') || request()->routeIs('client.invoices.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('client.payments.index')" :active="request()->routeIs('client.payments.index')">
                        {{ __('My Wallet') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('client.payments.topup')" :active="request()->routeIs('client.payments.topup')">
                        {{ __('Add Balance') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('client.invoices.index')" :active="request()->routeIs('client.invoices.*')">
                        {{ __('Transaction History') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <x-sidebar-dropdown title="My Workspace" :active="request()->routeIs('client.projects.*') || request()->routeIs('client.orders.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('client.projects.index')" :active="request()->routeIs('client.projects.*')">
                        {{ __('My Projects') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('client.orders.index')" :active="request()->routeIs('client.orders.index')">
                        <span class="flex-1">{{ __('My Orders') }}</span>
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('client.orders.running')" :active="request()->routeIs('client.orders.running')">
                        <span class="flex-1">{{ __('Running Orders') }}</span>
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Shop & Services</p>
                <x-sidebar-dropdown title="Shop & Services" :active="request()->routeIs('client.services.*') || request()->routeIs('client.guest_posts.*') || request()->routeIs('client.traffic.*') || request()->routeIs('client.link_building.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('client.services.index')" :active="request()->routeIs('client.services.*')">
                        {{ __('Services') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('client.guest_posts.index')" :active="request()->routeIs('client.guest_posts.*')">
                        {{ __('Guest Posts') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('client.traffic.index')" :active="request()->routeIs('client.traffic.*')">
                        {{ __('Website Traffic') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('client.link_building.index')" :active="request()->routeIs('client.link_building.*')">
                        {{ __('Link Building') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <x-sidebar-dropdown title="SEO Campaigns" :active="request()->routeIs('client.campaigns.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </x-slot>
                    @php
                        $seoCampaigns = [
                            'keyword-research' => 'Keyword Research',
                            'on-page-seo' => 'On-Page SEO',
                            'technical-seo' => 'Technical SEO',
                            'local-seo' => 'Local SEO',
                            'content-seo' => 'Content SEO',
                            'seo-audit' => 'SEO Audit',
                            'monthly-seo' => 'Monthly SEO',
                            'e-commerce-seo' => 'E-Commerce SEO'
                        ];
                    @endphp
                    @foreach($seoCampaigns as $slug => $name)
                        <x-sidebar-link :href="route('client.campaigns.index', $slug)" :active="request()->route('type') == $slug">
                            {{ __($name) }}
                        </x-sidebar-link>
                    @endforeach
                </x-sidebar-dropdown>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Support</p>
                <x-sidebar-dropdown title="Help & Resources" :active="request()->routeIs('client.support.*') || request()->routeIs('client.faq.*') || request()->routeIs('client.announcements.*') || request()->routeIs('client.tools.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('client.support.index')" :active="request()->routeIs('client.support.*')">
                        {{ __('Support Tickets') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('client.faq.index')" :active="request()->routeIs('client.faq.*')">
                        {{ __('Knowledge Base / FAQ') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('client.announcements.index')" :active="request()->routeIs('client.announcements.*')">
                        {{ __('Announcements') }}
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('client.tools.index')" :active="request()->routeIs('client.tools.*')">
                        {{ __('Free SEO Tools') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Account</p>
                <x-sidebar-dropdown title="Profile" :active="request()->routeIs('profile.edit')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                        {{ __('Profile Settings') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>

                <x-sidebar-dropdown title="Earn" :active="request()->routeIs('client.affiliate.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </x-slot>
                    <x-sidebar-link :href="route('client.affiliate.index')" :active="request()->routeIs('client.affiliate.*')">
                        {{ __('Affiliate Program') }}
                    </x-sidebar-link>
                </x-sidebar-dropdown>
            @endif

        </nav>
    </div>

    <!-- Sidebar Footer (User Info) -->
    <div class="absolute bottom-0 left-0 right-0 p-4 bg-[#0F1117] border-t border-gray-800/50">
        <a href="{{ route('profile.edit') }}" class="flex items-center w-full focus:outline-none hover:bg-white/5 p-2 rounded-xl transition duration-150 group">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-brand/20 text-brand flex items-center justify-center font-bold text-lg leading-none">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="ml-3 flex-1 overflow-hidden">
                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->is_admin ? 'Administrator' : 'Client' }}</p>
            </div>
            <div class="flex-shrink-0 ml-2 text-gray-500 group-hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
            </div>
        </a>
    </div>
</aside>
