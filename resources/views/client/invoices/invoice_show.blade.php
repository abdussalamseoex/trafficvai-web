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
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden print:shadow-none mb-8">

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
                            <div class="text-gray-400 text-xs uppercase tracking-widest mb-1">{{ ucfirst($invoice->type ?? 'Custom') }} Invoice</div>
                            <div class="text-3xl font-black text-brand">{{ $invoice->invoice_number }}</div>
                            @if($invoice->type === 'renewal' && $invoice->order_id)
                                <a href="{{ route('client.orders.show', $invoice->order_id) }}" class="text-sm text-purple-400 mt-1 font-semibold border border-purple-800/50 bg-purple-900/20 px-3 py-1 rounded-full inline-block hover:bg-purple-900/40 transition">
                                    Linked Order #{{ $invoice->order_id }}
                                </a>
                            @endif
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

            @if($invoice->status === 'unpaid')
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Payment Options
                </h3>

                <form action="{{ route('client.invoices.pay', $invoice) }}" method="POST" id="payment-form" x-data="{ selectedMethod: '' }">
                    @csrf
                    
                    <div class="space-y-6 mb-8">
                        {{-- 1. Internal Wallet --}}
                        <div class="w-full">
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Internal Balance</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <label class="relative flex flex-col p-4 border-2 rounded-2xl cursor-pointer transition group"
                                       :class="selectedMethod === 'wallet' ? 'border-brand bg-brand/5' : 'border-gray-50 hover:border-gray-200'">
                                    <input type="radio" name="payment_method" value="wallet" class="hidden" x-model="selectedMethod">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        </div>
                                        <span class="font-bold text-gray-900">Wallet Balance</span>
                                    </div>
                                    <div class="text-sm font-medium" :class="selectedMethod === 'wallet' ? 'text-brand' : 'text-gray-500'">
                                        Balance: {{ $invoice->currency }} {{ number_format(auth()->user()->balance, 2) }}
                                    </div>
                                    @if(auth()->user()->balance < $invoice->total)
                                        <div class="mt-2 text-[10px] font-black uppercase tracking-widest text-red-500 bg-red-50 px-2 py-0.5 rounded inline-block w-fit">Insufficient Balance</div>
                                    @endif
                                </label>
                            </div>
                        </div>

                        {{-- 2. External Gateways Categories --}}
                        @foreach($gateways as $category => $methods)
                            @if(count($methods) > 0)
                                @php
                                    // Remove wallet from global if it's there
                                    $filteredMethods = array_filter($methods, function($slug) {
                                        return $slug !== 'wallet';
                                    }, ARRAY_FILTER_USE_KEY);
                                @endphp

                                @if(count($filteredMethods) > 0)
                                <div class="w-full">
                                    <div class="flex items-center gap-2 mb-3 ml-1 mt-6">
                                        @if($category === 'global')
                                            <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 012.5 2.5V14a2 2 0 01-2-2h-1a2 2 0 00-2-2 2 2 0 01-2-2V7a2 2 0 00-2-2H8.065M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @elseif($category === 'crypto')
                                            <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @else
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        @endif
                                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                                            {{ $category === 'global' ? 'Global Gateways' : ($category === 'crypto' ? 'Crypto' : ($category === 'bangladesh' ? 'Local' : ucwords($category))) }}
                                        </h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($filteredMethods as $id => $gateway)
                                        <label class="relative cursor-pointer border bg-gray-50 hover:bg-gray-100 rounded-xl px-4 py-4 flex flex-col gap-3 transition overflow-hidden" 
                                               :class="selectedMethod === '{{ $id }}' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-50 shadow-sm' : 'border-gray-50'">
                                            <input type="radio" name="payment_method" value="{{ $id }}" class="sr-only" x-model="selectedMethod">
                                            
                                            <!-- Badge for Automatic/Manual (Invoice View) -->
                                            <div class="absolute top-0 right-0">
                                                @if(isset($gateway['type']) && $gateway['type'] === 'automatic')
                                                    <span class="bg-indigo-100 text-indigo-600 text-[7px] font-bold px-1.5 py-0.5 rounded-bl-lg uppercase tracking-tighter">Instant</span>
                                                @else
                                                    <span class="bg-gray-200 text-gray-500 text-[7px] font-bold px-1.5 py-0.5 rounded-bl-lg uppercase tracking-tighter">Manual</span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors" :class="selectedMethod === '{{ $id }}' ? 'border-indigo-500' : 'border-gray-300'">
                                                    <div class="w-2.5 h-2.5 bg-indigo-500 rounded-full" x-show="selectedMethod === '{{ $id }}'"></div>
                                                </div>
                                                <div class="h-6 max-h-6 flex items-center overflow-hidden">
                                                    @if(isset($gateway['logo']))
                                                        <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="h-full object-contain mix-blend-multiply opacity-90"
                                                              onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($gateway['name']) }}&color=7F9CF5&background=EBF4FF&font-size=0.33';">
                                                    @endif
                                                </div>
                                                <span class="font-bold text-gray-900 text-sm whitespace-nowrap">{{ $gateway['name'] }}</span>
                                            </div>
                                            <div class="text-[9px] text-gray-500 line-clamp-1 leading-relaxed pl-8">{{ $gateway['description'] ?? 'Pay securely via ' . $gateway['name'] }}</div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            @endif
                        @endforeach
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6 border-t border-gray-100 pt-8">
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Final Amount Due</div>
                            <div class="text-3xl font-black text-gray-900">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</div>
                        </div>

                        <div class="w-full sm:w-auto">
                            <button type="submit" 
                                    :disabled="!selectedMethod || (selectedMethod === 'wallet' && {{ auth()->user()->balance }} < {{ $invoice->total }})"
                                    class="w-full sm:w-auto bg-gray-900 hover:bg-black disabled:bg-gray-300 text-white font-black px-12 py-4 rounded-2xl transition shadow-xl shadow-gray-200 flex items-center justify-center gap-2 text-lg">
                                <span x-text="selectedMethod === 'wallet' ? 'Confirm Wallet Payment' : 'Proceed to Checkout'"></span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                            
                            <template x-if="selectedMethod === 'wallet' && {{ auth()->user()->balance }} < {{ $invoice->total }}">
                                <a href="{{ route('client.payments.topup') }}" class="mt-3 block text-center text-sm font-bold text-brand hover:underline">
                                    Low balance. Top-up here →
                                </a>
                            </template>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
