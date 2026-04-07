<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Guest Post Site') }}: {{ $guestPost->url }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.guest-posts.update', $guestPost) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- URL -->
                            <div>
                                <x-input-label for="url" :value="__('Website URL')" />
                                <x-text-input id="url" class="block mt-1 w-full" type="url" name="url" :value="old('url', $guestPost->url)" required autofocus />
                                <x-input-error :messages="$errors->get('url')" class="mt-2" />
                            </div>

                            <!-- Niche -->
                            <div x-data="{
                                search: '',
                                categories: [
                                    'Adult', 'All Categories', 'App', 'Art', 'Astrology', 'Automotive', 'Beauty', 'Betting & Gambling', 'Biography', 'Blog', 'Business', 'Casino', 'CBD', 'Crypto', 'Dental', 'Digital Marketing', 'Education', 'Electronics', 'Entertainment', 'Event', 'Family & Parenting', 'Fashion', 'Finance', 'Food', 'Furniture', 'Game', 'Garden', 'General', 'Green Environment & Agriculture', 'Hair loss', 'Health & Fitness', 'Home Improvement', 'Industry & Manufacturing', 'Jewellery', 'Job & Career', 'Law & Legal', 'Lifestyle', 'Logistics', 'Magazine', 'News & Media', 'Pet & Animal', 'Photography', 'Poetry', 'Real Estate', 'Saas', 'Service', 'Shopping', 'Social Media', 'Software', 'Spa & Massage', 'Sport', 'Technology', 'Trading', 'Transportation', 'Travel', 'Visa', 'Web & Technology', 'Wedding'
                                ],
                                selected: {{ json_encode(old('niche', is_array($guestPost->niche) ? $guestPost->niche : (!empty($guestPost->niche) ? [$guestPost->niche] : []))) }},
                                get filteredCategories() {
                                    if (this.search === '') return this.categories;
                                    return this.categories.filter(c => c.toLowerCase().includes(this.search.toLowerCase()));
                                }
                            }">
                                <div class="flex items-center gap-3 mb-2">
                                    <x-input-label for="niche" :value="__('Categories')" />
                                    <input type="text" x-model="search" placeholder="Search categories..." class="border-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-1.5 px-3 min-w-[200px]">
                                </div>
                                
                                <div class="border border-gray-200 rounded-lg p-4 max-h-60 overflow-y-auto bg-gray-50/50 shadow-inner">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-3 gap-x-4">
                                        <template x-for="category in filteredCategories" :key="category">
                                            <label class="inline-flex items-center cursor-pointer hover:bg-gray-100 rounded-md px-1 py-0.5 transition -ml-1">
                                                <input type="checkbox" name="niche[]" :value="category" x-model="selected" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700" x-text="category"></span>
                                            </label>
                                        </template>
                                        <div x-show="filteredCategories.length === 0" class="col-span-full py-4 text-center text-sm text-gray-500 font-medium select-none" x-cloak>
                                            No categories matching "<span x-text="search"></span>"
                                        </div>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('niche')" class="mt-2" />
                            </div>

                            <!-- DA -->
                            <div>
                                <x-input-label for="da" :value="__('Domain Authority (DA)')" />
                                <x-text-input id="da" class="block mt-1 w-full" type="number" name="da" min="0" max="100" :value="old('da', $guestPost->da)" />
                                <x-input-error :messages="$errors->get('da')" class="mt-2" />
                            </div>

                            <!-- DR -->
                            <div>
                                <x-input-label for="dr" :value="__('Domain Rating (DR)')" />
                                <x-text-input id="dr" class="block mt-1 w-full" type="number" name="dr" min="0" max="100" :value="old('dr', $guestPost->dr)" />
                                <x-input-error :messages="$errors->get('dr')" class="mt-2" />
                            </div>

                            <!-- Traffic -->
                            <div>
                                <x-input-label for="traffic" :value="__('Monthly Traffic')" />
                                <x-text-input id="traffic" class="block mt-1 w-full" type="number" name="traffic" min="0" :value="old('traffic', $guestPost->traffic)" />
                                <x-input-error :messages="$errors->get('traffic')" class="mt-2" />
                            </div>

                            <!-- Price (Placement) -->
                            <div>
                                <x-input-label for="price" :value="__('Placement Price ($)')" />
                                <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" min="0" :value="old('price', $guestPost->price)" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Price (Creation & Placement) -->
                            <div>
                                <x-input-label for="price_creation_placement" :value="__('Creation & Placement Price ($)')" />
                                <x-text-input id="price_creation_placement" class="block mt-1 w-full" type="number" step="0.01" name="price_creation_placement" min="0" :value="old('price_creation_placement', $guestPost->price_creation_placement)" />
                                <x-input-error :messages="$errors->get('price_creation_placement')" class="mt-2" />
                            </div>

                            <!-- Price (Link Insertion) -->
                            <div>
                                <x-input-label for="price_link_insertion" :value="__('Link Insertion Price ($)')" />
                                <x-text-input id="price_link_insertion" class="block mt-1 w-full" type="number" step="0.01" name="price_link_insertion" min="0" :value="old('price_link_insertion', $guestPost->price_link_insertion)" />
                                <x-input-error :messages="$errors->get('price_link_insertion')" class="mt-2" />
                            </div>

                            <!-- Link Type -->
                            <div>
                                <x-input-label for="link_type" :value="__('Link Type')" />
                                <select id="link_type" name="link_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="DoFollow" {{ old('link_type', $guestPost->link_type) == 'DoFollow' ? 'selected' : '' }}>DoFollow</option>
                                    <option value="NoFollow" {{ old('link_type', $guestPost->link_type) == 'NoFollow' ? 'selected' : '' }}>NoFollow</option>
                                </select>
                                <x-input-error :messages="$errors->get('link_type')" class="mt-2" />
                            </div>

                            <!-- Max Links Allowed -->
                            <div>
                                <x-input-label for="max_links_allowed" :value="__('Max Links Allowed')" />
                                <x-text-input id="max_links_allowed" class="block mt-1 w-full" type="number" name="max_links_allowed" min="1" :value="old('max_links_allowed', $guestPost->max_links_allowed)" required />
                                <x-input-error :messages="$errors->get('max_links_allowed')" class="mt-2" />
                            </div>

                            <!-- Language -->
                            <div>
                                <x-input-label for="language" :value="__('Language')" />
                                <x-text-input id="language" class="block mt-1 w-full" type="text" name="language" :value="old('language', $guestPost->language)" required />
                                <x-input-error :messages="$errors->get('language')" class="mt-2" />
                            </div>

                            <!-- Service Type -->
                            <div>
                                <x-input-label for="service_type" :value="__('Service Type')" />
                                <select id="service_type" name="service_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Guest Post" {{ old('service_type', $guestPost->service_type) == 'Guest Post' ? 'selected' : '' }}>Guest Post</option>
                                    <option value="Link Insertion" {{ old('service_type', $guestPost->service_type) == 'Link Insertion' ? 'selected' : '' }}>Link Insertion</option>
                                    <option value="Press Release" {{ old('service_type', $guestPost->service_type) == 'Press Release' ? 'selected' : '' }}>Press Release</option>
                                </select>
                                <x-input-error :messages="$errors->get('service_type')" class="mt-2" />
                            </div>

                            <!-- Ownership Type -->
                            <div>
                                <x-input-label for="ownership_type" :value="__('Ownership Type / Role')" />
                                <select id="ownership_type" name="ownership_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Owner" {{ old('ownership_type', $guestPost->ownership_type ?? 'Owner') == 'Owner' ? 'selected' : '' }}>Owner</option>
                                    <option value="Contributor" {{ old('ownership_type', $guestPost->ownership_type ?? 'Owner') == 'Contributor' ? 'selected' : '' }}>Contributor</option>
                                </select>
                                <x-input-error :messages="$errors->get('ownership_type')" class="mt-2" />
                            </div>

                            <!-- Spam Score -->
                            <div>
                                <x-input-label for="spam_score" :value="__('Spam Score (%)')" />
                                <x-text-input id="spam_score" class="block mt-1 w-full" type="number" name="spam_score" min="0" max="100" :value="old('spam_score', $guestPost->spam_score)" />
                                <x-input-error :messages="$errors->get('spam_score')" class="mt-2" />
                            </div>

                            <!-- Delivery Time Days -->
                            <div>
                                <x-input-label for="delivery_time_days" :value="__('Turnaround Time (TAT) in Days')" />
                                <x-text-input id="delivery_time_days" class="block mt-1 w-full" type="number" name="delivery_time_days" min="1" :value="old('delivery_time_days', $guestPost->delivery_time_days)" placeholder="e.g. 3" />
                                <p class="text-xs text-gray-500 mt-1">Average days to deliver</p>
                                <x-input-error :messages="$errors->get('delivery_time_days')" class="mt-2" />
                            </div>

                            <!-- Express Delivery Time Days -->
                            <div>
                                <x-input-label for="express_delivery_time_days" :value="__('Express Turnaround Time (Days)')" />
                                <x-text-input id="express_delivery_time_days" class="block mt-1 w-full" type="number" name="express_delivery_time_days" min="1" :value="old('express_delivery_time_days', $guestPost->express_delivery_time_days)" placeholder="e.g. 1" />
                                <p class="text-xs text-gray-500 mt-1">Faster delivery option (optional)</p>
                                <x-input-error :messages="$errors->get('express_delivery_time_days')" class="mt-2" />
                            </div>

                            <!-- Express Delivery Price -->
                            <div>
                                <x-input-label for="express_delivery_price" :value="__('Express Delivery Price ($)')" />
                                <x-text-input id="express_delivery_price" class="block mt-1 w-full" type="number" step="0.01" name="express_delivery_price" min="0" :value="old('express_delivery_price', $guestPost->express_delivery_price)" placeholder="e.g. 50" />
                                <p class="text-xs text-gray-500 mt-1">Price for express delivery (leave empty if not applicable)</p>
                                <x-input-error :messages="$errors->get('express_delivery_price')" class="mt-2" />
                            </div>

                            <!-- Word Count -->
                            <div>
                                <x-input-label for="word_count" :value="__('Article Word Count (For Creation Phase)')" />
                                <x-text-input id="word_count" class="block mt-1 w-full" type="number" name="word_count" min="100" step="50" :value="old('word_count', $guestPost->word_count ?? 500)" placeholder="e.g. 500" />
                                <p class="text-xs text-gray-500 mt-1">Default words for Creation & Placement</p>
                                <x-input-error :messages="$errors->get('word_count')" class="mt-2" />
                            </div>

                            <!-- Sample Post URL -->
                            <div class="md:col-span-2">
                                <x-input-label for="sample_post_url" :value="__('Sample Post URL')" />
                                <x-text-input id="sample_post_url" class="block mt-1 w-full" type="url" name="sample_post_url" :value="old('sample_post_url', $guestPost->sample_post_url)" placeholder="https://example.com/sample-post" />
                                <p class="text-sm text-gray-500 mt-1">A link to a live example guest post to show clients.</p>
                                <x-input-error :messages="$errors->get('sample_post_url')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description / Editor Notes')" />
                                <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" placeholder="Any specific rules or notes about this website...">{{ old('description', $guestPost->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Is Sponsored -->
                        <div class="block mt-4">
                            <label for="is_sponsored" class="inline-flex items-center">
                                <input id="is_sponsored" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_sponsored" value="1" {{ old('is_sponsored', $guestPost->is_sponsored) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600">{{ __('Marked As Sponsored') }}</span>
                            </label>
                            <x-input-error :messages="$errors->get('is_sponsored')" class="mt-2" />
                        </div>

                        <!-- Is Active -->
                        <div class="block mt-4">
                            <label for="is_active" class="inline-flex items-center">
                                <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_active" value="1" {{ old('is_active', $guestPost->is_active) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600">{{ __('Listed publicly in Guest Post Inventory') }}</span>
                            </label>
                            <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                        </div>

                        <!-- Is Featured -->
                        <div class="block mt-4">
                            <label for="is_featured" class="inline-flex items-center">
                                <input id="is_featured" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_featured" value="1" {{ old('is_featured', $guestPost->is_featured) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm font-bold text-amber-600">{{ __('Feature this site (shows at the top of client dashboard)') }}</span>
                            </label>
                            <x-input-error :messages="$errors->get('is_featured')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.guest-posts.index') }}" class="text-gray-600 hover:underline mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Site') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
