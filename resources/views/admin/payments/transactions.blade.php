<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('All Transactions') }}
            </h2>
            <a href="{{ route('admin.payments.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Overview
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="text-lg font-bold text-gray-900">Transaction History</h3>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.payments.transactions') }}" method="GET" class="flex items-center gap-2">
                            <input type="text" name="search" placeholder="Search user or ID..." value="{{ request('search') }}" class="rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 min-w-[240px]">
                            <button type="submit" class="bg-indigo-600 text-white p-2 rounded-xl hover:bg-indigo-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-widest font-bold">
                            <tr>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Source</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-xs font-mono text-gray-400">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $transaction->user->name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $transaction->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 uppercase text-[10px] font-black tracking-widest">
                                    <span class="{{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $transaction->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-lg bg-gray-100 text-[9px] font-bold text-gray-600 uppercase tracking-widest">{{ $transaction->source }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-black {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600 italic">
                                    {{ $transaction->description }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest
                                        @if($transaction->status === 'completed') bg-green-100 text-green-700
                                        @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ $transaction->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic">No transactions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-gray-50">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
