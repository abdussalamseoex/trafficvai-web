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
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Total Spent</span>
                            <span class="font-bold text-gray-900">${{ number_format($user->orders->where('status', 'completed')->sum('total_amount'), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Total Orders</span>
                            <span class="font-bold text-gray-900">{{ $user->orders->count() }}</span>
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

                    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-8">
                        <h4 class="text-lg font-bold text-gray-900 mb-6">Order History</h4>
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
                            <p class="text-center py-8 text-gray-500 italic">No orders yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
