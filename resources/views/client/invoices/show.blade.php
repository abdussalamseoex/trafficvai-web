<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Receipt #') . $invoice->order_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('client.invoices.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Invoices
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print / Save PDF
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg print:shadow-none print:border print:border-gray-200">
                <div class="p-10 divide-y divide-gray-200">
                    
                    <div class="flex justify-between items-start pb-8">
                        <div>
                            <h2 class="text-3xl font-black text-indigo-600 tracking-tighter">TrafficVai<span class="text-gray-900">SEO</span></h2>
                            <p class="text-sm text-gray-500 mt-1">Professional SEO Agency Services</p>
                        </div>
                        <div class="text-right">
                            <h3 class="text-xl font-bold text-gray-900">RECEIPT</h3>
                            <p class="text-sm font-medium text-gray-500 mt-1">#{{ $invoice->order_number }}</p>
                            <p class="text-sm text-gray-500">Date: {{ $invoice->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="py-8 grid grid-cols-2 gap-8">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Billed To</p>
                            <p class="font-bold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-gray-600 text-sm">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Payment Status</p>
                            <p class="font-bold text-gray-900 capitalize">{{ $invoice->status }}</p>
                            <p class="text-gray-600 text-sm">Method: Stripe</p>
                        </div>
                    </div>

                    <div class="py-8">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="pb-3 text-left">Description</th>
                                    <th class="pb-3 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr>
                                    <td class="py-4 text-gray-900">
                                        <div class="font-bold">
                                            @if($invoice->package_id)
                                                {{ $invoice->package->service->title ?? 'SEO Package' }} - {{ $invoice->package->name }}
                                            @elseif($invoice->guest_post_site_id)
                                                Guest Post Publication: {{ $invoice->guestPostSite->url }}
                                            @else
                                                Custom Service Order
                                            @endif
                                        </div>
                                        <div class="text-gray-500 mt-1 text-xs">
                                            @if($invoice->is_emergency)
                                                Includes Express Delivery Fee
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 text-right font-medium text-gray-900">
                                        <span class="price-convert" data-base-price="{{ $invoice->total_price }}">${{ number_format($invoice->total_price, 2) }}</span>
                                    </td>
                                </tr>
                                
                                @if($invoice->addons && count($invoice->addons) > 0)
                                    @foreach($invoice->addons as $addon)
                                        <tr>
                                            <td class="py-2 text-gray-600 pl-4 border-l-2 border-indigo-100">
                                                + Addon: {{ $addon->name }}
                                            </td>
                                            <td class="py-2 text-right text-gray-600">
                                                Included
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot class="border-t border-gray-200">
                                <tr>
                                    <td class="pt-6 font-bold text-gray-900 text-right pr-6">Total Paid</td>
                                    <td class="pt-6 font-bold text-indigo-600 text-xl text-right"><span class="price-convert" data-base-price="{{ $invoice->total_price }}">${{ number_format($invoice->total_price, 2) }}</span></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="pt-8 text-center text-sm text-gray-500">
                        <p>Thank you for choosing TrafficVai SEO!</p>
                        <p class="mt-1">If you have any questions about this receipt, please open a support ticket.</p>
                    </div>

                </div>
            </div>
            
            <style>
                @media print {
                    body {
                        background-color: white !important;
                    }
                    nav, header, button, .print\:hidden {
                        display: none !important;
                    }
                    main {
                        padding: 0 !important;
                        margin: 0 !important;
                    }
                    .max-w-4xl {
                        max-width: 100% !important;
                    }
                }
            </style>
        </div>
    </div>
</x-app-layout>
