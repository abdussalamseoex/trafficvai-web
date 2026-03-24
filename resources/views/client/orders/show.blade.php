<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order #') }}{{ $order->id }} - {{ $order->package ? $order->package->service->name : 'Guest Post Placement' }}
            </h2>
            <a href="{{ route('client.orders.invoice', $order) }}" target="_blank" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100 shadow-sm transition hover:shadow-md">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2H7a2 2 0 00-2 2v4m14 0h2"></path></svg>
                Print Invoice
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <!-- Order Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Order Summary</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Order Date</p>
                            <p class="text-lg text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ $order->package ? 'Package' : 'Target Site' }}</p>
                            <p class="text-lg text-gray-900 flex items-center">
                                {{ $order->package ? $order->package->name : $order->guestPostSite->url }}
                                @if($order->is_emergency)
                                    <span class="ml-3 inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200 uppercase tracking-wider">
                                        🚀 Express
                                    </span>
                                @else
                                    <span class="ml-3 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-wider">
                                        Standard
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Subtotal</p>
                            <p class="text-lg text-gray-900"><span class="price-convert" data-base-price="{{ $order->subtotal_display }}">${{ number_format($order->subtotal_display, 2) }}</span></p>
                        </div>
                        @if($order->discount_amount > 0)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Discount Applied</p>
                            <p class="text-lg font-medium text-green-600 flex items-center">
                                -<span class="price-convert" data-base-price="{{ $order->discount_amount }}">${{ number_format($order->discount_amount, 2) }}</span>
                                @if($order->coupon)
                                    <span class="text-[10px] ml-2 bg-green-100 text-green-800 px-2 py-0.5 rounded uppercase font-bold tracking-wider" title="Promo Code Used">{{ $order->coupon->code }}</span>
                                @endif
                            </p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Paid</p>
                            <p class="text-lg text-gray-900 font-bold"><span class="price-convert" data-base-price="{{ $order->total_paid_display }}">${{ number_format($order->total_paid_display, 2) }}</span></p>
                            @if($order->wallet_amount > 0)
                            <p class="text-[10px] text-amber-600 font-bold italic mt-1 uppercase tracking-wider">Includes ${{ number_format($order->wallet_amount, 2) }} Wallet</p>
                            @endif
                        </div>
                        
                        @if($order->addons->count() > 0)
                        <div class="col-span-2 mt-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Selected Addons</p>
                            <div class="space-y-3">
                                @foreach($order->addons as $addon)
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="font-medium text-gray-700">{{ $addon->name }}</span>
                                    </div>
                                    <span class="text-gray-500"><span class="price-convert" data-base-price="{{ $addon->pivot->price }}">${{ number_format($addon->pivot->price, 2) }}</span></span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div>
                            <p class="text-sm font-medium text-gray-500">Payment Method</p>
                            <p class="text-lg text-gray-900 capitalize font-medium">{{ str_replace('_', ' ', $order->payment_method ?? 'Stripe') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Payment Status</p>
                            @if($order->payment_status === 'paid')
                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-600 border border-green-200 mt-1 uppercase tracking-wider">Paid</span>
                            @elseif($order->payment_status === 'failed')
                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-600 border border-red-200 mt-1 uppercase tracking-wider">Failed</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200 mt-1 uppercase tracking-wider">Pending</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Order Status</p>
                            @if($order->status == 'pending_payment')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Awaiting Payment Approval
                                </span>
                            @elseif($order->status == 'pending_requirements')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Action Required (Awaiting Details)
                                </span>
                            @elseif($order->status == 'processing')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Processing
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Expected Delivery</p>
                            @if($order->status == 'completed')
                                <p class="text-lg font-bold text-green-600 mt-1">Delivered</p>
                            @elseif($order->expected_delivery_date)
                                <div x-data="countdownTimer('{{ $order->expected_delivery_date->toIso8601String() }}')" class="mt-1">
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $order->expected_delivery_date->format('M d, Y g:i A') }}
                                    </p>
                                    <template x-if="status === 'overdue'">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-red-100 text-red-800 border border-red-200 mt-1">
                                            OVERDUE
                                        </span>
                                    </template>
                                    <template x-if="status === 'running'">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 mt-1 shadow-sm" x-text="timeLeft">
                                        </span>
                                    </template>
                                </div>
                            @elseif(in_array($order->status, ['pending_requirements', 'pending_payment']))
                                <p class="text-sm text-gray-500 mt-1 italic disabled">Calculated upon requirements submission</p>
                            @else
                                <p class="text-lg text-gray-900 mt-1">Not Set</p>
                            @endif
                        </div>
                        @if($order->status == 'completed' && $order->report_file_path)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Final Report</p>
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 font-medium">Download Delivery Report</a>
                        </div>
                        @elseif($order->status == 'completed' && $order->published_url)
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Live Delivery Link</p>
                            <a href="{{ $order->published_url }}" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-bold mt-1 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                View Published Detail
                            </a>
                        </div>
                        @endif

                        <!-- Extension History -->
                        @if($order->extensions->count() > 0)
                        <div class="col-span-1 md:col-span-2 mt-6 border-t border-gray-100 pt-5">
                            <div class="bg-indigo-50/50 rounded-xl border border-indigo-100 p-5">
                                <h4 class="text-xs font-black text-indigo-900 flex items-center mb-4 uppercase tracking-widest">
                                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Delivery Time Updates & Admin Notes
                                </h4>
                                <div class="space-y-4">
                                    @foreach($order->extensions as $ext)
                                    <div class="bg-white border text-left border-indigo-100/50 rounded-lg p-4 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] relative overflow-hidden">
                                        <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
                                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-2 pl-3 gap-2">
                                            <span class="font-black text-indigo-700">Delivery Extended by {{ $ext->added_days }} Day(s)</span>
                                            <span class="text-xs font-bold text-indigo-500 whitespace-nowrap bg-indigo-50 px-2.5 py-1 rounded shadow-sm border border-indigo-100/50">{{ $ext->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="pl-3">
                                            <p class="text-sm text-gray-700 leading-relaxed italic">"{{ $ext->reason }}"</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

            @if($order->payment_method === 'bank_transfer' && $order->payment_status !== 'paid')
            <div class="bg-indigo-50 border border-indigo-200 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 text-indigo-900">
                    <h3 class="text-lg font-bold mb-3 flex items-center text-indigo-800">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Manual Bank Transfer Instructions
                    </h3>
                    <p class="text-sm mb-4 leading-relaxed">Your order has been received, but we are awaiting payment. Please transfer the total amount of <strong class="text-xl bg-indigo-100 px-2 py-0.5 rounded text-indigo-700 font-extrabold"><span class="price-convert" data-base-price="{{ $order->total_amount }}">${{ number_format($order->total_amount, 2) }}</span></strong> to the following bank account:</p>
                    <div class="bg-white p-5 rounded-lg border border-indigo-100 font-mono text-sm space-y-3 shadow-inner">
                        <p class="flex justify-between border-b border-gray-100 pb-2"><strong class="text-gray-500">Bank Name:</strong> <span class="text-gray-900 font-bold">{{ \App\Models\Setting::get('bank_name', 'Standard Chartered Bank') }}</span></p>
                        <p class="flex justify-between border-b border-gray-100 pb-2"><strong class="text-gray-500">Account Name:</strong> <span class="text-gray-900 font-bold">{{ \App\Models\Setting::get('bank_account_name', 'TrafficVai Solutions LLC') }}</span></p>
                        <p class="flex justify-between border-b border-gray-100 pb-2"><strong class="text-gray-500">Account Number:</strong> <span class="text-gray-900 font-bold tracking-widest">{{ \App\Models\Setting::get('bank_account_number', '394857293049') }}</span></p>
                        <p class="flex justify-between border-b border-gray-100 pb-2"><strong class="text-gray-500">Routing / SWIFT:</strong> <span class="text-gray-900 font-bold">{{ \App\Models\Setting::get('bank_routing_swift', 'SCBKUS33XXX') }}</span></p>
                        <p class="pt-2 text-indigo-600 font-black mt-2 text-center text-base"><span class="font-normal text-indigo-400 mr-2 text-sm uppercase tracking-widest">Reference / Note:</span> {{ \App\Models\Setting::get('bank_reference_prefix', 'ORDER-') }}{{ $order->id }}</p>
                    </div>
                    <div class="flex items-start mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <svg class="w-6 h-6 text-yellow-600 mr-3 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm text-yellow-800 font-medium"><strong>Important:</strong> {{ \App\Models\Setting::get('bank_transfer_instructions', 'After making the transfer, please send a message via the Order Messaging system below. Include your transfer receipt or reference number so our team can verify your payment and activate your order.') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if(in_array($order->payment_method, ['bkash', 'nagad', 'rocket']) && $order->payment_status !== 'paid')
            @php
                $bdGateways = config('payment_gateways.bangladesh');
                $gatewayInfo = $bdGateways[$order->payment_method] ?? null;
                $hasSubmittedProof = $order->payment_proof || $order->transaction_id || $order->sender_number;
            @endphp
            
            @if($gatewayInfo)
                @if(!$hasSubmittedProof)
                <div class="bg-green-50 border border-green-200 overflow-hidden shadow-sm sm:rounded-xl">
                    <div class="p-6 text-green-900">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-black flex items-center text-green-800">
                                @if($gatewayInfo['logo'])
                                    <img src="{{ $gatewayInfo['logo'] }}" alt="{{ $gatewayInfo['name'] }}" class="h-6 mr-3 rounded">
                                @endif
                                {{ $gatewayInfo['name'] }} Payment Instructions
                            </h3>
                        </div>
                        <p class="text-sm mb-4 leading-relaxed">Please send <strong class="text-lg bg-green-100 px-2 py-0.5 rounded text-green-800 font-extrabold"><span class="price-convert" data-base-price="{{ $order->total_amount }}">${{ number_format($order->total_amount, 2) }}</span></strong> to the following number and submit your proof below.</p>
                        
                        <div class="bg-white p-5 rounded-2xl border border-green-100 text-sm space-y-3 shadow-sm">
                            <p class="flex justify-between border-b border-gray-50 pb-2"><strong class="text-gray-500 uppercase tracking-widest text-xs">Number:</strong> <span class="text-gray-900 font-black text-lg tracking-widest">{{ \App\Models\Setting::get("gateway_{$order->payment_method}_account_number") }}</span></p>
                            <p class="flex justify-between border-b border-gray-50 pb-2"><strong class="text-gray-500 uppercase tracking-widest text-xs">Type:</strong> <span class="text-gray-900 font-bold">{{ \App\Models\Setting::get("gateway_{$order->payment_method}_account_type") }}</span></p>
                            
                            @if(\App\Models\Setting::get("gateway_{$order->payment_method}_instructions"))
                                <div class="pt-2">
                                    <strong class="text-gray-500 uppercase tracking-widest text-xs block mb-1">Instructions:</strong>
                                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-100">{{ \App\Models\Setting::get("gateway_{$order->payment_method}_instructions") }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 bg-white p-6 rounded-2xl border border-green-200">
                            <h4 class="font-bold text-gray-900 mb-4 border-b pb-2">Submit Payment Details</h4>
                            <form action="{{ route('client.orders.submit_proof', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="transaction_id" value="Transaction ID (Optional)" />
                                    <x-text-input id="transaction_id" name="transaction_id" type="text" class="mt-1 block w-full focus:ring-green-500 focus:border-green-500" placeholder="e.g., TXN12345678" />
                                </div>
                                <div>
                                    <x-input-label for="sender_number" value="Sender Number" />
                                    <x-text-input id="sender_number" name="sender_number" type="text" class="mt-1 block w-full focus:ring-green-500 focus:border-green-500" required placeholder="e.g., 017XXXXXXXX" />
                                    <x-input-error class="mt-2" :messages="$errors->get('sender_number')" />
                                </div>
                                
                                <div>
                                    <x-input-label for="payment_proof" value="Payment Screenshot (Optional)" />
                                    <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 mt-1" id="payment_proof" name="payment_proof" type="file" accept="image/*,.pdf">
                                    <x-input-error class="mt-2" :messages="$errors->get('payment_proof')" />
                                </div>

                                <div class="flex justify-end pt-2">
                                    <x-primary-button class="bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900">
                                        Submit Verification
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-yellow-50 border border-yellow-200 overflow-hidden shadow-sm sm:rounded-xl">
                    <div class="p-6 text-yellow-900 flex items-start">
                        <svg class="w-8 h-8 text-yellow-500 mr-4 shrink-0 bg-yellow-100 p-1.5 rounded-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h3 class="text-lg font-black text-yellow-800">Payment Verification Pending</h3>
                            <p class="text-sm text-yellow-700 mt-2 leading-relaxed">
                                You submitted the payment details from <span class="font-bold border-b border-yellow-300">{{ $order->sender_number }}</span>
                                @if($order->transaction_id)
                                    with Transaction ID: <span class="font-mono bg-yellow-100 px-1 rounded">{{ $order->transaction_id }}</span>
                                @endif. 
                                Our team is verifying the payment and will activate your order shortly.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            @endif
            @endif

            <!-- Dynamic Form submission module for Link Building / SEO process -->
            @if(in_array($order->status, ['pending_requirements', 'pending_payment']) && $order->requirements->count() == 0 && empty($order->guest_post_url))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-2 border-b pb-2 text-red-600">Action Required: Submit Order Details</h3>
                    <p class="text-sm text-gray-500 mb-6">Please provide the following information so we can start processing your order.</p>

                    <form method="POST" action="{{ route('client.orders.update', $order) }}">
                        @csrf
                        @method('PUT')

                        @if($order->package)
                            @foreach($order->package->service->requirements as $req)
                            <div class="mb-4">
                                <x-input-label :for="'req_'.$req->id">
                                    {{ $req->name }} @if($req->is_required) <span class="text-red-500">*</span> @endif
                                </x-input-label>
                                
                                @if($req->type == 'textarea')
                                    <textarea id="req_{{ $req->id }}" name="requirements[{{ $req->id }}]" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3" {{ $req->is_required ? 'required' : '' }}>{{ old('requirements.'.$req->id) }}</textarea>
                                @else
                                    <x-text-input id="req_{{ $req->id }}" class="block mt-1 w-full" :type="$req->type == 'url' ? 'url' : 'text'" name="requirements[{{ $req->id }}]" :value="old('requirements.'.$req->id)" :required="$req->is_required" />
                                @endif
                                <x-input-error :messages="$errors->get('requirements.'.$req->id)" class="mt-2" />
                            </div>
                            @endforeach
                        @elseif($order->guestPostSite)
                            <div class="mb-4">
                                <x-input-label for="guest_post_url">
                                    Target URL to Promote <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input id="guest_post_url" type="url" name="guest_post_url" class="block mt-1 w-full" :value="old('guest_post_url')" required placeholder="https://yourwebsite.com/article" />
                                <x-input-error :messages="$errors->get('guest_post_url')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="guest_post_anchor">
                                    Preferred Anchor Text <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input id="guest_post_anchor" type="text" name="guest_post_anchor" class="block mt-1 w-full" :value="old('guest_post_anchor')" required placeholder="e.g. best seo tools" />
                                <x-input-error :messages="$errors->get('guest_post_anchor')" class="mt-2" />
                            </div>

                            @if($order->service_tier === 'placement')
                            <div class="mb-4">
                                <x-input-label for="article_body">
                                    Article Content <span class="text-red-500">*</span>
                                </x-input-label>
                                <textarea id="article_body" name="article_body" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="10" placeholder="Paste your article content here...">{{ old('article_body') }}</textarea>
                                <x-input-error :messages="$errors->get('article_body')" class="mt-2" />
                            </div>
                            @endif
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Submit Requirements & Start Order') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
            @elseif($order->requirements->count() > 0 || $order->guest_post_url)
            <!-- Show Submitted Information -->
            <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Submitted Information</h3>
                    <div class="grid grid-cols-1 gap-y-4">
                        @if($order->package)
                            @foreach($order->requirements as $submittedReq)
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ $submittedReq->serviceRequirement->name ?? 'Detail' }}</p>
                                <p class="text-md text-gray-900 bg-white p-2 rounded border border-gray-200">{{ $submittedReq->value }}</p>
                            </div>
                            @endforeach
                        @elseif($order->guestPostSite)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Target URL</p>
                                <p class="text-md text-gray-900 bg-white p-2 rounded border border-gray-200"><a href="{{ $order->guest_post_url }}" target="_blank" class="text-indigo-600 underline">{{ $order->guest_post_url }}</a></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Anchor Text</p>
                                <p class="text-md text-gray-900 bg-white p-2 rounded border border-gray-200">{{ $order->guest_post_anchor }}</p>
                            </div>
                            @if($order->service_tier === 'placement')
                            <div class="col-span-1 mt-6">
                                <p class="text-base font-bold text-gray-900 border-l-4 border-indigo-500 pl-3 mb-4">Submitted Article Content</p>
                                <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-gray-100/80 rounded-xl p-8 md:p-12 font-sans prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-a:text-indigo-600 hover:prose-a:text-indigo-500">
                                    {!! $order->article_body !!}
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Order Messaging Component -->
            <x-order-messages :order="$order" />

            <!-- Advanced Floating Chat -->


        </div>
    </div>

    @if(in_array($order->status, ['pending_requirements', 'pending_payment']) && $order->guestPostSite && $order->service_tier === 'placement')
        <!-- TinyMCE Editor Script -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                tinymce.init({
                    selector: '#article_body',
                    height: 500,
                    menubar: true,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | ' +
                    'bold italic textcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'link image | removeformat | code | help',
                    content_style: 'body { font-family:Inter,Helvetica,Arial,sans-serif; font-size:16px }',
                    setup: function (editor) {
                        editor.on('change', function () {
                            editor.save(); // ensure textarea is updated
                        });
                    }
                });
            });
        </script>
    @endif

    <script>
        function countdownTimer(endDateStr) {
            return {
                endDate: new Date(endDateStr).getTime(),
                timeLeft: '',
                status: 'running',
                
                init() {
                    this.updateTimer();
                    setInterval(() => this.updateTimer(), 1000);
                },
                
                updateTimer() {
                    const now = new Date().getTime();
                    const distance = this.endDate - now;
                    
                    if (distance < 0) {
                        this.status = 'overdue';
                        this.timeLeft = 'OVERDUE';
                        return;
                    }
                    
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    
                    this.timeLeft = `⏱️ ${days}d ${hours}h ${minutes}m ${seconds}s`;
                }
            };
        }
    </script>
</x-app-layout>
