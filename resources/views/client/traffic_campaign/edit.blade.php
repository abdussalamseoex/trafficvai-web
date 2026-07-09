<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.traffic_campaign.index') }}" class="p-2.5 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-brand hover:border-brand transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Edit Campaign: ') }} {{ $campaign->external_order_id }}
                </h2>
                <p class="text-xs text-gray-500 font-medium mt-1">Target URL: <span class="font-bold text-gray-700">{{ $campaign->url }}</span></p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden p-8">
                
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                        <ul class="list-disc list-inside text-sm text-red-600 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('client.traffic_campaign.update', $campaign) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-8">
                        <!-- Limits Section -->
                        <div>
                            <h3 class="font-black text-lg text-gray-900 mb-4 border-b border-gray-100 pb-2">Traffic Limits</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Total Hits (Lifetime)</label>
                                    <input type="number" name="total_limit" value="{{ old('total_limit', $campaign->total_limit) }}" min="10"
                                        class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Hourly Limit</label>
                                    <input type="number" name="hourly_limit" value="{{ old('hourly_limit', $campaign->hourly_limit) }}" min="1"
                                        class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Daily Limit</label>
                                    <input type="number" name="daily_limit" value="{{ old('daily_limit', $campaign->daily_limit) }}" min="1"
                                        class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                                </div>
                            </div>
                        </div>

                        <!-- Behavior Section -->
                        <div>
                            <h3 class="font-black text-lg text-gray-900 mb-4 border-b border-gray-100 pb-2">Behavior & Engagement</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Main Page Duration (Seconds)</label>
                                    <input type="number" name="duration" value="{{ old('duration', $campaign->duration) }}" min="10" max="600"
                                        class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Target Country (Select Multiple)</label>
                                    <div class="grid grid-cols-2 gap-2 h-36 overflow-y-auto p-2 bg-gray-50 border border-gray-300 rounded-xl">
                                        @php
                                            $selectedCountries = explode(',', $campaign->target_country ?? '');
                                            $selectedCountries = array_map('trim', $selectedCountries);
                                            $allCountries = ['Worldwide', 'United States', 'United Kingdom', 'Canada', 'Australia', 'Germany', 'France', 'India', 'Bangladesh'];
                                        @endphp
                                        @foreach($allCountries as $country)
                                            <label class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-200 cursor-pointer transition">
                                                <input type="checkbox" name="target_country[]" value="{{ $country }}" {{ in_array($country, $selectedCountries) ? 'checked' : '' }} class="rounded border-gray-300 text-brand focus:ring-brand w-4 h-4">
                                                <span class="text-xs font-bold text-gray-800">{{ $country }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Device Targeting</label>
                                    <select name="device_type" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                                        <option value="All" {{ $campaign->device_type == 'All' ? 'selected' : '' }}>Mixed (Desktop + Mobile)</option>
                                        <option value="desktop" {{ $campaign->device_type == 'desktop' ? 'selected' : '' }}>Desktop Only</option>
                                        <option value="mobile" {{ $campaign->device_type == 'mobile' ? 'selected' : '' }}>Mobile Only</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Sub-Pages to Visit</label>
                                    <select name="sub_page_visits" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                                        <option value="0" {{ $campaign->sub_page_visits == 0 ? 'selected' : '' }}>None (Single Page)</option>
                                        <option value="1" {{ $campaign->sub_page_visits == 1 ? 'selected' : '' }}>1 Inner Page</option>
                                        <option value="2" {{ $campaign->sub_page_visits == 2 ? 'selected' : '' }}>2 Inner Pages</option>
                                        <option value="3" {{ $campaign->sub_page_visits == 3 ? 'selected' : '' }}>3 Inner Pages</option>
                                    </select>
                                    <input type="hidden" name="sub_page_toggle" value="{{ $campaign->sub_page_visits > 0 ? '1' : '0' }}">
                                </div>
                            </div>
                        </div>

                        <!-- Source Section -->
                        <div>
                            <h3 class="font-black text-lg text-gray-900 mb-4 border-b border-gray-100 pb-2">Traffic Source Data</h3>
                            
                            @if($campaign->campaign_type === 'search')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">Search Engine</label>
                                        <select name="search_engine" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                                            <option value="google" {{ $campaign->search_engine == 'google' ? 'selected' : '' }}>Google Search</option>
                                            <option value="bing" {{ $campaign->search_engine == 'bing' ? 'selected' : '' }}>Bing Search</option>
                                            <option value="yahoo" {{ $campaign->search_engine == 'yahoo' ? 'selected' : '' }}>Yahoo Search</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">Search Keywords (Comma Separated)</label>
                                        <textarea name="keywords" rows="3" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">{{ is_array($campaign->keywords) ? implode(', ', $campaign->keywords) : $campaign->keywords }}</textarea>
                                    </div>
                                </div>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">Traffic Sources (Comma Separated)</label>
                                        <input type="text" name="traffic_source" value="{{ old('traffic_source', $campaign->traffic_source) }}"
                                            placeholder="direct, facebook, twitter"
                                            class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">Custom Referrers (One per line)</label>
                                        <textarea name="custom_referrers" rows="3" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">{{ old('custom_referrers', $campaign->custom_referrers) }}</textarea>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ route('client.traffic_campaign.index') }}" class="px-6 py-3 rounded-xl font-bold text-sm text-gray-600 hover:bg-gray-100 transition">Cancel</a>
                            <button type="submit" class="px-8 py-3 rounded-xl bg-brand hover:bg-brand-dark text-white font-bold text-sm transition shadow-lg shadow-brand/20">
                                Save & Sync Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
