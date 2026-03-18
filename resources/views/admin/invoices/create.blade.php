<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ isset($invoice) ? 'Edit Invoice #' . $invoice->invoice_number : 'Create Invoice' }}</h2>
            @isset($invoice)
            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-indigo-600 text-sm font-medium hover:underline">← Back to Invoice</a>
            @endisset
        </div>
    </x-slot>

    @php
        $initialItems = [];
        if (isset($invoice)) {
            $initialItems = $invoice->items->map(fn($i) => [
                'description' => $i->description, 
                'quantity' => $i->quantity, 
                'unit_price' => $i->unit_price
            ]);
        } elseif (isset($order)) {
            $description = '';
            if ($order->package) {
                $description = 'Renewal for ' . ($order->package->service->name ?? 'Service') . ' - ' . $order->package->name . ' (Order #' . $order->id . ')';
            } elseif ($order->guestPostSite) {
                $description = 'Renewal for Guest Post - ' . $order->guestPostSite->url . ' (Order #' . $order->id . ')';
            } else {
                $description = 'Renewal for Custom Order #' . $order->id;
            }
            
            // Robust price selection: Subtotal > Total > Package Price > 0
            $price = 0;
            if ($order->subtotal_amount > 0) {
                $price = $order->subtotal_amount;
            } elseif ($order->total_amount > 0) {
                $price = $order->total_amount;
            } elseif ($order->package && $order->package->price > 0) {
                $price = $order->package->price;
            }
            
            $initialItems = [[
                'description' => $description,
                'quantity' => 1,
                'unit_price' => (float)$price
            ]];
        }
    @endphp

    <div class="py-12" x-data='{
        items: @json($initialItems),
        currency: "{{ isset($invoice) ? $invoice->currency : "USD" }}",
        discountType: "{{ isset($invoice) ? $invoice->discount_type : "" }}",
        discountValue: {{ isset($invoice) ? ($invoice->discount_value ?? 0) : 0 }},
        taxRate: {{ isset($invoice) ? ($invoice->tax_rate ?? 0) : 0 }},

        addItem() { this.items.push({ description: "", quantity: 1, unit_price: 0 }); },
        removeItem(i) { this.items.splice(i, 1); },

        getSubtotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0)), 0);
        },
        getDiscount() {
            let sub = this.getSubtotal();
            if (this.discountType === "percentage") return sub * (parseFloat(this.discountValue || 0) / 100);
            if (this.discountType === "fixed") return Math.min(parseFloat(this.discountValue || 0), sub);
            return 0;
        },
        getTax() {
            return (this.getSubtotal() - this.getDiscount()) * (parseFloat(this.taxRate || 0) / 100);
        },
        getTotal() {
            return Math.max(0, this.getSubtotal() - this.getDiscount() + this.getTax());
        },
        fmt(n) { return parseFloat(n).toFixed(2); }
    }'>
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ isset($invoice) ? route('admin.invoices.update', $invoice) : route('admin.invoices.store') }}">
                @csrf
                @isset($invoice) @method('PUT') @endisset

                @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Main Content --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- Predefined Services (Quick Add) --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 relative">
                            <span class="absolute -top-3 left-4 bg-brand text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-sm">Quick Add</span>
                            <div class="flex flex-col sm:flex-row gap-4 items-end">
                                <div class="flex-1 w-full">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Predefined Service</label>
                                    <select id="predefined_service" class="w-full rounded-xl border-gray-300 text-sm focus:ring-brand focus:border-brand bg-white" 
                                            @change="
                                                let sel = $event.target.options[$event.target.selectedIndex];
                                                if(sel.value) {
                                                    items.push({
                                                        description: sel.dataset.desc,
                                                        quantity: 1,
                                                        unit_price: parseFloat(sel.dataset.price)
                                                    });
                                                    $event.target.value = '';
                                                }
                                            ">
                                        <option value="">Select a service to auto-fill...</option>
                                        @forelse($invoiceServices as $service)
                                            <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-desc="{{ $service->name }} - {{ $service->description }}">
                                                {{ $service->name }} (${{ number_format($service->price, 2) }})
                                            </option>
                                        @empty
                                            <option value="" disabled>No services found. Add them in "Service Management" → "Predefined Services"</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Line Items --}}
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-900">Line Items</h3>
                                <button type="button" @click="addItem()" class="text-sm text-brand font-bold hover:text-orange-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Item
                                </button>
                            </div>

                            <div class="hidden sm:grid grid-cols-12 gap-2 text-xs font-bold text-gray-400 uppercase mb-2 px-1">
                                <div class="col-span-6">Description</div>
                                <div class="col-span-2 text-right">Qty</div>
                                <div class="col-span-2 text-right">Unit Price</div>
                                <div class="col-span-2 text-right">Total</div>
                            </div>

                            <div class="space-y-3" id="items-container">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="grid grid-cols-12 gap-2 items-center group">
                                        <div class="col-span-12 sm:col-span-6">
                                            <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                                   placeholder="Service or item description..."
                                                   class="w-full text-sm rounded-xl border-gray-200 focus:ring-brand focus:border-brand" required>
                                        </div>
                                        <div class="col-span-4 sm:col-span-2">
                                            <input type="number" step="0.01" min="0.01" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                                   class="w-full text-sm rounded-xl border-gray-200 focus:ring-brand focus:border-brand text-right" required>
                                        </div>
                                        <div class="col-span-4 sm:col-span-2">
                                            <input type="number" step="0.01" min="0" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                                                   class="w-full text-sm rounded-xl border-gray-200 focus:ring-brand focus:border-brand text-right" required>
                                        </div>
                                        <div class="col-span-3 sm:col-span-1 text-right text-sm font-bold text-gray-700">
                                            $<span x-text="fmt(parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0))"></span>
                                        </div>
                                        <div class="col-span-1 text-right">
                                            <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-600 transition opacity-0 group-hover:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div x-show="items.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                No items yet. <button type="button" @click="addItem()" class="text-brand font-bold">Add one</button>.
                            </div>

                            {{-- Totals --}}
                            <div class="border-t border-gray-100 mt-6 pt-4 space-y-2 text-sm">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span class="font-medium" x-text="'$' + fmt(getSubtotal())"></span>
                                </div>
                                <div class="flex justify-between text-gray-600" x-show="getDiscount() > 0">
                                    <span>Discount</span>
                                    <span class="font-medium text-green-600" x-text="'- $' + fmt(getDiscount())"></span>
                                </div>
                                <div class="flex justify-between text-gray-600" x-show="taxRate > 0">
                                    <span>Tax (<span x-text="taxRate"></span>%)</span>
                                    <span class="font-medium" x-text="'$' + fmt(getTax())"></span>
                                </div>
                                <div class="flex justify-between text-lg font-black text-gray-900 border-t border-gray-200 pt-2 mt-2">
                                    <span>Total</span>
                                    <span x-text="currency + ' ' + fmt(getTotal())"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Notes & Terms --}}
                        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                            <h3 class="font-bold text-gray-900">Notes & Terms</h3>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Notes (visible to client)</label>
                                <textarea name="notes" rows="3" class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand" placeholder="Thank you for your business!">{{ isset($invoice) ? $invoice->notes : '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Terms & Conditions</label>
                                <textarea name="terms" rows="3" class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand" placeholder="Payment is due within 30 days...">{{ isset($invoice) ? $invoice->terms : '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="space-y-6">
                        {{-- Client & Invoice Details --}}
                        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                            <h3 class="font-bold text-gray-900">Invoice Details</h3>
                            
                            @if(isset($order))
                                <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 text-sm">
                                    <span class="font-bold text-indigo-800">Renewal Invoice</span><br>
                                    <span class="text-indigo-600">Linked to Order #{{ $order->id }}</span>
                                </div>
                                <input type="hidden" name="type" value="renewal">
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                            @else
                                <div x-data="{ type: '{{ isset($invoice) ? $invoice->type : 'custom' }}' }">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Invoice Type</label>
                                    <select name="type" x-model="type" class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand mb-4">
                                        <option value="custom">Custom Invoice</option>
                                        <option value="renewal">Renewal Invoice</option>
                                    </select>
                                    
                                    <div x-show="type === 'renewal'" class="mb-4">
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Order ID (optional)</label>
                                        <input type="number" name="order_id" value="{{ isset($invoice) ? $invoice->order_id : old('order_id') }}" class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand" placeholder="e.g. 1042">
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Client *</label>
                                <select name="user_id" required class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand">
                                    <option value="">Select a client...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" @selected(isset($invoice) ? $invoice->user_id == $client->id : (isset($order) ? $order->user_id == $client->id : old('user_id') == $client->id))>
                                            {{ $client->name }} ({{ $client->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                                <select name="status" class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand">
                                    @foreach(['draft','unpaid','paid','cancelled','overdue'] as $s)
                                        <option value="{{ $s }}" @selected(isset($invoice) ? $invoice->status === $s : (isset($order) ? $s === 'unpaid' : $s === 'draft'))>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Currency</label>
                                <select name="currency" x-model="currency" class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand">
                                    @foreach(['USD','EUR','GBP','BDT','AUD','CAD'] as $cur)
                                        <option value="{{ $cur }}" @selected(isset($invoice) ? $invoice->currency === $cur : $cur === 'USD')>{{ $cur }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Due Date</label>
                                <input type="date" name="due_date" value="{{ isset($invoice) && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : (isset($order) && $order->expiry_date ? $order->expiry_date->format('Y-m-d') : '') }}"
                                       class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand">
                            </div>
                        </div>

                        {{-- Discount & Tax --}}
                        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                            <h3 class="font-bold text-gray-900">Discount & Tax</h3>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Discount Type</label>
                                <select name="discount_type" x-model="discountType" class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand">
                                    <option value="">No Discount</option>
                                    <option value="percentage" @selected(isset($invoice) && $invoice->discount_type === 'percentage')>Percentage (%)</option>
                                    <option value="fixed" @selected(isset($invoice) && $invoice->discount_type === 'fixed')>Fixed Amount ($)</option>
                                </select>
                            </div>
                            <div x-show="discountType">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Discount Value</label>
                                <input type="number" step="0.01" min="0" name="discount_value" x-model="discountValue"
                                       class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tax Rate (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="tax_rate" x-model="taxRate"
                                       class="w-full rounded-xl border-gray-200 text-sm focus:ring-brand focus:border-brand">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-brand hover:bg-orange-600 text-white font-black py-4 rounded-2xl transition shadow-lg shadow-orange-500/20 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ isset($invoice) ? 'Save Changes' : 'Create Invoice' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
