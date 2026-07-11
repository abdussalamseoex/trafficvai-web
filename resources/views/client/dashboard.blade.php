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
                <div class="rounded-2xl p-4 shadow-sm" style="background-color: #FFFBEB; border: 1px solid #FDE68A;">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-xl flex items-center justify-center font-bold" style="background-color: rgba(245, 158, 11, 0.15); color: #D97706;">📢</span>
                            <p class="text-sm font-medium" style="color: #1F2937;">
                                <strong class="font-bold mr-1" style="color: #111827;">{{ $notice->subject }}:</strong>
                                {{ Str::limit(html_entity_decode(strip_tags($notice->message)), 80) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span style="color: #6B7280;">{{ $notice->created_at->diffForHumans() }}</span>
                            <a href="{{ route('client.announcements.show', $notice) }}" class="px-3 py-1.5 rounded-lg font-bold transition" style="background-color: #111827; color: #FFFFFF;">
                                Read More
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
        
        @if(auth()->user()->balance < 10)
        <div class="mb-6 rounded-2xl p-4 flex items-center justify-between shadow-sm flex-wrap gap-4" style="background-color: #FFFBEB; border: 1px solid #FCD34D;">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #FEF3C7; color: #D97706;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider" style="color: #92400E;">Low Account Balance</h3>
                    <p class="text-sm font-medium" style="color: #B45309;">Your current balance is <span class="price-convert font-bold" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 2) }}</span>. Top up now to ensure uninterrupted service.</p>
                </div>
            </div>
            <a href="{{ route('client.payments.topup') }}" class="text-sm font-black px-5 py-2.5 rounded-xl transition shadow-sm" style="background-color: #F59E0B; color: #FFFFFF;">
                Top Up Balance &rarr;
            </a>
        </div>
        @endif

        <!-- Executive Welcome Header matching Logo Colors (Dark Charcoal Navy + Warm Orange Brand) -->
        <div class="rounded-3xl p-7 md:p-9 shadow-xl relative overflow-hidden flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6" style="background: linear-gradient(135deg, #0F1117 0%, #1E2330 100%); border: 1px solid rgba(232, 71, 10, 0.35); color: #FFFFFF;">
            <div class="relative z-10 w-full lg:w-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3" style="background-color: rgba(232, 71, 10, 0.2); border: 1px solid rgba(232, 71, 10, 0.4); color: #FB923C;">
                    <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: #F97316;"></span>
                    <span>Client Dashboard • Active Portal</span>
                </div>
                <h2 class="text-3xl lg:text-4xl font-extrabold tracking-tight" style="color: #FFFFFF;">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="font-medium text-sm md:text-base mt-2" style="color: #D1D5DB;">Manage your SEO campaigns, automated traffic, and high-authority guest posts.</p>
            </div>
            <div class="relative z-10 flex flex-wrap items-center gap-3 w-full lg:w-auto justify-start lg:justify-end">
                <a href="{{ route('client.traffic_campaign.builder') }}" class="px-5 py-3 font-extrabold text-sm rounded-xl shadow-lg transition-all flex items-center justify-center gap-2" style="background: linear-gradient(135deg, #E8470A 0%, #F97316 100%); color: #FFFFFF; box-shadow: 0 4px 14px rgba(232, 71, 10, 0.4);">
                    <span>⚡ Launch Website Traffic</span>
                    <span>&rarr;</span>
                </a>
                <a href="{{ route('client.services.index') }}" class="px-5 py-3 font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2" style="background-color: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); color: #FFFFFF;">
                    <span>Browse Packages</span>
                    <span>&rarr;</span>
                </a>
                <a href="{{ route('client.guest_posts.index') }}" class="px-5 py-3 font-bold text-sm rounded-xl transition-all flex items-center justify-center" style="background-color: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); color: #FFFFFF;">
                    Guest Post Inventory
                </a>
            </div>
        </div>

        <!-- FEATURED INSTANT & PREMIUM SERVICES HIGHLIGHT BOXES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Website Traffic Suite Card -->
            <div class="rounded-3xl p-7 sm:p-8 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-6" style="background-color: #FFFFFF; border: 2px solid #FED7AA;">
                <div>
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider" style="background-color: #FFF7ED; border: 1px solid #FDBA74; color: #C2410C;">
                            <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: #F97316;"></span>
                            ⚡ REAL VISITORS ENGINE • INSTANT LAUNCH
                        </span>
                        @if(($activeTrafficCampaigns ?? 0) > 0)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background-color: #ECFDF5; border: 1px solid #A7F3D0; color: #047857;">
                                {{ $activeTrafficCampaigns }} Active Campaign{{ $activeTrafficCampaigns > 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: #111827;">
                        Real Website Traffic (Direct & Organic)
                    </h3>
                    <p class="text-sm sm:text-base mt-2.5 font-normal leading-relaxed" style="color: #4B5563;">
                        Drive high-retention human-like visitors from Direct & Organic Google/Bing Search. Control stay durations (60s+), geo-targeting, and internal link clicks.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="{{ route('client.traffic_campaign.builder') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-bold text-sm shadow-md transition" style="background: linear-gradient(135deg, #E8470A 0%, #F97316 100%); color: #FFFFFF;">
                        <span>+ Launch New Campaign</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="{{ route('client.traffic_campaign.index') }}"
                        class="inline-flex items-center justify-center px-5 py-3.5 rounded-xl font-bold text-sm transition" style="background-color: #F3F4F6; border: 1px solid #D1D5DB; color: #1F2937;">
                        Manage Campaigns
                    </a>
                </div>
            </div>

            <!-- Guest Post Inventory Highlight Card -->
            <div class="rounded-3xl p-7 sm:p-8 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-6" style="background-color: #FFFFFF; border: 2px solid #A7F3D0;">
                <div>
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider" style="background-color: #ECFDF5; border: 1px solid #6EE7B7; color: #047857;">
                            ⭐ FEATURED INVENTORY • DOFOLLOW SEO
                        </span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: #111827;">
                        High-Authority Guest Posts
                    </h3>
                    <p class="text-sm sm:text-base mt-2.5 font-normal leading-relaxed" style="color: #4B5563;">
                        Publish articles on verified, high DA/DR websites with real organic traffic. Build powerful dofollow backlinks and accelerate your Google rankings.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="{{ route('client.guest_posts.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-bold text-sm shadow-md transition" style="background: linear-gradient(135deg, #059669 0%, #10B981 100%); color: #FFFFFF;">
                        <span>Browse Guest Post Sites</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="{{ route('client.services.index') }}"
                        class="inline-flex items-center justify-center px-5 py-3.5 rounded-xl font-bold text-sm transition" style="background-color: #F3F4F6; border: 1px solid #D1D5DB; color: #1F2937;">
                        SEO Packages
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Clean SaaS Metric Cards (4 cols) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Orders -->
            <div class="rounded-2xl p-6 shadow-sm transition" style="background-color: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color: #6B7280;">Total Orders</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #F3F4F6; color: #374151;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-extrabold" style="color: #111827;">{{ $totalOrders }}</h3>
                <p class="text-xs font-medium mt-1" style="color: #6B7280;">All orders placed</p>
            </div>

            <!-- Active Orders -->
            <div class="rounded-2xl p-6 shadow-sm transition" style="background-color: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color: #6B7280;">Active Orders</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #EFF6FF; color: #2563EB;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-extrabold" style="color: #111827;">{{ $activeOrders }}</h3>
                <p class="text-xs font-medium mt-1" style="color: #6B7280;">Currently in progress</p>
            </div>

            <!-- Completed Orders -->
            <div class="rounded-2xl p-6 shadow-sm transition" style="background-color: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color: #6B7280;">Completed</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #ECFDF5; color: #059669;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-extrabold" style="color: #111827;">{{ $completedOrders }}</h3>
                <p class="text-xs font-medium mt-1" style="color: #6B7280;">Delivered successfully</p>
            </div>

            <!-- Wallet Balance -->
            <div class="rounded-2xl p-6 shadow-sm transition" style="background-color: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color: #6B7280;">Account Balance</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #FFFBEB; color: #D97706;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-extrabold" style="color: #111827;"><span class="price-convert" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 2) }}</span></h3>
                <div class="flex items-center justify-between mt-1">
                    <p class="text-xs font-medium" style="color: #6B7280;">Wallet funds</p>
                    <a href="{{ route('client.payments.topup') }}" class="text-xs font-bold hover:underline" style="color: #D97706;">+ Top Up</a>
                </div>
            </div>
        </div>

        <!-- Lower Section Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Orders Table -->
            <div class="lg:col-span-2 rounded-2xl shadow-sm overflow-hidden" style="background-color: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="p-6 md:p-8 flex justify-between items-center" style="border-bottom: 1px solid #F3F4F6;">
                    <div>
                        <h3 class="text-lg font-extrabold" style="color: #111827;">Recent Order Activity</h3>
                        <p class="text-xs font-medium mt-0.5" style="color: #6B7280;">Your latest active & completed orders</p>
                    </div>
                    <a href="{{ route('client.orders.index') }}" class="px-4 py-2 text-xs font-bold rounded-xl transition whitespace-nowrap" style="background-color: #F3F4F6; color: #1F2937;">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-wider" style="color: #6B7280;">Order Details</th>
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-wider w-40" style="color: #6B7280;">Status</th>
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-wider w-24 text-right" style="color: #6B7280;">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50 transition duration-150 group">
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold line-clamp-1" style="color: #111827;">
                                        @if($order->package)
                                            {{ $order->package->service->name ?? 'Package Delivery' }}
                                        @elseif($order->guestPostSite)
                                            Guest Post: {{ $order->guestPostSite->url }}
                                        @else
                                            Custom Service
                                        @endif
                                    </div>
                                    <div class="text-xs font-medium mt-1 flex items-center" style="color: #6B7280;">
                                        Order #{{ $order->id }} • <span class="price-convert ml-1" data-base-price="{{ $order->subtotal_display }}">${{ number_format($order->subtotal_display, 2) }}</span>
                                        @if($order->unread_messages_count > 0)
                                            <span class="ml-2 text-[9px] font-extrabold px-1.5 py-0.5 rounded-full shadow-sm animate-pulse" style="background-color: #F59E0B; color: #111827;">
                                                {{ $order->unread_messages_count }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    @if($order->status == 'pending_payment')
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold" style="background-color: #F3F4F6; color: #374151;">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: #6B7280;"></span>
                                            Awaiting Payment
                                        </div>
                                    @elseif($order->status == 'pending_requirements')
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold" style="background-color: #FFFBEB; border: 1px solid #FDE68A; color: #B45309;">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: #F59E0B;"></span>
                                            Action Required
                                        </div>
                                    @elseif($order->status == 'processing')
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold" style="background-color: #EFF6FF; border: 1px solid #BFDBFE; color: #1D4ED8;">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: #3B82F6;"></span>
                                            Active
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold" style="background-color: #ECFDF5; border: 1px solid #A7F3D0; color: #047857;">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: #10B981;"></span>
                                            Completed
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('client.orders.show', $order) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition" style="background-color: #F3F4F6; color: #374151;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-16 text-center text-sm font-medium" style="color: #6B7280;">
                                    <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-3" style="background-color: #F3F4F6;">
                                        <svg class="h-6 w-6" style="color: #9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
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
            <div class="lg:col-span-1 flex flex-col gap-6">
                <!-- Quick Launch Hub -->
                <div class="rounded-2xl p-7 shadow-sm" style="background-color: #FFFFFF; border: 1px solid #E5E7EB;">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider" style="background-color: #F3F4F6; color: #374151;">
                            <span>⚡ Quick Navigation</span>
                        </span>
                    </div>
                    <h3 class="text-xl font-extrabold mb-2" style="color: #111827;">Ready to Boost Your Rankings?</h3>
                    <p class="text-xs font-normal mb-6 leading-relaxed" style="color: #4B5563;">Launch website traffic campaigns or browse premium SEO guest post inventory anytime.</p>
                    
                    <div class="space-y-3">
                        <a href="{{ route('client.traffic_campaign.builder') }}" class="flex items-center justify-between w-full px-5 py-3.5 font-bold text-sm rounded-xl shadow-sm transition-all" style="background: linear-gradient(135deg, #E8470A 0%, #F97316 100%); color: #FFFFFF;">
                            <span>⚡ Launch Website Traffic</span>
                            <span>&rarr;</span>
                        </a>
                        <a href="{{ route('client.services.index') }}" class="flex items-center justify-between w-full px-5 py-3.5 font-bold text-sm rounded-xl transition-all" style="background-color: #111827; color: #FFFFFF;">
                            <span>Browse SEO Packages</span>
                            <span>&rarr;</span>
                        </a>
                        <a href="{{ route('client.guest_posts.index') }}" class="flex items-center justify-between w-full px-5 py-3.5 font-bold text-sm rounded-xl transition-all" style="background-color: #F3F4F6; border: 1px solid #D1D5DB; color: #1F2937;">
                            <span>Guest Post Inventory</span>
                            <span style="color: #6B7280;">&rarr;</span>
                        </a>
                    </div>
                </div>
                
                <!-- Account Summary Card -->
                <div class="rounded-2xl p-7 shadow-sm flex-1" style="background-color: #FFFFFF; border: 1px solid #E5E7EB;">
                    <h3 class="text-lg font-extrabold mb-6" style="color: #111827;">Account Overview</h3>
                    
                    <div class="space-y-5">
                        <div class="flex items-center justify-between pb-5" style="border-bottom: 1px solid #F3F4F6;">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3.5" style="background-color: #FFFBEB; color: #D97706;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold" style="color: #111827;">Available Balance</p>
                                    <a href="{{ route('client.payments.topup') }}" class="text-xs font-semibold hover:underline" style="color: #D97706;">+ Add funds</a>
                                </div>
                            </div>
                            <span class="text-lg font-extrabold" style="color: #111827;"><span class="price-convert" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 2) }}</span></span>
                        </div>
                        
                        <div class="flex items-center justify-between pb-5" style="border-bottom: 1px solid #F3F4F6;">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3.5" style="background-color: #EFF6FF; color: #2563EB;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold" style="color: #111827;">Active Orders</p>
                                    <p class="text-xs" style="color: #6B7280;">In progress</p>
                                </div>
                            </div>
                            <span class="text-lg font-extrabold" style="color: #111827;">{{ $activeOrders }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3.5" style="background-color: #ECFDF5; color: #059669;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold" style="color: #111827;">Completed</p>
                                    <p class="text-xs" style="color: #6B7280;">All time</p>
                                </div>
                            </div>
                            <span class="text-lg font-extrabold" style="color: #111827;">{{ $completedOrders }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
