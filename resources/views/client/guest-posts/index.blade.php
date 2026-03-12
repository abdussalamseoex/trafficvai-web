<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Guest Post Inventory') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-xl text-gray-500">
                    Secure high-quality backlinks from real websites with genuine traffic. Browse our curated list of partner domains and instantly order placement.
                </p>
            </div>

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
                        <button type="button" @click="$dispatch('apply-global-coupon', { code: '{{ $coupon->code }}' })" 
                                class="w-full sm:w-auto bg-brand hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-xl transition shrink-0 flex items-center justify-center">
                            Apply this Promo
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Filters -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
                <form action="{{ route('client.guest_posts.index') }}" method="GET">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Row 1 -->
                        <!-- Moz DA -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Moz DA</label>
                            <div class="flex gap-2 items-center">
                                <input type="number" name="min_da" placeholder="From" value="{{ request('min_da', 1) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                <span class="text-gray-400 text-xs">To</span>
                                <input type="number" name="max_da" placeholder="To" value="{{ request('max_da', 100) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                            </div>
                        </div>

                        <!-- Categories -->
                        <div>
                            <label for="category" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Categories</label>
                            <select name="category" id="category" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                <option value="All" {{ request('category') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="Technology" {{ request('category') == 'Technology' ? 'selected' : '' }}>Technology</option>
                                <option value="Health" {{ request('category') == 'Health' ? 'selected' : '' }}>Health</option>
                                <option value="Business" {{ request('category') == 'Business' ? 'selected' : '' }}>Business</option>
                                <option value="Finance" {{ request('category') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Lifestyle" {{ request('category') == 'Lifestyle' ? 'selected' : '' }}>Lifestyle</option>
                            </select>
                        </div>

                        <!-- Website Language -->
                        <div class="lg:col-span-2">
                            <label for="language" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Website Language</label>
                            <select name="language" id="language" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3 max-w-xs">
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
                                <input type="number" name="min_dr" placeholder="From" value="{{ request('min_dr', 1) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                <span class="text-gray-400 text-xs">To</span>
                                <input type="number" name="max_dr" placeholder="To" value="{{ request('max_dr', 100) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                            </div>
                        </div>

                        <!-- Link Type -->
                        <div>
                            <label for="link_type" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Link Type</label>
                            <select name="link_type" id="link_type" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                <option value="All" {{ request('link_type') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="DoFollow" {{ request('link_type') == 'DoFollow' ? 'selected' : '' }}>DoFollow</option>
                                <option value="NoFollow" {{ request('link_type') == 'NoFollow' ? 'selected' : '' }}>NoFollow</option>
                            </select>
                        </div>

                        <!-- Max Links Allow -->
                        <div>
                            <label for="max_links_allowed" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Max Links Allow</label>
                            <input type="number" name="max_links_allowed" id="max_links_allowed" value="{{ request('max_links_allowed', 1) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3" min="1">
                        </div>

                        <!-- Marked As Sponsored -->
                        <div>
                            <label for="is_sponsored" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Marked As Sponsored</label>
                            <select name="is_sponsored" id="is_sponsored" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
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
                                <input type="number" name="min_price" placeholder="From" value="{{ request('min_price', 1) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                <span class="text-gray-400 text-xs">To</span>
                                <input type="number" name="max_price" placeholder="To" value="{{ request('max_price', 100000) }}" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                            </div>
                        </div>

                        <!-- Monthly Traffic -->
                        <div>
                            <label for="min_traffic" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2 text-indigo-600 flex items-center">
                                Monthly Traffic 
                                <svg class="w-3.5 h-3.5 ml-1 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            </label>
                            <select name="min_traffic" id="min_traffic" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                <option value="1" {{ request('min_traffic') == '1' ? 'selected' : '' }}>All</option>
                                <option value="1000" {{ request('min_traffic') == '1000' ? 'selected' : '' }}>1,000+</option>
                                <option value="10000" {{ request('min_traffic') == '10000' ? 'selected' : '' }}>10,000+</option>
                                <option value="100000" {{ request('min_traffic') == '100000' ? 'selected' : '' }}>100,000+</option>
                            </select>
                        </div>

                        <!-- Moz Spam Score -->
                        <div>
                            <label for="max_spam_score" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2 text-indigo-600 flex items-center">
                                Moz Spam Score 
                                <svg class="w-3.5 h-3.5 ml-1 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            </label>
                            <select name="max_spam_score" id="max_spam_score" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                <option value="" {{ request('max_spam_score') == '' ? 'selected' : '' }}>All</option>
                                <option value="5" {{ request('max_spam_score') == '5' ? 'selected' : '' }}>&lt;= 5%</option>
                                <option value="10" {{ request('max_spam_score') == '10' ? 'selected' : '' }}>&lt;= 10%</option>
                                <option value="30" {{ request('max_spam_score') == '30' ? 'selected' : '' }}>&lt;= 30%</option>
                            </select>
                        </div>

                        <!-- Service Type -->
                        <div>
                            <label for="service_type" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Service Type</label>
                            <select name="service_type" id="service_type" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3 border border-indigo-300 bg-indigo-50">
                                <option value="All" {{ request('service_type') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="Guest Post" {{ request('service_type') == 'Guest Post' ? 'selected' : '' }}>Guest Post</option>
                                <option value="Link Insertion" {{ request('service_type') == 'Link Insertion' ? 'selected' : '' }}>Link Insertion</option>
                                <option value="Press Release" {{ request('service_type') == 'Press Release' ? 'selected' : '' }}>Press Release</option>
                            </select>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-100 mt-4">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-150 text-sm">
                            Apply Filters
                        </button>
                        <a href="{{ route('client.guest_posts.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-150 text-sm">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Website URL</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Domain Authority (DA)</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Domain Rating (DR)</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Monthly Traffic</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($sites as $site)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <span class="text-indigo-600 font-bold uppercase">{{ substr(str_replace(['http://', 'https://', 'www.'], '', $site->url), 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ str_replace(['http://', 'https://'], '', $site->url) }}</div>
                                            <div class="text-xs text-indigo-600 font-medium">{{ $site->niche }}</div>
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
                                    <a href="{{ route('client.guest_posts.show', $site) }}" class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl transition duration-150 shadow-sm">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="text-lg font-medium">No guest post sites found matching your criteria.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($sites->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $sites->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
