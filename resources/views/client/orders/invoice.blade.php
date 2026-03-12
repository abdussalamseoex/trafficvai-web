<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->id }} - TrafficVai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .invoice-box { box-shadow: none; border: none; }
        }
    </style>
</head>
<body class="bg-gray-50 p-6 md:p-12">
    <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-3xl overflow-hidden invoice-box">
        <!-- Header -->
        <div class="bg-indigo-600 p-8 md:p-12 text-white flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h1 class="text-3xl font-black tracking-tighter uppercase mb-1">TrafficVai</h1>
                <p class="text-indigo-100 text-sm font-medium">Premium SEO & Growth Services</p>
            </div>
            <div class="mt-6 md:mt-0 text-right">
                <h2 class="text-4xl font-black">INVOICE</h2>
                <p class="text-indigo-100 font-bold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="p-8 md:p-12">
            <!-- Addresses -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Billed From</h3>
                    <p class="font-black text-gray-900 text-lg">TrafficVai Agency</p>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        House 12, Road 5, Block B<br>
                        Banani, Dhaka 1213<br>
                        Bangladesh<br>
                        contact@trafficvai.com
                    </p>
                </div>
                <div class="md:text-right">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Billed To</h3>
                    <p class="font-black text-gray-900 text-lg">{{ $order->user->name }}</p>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        {{ $order->user->email }}<br>
                        Date: {{ $order->created_at->format('M d, Y') }}<br>
                        Payment: {{ strtoupper(str_replace('_', ' ', $order->payment_method ?? 'Stripe')) }}
                    </p>
                </div>
            </div>

            <!-- Table -->
            <div class="mb-12">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-100 text-left">
                            <th class="py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Description</th>
                            <th class="py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Qty</th>
                            <th class="py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr>
                            <td class="py-6">
                                <p class="font-bold text-gray-900">
                                    {{ $order->package ? $order->package->service->name . ' - ' . $order->package->name : 'Guest Post Placement - ' . $order->guestPostSite->url }}
                                </p>
                                @if($order->is_emergency)
                                <span class="text-[10px] font-black text-red-500 uppercase tracking-tighter bg-red-50 px-2 py-0.5 rounded">Express Delivery Included</span>
                                @endif
                            </td>
                            <td class="py-6 text-center text-gray-600">1</td>
                            <td class="py-6 text-right font-black text-gray-900"><span class="price-convert" data-base-price="{{ $order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount }}">${{ number_format($order->subtotal_amount > 0 ? $order->subtotal_amount : $order->total_amount, 2) }}</span></td>
                        </tr>
                        @foreach($order->addons as $addon)
                        <tr>
                            <td class="py-6">
                                <p class="font-bold text-gray-600 text-sm">{{ $addon->name }}</p>
                            </td>
                            <td class="py-6 text-center text-xs text-gray-400">1</td>
                            <td class="py-6 text-right font-bold text-gray-600 text-sm"><span class="price-convert" data-base-price="{{ $addon->pivot->price }}">${{ number_format($addon->pivot->price, 2) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="flex flex-col items-end border-t-2 border-gray-50 pt-8">
                <div class="w-full md:w-64 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-bold text-gray-900"><span class="price-convert" data-base-price="{{ $order->subtotal_amount }}">${{ number_format($order->subtotal_amount, 2) }}</span></span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Discount @if($order->coupon) <span class="bg-green-100 text-green-800 text-[10px] px-2 py-0.5 rounded ml-1 uppercase font-bold tracking-wider">{{ $order->coupon->code }}</span> @endif</span>
                        <span>-<span class="price-convert" data-base-price="{{ $order->discount_amount }}">${{ number_format($order->discount_amount, 2) }}</span></span>
                    </div>
                    @endif
                    @if($order->wallet_amount > 0)
                    <div class="flex justify-between text-sm text-indigo-600">
                        <span>Paid via Wallet</span>
                        <span>-<span class="price-convert" data-base-price="{{ $order->wallet_amount }}">${{ number_format($order->wallet_amount, 2) }}</span></span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center bg-gray-900 text-white p-4 rounded-2xl mt-4">
                        <span class="text-xs font-bold uppercase tracking-widest">Total Paid</span>
                        <span class="text-xl font-black"><span class="price-convert" data-base-price="{{ $order->total_amount }}">${{ number_format($order->total_amount, 2) }}</span></span>
                    </div>
                </div>
            </div>

            <!-- Footer Notes -->
            <div class="mt-16 text-center">
                <p class="text-xs text-gray-400 font-medium italic">Thank you for choosing TrafficVai. Your growth is our priority.</p>
            </div>
        </div>

        <div class="bg-gray-50 p-6 flex justify-center gap-4 no-print">
            <button onclick="window.print()" class="bg-indigo-600 text-white px-8 py-3 rounded-2xl font-black shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition active:scale-95">
                Print / Save as PDF
            </button>
            <button onclick="window.close()" class="bg-white text-gray-900 border border-gray-200 px-8 py-3 rounded-2xl font-bold transition hover:bg-gray-100 active:scale-95">
                Close
            </button>
        </div>
    </div>
</body>
</html>
