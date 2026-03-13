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
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Media Grid -->
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <!-- Search & Filter -->
                            <div class="mb-6">
                                <form action="{{ route('admin.media.index') }}" method="GET" class="flex gap-2">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search images..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        Search
                                    </button>
                                </form>
                            </div>

                            @if($media->count() > 0)
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    @foreach($media as $item)
                                        <div 
                                            class="relative aspect-square cursor-pointer group rounded-lg overflow-hidden border-2 transition-all"
                                            :class="selected && selected.id == {{ $item->id }} ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-transparent hover:border-gray-300'"
                                            @click="selectMedia({{ json_encode($item) }}, '{{ $item->url }}')"
                                        >
                                            <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-x-0 bottom-0 bg-black/50 p-1 translate-y-full group-hover:translate-y-0 transition-transform">
                                                <p class="text-[10px] text-white truncate text-center">{{ $item->filename }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-8">
                                    {{ $media->links() }}
                                </div>
                            @else
                                <div class="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No media found</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by uploading your first image.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Details Panel -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Image Details</h3>
                            
                            <template x-if="selected">
                                <div class="space-y-6">
                                    <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden border">
                                        <img :src="selectedUrl" class="w-full h-full object-contain">
                                    </div>

                                    <div class="text-xs text-gray-500 space-y-1">
                                        <p><span class="font-semibold">Filename:</span> <span x-text="selected.filename"></span></p>
                                        <p><span class="font-semibold">Uploaded:</span> <span x-text="formatDate(selected.created_at)"></span></p>
                                        <p><span class="font-semibold">Size:</span> <span x-text="formatSize(selected.size)"></span></p>
                                        <p><span class="font-semibold">Type:</span> <span x-text="selected.mime_type"></span></p>
                                    </div>

                                    <form :action="'{{ route('admin.media.index') }}/' + selected.id" method="POST" class="space-y-4 pt-4 border-t">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Alt Text (SEO)</label>
                                            <input type="text" name="alt_text" x-model="selected.alt_text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Describe the image...">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Title Attribute</label>
                                            <input type="text" name="title" x-model="selected.title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Description</label>
                                            <textarea name="description" x-model="selected.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                                        </div>
                                        <div class="flex items-center justify-between gap-2">
                                            <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 text-center">
                                                Update SEO
                                            </button>
                                        </form>
                                        <form :action="'{{ route('admin.media.index') }}/' + selected.id" method="POST" onsubmit="return confirm('Permanently delete this image?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center p-2 bg-red-100 border border-transparent rounded-md text-red-600 hover:bg-red-200">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <div class="pt-4 border-t">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Copy URL</label>
                                        <div class="flex gap-2">
                                            <input type="text" readonly :value="selectedUrl" class="flex-1 bg-gray-50 rounded-md border-gray-300 text-[10px] text-gray-500">
                                            <button @click="copyToClipboard(selectedUrl)" class="p-1 px-2 bg-gray-100 text-gray-600 rounded hover:bg-gray-200">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selected">
                                <div class="text-center py-10">
                                    <p class="text-sm text-gray-400 italic">Select an image to view details and manage SEO properties.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Overlay (Simple) -->
        <div x-show="uploading" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-white p-6 rounded-2xl shadow-2xl text-center max-w-sm w-full">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-600 border-t-transparent mx-auto mb-4"></div>
                <h4 class="text-lg font-bold text-gray-900">Uploading Image...</h4>
                <p class="text-sm text-gray-500 mt-2">Please wait while we process and optimize your asset.</p>
            </div>
        </div>
    </div>

    <script>
        function mediaLibrary() {
            return {
                selected: null,
                selectedUrl: '',
                uploading: false,

                selectMedia(item, url) {
                    this.selected = item;
                    this.selectedUrl = url;
                },

                formatSize(bytes) {
                    if (bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },

                formatDate(dateString) {
                    return new Date(dateString).toLocaleDateString();
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
                            body: formData
                        });

                        const data = await response.json();
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Upload failed: ' + (data.message || 'Unknown error'));
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
