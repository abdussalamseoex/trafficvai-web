<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-[#0A0D14] text-gray-900 dark:text-gray-100 py-12 relative overflow-hidden transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <!-- Header -->
            <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b border-gray-200 dark:border-gray-800/80 pb-8">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20">Core Automation</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">30-Day Point Validity</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">Traffic Points Store</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm sm:text-base">Purchase Traffic Points using your Main Account USD Balance to launch campaigns.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('client.traffic_campaign.builder', ['tab' => 'direct']) }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm shadow hover:opacity-95 transition">
                        Launch Campaign
                    </a>
                    <a href="{{ route('client.traffic_campaign.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-300 font-semibold text-sm border border-gray-300 dark:border-gray-800 transition">
                        My Campaigns
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-8 p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-bold text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 p-5 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-300 dark:border-red-800 text-red-800 dark:text-red-300 font-bold text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <span>{{ session('error') }}</span>
                    <a href="{{ route('client.payments.topup') }}" class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold text-xs hover:bg-red-700 transition shrink-0">
                        + Deposit USD to Main Account
                    </a>
                </div>
            @endif

            <!-- Balances Bar -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                <!-- Main Account Balance -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-md flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-1">Main Account USD Balance</span>
                        <div class="text-3xl font-black text-gray-900 dark:text-white">${{ number_format($mainBalance, 2) }}</div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Used to purchase traffic point packages</p>
                    </div>
                    <a href="{{ route('client.payments.topup') }}" class="px-5 py-3 rounded-2xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-extrabold text-xs shadow hover:opacity-90 transition">
                        + Deposit USD
                    </a>
                </div>

                <!-- Traffic Points Balance -->
                <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-orange-500/10 via-amber-500/10 to-transparent border border-orange-500/30 shadow-md flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-orange-600 dark:text-orange-400 block mb-1">Available Traffic Points</span>
                        <div class="text-3xl font-black text-orange-600 dark:text-orange-400">{{ number_format($pointsBalance) }} <span class="text-lg font-bold">Pts</span></div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Valid for 30 days from purchase</p>
                    </div>
                    <a href="{{ route('client.traffic_campaign.builder', ['tab' => 'direct']) }}" class="px-5 py-3 rounded-2xl bg-orange-500 text-white font-extrabold text-xs shadow hover:bg-orange-600 transition">
                        Use Points
                    </a>
                </div>
            </div>

            <!-- Package Selection Section -->
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-6">Select a Points Package</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <!-- Starter Pack -->
                <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-lg transition flex flex-col justify-between">
                    <div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">Starter Pack</span>
                        <div class="text-3xl font-black text-gray-900 dark:text-white mt-4">5,000 <span class="text-sm font-semibold text-gray-500">Pts</span></div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Ideal for testing direct or search campaigns.</p>
                        <div class="text-2xl font-black text-orange-600 dark:text-orange-400 mt-6">$5.00 <span class="text-xs font-normal text-gray-500">USD</span></div>
                    </div>

                    <form action="{{ route('client.traffic_campaign.purchase_points') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="package" value="starter">
                        <button type="submit" class="w-full py-3 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold text-xs hover:opacity-90 transition">
                            Buy with Main Balance
                        </button>
                    </form>
                </div>

                <!-- Growth Pack -->
                <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-lg transition flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300">Growth Pack</span>
                        <div class="text-3xl font-black text-gray-900 dark:text-white mt-4">15,000 <span class="text-sm font-semibold text-gray-500">Pts</span></div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">10% discount compared to base rate.</p>
                        <div class="text-2xl font-black text-orange-600 dark:text-orange-400 mt-6">$13.50 <span class="text-xs font-normal text-gray-500">USD</span></div>
                    </div>

                    <form action="{{ route('client.traffic_campaign.purchase_points') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="package" value="growth">
                        <button type="submit" class="w-full py-3 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold text-xs hover:opacity-90 transition">
                            Buy with Main Balance
                        </button>
                    </form>
                </div>

                <!-- Pro Pack -->
                <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border-2 border-orange-500 shadow-xl flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-3 right-3 bg-orange-500 text-white text-[10px] font-black uppercase px-2.5 py-1 rounded-full">Popular</div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-orange-100 dark:bg-orange-950 text-orange-800 dark:text-orange-300">Pro Pack</span>
                        <div class="text-3xl font-black text-gray-900 dark:text-white mt-4">35,000 <span class="text-sm font-semibold text-gray-500">Pts</span></div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">20% bonus points for continuous campaigns.</p>
                        <div class="text-2xl font-black text-orange-600 dark:text-orange-400 mt-6">$28.00 <span class="text-xs font-normal text-gray-500">USD</span></div>
                    </div>

                    <form action="{{ route('client.traffic_campaign.purchase_points') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="package" value="pro">
                        <button type="submit" class="w-full py-3 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs transition">
                            Buy with Main Balance
                        </button>
                    </form>
                </div>

                <!-- GOAT Scale Pack -->
                <div class="p-6 rounded-3xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-lg transition flex flex-col justify-between">
                    <div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300">GOAT Scale</span>
                        <div class="text-3xl font-black text-gray-900 dark:text-white mt-4">100,000 <span class="text-sm font-semibold text-gray-500">Pts</span></div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Maximum value for agency volume scaling.</p>
                        <div class="text-2xl font-black text-orange-600 dark:text-orange-400 mt-6">$70.00 <span class="text-xs font-normal text-gray-500">USD</span></div>
                    </div>

                    <form action="{{ route('client.traffic_campaign.purchase_points') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="package" value="scale">
                        <button type="submit" class="w-full py-3 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold text-xs hover:opacity-90 transition">
                            Buy with Main Balance
                        </button>
                    </form>
                </div>
            </div>

            <!-- Custom Points Purchase Section -->
            <div class="p-8 rounded-3xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-md">
                <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-2">Custom Points Purchase</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Enter any amount of points you need (Rate: $1.00 USD per 1,000 Points | Minimum 1,000 Points).</p>

                <form action="{{ route('client.traffic_campaign.purchase_points') }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
                    @csrf
                    <input type="hidden" name="package" value="custom">

                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Points Amount</label>
                        <input type="number" name="custom_points" id="customPointsInput" min="1000" step="1000" value="1000" required
                            class="w-full bg-gray-50 dark:bg-gray-950 border border-gray-300 dark:border-gray-800 rounded-xl px-4 py-3 text-gray-900 dark:text-white font-bold focus:border-brand">
                    </div>

                    <div class="sm:w-48 w-full">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Cost (USD)</label>
                        <div id="customCostDisplay" class="px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 font-black text-gray-900 dark:text-white text-base">
                            $1.00 USD
                        </div>
                    </div>

                    <button type="submit" class="px-8 py-3.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-sm transition shrink-0">
                        Purchase Custom Points
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const customInput = document.getElementById('customPointsInput');
            const costDisplay = document.getElementById('customCostDisplay');

            if (customInput && costDisplay) {
                customInput.addEventListener('input', function() {
                    const pts = parseInt(this.value) || 0;
                    const usd = (pts / 1000.0).toFixed(2);
                    costDisplay.innerText = '$' + usd + ' USD';
                });
            }
        });
    </script>
</x-app-layout>
