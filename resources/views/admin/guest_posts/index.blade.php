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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.guest-posts.edit', $site) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
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
