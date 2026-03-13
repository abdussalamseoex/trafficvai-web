<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Media Library') }}
            </h2>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.media.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Sync Existing
                    </button>
                </form>
                <div x-data="{ uploading: false, progress: 0 }">
                    <label for="file-upload" class="cursor-pointer inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Upload Image
                    </label>
                    <input id="file-upload" type="file" class="hidden" @change="uploadFile($event)">
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="mediaLibrary()">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-bold uppercase tracking-tight">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Layout: Full Width Grid -->
            <div>
                <!-- Media Grid Container -->
                <div class="flex-1">
                    <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100">
                        <div class="p-3 sm:p-5">
                            <!-- Premium Toolbar -->
                            <div class="mb-10 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full xl:w-auto">
                                    <form action="{{ route('admin.media.index') }}" method="GET" class="flex items-center bg-gray-50 rounded-2xl border border-gray-200 p-1 focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all w-full sm:w-96">
                                        <div class="relative flex-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </div>
                                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search assets..." class="block w-full bg-transparent border-none py-1.5 pl-10 pr-3 text-sm placeholder-gray-400 focus:ring-0 font-medium">
                                        </div>
                                        <button type="submit" class="inline-flex items-center px-5 py-2 bg-indigo-600 rounded-xl text-white text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-600/20">
                                            Search
                                        </button>
                                    </form>

                                    <div class="flex items-center gap-2">
                                        <button 
                                            @click="selectAll()" 
                                            class="inline-flex items-center px-4 py-3 bg-white border border-gray-200 rounded-2xl text-[10px] font-black text-gray-600 uppercase tracking-widest hover:bg-gray-50 hover:border-indigo-300 transition shadow-sm"
                                        >
                                            <span x-text="selectedIds.length === allIds.length ? 'Deselect All' : 'Select All'"></span>
                                        </button>
                                        
                                        <button 
                                            x-show="selectedCount > 0" 
                                            @click="deleteSelected()" 
                                            class="inline-flex items-center px-4 py-3 bg-red-50 border border-red-100 rounded-2xl font-black text-[10px] text-red-600 uppercase tracking-widest hover:bg-red-100 transition shadow-sm"
                                            x-cloak
                                        >
                                            Delete (<span x-text="selectedCount"></span>)
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if($media->count() > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 xxl:grid-cols-5 gap-4">
                                    @foreach($media as $item)
                                        <div 
                                            class="relative aspect-square cursor-pointer active:scale-95 group rounded-2xl overflow-hidden border-2 transition-all duration-300 transform-gpu"
                                            :class="isSelected({{ $item->id }}) ? 'border-indigo-600 ring-4 ring-indigo-50 shadow-2xl scale-[1.01] z-10' : 'border-gray-50 hover:border-indigo-300 hover:shadow-xl'"
                                            @click="openModal({{ $loop->index }})"
                                        >
                                            <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110" :class="isSelected({{ $item->id }}) ? 'opacity-90' : ''">
                                            
                                            <!-- Modern Selection Badge (Click to select) -->
                                            <div 
                                                class="absolute top-3 left-3 w-6 h-6 rounded-full border-2 bg-white/90 backdrop-blur flex items-center justify-center transition-all duration-300 shadow-lg cursor-pointer hover:scale-125 z-20"
                                                :class="isSelected({{ $item->id }}) ? 'bg-indigo-600 border-indigo-600 scale-110 opacity-100' : 'border-gray-300 opacity-0 group-hover:opacity-100'"
                                                @click.stop="toggleSelection({{ $item->id }})"
                                            >
                                                <svg x-show="isSelected({{ $item->id }})" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            </div>

                                            <!-- Asset Metadata Overlay -->
                                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out backdrop-blur-[1px]">
                                                <p class="text-[9px] text-white font-black truncate leading-tight mb-1">{{ $item->filename }}</p>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[7px] text-indigo-300 font-black uppercase tracking-widest">{{ $item->mime_type ? strtoupper(explode('/', $item->mime_type)[1]) : 'IMG' }}</span>
                                                    <span class="text-[7px] text-gray-300 font-bold tracking-tighter">{{ $item->human_size }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-12">
                                    {{ $media->links() }}
                                </div>
                            @else
                                <div class="text-center py-40 bg-gray-50/50 rounded-[40px] border-4 border-dashed border-gray-100">
                                    <div class="bg-white w-24 h-24 rounded-[32px] shadow-xl flex items-center justify-center mx-auto mb-8 border border-gray-50">
                                        <svg class="h-12 w-12 text-indigo-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Empty Library</h3>
                                    <p class="mt-3 text-sm text-gray-500 font-medium max-w-xs mx-auto leading-relaxed">No visual assets were found. Start building your library by uploading files or syncing existing images.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- The Sidebar is removed for full-width layout -->
                <!-- WordPress Style Attachment Details Modal -->
                <div x-show="isModalOpen" 
                     class="fixed inset-0 z-50 flex bg-gray-100" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0"
                     @keydown.escape.window="closeModal"
                     @keydown.right.window="nextMedia"
                     @keydown.left.window="prevMedia">
                    
                    <div class="flex flex-col w-full h-full relative">
                        <!-- Modal Header -->
                        <div class="h-14 bg-white border-b border-gray-200 flex items-center justify-between px-4 flex-shrink-0 relative z-20">
                            <h2 class="text-sm font-semibold text-gray-800">Attachment details</h2>
                            
                            <div class="flex items-center gap-2">
                                <!-- Navigation -->
                                <div class="flex items-center border border-gray-200 rounded-md overflow-hidden bg-white shadow-sm">
                                    <button @click="prevMedia" :disabled="currentIndex === 0" class="p-1.5 hover:bg-gray-50 disabled:opacity-30 disabled:hover:bg-transparent transition text-gray-600 border-r border-gray-200">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <button @click="nextMedia" :disabled="currentIndex === mediaItems.length - 1" class="p-1.5 hover:bg-gray-50 disabled:opacity-30 disabled:hover:bg-transparent transition text-gray-600">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                                
                                <!-- Close Button -->
                                <button @click="closeModal" class="p-1.5 ml-2 hover:bg-gray-100 rounded-md transition text-gray-500">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body (Split Layout) -->
                        <template x-if="currentMedia">
                            <div class="flex-1 overflow-hidden flex flex-col lg:flex-row relative z-10 w-full">
                                
                                <!-- Left side: Large Image Preview -->
                                <div class="flex-1 bg-gray-100 flex items-center justify-center p-8 overflow-hidden relative">
                                    <div class="absolute inset-x-0 inset-y-0 p-8 flex items-center justify-center">
                                       <img :src="currentMedia.url" class="max-w-full max-h-full w-auto h-auto object-contain drop-shadow-xl" :alt="currentMedia.alt_text">
                                    </div>
                                </div>

                                <!-- Right side: Metadata and Actions -->
                                <div class="w-full lg:w-96 xl:w-4/12 bg-gray-50 border-l border-gray-200 flex-shrink-0 overflow-y-auto">
                                    <div class="p-6">
                                        <!-- Asset Info summary -->
                                        <div class="mb-6 flex gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                            <div class="w-16 h-16 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0 flex justify-center items-center">
                                                <img :src="currentMedia.url" class="max-w-full max-h-full object-contain">
                                            </div>
                                            <div class="flex-1 min-w-0 flex flex-col justify-center">
                                                <p class="text-[11px] text-gray-500 font-semibold mb-1" x-text="formatDate(currentMedia.created_at)"></p>
                                                <p class="text-sm font-bold text-gray-900 truncate mb-1" x-text="currentMedia.filename"></p>
                                                <div class="flex items-center gap-3 text-[11px] text-gray-500 font-medium">
                                                    <span x-text="currentMedia.human_size"></span>
                                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                                    <span class="uppercase" x-text="currentMedia.mime_type.split('/')[1] || 'IMAGE'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SEO and Info Form -->
                                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden pb-4">
                                            <div class="bg-gray-50/50 px-5 py-3 border-b border-gray-100">
                                                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Asset Metadata</h3>
                                            </div>
                                            
                                            <form :action="'{{ url('/admin/media') }}/' + currentMedia.id" method="POST" class="p-5 space-y-4">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 sm:gap-4">
                                                    <label class="w-full sm:w-28 text-xs font-semibold text-gray-600 sm:text-right flex-shrink-0">Alternative Text</label>
                                                    <div class="flex-1 min-w-0">
                                                        <input type="text" name="alt_text" :value="currentMedia.alt_text" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <p class="text-[10px] text-gray-400 mt-1 leading-tight"><a href="#" class="text-indigo-600 hover:underline">Learn how to describe the purpose of the image.</a> Leave empty if the image is purely decorative.</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                                    <label class="w-full sm:w-28 text-xs font-semibold text-gray-600 sm:text-right flex-shrink-0">Title</label>
                                                    <div class="flex-1 min-w-0">
                                                        <input type="text" name="title" :value="currentMedia.title" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    </div>
                                                </div>
                                                
                                                <div class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-4">
                                                    <label class="w-full sm:w-28 text-xs font-semibold text-gray-600 sm:text-right pt-2 flex-shrink-0">Description</label>
                                                    <div class="flex-1 min-w-0">
                                                        <textarea name="description" rows="3" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" x-text="currentMedia.description"></textarea>
                                                    </div>
                                                </div>

                                                <div class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-4 pt-2">
                                                    <label class="w-full sm:w-28 text-xs font-semibold text-gray-600 sm:text-right pt-1.5 flex-shrink-0">File URL</label>
                                                    <div class="flex-1 min-w-0 space-y-2">
                                                        <input type="text" readonly :value="currentMedia.url" class="w-full text-xs font-mono bg-gray-50 text-gray-500 border-gray-200 rounded-md shadow-inner py-1.5 px-3 truncate focus:ring-0">
                                                        <button type="button" @click="copyToClipboard(currentMedia.url)" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-semibold rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            Copy URL to clipboard
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="pt-6 mt-4 border-t border-gray-100 flex justify-between items-center">
                                                    <a :href="currentMedia.url" target="_blank" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">View original file</a>
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                        Update Asset
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Delete Action -->
                                        <div class="mt-4 flex justify-end">
                                            <form :action="'{{ url('/admin/media') }}/' + currentMedia.id" method="POST" onsubmit="return confirm('You are about to permanently delete this item from your site.\nThis action cannot be undone.\n\'Cancel\' to stop, \'OK\' to delete.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline">
                                                    Delete permanently
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- High-End Processing Overlay -->
        <div x-show="uploading" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/40 backdrop-blur-xl" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white p-12 rounded-[48px] shadow-2xl text-center max-w-sm w-full mx-6 border-b-8 border-indigo-600">
                <div class="relative w-24 h-24 mx-auto mb-8">
                    <div class="absolute inset-0 border-[6px] border-gray-100 rounded-full"></div>
                    <div class="absolute inset-0 border-[6px] border-indigo-600 rounded-full border-t-transparent animate-[spin_0.8s_linear_infinite]"></div>
                </div>
                <h4 class="text-3xl font-black text-gray-900 mb-2 tracking-tighter">Uploading</h4>
                <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Optimizing Assets</p>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        @media (min-width: 640px) { .sm\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (min-width: 768px) { .md\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        @media (min-width: 1024px) { .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (min-width: 1280px) { .xl\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        @media (min-width: 1536px) { .xxl\:grid-cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); } }
        
    </style>

    <script>
        function mediaLibrary() {
            return {
                isModalOpen: false,
                currentIndex: 0,
                mediaItems: @json($media->map(function($item) {
                    $item->url = $item->url;
                    $item->human_size = $item->human_size;
                    return $item;
                })->values()),
                selectedIds: [],
                uploading: false,
                allIds: @json($media->pluck('id')),

                get currentMedia() {
                    return this.mediaItems[this.currentIndex];
                },

                get selectedCount() {
                    return this.selectedIds.length;
                },

                isSelected(id) {
                    return this.selectedIds.includes(id);
                },

                selectAll() {
                    if (this.selectedIds.length === this.allIds.length) {
                        this.selectedIds = [];
                        this.selected = null;
                        this.selectedUrl = '';
                    } else {
                        this.selectedIds = [...this.allIds];
                    }
                },

                openModal(index) {
                    this.currentIndex = index;
                    this.isModalOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.isModalOpen = false;
                    document.body.style.overflow = 'auto';
                },

                nextMedia() {
                    if (this.currentIndex < this.mediaItems.length - 1) {
                        this.currentIndex++;
                    }
                },

                prevMedia() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    }
                },

                toggleSelection(id) {
                    const index = this.selectedIds.indexOf(id);
                    if (index === -1) {
                        this.selectedIds.push(id);
                    } else {
                        this.selectedIds.splice(index, 1);
                    }
                },

                deleteSelected() {
                    if (!confirm(`Confirm destruction of ${this.selectedCount} selected assets?`)) return;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.media.bulk-destroy') }}';
                    
                    const token = document.createElement('input');
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = '{{ csrf_token() }}';
                    form.appendChild(token);

                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    form.appendChild(method);

                    this.selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                },

                formatSize(bytes) {
                    if (bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },

                formatDate(dateString) {
                    return new Date(dateString).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                },

                async uploadFile(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    this.uploading = true;

                    try {
                        const response = await fetch('{{ route('admin.media.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Upload failed: ' + (data.message || 'Verification of file size/type required.'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Network protocol error during upload.');
                    } finally {
                        this.uploading = false;
                    }
                },

                copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                        const toast = document.createElement('div');
                        toast.className = 'fixed bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest shadow-2xl z-[200] animate-bounce';
                        toast.innerText = 'Endpoint Copied';
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 2000);
                    });
                }
            }
        }
    </script>
</x-app-layout>
