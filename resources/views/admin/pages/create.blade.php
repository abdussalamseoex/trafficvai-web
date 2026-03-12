<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.pages.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Dynamic Page') }}
            </h2>
        </div>
    </x-slot>

    <!-- Trix Editor Dependencies -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <style>
        trix-editor { min-height: 500px; background: white; }
    </style>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('admin.pages.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            
                            <!-- Left: Content editor -->
                            <div class="md:col-span-2 space-y-6">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">Page Title <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-lg border-gray-300 rounded-md" placeholder="e.g. Privacy Policy">
                                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content <span class="text-red-500">*</span></label>
                                    <input id="content" type="hidden" name="content" value="{{ old('content') }}">
                                    <trix-editor input="content" class="trix-content focus:ring-indigo-500 focus:border-indigo-500 rounded-md border-gray-300 shadow-sm prose max-w-none prose-indigo"></trix-editor>
                                    @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            
                            <!-- Right: Meta and settings -->
                            <div class="space-y-6 bg-gray-50 p-5 rounded-xl border border-gray-100">
                                
                                <div>
                                    <label for="is_active" class="flex items-center mt-2">
                                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600">Publish Page (Active)</span>
                                    </label>
                                    @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <hr class="border-gray-200">

                                <!-- Hero Section Settings -->
                                <div class="space-y-4">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hero Section (Optional)</p>
                                    
                                    <div>
                                        <label for="hero_badge" class="block text-sm font-medium text-gray-700">Hero Badge</label>
                                        <input type="text" name="hero_badge" id="hero_badge" value="{{ old('hero_badge') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md" placeholder="e.g. Our Story">
                                        @error('hero_badge') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="hero_description" class="block text-sm font-medium text-gray-700">Hero Description</label>
                                        <textarea name="hero_description" id="hero_description" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm text-sm border-gray-300 rounded-md" placeholder="Short intro text for the hero section">{{ old('hero_description') }}</textarea>
                                        @error('hero_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                
                                <hr class="border-gray-200">
                                
                                <x-seo-form-tabs />

                            </div>
                        </div>

                        <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end">
                            <a href="{{ route('admin.pages.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-8 border border-transparent shadow-sm text-sm font-bold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save Page
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
