<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Guest Post Inventory') }}
            </h2>
            <div class="flex gap-2">
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'import-sites-modal')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded flex items-center gap-2 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Import CSV
                </button>
                <a href="{{ route('admin.guest-posts.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Add New Site
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 border-b border-gray-200 bg-gray-50/50">
                    <form method="GET" action="{{ route('admin.guest-posts.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Search URL</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by domain..." class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                        </div>

                        <!-- Per Page -->
                        <div>
                            <label for="per_page" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Items Per Page</label>
                            <select name="per_page" id="per_page" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 Sites</option>
                                <option value="20" {{ request('per_page', 20) == '20' ? 'selected' : '' }}>20 Sites</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Sites</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 Sites</option>
                            </select>
                        </div>

                        <!-- Ownership Type -->
                        <div>
                            <label for="ownership_type" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Role / Type</label>
                            <select name="ownership_type" id="ownership_type" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                <option value="All" {{ request('ownership_type') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="Owner" {{ request('ownership_type') == 'Owner' ? 'selected' : '' }}>Owner</option>
                                <option value="Contributor" {{ request('ownership_type') == 'Contributor' ? 'selected' : '' }}>Contributor</option>
                            </select>
                        </div>

                        <!-- Is Featured -->
                        <div>
                            <label for="is_featured" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Featured Status</label>
                            <select name="is_featured" id="is_featured" class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                                <option value="All" {{ request('is_featured') == 'All' ? 'selected' : '' }}>All</option>
                                <option value="1" {{ request('is_featured') == '1' ? 'selected' : '' }}>Featured Only</option>
                                <option value="0" {{ request('is_featured') == '0' ? 'selected' : '' }}>Non-Featured Only</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-4 rounded-lg transition duration-150 text-sm flex-1">
                                Filter
                            </button>
                            <a href="{{ route('admin.guest-posts.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1.5 px-4 rounded-lg transition duration-150 text-sm flex-none text-center">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="p-6 text-gray-900 border-t border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website URL</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niche</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metrics (DA/DR/Traffic)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($sites as $site)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ $site->url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">{{ $site->url }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(is_array($site->niche))
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_slice($site->niche, 0, 3) as $n)
                                                    <span class="inline-block bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded border border-indigo-100 uppercase">{{ $n }}</span>
                                                @endforeach
                                                @if(count($site->niche) > 3)
                                                    <span class="text-xs text-gray-500" title="{{ implode(', ', $site->niche) }}">+{{ count($site->niche) - 3 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            {{ $site->niche }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        DA: {{ $site->da ?? 'N/A' }} | DR: {{ $site->dr ?? 'N/A' }} | Trf: {{ number_format($site->traffic) ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ${{ number_format($site->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $site->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $site->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        @if($site->is_featured)
                                        <span class="px-2 mt-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                            Featured
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex items-center justify-end gap-3">
                                        <form action="{{ route('admin.guest-posts.toggle-feature', $site) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" title="{{ $site->is_featured ? 'Unfeature' : 'Feature this site' }}" class="text-amber-500 hover:text-amber-600 focus:outline-none">
                                                <svg class="w-5 h-5 {{ $site->is_featured ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 20 20">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.guest-posts.edit', $site) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form action="{{ route('admin.guest-posts.destroy', $site) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this site?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        No guest post sites added yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($sites->hasPages())
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        {{ $sites->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <x-modal name="import-sites-modal" maxWidth="md" focusable>
        <form method="post" action="{{ route('admin.guest-posts.import') }}" class="p-6" enctype="multipart/form-data">
            @csrf

            <h2 class="text-lg font-medium text-gray-900 mb-2">
                Import Guest Post Sites
            </h2>

            <p class="text-sm text-gray-600 mb-4">
                Upload a CSV file containing your guest post sites data. 
                <strong>Required columns:</strong> url.
                <br>
                <strong>Optional columns:</strong> niche, da, dr, traffic, price, is_active, link_type, max_links_allowed, is_sponsored, language, service_type, spam_score, price_creation_placement, price_link_insertion, delivery_time_days.
            </p>

            <div class="mt-4">
                <x-input-label for="import_file" value="CSV File" />
                <input type="file" id="import_file" name="import_file" class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded file:border-0
                    file:text-sm file:font-semibold
                    file:bg-emerald-50 file:text-emerald-700
                    hover:file:bg-emerald-100 border border-gray-300 rounded p-1
                " accept=".csv, .txt" required autofocus>
                <x-input-error class="mt-2" :messages="$errors->get('import_file')" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancel
                </x-secondary-button>

                <x-primary-button class="ml-3 bg-emerald-600 hover:bg-emerald-700">
                    Import Sites
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
