<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-seo-tags />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <x-frontend-header />

        <!-- Hero Section -->
        <section class="w-full">
            <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 relative overflow-hidden">
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute -top-32 -left-32 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
                </div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
                    <nav class="flex items-center space-x-2 text-sm text-gray-400 mb-10">
                        <a href="{{ route('services.index') }}" class="hover:text-orange-400 transition-colors">Services</a>
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-gray-300 font-medium">{{ $service->name }}</span>
                    </nav>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div>
                            <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-6">
                                <div class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                                <span class="text-orange-400 text-xs font-bold uppercase tracking-widest">Premium Service</span>
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-white leading-tight mb-6">{{ $service->name }}</h1>
                            @if($service->description)
                            <p class="text-lg text-gray-300 leading-relaxed mb-8">{{ $service->description }}</p>
                            @endif
                            <div class="flex flex-wrap gap-4">
                                <a href="#pricing-section"
                                   onclick="event.preventDefault(); document.getElementById('pricing-section').scrollIntoView({behavior:'smooth',block:'start'});"
                                   class="inline-flex items-center gap-2 bg-[#E8470A] hover:bg-orange-600 text-white font-black px-8 py-4 rounded-2xl transition-all duration-300 shadow-xl shadow-orange-600/30 hover:scale-105 active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Order Now
                                </a>
                                @if($service->sample_link)
                                <a href="{{ $service->sample_link }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold px-8 py-4 rounded-2xl transition-all duration-300 hover:scale-105 active:scale-95 backdrop-blur-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Sample / Download
                                </a>
                                @endif
                            </div>
                            @php
                                $badge1 = \App\Models\Setting::get('service_hero_badge_1', 'Secure Payment');
                                $badge2 = \App\Models\Setting::get('service_hero_badge_2', 'Professional Team');
                                $badge3 = \App\Models\Setting::get('service_hero_badge_3', 'Results Guaranteed');
                            @endphp
                            <div class="flex flex-wrap gap-5 mt-8 pt-8 border-t border-white/10">
                                <span class="flex items-center gap-2 text-gray-400 text-sm"><svg class="w-4 h-4 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ $badge1 }}</span>
                                <span class="flex items-center gap-2 text-gray-400 text-sm"><svg class="w-4 h-4 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ $badge2 }}</span>
                                <span class="flex items-center gap-2 text-gray-400 text-sm"><svg class="w-4 h-4 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ $badge3 }}</span>
                            </div>
                        </div>
                        <div>
                            @php
                                $embedUrl = null;
                                if ($service->hero_video_url) {
                                    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $service->hero_video_url, $m)) {
                                        $embedUrl = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
                                    } elseif (preg_match('/vimeo\.com\/(\d+)/', $service->hero_video_url, $m)) {
                                        $embedUrl = 'https://player.vimeo.com/video/' . $m[1] . '?byline=0&portrait=0';
                                    }
                                }
                                $isDirectVideo = $service->hero_video_url && !$embedUrl;
                            @endphp
                            <div class="rounded-3xl overflow-hidden shadow-2xl shadow-black/50 border border-white/10">
                                @if($embedUrl)
                                    <div class="aspect-video bg-gray-900">
                                        <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy" title="{{ $service->name }}"></iframe>
                                    </div>
                                @elseif($isDirectVideo)
                                    <div class="aspect-video bg-gray-900">
                                        <video class="w-full h-full object-cover" controls preload="metadata" @if($service->hero_image) poster="{{ Storage::disk('public')->url($service->hero_image) }}" @endif>
                                            <source src="{{ $service->hero_video_url }}">
                                        </video>
                                    </div>
                                @elseif($service->hero_image)
                                    <div class="aspect-video bg-gray-800">
                                        <img src="{{ Storage::disk('public')->url($service->hero_image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="aspect-video bg-gradient-to-br from-gray-800 to-gray-900 flex flex-col items-center justify-center gap-4 relative">
                                        <div class="w-20 h-20 rounded-2xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center">
                                            <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-white font-bold text-lg">{{ $service->name }}</p>
                                            <p class="text-gray-500 text-sm mt-1">Premium Quality Service</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Pricing / Packages Section -->
            <div id="pricing-section" class="mb-20 scroll-mt-8" x-data="{
                selectedPackageId: {{ $service->packages->sortBy('price')->values()->first()->id ?? 'null' }},
                selectedPackagePrice: {{ $service->packages->sortBy('price')->values()->first()->price ?? 0 }},
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
                walletBalance: {{ auth()->user()->balance ?? 0 }},
                useWallet: false,
                selectedAddons: [],
                addonPrices: {},
                couponCode: '',
                paymentMethod: null,
                isChecking: false,
                couponApplied: false,
                discountAmount: 0,
                currentCouponType: null,
                currentCouponValue: 0,
                couponMessage: '',
                couponError: false,
                isProcessing: false,
                
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
                    .catch(err => {
                        this.isChecking = false;
                        this.couponError = true;
                        this.couponMessage = 'Error connecting to server.';
                    });
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
                    if(this.couponApplied) {
                        this.applyCoupon();
                    }
                },

                removeCoupon() {
                    this.couponCode = '';
                    this.couponApplied = false;
                    this.discountAmount = 0;
                    this.couponMessage = '';
                    this.currentCouponType = null;
                    this.currentCouponValue = 0;
                },

                getPackageDiscountAmount(price) {
                    if(!this.couponApplied) return 0;
                    if(this.currentCouponType === 'percentage') {
                        return (price * this.currentCouponValue) / 100;
                    } else {
                        return Math.min(price, this.currentCouponValue);
                    }
                },

                getPackageDiscountText(price) {
                    if(!this.couponApplied) return '';
                    let dAmount = this.getPackageDiscountAmount(price);
                    if(dAmount <= 0) return '';
                    if(this.currentCouponType === 'percentage') {
                        return `Save $${dAmount.toFixed(2)} (${this.currentCouponValue}%)`;
                    } else {
                        return `Save $${dAmount.toFixed(2)}`;
                    }
                },
                getSubtotal() {
                    let addonsTotal = this.selectedAddons.reduce((sum, id) => sum + (this.addonPrices[id] || 0), 0);
                    let emergencyFee = 0;
                    if (this.deliveryOption === 'express' && this.selectedPackageId && this.packageData[this.selectedPackageId]) {
                        emergencyFee = parseFloat(this.packageData[this.selectedPackageId].emergency_fee) || 0;
                    }
                    return parseFloat(this.selectedPackagePrice) + addonsTotal + emergencyFee;
                },

                getTotal() {
                    let total = this.getSubtotal() - this.discountAmount;
                    if (this.useWallet && this.walletBalance > 0) {
                        total = Math.max(0, total - this.walletBalance);
                    }
                    return Math.max(0, total);
                },

                getWalletDeduction() {
                    let total = this.getSubtotal() - this.discountAmount;
                    return Math.min(total, this.walletBalance);
                }
            }">

                <!-- Compact Active Coupons Banner (Screenshot matching style) -->
                @if(isset($activeCoupons) && $activeCoupons->count() > 0)
                <div class="max-w-4xl mx-auto mb-12 space-y-4">
                    @foreach($activeCoupons as $coupon)
                    <div class="bg-white border border-blue-200 rounded-xl p-5 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between transition hover:shadow-md">
                        <div class="mb-4 sm:mb-0">
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
                                <span x-show="couponCode === '{{ $coupon->code }}' && couponApplied && !isChecking">Applied! &#10003;</span>
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
                        @click="selectedPackageId = {{ $package->id }}; selectedPackagePrice = {{ $package->price }}; deliveryOption = 'standard';"
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
                            <div class="flex items-baseline mb-2">
                                <span class="text-4xl font-black text-gray-900 leading-none" x-text="$store.currency ? $store.currency.format(couponApplied ? ({{ $package->price }} - getPackageDiscountAmount({{ $package->price }})) : {{ $package->price }}) : '$' + (couponApplied ? ({{ $package->price }} - getPackageDiscountAmount({{ $package->price }})) : {{ $package->price }})"></span>
                                <span x-show="couponApplied" style="display:none;" class="ml-2 text-xl font-medium text-gray-400 line-through"><span class="price-convert" data-base-price="{{ $package->price }}">${{ number_format($package->price, 0) }}</span></span>
                            </div>
                            <span class="text-gray-500 text-sm font-medium">/ package</span>
                            
                            <div x-show="couponApplied" style="display:none;" class="mt-2 text-left">
                                <span class="inline-block bg-blue-50 border border-blue-100 text-blue-600 font-bold text-xs px-3 py-1 rounded-md" x-text="getPackageDiscountText({{ $package->price }})"></span>
                            </div>
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
                        <div class="flex flex-col gap-6">
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
                                        <h4 class="text-xs font-bold text-indigo-500 uppercase tracking-widest">{{ $category === 'global' ? 'Global Gateways' : ($category === 'crypto' ? 'Pay with Crypto' : ($category === 'bangladesh' ? 'Bangladesh Local (BDT)' : ucwords($category))) }}</h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($methods as $slug => $gateway)
                                        <label class="relative cursor-pointer border border-gray-700 bg-gray-800/50 hover:bg-gray-800 rounded-xl px-4 py-4 flex items-center gap-4 transition shadow-sm overflow-hidden" 
                                               :class="paymentMethod === '{{ $slug }}' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-500/10' : ''">
                                            <input type="radio" name="payment_method" class="sr-only" value="{{ $slug }}" x-model="paymentMethod">
                                            
                                            <!-- Badge for Automatic/Manual -->
                                            <div class="absolute top-0 right-0">
                                                @if(isset($gateway['type']) && $gateway['type'] === 'automatic')
                                                    <span class="bg-indigo-500/20 text-indigo-400 text-[8px] font-bold px-2 py-0.5 rounded-bl-lg uppercase tracking-tighter">Instant</span>
                                                @else
                                                    <span class="bg-gray-700 text-gray-400 text-[8px] font-bold px-2 py-0.5 rounded-bl-lg uppercase tracking-tighter">Manual</span>
                                                @endif
                                            </div>

                                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentMethod === '{{ $slug }}' ? 'border-indigo-500' : 'border-gray-500'">
                                                <div class="w-3 h-3 bg-indigo-500 rounded-full" x-show="paymentMethod === '{{ $slug }}'"></div>
                                            </div>
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-3">
                                                    @if(isset($gateway['logo']))
                                                        <div class="h-8 max-h-8 flex items-center">
                                                            <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="h-full object-contain opacity-90">
                                                        </div>
                                                    @endif
                                                    <span class="text-white font-bold text-sm">{{ $slug === 'wallet' ? 'Account Balance' : $gateway['name'] }}</span>
                                                </div>
                                                @if($slug === 'wallet' && auth()->check())
                                                    <span class="text-xs text-indigo-400 font-bold tracking-tighter mt-1">${{ number_format(auth()->user()->balance, 2) }} available</span>
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
                            
                            <!-- Coupon Display -->
                            <div x-show="couponApplied && discountAmount > 0" style="display: none;" class="mb-2">
                                <span class="text-gray-400 line-through mr-2" x-text="$store.currency ? $store.currency.format(getSubtotal()) : '$' + getSubtotal().toFixed(2)"></span>
                                <span class="text-blue-500 font-bold text-sm">Save <span x-text="$store.currency ? $store.currency.format(discountAmount) : '$' + discountAmount.toFixed(2)"></span></span>
                            </div>

                            <div class="flex items-baseline">
                                <span class="text-5xl font-black text-white" x-text="$store.currency ? $store.currency.format(getTotal()) : '$' + getTotal().toFixed(2)"></span>
                                <span x-show="useWallet && getWalletDeduction() > 0" class="ml-3 text-indigo-400 font-bold text-sm">
                                    (-<span x-text="$store.currency ? $store.currency.format(getWalletDeduction()) : '$' + getWalletDeduction().toFixed(2)"></span> from wallet)
                                </span>
                            </div>
                        </div>

                        <div class="flex-1 max-w-md px-4 hidden md:flex flex-col">
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
                            @auth
                            <form method="POST" :action="'/services/' + selectedPackageId + '/checkout'" @submit.prevent="if(!paymentMethod && getTotal() > 0) { $dispatch('notify', {type: 'error', message: 'Please select a payment method'}); return; } isProcessing = true; $el.submit();">
                                @csrf
                                <template x-for="addonId in selectedAddons">
                                    <input type="hidden" name="addons[]" :value="addonId">
                                </template>
                                <input type="hidden" name="payment_method" :value="paymentMethod">
                                <input type="hidden" name="use_wallet" :value="useWallet ? 1 : 0">
                                <input type="hidden" name="coupon_code" :value="couponApplied ? couponCode : ''">
                                <button type="submit" class="w-full md:w-auto bg-brand hover:bg-orange-600 text-white font-black text-xl px-12 py-5 rounded-2xl transition duration-300 transform hover:scale-105 active:scale-95 shadow-xl shadow-orange-600/20 flex items-center justify-center">
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
                <div class="max-w-3xl mx-auto mt-8 bg-white border border-gray-100 p-8 rounded-3xl shadow-xl flex flex-col gap-6">
                    
                    <!-- Delivery Option Section for Simple Packages -->
                    <div class="border-b border-gray-100 pb-6 w-full" x-show="selectedPackageId && packageData[selectedPackageId]?.express_turnaround">
                        <h3 class="text-gray-900 font-bold mb-4">Select Delivery Speed</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Standard -->
                            <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 transition"
                                :class="deliveryOption === 'standard' ? 'border-indigo-500 ring-2 ring-indigo-50 bg-indigo-50/30' : 'border-gray-200 hover:border-gray-300 bg-white'">
                                <input type="radio" value="standard" x-model="deliveryOption" class="sr-only">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5" :class="deliveryOption === 'standard' ? 'border-indigo-500' : 'border-gray-300'">
                                    <div class="w-2.5 h-2.5 bg-indigo-500 rounded-full" x-show="deliveryOption === 'standard'"></div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-gray-900 leading-none mb-1.5">Standard Delivery</h4>
                                    <p class="text-xs text-gray-500">
                                        Delivery in <span class="text-indigo-600 font-bold" x-text="packageData[selectedPackageId]?.turnaround || '...'"></span> days
                                    </p>
                                </div>
                                <span class="text-xs font-semibold text-gray-500">Included</span>
                            </label>

                            <!-- Express -->
                            <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 transition"
                                :class="deliveryOption === 'express' ? 'border-[#E8470A] ring-2 ring-orange-50 bg-orange-50/30' : 'border-gray-200 hover:border-gray-300 bg-white'">
                                <input type="radio" value="express" x-model="deliveryOption" class="sr-only">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5" :class="deliveryOption === 'express' ? 'border-[#E8470A]' : 'border-gray-300'">
                                    <div class="w-2.5 h-2.5 bg-[#E8470A] rounded-full" x-show="deliveryOption === 'express'"></div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1.5 leading-none">
                                        <h4 class="text-sm font-bold text-gray-900">Express Delivery</h4>
                                        <span class="bg-[#E8470A] text-white text-[8px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">Fast</span>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Delivery in <span class="text-[#E8470A] font-bold" x-text="packageData[selectedPackageId]?.express_turnaround"></span> days
                                    </p>
                                </div>
                                <span class="text-xs font-bold text-[#E8470A]">$<span x-text="packageData[selectedPackageId]?.emergency_fee"></span></span>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Options -->
                    <div class="border-b border-gray-100 pb-6 w-full" x-show="selectedPackageId">
                        <h3 class="text-gray-900 font-bold mb-3">Select Payment Method</h3>
                        <div class="divide-y divide-gray-100">
                            @php
                                $allEnabled = \App\Services\Payments\PaymentGatewayManager::getEnabledGateways();
                            @endphp
                            @foreach($gateways as $category => $methods)
                                @if(count($methods) > 0)
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
                                        <label class="group relative cursor-pointer border-2 bg-white hover:bg-gray-50 rounded-2xl p-5 flex flex-col items-center text-center transition-all duration-200 outline-none" 
                                               :class="paymentMethod === '{{ $slug }}' ? 'border-indigo-600 bg-indigo-50/30 shadow-md transform scale-[1.02]' : (('{{ $slug }}' === 'wallet' && @auth {{ auth()->user()->balance }} < getTotal() @else true @endauth) ? 'opacity-50 cursor-not-allowed border-gray-100' : 'border-gray-100 hover:border-gray-200')"
                                               @click="'{{ $slug }}' === 'wallet' && @auth {{ auth()->user()->balance }} < getTotal() @else true @endauth ? $dispatch('notify', {type: 'error', message: 'Please log in to use your account balance.'}) : paymentMethod = '{{ $slug }}'">
                                            <input type="radio" name="payment_method" class="sr-only" value="{{ $slug }}" x-model="paymentMethod" :disabled="'{{ $slug }}' === 'wallet' && @auth {{ auth()->user()->balance }} < getTotal() @else true @endauth">
                                            
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
                                                @if($slug === 'wallet')
                                                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                                    </div>
                                                @elseif(isset($gateway['logo']))
                                                    <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="h-full object-contain mix-blend-multiply opacity-90 transition-opacity group-hover:opacity-100"
                                                          onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($gateway['name']) }}&color=7F9CF5&background=EBF4FF&font-size=0.33';">
                                                @endif
                                            </div>

                                            <div class="flex flex-col">
                                                <span class="text-gray-900 font-black text-sm">{{ $slug === 'wallet' ? 'Account Balance' : $gateway['name'] }}</span>
                                                @auth
                                                    @if($slug === 'wallet')
                                                        <span class="text-[10px] font-bold mt-1" :class="{{ auth()->user()->balance }} < getTotal() ? 'text-red-500' : 'text-gray-500'">
                                                            <span x-show="{{ auth()->user()->balance }} < getTotal()">Insufficient Funds</span>
                                                            <span x-show="{{ auth()->user()->balance }} >= getTotal()">Pay via Account</span>
                                                        </span>
                                                    @else
                                                        <span class="text-[9px] text-gray-400 mt-1 line-clamp-1">{{ $gateway['description'] ?? 'Pay securely via ' . $gateway['name'] }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-[9px] text-gray-400 mt-1 line-clamp-1">Login to see balance</span>
                                                @endauth
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row items-center justify-between gap-6 w-full">
                        <div class="w-full md:w-auto">
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Final Price</p>
                        
                        <div x-show="couponApplied && discountAmount > 0" style="display: none;" class="mb-1">
                            <span class="text-gray-400 line-through mr-2" x-text="$store.currency ? $store.currency.format(getSubtotal()) : '$' + getSubtotal().toFixed(2)"></span>
                            <span class="text-blue-600 font-bold tracking-tight text-sm bg-blue-50 px-2 py-0.5 rounded">Save <span x-text="$store.currency ? $store.currency.format(discountAmount) : '$' + discountAmount.toFixed(2)"></span></span>
                        </div>

                        <span class="text-4xl font-black text-gray-900" x-text="$store.currency ? $store.currency.format(getTotal()) : '$' + getTotal().toFixed(2)"></span>
                    </div>

                    <div class="w-full md:w-auto mt-4 md:mt-0">
                        <div class="relative flex items-center gap-2">
                            <div class="relative flex-1">
                                <input type="text" x-model="couponCode" @keydown.enter.prevent="applyCoupon()" placeholder="Coupon code" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl py-3 pl-4 pr-12 focus:ring-indigo-500 focus:border-indigo-500 uppercase">
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
                            <p :class="couponError ? 'text-red-500' : 'text-blue-600 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded text-xs'" class="text-xs" x-html="(couponError ? '' : '<svg class=\'w-3 h-3 inline mr-1\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'></path></svg>') + couponMessage"></p>
                        </div>
                    </div>

                    @auth
                    <form method="POST" :action="'/services/' + selectedPackageId + '/checkout'" class="w-full md:w-auto" @submit.prevent="if(!paymentMethod && getTotal() > 0) { $dispatch('notify', {type: 'error', message: 'Please select a payment method'}); return; } isProcessing = true; $el.submit();">
                        @csrf
                        <input type="hidden" name="coupon_code" :value="couponApplied ? couponCode : ''">
                        <input type="hidden" name="payment_method" :value="paymentMethod">
                        <button type="submit" class="w-full md:w-auto bg-brand hover:bg-orange-600 text-white font-bold px-8 py-4 rounded-2xl transition shadow-lg shadow-orange-100 flex items-center justify-center" :disabled="isProcessing" :class="{ 'opacity-75 cursor-wait': isProcessing }">
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
