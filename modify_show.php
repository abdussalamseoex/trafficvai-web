<?php
$file = __DIR__ . '/resources/views/services/show.blade.php';
$content = file_get_contents($file);

// 1. Add delivery option state and package data mapping to Alpine store
$replacementHead = <<<EOT
                selectedPackageId: {{ \$service->packages->sortBy('price')->values()->first()->id ?? 'null' }},
                selectedPackagePrice: {{ \$service->packages->sortBy('price')->values()->first()->price ?? 0 }},
                deliveryOption: 'standard', // 'standard' or 'express'
                packageData: {
                    @foreach (\$service->packages as \$package)
                    {{ \$package->id }}: {
                        price: {{ \$package->price }},
                        turnaround: {{ \$package->turnaround_time_days ?? 'null' }},
                        express_turnaround: {{ \$package->express_turnaround_time_days ?? 'null' }},
                        emergency_fee: {{ \$package->emergency_fee ?? 0 }}
                    },
                    @endforeach
                },
                walletBalance: {{ auth()->user()->balance ?? 0 }},
EOT;

$content = str_replace(
    "                selectedPackageId: {{ \$service->packages->sortBy('price')->values()->first()->id ?? 'null' }},\n                selectedPackagePrice: {{ \$service->packages->sortBy('price')->values()->first()->price ?? 0 }},\n                walletBalance: {{ auth()->user()->balance ?? 0 }},",
    $replacementHead,
    $content
);

// 2. Update toggleAddon to reset delivery if package changes... wait, selectedPackageId is what changes package.
// We must update selectedPackagePrice AND deliveryOption when changing package.
$replacementPackageClick = <<<EOT
                        @click="selectedPackageId = {{ \$package->id }}; selectedPackagePrice = {{ \$package->price }}; deliveryOption = 'standard';"
EOT;

$content = str_replace(
    "@click=\"selectedPackageId = {{ \$package->id }}; selectedPackagePrice = {{ \$package->price }}\"",
    $replacementPackageClick,
    $content
);

// 3. Update getSubtotal() to include emergency fee
$replacementSubtotal = <<<EOT
                getSubtotal() {
                    let addonsTotal = this.selectedAddons.reduce((sum, id) => sum + (this.addonPrices[id] || 0), 0);
                    let emergencyFee = 0;
                    if (this.deliveryOption === 'express' && this.selectedPackageId && this.packageData[this.selectedPackageId]) {
                        emergencyFee = parseFloat(this.packageData[this.selectedPackageId].emergency_fee) || 0;
                    }
                    return parseFloat(this.selectedPackagePrice) + addonsTotal + emergencyFee;
                },
EOT;

$content = str_replace(
    "                getSubtotal() {\n                    let addonsTotal = this.selectedAddons.reduce((sum, id) => sum + (this.addonPrices[id] || 0), 0);\n                    return parseFloat(this.selectedPackagePrice) + addonsTotal;\n                },",
    $replacementSubtotal,
    $content
);

// 4. Update the Bottom Bar (for simple services) payment/checkout form with hidden input
$replacementForm1 = <<<EOT
                    @auth
                    <form method="POST" :action="'/services/' + selectedPackageId + '/checkout'" class="w-full md:w-auto">
                        @csrf
                        <input type="hidden" name="is_emergency" :value="deliveryOption === 'express' ? 'express' : '0'">
                        <input type="hidden" name="coupon_code" :value="couponApplied ? couponCode : ''">
EOT;

$content = str_replace(
    "                    @auth\n                    <form method=\"POST\" :action=\"'/services/' + selectedPackageId + '/checkout'\" class=\"w-full md:w-auto\">\n                        @csrf\n                        <input type=\"hidden\" name=\"coupon_code\" :value=\"couponApplied ? couponCode : ''\">",
    $replacementForm1,
    $content
);

// 5. Update the form inside the Addons section
$replacementForm2 = <<<EOT
                            @auth
                            <form method="POST" :action="'/services/' + selectedPackageId + '/checkout'">
                                @csrf
                                <input type="hidden" name="is_emergency" :value="deliveryOption === 'express' ? 'express' : '0'">
                                <template x-for="addonId in selectedAddons">
EOT;

$content = str_replace(
    "                            @auth\n                            <form method=\"POST\" :action=\"'/services/' + selectedPackageId + '/checkout'\">\n                                @csrf\n                                <template x-for=\"addonId in selectedAddons\">",
    $replacementForm2,
    $content
);

