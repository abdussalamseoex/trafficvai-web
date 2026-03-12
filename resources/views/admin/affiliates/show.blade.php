<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.affiliates.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mr-3">← Affiliates</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $affiliate->user->name ?? 'Unknown' }} — Affiliate Detail</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Clicks</p>
                    <p class="text-2xl font-extrabold text-blue-600">{{ number_format($stats['clicks']) }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Signups</p>
                    <p class="text-2xl font-extrabold text-indigo-600">{{ number_format($stats['signups']) }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Orders</p>
                    <p class="text-2xl font-extrabold text-orange-500">{{ number_format($stats['orders']) }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Commission</p>
                    <p class="text-2xl font-extrabold text-green-600">${{ number_format($stats['total_commission'], 2) }}</p>
                </div>
            </div>

            <!-- Referral Link Info -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Referral Link</h3>
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2">
                        <p class="font-mono text-sm text-gray-700">{{ url('/ref/' . $affiliate->code) }}</p>
                    </div>
                    <code class="bg-indigo-50 text-indigo-700 px-3 py-2 rounded-lg text-sm font-bold">{{ $affiliate->code }}</code>
                </div>
                <p class="text-xs text-gray-500 mt-2">Affiliate: <strong>{{ $affiliate->user->name ?? 'N/A' }}</strong> &middot; {{ $affiliate->user->email ?? '' }} &middot; Member since {{ $affiliate->user->created_at->format('M d, Y') }}</p>
            </div>

            <!-- Referrals Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Referred Signups & Orders</h3>
                    <p class="text-xs text-gray-500 mt-0.5">All activity attributed to this affiliate's referral link</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Date</th>
                                <th class="px-6 py-3 text-left">Referred User</th>
                                <th class="px-6 py-3 text-left">Order</th>
                                <th class="px-6 py-3 text-right">Commission</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($affiliate->referrals as $referral)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-500 text-xs">{{ $referral->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($referral->referredUser)
                                        <p class="font-semibold text-gray-900">{{ $referral->referredUser->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $referral->referredUser->email }}</p>
                                    @else
                                        <span class="text-gray-400 italic text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($referral->order)
                                        <a href="{{ route('admin.orders.show', $referral->order) }}" class="text-indigo-600 hover:underline font-medium">
                                            Order #{{ $referral->order_id }}
                                        </a>
                                        <p class="text-xs text-gray-400">${{ number_format($referral->order->total_amount, 2) }}</p>
                                    @else
                                        <span class="text-gray-400 italic text-xs">No order yet</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-green-600">
                                    ${{ number_format($referral->commission_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($referral->status === 'paid')
                                        <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">Paid</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <p class="font-medium text-gray-500">No referrals recorded yet</p>
                                    <p class="text-xs mt-1">Activity will appear here once someone registers or orders via this affiliate's link.</p>
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
