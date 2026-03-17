<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Billing & Invoices') }}</h2>
    </x-slot>

    <div class="py-12" x-data="{ tab: '{{ auth()->user()->invoices()->count() > 0 ? 'invoices' : 'orders' }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Tabs --}}
            <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
                <button @click="tab='invoices'" :class="tab==='invoices' ? 'bg-white shadow text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-2 rounded-lg text-sm transition">
                    Custom Invoices
                    @if(auth()->user()->invoices()->count() > 0)
                        <span class="ml-1.5 bg-brand text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ auth()->user()->invoices()->count() }}</span>
                    @endif
                </button>
                <button @click="tab='orders'" :class="tab==='orders' ? 'bg-white shadow text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-2 rounded-lg text-sm transition">Order Receipts</button>
            </div>

            {{-- Custom Invoices Tab --}}
            <div x-show="tab==='invoices'" x-transition>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    @php $customInvoices = auth()->user()->invoices()->with('items')->latest()->get(); @endphp
                    @if($customInvoices->count() > 0)
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Invoice #</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Due Date</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Total</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($customInvoices as $inv)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-indigo-600">{{ $inv->invoice_number }}</div>
                                    <div class="text-xs text-gray-400">{{ $inv->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php $colors=['draft'=>'gray','unpaid'=>'yellow','paid'=>'green','cancelled'=>'red','overdue'=>'red']; $c=$colors[$inv->status]??'gray'; @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $c }}-100 text-{{ $c }}-800">{{ ucfirst($inv->status) }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->due_date ? $inv->due_date->format('M d, Y') : '—' }}</td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">{{ $inv->currency }} {{ number_format($inv->total, 2) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('client.invoices.show', $inv) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="py-16 text-center text-gray-400 text-sm">
                        <svg class="mx-auto h-12 w-12 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        No custom invoices have been issued to you yet.
                    </div>
                    @endif
                </div>
            </div>

            {{-- Order Receipts Tab --}}
            <div x-show="tab==='orders'" x-transition>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Service</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 font-bold text-indigo-600">#{{ $order->order_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($order->package_id) {{ $order->package->service->title ?? 'Package' }} — {{ $order->package->name }}
                                    @elseif($order->guest_post_site_id) Guest Post: {{ $order->guestPostSite->url }}
                                    @else Custom Service @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">${{ number_format($order->total_price, 2) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('client.invoices.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold">View Receipt</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-16 text-center text-gray-400 text-sm">No order receipts found yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

                    
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Your Purchase History</h3>
                            <p class="mt-1 text-sm text-gray-500">View and download receipts for all your past orders.</p>
                        </div>
                    </div>

                    @if(session('info'))
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">{{ session('info') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Order / Invoice ID</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Service</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="relative px-6 py-4"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($orders as $order)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-indigo-600">#{{ $order->order_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($order->package_id)
                                                {{ $order->package->service->title ?? 'Package' }} - {{ $order->package->name }}
                                            @elseif($order->guest_post_site_id)
                                                Guest Post: {{ $order->guestPostSite->url }}
                                            @else
                                                Custom Service
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            <span class="price-convert" data-base-price="{{ $order->total_price }}">${{ number_format($order->total_price, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                                            @elseif($order->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">{{ $order->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('client.invoices.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center group">
                                                View Receipt
                                                <svg class="ml-1 w-4 h-4 text-indigo-400 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            No invoices found. You haven't made any purchases yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
