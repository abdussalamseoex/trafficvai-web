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
        <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8">
            
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

            <!-- Main Layout: Denser Grid on left, Slim Sidebar on right -->
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Media Grid Container -->
                <div class="flex-1">
                    <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100">
                        <div class="p-4 sm:p-8">
                            <!-- Premium Toolbar -->
                            <div class="mb-10 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full xl:w-auto">
                                    <form action="{{ route('admin.media.index') }}" method="GET" class="flex items-center bg-gray-50 rounded-2xl border border-gray-200 p-1 focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500 transition-all w-full sm:w-96">
                                        <div class="relative flex-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </div>
                                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Find assets..." class="block w-full bg-transparent border-none py-2.5 pl-10 pr-3 text-sm placeholder-gray-400 focus:ring-0">
                                        </div>
                                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 rounded-xl text-white text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-600/20">
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
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-6">
                                    @foreach($media as $item)
                                        <div 
                                            class="relative aspect-square cursor-pointer active:scale-95 group rounded-3xl overflow-hidden border-2 transition-all duration-300 transform-gpu"
                                            :class="isSelected({{ $item->id }}) ? 'border-indigo-600 ring-8 ring-indigo-50 shadow-2xl scale-[1.02] z-10' : 'border-gray-50 hover:border-indigo-300 hover:shadow-xl hover:-translate-y-1'"
                                            @click="toggleSelection({{ json_encode($item) }}, '{{ $item->url }}')"
                                        >
                                            <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110" :class="isSelected({{ $item->id }}) ? 'opacity-90' : ''">
                                            
                                            <!-- Modern Selection Badge -->
                                            <div 
                                                class="absolute top-4 left-4 w-7 h-7 rounded-full border-2 bg-white/90 backdrop-blur flex items-center justify-center transition-all duration-300 shadow-lg"
                                                :class="isSelected({{ $item->id }}) ? 'bg-indigo-600 border-indigo-600 scale-110' : 'border-gray-200 opacity-0 group-hover:opacity-100'"
                                            >
                                                <svg x-show="isSelected({{ $item->id }})" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            </div>

                                            <!-- Asset Metadata Overlay -->
                                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out backdrop-blur-[2px]">
                                                <p class="text-[10px] text-white font-black truncate leading-tight mb-1">{{ $item->filename }}</p>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[8px] text-indigo-300 font-black uppercase tracking-widest">{{ $item->mime_type ? explode('/', $item->mime_type)[1] : 'IMG' }}</span>
                                                    <span class="text-[8px] text-gray-300 font-bold tracking-tighter">{{ $item->human_size }}</span>
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

                <!-- Premium Inspect Sidebar (Slimmer) -->
                <div class="w-full lg:w-80 flex-shrink-0">
                    <div class="bg-white overflow-hidden shadow-2xl sm:rounded-[32px] border border-gray-100 sticky top-6">
                        <div class="p-8">
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-8 flex items-center">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full mr-3 animate-pulse"></span>
                                Inspector
                            </h3>
                            
                            <template x-if="selected">
                                <div class="space-y-8">
                                    <!-- High-Quality Preview -->
                                    <div class="group relative rounded-3xl overflow-hidden bg-gray-50 border border-gray-100 shadow-inner overflow-hidden">
                                        <div class="aspect-square flex items-center justify-center p-4">
                                            <img :src="selectedUrl" class="max-w-full max-h-full object-contain transition duration-700 group-hover:scale-110 drop-shadow-2xl">
                                        </div>
                                    </div>

                                    <!-- Asset DNA -->
                                    <div class="grid grid-cols-1 gap-px bg-gray-100 rounded-2xl overflow-hidden border border-gray-100">
                                        <div class="bg-white p-4">
                                            <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-1">Mime Type</p>
                                            <p class="text-xs font-bold text-gray-700" x-text="selected.mime_type"></p>
                                        </div>
                                        <div class="bg-white p-4">
                                            <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-1">Dimensions / Weight</p>
                                            <p class="text-xs font-bold text-gray-700" x-text="formatSize(selected.size)"></p>
                                        </div>
                                    </div>

                                    <!-- SEO Orchestration -->
                                    <form :action="'{{ route('admin.media.index') }}/' + selected.id" method="POST" class="space-y-6">
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Alt Property</label>
                                                <input type="text" name="alt_text" x-model="selected.alt_text" class="w-full rounded-2xl border-gray-200 h-12 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all" placeholder="SEO Description...">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Anchor Title</label>
                                                <input type="text" name="title" x-model="selected.title" class="w-full rounded-2xl border-gray-200 h-12 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-1">Metadata Hook</label>
                                                <textarea name="description" x-model="selected.description" rows="3" class="w-full rounded-2xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-2">
                                            <button type="submit" class="flex-1 inline-flex justify-center items-center px-6 py-4 bg-indigo-600 rounded-2xl font-black text-[10px] text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-600/20 active:scale-95">
                                                Update Asset
                                            </button>
                                        </form>
                                        
                                        <form :action="'{{ url('/admin/media') }}/' + selected.id" method="POST" onsubmit="return confirm('Attention: Irreversible deletion of asset. Continue?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center p-4 bg-red-50 text-red-600 rounded-2xl hover:bg-red-100 transition border border-red-100 active:scale-95">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Asset Link -->
                                    <div class="pt-8 border-t border-gray-50">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Asset Endpoint</p>
                                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-2xl border border-gray-100">
                                            <input type="text" readonly :value="selectedUrl" class="flex-1 bg-transparent border-none text-[10px] font-bold text-gray-400 focus:ring-0 truncate py-1">
                                            <button @click="copyToClipboard(selectedUrl)" class="p-2.5 bg-white text-indigo-600 rounded-xl shadow-sm hover:text-indigo-800 transition active:scale-90">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selected">
                                <div class="text-center py-20 bg-indigo-50/30 rounded-[32px] border-4 border-dashed border-indigo-100/50">
                                    <svg class="h-16 w-16 text-indigo-100 mx-auto mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.172-1.172a4 4 0 115.656 5.656L17 12.586"/></svg>
                                    <p class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em] px-6 leading-loose">Select asset instance to initiate analysis</p>
                                </div>
                            </template>
                        </div>
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
        @media (min-width: 1024px) { .lg\:grid-cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); } }
        @media (min-width: 1280px) { .xl\:grid-cols-6 { grid-template-columns: repeat(6, minmax(0, 1fr)); } }
    </style>

    <script>
        function mediaLibrary() {
            return {
                selected: null,
                selectedUrl: '',
                selectedIds: [],
                uploading: false,
                allIds: @json($media->pluck('id')),

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

                toggleSelection(item, url) {
                    const index = this.selectedIds.indexOf(item.id);
                    if (index === -1) {
                        this.selectedIds.push(item.id);
                        this.selected = item;
                        this.selectedUrl = url;
                    } else {
                        this.selectedIds.splice(index, 1);
                        if (this.selected && this.selected.id === item.id) {
                            if (this.selectedIds.length > 0) {
                                this.selected = null;
                                this.selectedUrl = '';
                            } else {
                                this.selected = null;
                                this.selectedUrl = '';
                            }
                        }
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
