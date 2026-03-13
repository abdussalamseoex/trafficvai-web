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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-md shadow-sm">
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Main Layout: Grid on left (larger), sidebar on right -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Media Grid Container -->
                <div class="flex-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                        <div class="p-6">
                            <!-- Search & Action Bar -->
                            <div class="mb-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                                <form action="{{ route('admin.media.index') }}" method="GET" class="flex w-full sm:max-w-xs gap-2">
                                    <div class="relative flex-1">
                                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search images..." class="w-full rounded-lg border-gray-200 pl-10 h-10 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 rounded-lg text-white text-xs font-bold uppercase transition hover:bg-indigo-700">
                                        Go
                                    </button>
                                </form>
                                
                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    <button 
                                        @click="selectAll()" 
                                        class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-widest px-2 py-1 bg-indigo-50 rounded"
                                    >
                                        Select All
                                    </button>
                                    <button 
                                        x-show="selectedCount > 0" 
                                        @click="deleteSelected()" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition shadow-sm"
                                        style="display: none;"
                                    >
                                        Delete Selected (<span x-text="selectedCount"></span>)
                                    </button>
                                </div>
                            </div>

                            @if($media->count() > 0)
                                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                                    @foreach($media as $item)
                                        <div 
                                            class="relative aspect-square cursor-pointer active:scale-95 group rounded-xl overflow-hidden border-2 transition-all duration-200"
                                            :class="isSelected({{ $item->id }}) ? 'border-indigo-600 ring-4 ring-indigo-50 shadow-xl' : 'border-gray-100 hover:border-indigo-300 hover:shadow-md'"
                                            @click="toggleSelection({{ json_encode($item) }}, '{{ $item->url }}')"
                                        >
                                            <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-110" :class="isSelected({{ $item->id }}) ? 'opacity-90' : ''">
                                            
                                            <!-- Selection indicator -->
                                            <div 
                                                class="absolute top-3 left-3 w-6 h-6 rounded-full border-2 bg-white flex items-center justify-center transition-all duration-300"
                                                :class="isSelected({{ $item->id }}) ? 'bg-indigo-600 border-indigo-600 scale-110' : 'border-gray-200 opacity-0 group-hover:opacity-100'"
                                            >
                                                <svg x-show="isSelected({{ $item->id }})" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            </div>

                                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                                <p class="text-[10px] text-white font-medium truncate">{{ $item->filename }}</p>
                                                <p class="text-[8px] text-gray-200 uppercase tracking-widest">{{ $item->human_size }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-10">
                                    {{ $media->links() }}
                                </div>
                            @else
                                <div class="text-center py-32 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100">
                                    <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                                        <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900">Media library is empty</h3>
                                    <p class="mt-2 text-sm text-gray-500 max-w-xs mx-auto">Upload images or sync existing ones to start optimizing your content.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Details Panel (Fixed on desktop) -->
                <div class="w-full lg:w-96">
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border border-indigo-50 sticky top-6">
                        <div class="p-8">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Details
                            </h3>
                            
                            <template x-if="selected">
                                <div class="space-y-6">
                                    <div class="aspect-video bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 shadow-inner group">
                                        <img :src="selectedUrl" class="w-full h-full object-contain p-2 transition duration-500 group-hover:scale-105">
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Format</p>
                                            <p class="text-xs font-semibold text-gray-700 truncate" x-text="selected.mime_type.split('/')[1].toUpperCase()"></p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Weight</p>
                                            <p class="text-xs font-semibold text-gray-700" x-text="formatSize(selected.size)"></p>
                                        </div>
                                    </div>

                                    <form :action="'{{ route('admin.media.index') }}/' + selected.id" method="POST" class="space-y-5 pt-4">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Alt Text (Search Engine Optimization)</label>
                                            <input type="text" name="alt_text" x-model="selected.alt_text" class="w-full rounded-xl border-gray-200 h-12 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="Provide a detailed description...">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Anchor Title</label>
                                            <input type="text" name="title" x-model="selected.title" class="w-full rounded-xl border-gray-200 h-12 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Metadata Description</label>
                                            <textarea name="description" x-model="selected.description" rows="4" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                                        </div>
                                        
                                        <div class="flex items-center gap-3 pt-2">
                                            <button type="submit" class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-md">
                                                Save Changes
                                            </button>
                                        </form>
                                        
                                        <form :action="'{{ url('/admin/media') }}/' + selected.id" method="POST" onsubmit="return confirm('Attention: This will permanently delete the file. Proceed?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center p-3 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition border border-red-100">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <div class="pt-6 border-t border-gray-50">
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Asset URL</label>
                                        <div class="flex gap-2 p-1.5 bg-gray-50 rounded-xl border border-gray-100">
                                            <input type="text" readonly :value="selectedUrl" class="flex-1 bg-transparent border-none text-[10px] text-gray-400 focus:ring-0 truncate py-1">
                                            <button @click="copyToClipboard(selectedUrl)" class="p-2 bg-white text-indigo-600 rounded-lg hover:shadow-sm hover:text-indigo-800 transition">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selected">
                                <div class="text-center py-20 bg-indigo-50/50 rounded-3xl border-2 border-dashed border-indigo-100">
                                    <svg class="h-12 w-12 text-indigo-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.172-1.172a4 4 0 115.656 5.656L17 12.586"/></svg>
                                    <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest px-6">Select an image to preview and edit</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Overlay (Premium) -->
        <div x-show="uploading" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-md" x-cloak>
            <div class="bg-white p-10 rounded-3xl shadow-2xl text-center max-w-sm w-full mx-4">
                <div class="relative w-20 h-20 mx-auto mb-6">
                    <div class="absolute inset-0 border-4 border-indigo-50 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
                </div>
                <h4 class="text-2xl font-black text-gray-900 mb-2 tracking-tight">Processing</h4>
                <p class="text-sm text-gray-500 font-medium leading-relaxed">Please wait while we optimize and secure your media assets...</p>
            </div>
        </div>
    </div>

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
                                // Potentially find another one to show, or just clear
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
                    if (!confirm(`Are you sure you want to delete ${this.selectedCount} selected images?`)) return;

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
                            alert('Upload failed: ' + (data.message || 'Check file size or format.'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred during upload.');
                    } finally {
                        this.uploading = false;
                    }
                },

                copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                        alert('URL copied to clipboard!');
                    });
                }
            }
        }
    </script>
</x-app-layout>
