<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <a href="{{ route('admin.invoices.index') }}" class="text-xs text-gray-400 hover:text-gray-600 font-medium">← All Invoices</a>
                <h2 class="font-bold text-xl text-gray-900 mt-1">Invoice {{ $invoice->invoice_number }}</h2>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Status Changer --}}
                <form method="POST" action="{{ route('admin.invoices.update-status', $invoice) }}" class="flex gap-2">
                    @csrf
                    <select name="status" onchange="this.form.submit()" class="text-sm rounded-xl border-gray-200 focus:ring-brand focus:border-brand shadow-sm">
                        @foreach(['draft','unpaid','paid','cancelled','overdue'] as $s)
                            <option value="{{ $s }}" @selected($invoice->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('admin.invoices.edit', $invoice) }}" class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm px-4 py-2 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <a href="{{ route('admin.invoices.pdf', $invoice) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white font-bold text-sm px-4 py-2 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download PDF
                </a>
                <form method="POST" action="{{ route('admin.invoices.send-email', $invoice) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Email to Client
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete this invoice?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-sm px-4 py-2 rounded-xl transition border border-red-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
            @endif

            {{-- Invoice Card --}}
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden">

                {{-- Header --}}
                <div class="bg-gray-900 px-8 py-10 text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            @php $logo = \App\Models\Setting::get('site_logo'); @endphp
                            @if($logo)
                                <img src="{{ Storage::disk('public')->url(str_replace('storage/', '', $logo)) }}" alt="Logo" class="h-12 mb-4 brightness-200">
                            @else
                                <div class="text-2xl font-black mb-4">{{ config('app.name') }}</div>
                            @endif
                            <div class="text-gray-400 text-sm mt-4">Issued to:</div>
                            <div class="font-bold text-lg">{{ $invoice->user->name }}</div>
                            <div class="text-gray-400 text-sm">{{ $invoice->user->email }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-gray-400 text-xs uppercase tracking-widest mb-1">{{ ucfirst($invoice->type ?? 'Custom') }} Invoice</div>
                            <div class="text-3xl font-black text-brand">{{ $invoice->invoice_number }}</div>
                            @if($invoice->type === 'renewal' && $invoice->order_id)
                                <div class="text-sm text-purple-400 mt-1 font-semibold border border-purple-800/50 bg-purple-900/20 px-3 py-1 rounded-full inline-block">Order #{{ $invoice->order_id }}</div>
                            @endif
                            @php
                                $colors = ['draft'=>'gray','unpaid'=>'yellow','paid'=>'green','cancelled'=>'red','overdue'=>'red'];
                                $c = $colors[$invoice->status] ?? 'gray';
                            @endphp
                            <span class="mt-3 inline-block bg-{{ $c }}-500 text-white text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest">{{ $invoice->status }}</span>
                            <div class="text-gray-400 text-sm mt-3">Due: {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</div>
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
                            <span>- {{ $invoice->currency }} {{ number_format($invoice->subtotal - ($invoice->subtotal - $invoice->discount_value), 2) }}</span>
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
                <div class="px-8 pb-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
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
            </div>
        </div>
    </div>
</x-app-layout>
