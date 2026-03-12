<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Affiliate Management</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-100 text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Commission Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Commission Settings</h3>
                        <p class="text-xs text-gray-500">Control the global affiliate commission percentage applied to all orders</p>
                    </div>
                </div>

                <form action="{{ route('admin.affiliates.settings') }}" method="POST" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Commission Rate (%)</label>
                            <div class="relative">
                                <input type="number" name="affiliate_commission_rate" 
                                       value="{{ \App\Models\Setting::get('affiliate_commission_rate', '10') }}"
                                       min="0" max="100" step="0.5"
                                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm pr-10">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">%</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Percentage of each referred order paid as commission</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Payout (USD)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">$</span>
                                <input type="number" name="affiliate_min_payout"
                                       value="{{ \App\Models\Setting::get('affiliate_min_payout', '50') }}"
                                       min="0" step="1"
                                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm pl-7">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Minimum balance before a payout can be requested</p>
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-sm">
                                Save Commission Settings
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-6 text-xs text-gray-600">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-orange-400 inline-block"></span>
                            Current Rate: <strong>{{ \App\Models\Setting::get('affiliate_commission_rate', '10') }}%</strong>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-400 inline-block"></span>
                            Min. Payout: <strong>${{ \App\Models\Setting::get('affiliate_min_payout', '50') }}</strong>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Overview Stats -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Total Affiliates</p>
                    <p class="text-2xl font-extrabold text-gray-900">{{ number_format($totalAffiliates) }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Total Clicks</p>
                    <p class="text-2xl font-extrabold text-blue-600">{{ number_format($totalClicks) }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Signups Referred</p>
                    <p class="text-2xl font-extrabold text-indigo-600">{{ number_format($totalSignups) }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Orders via Refs</p>
                    <p class="text-2xl font-extrabold text-orange-500">{{ number_format($totalOrders) }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Total Commission</p>
                    <p class="text-2xl font-extrabold text-green-600">${{ number_format($totalCommission, 2) }}</p>
                </div>
            </div>

            <!-- Affiliates Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">All Affiliates</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Users who have a referral code</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Affiliate</th>
                                <th class="px-6 py-3 text-left">Referral Code</th>
                                <th class="px-6 py-3 text-center">Clicks</th>
                                <th class="px-6 py-3 text-center">Signups</th>
                                <th class="px-6 py-3 text-center">Orders</th>
                                <th class="px-6 py-3 text-right">Commission</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($affiliates as $affiliate)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm mr-3 flex-shrink-0">
                                            {{ strtoupper(substr($affiliate->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $affiliate->user->name ?? 'Deleted User' }}</p>
                                            <p class="text-xs text-gray-400">{{ $affiliate->user->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <code class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-mono">{{ $affiliate->code }}</code>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-blue-600">{{ number_format($affiliate->clicks) }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-indigo-600">{{ $affiliate->signups_count ?? 0 }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-orange-500">{{ $affiliate->orders_count ?? 0 }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-green-600">
                                    ${{ number_format($affiliate->total_commission ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.affiliates.show', $affiliate) }}" 
                                       class="text-indigo-600 hover:text-indigo-800 font-semibold text-xs bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-base font-medium text-gray-500">No affiliates yet</p>
                                    <p class="text-xs text-gray-400 mt-1">Users will appear here once they have a referral code.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($affiliates->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $affiliates->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
