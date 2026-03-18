<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Running Orders (Active Services)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                        <p class="text-sm text-gray-600 max-w-2xl">This list displays orders that are currently marked as "Processing" and have a specified Expiration / Renewal date. Use this page to track upcoming renewals.</p>
                        
                        <div class="flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200">
                            <a href="{{ route('admin.orders.running') }}" 
                               class="px-4 py-2 rounded-lg text-sm font-bold transition {{ !request('filter') ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                                All Orders
                            </a>
                            <a href="{{ route('admin.orders.running', ['filter' => 'expiring_soon']) }}" 
                               class="px-4 py-2 rounded-lg text-sm font-bold transition {{ request('filter') === 'expiring_soon' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-500 hover:text-gray-700' }}">
                                Expiring Soon
                            </a>
                            <a href="{{ route('admin.orders.running', ['filter' => 'expired']) }}" 
                               class="px-4 py-2 rounded-lg text-sm font-bold transition {{ request('filter') === 'expired' ? 'bg-white shadow-sm text-red-600' : 'text-gray-500 hover:text-gray-700' }}">
                                Expired
                            </a>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($orders as $order)
                                @php
                                    $isExpired = now()->greaterThan($order->expiry_date);
                                    $isExpiringSoon = !$isExpired && now()->diffInDays($order->expiry_date) <= 7;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="font-bold text-indigo-600 hover:underline">#{{ $order->id }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->package ? $order->package->service->name : 'Custom Service' }}</div>
                                        <div class="text-xs text-gray-500">${{ number_format($order->subtotal_display, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm border py-1 px-2 rounded-md font-medium inline-block text-center shadow-sm 
                                                {{ $isExpired ? 'bg-red-50 text-red-700 border-red-200' : ($isExpiringSoon ? 'bg-orange-50 text-orange-700 border-orange-200' : 'bg-green-50 text-green-700 border-green-200') }}">
                                                {{ $order->expiry_date->format('M d, Y') }}
                                            </span>
                                            @if($isExpired)
                                                <span class="text-[10px] text-red-600 font-bold mt-1 text-center uppercase tracking-widest">Expired</span>
                                            @elseif($isExpiringSoon)
                                                <span class="text-[10px] text-orange-600 font-bold mt-1 text-center uppercase tracking-widest">Expiring Soon</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.invoices.create', ['order_id' => $order->id]) }}" class="inline-flex items-center px-3 py-1 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition shadow-sm mb-1">
                                            Send Renewal
                                        </a><br>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 text-xs">Manage Order</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 whitespace-nowrap text-sm text-center text-gray-500">
                                        No running orders with expiry dates found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
