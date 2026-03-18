<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service / Package</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <span>#{{ $order->id }}</span>
                                            @if($order->unread_messages_count > 0)
                                                <span class="ml-2 bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse" title="New Messages from Admin">
                                                    {{ $order->unread_messages_count }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->package ? $order->package->service->name : 'Guest Post Placement' }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->package ? $order->package->name : $order->guestPostSite->url }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($order->status == 'pending_payment')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Awaiting Payment
                                            </span>
                                        @elseif($order->status == 'pending_requirements')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Action Required
                                            </span>
                                        @elseif($order->status == 'processing')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Processing
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="price-convert" data-base-price="{{ $order->subtotal_display }}">${{ number_format($order->subtotal_display, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($order->status == 'completed')
                                            <span class="text-xs text-gray-500">Delivered</span>
                                        @elseif(in_array($order->status, ['pending_requirements', 'pending_payment']))
                                            <span class="text-xs text-gray-400 italic">Awaiting Action</span>
                                        @elseif($order->expected_delivery_date)
                                            @php
                                                $now = now();
                                                $isOverdue = $now->greaterThan($order->expected_delivery_date);
                                                $daysUntil = $now->diffInDays($order->expected_delivery_date, false);
                                            @endphp
                                            <div class="flex flex-col">
                                                <span class="text-xs font-medium px-2 py-1 rounded {{ $isOverdue ? 'bg-red-50 text-red-700' : ($daysUntil <= 2 ? 'bg-orange-50 text-orange-700' : 'bg-indigo-50 text-indigo-700') }}">
                                                    {{ $order->expected_delivery_date->format('M d, Y') }}
                                                    @if($isOverdue)
                                                        <span class="font-bold ml-1">(Delay)</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-500">Scheduled Soon</span>
                                        @endif

                                        @if($order->is_emergency)
                                            <div class="mt-1">
                                                <span class="text-[10px] font-bold text-red-600 uppercase tracking-widest flex items-center">
                                                    <svg class="w-2.5 h-2.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                    Express
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('client.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        You don't have any orders yet.
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
