@props(['order'])

<div x-data="{ 
    open: false, 
    messages: @js($order->messages->map(function($msg) {
        return [
            'id' => $msg->id,
            'user_id' => $msg->user_id,
            'user_name' => $msg->user->name ?? 'User',
            'message' => $msg->message,
            'created_at' => $msg->created_at->diffForHumans(),
            'is_self' => $msg->user_id == auth()->id(),
            'attachment_path' => $msg->attachment_path ? asset('storage/' . $msg->attachment_path) : null,
            'attachment_name' => $msg->attachment_name,
        ];
    })),
    newMessage: '',
    isSending: false,
    
    init() {
        this.$watch('open', value => {
            if (value) {
                this.$nextTick(() => {
                    const container = this.$refs.messagesContainer;
                    container.scrollTop = container.scrollHeight;
                });
            }
        });
    },

    scrollToBottom() {
        this.$nextTick(() => {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    }
}" class="fixed bottom-6 right-6 z-50">
    <!-- Chat Toggle Button -->
    <button 
        @click="open = !open" 
        class="w-16 h-16 bg-indigo-600 rounded-full shadow-2xl flex items-center justify-center text-white hover:bg-indigo-700 transition-all duration-300 transform hover:scale-110 active:scale-95 group relative"
        aria-label="Toggle Chat"
    >
        <template x-if="!open">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
        </template>
        <template x-if="open">
            <svg class="w-8 h-8 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </template>

        <!-- Unread Indicator -->
        <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full border-4 border-gray-50 flex items-center justify-center text-[10px] font-bold" x-show="messages.filter(m => !m.is_read && !m.is_self).length > 0">
            <span x-text="messages.filter(m => !m.is_read && !m.is_self).length"></span>
        </div>
    </button>

    <!-- Chat Window -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-10 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-10 scale-95"
        class="absolute bottom-20 right-0 w-[400px] h-[600px] bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] overflow-hidden flex flex-col border border-gray-100/50 backdrop-blur-xl"
        x-cloak
    >
        <!-- Header -->
        <div class="p-6 bg-gradient-to-br from-indigo-600 to-purple-700 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-lg leading-tight">Order Chat</h4>
                    <p class="text-xs opacity-70">Order #{{ $order->id }} • Active</p>
                </div>
            </div>
            <button @click="open = false" class="p-2 hover:bg-white/10 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div x-ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50/50">
            <div class="text-center py-4">
                <span class="px-3 py-1 bg-gray-200/50 rounded-full text-[10px] font-bold text-gray-400 uppercase tracking-widest">Conversation Started</span>
            </div>
            
            <template x-for="msg in messages" :key="msg.id">
                <div class="flex flex-col" :class="msg.is_self ? 'items-end' : 'items-start'">
                    <div 
                        class="max-w-[85%] p-4 shadow-sm"
                        :class="msg.is_self ? 'bg-indigo-600 text-white rounded-[1.5rem] rounded-tr-none' : 'bg-white text-gray-800 rounded-[1.5rem] rounded-tl-none border border-gray-100'"
                    >
                        <p class="text-sm leading-relaxed" x-text="msg.message"></p>
                        
                        <template x-if="msg.attachment_path">
                            <div class="mt-3 pt-3 border-t" :class="msg.is_self ? 'border-white/20' : 'border-gray-100'">
                                <a :href="msg.attachment_path" target="_blank" class="flex items-center text-xs font-bold hover:underline" :class="msg.is_self ? 'text-white' : 'text-indigo-600'">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span x-text="msg.attachment_name || 'View Attachment'"></span>
                                </a>
                            </div>
                        </template>
                    </div>
                    <span class="text-[9px] mt-1.5 font-bold text-gray-400 uppercase px-2" x-text="msg.created_at"></span>
                </div>
            </template>
            
            <div x-show="isSending" class="flex justify-end opacity-50 animate-pulse">
                <div class="bg-indigo-600 text-white px-4 py-2 rounded-2xl">
                    <div class="flex space-x-1">
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-1.5 h-1.5 bg-white rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-6 bg-white border-t border-gray-100">
            <form 
                action="{{ route('orders.messages.store', $order) }}" 
                method="POST" 
                enctype="multipart/form-data" 
                class="relative"
                @submit="isSending = true"
            >
                @csrf
                <textarea 
                    name="message" 
                    x-model="newMessage"
                    rows="1" 
                    @keydown.enter.prevent="if(!isSending && newMessage.trim()){ $el.closest('form').submit() }"
                    class="w-full bg-gray-50 border-none rounded-2xl pr-24 pl-5 py-4 focus:ring-2 focus:ring-indigo-600 resize-none overflow-hidden text-sm"
                    placeholder="Message..."
                ></textarea>
                
                <div class="absolute right-2 top-2 flex items-center space-x-1">
                    <label class="p-2 text-gray-400 hover:text-indigo-600 cursor-pointer transition rounded-xl hover:bg-indigo-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        <input type="file" name="attachment" class="hidden" @change="if($event.target.files.length) newMessage = 'File: ' + $event.target.files[0].name">
                    </label>
                    <button 
                        type="submit" 
                        :disabled="!newMessage.trim() || isSending"
                        class="bg-indigo-600 text-white p-2.5 rounded-xl hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-indigo-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </div>
            </form>
            <p class="text-[9px] text-gray-400 text-center mt-3 font-bold uppercase tracking-tighter italic">Press Enter to Send</p>
        </div>
    </div>
</div>
