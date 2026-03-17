<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $service->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <a href="{{ $type === 'link-building' ? route('client.link_building.index') : route('client.campaigns.index', $type) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Catalog
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl p-8 mb-12">
                <div class="max-w-4xl">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
                        {{ $service->name }}
                    </h1>
                    <p class="text-xl text-gray-600 leading-relaxed">
                        {{ $service->description }}
                    </p>
                </div>
            </div>

            <!-- Pricing / Packages Section -->
            <div class="mb-12" x-data="{ 
                selectedPackageId: {{ $service->packages->sortBy('price')->values()->first()->id ?? 'null' }},
                selectedPackagePrice: {{ $service->packages->sortBy('price')->values()->first()->price ?? 0 }},
                selectedDelivery: 'standard',
                packages: [
                    @foreach($service->packages as $pkg)
                    { id: {{ $pkg->id }}, emergency_fee: {{ $pkg->emergency_fee ?? 0 }}, turnaround_time_days: {{ $pkg->turnaround_time_days ?? 'null' }}, express_turnaround_time_days: {{ $pkg->express_turnaround_time_days ?? 'null' }} },
                    @endforeach
                ],
                getPackageDeliveryDays(id) {
                    let pkg = this.packages.find(p => p.id === id);
                    return pkg ? pkg.turnaround_time_days : null;
                },
                getPackageExpressDays(id) {
                    let pkg = this.packages.find(p => p.id === id);
                    return pkg ? pkg.express_turnaround_time_days : null;
                },
                getPackageExpressFee(id) {
                    let pkg = this.packages.find(p => p.id === id);
                    return pkg ? parseFloat(pkg.emergency_fee || 0) : 0;
                },
                couponCode: '',
                couponApplied: false,
                discountAmount: 0,
                currentCouponType: null,
                currentCouponValue: 0,
                couponMessage: '',
                couponError: false,
                isChecking: false,
                walletBalance: {{ auth()->user()->balance ?? 0 }},
                isProcessing: false,
                useWallet: false,
                selectedAddons: [],
                addonPrices: {},
                paymentMethod: null,
                
                toggleAddon(id, price) {
                    if (this.selectedAddons.includes(id)) {
                        this.selectedAddons = this.selectedAddons.filter(i => i !== id);
                    } else {
                        this.selectedAddons.push(id);
                        this.addonPrices[id] = price;
                    }
                    this.recalcDiscount();
                },

                applyCoupon() {
                    if(!this.couponCode) return;
                    this.isChecking = true;
                    this.couponMessage = '';
                    fetch('{{ route('services.coupon.check') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ code: this.couponCode, service_id: {{ $service->id }} })
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
                            this.calculateDiscountValue(data.type, data.value);
                        } else {
                            this.couponApplied = false;
                            this.couponError = true;
                            this.couponMessage = data.message;
                            this.discountAmount = 0;
                            this.currentCouponType = null;
                            this.currentCouponValue = 0;
                        }
                    })
                    .catch(() => { this.isChecking = false; this.couponError = true; });
                },

                calculateDiscountValue(type, value) {
                    let subtotal = this.getSubtotal();
                    if(type === 'percentage') {
                        this.discountAmount = (subtotal * parseFloat(value)) / 100;
                    } else {
                        this.discountAmount = Math.min(subtotal, parseFloat(value));
                    }
                },

                recalcDiscount() {
                    if(this.couponApplied) { this.applyCoupon(); }
                },

                removeCoupon() {
                    this.couponCode = '';
                    this.couponApplied = false;
                    this.discountAmount = 0;
                    this.couponMessage = '';
                    this.currentCouponType = null;
                    this.currentCouponValue = 0;
                },

                getSubtotal() {
                    let addonsTotal = this.selectedAddons.reduce((sum, id) => sum + (this.addonPrices[id] || 0), 0);
                    let emergencyFee = 0;
                    if (this.selectedDelivery === 'express') {
                        let pkg = this.packages.find(p => p.id === this.selectedPackageId);
                        if (pkg && pkg.emergency_fee) {
                            emergencyFee = parseFloat(pkg.emergency_fee);
                        }
                    }
                    return parseFloat(this.selectedPackagePrice) + addonsTotal + emergencyFee;
                },
                
                getTotal() {
                    let subtotal = this.getSubtotal();
                    let total = subtotal - this.discountAmount;
                    return Math.max(0, total);
                },

                getWalletDeduction() {
                    if (!this.useWallet) return 0;
                    return Math.min(this.getTotal(), this.walletBalance);
                },

                getFinalTotal() {
                    return Math.max(0, this.getTotal() - this.getWalletDeduction());
                }
            }">
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
                            <button type="button" @click="couponCode = '{{ $coupon->code }}'; applyCoupon(); window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })" 
                                    class="w-full sm:w-auto bg-brand hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-xl transition shrink-0 flex items-center justify-center">
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
                @if($service->addons->count() > 0)
                <div class="max-w-4xl mx-auto bg-gray-900 rounded-[2.5rem] p-10 shadow-2xl overflow-hidden relative border border-gray-800">
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

                    <!-- Payment Options -->
                    <div class="mb-8 border-t border-gray-800 pt-8" x-show="selectedPackageId">
                        <h3 class="text-white font-bold mb-4">Select Payment Method</h3>
                        <div class="flex flex-col gap-6">
                            @foreach($gateways as $category => $methods)
                                @if(count($methods) > 0)
                                <div>
                                    <h4 class="text-xs font-bold text-indigo-400/80 uppercase tracking-widest mb-4">{{ $category === 'global' ? 'Global Gateways' : ($category === 'crypto' ? 'Pay with Crypto' : ($category === 'bangladesh' ? 'Bangladesh Local (BDT)' : ucwords($category))) }}</h4>
                                    <div class="flex flex-col sm:flex-row gap-4 flex-wrap">
                                        @foreach($methods as $slug => $gateway)
                                        <label class="cursor-pointer border border-gray-700 bg-gray-800/50 hover:bg-gray-800 rounded-xl px-4 py-3 flex items-center gap-3 transition flex-1 min-w-[200px]" :class="paymentMethod === '{{ $slug }}' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-500/10' : ''">
                                            <input type="radio" name="payment_method" class="sr-only" value="{{ $slug }}" x-model="paymentMethod">
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentMethod === '{{ $slug }}' ? 'border-indigo-500' : 'border-gray-500'">
                                                <div class="w-2.5 h-2.5 bg-indigo-500 rounded-full" x-show="paymentMethod === '{{ $slug }}'"></div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if(isset($gateway['logo']))
                                                    <div class="h-8 max-h-8 flex items-center">
                                                        <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="h-full object-contain opacity-80">
                                                    </div>
                                                @endif
                                                <div class="flex flex-col">
                                                    <span class="text-white font-medium text-sm">{{ $slug === 'wallet' ? 'Account Balance' : $gateway['name'] }}</span>
                                                    @if($slug === 'wallet')
                                                        <span class="text-[10px] text-indigo-400 font-bold tracking-tighter mt-1"><span class="price-convert" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 2) }}</span> available</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Partial Payment Option -->
                        <div class="mt-4 p-4 bg-indigo-500/10 border border-indigo-500/30 rounded-xl" x-show="paymentMethod !== 'wallet' && walletBalance > 0">
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" class="sr-only" x-model="useWallet">
                                    <div class="w-10 h-6 bg-gray-700 rounded-full shadow-inner transition" :class="useWallet ? 'bg-indigo-600' : ''"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="useWallet ? 'translate-x-4' : ''"></div>
                                </div>
                                <span class="ml-3 text-white text-sm font-medium">Use my <span class="text-indigo-400 font-bold"><span class="price-convert" data-base-price="{{ auth()->user()->balance }}">${{ number_format(auth()->user()->balance, 2) }}</span></span> account balance</span>
                            </label>
                        </div>
                    </div>

                    <!-- Final Checkout Bar -->
                    <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0 relative z-10">
                        <div>
                            <p class="text-gray-400 text-sm font-medium mb-1">Total Project Investment:</p>
                            <div class="flex flex-col">
                                <span class="text-5xl font-black text-white"><span class="price-convert" :data-base-price="getFinalTotal()">$<span x-text="(getFinalTotal() * ($store?.currency === 'USD' ? 1 : ($store?.rates || {BDT:{rate:120},EUR:{rate:0.95}})[$store?.currency || 'USD']?.rate || 1)).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span></span>
                                
                                <div x-show="couponApplied && discountAmount > 0" style="display: none;" class="mt-1">
                                    <span class="text-gray-500 line-through text-xs mr-2">$<span x-text="getSubtotal().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                    <span class="text-blue-500 font-bold text-xs">Save $<span x-text="discountAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                </div>

                                <span x-show="useWallet && getWalletDeduction() > 0" style="display: none;" class="text-indigo-400 font-bold text-xs mt-1">
                                    (-$<span x-text="getWalletDeduction().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span> from account balance)
                                </span>
                            </div>
                        </div>

                        <div class="flex-1 max-w-sm px-4 hidden lg:flex flex-col">
                            <!-- Coupon Input & Remove Button -->
                            <div class="flex items-center gap-2 relative">
                                <div class="relative flex-1">
                                    <input type="text" x-model="couponCode" @keydown.enter.prevent="applyCoupon()" placeholder="Have a coupon?" class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl py-3 pl-4 pr-12 focus:ring-indigo-500 focus:border-indigo-500 uppercase">
                                    <button type="button" @click="applyCoupon()" x-show="!couponApplied" class="absolute right-1 top-1 bottom-1 bg-brand hover:bg-orange-600 text-white font-bold px-4 rounded-lg flex items-center transition" :class="isChecking ? 'opacity-70 cursor-not-allowed' : ''" :disabled="isChecking">
                                        <span x-show="!isChecking">Apply</span>
                                        <svg x-show="isChecking" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </button>
                                </div>
                                <button type="button" x-show="couponApplied" @click="removeCoupon()" style="display:none;" class="bg-red-50 text-red-500 hover:bg-red-100 border border-red-200 px-4 py-3 rounded-xl font-medium text-sm flex items-center justify-center shrink-0 transition" title="Remove Coupon">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Remove
                                </button>
                            </div>
                            <div x-show="couponMessage" style="display:none;" class="w-full mt-2">
                                <p :class="couponError ? 'text-red-400' : 'text-blue-600 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded text-xs'" class="text-xs" x-html="(couponError ? '' : '<svg class=\'w-3 h-3 inline mr-1\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'></path></svg>') + couponMessage"></p>
                            </div>
                        </div>

                        <div class="w-full md:w-auto">
                            <form method="POST" :action="'{{ $type === 'link-building' ? '/client/link-building/' : '/client/campaigns/' . $type . '/' }}' + selectedPackageId + '/checkout'" class="flex flex-col md:flex-row md:items-end gap-4" @submit.prevent="if(!paymentMethod && getTotal() > 0) { $dispatch('notify', {type: 'error', message: 'Please select a payment method'}); return; } isProcessing = true; $el.submit();">
                                @csrf
                                <template x-for="addonId in selectedAddons">
                                    <input type="hidden" name="addons[]" :value="addonId">
                                </template>
                                <input type="hidden" name="payment_method" :value="paymentMethod">
                                <input type="hidden" name="use_wallet" :value="useWallet ? 1 : 0">
                                <input type="hidden" name="coupon_code" :value="couponApplied ? couponCode : ''">
                                
                                <div class="w-full md:w-32 text-left">
                                    <label for="is_emergency_1" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Delivery Option</label>
                                    <select name="is_emergency" id="is_emergency_1" x-model="selectedDelivery" class="block w-full rounded-xl border-gray-700 bg-gray-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="standard" x-text="'Standard' + (getPackageDeliveryDays(selectedPackageId) ? ` (${getPackageDeliveryDays(selectedPackageId)} Days)` : '')"></option>
                                        <option value="express" x-show="getPackageExpressFee(selectedPackageId) > 0 || getPackageExpressDays(selectedPackageId)" x-text="'Express' + (getPackageExpressDays(selectedPackageId) ? ` (${getPackageExpressDays(selectedPackageId)} Days)` : '') + (getPackageExpressFee(selectedPackageId) > 0 ? ` (+$${getPackageExpressFee(selectedPackageId)})` : '')"></option>
                                    </select>
                                </div>

                                <div class="w-full md:w-48 text-left">
                                    <label for="project_id_1" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Assign Project</label>
                                    <select name="project_id" id="project_id_1" class="block w-full rounded-xl border-gray-700 bg-gray-800 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">None</option>
                                        @if(auth()->check())
                                            @foreach(auth()->user()->projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <button type="submit" class="w-full md:w-auto bg-brand hover:bg-orange-600 text-white font-black text-xl px-12 py-5 rounded-2xl transition duration-300 transform hover:scale-105 active:scale-95 shadow-xl shadow-orange-600/20 flex items-center justify-center" :disabled="isProcessing" :class="{ 'opacity-75 cursor-wait': isProcessing }">
                                    <span x-show="!isProcessing">Proceed to Order</span>
                                    <span x-show="isProcessing" style="display: none;" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Processing...
                                    </span>
                                    <svg x-show="!isProcessing" class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <!-- Bottom Bar for simple services without addons -->
                <div class="max-w-xl mx-auto mt-8 bg-white border border-gray-100 p-8 rounded-3xl shadow-xl flex flex-col gap-6">
                    <!-- Payment Options -->
                    <div class="border-b border-gray-100 pb-6 w-full" x-show="selectedPackageId">
                        <h3 class="text-gray-900 font-bold mb-3">Select Payment Method</h3>
                        <div class="flex flex-col gap-6 w-full">
                            @foreach($gateways as $category => $methods)
                                @if(count($methods) > 0)
                                <div class="w-full">
                                    <h4 class="text-[11px] font-bold text-indigo-600/70 uppercase tracking-widest mb-3">{{ $category === 'global' ? 'Global Gateways' : ($category === 'crypto' ? 'Pay with Crypto' : ($category === 'bangladesh' ? 'Bangladesh Local (BDT)' : ucwords($category))) }}</h4>
                                    <div class="flex flex-col sm:flex-row gap-4 flex-wrap">
                                        @foreach($methods as $slug => $gateway)
                                        <label class="cursor-pointer border bg-gray-50 hover:bg-gray-100 rounded-xl px-4 py-3 flex items-center gap-3 transition flex-1 min-w-[150px]" 
                                               :class="paymentMethod === '{{ $slug }}' ? 'ring-2 ring-orange-500 border-orange-500 bg-orange-50' : (('{{ $slug }}' === 'wallet' && {{ auth()->user()->balance }} < getTotal()) ? 'opacity-50 cursor-not-allowed border-gray-200' : 'border-gray-200')"
                                               @click="'{{ $slug }}' === 'wallet' && {{ auth()->user()->balance }} < getTotal() ? $dispatch('notify', {type: 'error', message: 'Insufficient balance. Please top up your account.'}) : paymentMethod = '{{ $slug }}'">
                                            <input type="radio" name="payment_method" class="sr-only" value="{{ $slug }}" x-model="paymentMethod" :disabled="'{{ $slug }}' === 'wallet' && {{ auth()->user()->balance }} < getTotal()">
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentMethod === '{{ $slug }}' ? 'border-orange-500' : 'border-gray-300'">
                                                <div class="w-2.5 h-2.5 bg-orange-500 rounded-full" x-show="paymentMethod === '{{ $slug }}'"></div>
                                            </div>
                                            <span class="text-gray-900 font-bold text-sm flex flex-col">
                                                {{ $slug === 'wallet' ? 'Account Balance' : $gateway['name'] }}
                                                @if($slug === 'wallet')
                                                    <span class="text-[9px] font-bold uppercase tracking-wider" :class="{{ auth()->user()->balance }} < getTotal() ? 'text-red-500' : 'text-gray-500'">
                                                        <span x-show="{{ auth()->user()->balance }} < getTotal()">Insufficient Funds</span>
                                                        <span x-show="{{ auth()->user()->balance }} >= getTotal()">Pay via Account</span>
                                                    </span>
                                                @elseif(isset($gateway['logo']))
                                                    <div class="h-8 max-h-8 flex items-center mt-1">
                                                        <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="h-full object-contain opacity-80">
                                                    </div>
                                                @endif
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        </div>
                    <!-- Checkout Layout -->
                    <div class="flex flex-col w-full gap-6">
                        <!-- Top Row: Price & Coupon -->
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6 w-full">
                            <div class="w-full md:w-auto">
                                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Final Price</p>
                                <div class="flex flex-col">
                                    <span class="text-4xl font-black text-gray-900"><span class="price-convert" :data-base-price="getTotal()">$<span x-text="(getTotal() * ($store?.currency === 'USD' ? 1 : ($store?.rates || {BDT:{rate:120},EUR:{rate:0.95}})[$store?.currency || 'USD']?.rate || 1)).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span></span>
                                    
                                    <div x-show="couponApplied && discountAmount > 0" style="display: none;" class="mt-1 flex items-center gap-2">
                                        <span class="text-gray-400 line-through text-sm mr-2">$<span x-text="getSubtotal().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                        <span class="text-blue-600 font-bold tracking-tight text-sm bg-blue-50 px-2 py-0.5 rounded">Save $<span x-text="discountAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                    </div>

                                    <div x-show="useWallet && getWalletDeduction() > 0" style="display: none;" class="text-[11px] font-bold text-indigo-500 mt-1 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0-2.08.402-2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        -$<span x-text="getWalletDeduction().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span> from account balance
                                    </div>
                                </div>
                            </div>

                            <!-- Coupon Input for Simple View -->
                            <div class="w-full md:w-1/3 flex flex-col mb-4 md:mb-0">
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

                        <!-- Bottom Row: Form -->
                        <div class="flex w-full justify-end border-t border-gray-100 pt-6">
                            <form method="POST" :action="'{{ $type === 'link-building' ? '/client/link-building/' : '/client/campaigns/' . $type . '/' }}' + selectedPackageId + '/checkout'" class="flex flex-col sm:flex-row items-end gap-4 w-full justify-end" @submit.prevent="if(!paymentMethod && getTotal() > 0) { $dispatch('notify', {type: 'error', message: 'Please select a payment method'}); return; } isProcessing = true; $el.submit();">
                                @csrf
                                <input type="hidden" name="payment_method" :value="paymentMethod">
                                <input type="hidden" name="use_wallet" :value="useWallet ? 1 : 0">
                                <input type="hidden" name="coupon_code" :value="couponApplied ? couponCode : ''">
                                
                                <div class="w-full sm:w-40">
                                    <label for="is_emergency_2" class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Delivery Option</label>
                                    <select name="is_emergency" id="is_emergency_2" x-model="selectedDelivery" class="block w-full rounded-xl border-gray-200 bg-gray-50 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm py-3 transition-colors">
                                        <option value="standard" x-text="'Standard' + (getPackageDeliveryDays(selectedPackageId) ? ` (${getPackageDeliveryDays(selectedPackageId)} Days)` : '')"></option>
                                        <option value="express" x-show="getPackageExpressFee(selectedPackageId) > 0 || getPackageExpressDays(selectedPackageId)" x-text="'Express' + (getPackageExpressDays(selectedPackageId) ? ` (${getPackageExpressDays(selectedPackageId)} Days)` : '') + (getPackageExpressFee(selectedPackageId) > 0 ? ` (+ $${getPackageExpressFee(selectedPackageId)})` : '')"></option>
                                    </select>
                                </div>
                                
                                <div class="w-full sm:w-48">
                                    <label for="project_id_2" class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Assign Project</label>
                                    <select name="project_id" id="project_id_2" class="block w-full rounded-xl border-gray-200 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 transition-colors">
                                        <option value="">None</option>
                                        @if(auth()->check())
                                            @foreach(auth()->user()->projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                
                                <button type="submit" class="w-full sm:w-auto bg-brand hover:bg-orange-600 text-white font-bold px-8 py-3 rounded-xl transition duration-300 shadow-md shadow-orange-600/20 active:scale-95 flex items-center justify-center" :disabled="isProcessing" :class="{ 'opacity-75 cursor-wait': isProcessing }">
                                    <span x-show="!isProcessing">Order Now</span>
                                    <span x-show="isProcessing" style="display: none;" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Processing...
                                    </span>
                                    <svg x-show="!isProcessing" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                </div><!-- End of Alpine scope -->
            </div>

        </div>
    </div>
</x-app-layout>
