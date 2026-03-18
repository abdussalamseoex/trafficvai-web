<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Running Services & Subscriptions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <p class="text-gray-600 text-sm max-w-2xl">Below is a list of your actively running services and their upcoming renewal or expiration dates. Stay on top of your subscriptions to ensure uninterrupted service.</p>
                
                <div class="flex items-center bg-gray-50 p-1 rounded-xl border border-gray-100">
                    <a href="{{ route('client.orders.running') }}" 
                       class="px-4 py-2 rounded-lg text-xs font-bold transition {{ !request('filter') ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">
                        All
                    </a>
                    <a href="{{ route('client.orders.running', ['filter' => 'expiring_soon']) }}" 
                       class="px-4 py-2 rounded-lg text-xs font-bold transition {{ request('filter') === 'expiring_soon' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-400 hover:text-gray-600' }}">
                        Expiring
                    </a>
                    <a href="{{ route('client.orders.running', ['filter' => 'expired']) }}" 
                       class="px-4 py-2 rounded-lg text-xs font-bold transition {{ request('filter') === 'expired' ? 'bg-white shadow-sm text-red-600' : 'text-gray-400 hover:text-gray-600' }}">
                        Expired
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Service Details</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Renewal / Expiry Date</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @if($orders->count() > 0)
                            @foreach($orders as $order)
                                @php
                                    $isExpired = now()->greaterThan($order->expiry_date);
                                    $isExpiringSoon = !$isExpired && now()->diffInDays($order->expiry_date) <= 7;
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('client.orders.show', $order) }}" class="font-bold text-indigo-600 hover:underline">#{{ $order->id }}</a>
                                        <div class="text-xs text-gray-400 mt-0.5">Purchased: {{ $order->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">
                                            @if($order->package)
                                                {{ $order->package->service->title ?? 'Service' }}
                                            @elseif($order->guestPostSite)
                                                Guest Post
                                            @else
                                                Custom Service
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $order->package ? $order->package->name : ($order->guestPostSite ? $order->guestPostSite->url : '') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-md bg-blue-100 text-blue-800">
                                            Active / Running
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm border py-1.5 px-3 rounded-lg font-bold inline-block text-center shadow-sm 
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
                                        <a href="{{ route('client.orders.show', $order) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg font-bold hover:bg-indigo-100 transition shadow-sm border border-indigo-100">
                                            Manage Service
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-16 whitespace-nowrap text-sm text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    You do not have any actively running services with an expiration date.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
