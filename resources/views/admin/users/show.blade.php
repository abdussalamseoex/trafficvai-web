<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600">&larr; Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Client Profile') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- User Info -->
                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm h-fit">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-black text-3xl mx-auto mb-4">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <h3 class="text-2xl font-black text-gray-900">{{ $user->name }}</h3>
                        <p class="text-indigo-600 font-bold">{{ $user->email }}</p>
                    </div>
                    <div class="space-y-4 pt-6 border-t border-gray-50">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Website</span>
                            <span class="font-bold text-gray-900 truncate max-w-[150px]">
                                @if($user->website)
                                    <a href="{{ $user->website }}" target="_blank" class="text-indigo-600 hover:underline">{{ parse_url($user->website, PHP_URL_HOST) ?: $user->website }}</a>
                                @else
                                    <span class="text-gray-300 italic">Not set</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Phone</span>
                            <span class="font-bold text-gray-900">
                                {{ $user->phone ?: 'Not set' }}
                            </span>
                        </div>
                        @php
                            $seoSpent = $user->orders->where('status', 'completed')->sum('total_amount');
                            $trafficTopupSpent = $user->trafficPointLogs->where('type', 'credit')->sum('cost_usd');
                            $totalSpentAll = $seoSpent + $trafficTopupSpent;
                            $totalOrdersAll = $user->orders->count() + $user->trafficCampaigns->count();
                        @endphp
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">USD Balance</span>
                            <span class="font-black text-emerald-600">${{ number_format($user->balance ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Traffic Points</span>
                            <span class="font-black text-orange-600">{{ number_format($user->traffic_points ?? 0) }} Pts</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Total Spent</span>
                            <span class="font-bold text-gray-900">${{ number_format($totalSpentAll, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Total Orders</span>
                            <span class="font-bold text-gray-900">{{ $totalOrdersAll }} <span class="text-xs text-gray-400">({{ $user->trafficCampaigns->count() }} Traffic)</span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Joined</span>
                            <span class="font-bold text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- User Orders & Direct Chat -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Direct Chat -->
                    <div x-data="{
                        messages: {{ $user->directMessages->sortBy('created_at')->map(fn($m) => [
                            'id' => $m->id,
                            'message' => $m->message,
                            'isSelf' => $m->sender_id === auth()->id(),
                            'user_name' => ($m->sender_id === auth()->id()) ? 'Administrative Post' : $user->name,
                            'created_at' => $m->created_at->diffForHumans()
                        ])->values()->toJson() }},
                        newMessage: '',
                        isSending: false,

                        init() {
                            this.scrollToBottom();
                        },

                        scrollToBottom() {
                            this.$nextTick(() => {
                                const box = this.$refs.chatBox;
                                if (box) box.scrollTop = box.scrollHeight;
                            });
                        },

                        async sendMessage() {
                            if (!this.newMessage.trim() || this.isSending) return;
                            this.isSending = true;

                            try {
                                const response = await fetch('{{ route('support.messages.store') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ 
                                        message: this.newMessage,
                                        client_id: {{ $user->id }}
                                    })
                                });

                                const data = await response.json();
                                if (data.status === 'success') {
                                    this.messages.push({
                                        id: data.message.id,
                                        message: data.message.message,
                                        isSelf: true,
                                        user_name: 'Administrative Post',
                                        created_at: data.message.created_at
                                    });
                                    this.newMessage = '';
                                    this.scrollToBottom();
                                }
                            } catch (error) {
                                console.error('Dispatch failed:', error);
                            } finally {
                                this.isSending = false;
                            }
                        }
                    }" class="bg-white rounded-[2.5rem] border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-black text-gray-900 leading-none">Direct Support Center</h4>
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">Correspondence with {{ $user->name }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div x-ref="chatBox" class="bg-gray-50/30 p-8 max-h-[500px] overflow-y-auto space-y-8 custom-scrollbar scroll-smooth">
                            <template x-for="msg in messages" :key="msg.id">
                                <div class="flex animate-fade-in-up" :class="msg.isSelf ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[75%] flex flex-col" :class="msg.isSelf ? 'items-end' : 'items-start'">
                                        <div class="flex items-center space-x-2 mb-1.5 px-1">
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest" x-text="msg.user_name"></span>
                                            <span class="text-[9px] text-gray-300">•</span>
                                            <span class="text-[9px] font-bold text-gray-300 uppercase" x-text="msg.created_at"></span>
                                        </div>
                                        <div class="px-6 py-4 shadow-sm transition-all duration-300" :class="msg.isSelf ? 'bg-indigo-600 text-white rounded-[1.5rem] rounded-tr-none' : 'bg-white text-gray-800 rounded-[1.5rem] rounded-tl-none border border-gray-100'">
                                            <p class="text-sm font-medium leading-relaxed" x-text="msg.message"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="messages.length === 0">
                                <div class="text-center py-20 text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    <p class="text-xs font-black uppercase tracking-widest">Initialization Required</p>
                                </div>
                            </template>
                        </div>

                        <div class="p-8 bg-white border-t border-gray-50">
                            <form @submit.prevent="sendMessage">
                                <div class="flex items-center space-x-4 bg-gray-50 rounded-[1.75rem] p-2 pr-3 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-600/10 border border-transparent focus-within:border-indigo-100 transition-all duration-300">
                                    <input x-model="newMessage" type="text" placeholder="Type your message to {{ $user->name }}..." class="flex-1 bg-transparent border-none rounded-xl px-4 py-3 text-sm font-medium focus:ring-0" required autocomplete="off">
                                    <button type="submit" :disabled="isSending" class="bg-indigo-600 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 transform active:scale-95 disabled:opacity-50">
                                        <span x-show="!isSending">Dispatch</span>
                                        <span x-show="isSending">Sending...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Client Traffic Campaigns History -->
                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-lg font-bold text-gray-900">Traffic Campaigns History</h4>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-600">{{ $user->trafficCampaigns->count() }} Campaigns</span>
                        </div>
                        <div class="space-y-4">
                            @forelse($user->trafficCampaigns as $tc)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-gray-50 rounded-2xl border border-gray-100 gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-gray-900 text-sm">#{{ $tc->external_order_id }}</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $tc->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">
                                            {{ $tc->status }}
                                        </span>
                                    </div>
                                    <p class="text-xs font-bold text-indigo-600 truncate max-w-md mt-1">{{ $tc->url }}</p>
                                    <p class="text-[11px] text-gray-500 mt-1">
                                        Type: <strong class="uppercase">{{ $tc->campaign_type }}</strong> | 
                                        Delivered: <strong>{{ number_format($tc->hits_delivered) }}</strong> / {{ number_format($tc->total_limit) }} hits ({{ $tc->delivery_percentage }}%)
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-3 py-1 rounded-xl bg-orange-500/10 text-orange-600 font-extrabold text-xs">
                                        -{{ number_format($tc->points_deducted) }} Pts
                                    </span>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $tc->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-center py-6 text-gray-500 italic text-sm">No traffic campaigns launched yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Client Traffic Points Ledger / Top-up History -->
                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8" x-data="{ activeTab: 'topups', limitTopups: 10, limitUsage: 10 }">
                        @php
                            $topupLogs = $user->trafficPointLogs->filter(fn($l) => in_array(strtolower(trim($l->type)), ['credit', 'purchase', 'topup']) || $l->points > 0)->values();
                            $usageLogs = $user->trafficPointLogs->filter(fn($l) => !in_array(strtolower(trim($l->type)), ['credit', 'purchase', 'topup']) && $l->points <= 0)->values();
                        @endphp

                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                            <h4 class="text-lg font-bold text-gray-900">Traffic Points & Top-up Ledger</h4>
                            <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-xl">
                                <button type="button" @click="activeTab = 'topups'" 
                                        :class="activeTab === 'topups' ? 'bg-white text-emerald-700 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                                    Top-ups ({{ $topupLogs->count() }})
                                </button>
                                <button type="button" @click="activeTab = 'usage'" 
                                        :class="activeTab === 'usage' ? 'bg-white text-orange-700 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                                    Usage Deductions ({{ $usageLogs->count() }})
                                </button>
                            </div>
                        </div>

                        <!-- Top-ups Tab -->
                        <div x-show="activeTab === 'topups'" x-cloak class="space-y-4">
                            @forelse($topupLogs as $index => $ledger)
                                <div class="flex items-center justify-between p-4 bg-emerald-50/40 rounded-2xl border border-emerald-100/60"
                                     x-show="{{ $index }} < limitTopups">
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">{{ $ledger->description }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            USD Spent: <strong class="text-emerald-600">${{ number_format($ledger->cost_usd, 2) }}</strong>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-sm text-emerald-600">
                                            +{{ number_format($ledger->points) }} Pts
                                        </span>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $ledger->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center py-6 text-gray-500 italic text-sm">No top-up purchase records found.</p>
                            @endforelse

                            @if($topupLogs->count() > 10)
                                <div class="text-center pt-3" x-show="limitTopups < {{ $topupLogs->count() }}">
                                    <button type="button" @click="limitTopups += 20" class="px-5 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800 transition">
                                        Load More Top-ups
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Usage Deductions Tab -->
                        <div x-show="activeTab === 'usage'" x-cloak class="space-y-4">
                            @forelse($usageLogs as $index => $ledger)
                                <div class="flex items-center justify-between p-4 bg-orange-50/40 rounded-2xl border border-orange-100/60"
                                     x-show="{{ $index }} < limitUsage">
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">{{ $ledger->description }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-black text-sm text-orange-600">
                                            -{{ number_format($ledger->points) }} Pts
                                        </span>
                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $ledger->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center py-6 text-gray-500 italic text-sm">No point deduction records found.</p>
                            @endforelse

                            @if($usageLogs->count() > 10)
                                <div class="text-center pt-3" x-show="limitUsage < {{ $usageLogs->count() }}">
                                    <button type="button" @click="limitUsage += 20" class="px-5 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800 transition">
                                        Load More Deductions
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- SEO / Guest Post Order History -->
                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                        <h4 class="text-lg font-bold text-gray-900 mb-6">SEO & Guest Post Order History</h4>
                        <div class="space-y-4">
                            @forelse($user->orders as $order)
                            <div class="flex items-center justify-between p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                <div>
                                    <p class="font-bold text-gray-900">
                                        {{ $order->package ? $order->package->service->name : ($order->guestPostSite ? 'Guest Post Placement' : 'SEO Service') }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $order->package ? $order->package->name : ($order->guestPostSite ? $order->guestPostSite->domain : 'Custom Order') }} - ${{ number_format($order->total_amount, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-white border border-gray-200">
                                        {{ $order->status }}
                                    </span>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-center py-6 text-gray-500 italic text-sm">No SEO/Guest Post orders yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
