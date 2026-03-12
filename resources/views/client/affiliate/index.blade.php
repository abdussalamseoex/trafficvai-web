<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Affiliate Program') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl shadow-lg overflow-hidden relative">
                <div class="absolute inset-0 bg-pattern opacity-10"></div>
                <div class="relative px-8 py-12 md:p-16 text-center lg:text-left flex flex-col lg:flex-row items-center justify-between">
                    <div class="lg:w-2/3 mb-8 lg:mb-0">
                        <h3 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl">
                            Refer Friends. Earn <span class="text-yellow-400">Recurring Commissions.</span>
                        </h3>
                        <p class="mt-4 text-lg text-indigo-100 max-w-2xl mx-auto lg:mx-0">
                            Share your unique referral link with your network. When they sign up and make a purchase, you earn a 10% commission on all their orders for life.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Referral Link Box -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-8">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Your Unique Referral Link</h4>
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="text" id="referral-link" readonly value="{{ $referralLink }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 text-gray-600 font-mono text-sm py-3 px-4">
                        </div>
                        <button onclick="copyReferralLink()" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Copy Link
                        </button>
                    </div>
                    <p id="copy-success" class="mt-2 text-sm text-green-600 hidden font-medium">Link copied to clipboard!</p>
                </div>
            </div>

            <script>
                function copyReferralLink() {
                    var copyText = document.getElementById("referral-link");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999); // For mobile devices
                    navigator.clipboard.writeText(copyText.value);
                    
                    document.getElementById('copy-success').classList.remove('hidden');
                    setTimeout(() => {
                        document.getElementById('copy-success').classList.add('hidden');
                    }, 3000);
                }
            </script>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 truncate">Total Clicks</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['clicks'] }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 truncate">Sign Ups</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['signups'] }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 truncate">Earnings</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900"><span class="price-convert" data-base-price="{{ $stats['earnings'] }}">${{ number_format($stats['earnings'], 2) }}</span></p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 truncate">Conversion Rate</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['conversion_rate'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Referrals Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Referrals</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Earned</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentReferrals as $referral)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $referral->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $referral->referredUser->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($referral->status === 'paid')
                                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">Paid</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2.5 py-1 rounded-full">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600"><span class="price-convert" data-base-price="{{ $referral->commission_amount }}">${{ number_format($referral->commission_amount, 2) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <p class="text-base font-medium text-gray-900">No referrals yet</p>
                                    <p class="mt-1">Share your link above to start earning commissions!</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
