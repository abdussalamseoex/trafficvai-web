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
            <div class="space-y-4 mb-6">
                @foreach($activeNotices as $notice)
                <div class="rounded-xl bg-indigo-50 p-4 border border-indigo-200 shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1 md:flex md:justify-between items-center">
                            <p class="text-sm text-indigo-700">
                                <strong class="font-bold mr-1">{{ $notice->subject }}:</strong>
                                {{ Str::limit(html_entity_decode(strip_tags($notice->message)), 60) }}
                            </p>
                            <div class="mt-3 flex items-center text-sm md:mt-0 md:ml-6 text-indigo-500 space-x-4">
                                <span>{{ $notice->created_at->diffForHumans() }}</span>
                                <a href="{{ route('client.announcements.show', $notice) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-bold rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                    Read More
                                </a>
                            </div>
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

        <!-- Welcome Banner -->
        <div class="rounded-3xl bg-gradient-to-r from-brand-600 to-brand-400 p-8 md:p-10 text-white shadow-xl shadow-brand/20 relative overflow-hidden flex flex-col md:flex-row items-center justify-between">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white opacity-5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-gray-900 opacity-20 rounded-full blur-2xl"></div>
            
            <div class="relative z-10 w-full md:w-auto text-center md:text-left">
                <h2 class="text-3xl lg:text-4xl font-bold mb-2 text-white">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-brand-50 font-medium text-lg">Here's what's happening with your SEO campaigns today.</p>
            </div>
            <div class="relative z-10 mt-6 md:mt-0 flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                <a href="{{ route('client.services.index') }}" class="px-6 py-3 bg-white text-brand font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all text-center flex items-center justify-center">
                    Browse Packages <span class="ml-2">&rarr;</span>
                </a>
                <a href="{{ route('client.guest_posts.index') }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold rounded-xl backdrop-blur-sm transition-all text-center">
                    Guest Post Inventory
                </a>
            </div>
        </div>
        
        <!-- Quick Stats Cards (4 cols) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Orders -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 border-t-4 border-t-brand hover:-translate-y-1 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-brand/10 flex items-center justify-center text-brand">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <span class="px-2.5 py-1 bg-brand-50 text-brand text-[10px] font-bold uppercase tracking-wider rounded-lg">Total</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900">{{ $totalOrders }}</h3>
                <p class="text-sm font-semibold text-gray-400 mt-1">Total Orders</p>
            </div>

            <!-- Active Orders -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 border-t-4 border-t-blue-500 hover:-translate-y-1 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wider rounded-lg">Active</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900">{{ $activeOrders }}</h3>
                <p class="text-sm font-semibold text-gray-400 mt-1">Active Orders</p>
            </div>

            <!-- Completed -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 border-t-4 border-t-green-400 hover:-translate-y-1 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center text-green-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="px-2.5 py-1 bg-green-50 text-green-600 text-[10px] font-bold uppercase tracking-wider rounded-lg">Done</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900">{{ $completedOrders }}</h3>
                <p class="text-sm font-semibold text-gray-400 mt-1">Completed</p>
            </div>

            <!-- Wallet Balance -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 border-t-4 border-t-amber-400 hover:-translate-y-1 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                    <span class="px-2.5 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-wider rounded-lg">Balance</span>
                </div>
                <h3 class="text-3xl font-bold text-gray-900"><span class="price-convert" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 0) }}</span></h3>
                <p class="text-sm font-semibold text-gray-400 mt-1">Account Balance</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Recent Orders Table -->
            <div class="xl:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 flex justify-between items-center border-b border-gray-50 bg-white">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Recent Orders</h3>
                        <p class="text-sm text-gray-500 font-medium mt-1">Your latest 5 order activities</p>
                    </div>
                    <a href="{{ route('client.orders.index') }}" class="px-4 py-2 bg-brand-50 text-brand text-sm font-bold rounded-xl hover:bg-brand-100 transition whitespace-nowrap">View All &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-y border-gray-100">
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Order Info</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest w-40">Status</th>
                                <th class="px-8 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest w-24 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50/50 transition duration-150 group">
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold text-gray-900 line-clamp-1">
                                        @if($order->package)
                                            {{ $order->package->service->name ?? 'Package Delivery' }}
                                        @elseif($order->guestPostSite)
                                            Guest Post: {{ $order->guestPostSite->url }}
                                        @else
                                            Custom Service
                                        @endif
                                    </div>
                                    <div class="text-xs font-semibold text-gray-400 mt-1.5 flex items-center">
                                        Order #{{ $order->id }} • <span class="price-convert" data-base-price="{{ $order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount }}">${{ number_format($order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount, 2) }}</span>
                                        @if($order->unread_messages_count > 0)
                                            <span class="ml-2 bg-brand text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-sm animate-pulse">
                                                {{ $order->unread_messages_count }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    @if($order->status == 'pending_payment')
                                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-gray-50 border border-gray-200 text-gray-600 text-[11px] font-bold uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500 mr-1.5"></span>
                                            Awaiting Payment
                                        </div>
                                    @elseif($order->status == 'pending_requirements')
                                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-amber-50 border border-amber-100 text-amber-600 text-[11px] font-bold uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                            Action Required
                                        </div>
                                    @elseif($order->status == 'processing')
                                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-[11px] font-bold uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                            Active
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-green-50 border border-green-100 text-green-600 text-[11px] font-bold uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                            Completed
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('client.orders.show', $order) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 border border-gray-200 text-gray-400 group-hover:bg-brand-50 group-hover:text-brand group-hover:border-brand-200 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-16 text-center text-sm text-gray-400 font-medium">
                                    <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    You haven't placed any orders yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column Widgets -->
            <div class="xl:col-span-1 flex flex-col gap-6">
                <!-- Ready to Grow Card -->
                <div class="bg-gradient-to-br from-[#1a0f0a] to-[#26160f] rounded-3xl shadow-xl shadow-gray-900/10 overflow-hidden relative p-8 border border-white/5">
                    <div class="absolute inset-0 opacity-20">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-500 rounded-full blur-3xl"></div>
                    </div>
                    <div class="relative z-10">
                        <span class="inline-flex px-3 py-1 rounded-full bg-brand-900/50 border border-brand-500/30 text-brand-400 text-[10px] font-bold uppercase tracking-widest mb-4"><span class="mr-1">🚀</span> Grow Faster</span>
                        <h3 class="text-2xl font-bold text-white mb-3 leading-tight">Ready to Grow Your Rankings?</h3>
                        <p class="text-gray-400 text-sm font-medium mb-8 leading-relaxed">Explore premium SEO services and high-quality guest post placements to boost your visibility today.</p>
                        
                        <div class="space-y-3">
                            <a href="{{ route('client.services.index') }}" class="flex items-center justify-between w-full px-5 py-4 bg-brand text-white font-bold rounded-xl shadow-md hover:bg-brand-600 transition-all">
                                Browse Packages
                                <span>&rarr;</span>
                            </a>
                            <a href="{{ route('client.guest_posts.index') }}" class="flex items-center justify-between w-full px-5 py-4 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-all">
                                Guest Post Inventory
                                <span class="text-gray-400">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Account Summary -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Account Summary</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-center justify-between pb-6 border-b border-gray-50">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Available Balance</p>
                                    <a href="{{ route('client.payments.topup') }}" class="text-[11px] font-semibold text-gray-400 hover:text-brand transition">Tap + to add funds</a>
                                </div>
                            </div>
                            <span class="text-lg font-bold text-gray-900"><span class="price-convert" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 0) }}</span></span>
                        </div>
                        
                        <div class="flex items-center justify-between pb-6 border-b border-gray-50">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Active Orders</p>
                                    <p class="text-[11px] font-semibold text-gray-400">In progress</p>
                                </div>
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ $activeOrders }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-500 flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Completed</p>
                                    <p class="text-[11px] font-semibold text-gray-400">All time</p>
                                </div>
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ $completedOrders }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
