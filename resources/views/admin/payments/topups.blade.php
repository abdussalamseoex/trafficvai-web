<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Top-up Requests') }}
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
                <div class="p-6 border-b border-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Manual Approval Queue</h3>
                    <p class="text-sm text-gray-500">Approve or reject manual bank transfer requests for wallet balance.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-widest font-bold">
                            <tr>
                                <th class="px-6 py-4">Request ID</th>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Method</th>
                                <th class="px-6 py-4">Transaction ID</th>
                                <th class="px-6 py-4">Sender Phone</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Proof / Reference</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($requests as $topup)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-xs font-mono text-gray-400">#TP-{{ $topup->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $topup->user->name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $topup->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 font-black text-indigo-600">${{ number_format($topup->amount, 2) }}</td>
                                <td class="px-6 py-4 uppercase text-[10px] font-bold text-gray-500 tracking-wider">
                                    {{ str_replace('_', ' ', $topup->payment_method) }}
                                </td>
                                <td class="px-6 py-4 text-xs font-mono font-bold text-indigo-600">
                                    {{ $topup->transaction_id ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-gray-600">
                                    {{ $topup->sender_number ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <span class="px-2 py-0.5 rounded-full font-bold uppercase tracking-widest
                                        @if($topup->status === 'approved') bg-green-100 text-green-700
                                        @elseif($topup->status === 'pending') bg-yellow-100 text-yellow-700
                                        @elseif($topup->status === 'rejected') bg-red-100 text-red-700
                                        @else bg-gray-100 text-gray-500 @endif">
                                        {{ $topup->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400 italic">
                                    {{ Str::limit($topup->proof, 20) ?? '--' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($topup->status === 'pending')
                                    <div class="flex items-center justify-end gap-2" x-data="{ confirmingReject: false, confirmingApprove: false }">
                                        <button @click="confirmingApprove = true" style="background-color: #10b981 !important; color: white !important;" class="text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-lg flex items-center gap-1.5 border border-green-600/20 whitespace-nowrap">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            Approve
                                        </button>
                                        
                                        <button @click="confirmingReject = true" class="bg-white border-2 border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold px-4 py-2 rounded-xl transition shadow-sm">
                                            Reject
                                        </button>

                                        <!-- Approve Modal -->
                                        <div x-show="confirmingApprove" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm" x-cloak x-transition>
                                            <div @click.away="confirmingApprove = false" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm mx-4 overflow-hidden border border-gray-100">
                                                <div class="p-8 text-center">
                                                    <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                                                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </div>
                                                    <h3 class="text-xl font-black text-gray-900 mb-2">Approve Top-up</h3>
                                                    <p class="text-sm text-gray-500 mb-8">Confirm approval for <span class="font-bold text-green-600">${{ number_format($topup->amount, 2) }}</span>? This credits the user wallet immediately.</p>
                                                    
                                                    <form action="{{ route('admin.payments.topups.approve', $topup) }}" method="POST">
                                                        @csrf
                                                        <div class="flex gap-3">
                                                            <button type="button" @click="confirmingApprove = false" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3.5 rounded-2xl transition hover:bg-gray-200">Cancel</button>
                                                            <button type="submit" style="background-color: #10b981 !important; color: white !important;" class="flex-1 font-bold py-3.5 rounded-2xl transition shadow-lg">Confirm</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Reject Modal -->
                                        <div x-show="confirmingReject" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm" x-cloak x-transition>
                                            <div @click.away="confirmingReject = false" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm mx-4 overflow-hidden border border-gray-100">
                                                <div class="p-8 text-center">
                                                    <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                                                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                    </div>
                                                    <h3 class="text-xl font-black text-gray-900 mb-2">Reject Request</h3>
                                                    <p class="text-sm text-gray-500 mb-6">Explain the reason for rejection.</p>
                                                    
                                                    <form action="{{ route('admin.payments.topups.reject', $topup) }}" method="POST">
                                                        @csrf
                                                        <textarea name="admin_note" rows="3" placeholder="e.g. Invalid transaction ID" class="w-full rounded-2xl border-gray-100 text-sm focus:ring-red-500 focus:border-red-500 mb-6 bg-gray-50"></textarea>
                                                        <div class="flex gap-3">
                                                            <button type="button" @click="confirmingReject = false" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3.5 rounded-2xl">Cancel</button>
                                                            <button type="submit" class="flex-1 bg-red-500 text-white font-bold py-3.5 rounded-2xl hover:bg-red-600 transition shadow-lg">Reject</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest italic">Processed at {{ $topup->updated_at->format('M d') }}</div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">No top-up requests in queue.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-gray-50">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
