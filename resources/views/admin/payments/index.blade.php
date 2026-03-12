<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payments Overview') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Revenue</div>
                    <div class="text-3xl font-black text-indigo-600">${{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Top-ups</div>
                    <div class="text-3xl font-black text-purple-600">${{ number_format($stats['total_topup'], 2) }}</div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Pending Top-ups</div>
                    <div class="text-3xl font-black text-orange-500">{{ $stats['pending_topups'] }}</div>
                    @if($stats['pending_topups'] > 0)
                        <a href="{{ route('admin.payments.topups') }}" class="mt-2 text-xs font-bold text-indigo-600 hover:underline flex items-center">
                            Review Requests <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    @endif
                </div>
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Estimated Liabilities</div>
                    <div class="text-3xl font-black text-blue-600">${{ number_format($stats['total_users_balance'], 2) }}</div>
                    <div class="text-[10px] text-gray-400 italic">Sum of all user wallet balances</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Transactions -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Recent Transactions</h3>
                            <a href="{{ route('admin.payments.transactions') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-widest font-bold">
                                    <tr>
                                        <th class="px-6 py-4">User</th>
                                        <th class="px-6 py-4">Type</th>
                                        <th class="px-6 py-4">Source</th>
                                        <th class="px-6 py-4 text-right">Amount</th>
                                        <th class="px-6 py-4">Status</th>
                                        <th class="px-6 py-4">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($recentTransactions as $transaction)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $transaction->user->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $transaction->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 uppercase text-[10px] font-black tracking-widest">
                                            <span class="{{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-500' }}">
                                                {{ $transaction->type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-medium text-gray-500 uppercase">{{ $transaction->source }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm font-black {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-gray-900' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest
                                                @if($transaction->status === 'completed') bg-green-100 text-green-700
                                                @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-700
                                                @else bg-red-100 text-red-700 @endif">
                                                {{ $transaction->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-400">
                                            {{ $transaction->created_at->format('M d, H:i') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">No transactions found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Wallet Adjustment Tool -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-900 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden border border-gray-800">
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold text-white mb-2">Manual Adjustment</h3>
                            <p class="text-gray-400 text-sm mb-6 leading-relaxed">Manually credit or debit a user's wallet balance. Use this with caution.</p>
                            
                            <form action="{{ route('admin.payments.wallet.adjust') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Search User</label>
                                    <select name="user_id" class="w-full bg-gray-800 border-gray-700 rounded-2xl text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Select User --</option>
                                        @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} (${{ number_format($user->balance, 2) }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Action</label>
                                        <select name="type" class="w-full bg-gray-800 border-gray-700 rounded-2xl text-white text-sm">
                                            <option value="credit">Credit (+)</option>
                                            <option value="debit">Debit (-)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Amount ($)</label>
                                        <input type="number" step="0.01" name="amount" placeholder="0.00" class="w-full bg-gray-800 border-gray-700 rounded-2xl text-white text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Description / Reason</label>
                                    <textarea name="description" rows="2" placeholder="e.g. Loyalty bonus, Refund for order #123" class="w-full bg-gray-800 border-gray-700 rounded-2xl text-white text-sm"></textarea>
                                </div>

                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl transition shadow-xl shadow-indigo-600/20 active:scale-95">
                                    Process Adjustment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
