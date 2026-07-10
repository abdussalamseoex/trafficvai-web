<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-500/10 text-orange-600 border border-orange-500/20">Organic Traffic Delivery</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-600 border border-blue-500/20">Client Ledger</span>
                </div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Traffic Points & Topup Transactions') }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.traffic_campaigns.index') }}" class="px-4 py-2 rounded-xl bg-gray-900 text-white font-bold text-xs shadow hover:bg-gray-800 transition">
                    All Campaigns
                </a>
                <a href="{{ route('admin.traffic_campaigns.active') }}" class="px-4 py-2 rounded-xl bg-orange-500 text-white font-bold text-xs shadow hover:bg-orange-600 transition">
                    Active Running
                </a>
                <a href="{{ route('admin.traffic_campaigns.ledger') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs shadow hover:bg-blue-700 transition">
                    Points Ledger & Topups
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Overall Ledger Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block mb-1">Total Purchased Points</span>
                    <span class="text-2xl font-black text-emerald-700">{{ number_format($stats['total_credits'] ?? 0) }} Pts</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-orange-600 uppercase tracking-wider block mb-1">Total Points Deducted</span>
                    <span class="text-2xl font-black text-orange-700">{{ number_format($stats['total_debits'] ?? 0) }} Pts</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block mb-1">Total USD Topup Spend</span>
                    <span class="text-2xl font-black text-blue-700">${{ number_format($stats['total_usd_topups'] ?? 0, 2) }}</span>
                </div>
            </div>

            <!-- Search & Filter Bar -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mb-8">
                <form action="{{ route('admin.traffic_campaigns.ledger') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Client Name, Email or Transaction Description..." 
                        class="flex-1 rounded-xl border-gray-200 px-4 py-2.5 text-sm focus:border-brand focus:ring-brand">
                    
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gray-900 text-white font-bold text-sm hover:bg-gray-800 transition">
                        Filter Ledger
                    </button>
                </form>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                @if($ledgers->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-gray-500 font-medium">No point ledger transactions found.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-500">
                                    <th class="p-5">Client</th>
                                    <th class="p-5">Description</th>
                                    <th class="p-5">Type</th>
                                    <th class="p-5">Points</th>
                                    <th class="p-5">USD Cost</th>
                                    <th class="p-5">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($ledgers as $item)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="p-5">
                                            <a href="{{ route('admin.users.show', $item->user_id) }}" class="font-bold text-blue-600 hover:underline">
                                                {{ $item->user->name ?? 'User #' . $item->user_id }}
                                            </a>
                                            <div class="text-xs text-gray-400">{{ $item->user->email ?? '' }}</div>
                                        </td>
                                        <td class="p-5 font-medium text-gray-800">
                                            {{ $item->description }}
                                        </td>
                                        <td class="p-5">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase {{ $item->type === 'credit' ? 'bg-emerald-50 text-emerald-700' : 'bg-orange-50 text-orange-700' }}">
                                                {{ $item->type }}
                                            </span>
                                        </td>
                                        <td class="p-5 font-black {{ $item->type === 'credit' ? 'text-emerald-600' : 'text-orange-600' }}">
                                            {{ $item->type === 'credit' ? '+' : '-' }}{{ number_format($item->points) }} Pts
                                        </td>
                                        <td class="p-5 font-bold text-gray-900">
                                            {{ $item->usd_spent > 0 ? '$' . number_format($item->usd_spent, 2) : '-' }}
                                        </td>
                                        <td class="p-5 text-xs text-gray-500">
                                            {{ $item->created_at->format('M d, Y h:i A') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t border-gray-100">
                        {{ $ledgers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
