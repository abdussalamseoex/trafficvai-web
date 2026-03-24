<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Order #') }}{{ $order->id }}
            </h2>
            <a href="{{ route('admin.invoices.create', ['order_id' => $order->id]) }}" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-bold px-4 py-2 rounded-xl transition shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Create Renewal Invoice
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Order Client & Package Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 border-b">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Order Information</h3>
                        <p class="mb-2"><span class="font-semibold">Client:</span> {{ $order->user->name }} ({{ $order->user->email }})</p>
                        <p class="mb-2"><span class="font-semibold">Order Date:</span> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                        <p class="mb-2"><span class="font-semibold">Service:</span> {{ $order->package ? $order->package->service->name . ' - ' . $order->package->name : 'Guest Post Placement - ' . $order->guestPostSite->url }}</p>
                        <p class="mb-2"><span class="font-semibold">Subtotal:</span> ${{ number_format($order->subtotal_display, 2) }}</p>
                        @if($order->discount_amount > 0)
                        <p class="mb-2 flex items-center"><span class="font-semibold mr-1">Discount:</span> <span class="text-green-600 font-bold">-${{ number_format($order->discount_amount, 2) }}</span>
                            @if($order->coupon)
                                <span class="ml-2 text-[10px] bg-green-100 text-green-800 px-2 py-0.5 rounded uppercase font-bold tracking-wider">Promo: {{ $order->coupon->code }}</span>
                            @endif
                        </p>
                        @endif
                        <p class="mb-2"><span class="font-semibold">Total Paid:</span> ${{ number_format($order->total_paid_display, 2) }}</p>
                        @if($order->wallet_amount > 0)
                        <p class="mb-2 text-xs text-amber-600 font-medium italic">(Included Wallet: ${{ number_format($order->wallet_amount, 2) }})</p>
                        @endif
                        
                        @if($order->addons->count() > 0)
                        <div class="mt-4 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                            <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-2">Selected Addons</p>
                            <ul class="space-y-1">
                                @foreach($order->addons as $addon)
                                <li class="text-sm flex justify-between">
                                    <span class="text-gray-700">{{ $addon->name }}</span>
                                    <span class="font-semibold text-indigo-600">${{ number_format($addon->pivot->price, 2) }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <p class="mb-2">
                            <span class="font-semibold">Status:</span> 
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : ($order->status == 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ strtoupper($order->status) }}
                            </span>
                        </p>
                        <p class="mb-2 flex items-center gap-2">
                            <span class="font-semibold">Payment:</span> 
                            <span class="px-2 inline-flex text-xs leading-5 font-bold rounded bg-gray-100 text-gray-800 uppercase border border-gray-200">
                                {{ str_replace('_', ' ', $order->payment_method ?? 'Stripe') }}
                            </span>
                            @if($order->payment_status === 'paid')
                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-700 border border-green-200">PAID</span>
                            @elseif($order->payment_status === 'failed')
                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-700 border border-red-200">FAILED</span>
                            @elseif($order->payment_status === 'refunded')
                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-200 text-gray-700 border border-gray-300">REFUNDED</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200">PENDING</span>
                            @endif
                        </p>

                        @if($order->sender_number)
                        <div class="mt-4 p-4 bg-green-50 rounded-xl border border-green-200">
                            <h4 class="text-xs font-bold text-green-600 uppercase tracking-widest mb-2">Manual Payment Proof</h4>
                            <p class="text-sm mb-2"><span class="font-semibold text-gray-700">Sender Number:</span> <span class="font-mono text-green-700 bg-white px-2 py-0.5 rounded border border-green-100">{{ $order->sender_number }}</span></p>
                            @if($order->payment_proof)
                                <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 shadow-sm transition-all hover:shadow-md">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    View Proof Attachment
                                </a>
                            @else
                                <p class="text-xs text-gray-500 italic">No attachment uploaded</p>
                            @endif
                        </div>
                        @endif
                        @if($order->status == 'completed')
                            <div class="mt-4 p-3 rounded-lg border bg-green-50 border-green-200">
                                <p class="text-sm font-semibold flex items-center text-green-700">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Order Delivered
                                </p>
                            </div>
                        @elseif($order->expected_delivery_date)
                            <div class="mt-4 p-4 rounded-xl border bg-gray-50 border-gray-200 shadow-sm" x-data="countdownTimer('{{ $order->expected_delivery_date->toIso8601String() }}')">
                                <div class="flex flex-col md:flex-row md:items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold flex items-center text-gray-700" :class="status === 'overdue' ? 'text-red-700' : ''">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Expected Delivery: {{ $order->expected_delivery_date->format('M d, Y h:i A') }}
                                        </p>
                                        <button @click="$dispatch('open-extend-modal')" type="button" class="mt-2 text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center bg-indigo-50 px-2 py-1 rounded shadow-sm border border-indigo-100 transition">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                            Extend Delivery Time
                                        </button>
                                    </div>
                                    <div class="mt-2 md:mt-0">
                                        <template x-if="status === 'overdue'">
                                            <span class="bg-red-600 text-white px-2.5 py-1 rounded-md text-xs uppercase font-bold tracking-wider shadow-sm">Overdue</span>
                                        </template>
                                        <template x-if="status === 'running'">
                                            <span class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm font-black shadow-md tracking-wide flex items-center" x-text="timeLeft"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 p-3 rounded-lg border bg-gray-50 border-gray-200">
                                <p class="text-sm font-semibold text-gray-500 italic">Delivery timer starting soon...</p>
                            </div>
                        @endif
                        
                        @if($order->is_emergency)
                            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start shadow-sm">
                                <p class="text-sm font-bold text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Client requested Express Delivery
                                </p>
                            </div>
                        @endif

                        <!-- Extension History -->
                        @if($order->extensions->count() > 0)
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-bold text-gray-900 flex items-center mb-3">
                                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Delivery Extension History
                            </h4>
                            <div class="space-y-3">
                                @foreach($order->extensions as $ext)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 shadow-sm text-sm">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="font-bold text-indigo-700">+{{ $ext->added_days }} Day(s)</span>
                                        <span class="text-[11px] font-medium text-gray-500">{{ $ext->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                    <p class="text-gray-700 italic border-l-2 border-indigo-200 pl-2">"{{ $ext->reason }}"</p>
                                    <p class="text-[10px] text-gray-400 mt-2 text-right uppercase tracking-wider font-bold">- Extended by {{ $ext->admin->name ?? 'Admin' }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Submitted Requirements by Client -->
                    <div class="p-6 text-gray-900 bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Client Requirements</h3>
                        
                        @if($order->requirements->isEmpty() && !$order->guest_post_url)
                            <p class="text-sm text-yellow-600">The client has not yet submitted the required details for this order. It is currently placed on hold.</p>
                        @elseif($order->package)
                            <div class="space-y-4">
                                @foreach($order->requirements as $req)
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">{{ $req->serviceRequirement->name ?? 'Detail' }}</p>
                                    <div class="mt-1 p-3 bg-white border border-gray-200 rounded-md">
                                        @if($req->serviceRequirement->type == 'url')
                                            <a href="{{ $req->value }}" target="_blank" class="text-indigo-600 hover:underline">{{ $req->value }}</a>
                                        @else
                                            {!! nl2br(e($req->value)) !!}
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @elseif($order->guestPostSite)
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Target URL</p>
                                    <div class="mt-1 p-3 bg-white border border-gray-200 rounded-md">
                                        <a href="{{ $order->guest_post_url }}" target="_blank" class="text-indigo-600 hover:underline">{{ $order->guest_post_url }}</a>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Anchor Text</p>
                                    <div class="mt-1 p-3 bg-white border border-gray-200 rounded-md">
                                        {!! nl2br(e($order->guest_post_anchor)) !!}
                                    </div>
                                </div>
                                @if($order->service_tier === 'placement')
                                <div class="mt-8">
                                    <div class="flex items-center justify-between mb-4">
                                        <p class="text-base font-bold text-gray-900 border-l-4 border-indigo-500 pl-3">Submitted Article Content</p>
                                    </div>
                                    <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-gray-100/80 rounded-xl p-8 md:p-12 font-sans prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-a:text-indigo-600 hover:prose-a:text-indigo-500 min-h-[400px]">
                                        {!! $order->article_body !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Admin Action / Order Fulfillment panel -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Fulfill Order</h3>
                        
                        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <x-input-label for="status" :value="__('Update Order Status')" />
                                <select id="status" name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm">
                                    <option value="pending_payment" {{ $order->status == 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                                    <option value="pending_requirements" {{ $order->status == 'pending_requirements' ? 'selected' : '' }}>Pending Client Action</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed & Delivered</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="payment_status" :value="__('Update Payment Status')" />
                                <select id="payment_status" name="payment_status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm">
                                    <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_status')" class="mt-2" />
                            </div>

                            <div class="mb-6">
                                <x-input-label for="report_file_path" :value="__('Delivery Report URL / Proof of Work (Optional)')" />
                                <x-text-input id="report_file_path" class="block mt-1 w-full" type="text" name="report_file_path" :value="old('report_file_path', $order->report_file_path)" placeholder="e.g. Google Drive Link or Dashboard file url" />
                                <p class="text-xs text-gray-500 mt-1">If set, the client will be able to download/view the report after the order is completed.</p>
                                <x-input-error :messages="$errors->get('report_file_path')" class="mt-2" />
                            </div>

                            @if($order->guestPostSite)
                            <div class="mb-6">
                                <x-input-label for="published_url" :value="__('Live Published URL (For Guest Posts)')" />
                                <x-text-input id="published_url" class="block mt-1 w-full" type="url" name="published_url" :value="old('published_url', $order->published_url)" placeholder="https://published-article.com/link" />
                                <p class="text-xs text-gray-500 mt-1">The direct URL to the published guest post with the client's link.</p>
                                <x-input-error :messages="$errors->get('published_url')" class="mt-2" />
                            </div>
                            @endif

                            <div class="mb-6">
                                <x-input-label for="expiry_date" :value="__('Service Expiry / Renewal Date (Optional)')" />
                                <x-text-input id="expiry_date" class="block mt-1 w-full" type="date" name="expiry_date" :value="old('expiry_date', $order->expiry_date ? $order->expiry_date->format('Y-m-d') : '')" />
                                <p class="text-xs text-gray-500 mt-1">Set an expiry date to track when this order service needs to be renewed.</p>
                                <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end">
                                <x-primary-button>
                                    {{ __('Save Changes & Notify Client') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Messaging Component -->
            <x-order-messages :order="$order" />

            <!-- Advanced Floating Chat -->


        </div>
    </div>

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

    <!-- Extend Delivery Time Modal -->
    <div x-data="{ open: false }" @open-extend-modal.window="open = true" class="relative z-50">
        <div x-show="open" style="display: none;" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity z-40"></div>
        <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div @click.away="open = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200">
                    <form method="POST" action="{{ route('admin.orders.extend', $order) }}">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-xl font-bold leading-6 text-gray-900 mb-4 tracking-tight border-b pb-3">Extend Delivery Time</h3>
                            <div class="mb-5">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Days to Add</label>
                                <input type="number" name="added_days" min="1" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg font-mono ps-4" placeholder="e.g. 3">
                                <p class="text-xs text-gray-500 mt-1">This will increase the current countdown timer.</p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Detailed Reason / Note</label>
                                <textarea name="reason" required rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3" placeholder="Explain exclusively to the client why extra days are needed..."></textarea>
                                <p class="text-xs text-indigo-600 mt-2 font-medium bg-indigo-50 py-1.5 px-3 rounded-md flex gap-2 items-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    This note will be automatically emailed to the client. Keep it professional.
                                </p>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-200">
                            <button type="submit" class="inline-flex w-full justify-center rounded-lg border border-transparent bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-indigo-700 sm:ml-3 sm:w-auto transition">Confirm & Notify</button>
                            <button @click="open = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto transition">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
