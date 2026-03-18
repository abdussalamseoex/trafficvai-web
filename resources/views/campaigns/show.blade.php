<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $service->name }} - {{ config('app.name', 'TrafficVai') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @php 
        $favicon = \App\Models\Setting::get('site_favicon');
        $faviconUrl = $favicon ? Storage::disk('public')->url(str_replace('storage/', '', $favicon)) : null;
    @endphp
    @if($faviconUrl)
    <link rel="icon" href="{{ $faviconUrl }}?v={{ file_exists(public_path(str_replace('storage/', '', $favicon))) ? filemtime(public_path(str_replace('storage/', '', $favicon))) : '1' }}">
    @endif
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <x-frontend-header />

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Hero / Service Info -->
            <div class="max-w-4xl mx-auto text-center mb-16">
                <nav class="flex justify-center space-x-2 text-sm text-gray-500 mb-6">
                    <a href="{{ route('campaigns.index', $type) }}" class="hover:text-indigo-600">{{ $title }}</a>
                    <span>/</span>
                    <span class="text-gray-900 font-medium">{{ $service->name }}</span>
                </nav>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight">
                    {{ $service->name }}
                </h1>
                <p class="text-xl text-gray-600 leading-relaxed">
                    {{ $service->description }}
                </p>
            </div>

            <!-- Pricing / Packages Section -->
            <div class="mb-20" x-data="{ 
                selectedPackageId: {{ $service->packages->sortBy('price')->values()->first()->id ?? 'null' }},
                selectedPackagePrice: {{ $service->packages->sortBy('price')->values()->first()->price ?? 0 }},
                selectedAddons: [],
                addonPrices: {},
                deliveryOption: 'standard', // 'standard' or 'express'
                packageData: {
                    @foreach ($service->packages as $package)
                    {{ $package->id }}: {
                        price: {{ $package->price }},
                        turnaround: {{ $package->turnaround_time_days ?? 'null' }},
                        express_turnaround: {{ $package->express_turnaround_time_days ?? 'null' }},
                        emergency_fee: {{ $package->emergency_fee ?? 0 }}
                    },
                    @endforeach
                },
                paymentMethod: null,
                isProcessing: false,
                walletBalance: {{ auth()->user()->balance ?? 0 }},
                useWallet: false,
                couponCode: '',
                isChecking: false,
                couponApplied: false,
                discountAmount: 0,
                currentCouponType: null,
                currentCouponValue: 0,
                couponMessage: '',
                couponError: false,
                
                toggleAddon(id, price) {
                    if (this.selectedAddons.includes(id)) {
                        this.selectedAddons = this.selectedAddons.filter(i => i !== id);
                    } else {
                        this.selectedAddons.push(id);
                        this.addonPrices[id] = price;
                    }
                },

                applyCoupon() {
                    if(!this.couponCode) return;
                    this.isChecking = true;
                    this.couponMessage = '';
                    
                    fetch('{{ route('services.coupon.check') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            code: this.couponCode,
                            service_id: {{ $service->id }}
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isChecking = false;
                        if(data.valid) {
                            this.couponApplied = true;
                            this.couponError = false;
                            this.couponMessage = data.message;
                            this.currentCouponType = data.type;
                            this.currentCouponValue = data.value;
                        } else {
                            this.couponApplied = false;
                            this.couponError = true;
                            this.couponMessage = data.message;
                        }
                    })
                    .catch(err => {
                        this.isChecking = false;
                        this.couponError = true;
                        this.couponMessage = 'Error validating coupon.';
                    });
                },

                removeCoupon() {
                    this.couponApplied = false;
                    this.couponCode = '';
                    this.discountAmount = 0;
                    this.couponMessage = '';
                },
                
                getTotal() {
                    let addonsTotal = this.selectedAddons.reduce((sum, id) => sum + (this.addonPrices[id] || 0), 0);
                    let total = parseFloat(this.selectedPackagePrice) + addonsTotal;

                    if(this.couponApplied) {
                        if(this.currentCouponType === 'percentage') {
                            total = total - (total * this.currentCouponValue / 100);
                        } else {
                            total = total - this.currentCouponValue;
                        }
                    }

                    if (this.useWallet && this.walletBalance > 0) {
                        total = Math.max(0, total - this.walletBalance);
                    }
                    return Math.max(0, total);
                },

                getWalletDeduction() {
                    let addonsTotal = this.selectedAddons.reduce((sum, id) => sum + (this.addonPrices[id] || 0), 0);
                    let total = parseFloat(this.selectedPackagePrice) + addonsTotal;

                    if(this.couponApplied) {
                        if(this.currentCouponType === 'percentage') {
                            total = total - (total * this.currentCouponValue / 100);
                        } else {
                            total = total - this.currentCouponValue;
                        }
                    }

                    return Math.min(total, this.walletBalance);
                }
            }">

                <!-- Compact Active Coupons Banner -->
                @if(isset($activeCoupons) && $activeCoupons->count() > 0)
                <div class="max-w-4xl mx-auto mb-12 space-y-4">
                    @foreach($activeCoupons as $coupon)
                    <div class="bg-white border border-blue-200 rounded-xl p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between transition hover:shadow-md">
                        <div class="mb-4 sm:mb-0 text-left">
                            <span class="inline-block bg-brand text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mb-2">Special Offer</span>
                            <h4 class="text-xl font-bold text-gray-900 mb-1 leading-tight">
                                {{ $coupon->type === 'percentage' ? rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') . '% OFF' : '$' . rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') . ' OFF' }} Promo Code
                            </h4>
                            <p class="text-gray-500 text-sm">Use this exclusive code to get a special discount on your order</p>
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto mt-1 sm:mt-0">
                            <div class="border border-blue-300 border-dashed rounded-lg px-5 py-2.5 bg-blue-50/50 hidden sm:block">
                                <span class="text-blue-600 font-bold text-lg select-all tracking-wider">{{ $coupon->code }}</span>
                            </div>
                            <button type="button" @click="couponCode = '{{ $coupon->code }}'; applyCoupon(); window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })" 
                                    class="w-full sm:w-auto bg-brand hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition shrink-0 flex items-center justify-center">
                                <span x-show="couponCode !== '{{ $coupon->code }}' || !couponApplied" class="whitespace-nowrap">Apply this Promo</span>
                                <span x-show="couponCode === '{{ $coupon->code }}' && couponApplied && !isChecking">Applied! ✓</span>
                                <svg x-show="couponCode === '{{ $coupon->code }}' && isChecking" class="animate-spin h-5 w-5 ml-2 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                    @foreach ($service->packages->sortBy('price') as $package)
                    <div 
                        @click="selectedPackageId = {{ $package->id }}; selectedPackagePrice = {{ $package->price }}"
                        class="cursor-pointer bg-white border-2 rounded-3xl p-8 transition-all duration-300 relative flex flex-col h-full"
                        :class="selectedPackageId === {{ $package->id }} ? 'border-indigo-600 shadow-2xl scale-[1.02] z-10' : 'border-gray-100 hover:border-gray-200 shadow-sm'"
                    >
                        @if($loop->iteration == 2)
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg">
                            Most Popular
                        </div>
                        @endif

                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-2xl font-bold text-gray-900">{{ $package->name }}</h3>
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center" :class="selectedPackageId === {{ $package->id }} ? 'border-indigo-600' : 'border-gray-300'">
                                    <div class="w-2.5 h-2.5 rounded-full bg-indigo-600" x-show="selectedPackageId === {{ $package->id }}"></div>
                                </div>
                            </div>
                            <p class="text-gray-500 text-sm leading-relaxed min-h-[48px]">{{ $package->description }}</p>
                        </div>

                        <div class="mb-8">
                            <span class="text-4xl font-black text-gray-900"><span class="price-convert" data-base-price="{{ $package->price }}">${{ number_format($package->price, 0) }}</span></span>
                            <span class="text-gray-500 text-sm ml-1 font-medium">/ package</span>
                        </div>

                        <ul class="space-y-4 flex-1 mb-8">
                            @if($package->features)
                                @foreach($package->features as $feature)
                                <li class="flex items-start text-gray-700">
                                    <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium">{{ $feature }}</span>
                                </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    @endforeach
                </div>

                <!-- Addons Section -->
                @if($service->addons->count() > 0)
                <div class="max-w-4xl mx-auto bg-gray-900 rounded-[2.5rem] p-10 shadow-2xl overflow-hidden relative border border-gray-800">
                    <!-- Background Decor -->
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-600/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-purple-600/10 rounded-full blur-3xl"></div>

                    <div class="relative z-10 mb-8">
                        <h2 class="text-3xl font-bold text-white mb-2">Enhance Your Package</h2>
                        <p class="text-gray-400">Add these complementary extras to supercharge your SEO results.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
                        @foreach ($service->addons as $addon)
                        <div 
                            @click="toggleAddon({{ $addon->id }}, {{ $addon->price }})"
                            class="group cursor-pointer flex items-center p-5 rounded-2xl border transition-all duration-300"
                            :class="selectedAddons.includes({{ $addon->id }}) ? 'bg-indigo-600/20 border-indigo-500/50' : 'bg-gray-800/40 border-gray-700 hover:border-gray-600'"
                        >
                            <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-colors mr-4" :class="selectedAddons.includes({{ $addon->id }}) ? 'bg-indigo-500 border-indigo-500' : 'border-gray-600'">
                                <svg x-show="selectedAddons.includes({{ $addon->id }})" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-white font-bold">{{ $addon->name }}</h4>
                                <p class="text-gray-400 text-xs">{{ $addon->description }}</p>
                            </div>
                            <div class="text-right ml-4">
                                <span class="text-indigo-400 font-bold">+<span class="price-convert" data-base-price="{{ $addon->price }}">${{ number_format($addon->price, 0) }}</span></span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-8 border-t border-gray-800 pt-8" x-show="selectedPackageId">
                        <h3 class="text-white font-bold mb-4">Select Payment Method</h3>
                        <div class="flex flex-col sm:flex-row gap-4">
                            @php
                                $allEnabled = \App\Services\Payments\PaymentGatewayManager::getEnabledGateways();
                            @endphp
                        <div class="flex flex-col gap-8">
                            @foreach($gateways as $category => $methods)
                                @if(count($methods) > 0)
                                <div>
                                    <div class="flex items-center gap-2 mb-4">
                                        @if($category === 'global')
                                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 012.5 2.5V14a2 2 0 01-2-2h-1a2 2 0 00-2-2 2 2 0 01-2-2V7a2 2 0 00-2-2H8.065M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @elseif($category === 'crypto')
                                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @else
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        @endif
                                        <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-widest">{{ $category === 'global' ? 'Global Gateways' : ($category === 'crypto' ? 'Pay with Crypto' : ($category === 'bangladesh' ? 'Bangladesh Local (BDT)' : ucwords($category))) }}</h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($methods as $slug => $gateway)
                                        <label class="relative cursor-pointer border border-gray-700 bg-gray-800/50 hover:bg-gray-800 rounded-xl px-5 py-4 flex items-center gap-3 transition flex-1 min-w-[200px] overflow-hidden" :class="paymentMethod === '{{ $slug }}' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-500/10' : ''">
                                            <input type="radio" name="payment_method" class="sr-only" value="{{ $slug }}" x-model="paymentMethod">
                                            
                                            <!-- Badge for Automatic/Manual -->
                                            <div class="absolute top-0 right-0">
                                                @if(isset($gateway['type']) && $gateway['type'] === 'automatic')
                                                    <span class="bg-indigo-500/20 text-indigo-400 text-[8px] font-bold px-1.5 py-0.5 rounded-bl-lg uppercase tracking-tighter">Instant</span>
                                                @else
                                                    <span class="bg-gray-700 text-gray-400 text-[8px] font-bold px-1.5 py-0.5 rounded-bl-lg uppercase tracking-tighter">Manual</span>
                                                @endif
                                            </div>

                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentMethod === '{{ $slug }}' ? 'border-indigo-500' : 'border-gray-500'">
                                                <div class="w-2.5 h-2.5 bg-indigo-500 rounded-full" x-show="paymentMethod === '{{ $slug }}'"></div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    @if(isset($gateway['logo']))
                                                        <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="h-6 object-contain opacity-90 mx-auto sm:mx-0">
                                                    @endif
                                                    <span class="text-white font-medium block text-sm">{{ $slug === 'wallet' ? 'Account Balance' : $gateway['name'] }}</span>
                                                </div>
                                                @if($slug === 'wallet' && auth()->check())
                                                    <span class="text-xs text-indigo-400 font-bold tracking-tighter block mt-1">${{ number_format(auth()->user()->balance, 2) }} available</span>
                                                @endif
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        </div>

                        <!-- Partial Payment Option -->
                        <div class="mt-4 p-4 bg-indigo-500/10 border border-indigo-500/30 rounded-xl" x-show="paymentMethod !== 'wallet' && walletBalance > 0">
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" class="sr-only" x-model="useWallet">
                                    <div class="w-10 h-6 bg-gray-700 rounded-full shadow-inner transition" :class="useWallet ? 'bg-indigo-600' : ''"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="useWallet ? 'translate-x-4' : ''"></div>
                                </div>
                                <span class="ml-3 text-white text-sm font-medium">Use my <span class="text-indigo-400 font-bold">@auth ${{ number_format(auth()->user()->balance, 2) }} @else $0.00 @endauth</span> account balance</span>
                            </label>
                        </div>
                    </div>

                    <!-- Final Checkout Bar -->
                    <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0 relative z-10">
                        <div>
                            <p class="text-gray-400 text-sm font-medium mb-1">Total Project Investment:</p>
                            <div class="flex items-baseline">
                                 <span class="text-5xl font-black text-white">$<span x-text="getTotal().toLocaleString()"></span></span>
                                <span x-show="useWallet && getWalletDeduction() > 0" class="ml-3 text-indigo-400 font-bold text-sm">
                                    (-$<span x-text="getWalletDeduction().toLocaleString()"></span> from wallet)
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto mt-4 md:mt-0">
                            <!-- Coupon Input -->
                            <div class="relative w-full md:w-64">
                                <input type="text" x-model="couponCode" @keydown.enter.prevent="applyCoupon()" placeholder="Promo code" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl py-3 pl-4 pr-12 focus:ring-orange-500 focus:border-orange-500 uppercase text-sm">
                                <button type="button" @click="applyCoupon()" x-show="!couponApplied" class="absolute right-1 top-1 bottom-1 bg-brand hover:bg-orange-600 text-white font-bold px-3 rounded-lg flex items-center transition" :class="isChecking ? 'opacity-70 cursor-not-allowed' : ''" :disabled="isChecking">
                                    <span x-show="!isChecking" class="text-xs">Apply</span>
                                    <svg x-show="isChecking" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </button>
                                <button type="button" x-show="couponApplied" x-cloak @click="removeCoupon()" style="display:none;" class="absolute right-1 top-1 bottom-1 text-red-500 hover:bg-red-500/10 px-2 rounded-lg transition" title="Remove Coupon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            @auth
                            <form method="POST" :action="'/campaigns/{{ $type }}/' + selectedPackageId + '/checkout'" @submit.prevent="if(!paymentMethod && getTotal() > 0) { $dispatch('notify', {type: 'error', message: 'Please select a payment method'}); return; } isProcessing = true; $el.submit();">
                                @csrf
                                <template x-for="addonId in selectedAddons">
                                    <input type="hidden" name="addons[]" :value="addonId">
                                </template>
                                <input type="hidden" name="coupon_code" :value="couponCode">
                                <input type="hidden" name="payment_method" :value="paymentMethod">
                                <input type="hidden" name="use_wallet" :value="useWallet ? 1 : 0">
                                <button type="submit" class="w-full md:w-auto bg-brand hover:bg-orange-600 text-white font-black text-xl px-12 py-5 rounded-2xl transition duration-300 transform hover:scale-105 active:scale-95 shadow-xl shadow-orange-600/20 flex items-center justify-center" :disabled="isProcessing" :class="{ 'opacity-75 cursor-wait': isProcessing }">
                                    <span x-show="!isProcessing">Proceed to Order</span>
                                    <span x-show="isProcessing" style="display: none;" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Processing...
                                    </span>
                                    <svg x-show="!isProcessing" class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </form>
                            @else
                            <div class="flex flex-col items-end">
                                <a href="{{ route('login') }}" class="w-full md:w-auto bg-white/10 hover:bg-white/20 text-white font-bold px-10 py-4 rounded-xl transition backdrop-blur-sm border border-white/10 text-center">
                                    Log in to Continue
                                </a>
                                <p class="text-gray-500 text-[10px] mt-2 text-center w-full uppercase tracking-tighter">Already have an account? Log in back</p>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
                @else
                <!-- Bottom Bar for simple services without addons -->
                <div class="max-w-xl mx-auto mt-8 bg-white border border-gray-100 p-8 rounded-3xl shadow-xl flex flex-col gap-6">
                    <!-- Payment Options -->
                    <div class="border-b border-gray-100 pb-6 w-full" x-show="selectedPackageId">
                        <h3 class="text-gray-900 font-bold mb-3">Select Payment Method</h3>
                        <div class="flex flex-col sm:flex-row gap-4">                            @foreach($gateways as $category => $methods)
                                <div class="w-full">
                                    <div class="flex items-center gap-2 mb-3 ml-1 mt-4">
                                        @if($category === 'global')
                                            <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 012.5 2.5V14a2 2 0 01-2-2h-1a2 2 0 00-2-2 2 2 0 01-2-2V7a2 2 0 00-2-2H8.065M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @elseif($category === 'crypto')
                                            <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @else
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        @endif
                                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                                            {{ $category === 'global' ? 'Global Gateways' : ($category === 'crypto' ? 'Pay with Crypto' : ($category === 'bangladesh' ? 'Bangladesh Local (BDT)' : ucwords($category))) }}
                                        </h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($methods as $slug => $gateway)
                                        <label class="relative cursor-pointer border bg-gray-50 hover:bg-gray-100 rounded-xl px-4 py-3 flex items-center gap-3 transition flex-1 min-w-[150px] overflow-hidden" 
                                               :class="paymentMethod === '{{ $slug }}' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-50 shadow-sm' : (('{{ $slug }}' === 'wallet' && @auth {{ auth()->user()->balance }} < getTotal() @else true @endauth) ? 'opacity-50 cursor-not-allowed border-gray-200' : 'border-gray-200')"
                                               @click="'{{ $slug }}' === 'wallet' && @auth {{ auth()->user()->balance }} < getTotal() @else true @endauth ? $dispatch('notify', {type: 'error', message: 'Please log in to use your account balance.'}) : paymentMethod = '{{ $slug }}'">
                                            <input type="radio" name="payment_method" class="sr-only" value="{{ $slug }}" x-model="paymentMethod" :disabled="'{{ $slug }}' === 'wallet' && @auth {{ auth()->user()->balance }} < getTotal() @else true @endauth">
                                            
                                            <!-- Badge for Automatic/Manual (Campaign Simple View) -->
                                            <div class="absolute top-0 right-0">
                                                @if(isset($gateway['type']) && $gateway['type'] === 'automatic')
                                                    <span class="bg-indigo-100 text-indigo-600 text-[7px] font-bold px-1.5 py-0.5 rounded-bl-lg uppercase tracking-tighter">Instant</span>
                                                @else
                                                    <span class="bg-gray-200 text-gray-500 text-[7px] font-bold px-1.5 py-0.5 rounded-bl-lg uppercase tracking-tighter">Manual</span>
                                                @endif
                                            </div>

                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors" :class="paymentMethod === '{{ $slug }}' ? 'border-indigo-500' : 'border-gray-300'">
                                                <div class="w-2.5 h-2.5 bg-indigo-500 rounded-full" x-show="paymentMethod === '{{ $slug }}'"></div>
                                            </div>
                                            <span class="text-gray-900 font-bold text-sm flex flex-col pt-1">
                                                {{ $slug === 'wallet' ? 'Account Balance' : $gateway['name'] }}
                                                @if($slug === 'wallet')
                                                    <span class="text-[9px] font-bold uppercase tracking-wider" :class="@auth {{ auth()->user()->balance }} < getTotal() ? 'text-red-500' : 'text-gray-500' @else 'text-gray-500' @endauth">
                                                        @auth
                                                            <span x-show="{{ auth()->user()->balance }} < getTotal()">Insufficient Funds</span>
                                                            <span x-show="{{ auth()->user()->balance }} >= getTotal()">Pay via Account</span>
                                                        @else
                                                            <span>Log in to Use</span>
                                                        @endauth
                                                    </span>
                                                @elseif(isset($gateway['logo']))
                                                    <div class="h-6 max-h-6 flex items-center mt-1">
                                                        <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="h-full object-contain mix-blend-multiply opacity-80"
                                                              onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($gateway['name']) }}&color=7F9CF5&background=EBF4FF&font-size=0.33';">
                                                    </div>
                                                @endif
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6 w-full">
                        <div class="w-full md:w-auto">
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Final Price</p>
                                <div class="flex flex-col">
                                <span class="text-4xl font-black text-gray-900">$<span x-text="getTotal().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                
                                <div x-show="couponApplied" style="display: none;" class="mt-1 flex items-center gap-2">
                                    <span class="text-gray-400 line-through text-sm mr-2">$<span x-text="(parseFloat(selectedPackagePrice)).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                    <span class="text-blue-600 font-bold tracking-tight text-sm bg-blue-50 px-2 py-0.5 rounded">Discount Applied</span>
                                </div>

                                <div x-show="useWallet && getWalletDeduction() > 0" style="display: none;" class="text-[11px] font-bold text-indigo-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0-2.08.402-2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    -$<span x-text="getWalletDeduction().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span> from account balance
                                </div>
                            </div>
                        </div>

                        <!-- Coupon Input for Simple View -->
                        <div class="w-full md:w-1/3 flex flex-col">
                            <div class="flex items-center gap-2 relative">
                                <div class="relative w-full">
                                    <input type="text" x-model="couponCode" @keydown.enter.prevent="applyCoupon()" placeholder="Coupon?" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl py-3 pl-4 pr-12 focus:ring-indigo-500 focus:border-indigo-500 uppercase text-sm font-medium transition-shadow">
                                    <button type="button" @click="applyCoupon()" x-show="!couponApplied" class="absolute right-1 top-1 bottom-1 bg-brand hover:bg-orange-600 text-white font-bold px-4 rounded-lg flex items-center transition" :class="isChecking ? 'opacity-70 cursor-not-allowed' : ''" :disabled="isChecking">
                                        <span x-show="!isChecking" class="text-xs">Apply</span>
                                        <svg x-show="isChecking" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </button>
                                </div>
                                <button type="button" x-show="couponApplied" @click="removeCoupon()" style="display:none;" class="bg-red-50 text-red-500 hover:bg-red-100 border border-red-200 p-3 rounded-xl transition shadow-sm" title="Remove">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            <div x-show="couponMessage" style="display:none;" class="w-full mt-2 pl-1">
                                <p :class="couponError ? 'text-red-500' : 'text-blue-600'" class="text-xs font-medium" x-html="couponMessage"></p>
                            </div>
                        </div>
                    </div>
                    @auth
                    <form method="POST" :action="'/campaigns/{{ $type }}/' + selectedPackageId + '/checkout'" @submit.prevent="if(!paymentMethod && getTotal() > 0) { $dispatch('notify', {type: 'error', message: 'Please select a payment method'}); return; } isProcessing = true; $el.submit();">
                        @csrf
                        <input type="hidden" name="payment_method" :value="paymentMethod">
                        <input type="hidden" name="coupon_code" :value="couponApplied ? couponCode : ''">
                        <button type="submit" class="bg-brand hover:bg-orange-600 text-white font-bold px-8 py-4 rounded-2xl transition shadow-lg shadow-orange-100 flex items-center justify-center" :disabled="isProcessing" :class="{ 'opacity-75 cursor-wait': isProcessing }">
                            <span x-show="!isProcessing">Order Now</span>
                            <span x-show="isProcessing" style="display: none;" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Processing...
                            </span>
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold px-8 py-4 rounded-2xl transition">
                        Login to Buy
                    </a>
                    @endauth
                </div>
                @endif
            </div>

            <!-- FAQ Section -->
            @if(!empty($service->faqs) && is_array($service->faqs))
            <div class="max-w-4xl mx-auto my-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Frequently Asked Questions</h2>
                    <p class="text-lg text-gray-600">Everything you need to know about our {{ $service->name }} service.</p>
                </div>
                
                <div class="space-y-4" x-data="{ activeFaq: null }">
                    @foreach($service->faqs as $index => $faq)
                    @if(!empty($faq['question']) && !empty($faq['answer']))
                    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden transition-all duration-300" 
                         :class="activeFaq === {{ $index }} ? 'shadow-md border-indigo-100 ring-1 ring-indigo-100' : 'shadow-sm hover:border-gray-200'">
                        <button 
                            @click="activeFaq = activeFaq === {{ $index }} ? null : {{ $index }}" 
                            class="w-full text-left px-8 py-6 focus:outline-none flex justify-between items-center"
                        >
                            <span class="font-bold text-gray-900 pr-8" :class="activeFaq === {{ $index }} ? 'text-indigo-600' : ''">{{ $faq['question'] }}</span>
                            <span class="rounded-full flex items-center justify-center transition-transform duration-300" :class="activeFaq === {{ $index }} ? 'rotate-180 text-indigo-600 bg-indigo-50 w-8 h-8' : 'text-gray-400 w-8 h-8'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </span>
                        </button>
                        <div 
                            x-show="activeFaq === {{ $index }}" 
                            x-collapse 
                            class="px-8 pb-6 text-gray-600 leading-relaxed prose prose-sm max-w-none"
                            style="display: none;"
                        >
                            {!! nl2br(e($faq['answer'])) !!}
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- SEO Process / Trust Section -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 py-12 border-t border-gray-100 mt-12">
                <div class="text-center">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h5 class="font-bold text-gray-900 mb-2">Secure Payments</h5>
                    <p class="text-xs text-gray-500">Industry standard encryption for all transactions.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h5 class="font-bold text-gray-900 mb-2">Fast Execution</h5>
                    <p class="text-xs text-gray-500">Most orders started within 24-48 hours.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2M7 5h2M11 9h2M7 9h2M11 13h2M7 13h2M11 17h2M7 17h2M5 3a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <h5 class="font-bold text-gray-900 mb-2">Detailed Reports</h5>
                    <p class="text-xs text-gray-500">Transparent updates and white-label reporting.</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h5 class="font-bold text-gray-900 mb-2">24/7 Support</h5>
                    <p class="text-xs text-gray-500">Always available to help with your campaigns.</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <x-frontend-footer />
    </div>
    <x-currency-script />
</body>
</html>
