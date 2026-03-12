<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Business Intelligence & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-indigo-600 rounded-3xl p-6 shadow-xl shadow-indigo-100 text-white">
                    <p class="text-indigo-100 text-sm font-bold uppercase tracking-wider mb-1">Total Revenue</p>
                    <h3 class="text-3xl font-black">${{ number_format($totalRevenue, 2) }}</h3>
                    <p class="text-xs mt-2 text-indigo-100 italic">Net revenue from completed orders</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-1">Total Orders</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $totalOrders }}</h3>
                    <p class="text-xs mt-2 text-gray-500">Total volume processed</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-1">Total Clients</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $totalUsers }}</h3>
                    <p class="text-xs mt-2 text-gray-500">Registered non-admin users</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-1">Leads</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $totalLeads }}</h3>
                    <p class="text-xs mt-2 text-gray-500">Contact requests received</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Activity -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                        <h4 class="text-lg font-bold text-gray-900 mb-6">Recent Successful Orders</h4>
                        <div class="space-y-4">
                            @foreach($recentOrders as $order)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 transition hover:bg-white hover:shadow-md">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($order->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">{{ $order->user->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $order->package->service->name ?? ($order->guestPostSite->domain ?? 'SEO Service') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-indigo-600 text-sm">${{ number_format($order->total_amount, 2) }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-6 text-center">
                            <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">View All Sales &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Growth Insight -->
                <div class="space-y-6">
                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                        <h4 class="text-lg font-bold text-gray-900 mb-6">Growth Insight</h4>
                        <div class="space-y-6">
                            <div>
                                <p class="text-sm text-gray-500 mb-2">Revenue: {{ now()->format('F') }}</p>
                                <div class="flex items-end justify-between">
                                    <h3 class="text-2xl font-black text-gray-900">${{ number_format($thisMonthRevenue, 0) }}</h3>
                                    @php
                                        $percent = $lastMonthRevenue > 0 ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 100;
                                    @endphp
                                    <span class="text-xs font-bold @if($percent >= 0) text-green-500 @else text-red-500 @endif">
                                        @if($percent >= 0) + @endif {{ number_format($percent, 1) }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 h-2 rounded-full mt-2 overflow-hidden">
                                    <div class="bg-indigo-600 h-full" style="width: {{ min(100, $percent) }}%"></div>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-100">
                                <h5 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Quick Actions</h5>
                                <div class="grid grid-cols-2 gap-3">
                                    <a href="{{ route('admin.services.index') }}" class="p-3 bg-gray-50 rounded-xl text-center hover:bg-indigo-50 transition group">
                                        <p class="text-xs font-bold text-gray-600 group-hover:text-indigo-600">Services</p>
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="p-3 bg-gray-50 rounded-xl text-center hover:bg-indigo-50 transition group">
                                        <p class="text-xs font-bold text-gray-600 group-hover:text-indigo-600">Clients</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
