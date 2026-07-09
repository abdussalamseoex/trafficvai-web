<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.traffic_campaign.index') }}" class="p-2.5 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-brand hover:border-brand transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Edit Traffic Limits') }}
                </h2>
                <p class="text-xs text-gray-500 font-medium mt-1">Order ID: <span class="font-bold text-gray-700">{{ $campaign->external_order_id }}</span></p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden p-8">
                
                <div class="mb-8 p-5 rounded-2xl bg-orange-50 border border-orange-100 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center text-white shrink-0 shadow-inner">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-orange-900 mb-1">Live Sync Warning</h4>
                        <p class="text-sm text-orange-800/80">Updating these limits will instantly sync with the Core Engine. Ensure your Traffic Points balance can support any increased limits.</p>
                    </div>
                </div>

                <form action="{{ route('client.traffic_campaign.update', $campaign) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Total Hits Limit (Lifetime)</label>
                            <input type="number" name="total_limit" value="{{ old('total_limit', $campaign->total_limit) }}" min="10"
                                class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Hourly Hit Limit</label>
                                <input type="number" name="hourly_limit" value="{{ old('hourly_limit', $campaign->hourly_limit) }}" min="1"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Daily Hit Limit</label>
                                <input type="number" name="daily_limit" value="{{ old('daily_limit', $campaign->daily_limit) }}" min="1"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-gray-900 focus:border-brand font-medium">
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ route('client.traffic_campaign.index') }}" class="px-6 py-3 rounded-xl font-bold text-sm text-gray-600 hover:bg-gray-100 transition">Cancel</a>
                            <button type="submit" class="px-8 py-3 rounded-xl bg-brand hover:bg-brand-dark text-white font-bold text-sm transition shadow-lg shadow-brand/20">
                                Save & Sync Limits
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
