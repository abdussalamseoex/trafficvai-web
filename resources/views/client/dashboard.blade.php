<x-app-layout>
    <x-slot name="header">
        <!-- Kept empty as title moved to navigation -->
    </x-slot>

    <div class="max-w-[1400px] mx-auto space-y-6">

        @php
            $activeNotices = \App\Models\Announcement::whereIn('type', ['notice', 'both'])
                ->where('status', 'sent')
                ->latest()
                ->take(3)
                ->get();
        @endphp
        @if($activeNotices->count() > 0)
            <div class="space-y-3 mb-6">
                @foreach($activeNotices as $notice)
                <div class="rounded-2xl bg-amber-50/80 p-4 border border-amber-200/60 shadow-sm">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold">📢</span>
                            <p class="text-sm text-slate-800 font-medium">
                                <strong class="font-bold text-slate-900 mr-1">{{ $notice->subject }}:</strong>
                                {{ Str::limit(html_entity_decode(strip_tags($notice->message)), 80) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="text-slate-500">{{ $notice->created_at->diffForHumans() }}</span>
                            <a href="{{ route('client.announcements.show', $notice) }}" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                                Read More
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
        
        @if(auth()->user()->balance < 10)
        <div class="mb-6 rounded-2xl bg-amber-50 p-4 border border-amber-200 flex items-center justify-between shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-bold text-amber-800 uppercase tracking-wider">Low Account Balance</h3>
                    <p class="text-sm text-amber-700 font-medium">Your current balance is <span class="price-convert" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 2) }}</span>. Top up now to ensure uninterrupted service.</p>
                </div>
            </div>
            <a href="{{ route('client.payments.topup') }}" class="text-sm font-black text-amber-900 bg-amber-200 px-4 py-2 rounded-xl hover:bg-amber-300 transition">
                Top Up
            </a>
        </div>
        @endif

        <!-- Executive Welcome Header matching Logo Colors (Slate Navy + Warm Amber) -->
        <div class="rounded-3xl bg-gradient-to-r from-[#0F172A] via-[#1E293B] to-[#0F172A] p-7 md:p-9 text-white shadow-xl border border-slate-700/60 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 w-full md:w-auto text-center md:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700 text-amber-400 text-xs font-bold uppercase tracking-wider mb-3">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Client Dashboard • Active Portal</span>
                </div>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-slate-300 font-medium text-sm md:text-base mt-1.5">Manage your SEO campaigns, automated traffic, and high-authority guest posts.</p>
            </div>
            <div class="relative z-10 flex flex-wrap gap-3 w-full md:w-auto justify-center md:justify-end">
                <a href="{{ route('client.traffic_campaign.builder') }}" class="px-5 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-amber-500/20 hover:shadow-amber-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center">
                    ⚡ Launch Website Traffic <span class="ml-1.5">&rarr;</span>
                </a>
                <a href="{{ route('client.services.index') }}" class="px-5 py-3 bg-white/10 hover:bg-white/15 border border-slate-600/80 text-white font-bold text-sm rounded-xl transition-all flex items-center justify-center">
                    Browse Packages <span class="ml-1.5">&rarr;</span>
                </a>
                <a href="{{ route('client.guest_posts.index') }}" class="px-5 py-3 bg-white/10 hover:bg-white/15 border border-slate-600/80 text-white font-bold text-sm rounded-xl transition-all flex items-center justify-center">
                    Guest Post Inventory
                </a>
            </div>
        </div>

        <!-- FEATURED INSTANT & PREMIUM SERVICES HIGHLIGHT BOXES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Website Traffic Suite Card -->
            <div class="rounded-3xl bg-white p-7 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-6">
                <div>
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            ⚡ REAL VISITORS ENGINE • INSTANT LAUNCH
                        </span>
                        @if(($activeTrafficCampaigns ?? 0) > 0)
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                                {{ $activeTrafficCampaigns }} Active Campaign{{ $activeTrafficCampaigns > 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Real Website Traffic (Direct & Organic)
                    </h3>
                    <p class="text-sm sm:text-base text-slate-600 mt-2.5 font-normal leading-relaxed">
                        Drive high-retention human-like visitors from Direct & Organic Google/Bing Search. Control stay durations (60s+), geo-targeting, and internal link clicks.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="{{ route('client.traffic_campaign.builder') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-sm transition">
                        <span>+ Launch New Campaign</span>
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="{{ route('client.traffic_campaign.index') }}"
                        class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-sm transition">
                        Manage Campaigns
                    </a>
                </div>
            </div>

            <!-- Guest Post Inventory Highlight Card -->
            <div class="rounded-3xl bg-white p-7 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-6">
                <div>
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                            ⭐ FEATURED INVENTORY • DOFOLLOW SEO
                        </span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        High-Authority Guest Posts
                    </h3>
                    <p class="text-sm sm:text-base text-slate-600 mt-2.5 font-normal leading-relaxed">
                        Publish articles on verified, high DA/DR websites with real organic traffic. Build powerful dofollow backlinks and accelerate your Google rankings.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="{{ route('client.guest_posts.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-sm transition">
                        <span>Browse Guest Post Sites</span>
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="{{ route('client.services.index') }}"
                        class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-sm transition">
                        SEO Packages
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Clean SaaS Metric Cards (4 cols) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Orders -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Orders</span>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $totalOrders }}</h3>
                <p class="text-xs font-medium text-slate-500 mt-1">All orders placed</p>
            </div>

            <!-- Active Orders -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Orders</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $activeOrders }}</h3>
                <p class="text-xs font-medium text-slate-500 mt-1">Currently in progress</p>
            </div>

            <!-- Completed Orders -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Completed</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $completedOrders }}</h3>
                <p class="text-xs font-medium text-slate-500 mt-1">Delivered successfully</p>
            </div>

            <!-- Wallet Balance -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 hover:border-slate-300 transition">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Balance</span>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-extrabold text-slate-900"><span class="price-convert" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 2) }}</span></h3>
                <div class="flex items-center justify-between mt-1">
                    <p class="text-xs font-medium text-slate-500">Wallet funds</p>
                    <a href="{{ route('client.payments.topup') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">+ Top Up</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Recent Orders Table -->
            <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 md:p-8 flex justify-between items-center border-b border-slate-100 bg-white">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">Recent Order Activity</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Your latest 5 active & completed orders</p>
                    </div>
                    <a href="{{ route('client.orders.index') }}" class="px-4 py-2 bg-slate-100 text-slate-800 text-xs font-bold rounded-xl hover:bg-slate-200 transition whitespace-nowrap">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-100">
                                <th class="px-8 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Order Details</th>
                                <th class="px-8 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider w-40">Status</th>
                                <th class="px-8 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider w-24 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-50/60 transition duration-150 group">
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold text-slate-900 line-clamp-1">
                                        @if($order->package)
                                            {{ $order->package->service->name ?? 'Package Delivery' }}
                                        @elseif($order->guestPostSite)
                                            Guest Post: {{ $order->guestPostSite->url }}
                                        @else
                                            Custom Service
                                        @endif
                                    </div>
                                    <div class="text-xs font-medium text-slate-500 mt-1 flex items-center">
                                        Order #{{ $order->id }} • <span class="price-convert ml-1" data-base-price="{{ $order->subtotal_display }}">${{ number_format($order->subtotal_display, 2) }}</span>
                                        @if($order->unread_messages_count > 0)
                                            <span class="ml-2 bg-amber-500 text-slate-950 text-[9px] font-extrabold px-1.5 py-0.5 rounded-full shadow-sm animate-pulse">
                                                {{ $order->unread_messages_count }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    @if($order->status == 'pending_payment')
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-1.5"></span>
                                            Awaiting Payment
                                        </div>
                                    @elseif($order->status == 'pending_requirements')
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200/60 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                            Action Required
                                        </div>
                                    @elseif($order->status == 'processing')
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200/60 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                            Active
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-xs font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Completed
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('client.orders.show', $order) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-16 text-center text-sm text-slate-400 font-medium">
                                    <div class="w-12 h-12 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                        <svg class="h-6 w-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    No orders found yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column Widgets -->
            <div class="xl:col-span-1 flex flex-col gap-6">
                <!-- Quick Launch & Growth Hub -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200/80 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider">
                            <span>⚡ Quick Navigation</span>
                        </span>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 mb-2">Ready to Boost Your Rankings?</h3>
                    <p class="text-slate-600 text-xs font-normal mb-6 leading-relaxed">Launch website traffic campaigns or browse premium SEO guest post inventory anytime.</p>
                    
                    <div class="space-y-3">
                        <a href="{{ route('client.traffic_campaign.builder') }}" class="flex items-center justify-between w-full px-5 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-sm transition-all">
                            <span>⚡ Launch Website Traffic</span>
                            <span>&rarr;</span>
                        </a>
                        <a href="{{ route('client.services.index') }}" class="flex items-center justify-between w-full px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-sm rounded-xl transition-all">
                            <span>Browse SEO Packages</span>
                            <span>&rarr;</span>
                        </a>
                        <a href="{{ route('client.guest_posts.index') }}" class="flex items-center justify-between w-full px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-sm rounded-xl transition-all">
                            <span>Guest Post Inventory</span>
                            <span class="text-slate-500">&rarr;</span>
                        </a>
                    </div>
                </div>
                
                <!-- Account Summary Card -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200/80 shadow-sm flex-1">
                    <h3 class="text-lg font-extrabold text-slate-900 mb-6">Account Overview</h3>
                    
                    <div class="space-y-5">
                        <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mr-3.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Available Balance</p>
                                    <a href="{{ route('client.payments.topup') }}" class="text-xs font-semibold text-amber-600 hover:underline">+ Add funds</a>
                                </div>
                            </div>
                            <span class="text-lg font-extrabold text-slate-900"><span class="price-convert" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 2) }}</span></span>
                        </div>
                        
                        <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mr-3.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Active Orders</p>
                                    <p class="text-xs text-slate-500">In progress</p>
                                </div>
                            </div>
                            <span class="text-lg font-extrabold text-slate-900">{{ $activeOrders }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mr-3.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Completed</p>
                                    <p class="text-xs text-slate-500">All time</p>
                                </div>
                            </div>
                            <span class="text-lg font-extrabold text-slate-900">{{ $completedOrders }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
