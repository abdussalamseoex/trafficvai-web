<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-seo-tags />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50" x-data="{ 
} ">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <x-frontend-header />

        <!-- Hero Section -->
        <x-page-hero
            :badge="$page->hero_badge ?? 'Curated Guest Post Marketplace'"
            :title="$page->title ?? 'Browse & Buy Guest Posts'"
            :description="$page->hero_description ?? 'Secure high-quality backlinks from real websites with genuine traffic. Browse our curated list of partner domains and instantly order placement.'"
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
                                            <div class="text-[10px] text-brand font-bold mt-1.5 flex flex-wrap gap-1">
                                                @if(is_array($site->niche))
                                                    @foreach(array_slice($site->niche, 0, 3) as $n)
                                                        <span class="inline-block bg-orange-50 rounded px-1.5 py-0.5 border border-orange-100 uppercase tracking-wider">{{ $n }}</span>
                                                    @endforeach
                                                    @if(count($site->niche) > 3)
                                                        <span class="inline-flex items-center text-gray-400 font-normal px-1 shrink-0" title="{{ implode(', ', array_slice($site->niche, 3)) }}">+{{ count($site->niche) - 3 }} more</span>
                                                    @endif
                                                @else
                                                    <span class="inline-block bg-orange-50 rounded px-1.5 py-0.5 border border-orange-100 uppercase tracking-wider">{{ $site->niche }}</span>
                                                @endif
                                            </div>
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
                                    <a href="{{ route('client.guest_posts.show', $site->id) }}" class="inline-block bg-brand text-white hover:bg-orange-600 px-8 py-2.5 rounded-xl font-bold transition duration-150 whitespace-nowrap shadow-sm">
                                        Buy Post
                                    </a>
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
    <x-frontend-footer />
    <x-currency-script />
</body>
</html>
