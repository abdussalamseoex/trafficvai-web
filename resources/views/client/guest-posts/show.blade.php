<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Guest Post Details') }}: <span class="text-brand">{{ $guestPost->url }}</span>
            </h2>
            <a href="{{ route('client.guest_posts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Inventory
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Compact Active Coupons Banner -->
            @if(isset($activeCoupons) && $activeCoupons->count() > 0)
            <div class="max-w-4xl mx-auto mb-12 space-y-4">
                @foreach($activeCoupons as $coupon)
                <div class="bg-white border border-blue-200 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between transition hover:shadow-md">
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
                        <button type="button" x-data @click="$dispatch('apply-promo', '{{ $coupon->code }}'); window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })" 
                                class="w-full sm:w-auto bg-brand hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-xl transition shrink-0 flex items-center justify-center">
                            Apply this Promo
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column: Site Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Metrics Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                Domain Metrics
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-gray-50 rounded-xl p-4 text-center">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Moz DA</div>
                                    <div class="text-2xl font-black text-brand">{{ $guestPost->da ?? 'N/A' }}</div>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 text-center">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Ahrefs DR</div>
                                    <div class="text-2xl font-black text-purple-600">{{ $guestPost->dr ?? 'N/A' }}</div>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 text-center">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Traffic</div>
                                    <div class="text-2xl font-black text-blue-600">{{ $guestPost->traffic ? number_format($guestPost->traffic) . '+' : 'N/A' }}</div>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 text-center">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Spam Score</div>
                                    <div class="text-2xl font-black {{ $guestPost->spam_score > 10 ? 'text-red-500' : 'text-green-500' }}">{{ $guestPost->spam_score ?? 0 }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                General Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Niche / Category</span>
                                    <span class="mt-1 block text-base font-semibold text-gray-900">{{ $guestPost->niche }}</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Language</span>
                                    <span class="mt-1 block text-base font-semibold text-gray-900">{{ $guestPost->language }}</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Link Type</span>
                                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $guestPost->link_type == 'DoFollow' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $guestPost->link_type }}
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Max Links Allowed</span>
                                    <span class="mt-1 block text-base font-semibold text-gray-900">{{ $guestPost->max_links_allowed }}</span>
                                </div>
                                @if($guestPost->delivery_time_days)
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Turnaround Time</span>
                                    <span class="mt-1 flex items-center text-base font-semibold text-gray-900">
                                        <svg class="w-4 h-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        ~{{ $guestPost->delivery_time_days }} Days
                                    </span>
                                </div>
                                @endif
                                @if($guestPost->word_count)
                                <div>
                                    <span class="block text-sm font-medium text-gray-500">Article Word Count</span>
                                    <span class="mt-1 block text-base font-semibold text-gray-900">{{ $guestPost->word_count }}+ Words</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Add Description and Sample Post -->
                        @if($guestPost->description || $guestPost->sample_post_url)
                        <div class="border-t border-gray-100 p-6 bg-gray-50">
                            @if($guestPost->sample_post_url)
                            <div class="mb-6">
                                <h4 class="text-sm font-bold text-gray-900 mb-2 uppercase tracking-wide">Sample Post</h4>
                                <a href="{{ $guestPost->sample_post_url }}" target="_blank" class="inline-flex items-center text-brand hover:text-orange-800 font-medium">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    View Live Example
                                </a>
                            </div>
                            @endif

                            @if($guestPost->description)
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 mb-2 uppercase tracking-wide">Editor Notes / Description</h4>
                                <div class="prose prose-sm text-gray-700 max-w-none">
                                    {!! nl2br(e($guestPost->description)) !!}
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Checkout Options -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 sticky top-6">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-xl font-black text-gray-900">Order Service</h3>
                            <p class="text-sm text-gray-500 mt-1">Select your preferred tier to proceed.</p>
                        </div>
                        
                        <div class="p-6">
                            <form action="{{ route('client.guest_posts.checkout', $guestPost) }}" method="POST" x-data="{ 
                                isProcessing: false,
                                selectedService: 'placement', 
                                selectedDelivery: '0', 
                                paymentMethod: null,
                                couponCode: '',
                                couponApplied: false,
                                discountAmount: 0,
                                currentCouponType: null,
                                currentCouponValue: 0,
                                couponMessage: '',
                                couponError: false,
                                isChecking: false,
                                useWallet: false,
                                walletBalance: {{ auth()->user()->balance ?? 0 }},
                                basePrice: {{ $guestPost->price }},
                                creationPrice: {{ $guestPost->price_creation_placement ?? 0 }},
                                insertionPrice: {{ $guestPost->price_link_insertion ?? 0 }},

                                applyCoupon() {
                                    if(!this.couponCode) return;
                                    this.isChecking = true;
                                    this.couponMessage = '';
                                    fetch('{{ route('services.coupon.check') }}', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                        body: JSON.stringify({ code: this.couponCode, is_global: true })
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
                                    .catch(() => { this.isChecking = false; this.couponError = true; });
                                },

                                removeCoupon() {
                                    this.couponCode = '';
                                    this.couponApplied = false;
                                    this.discountAmount = 0;
                                    this.couponMessage = '';
                                },

                                getSubtotal() {
                                    let total = this.basePrice;
                                    if (this.selectedService === 'creation_placement') total = this.creationPrice;
                                    if (this.selectedService === 'link_insertion') total = this.insertionPrice;
                                    if (this.selectedDelivery === '1') total += {{ $guestPost->express_delivery_price ?? 50 }};
                                    return total;
                                },

                                getTotal() {
                                    let subtotal = this.getSubtotal();
                                    let total = subtotal;
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
                                    if(!this.useWallet) return 0;
                                    let subtotal = this.getSubtotal();
                                    let total = subtotal;
                                    if(this.couponApplied) {
                                        if(this.currentCouponType === 'percentage') {
                                            total = total - (total * this.currentCouponValue / 100);
                                        } else {
                                            total = total - this.currentCouponValue;
                                        }
                                    }
                                    return Math.min(total, this.walletBalance);
                                },

                                getDiscountValue() {
                                    if(!this.couponApplied) return 0;
                                    let subtotal = this.getSubtotal();
                                    if(this.currentCouponType === 'percentage') {
                                        return (subtotal * this.currentCouponValue) / 100;
                                    } else {
                                        return this.currentCouponValue;
                                    }
                                }
                            }" @apply-promo.window="couponCode = $event.detail; applyCoupon();" @submit.prevent="if(!paymentMethod && getTotal() > 0) { $dispatch('notify', {type: 'error', message: 'Please select a payment method'}); return; } isProcessing = true; $el.submit();">
                                @csrf
                                <input type="hidden" name="coupon_code" :value="couponApplied ? couponCode : ''">
                                <div class="space-y-4 mb-6">
                                    <h4 class="text-sm font-bold text-gray-900 mb-2 uppercase tracking-wide">Service Tier</h4>
                                    <!-- Placement Option -->
                                    <label class="relative block cursor-pointer rounded-xl border-2 p-4 shadow-sm focus:outline-none transition-all duration-200" 
                                           :class="{ 'border-brand ring-1 ring-brand bg-orange-50/30': selectedService === 'placement', 'border-gray-200 hover:border-gray-300 bg-white': selectedService !== 'placement' }">
                                        <input type="radio" name="service_tier" value="placement" class="sr-only" x-model="selectedService">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <span class="block text-base font-bold" :class="{ 'text-orange-900': selectedService === 'placement', 'text-gray-900': selectedService !== 'placement' }">Placement Only</span>
                                                <span class="block text-sm text-gray-500 mt-1">You provide the article. We publish it with your link.</span>
                                            </div>
                                            <span class="ml-4 text-xl font-black text-gray-900 mt-0.5"><span class="price-convert" data-base-price="{{ $guestPost->price }}">${{ number_format($guestPost->price) }}</span></span>
                                        </div>
                                    </label>

                                    <!-- Creation & Placement Option -->
                                    @if($guestPost->price_creation_placement)
                                    <label class="relative block cursor-pointer rounded-xl border-2 p-4 shadow-sm focus:outline-none transition-all duration-200" 
                                           :class="{ 'border-brand ring-1 ring-brand bg-orange-50/30': selectedService === 'creation_placement', 'border-gray-200 hover:border-gray-300 bg-white': selectedService !== 'creation_placement' }">
                                        <input type="radio" name="service_tier" value="creation_placement" class="sr-only" x-model="selectedService">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <span class="block text-base font-bold" :class="{ 'text-orange-900': selectedService === 'creation_placement', 'text-gray-900': selectedService !== 'creation_placement' }">Creation & Placement</span>
                                                <span class="block text-sm text-gray-500 mt-1">We write a {{ $guestPost->word_count ?? '500' }}+ word article and publish it.</span>
                                            </div>
                                            <span class="ml-4 text-xl font-black text-gray-900 mt-0.5"><span class="price-convert" data-base-price="{{ $guestPost->price_creation_placement }}">${{ number_format($guestPost->price_creation_placement) }}</span></span>
                                        </div>
                                    </label>
                                    @endif

                                    <!-- Link Insertion Option -->
                                    @if($guestPost->price_link_insertion)
                                    <label class="relative block cursor-pointer rounded-xl border-2 p-4 shadow-sm focus:outline-none transition-all duration-200" 
                                           :class="{ 'border-brand ring-1 ring-brand bg-orange-50/30': selectedService === 'link_insertion', 'border-gray-200 hover:border-gray-300 bg-white': selectedService !== 'link_insertion' }">
                                        <input type="radio" name="service_tier" value="link_insertion" class="sr-only" x-model="selectedService">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <span class="block text-base font-bold" :class="{ 'text-orange-900': selectedService === 'link_insertion', 'text-gray-900': selectedService !== 'link_insertion' }">Link Insertion</span>
                                                <span class="block text-sm text-gray-500 mt-1">We insert your link into an existing aged article.</span>
                                            </div>
                                            <span class="ml-4 text-xl font-black text-gray-900 mt-0.5"><span class="price-convert" data-base-price="{{ $guestPost->price_link_insertion }}">${{ number_format($guestPost->price_link_insertion) }}</span></span>
                                        </div>
                                    </label>
                                    @endif

                                </div>

                                <div class="space-y-4 mb-6">
                                    <h4 class="text-sm font-bold text-gray-900 mb-2 uppercase tracking-wide">Delivery Speed</h4>
                                    <label class="relative block cursor-pointer rounded-xl border-2 p-4 shadow-sm focus:outline-none transition-all duration-200" 
                                           :class="{ 'border-brand ring-1 ring-brand bg-orange-50/30': selectedDelivery == '0', 'border-gray-200 hover:border-gray-300 bg-white': selectedDelivery != '0' }">
                                        <input type="radio" name="is_emergency" value="0" class="sr-only" x-model="selectedDelivery">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <span class="block text-base font-bold" :class="{ 'text-orange-900': selectedDelivery == '0', 'text-gray-900': selectedDelivery != '0' }">Standard Delivery</span>
                                                <span class="block text-sm text-gray-500 mt-1">~{{ $guestPost->delivery_time_days }} Days target delivery.</span>
                                            </div>
                                            <span class="ml-4 text-xl font-bold text-gray-700 mt-0.5">Included</span>
                                        </div>
                                    </label>

                                    <label class="relative block cursor-pointer rounded-xl border-2 p-4 shadow-sm focus:outline-none transition-all duration-200" 
                                           :class="{ 'border-brand ring-1 ring-brand bg-orange-50/30': selectedDelivery == '1', 'border-gray-200 hover:border-gray-300 bg-white': selectedDelivery != '1' }">
                                        <input type="radio" name="is_emergency" value="1" class="sr-only" x-model="selectedDelivery">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <span class="block text-base font-bold" :class="{ 'text-orange-900': selectedDelivery == '1', 'text-gray-900': selectedDelivery != '1' }">Express Delivery</span>
                                                <span class="block text-sm text-gray-500 mt-1">Jump the queue. Target delivery in ~{{ $guestPost->express_delivery_time_days ?? ceil($guestPost->delivery_time_days / 2) }} Days.</span>
                                            </div>
                                            <span class="ml-4 text-xl font-black text-brand mt-0.5">+${{ number_format($guestPost->express_delivery_price ?? 50) }}</span>
                                        </div>
                                    </label>
                                </div>

                                <!-- Coupon Section -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-bold text-gray-900 mb-2 uppercase tracking-wide">Discount Code</h4>
                                    <div class="flex items-center gap-2 relative">
                                        <div class="relative flex-1">
                                            <input type="text" x-model="couponCode" @keydown.enter.prevent="applyCoupon()" placeholder="Coupon?" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl py-2.5 pl-4 pr-12 focus:ring-orange-500 focus:border-orange-500 uppercase text-sm font-medium transition-shadow">
                                            <button type="button" @click="applyCoupon()" x-show="!couponApplied" class="absolute right-1 top-1 bottom-1 bg-brand hover:bg-orange-600 text-white font-bold px-4 rounded-lg flex items-center transition" :class="isChecking ? 'opacity-70 cursor-not-allowed' : ''" :disabled="isChecking">
                                                <span x-show="!isChecking" class="text-xs">Apply</span>
                                                <svg x-show="isChecking" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </button>
                                        </div>
                                        <button type="button" x-show="couponApplied" x-cloak @click="removeCoupon()" style="display:none;" class="bg-red-50 text-red-500 hover:bg-red-100 border border-red-200 p-2.5 rounded-xl transition shadow-sm" title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div x-show="couponMessage" x-cloak style="display:none;" class="w-full mt-2 pl-1">
                                        <p :class="couponError ? 'text-red-500' : 'text-blue-600'" class="text-[10px] font-medium" x-html="couponMessage"></p>
                                    </div>
                                </div>

                                <div class="mt-6 border-t border-gray-100 pt-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Select Payment Method</h4>
                                    </div>
                                    <div class="space-y-6">
                                        <div class="space-y-6">
                                            @php
                                                $allEnabled = \App\Services\Payments\PaymentGatewayManager::getEnabledGateways();
                                            @endphp
                                            @foreach($allEnabled as $category => $methods)
                                                @if(count($methods) > 0)
                                                <div class="w-full">
                                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">
                                                        {{ $category === 'global' ? 'Global Gateways' : ($category === 'crypto' ? 'Pay with Crypto' : ($category === 'bangladesh' ? 'Bangladesh Local (BDT)' : ucwords($category))) }}
                                                    </h4>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                        @foreach($methods as $slug => $gateway)
                                                        <label class="cursor-pointer border bg-gray-50 hover:bg-gray-100 rounded-xl px-4 py-3 flex items-center gap-3 transition flex-1 min-w-[150px]" 
                                                            :class="paymentMethod === '{{ $slug }}' ? 'ring-2 ring-orange-500 border-orange-500 bg-orange-50' : (('{{ $slug }}' === 'wallet' && {{ auth()->user()->balance ?? 0 }} < getTotal()) ? 'opacity-50 cursor-not-allowed border-gray-200' : 'border-gray-200')"
                                                            @click="'{{ $slug }}' === 'wallet' && {{ auth()->user()->balance ?? 0 }} < getTotal() ? $dispatch('notify', {type: 'error', message: 'Insufficient balance. Please top up your account.'}) : paymentMethod = '{{ $slug }}'">
                                                            <input type="radio" name="payment_method" value="{{ $slug }}" class="sr-only" x-model="paymentMethod" :disabled="'{{ $slug }}' === 'wallet' && {{ auth()->user()->balance ?? 0 }} < getTotal()">
                                                            <div class="flex items-center">
                                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentMethod === '{{ $slug }}' ? 'border-orange-500' : 'border-gray-300'">
                                                                    <div class="w-2.5 h-2.5 bg-orange-500 rounded-full" x-show="paymentMethod === '{{ $slug }}'"></div>
                                                                </div>
                                                                <span class="ml-3 flex flex-col text-sm font-bold text-gray-900">
                                                                    {{ $slug === 'wallet' ? 'Account Balance' : $gateway['name'] }}
                                                                    @if($slug === 'wallet')
                                                                        <span class="text-[9px] font-bold uppercase tracking-wider mt-0.5" :class="{{ auth()->user()->balance ?? 0 }} < getTotal() ? 'text-red-500' : 'text-gray-500'">
                                                                            <span x-show="{{ auth()->user()->balance ?? 0 }} < getTotal()">Insufficient Funds</span>
                                                                            <span x-show="{{ auth()->user()->balance ?? 0 }} >= getTotal()">Pay via Account</span>
                                                                        </span>
                                                                    @elseif(isset($gateway['logo']))
                                                                        <div class="h-8 max-h-8 flex items-center mt-1 flex-shrink-0">
                                                                            <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="h-full object-contain opacity-90 mix-blend-multiply" 
                                                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($gateway['name']) }}&color=7F9CF5&background=EBF4FF&font-size=0.33';">
                                                                        </div>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <!-- Partial Payment Option -->
                                        <div class="mt-4 p-4 bg-orange-50 border border-orange-100 rounded-xl" x-show="paymentMethod !== 'wallet' && walletBalance > 0">
                                            <label class="flex items-center cursor-pointer">
                                                <div class="relative">
                                                    <input type="checkbox" name="use_wallet" value="1" class="sr-only" x-model="useWallet">
                                                    <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner transition" :class="useWallet ? 'bg-brand' : ''"></div>
                                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="useWallet ? 'translate-x-4' : ''"></div>
                                                </div>
                                                <span class="ml-3 text-gray-700 text-xs font-medium">Use my <span class="text-brand font-bold"><span class="price-convert" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 2) }}</span></span> account balance</span>
                                            </label>
                                        </div>

                                        <div class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                                            <div class="flex justify-between items-center text-xs font-bold text-gray-500 uppercase tracking-widest">
                                                <span>Subtotal</span>
                                                <span class="text-gray-900">$<span x-text="getSubtotal().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                            </div>
                                            <div x-show="couponApplied" x-cloak class="flex justify-between items-center text-xs font-bold text-blue-600 uppercase tracking-widest">
                                                <span>Coupon Discount</span>
                                                <span>- $<span x-text="getDiscountValue().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                            </div>
                                            <div x-show="useWallet && getWalletDeduction() > 0" x-cloak class="flex justify-between items-center text-xs font-bold text-orange-500 uppercase tracking-widest">
                                                <span>Wallet Paid</span>
                                                <span>- $<span x-text="getWalletDeduction().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                            </div>
                                            <div class="flex items-center justify-between pt-3 border-t border-dashed border-gray-200">
                                                <span class="text-sm font-black text-gray-900 uppercase">Final Total</span>
                                                <div class="text-right">
                                                    <div class="text-2xl font-black text-brand">$<span x-text="getTotal().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-brand hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-150">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Proceed to Checkout
                                    </button>
                                </div>
                                <div class="mt-4 text-center">
                                    <p class="text-xs text-gray-500">Secure payment. You will submit your final requirements on the next page.</p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
