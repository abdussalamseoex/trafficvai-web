<div x-data="{ 
    chatOpen: localStorage.getItem('trafficvai_support_chat') === 'true',
    hasOpened: localStorage.getItem('trafficvai_support_opened') === 'true',
    messages: @js($messages),
    newMessage: '',
    isSending: false,
    showHelp: false,
    
    init() {
        if (!this.messages) {
            this.messages = [];
        }
        
        this.$watch('chatOpen', value => {
            localStorage.setItem('trafficvai_support_chat', value);
            if (value) {
                this.scrollToBottom();
                this.showHelp = false;
                
                if (!this.hasOpened && this.messages.length === 0) {
                    this.hasOpened = true;
                    localStorage.setItem('trafficvai_support_opened', 'true');
                    setTimeout(() => {
                        this.messages.push({
                            id: 'welcome_msg_' + Date.now(),
                            message: 'Welcome to TrafficVai Support! How can we help you today?',
                            is_self: false,
                            created_at: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                        });
                        this.scrollToBottom();
                    }, 500);
                }
            }
        });
    },

    scrollToBottom() {
        this.$nextTick(() => {
            const container = this.$refs.messagesContainer;
            if (container) container.scrollTop = container.scrollHeight;
        });
    },

    handleFileUpload(e) {
        const file = e.target.files[0];
        if (!file) return;
        this.newMessage = '[Asset: ' + file.name + '] ' + this.newMessage;
    },

    async sendMessage() {
        if (this.isSending) return;
        
        const fileInput = this.$refs.attachmentInput;
        const file = fileInput ? fileInput.files[0] : null;
        
        if (!this.newMessage.trim() && !file) return;
        
        this.isSending = true;
        let formData = new FormData();
        
        let actualMessage = this.newMessage;
        if (file && actualMessage.startsWith('[Asset: ')) {
            actualMessage = actualMessage.substring(actualMessage.indexOf(']') + 1).trim();
        }
        
        if (!actualMessage && file) {
            actualMessage = 'Sent an attachment';
        }

        formData.append('message', actualMessage);
        if (file) {
            formData.append('attachment', file);
        }
        
        try {
            const response = await fetch('{{ route('support.messages.store') }}', {
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
                throw new Error(errData.message || 'Support Line Failure');
            }

            const data = await response.json();
            
            if (data.status === 'success') {
                this.messages.push(data.message);
                this.newMessage = '';
                if (fileInput) fileInput.value = '';
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Failed to send message:', error);
            alert('Error: ' + error.message);
        } finally {
            this.isSending = false;
        }
    }
}" 
x-on:toggle-support-chat.window="chatOpen = !chatOpen"
x-on:open-support-chat.window="chatOpen = true"
class="fixed flex flex-col items-end" style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 999999 !important; top: auto !important; left: auto !important;">
    
    <!-- Floating Quick Actions -->
    <div x-show="!chatOpen" class="flex flex-col items-end space-y-3 mb-4 pr-1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <!-- Messages Button -->
        <a href="{{ route('inbox') }}" class="flex items-center space-x-2 bg-white px-5 py-2.5 rounded-full shadow-lg border border-gray-100 font-bold text-sm text-gray-600 hover:text-brand hover:-translate-x-1 transition-all duration-300 whitespace-nowrap">
            <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_5px_rgba(34,197,94,0.5)] animate-pulse"></span>
            <span>Universal Inbox</span>
        </a>

        <!-- Customer Support Button -->
        <button @click="chatOpen = true" class="flex items-center space-x-2 bg-white px-5 py-2.5 rounded-full shadow-lg border border-gray-100 font-bold text-sm text-gray-600 hover:text-brand hover:-translate-x-1 transition-all duration-300 whitespace-nowrap focus:outline-none">
            <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_5px_rgba(34,197,94,0.5)] animate-pulse"></span>
            <span>Direct Support</span>
        </button>
    </div>

    <!-- Support Chat Window -->
    <div 
        x-show="chatOpen" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-10 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-10 scale-95"
        class="mb-6 w-[350px] h-[500px] max-w-[calc(100vw-48px)] bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.2)] overflow-hidden flex flex-col border border-gray-100/50 backdrop-blur-xl"
        x-cloak
    >
        <!-- Header -->
        <div class="p-6 bg-gradient-to-br from-brand-600 to-brand text-white shadow-lg flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-11 h-11 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-4 border-indigo-600 rounded-full"></div>
                    </div>
                    <div>
                        <h4 class="font-black text-sm leading-tight tracking-tight uppercase">Support Hub</h4>
                        <p class="text-[9px] opacity-70 uppercase font-black tracking-widest mt-0.5 flex items-center">
                            <span class="w-1 h-1 bg-white rounded-full mr-2 animate-ping"></span>
                            Live Node Connection
                        </p>
                    </div>
                </div>
                <button @click="chatOpen = false" class="text-white hover:text-indigo-200 transition p-2 bg-white/10 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div x-ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-5 bg-gray-50/50 scroll-smooth custom-scrollbar">
            <div class="text-center py-2">
                <span class="px-4 py-1.5 bg-white text-gray-400 rounded-full text-[9px] font-black uppercase tracking-widest shadow-sm border border-gray-100">Synchronized Correspondence</span>
            </div>
            
            <template x-for="msg in messages" :key="msg.id">
                <div class="flex flex-col animate-fade-in" :class="msg.is_self ? 'items-end' : 'items-start'">
                    <div 
                        class="max-w-[85%] p-4 shadow-xl border relative"
                        :class="msg.is_self ? 'bg-indigo-600 text-white border-indigo-700 rounded-[1.5rem] rounded-tr-none' : 'bg-white text-gray-800 rounded-[1.5rem] rounded-tl-none border-gray-100 shadow-gray-100/50'"
                    >
                        <p class="text-[14px] font-medium leading-relaxed whitespace-pre-wrap" x-text="msg.message"></p>
                        
                        <!-- Attachment logic -->
                        <template x-if="msg.attachment_path">
                            <div class="mt-3 pt-3 border-t" :class="msg.is_self ? 'border-indigo-400/50' : 'border-gray-50'">
                                <template x-if="msg.attachment_path.match(/\.(jpeg|jpg|gif|png|webp|bmp|svg)(\?.*)?$/i)">
                                    <div class="mb-1 relative group overflow-hidden rounded-xl bg-black/5 inline-block">
                                        <img :src="msg.attachment_path" class="max-w-full sm:max-w-[200px] max-h-[200px] object-cover rounded-xl transition-transform duration-300 group-hover:scale-105" alt="Attachment" />
                                        <a :href="msg.attachment_path" download class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <span class="p-2.5 bg-white text-gray-900 rounded-full shadow-lg transform scale-75 group-hover:scale-100 transition-transform">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </span>
                                        </a>
                                    </div>
                                </template>
                                <template x-if="!msg.attachment_path.match(/\.(jpeg|jpg|gif|png|webp|bmp|svg)(\?.*)?$/i)">
                                    <a :href="msg.attachment_path" download class="flex justify-between items-center p-2 rounded-xl transition-all hover:bg-black/5 group/link" :class="msg.is_self ? 'bg-white/10 text-white' : 'bg-indigo-50/50 text-indigo-600'">
                                        <div class="flex items-center min-w-0 pr-2">
                                            <svg class="w-3.5 h-3.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            <span class="text-[10px] font-black uppercase tracking-wider truncate" x-text="msg.attachment_name || 'Source File'"></span>
                                        </div>
                                        <div class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center transition-colors shadow-sm" :class="msg.is_self ? 'bg-white/20 group-hover/link:bg-white group-hover/link:text-indigo-600' : 'bg-indigo-100 group-hover/link:bg-indigo-600 group-hover/link:text-white'">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                    <span class="text-[8px] mt-1.5 font-black text-gray-300 uppercase px-2 tracking-widest" x-text="msg.created_at"></span>
                </div>
            </template>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-50 flex-shrink-0">
            <div class="flex items-end space-x-2 bg-gray-50 border border-gray-100 focus-within:border-indigo-300 focus-within:bg-white focus-within:ring-4 focus-within:ring-indigo-100/50 rounded-2xl p-1.5 transition-all duration-300">
                
                <!-- Attachment Button -->
                <label class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl cursor-pointer transition-all shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    <input type="file" class="hidden" x-ref="attachmentInput" @change="handleFileUpload">
                </label>
                
                <!-- Textarea -->
                <div class="flex-1 max-w-full">
                    <div x-show="newMessage && newMessage.startsWith('[Asset:')" class="text-[9px] font-bold text-indigo-600 mb-1 px-2 break-all flex items-center uppercase tracking-wider">
                        <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        <span x-text="newMessage.substring(0, newMessage.indexOf(']')+1) + ' attached'"></span>
                    </div>
                    <textarea 
                        x-model="newMessage" 
                        @keydown.enter.prevent="sendMessage()" 
                        rows="1" 
                        class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium p-2 pb-2.5 resize-none placeholder:text-gray-400 placeholder:italic scrollbar-hide h-10 overflow-hidden" 
                        placeholder="Ask..."
                    ></textarea>
                </div>
                
                <!-- Send Button -->
                <button 
                    @click="sendMessage()" 
                    type="button" 
                    :disabled="(!newMessage.trim() && !$refs.attachmentInput?.files[0]) || isSending" 
                    class="p-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all disabled:opacity-50 disabled:bg-gray-300 disabled:text-gray-500 shrink-0 shadow-md transform active:scale-95"
                >
                    <svg x-show="!isSending" class="w-4 h-4 translate-x-0.5 -translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    <svg x-show="isSending" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
            <p class="text-[8px] text-gray-400 text-center mt-3 font-black uppercase tracking-widest italic opacity-40">End-to-End Encrypted Data Transfer</p>
        </div>
    </div>

    <!-- Main Support Toggle Button -->
    <button 
        @click="chatOpen = !chatOpen" 
        id="toggle-support-chat"
        class="w-16 h-16 bg-brand rounded-full shadow-[0_10px_25px_rgba(232,71,10,0.4)] flex items-center justify-center text-white hover:bg-brand-600 hover:-translate-y-1 transition-all duration-300 transform active:scale-95 group relative border border-brand-400 focus:outline-none"
        aria-label="Toggle Support Chat"
    >
        <template x-if="!chatOpen">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
        </template>
        <template x-if="chatOpen">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </template>
        
        <span class="absolute -top-1 -right-1 flex h-5 w-5" x-show="messages.some(m => !m.is_read)">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-5 w-5 bg-red-500 border-2 border-white"></span>
        </span>
    </button>
</div>

<style>
    .w-18 { width: 4.5rem; }
    .h-18 { height: 4.5rem; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    [x-cloak] { display: none !important; }
</style>
