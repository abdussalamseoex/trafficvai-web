<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('client.invoices.index') }}" class="text-xs text-gray-400 hover:text-gray-600 font-medium">← Back to Invoices</a>
                <h2 class="font-bold text-xl text-gray-900 mt-1">Invoice {{ $invoice->invoice_number }}</h2>
            </div>
            <a href="{{ route('client.invoices.download', $invoice) }}" target="_blank"
               class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white font-bold text-sm px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Save as PDF
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('info'))
            <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm">{{ session('info') }}</div>
            @endif

            {{-- Invoice Card --}}
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden print:shadow-none">

                {{-- Header --}}
                <div class="bg-gray-900 px-8 py-10 text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-2xl font-black mb-2">{{ config('app.name') }}</div>
                            <div class="text-gray-400 text-sm mt-4">Issued to:</div>
                            <div class="font-bold text-lg">{{ auth()->user()->name }}</div>
                            <div class="text-gray-400 text-sm">{{ auth()->user()->email }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-gray-400 text-xs uppercase tracking-widest mb-1">Invoice</div>
                            <div class="text-3xl font-black text-brand">{{ $invoice->invoice_number }}</div>
                            @php
                                $colors = ['draft'=>'gray','unpaid'=>'yellow','paid'=>'green','cancelled'=>'red','overdue'=>'red'];
                                $c = $colors[$invoice->status] ?? 'gray';
                            @endphp
                            <span class="mt-3 inline-block bg-{{ $c }}-500 text-white text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest">{{ $invoice->status }}</span>
                            <div class="text-gray-400 text-sm mt-3">
                                Issued: {{ $invoice->created_at->format('M d, Y') }}
                            </div>
                            @if($invoice->due_date)
                            <div class="text-gray-300 text-sm font-bold mt-1">
                                Due: {{ $invoice->due_date->format('M d, Y') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Line Items --}}
                <div class="px-8 py-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left text-xs font-bold text-gray-400 uppercase py-2">Description</th>
                                <th class="text-right text-xs font-bold text-gray-400 uppercase py-2">Qty</th>
                                <th class="text-right text-xs font-bold text-gray-400 uppercase py-2">Unit Price</th>
                                <th class="text-right text-xs font-bold text-gray-400 uppercase py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="py-3 text-gray-800 font-medium">{{ $item->description }}</td>
                                <td class="py-3 text-right text-gray-600">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                                <td class="py-3 text-right text-gray-600">{{ $invoice->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 text-right font-bold text-gray-900">{{ $invoice->currency }} {{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Totals --}}
                    <div class="mt-6 border-t border-gray-100 pt-4 space-y-2 max-w-xs ml-auto text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @if($invoice->discount_value > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount ({{ $invoice->discount_type == 'percentage' ? $invoice->discount_value.'%' : 'Fixed' }})</span>
                            <span>- {{ $invoice->currency }} {{ number_format($invoice->subtotal - ($invoice->total - $invoice->tax_amount), 2) }}</span>
                        </div>
                        @endif
                        @if($invoice->tax_rate > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Tax ({{ $invoice->tax_rate }}%)</span>
                            <span>{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-black text-lg text-gray-900 border-t border-gray-200 pt-2">
                            <span>Total Due</span>
                            <span>{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Notes & Terms --}}
                @if($invoice->notes || $invoice->terms)
                <div class="px-8 pb-8 grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-100 pt-6">
                    @if($invoice->notes)
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes</div>
                        <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                    @if($invoice->terms)
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Terms & Conditions</div>
                        <p class="text-sm text-gray-600">{{ $invoice->terms }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 text-center text-xs text-gray-400">
                    {{ config('app.name') }} &bull; {{ config('app.url') }} &bull; Thank you for your business!
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
