<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Wallet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Wallet Balance Card -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-900 rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden border border-gray-800 h-full flex flex-col justify-between">
                        <div class="absolute top-0 right-0 -m-8 w-32 h-32 bg-indigo-500/20 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 -m-8 w-24 h-24 bg-purple-500/20 rounded-full blur-3xl"></div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-8">
                                <div class="bg-gray-800 p-3 rounded-2xl border border-gray-700">
                                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                </div>
                                <span class="px-3 py-1 bg-green-500/10 text-green-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-green-500/20">Active</span>
                            </div>

                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Available Balance</div>
                            <div class="text-5xl font-black text-white mb-2"><span class="price-convert" data-base-price="{{ $wallet->balance }}">${{ number_format($wallet->balance, 2) }}</span></div>
                            <p class="text-gray-500 text-xs mb-8 italic">Ready to use for any direct service purchase.</p>
                        </div>

                        <div class="relative z-10 space-y-3">
                            <a href="{{ route('client.payments.topup') }}" class="w-full flex justify-center items-center py-4 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl transition shadow-xl shadow-indigo-600/20 active:scale-95 group">
                                <span>Add Balance</span>
                                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </a>
                            <p class="text-[10px] text-center text-gray-500 uppercase tracking-widest font-bold">Secure payments by Stripe</p>
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Pending Requests (New Section) -->
                    @if(isset($pendingTopups) && $pendingTopups->count() > 0)
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-yellow-100 overflow-hidden flex flex-col">
                        <div class="p-8 border-b border-yellow-50 flex items-center justify-between bg-yellow-50/30">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Pending Manual Payments</h3>
                                <p class="text-xs text-yellow-600 font-medium italic mt-1">Administrator is verifying these payments.</p>
                            </div>
                            <div class="text-[10px] font-black text-yellow-500 uppercase tracking-widest bg-white px-3 py-1 rounded-full border border-yellow-100">Action Required</div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50/50 text-gray-400 text-[10px] uppercase tracking-widest font-bold">
                                    <tr>
                                        <th class="px-8 py-4">Details</th>
                                        <th class="px-8 py-4">Method</th>
                                        <th class="px-8 py-4 text-right">Amount</th>
                                        <th class="px-8 py-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($pendingTopups as $pending)
                                    <tr class="hover:bg-yellow-50/20 transition">
                                        <td class="px-8 py-6">
                                            <div class="text-sm font-bold text-gray-900">Top-up Request #{{ $pending->id }}</div>
                                            <div class="text-[10px] text-gray-400 mt-1">{{ $pending->created_at->format('M d, Y h:i A') }}</div>
                                        </td>
                                        <td class="px-8 py-6 uppercase text-[10px] font-bold text-gray-500 tracking-wider">
                                            {{ str_replace('_', ' ', $pending->payment_method) }}
                                        </td>
                                        <td class="px-8 py-6 text-right font-black text-gray-900"><span class="price-convert" data-base-price="{{ $pending->amount }}">${{ number_format($pending->amount, 2) }}</span></td>
                                        <td class="px-8 py-6">
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-black uppercase tracking-widest rounded-full">Pending</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                            <h3 class="text-xl font-bold text-gray-900">Transaction History</h3>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recent Activity</div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase tracking-widest font-bold">
                                    <tr>
                                        <th class="px-8 py-4">Transaction Details</th>
                                        <th class="px-8 py-4">Method / Source</th>
                                        <th class="px-8 py-4 text-right">Amount</th>
                                        <th class="px-8 py-4">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($transactions as $transaction)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-8 py-6">
                                            <div class="text-sm font-bold text-gray-900 line-clamp-1">{{ $transaction->description }}</div>
                                            <div class="text-[10px] font-black uppercase tracking-widest mt-1 {{ $transaction->type === 'credit' ? 'text-green-500' : 'text-red-400' }}">
                                                {{ $transaction->type }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 uppercase text-[10px] font-bold text-gray-400 tracking-wider">
                                            {{ str_replace('_', ' ', $transaction->source) }}
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <span class="text-base font-black {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-gray-900' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}<span class="price-convert" data-base-price="{{ $transaction->amount }}">${{ number_format($transaction->amount, 2) }}</span>
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-[11px] text-gray-400">
                                            {{ $transaction->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-8 py-20 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="bg-gray-100 p-4 rounded-full mb-4">
                                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </div>
                                                <p class="text-gray-400 italic text-sm font-medium">No transactions yet. Add balance to get started.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-6 border-t border-gray-50 bg-gray-50/30">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
