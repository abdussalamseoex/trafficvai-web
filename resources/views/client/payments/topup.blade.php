<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Balance') }}
            </h2>
            <a href="{{ route('client.payments.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Wallet
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-10 border-b border-gray-50 text-center">
                    <div class="bg-indigo-50 w-20 h-20 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 mb-2">Refill Your Wallet</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">Select an amount and payment method to securely add funds to your TrafficVai account.</p>

                    <div class="mt-6 max-w-md mx-auto">
                        @if(session('success'))
                            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl relative" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl relative" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl relative" role="alert">
                                <ul class="list-disc list-inside text-sm text-left">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-10">
                    <form action="{{ route('client.payments.topup.process') }}" method="POST" x-data="{ amount: 50, paymentMethod: null, isProcessing: false }" class="space-y-10" @submit.prevent="if(!paymentMethod) { $dispatch('notify', {type: 'error', message: 'Please select a payment method'}); return; } isProcessing = true; $el.submit();">
                        @csrf
                        
                        <!-- Fixed Amounts Selection -->
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Choose Amount</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach([10, 50, 100, 500] as $opt)
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="quick_amount" value="{{ $opt }}" class="sr-only" @click="amount = {{ $opt }}">
                                    <div class="py-4 rounded-2xl border-2 text-center transition-all duration-200"
                                         :class="amount == {{ $opt }} ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 shadow-md shadow-indigo-100' : 'border-gray-200 text-gray-500 hover:border-gray-300 bg-white shadow-sm'">
                                        <span class="text-lg font-black"><span class="price-convert" data-base-price="{{ $opt }}">${{ $opt }}</span></span>
                                    </div>
                                </label>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-bold" x-text="$store.currency.rates[$store.currency.current].symbol">$</span>
                                    </div>
                                    <input type="number" name="amount" x-model="amount" min="5" step="0.01" class="block w-full pl-8 pr-12 py-4 border-gray-200 rounded-2xl focus:ring-indigo-600 focus:border-indigo-600 text-xl font-black text-gray-900" placeholder="Custom Amount">
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest" x-text="$store.currency.current">USD</span>
                                    </div>
                                </div>
                                <p class="mt-2 text-[10px] text-gray-400 font-medium">Minimum top-up amount: <span class="price-convert" data-base-price="5">$5.00</span></p>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="divide-y divide-gray-100">
                            @forelse($gateways as $category => $methods)
                                @php
                                    $shouldShow = true;
                                    if ($category === 'crypto' && !$cryptoEnabled) $shouldShow = false;
                                    if ($category === 'bangladesh' && !$bdEnabled) $shouldShow = false;
                                @endphp

                                @if(count($methods) > 0 && $shouldShow)
                                    <div class="py-8 first:pt-0 last:pb-0">
                                        <div class="flex items-center gap-3 mb-6 ml-1">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-gray-50">
                                                @if($category === 'global')
                                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 012.5 2.5V14a2 2 0 01-2-2h-1a2 2 0 00-2-2 2 2 0 01-2-2V7a2 2 0 00-2-2H8.065M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @elseif($category === 'crypto')
                                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @else
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                @endif
                                            </div>
                                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-widest">
                                                {{ $category === 'global' ? 'Global Gateways' : ($category === 'crypto' ? 'Pay with Crypto' : ($category === 'bangladesh' ? 'Bangladesh Local (BDT)' : ucwords($category))) }}
                                            </h4>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                                            @foreach($methods as $slug => $gateway)
                                                @if($slug !== 'wallet') <!-- Cannot top up with a wallet -->
                                                <label class="group relative cursor-pointer border-2 bg-white hover:bg-gray-50 rounded-2xl p-5 flex flex-col items-center text-center transition-all duration-200 outline-none" 
                                                       :class="paymentMethod === '{{ $slug }}' ? 'border-indigo-600 bg-indigo-50/30 shadow-md transform scale-[1.02]' : 'border-gray-100 hover:border-gray-200'"
                                                       @click="paymentMethod = '{{ $slug }}'">
                                                    <input type="radio" name="payment_method" value="{{ $slug }}" class="sr-only" x-model="paymentMethod">
                                                    
                                                    <!-- Integrated Badge -->
                                                    <div class="absolute top-3 right-3">
                                                        <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-tighter shadow-sm"
                                                              :class="'{{ $gateway['type'] ?? 'manual' }}' === 'automatic' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500'">
                                                            {{ ($gateway['type'] ?? 'manual') === 'automatic' ? 'Instant' : 'Manual' }}
                                                        </span>
                                                    </div>

                                                    <!-- Radio Indicator (Top Left) -->
                                                    <div class="absolute top-4 left-4">
                                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors" 
                                                             :class="paymentMethod === '{{ $slug }}' ? 'border-indigo-600' : 'border-gray-300 group-hover:border-gray-400'">
                                                            <div class="w-2 h-2 bg-indigo-600 rounded-full" x-show="paymentMethod === '{{ $slug }}'"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Logo/Visual (Main Focus) -->
                                                    <div class="h-12 flex items-center justify-center mb-4 mt-2">
                                                        @if(isset($gateway['logo']))
                                                            <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="h-full object-contain mix-blend-multiply opacity-90 transition-opacity group-hover:opacity-100"
                                                                  onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($gateway['name']) }}&color=7F9CF5&background=EBF4FF&font-size=0.33';">
                                                        @endif
                                                    </div>

                                                    <div class="flex flex-col">
                                                        <span class="text-gray-900 font-black text-sm">{{ $gateway['name'] }}</span>
                                                        <span class="text-[9px] text-gray-400 mt-1 line-clamp-1">{{ $gateway['description'] ?? 'Pay securely via ' . $gateway['name'] }}</span>
                                                    </div>
                                                </label>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <div class="p-6 text-center text-sm font-bold text-gray-500 bg-gray-50 rounded-2xl border border-gray-100">
                                    No payment methods are currently active.
                                </div>
                            @endforelse
                        </div>

                        @if(session('success'))
                            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl relative" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl relative" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl relative" role="alert">
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-lg font-black py-6 rounded-2xl transition shadow-xl shadow-indigo-600/30 active:scale-95 flex items-center justify-center" :disabled="isProcessing" :class="{ 'opacity-75 cursor-wait': isProcessing }">
                            <span x-show="!isProcessing" class="flex items-center">
                                Add Balance - <span x-text="$store.currency.format(amount)">$50</span>
                                <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </span>
                            <span x-show="isProcessing" style="display: none;" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Processing Top-up...
                            </span>
                        </button>
                    </form>
                </div>
                
                <div class="p-8 bg-gray-50/50 border-t border-gray-50 flex items-center justify-center gap-8 opacity-50 grayscale transition hover:grayscale-0 hover:opacity-100">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg" alt="Stripe" class="h-6">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-8">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
