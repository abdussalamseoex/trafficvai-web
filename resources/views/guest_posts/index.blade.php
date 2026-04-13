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
            
            <!-- Filters -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 mt-4">
                <form action="{{ route('guest_posts.index') }}" method="GET">
                    
                    <div class="mb-6 grid grid-cols-1 lg:grid-cols-4 gap-4">
                        <!-- Keyword Search -->
                        <div class="lg:col-span-4">
                            <label for="q" class="block text-xs font-semibold text-orange-600 uppercase tracking-wider mb-2 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Search Website / Keyword
                            </label>
                            <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Enter website URL, niche, or keyword..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2.5 px-4 bg-orange-50/30">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Row 1 -->
                        <!-- Moz DA -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Moz DA</label>
                            <div class="flex gap-2 items-center">
                                <input type="number" name="min_da" placeholder="From" value="{{ request('min_da', 1) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                                <span class="text-gray-400 text-xs">To</span>
                                <input type="number" name="max_da" placeholder="To" value="{{ request('max_da', 100) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                            </div>
                        </div>

                        <!-- Categories -->
                        <div>
                            <label for="category" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Categories</label>
                            <select name="category" id="category" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                                <option value="All" {{ request('category') == 'All' ? 'selected' : '' }}>All Categories</option>
                                @php
                                    $categories = [
                                        'Adult', 'App', 'Art', 'Astrology', 'Automotive', 'Beauty', 'Betting & Gambling', 'Biography', 'Blog', 'Business', 'Casino', 'CBD', 'Crypto', 'Dental', 'Digital Marketing', 'Education', 'Electronics', 'Entertainment', 'Event', 'Family & Parenting', 'Fashion', 'Finance', 'Food', 'Furniture', 'Game', 'Garden', 'General', 'Green Environment & Agriculture', 'Hair loss', 'Health & Fitness', 'Home Improvement', 'Industry & Manufacturing', 'Jewellery', 'Job & Career', 'Law & Legal', 'Lifestyle', 'Logistics', 'Magazine', 'News & Media', 'Pet & Animal', 'Photography', 'Poetry', 'Real Estate', 'Saas', 'Service', 'Shopping', 'Social Media', 'Software', 'Spa & Massage', 'Sport', 'Technology', 'Trading', 'Transportation', 'Travel', 'Visa', 'Web & Technology', 'Wedding'
                                    ];
                                @endphp
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Website Language -->
                        <div class="lg:col-span-2">
                            <label for="language" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Website Language</label>
                            <select name="language" id="language" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3 max-w-xs">
                                <option value="All" {{ request('language') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="English" {{ request('language') == 'English' ? 'selected' : '' }}>English</option>
                                <option value="Spanish" {{ request('language') == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                                <option value="French" {{ request('language') == 'French' ? 'selected' : '' }}>French</option>
                                <option value="German" {{ request('language') == 'German' ? 'selected' : '' }}>German</option>
                            </select>
                        </div>

                        <!-- Row 2 -->
                        <!-- Ahrefs DR -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Ahrefs DR</label>
                            <div class="flex gap-2 items-center">
                                <input type="number" name="min_dr" placeholder="From" value="{{ request('min_dr', 1) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                                <span class="text-gray-400 text-xs">To</span>
                                <input type="number" name="max_dr" placeholder="To" value="{{ request('max_dr', 100) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                            </div>
                        </div>

                        <!-- Link Type -->
                        <div>
                            <label for="link_type" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Link Type</label>
                            <select name="link_type" id="link_type" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                                <option value="All" {{ request('link_type') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="DoFollow" {{ request('link_type') == 'DoFollow' ? 'selected' : '' }}>DoFollow</option>
                                <option value="NoFollow" {{ request('link_type') == 'NoFollow' ? 'selected' : '' }}>NoFollow</option>
                            </select>
                        </div>

                        <!-- Max Links Allow -->
                        <div>
                            <label for="max_links_allowed" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Max Links Allow</label>
                            <input type="number" name="max_links_allowed" id="max_links_allowed" value="{{ request('max_links_allowed', 1) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3" min="1">
                        </div>

                        <!-- Marked As Sponsored -->
                        <div>
                            <label for="is_sponsored" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Marked As Sponsored</label>
                            <select name="is_sponsored" id="is_sponsored" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                                <option value="All" {{ request('is_sponsored') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="Yes" {{ request('is_sponsored') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ request('is_sponsored') == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <!-- Row 3 -->
                        <!-- Price -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Price</label>
                            <div class="flex gap-2 items-center">
                                <input type="number" name="min_price" placeholder="From" value="{{ request('min_price', 1) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                                <span class="text-gray-400 text-xs">To</span>
                                <input type="number" name="max_price" placeholder="To" value="{{ request('max_price', 100000) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                            </div>
                        </div>

                        <!-- Monthly Traffic -->
                        <div>
                            <label for="min_traffic" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2 text-orange-600 flex items-center">
                                Monthly Traffic 
                                <svg class="w-3.5 h-3.5 ml-1 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            </label>
                            <select name="min_traffic" id="min_traffic" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                                <option value="1" {{ request('min_traffic') == '1' ? 'selected' : '' }}>All</option>
                                <option value="1000" {{ request('min_traffic') == '1000' ? 'selected' : '' }}>1,000+</option>
                                <option value="10000" {{ request('min_traffic') == '10000' ? 'selected' : '' }}>10,000+</option>
                                <option value="100000" {{ request('min_traffic') == '100000' ? 'selected' : '' }}>100,000+</option>
                            </select>
                        </div>

                        <!-- Moz Spam Score -->
                        <div>
                            <label for="max_spam_score" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2 text-orange-600 flex items-center">
                                Moz Spam Score 
                                <svg class="w-3.5 h-3.5 ml-1 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            </label>
                            <select name="max_spam_score" id="max_spam_score" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                                <option value="" {{ request('max_spam_score') == '' ? 'selected' : '' }}>All</option>
                                <option value="5" {{ request('max_spam_score') == '5' ? 'selected' : '' }}>&lt;= 5%</option>
                                <option value="10" {{ request('max_spam_score') == '10' ? 'selected' : '' }}>&lt;= 10%</option>
                                <option value="30" {{ request('max_spam_score') == '30' ? 'selected' : '' }}>&lt;= 30%</option>
                            </select>
                        </div>

                        <!-- Service Type -->
                        <div>
                            <label for="service_type" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Service Type</label>
                            <select name="service_type" id="service_type" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3 border border-orange-300 bg-orange-50">
                                <option value="All" {{ request('service_type') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="Guest Post" {{ request('service_type') == 'Guest Post' ? 'selected' : '' }}>Guest Post</option>
                                <option value="Link Insertion" {{ request('service_type') == 'Link Insertion' ? 'selected' : '' }}>Link Insertion</option>
                                <option value="Press Release" {{ request('service_type') == 'Press Release' ? 'selected' : '' }}>Press Release</option>
                            </select>
                        </div>

                        <!-- Ownership Type -->
                        <div>
                            <label for="ownership_type" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Role</label>
                            <select name="ownership_type" id="ownership_type" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-1.5 px-3">
                                <option value="All" {{ request('ownership_type') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="Owner" {{ request('ownership_type') == 'Owner' ? 'selected' : '' }}>Owner</option>
                                <option value="Contributor" {{ request('ownership_type') == 'Contributor' ? 'selected' : '' }}>Contributor</option>
                            </select>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-100 mt-4">
                        <button type="submit" class="bg-brand hover:bg-orange-600 text-white font-bold py-2 px-6 rounded-lg transition duration-150 text-sm">
                            Apply Filters
                        </button>
                        <a href="{{ route('guest_posts.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-150 text-sm">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100" x-data="{ limit: 10 }">
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
                            <tr x-show="{{ $loop->index }} < limit" x-cloak class="hover:bg-gray-50 transition duration-150">
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
                                    @php $domainName = str_replace(['http://', 'https://', 'www.', '/'], '', $site->url); @endphp
                                    <a href="{{ route('client.guest_posts.show', $domainName) }}" class="inline-block bg-brand text-white hover:bg-orange-600 px-8 py-2.5 rounded-xl font-bold transition duration-150 whitespace-nowrap shadow-sm">
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

                <!-- Load More Button -->
                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-center" x-show="limit < {{ count($sites) }}" x-cloak>
                    <button @click="limit += 10" type="button" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-6 rounded-lg shadow-sm transition duration-150 flex items-center">
                        Load More Sites
                        <svg class="ml-2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
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
