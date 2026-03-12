@props(['order'])

<div x-data="{
    messages: {{ $order->messages->map(fn($msg) => [
        'id' => $msg->id,
        'user_name' => ($msg->user?->name) ?? 'System Author',
        'message' => $msg->message,
        'is_self' => $msg->user_id == auth()->id(),
        'created_at' => $msg->created_at->diffForHumans(),
        'attachment_path' => $msg->attachment_path ? asset('storage/' . $msg->attachment_path) : null,
        'attachment_name' => $msg->attachment_name,
    ])->toJson() }},
    newMessage: '',
    isSending: false,
    attachmentSelected: false,
    attachmentName: '',

    init() {
        this.scrollToBottom();
    },

    scrollToBottom() {
        this.$nextTick(() => {
            const container = this.$refs.messagesContainer;
            if (container) container.scrollTop = container.scrollHeight;
        });
    },

    async handleSubmit() {
        if (!this.newMessage.trim() && !this.$refs.attachmentInput.files.length) return;
        this.isSending = true;

        const formData = new FormData();
        formData.append('message', this.newMessage);
        formData.append('_token', '{{ csrf_token() }}');
        if (this.$refs.attachmentInput.files.length) {
            formData.append('attachment', this.$refs.attachmentInput.files[0]);
        }

        try {
            const response = await fetch('{{ route('orders.messages.store', $order) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) {
                const errData = await response.json();
                throw new Error(errData.message || 'Dispatch Interrupted');
            }

            const data = await response.json();
            if (data.status === 'success') {
                this.messages.push(data.message);
                this.newMessage = '';
                this.attachmentSelected = false;
                this.attachmentName = '';
                this.$refs.attachmentInput.value = '';
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Dispatch failed:', error);
            alert('Error: ' + error.message);
        } finally {
            this.isSending = false;
        }
    }
}" class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-[2.5rem] mt-10 border border-gray-100">
    <div class="p-8 md:p-10">
        <!-- Header -->
        <div class="flex items-center justify-between mb-10 pb-6 border-b border-gray-50">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-gray-900 leading-tight">Order Discussion</h3>
                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mt-1 flex items-center">
                        <span class="inline-block w-2 h-2 bg-indigo-400 rounded-full mr-2 animate-pulse"></span>
                        <span x-text="messages.length"></span> messages in thread
                    </p>
                </div>
            </div>
            <div class="hidden sm:block">
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100/50 uppercase tracking-wider">
                    Official Communication Canal
                </span>
            </div>
        </div>

        <!-- Messages Thread -->
        <div x-ref="messagesContainer" class="space-y-10 mb-10 max-h-[600px] overflow-y-auto px-4 py-2 custom-scrollbar scroll-smooth">
            <template x-for="msg in messages" :key="msg.id">
                <div class="flex w-full" :class="msg.is_self ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[80%] md:max-w-[70%] flex flex-col group" :class="msg.is_self ? 'items-end' : 'items-start'">
                        <!-- Sender Metadata -->
                        <div class="flex items-center space-x-3 mb-2 px-1">
                            <template x-if="!msg.is_self">
                                <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-black text-gray-400 border-2 border-white shadow-sm overflow-hidden" x-text="msg.user_name.charAt(0)"></div>
                            </template>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest" x-text="msg.user_name"></span>
                            <span class="text-[10px] text-gray-300">•</span>
                            <span class="text-[10px] font-bold text-gray-300 uppercase tracking-tighter" x-text="msg.created_at"></span>
                        </div>
 
                        <!-- Message Bubble -->
                        <div 
                            class="relative px-6 py-4 transition-all duration-300 shadow-sm border animate-fade-in-up"
                            :class="msg.is_self ? 'bg-indigo-600 text-white border-indigo-700' : 'bg-white text-gray-800 border-gray-100'"
                            :style="msg.is_self ? 'border-radius: 1.75rem 1.75rem 0.5rem 1.75rem;' : 'border-radius: 1.75rem 1.75rem 1.75rem 0.5rem;'"
                        >
                            <div class="whitespace-pre-wrap text-[15px] leading-relaxed font-medium antialiased" x-text="msg.message"></div>
                            
                            <template x-if="msg.attachment_path">
                                <div class="mt-4 pt-4 border-t" :class="msg.is_self ? 'border-indigo-400/50' : 'border-gray-50'">
                                    <template x-if="msg.attachment_path.match(/\.(jpeg|jpg|gif|png|webp|bmp|svg)(\?.*)?$/i)">
                                        <div class="mb-1 relative group overflow-hidden rounded-xl bg-black/5 inline-block">
                                            <img :src="msg.attachment_path" class="max-w-full sm:max-w-[250px] max-h-[250px] object-cover rounded-xl transition-transform duration-300 group-hover:scale-105" alt="Attachment" />
                                            <a :href="msg.attachment_path" download class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                <span class="p-2.5 bg-white text-gray-900 rounded-full shadow-lg transform scale-75 group-hover:scale-100 transition-transform">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                </span>
                                            </a>
                                        </div>
                                    </template>
                                    <template x-if="!msg.attachment_path.match(/\.(jpeg|jpg|gif|png|webp|bmp|svg)(\?.*)?$/i)">
                                        <a :href="msg.attachment_path" download class="flex justify-between items-center p-2.5 rounded-xl transition-all hover:bg-black/5 group/link" :class="msg.is_self ? 'bg-white/10 text-white' : 'bg-indigo-50/50 text-indigo-600'">
                                            <div class="flex items-center min-w-0 pr-3">
                                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                <span class="text-[11px] font-black uppercase tracking-wider truncate" x-text="msg.attachment_name || 'Source File'"></span>
                                            </div>
                                            <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center transition-colors shadow-sm" :class="msg.is_self ? 'bg-white/20 group-hover/link:bg-white group-hover/link:text-indigo-600' : 'bg-indigo-100 group-hover/link:bg-indigo-600 group-hover/link:text-white'">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
            
            <template x-if="messages.length === 0">
                <div class="text-center py-20 bg-gray-50/50 rounded-[3rem] border-2 border-dashed border-gray-100">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm border border-gray-50">
                        <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <p class="text-gray-400 font-black uppercase tracking-widest text-xs">Awaiting primary correspondence</p>
                </div>
            </template>
        </div>

        <!-- Composer -->
        <div class="px-8 py-6 bg-white border-t border-gray-50 flex items-center">
            <div class="flex-1 flex items-end space-x-3 bg-[#f3f4f6] border border-transparent focus-within:border-indigo-300 focus-within:bg-white focus-within:ring-4 focus-within:ring-indigo-100 rounded-3xl p-2 transition-all duration-300 shadow-inner">
                
                <!-- Attachment Button -->
                <label class="p-3 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-full cursor-pointer transition-all shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    <input type="file" class="hidden" x-ref="attachmentInput" @change="attachmentSelected = true; attachmentName = $el.files[0].name">
                </label>
                
                <!-- Textarea Input -->
                <div class="flex-1 max-w-full">
                    <div x-show="attachmentSelected" class="text-xs font-bold text-indigo-600 mb-1 px-3 break-all flex items-center">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        <span x-text="attachmentName + ' attached'"></span>
                        <button type="button" @click="attachmentSelected = false; attachmentName = ''; $refs.attachmentInput.value = ''" class="ml-2 text-red-500 hover:text-red-700 font-bold px-1">✕</button>
                    </div>
                    <textarea 
                        x-model="newMessage" 
                        @keydown.enter.prevent="if(!isSending && (newMessage.trim() || attachmentSelected)) handleSubmit()"
                        rows="1" 
                        placeholder="Type your message here..." 
                        class="w-full bg-transparent border-none focus:ring-0 text-[15px] font-medium p-3 pb-3.5 resize-none placeholder:text-gray-400 scrollbar-hide h-14 overflow-hidden"
                    ></textarea>
                </div>
                
                <!-- Send Button -->
                <button 
                    @click="handleSubmit()"
                    :disabled="isSending || (!newMessage.trim() && !attachmentSelected)"
                    class="p-3 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 disabled:opacity-50 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed transition-all shrink-0 shadow-md transform active:scale-95"
                    title="Send message"
                >
                    <svg class="w-6 h-6 translate-x-0.5 -translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #e2e8f0; }

    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