// 6. Inject Delivery Options UI right before 'Select Payment Method' in Addons section
$deliveryUI = <<<EOT
                    <!-- Delivery Option Section -->
                    <div class="mb-8 border-t border-gray-800 pt-8" x-show="selectedPackageId">
                        <h3 class="text-white font-bold mb-4">Select Delivery Speed</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Standard Delivery -->
                            <label class="cursor-pointer group flex items-start p-5 rounded-2xl border transition-all duration-300"
                                :class="deliveryOption === 'standard' ? 'bg-indigo-600/20 border-indigo-500/50' : 'bg-gray-800/40 border-gray-700 hover:border-gray-600'">
                                <input type="radio" name="delivery_speed" value="standard" class="sr-only" x-model="deliveryOption">
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors mr-4 mt-0.5 shrink-0" 
                                    :class="deliveryOption === 'standard' ? 'bg-indigo-500 border-indigo-500' : 'border-gray-600'">
                                    <div class="w-2.5 h-2.5 bg-white rounded-full" x-show="deliveryOption === 'standard'"></div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-white font-bold mb-1">Standard Delivery</h4>
                                    <p class="text-gray-400 text-xs">
                                        Estimated delivery in <span class="text-indigo-400 font-bold" x-text="packageData[selectedPackageId]?.turnaround || '...' "></span> days
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <span class="text-gray-400 text-sm font-medium">Included</span>
                                </div>
                            </label>

                            <!-- Express Delivery -->
                            <label x-show="packageData[selectedPackageId]?.express_turnaround" style="display: none;" 
                                class="cursor-pointer group flex items-start p-5 rounded-2xl border transition-all duration-300"
                                :class="deliveryOption === 'express' ? 'bg-indigo-600/20 border-indigo-500/50' : 'bg-gray-800/40 border-gray-700 hover:border-gray-600'">
                                <input type="radio" name="delivery_speed" value="express" class="sr-only" x-model="deliveryOption">
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors mr-4 mt-0.5 shrink-0" 
                                    :class="deliveryOption === 'express' ? 'bg-indigo-500 border-indigo-500' : 'border-gray-600'">
                                    <div class="w-2.5 h-2.5 bg-white rounded-full" x-show="deliveryOption === 'express'"></div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="text-white font-bold">Express Delivery</h4>
                                        <span class="bg-[#E8470A] text-white text-[9px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">Fast</span>
                                    </div>
                                    <p class="text-gray-400 text-xs">
                                        Estimated delivery in <span class="text-[#E8470A] font-bold" x-text="packageData[selectedPackageId]?.express_turnaround"></span> days
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <span class="text-[#E8470A] font-bold">+<span x-text="packageData[selectedPackageId]?.emergency_fee"></span></span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-8 border-t border-gray-800 pt-8" x-show="selectedPackageId">
EOT;

$content = str_replace(
    "                    <div class=\"mb-8 border-t border-gray-800 pt-8\" x-show=\"selectedPackageId\">\n                        <h3 class=\"text-white font-bold mb-4\">Select Payment Method</h3>",
    $deliveryUI . "\n                        <h3 class=\"text-white font-bold mb-4\">Select Payment Method</h3>",
    $content
);

// 7. Inject Delivery Options UI right before 'Select Payment Method' in Simple Bottom Bar section
$deliveryUI2 = <<<EOT
                    <!-- Delivery Option Section for Simple Packages -->
                    <div class="border-b border-gray-100 pb-6 w-full" x-show="selectedPackageId && packageData[selectedPackageId]?.express_turnaround">
                        <h3 class="text-gray-900 font-bold mb-4">Select Delivery Speed</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Standard -->
                            <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 transition"
                                :class="deliveryOption === 'standard' ? 'border-indigo-500 ring-2 ring-indigo-50 bg-indigo-50/30' : 'border-gray-200 hover:border-gray-300 bg-white'">
                                <input type="radio" value="standard" x-model="deliveryOption" class="sr-only">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5" :class="deliveryOption === 'standard' ? 'border-indigo-500' : 'border-gray-300'">
                                    <div class="w-2.5 h-2.5 bg-indigo-500 rounded-full" x-show="deliveryOption === 'standard'"></div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-gray-900 leading-none mb-1.5">Standard Delivery</h4>
                                    <p class="text-xs text-gray-500">
                                        Delivery in <span class="text-indigo-600 font-bold" x-text="packageData[selectedPackageId]?.turnaround || '...'"></span> days
                                    </p>
                                </div>
                                <span class="text-xs font-semibold text-gray-500">Included</span>
                            </label>

                            <!-- Express -->
                            <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 transition"
                                :class="deliveryOption === 'express' ? 'border-[#E8470A] ring-2 ring-orange-50 bg-orange-50/30' : 'border-gray-200 hover:border-gray-300 bg-white'">
                                <input type="radio" value="express" x-model="deliveryOption" class="sr-only">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5" :class="deliveryOption === 'express' ? 'border-[#E8470A]' : 'border-gray-300'">
                                    <div class="w-2.5 h-2.5 bg-[#E8470A] rounded-full" x-show="deliveryOption === 'express'"></div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1.5 leading-none">
                                        <h4 class="text-sm font-bold text-gray-900">Express Delivery</h4>
                                        <span class="bg-[#E8470A] text-white text-[8px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">Fast</span>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Delivery in <span class="text-[#E8470A] font-bold" x-text="packageData[selectedPackageId]?.express_turnaround"></span> days
                                    </p>
                                </div>
                                <span class="text-xs font-bold text-[#E8470A]">$<span x-text="packageData[selectedPackageId]?.emergency_fee"></span></span>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Options -->
EOT;

$content = str_replace(
    "                    <!-- Payment Options -->",
    $deliveryUI2,
    $content
);

file_put_contents($file, $content);
echo "Modification complete.\n";
