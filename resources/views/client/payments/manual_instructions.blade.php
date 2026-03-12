<x-app-layout>
    @php
        $isMobileBanking = in_array($method, ['bkash', 'nagad', 'rocket']);
        $brandColor = match($method) {
            'bkash' => '#e2136e',
            'nagad' => '#ed1c24',
            'rocket' => '#8c348d',
            default => '#4f46e5'
        };
        $brandName = match($method) {
            'bkash' => 'bKash',
            'nagad' => 'Nagad',
            'rocket' => 'Rocket',
            default => strtoupper($method)
        };
        $currencySymbol = $isMobileBanking ? '৳' : '$';
        
        // Use realistic defaults based on the screenshots if not set in DB
        if (!isset($receiverNumber)) {
            $receiverNumber = \App\Models\Setting::get("gateway_{$method}_account_number", '01639467420');
            if($method === 'nagad' || $method === 'rocket') {
                $receiverNumber = \App\Models\Setting::get("gateway_{$method}_account_number", '01852744119');
            }
        }
        
        $exchangeRate = \App\Models\Setting::get('bdt_exchange_rate', 120);
        $displayAmount = $isMobileBanking ? ($amount * $exchangeRate) : $amount;
    @endphp

    <div class="py-12 min-h-screen flex justify-center pb-24" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23f3f4f6\' fill-opacity=\'0.4\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E');">
        <div class="max-w-[480px] w-full px-4">
            
            <!-- White Wrapper matching mockup -->
            <div x-data="{ lang: 'bn' }" class="bg-white rounded-xl shadow-md border border-gray-100 p-6 relative">
                
                <!-- Header Actions (Icons) -->
                <div class="flex items-center justify-between mb-8 text-gray-500">
                    <a href="{{ route('client.payments.topup') }}" class="hover:text-gray-800 transition w-8 h-8 flex items-center justify-center rounded-full border border-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    
                    <div class="flex items-center gap-3">
                        <button type="button" @click="lang = lang === 'bn' ? 'en' : 'bn'" class="hover:text-gray-800 transition" title="Translate / অনুবাদ করুন">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 15h4.498m-4.748 4h5"></path></svg>
                        </button>
                        <a href="{{ route('client.payments.topup') }}" class="hover:text-gray-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Header Logo -->
                <div class="flex justify-center mb-8 h-12">
                     @if(isset($gateway['logo']))
                        <img src="{{ $gateway['logo'] }}" alt="{{ $method }}" class="h-full object-contain">
                     @else
                        <div class="text-4xl font-black tracking-tighter uppercase" style="color: {{ $brandColor }}">{{ $method }}</div>
                     @endif
                </div>

                <!-- Invoice & Amount Card inside White wrapper -->
                <div class="flex gap-4 mb-6">
                    <div class="flex-1 bg-white p-4 rounded-xl border border-gray-100 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center flex-shrink-0 border border-gray-100 overflow-hidden">
                           <img src="https://ui-avatars.com/api/?name=TrafficVai&color=4f46e5&background=e0e7ff&font-size=0.4" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <div class="text-[14px] font-bold text-gray-700">TrafficVai</div>
                            <div class="text-[11px] text-gray-500 font-medium">
                                @if(isset($topup))
                                    ইনভয়েস আইডিঃ
                                @else
                                    অর্ডার আইডিঃ
                                @endif
                            </div>
                            <div class="text-[11px] text-gray-400 font-mono">
                                @if(isset($order))
                                    #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                @else
                                    {{ strtoupper($topup->id ?? 'XLVVB40Bhvw6NR7Do3Ou') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 bg-white px-6 rounded-xl border border-gray-100 flex items-center justify-center min-w-[120px]">
                        <div class="text-[22px] font-bold text-gray-700">{{ $currencySymbol }} {{ number_format($displayAmount, 0) }}</div>
                    </div>
                </div>

                <!-- Colored Main Card -->
                <div class="rounded-xl overflow-hidden shadow-sm" style="background-color: {{ $brandColor }}">
                    @php
                        $actionRoute = isset($order) ? route('client.orders.submit_proof', $order) : route('client.payments.topup.manual');
                    @endphp
                    <form id="manualPaymentForm" action="{{ $actionRoute }}" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $amount }}">
                        <input type="hidden" name="payment_method" value="{{ $method }}">
                        
                        @if(isset($topup))
                            <input type="hidden" name="topup_id" value="{{ $topup->id }}">
                        @endif

                        @php
                            $ussdInst = \App\Models\Setting::get("gateway_{$method}_ussd_instruction");
                            $actionInst = \App\Models\Setting::get("gateway_{$method}_action_instruction");
                            
                            if (!$ussdInst) {
                                if ($method === 'bkash') $ussdInst = '*247# ডায়াল করে আপনার BKASH মোবাইল মেনুতে যান অথবা BKASH অ্যাপে যান।';
                                elseif ($method === 'nagad') $ussdInst = '*167# ডায়াল করে আপনার NAGAD মোবাইল মেনুতে যান অথবা NAGAD অ্যাপে যান।';
                                elseif ($method === 'rocket') $ussdInst = '*322# ডায়াল করে আপনার ROCKET মোবাইল মেনুতে যান অথবা ROCKET অ্যাপে যান।';
                            }
                            if (!$actionInst) $actionInst = '"Send Money" -এ ক্লিক করুন।';
                        @endphp

                        <div class="text-center mb-3">
                            <h3 class="text-[15px] font-bold text-white">
                                <span x-show="lang === 'bn'">ট্রানজেকশন আইডি দিন</span>
                                <span x-show="lang === 'en'" style="display: none;">Enter Transaction ID</span>
                            </h3>
                        </div>

                        <!-- Transaction ID -->
                        <div class="mb-4">
                            <input type="text" name="transaction_id" required :placeholder="lang === 'bn' ? 'ট্রানজেকশন আইডি দিন' : 'Enter Transaction ID'" 
                                   class="w-full h-11 bg-white border-0 rounded text-gray-800 text-sm focus:ring-2 focus:ring-white/50 placeholder:text-gray-400 px-4">
                        </div>

                        <!-- Phone Number -->
                        <div class="text-center mb-3">
                            <h3 class="text-[15px] font-bold text-white">
                                <span x-show="lang === 'bn'">ফোন নম্বর লিখুন</span>
                                <span x-show="lang === 'en'" style="display: none;">Enter Phone Number</span>
                            </h3>
                        </div>
                        <div class="mb-6">
                            <input type="text" name="sender_number" required :placeholder="lang === 'bn' ? 'ফোন নম্বর লিখুন' : 'Enter Phone Number'" 
                                   class="w-full h-11 bg-white border-0 rounded text-gray-800 text-sm focus:ring-2 focus:ring-white/50 placeholder:text-gray-400 px-4">
                        </div>

                        <!-- Instruction List -->
                        <div class="text-[13px] font-medium text-white space-y-0">
                            @if(isset($instructions) && $instructions)
                                <div class="py-4 border-b border-white/10 text-white leading-relaxed mb-2 px-1">
                                    {!! nl2br(e($instructions)) !!}
                                </div>
                            @endif
                            
                            @if(in_array($method, ['bkash', 'nagad', 'rocket']))
                                <div class="flex gap-2 py-3 border-b border-white/10"><span class="mt-0.5">•</span> <span>{{ $ussdInst }}</span></div>
                                <div class="flex gap-2 py-3 border-b border-white/10"><span class="mt-0.5">•</span> <span>{{ $actionInst }}</span></div>
                            @endif
                            
                            <div class="flex items-center justify-between py-3 border-b border-white/10 w-full group">
                                <div class="flex gap-2 items-center">
                                    <span>•</span> 
                                    <span>
                                        <span x-show="lang === 'bn'">প্রাপক নম্বর হিসেবে এই নম্বরটি লিখুনঃ</span>
                                        <span x-show="lang === 'en'" style="display: none;">Recipient Number:</span>
                                        <span class="font-bold text-yellow-300">{{ $receiverNumber }}</span>
                                    </span>
                                </div>
                                <button type="button" @click="navigator.clipboard.writeText('{{ $receiverNumber }}'); alert(lang === 'bn' ? 'নম্বরটি কপি করা হয়েছে!' : 'Number Copied!')" class="px-2 py-1 bg-black/30 hover:bg-black/40 text-white rounded text-[11px] flex items-center gap-1.5 transition ml-2 border border-black/10 shadow-sm flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                    <span x-show="lang === 'bn'">কপি</span><span x-show="lang === 'en'" style="display: none;">Copy</span>
                                </button>
                            </div>

                            <div class="flex gap-2 py-3 border-b border-white/10"><span class="mt-0.5">•</span> 
                                <span>
                                    <span x-show="lang === 'bn'">টাকার পরিমাণঃ</span>
                                    <span x-show="lang === 'en'" style="display: none;">Amount:</span>
                                    <span class="font-bold text-yellow-300">{{ number_format($displayAmount, 0) }}</span>
                                </span>
                            </div>
                            <div class="flex gap-2 py-3 border-b border-white/10"><span class="mt-0.5">•</span> 
                                <span x-show="lang === 'bn'">নিশ্চিত করতে এখন আপনার {{ strtoupper($method) }} মোবাইল মেনু পিন লিখুন।</span>
                                <span x-show="lang === 'en'" style="display: none;">Confirm by entering your {{ strtoupper($method) }} PIN.</span>
                            </div>
                            <div class="flex gap-2 py-3 border-b border-white/10"><span class="mt-0.5">•</span> 
                                <span x-show="lang === 'bn'">সবকিছু ঠিক থাকলে, আপনি {{ strtoupper($method) }} থেকে একটি নিশ্চিতকরণ বার্তা পাবেন।</span>
                                <span x-show="lang === 'en'" style="display: none;">If everything is correct, you will receive a confirmation message from {{ strtoupper($method) }}.</span>
                            </div>
                            <div class="flex gap-2 py-3 pt-3"><span class="mt-0.5">•</span> 
                                <span>
                                    <span x-show="lang === 'bn'">এখন উপরের বক্সে আপনার <span class="text-yellow-300 font-bold">Transaction ID</span> দিন এবং নিচের <span class="text-yellow-300 font-bold">VERIFY</span> বাটনে ক্লিক করুন।</span>
                                    <span x-show="lang === 'en'" style="display: none;">Now enter your <span class="text-yellow-300 font-bold">Transaction ID</span> above and click <span class="text-yellow-300 font-bold">VERIFY</span>.</span>
                                </span>
                            </div>
                        </div>

                    </form>
                </div>
                
                <!-- Submit Button placed outside the colored box -->
                <div class="mt-5">
                    <button type="button" onclick="document.getElementById('manualPaymentForm').submit()" class="w-full text-white font-bold py-3.5 rounded-xl transition hover:opacity-90 active:scale-[0.98] tracking-widest text-[15px] shadow-sm"
                            style="background-color: {{ $brandColor }};">
                        VERIFY
                    </button>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
