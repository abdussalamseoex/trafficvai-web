<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Invoices') }}
            </h2>
            <a href="{{ route('admin.invoices.create') }}"
               class="inline-flex items-center gap-2 bg-brand hover:bg-orange-600 text-white font-bold px-5 py-2.5 rounded-xl transition shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Invoice
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="mb-6 flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by invoice # or client..."
                       class="flex-1 rounded-xl border-gray-200 shadow-sm text-sm focus:ring-brand focus:border-brand">
                <select name="status" class="rounded-xl border-gray-200 text-sm shadow-sm focus:ring-brand focus:border-brand">
                    <option value="">All Statuses</option>
                    @foreach(['draft','unpaid','paid','cancelled','overdue'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-800 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-gray-700 transition">Filter</button>
            </form>

            <div class="bg-white shadow-sm rounded-2xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="font-bold text-indigo-600 hover:underline">{{ $invoice->invoice_number }}</a>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $invoice->created_at->format('M d, Y') }}</div>
                                @if($invoice->type === 'renewal')
                                    <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-800">
                                        Renewal @if($invoice->order_id) (Order #{{ $invoice->order_id }}) @endif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 text-sm">{{ $invoice->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $invoice->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $colors = ['draft'=>'gray','unpaid'=>'yellow','paid'=>'green','cancelled'=>'red','overdue'=>'red'];
                                    $c = $colors[$invoice->status] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $c }}-100 text-{{ $c }}-800">{{ ucfirst($invoice->status) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                {{ $invoice->currency }} {{ number_format($invoice->total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold">View</a>
                                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="text-gray-600 hover:text-gray-800 text-xs font-bold">Edit</a>
                                    <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="text-green-600 hover:text-green-800 text-xs font-bold">PDF</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">No invoices found. <a href="{{ route('admin.invoices.create') }}" class="text-brand font-bold">Create one</a>.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($invoices->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $invoices->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
