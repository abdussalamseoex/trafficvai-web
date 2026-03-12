<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Home Section: ') }} {{ $homeSection->name }}
            </h2>
            <a href="{{ route('admin.home-sections.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                &larr; Back to Sections
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.home-sections.update', $homeSection) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-y-6">
                            @foreach ($homeSection->content as $key => $value)
                                <div>
                                    <label for="content_{{ $key }}" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                    </label>

                                    @if(is_array($value))
                                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <p class="text-xs text-gray-500 mb-2 italic">This field contains multiple items (Repeater). Dynamic editing for this list is coming soon. Currently editable via JSON (Optional).</p>
                                            <textarea name="content[{{ $key }}]" id="content_{{ $key }}" rows="8" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs font-mono">{{ json_encode($value, JSON_PRETTY_PRINT) }}</textarea>
                                        </div>
                                    @elseif(str_contains($key, 'image'))
                                        <div class="flex items-center gap-4">
                                            @if($value)
                                                <div class="w-32 h-20 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                                    <img src="{{ asset($value) }}" alt="Preview" class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                            <input type="file" name="content[{{ $key }}]" id="content_{{ $key }}" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        </div>
                                        <input type="hidden" name="content_current[{{ $key }}]" value="{{ $value }}">
                                    @elseif(str_contains($key, 'description') || str_contains($key, 'subheadline') || str_contains($key, 'headline'))
                                        <textarea name="content[{{ $key }}]" id="content_{{ $key }}" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $value }}</textarea>
                                    @else
                                        <input type="text" name="content[{{ $key }}]" id="content_{{ $key }}" value="{{ $value }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-6">

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Section Visibility</label>
                            <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="published" {{ $homeSection->status === 'published' ? 'selected' : '' }}>Published (Visible)</option>
                                <option value="draft" {{ $homeSection->status === 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                            </select>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-bold text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg shadow-indigo-200">
                                Update Section Content
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
