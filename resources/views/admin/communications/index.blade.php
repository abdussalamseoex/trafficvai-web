<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-gray-900 tracking-tight">
                Support intelligence <span class="text-purple-600 block text-[10px] uppercase tracking-[0.3em] font-black mt-0.5">Global Correspondence Control</span>
            </h2>
            <div class="flex items-center px-4 py-1.5 bg-green-50 rounded-full border border-green-100 shadow-sm">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-ping mr-2"></span>
                <span class="text-[9px] font-black text-green-700 uppercase tracking-widest">Network Operational</span>
            </div>
        </div>
    </x-slot>

    <div class="py-4 h-[calc(100vh-140px)] min-h-[600px]">
        <div class="max-w-[1600px] mx-auto h-full px-4 sm:px-6 lg:px-8" x-data="adminCommunicationHub()" x-init="init()">
            <div class="bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.04)] border border-gray-100 overflow-hidden flex h-full relative">
                
                <!-- Sidebar: Streams -->
                <div 
                    class="w-full md:w-80 lg:w-[380px] border-r border-gray-100 flex flex-col bg-white z-20 transition-all duration-300 shrink-0"
                    :class="activeConv ? 'hidden md:flex' : 'flex'"
                >
                    <!-- Search -->
                    <div class="p-6 space-y-4 flex-shrink-0">
                        <div class="relative group">
                            <input 
                                type="text" 
                                x-model="search"
                                placeholder="Search contacts..." 
                                class="w-full bg-[#f3f4f6] border-none rounded-xl pl-10 pr-10 py-3 text-sm focus:ring-2 focus:ring-purple-600 transition-all font-medium"
                            >
                            <svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <svg class="w-4 h-4 absolute right-3.5 top-3.5 text-gray-400 cursor-pointer hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4.5h18m-18 6h18m-18 6h18"></path></svg>
                        </div>

                        <!-- Tabs -->
                        <div class="flex space-x-2">
                            <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-[#9333ea] text-white shadow-md' : 'bg-[#f3f4f6] text-gray-500 hover:text-gray-700'" class="px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all">All</button>
                            <button @click="filter = 'order'" :class="filter === 'order' ? 'bg-[#9333ea] text-white shadow-md' : 'bg-[#f3f4f6] text-gray-500 hover:text-gray-700'" class="px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all">Orders</button>
                            <button @click="filter = 'direct'" :class="filter === 'direct' ? 'bg-[#9333ea] text-white shadow-md' : 'bg-[#f3f4f6] text-gray-500 hover:text-gray-700'" class="px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all">Direct</button>
                        </div>
                    </div>

                    <!-- Streams List -->
                    <div class="flex-1 overflow-y-auto px-2 space-y-1 pb-8 min-h-0 custom-scrollbar scroll-smooth">
                        <template x-for="conv in filteredConversations" :key="conv.type + '-' + (conv.id || Math.random())">
                            <button 
                                @click="selectConversation(conv)"
                                :class="isActive(conv) ? 'bg-[#f8f6ff]' : 'bg-white hover:bg-gray-50'"
                                class="w-full flex items-start p-4 transition-all duration-200 group text-left relative overflow-hidden rounded-xl"
                            >
                                <div class="relative shrink-0">
                                    <div class="w-12 h-12 rounded-lg bg-gray-900 flex items-center justify-center shadow-md transform transition-transform group-hover:scale-105 overflow-hidden">
                                        <template x-if="conv.client && conv.client.avatar_url">
                                            <img :src="conv.client.avatar_url" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!conv.client || !conv.client.avatar_url">
                                            <span class="text-white text-lg font-black" x-text="(conv.client ? conv.client.name : 'U').substring(0,1).toUpperCase()"></span>
                                        </template>
                                    </div>
                                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                                </div>
                                
                                <div class="ml-4 flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <h4 class="text-sm font-black text-gray-900 truncate tracking-tight pr-2" x-text="conv.client ? conv.client.name : conv.title"></h4>
                                        <span class="text-[10px] text-gray-400 font-medium flex-shrink-0" x-text="formatTime(conv.created_at)"></span>
                                    </div>
                                    <p class="text-[11px] text-[#9333ea] font-medium uppercase tracking-tighter mb-1" x-text="conv.type === 'order' ? conv.title : 'Direct Line'"></p>
                                    <p class="text-xs truncate text-gray-500 font-medium" x-text="conv.last_message ? conv.last_message.message : 'Ready for uplink...'"></p>
                                </div>
                                
                                <div x-show="conv.unread_count > 0" class="absolute right-4 bottom-4 w-5 h-5 bg-[#9333ea] rounded-full flex items-center justify-center text-[10px] font-bold text-white shadow-sm">
                                    <span x-text="conv.unread_count"></span>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Main: Terminal -->
                <div 
                    class="flex-1 flex flex-col bg-white overflow-hidden relative z-10 min-h-0"
                    :class="activeConv ? 'flex' : 'hidden md:flex'"
                >
                    <template x-if="activeConv">
                        <div class="flex flex-col h-full">
                            <!-- Header -->
                            <div class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white z-20 shrink-0">
                                <div class="flex items-center min-w-0">
                                    <button @click="activeConv = null" class="md:hidden mr-4 p-1 text-gray-400 hover:text-purple-600 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center text-white text-lg font-black shadow-md mr-4 overflow-hidden relative shrink-0">
                                        <template x-if="activeConv.client && activeConv.client.avatar_url">
                                            <img :src="activeConv.client.avatar_url" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!activeConv.client || !activeConv.client.avatar_url">
                                            <span x-text="(activeConv.client ? activeConv.client.name : 'U').substring(0,1).toUpperCase()"></span>
                                        </template>
                                        <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-black text-base text-gray-900 truncate tracking-tight" x-text="activeConv && activeConv.client ? activeConv.client.name : (activeConv ? activeConv.title : '')"></h3>
                                        <p class="text-[11px] font-medium text-purple-600 uppercase tracking-tighter" x-text="activeConv && activeConv.type === 'order' ? 'Order Segment: LE' + activeConv.id : 'Direct Admin Link'"></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 shrink-0">
                                    <div class="relative group hidden sm:block">
                                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <input type="text" x-model="messageSearch" placeholder="Search dialog..." class="bg-gray-50 border-transparent focus:border-purple-300 focus:ring-2 focus:ring-purple-100 rounded-full pl-9 pr-4 py-1.5 text-xs text-gray-700 font-bold w-48 transition-all">
                                    </div>
                                    <a :href="activeDetails.link || '#'" class="px-3 py-1.5 bg-gray-900 text-white rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all">Context</a>
                                </div>
                            </div>

                            <!-- Messages Area -->
                            <div 
                                x-ref="messagesBox" 
                                class="flex-1 overflow-y-auto p-10 space-y-6 bg-white custom-scrollbar scroll-smooth relative"
                                :class="loadingMessages ? 'opacity-50' : ''"
                            >
                                <template x-for="msg in filteredMessages" :key="msg.id">
                                    <div class="flex flex-col group" :class="msg.is_self ? 'items-end' : 'items-start'">
                                        <div class="flex flex-col max-w-[75%]" :class="msg.is_self ? 'items-end' : 'items-start'">
                                            <div 
                                                class="px-5 py-4 shadow-none relative text-[14px] font-medium leading-relaxed"
                                                :class="msg.is_self ? 'bg-[#9333ea] text-white rounded-[1.25rem] rounded-tr-none shadow-lg shadow-purple-100' : 'bg-[#f3f4f6] text-gray-700 rounded-[1.25rem] rounded-tl-none'"
                                            >
                                                <p class="whitespace-pre-wrap" x-text="msg.message"></p>
                                                
                                                <template x-if="msg.attachment_path">
                                                    <div class="mt-3 pt-3 border-t" :class="msg.is_self ? 'border-white/20' : 'border-gray-200'">
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
                                                            <a :href="msg.attachment_path" download class="flex justify-between items-center p-2.5 rounded-xl transition-all hover:bg-black/5 group/link" :class="msg.is_self ? 'text-white bg-white/10' : 'text-purple-600 bg-black/5'">
                                                                <div class="flex items-center min-w-0 pr-3">
                                                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                    <span class="text-[10px] font-black uppercase truncate" x-text="msg.attachment_name || 'Attachment'"></span>
                                                                </div>
                                                                <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center transition-colors shadow-sm" :class="msg.is_self ? 'bg-white/20 group-hover/link:bg-white group-hover/link:text-purple-600' : 'bg-purple-100 group-hover/link:bg-purple-600 group-hover/link:text-white'">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                                </div>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="flex items-center mt-1.5 space-x-1" :class="msg.is_self ? 'justify-end' : 'justify-start'">
                                                <span class="text-[10px] text-gray-300 font-bold uppercase tracking-widest" x-text="msg.created_at"></span>
                                                <template x-if="msg.is_self">
                                                    <div>
                                                        <!-- Read -->
                                                        <svg x-show="msg.is_read" class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7m-6 8l3 3L22 7"></path></svg>
                                                        <!-- Unread -->
                                                        <svg x-show="!msg.is_read" class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Input Area -->
                            <div class="px-8 py-6 bg-white border-t border-gray-50 flex items-center shrink-0 relative">
                                <!-- Canned Responses Popup -->
                                <div x-show="showCanned && newMessage === '/'" x-transition class="absolute bottom-full left-10 mb-2 w-64 bg-white rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-gray-100 overflow-hidden z-50">
                                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-100 text-[10px] font-black uppercase tracking-widest text-gray-400">Quick Replies</div>
                                    <div class="max-h-48 overflow-y-auto custom-scrollbar">
                                        <template x-for="canned in cannedResponses">
                                            <button @click="useCanned(canned)" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors border-b border-gray-50 last:border-0 truncate" x-text="canned"></button>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex-1 flex items-end space-x-3 bg-[#f3f4f6] border border-transparent focus-within:border-purple-300 focus-within:bg-white focus-within:ring-4 focus-within:ring-purple-100 rounded-3xl p-2 transition-all duration-300 shadow-inner">
                                    
                                    <!-- Attachment Button -->
                                    <label class="p-3 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-full cursor-pointer transition-all shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        <input type="file" class="hidden" x-ref="attachmentInput" @change="handleFileUpload">
                                    </label>
                                    
                                    <!-- Textarea Input -->
                                    <div class="flex-1 max-w-full relative">
                                        <div x-show="newMessage && newMessage.startsWith('[Asset:')" class="text-xs font-bold text-purple-600 mb-1 px-3 break-all flex items-center">
                                            <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            <span x-text="newMessage.substring(0, newMessage.indexOf(']')+1) + ' attached'"></span>
                                        </div>
                                        <textarea 
                                            x-ref="messageInput"
                                            x-model="newMessage" 
                                            @keydown.enter.prevent="if(!isSending && newMessage.trim() && !showCanned) sendMessage()"
                                            @input="showCanned = newMessage === '/'"
                                            rows="1" 
                                            placeholder="Type your response here... (use / for quick replies)" 
                                            class="w-full bg-transparent border-none focus:ring-0 text-[15px] font-medium p-3 pb-3.5 resize-none placeholder:text-gray-400 scrollbar-hide h-14 overflow-hidden"
                                        ></textarea>
                                    </div>
                                    
                                    <!-- Send Button -->
                                    <button 
                                        @click="sendMessage()"
                                        :disabled="isSending || !newMessage.trim()"
                                        class="p-3 bg-purple-600 text-white rounded-full hover:bg-purple-700 disabled:opacity-50 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed transition-all shrink-0 shadow-md transform active:scale-95"
                                        title="Send message"
                                    >
                                        <svg class="w-6 h-6 translate-x-0.5 -translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="!activeConv" class="flex-1 flex flex-col items-center justify-center p-20 text-center bg-gray-50/10">
                        <div class="w-24 h-24 bg-white rounded-[2.5rem] flex items-center justify-center text-gray-100 mb-8 shadow-[0_20px_50px_rgba(0,0,0,0.02)] border border-gray-50 animate-pulse">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 mb-2 tracking-tight">Monitoring Inactive</h3>
                        <p class="text-gray-400 max-w-xs mx-auto text-[10px] font-black uppercase tracking-[0.2em]">Select an active transmission to initialize correspondence monitoring.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function adminCommunicationHub() {
        return {
            conversations: @js($conversations),
            search: '',
            messageSearch: '',
            filter: 'all',
            activeConv: null,
            activeDetails: { title: '', subtitle: '', link: '#' },
            messages: [],
            newMessage: '',
            isSending: false,
            loadingMessages: false,
            pollInterval: null,
            showCanned: false,
            cannedResponses: [
                "Hello, how can I assist you today?",
                "We are currently reviewing your request.",
                "Your order has been updated.",
                "Please provide more details.",
                "Thank you for your patience."
            ],

            init() {
                console.log('Admin Intelligence Grid Online.');
                this.pollInterval = setInterval(() => {
                    if (this.activeConv && !this.isSending) {
                        this.fetchMessages(this.activeConv, true);
                    }
                }, 3000);
            },

            isActive(conv) {
                return this.activeConv && this.activeConv.id == conv.id && this.activeConv.type == conv.type;
            },

            get filteredConversations() {
                return this.conversations.filter(c => {
                    const title = (c.client ? c.client.name : c.title) || '';
                    const matchesSearch = title.toLowerCase().includes(this.search.toLowerCase());
                    const matchesFilter = this.filter === 'all' || c.type === this.filter;
                    return matchesSearch && matchesFilter;
                });
            },

            get filteredMessages() {
                if (!this.messageSearch) return this.messages;
                return this.messages.filter(m => {
                    return m.message && m.message.toLowerCase().includes(this.messageSearch.toLowerCase());
                });
            },

            async selectConversation(conv) {
                this.activeConv = conv;
                this.messages = [];
                this.loadingMessages = true;
                this.messageSearch = '';
                await this.fetchMessages(conv);
            },

            async fetchMessages(conv, hidden = false) {
                try {
                    const response = await fetch(`{{ route('inbox.messages') }}?type=${conv.type}&id=${conv.id}`);
                    const data = await response.json();
                    
                    const oldLength = this.messages.length;
                    this.messages = data.messages;
                    this.activeDetails = data.details || { title: '', subtitle: '', link: '#' };
                    
                    if (!hidden || data.messages.length > oldLength) {
                        this.scrollToBottom();
                    }

                    if (conv.unread_count > 0) {
                        window.dispatchEvent(new CustomEvent('message-read', { detail: { count: conv.unread_count } }));
                        conv.unread_count = 0;
                    }
                } catch (e) {
                    if(!hidden) console.error('Data Fetch Error:', e);
                } finally {
                    if(!hidden) this.loadingMessages = false;
                }
            },

            useCanned(response) {
                this.newMessage = response;
                this.showCanned = false;
                this.$refs.messageInput.focus();
            },

            async sendMessage() {
                const fileInput = this.$refs.attachmentInput;
                const file = fileInput ? fileInput.files[0] : null;

                if ((!this.newMessage.trim() && !file) || this.isSending) return;
                this.isSending = true;

                const url = this.activeConv.type === 'order' 
                    ? `/orders/${this.activeConv.id}/messages` 
                    : '{{ route("support.messages.store") }}';

                let formData = new FormData();
                
                let actualMessage = this.newMessage;
                if (file && actualMessage.startsWith('[Asset: ')) {
                    actualMessage = actualMessage.substring(actualMessage.indexOf(']') + 1).trim();
                }
                
                if (!actualMessage && file) {
                    actualMessage = 'Sent an attachment'; // Default message if only sending file
                }

                formData.append('message', actualMessage);
                if (file) formData.append('attachment', file);
                if (this.activeConv.type === 'direct') formData.append('client_id', this.activeConv.id);

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();
                    if (data.status === 'success') {
                        this.messages.push(data.message);
                        this.newMessage = '';
                        if (fileInput) fileInput.value = '';
                        this.messageSearch = '';
                        this.scrollToBottom();
                        
                        this.activeConv.last_message = {
                            message: data.message.message,
                            created_at: new Date().toISOString()
                        };
                    }
                } catch (e) {
                    console.error('Failure:', e);
                } finally {
                    this.isSending = false;
                }
            },

            handleFileUpload(e) {
                const file = e.target.files[0];
                if (!file) return;
                this.newMessage = "[Asset: " + file.name + "] " + this.newMessage;
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const box = this.$refs.messagesBox;
                    if (box) box.scrollTop = box.scrollHeight;
                });
            },

            formatTime(timestamp) {
                if (!timestamp) return '';
                const date = new Date(timestamp);
                if (isNaN(date.getTime())) return timestamp;
                const now = new Date();
                const diff = now - date;
                if (diff < 3600000) return Math.floor(Math.max(0, diff / 60000)) + 'm ago';
                if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
                return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
            }
        }
    }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-app-layout>
