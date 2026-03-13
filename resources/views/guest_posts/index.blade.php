<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buy Guest Posts - {{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @php $favicon = \App\Models\Setting::get('site_favicon'); @endphp
    @if($favicon)
    <link rel="icon" href="{{ asset($favicon) }}?v={{ file_exists(public_path($favicon)) ? filemtime(public_path($favicon)) : '1' }}">
    @endif
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50" x-data="{ 
    showCheckoutModal: false, 
    selectedSite: null, 
    paymentMethod: null,
    isProcessing: false,
    deliveryOption: 'standard',
    expressFee: {{ \App\Models\Setting::get('express_delivery_fee_guest_post', 50) }},
    couponCode: '',
    couponApplied: false,
    discountAmount: 0,
    currentCouponType: null,
    currentCouponValue: 0,
    couponMessage: '',
    couponError: false,
    isChecking: false,

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

    getTotal() {
        if (!this.selectedSite) return 0;
        let total = parseFloat(this.selectedSite.price);
        if (this.deliveryOption === 'express') {
            total += this.expressFee;
        }
        if(this.couponApplied) {
            if(this.currentCouponType === 'percentage') {
                total = total - (total * this.currentCouponValue / 100);
            } else {
                total = total - this.currentCouponValue;
            }
        }
        return Math.max(0, total);
    },

    getDiscountAmount() {
        if (!this.selectedSite || !this.couponApplied) return 0;
        let total = parseFloat(this.selectedSite.price);
        if (this.deliveryOption === 'express') {
            total += this.expressFee;
        }
        if(this.currentCouponType === 'percentage') {
            return (total * this.currentCouponValue / 100);
        }
        return this.currentCouponValue;
    }
}">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <x-frontend-header />

        <!-- Hero Section -->
        <x-page-hero
            badge="Curated Guest Post Marketplace"
            title="Browse &amp; Buy Guest Posts"
            description="Secure high-quality backlinks from real websites with genuine traffic. Browse our curated list of partner domains and instantly order placement."
            cta-label="Explore Inventory"
            cta-scroll="gp-inventory"
        />

        <!-- Main Content -->
        <main id="gp-inventory" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <!-- Compact Active Coupons Banner -->
            @if(isset($activeCoupons) && $activeCoupons->count() > 0)
            <div class="max-w-4xl mx-auto mb-12 space-y-4">
                @foreach($activeCoupons as $coupon)
                <div class="bg-white border border-blue-200 rounded-xl p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between transition hover:shadow-md">
                    <div class="mb-4 sm:mb-0 text-left">
                        <span class="inline-block bg-[#E8470A] text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mb-2">Special Offer</span>
                        <h4 class="text-xl font-bold text-gray-900 mb-1 leading-tight">
                            {{ $coupon->type === 'percentage' ? rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') . '% OFF' : '$' . rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') . ' OFF' }} Promo Code
                        </h4>
                        <p class="text-gray-500 text-sm">Use this exclusive code to get a special discount on your order</p>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto mt-1 sm:mt-0">
                        <div class="border border-blue-300 border-dashed rounded-lg px-5 py-2.5 bg-blue-50/50 hidden sm:block">
                            <span class="text-blue-600 font-bold text-lg select-all tracking-wider">{{ $coupon->code }}</span>
                        </div>
                        <button type="button" @click="couponCode = '{{ $coupon->code }}'; applyCoupon();" 
                                class="w-full sm:w-auto bg-brand hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition shrink-0 flex items-center justify-center">
                            <span x-show="couponCode !== '{{ $coupon->code }}' || !couponApplied" class="whitespace-nowrap">Apply this Promo</span>
                            <span x-show="couponCode === '{{ $coupon->code }}' && couponApplied && !isChecking">Applied! âœ“</span>
                            <svg x-show="couponCode === '{{ $coupon->code }}' && isChecking" class="animate-spin h-5 w-5 ml-2 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Website URL</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Domain Authority (DA)</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Domain Rating (DR)</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Monthly Traffic</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Price placement</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($sites as $site)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-orange-100 rounded-full flex items-center justify-center">
                                            <span class="text-brand font-bold uppercase">{{ substr(str_replace(['http://', 'https://', 'www.'], '', $site->url), 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ str_replace(['http://', 'https://'], '', $site->url) }}</div>
                                            <div class="text-xs text-brand font-medium">{{ $site->niche }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full bg-blue-100 text-blue-800">
                                        {{ $site->da ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-sm font-bold rounded-full bg-purple-100 text-purple-800">
                                        {{ $site->dr ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-700 font-medium">
                                    {{ $site->traffic ? number_format($site->traffic) . '+' : 'N/A' }}
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-lg font-bold text-gray-900">
                                    <span class="price-convert" data-base-price="{{ $site->price }}">${{ number_format($site->price) }}</span>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap text-right text-sm font-medium">
                                    @auth
                                    <button @click="selectedSite = { id: {{ $site->id }}, url: '{{ str_replace(['http://', 'https://'], '', $site->url) }}', price: {{ $site->price }} }; deliveryOption = 'standard'; showCheckoutModal = true;" type="button" class="bg-brand text-white hover:bg-orange-600 px-5 py-2 rounded-lg font-bold transition duration-150 whitespace-nowrap">
                                        Buy Post
                                    </button>
                                    @else
                                    <a href="{{ route('login') }}" class="inline-block bg-gray-100 text-gray-700 hover:bg-gray-200 px-5 py-2 rounded-lg font-bold transition duration-150">
                                        Log in to Buy
                                    </a>
                                    @endauth
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 whitespace-nowrap text-sm text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    No guest post inventory currently available. Check back soon!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-8 text-center text-gray-500 text-sm">
                * Note: All guest post placements include a 1000+ word human-written article unless otherwise specified. Posts will be placed permanently.
            </div>
        </main>
    </div>
    <!-- Checkout Modal -->
    <div x-show="showCheckoutModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showCheckoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showCheckoutModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="showCheckoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <form method="POST" :action="selectedSite ? '{{ url('guest-posts') }}/' + selectedSite.id + '/checkout' : '#'" @submit.prevent="if(!paymentMethod && getTotal() > 0) { $dispatch('notify', {type: 'error', message: 'Please select a payment method'}); return; } isProcessing = true; $el.submit();">
                    @csrf
                    <input type="hidden" name="is_emergency" :value="deliveryOption === 'express' ? '1' : '0'">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl leading-8 font-extrabold text-gray-900" id="modal-title">
                                    Complete Order
                                </h3>
                                
                                <div class="mt-4 bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-500">Website</span>
                                        <span class="text-sm font-bold text-gray-900" x-text="selectedSite?.url"></span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-500">Service</span>
                                        <span class="text-sm font-bold text-gray-900">Guest Post Placement</span>
                                    </div>
                                    <div x-show="couponApplied" x-cloak class="flex justify-between items-center mb-2 text-blue-600">
                                        <span class="text-sm font-medium">Discount Applied</span>
                                        <span class="text-sm font-bold">- $<span x-text="getDiscountAmount().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-200 mt-2">
                                        <span class="text-base font-bold text-gray-900">Total</span>
                                        <span class="text-xl font-black text-brand">$<span x-text="getTotal().toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="relative flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <input type="text" x-model="couponCode" @keydown.enter.prevent="applyCoupon()" placeholder="Coupon code" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl py-2.5 pl-4 pr-12 focus:ring-brand focus:border-brand uppercase text-sm">
                                            <button type="button" @click="applyCoupon()" x-show="!couponApplied" class="absolute right-1 top-1 bottom-1 bg-brand hover:bg-orange-600 text-white font-bold px-3 rounded-lg flex items-center transition" :class="isChecking ? 'opacity-70 cursor-not-allowed' : ''" :disabled="isChecking">
                                                <span x-show="!isChecking" class="text-xs">Apply</span>
                                                <svg x-show="isChecking" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </button>
                                        </div>
                                        <button type="button" x-show="couponApplied" @click="removeCoupon()" x-cloak style="display:none;" class="bg-red-50 text-red-500 hover:bg-red-100 border border-red-200 p-2.5 rounded-xl transition shadow-sm" title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div x-show="couponMessage" x-cloak style="display:none;" class="w-full mt-2 pl-1">
                                        <p :class="couponError ? 'text-red-500' : 'text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded text-[10px]'" class="font-medium" x-html="couponMessage"></p>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Select Delivery Speed</h4>
                                    <div class="grid grid-cols-1 gap-3">
                                        <label class="relative block cursor-pointer rounded-xl border-2 p-3 shadow-sm focus:outline-none transition-all duration-200"
                                            :class="deliveryOption === 'standard' ? 'border-brand ring-1 ring-brand bg-orange-50/30' : 'border-gray-200 hover:border-gray-300 bg-white'">
                                            <input type="radio" value="standard" class="sr-only" x-model="deliveryOption">
                                            <div class="flex items-center justify-between pointer-events-none">
                                                <div class="flex items-center">
                                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="deliveryOption === 'standard' ? 'border-brand' : 'border-gray-400'">
                                                        <div class="w-2.5 h-2.5 bg-brand rounded-full" x-show="deliveryOption === 'standard'"></div>
                                                    </div>
                                                    <div class="ml-3 flex flex-col">
                                                        <span class="text-sm font-bold text-gray-900">Standard Delivery</span>
                                                        <span class="text-xs text-gray-500">Estimated 3-5 days</span>
                                                    </div>
                                                </div>
                                                <span class="text-xs font-bold text-gray-500">Included</span>
                                            </div>
                                        </label>

                                        <label class="relative block cursor-pointer rounded-xl border-2 p-3 shadow-sm focus:outline-none transition-all duration-200"
                                            :class="deliveryOption === 'express' ? 'border-[#E8470A] ring-1 ring-orange-500 bg-orange-50/30' : 'border-gray-200 hover:border-gray-300 bg-white'">
                                            <input type="radio" value="express" class="sr-only" x-model="deliveryOption">
                                            <div class="flex items-center justify-between pointer-events-none">
                                                <div class="flex items-center">
                                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="deliveryOption === 'express' ? 'border-[#E8470A]' : 'border-gray-400'">
                                                        <div class="w-2.5 h-2.5 bg-[#E8470A] rounded-full" x-show="deliveryOption === 'express'"></div>
                                                    </div>
                                                    <div class="ml-3 flex flex-col">
                                                        <span class="text-sm font-bold text-gray-900 flex items-center gap-2">Express Delivery <span class="bg-[#E8470A] text-white text-[8px] px-1.5 py-0.5 rounded uppercase font-black">Fast</span></span>
                                                        <span class="text-xs text-gray-500">Guaranteed 24-48 hours</span>
                                                    </div>
                                                </div>
                                                <span class="text-sm font-black text-[#E8470A]">+$<span x-text="expressFee"></span></span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Select Payment Method</h4>
                                    <div class="space-y-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                        @foreach($gateways as $category => $methods)
                                            @foreach($methods as $slug => $gateway)
                                            <label class="relative block cursor-pointer rounded-xl border-2 p-3 shadow-sm focus:outline-none transition-all duration-200" 
                                                :class="{ 
                                                    'border-brand ring-1 ring-brand bg-orange-50/30': paymentMethod === '{{ $slug }}', 
                                                    'border-gray-200 hover:border-gray-300 bg-white': paymentMethod !== '{{ $slug }}',
                                                    'opacity-50 cursor-not-allowed': '{{ $slug }}' === 'wallet' && {{ auth()->check() ? auth()->user()->balance : 0 }} < (selectedSite ? (deliveryOption === 'express' ? selectedSite.price + expressFee : selectedSite.price) : 0)
                                                }">
                                                <input type="radio" name="payment_method" value="{{ $slug }}" class="sr-only" x-model="paymentMethod" :disabled="'{{ $slug }}' === 'wallet' && {{ auth()->check() ? auth()->user()->balance : 0 }} < (selectedSite ? (deliveryOption === 'express' ? selectedSite.price + expressFee : selectedSite.price) : 0)">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentMethod === '{{ $slug }}' ? 'border-brand' : 'border-gray-400'">
                                                            <div class="w-2.5 h-2.5 bg-brand rounded-full" x-show="paymentMethod === '{{ $slug }}'"></div>
                                                        </div>
                                                        <span class="ml-3 flex flex-col text-sm font-bold text-gray-900">
                                                            {{ $slug === 'wallet' ? 'Account Balance' : $gateway['name'] }}
                                                            @if($slug === 'wallet')
                                                                <span class="text-[10px] uppercase font-bold tracking-wider" :class="{{ auth()->check() ? auth()->user()->balance : 0 }} < (selectedSite ? (deliveryOption === 'express' ? selectedSite.price + expressFee : selectedSite.price) : 0) ? 'text-red-500' : 'text-brand'">
                                                                    <span x-show="{{ auth()->check() ? auth()->user()->balance : 0 }} < (selectedSite ? (deliveryOption === 'express' ? selectedSite.price + expressFee : selectedSite.price) : 0)">Insufficient Funds (${{ auth()->check() ? number_format(auth()->user()->balance, 2) : '0.00' }} available)</span>
                                                                    <span x-show="{{ auth()->check() ? auth()->user()->balance : 0 }} >= (selectedSite ? (deliveryOption === 'express' ? selectedSite.price + expressFee : selectedSite.price) : 0)">${{ auth()->check() ? number_format(auth()->user()->balance, 2) : '0.00' }} available</span>
                                                                </span>
                                                            @elseif(isset($gateway['logo']))
                                                                <div class="h-6 max-h-6 flex items-center mt-1 flex-shrink-0">
                                                                    <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="h-full object-contain opacity-80">
                                                                </div>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="coupon_code" :value="couponApplied ? couponCode : ''">
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-brand text-base font-bold text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm transition flex items-center" :disabled="isProcessing" :class="{ 'opacity-75 cursor-wait': isProcessing }">
                            <span x-show="!isProcessing">Confirm & Pay</span>
                            <span x-show="isProcessing" style="display: none;" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Processing...
                            </span>
                        </button>
                        <button type="button" @click="showCheckoutModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition" :disabled="isProcessing">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-frontend-footer />
    <x-currency-script />
</body>
</html>
