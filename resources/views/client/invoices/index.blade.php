<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Billing & Invoices') }}</h2>
    </x-slot>

    @php
        $customInvoices = auth()->user()->invoices()->with('items')->latest()->get();
        $defaultTab = $customInvoices->count() > 0 ? 'invoices' : 'orders';
    @endphp

    <div class="py-12" x-data="{ tab: '{{ $defaultTab }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Tabs --}}
            <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
                <button @click="tab='invoices'" :class="tab==='invoices' ? 'bg-white shadow text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-2 rounded-lg text-sm transition">
                    Custom Invoices
                    @if($customInvoices->count() > 0)
                        <span class="ml-1.5 bg-brand text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $customInvoices->count() }}</span>
                    @endif
                </button>
                <button @click="tab='orders'" :class="tab==='orders' ? 'bg-white shadow text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-2 rounded-lg text-sm transition">Order Receipts</button>
            </div>

            {{-- Custom Invoices Tab --}}
            <div x-show="tab==='invoices'" x-transition>
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
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
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $inv->created_at->format('M d, Y') }}</div>
                                    @if($inv->type === 'renewal')
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-800">
                                                Renewal @if($inv->order_id) (Order #{{ $inv->order_id }}) @endif
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $colors = ['draft'=>'gray','unpaid'=>'yellow','paid'=>'green','cancelled'=>'red','overdue'=>'red'];
                                        $c = $colors[$inv->status] ?? 'gray';
                                    @endphp
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
                                <td class="px-6 py-4 text-right font-bold text-gray-900">${{ number_format($order->total_paid_display, 2) }}</td>
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
