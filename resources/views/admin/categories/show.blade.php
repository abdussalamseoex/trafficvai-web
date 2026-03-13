<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Category: {{ $category->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    @if($category->parent)
                        <span class="text-indigo-600 font-medium">{{ $category->parent->name }}</span> &rsaquo;
                    @endif
                    /{{ $category->slug }}
                    &bull;
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $category->type === 'post' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ $category->type === 'post' ? 'Blog Post' : 'Service' }}
                    </span>
                    &bull;
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.categories.edit', $category) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    Edit Category
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                    &larr; All Categories
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Description --}}
            @if($category->description)
            <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</h3>
                <p class="text-gray-700 text-sm leading-relaxed">{{ $category->description }}</p>
            </div>
            @endif

            {{-- Sub-categories --}}
            @if($category->children->count() > 0)
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Sub-categories
                        <span class="ml-2 bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $category->children->count() }}</span>
                    </h3>
                </div>
                <div class="p-6 flex flex-wrap gap-2">
                    @foreach($category->children as $child)
                        <a href="{{ route('admin.categories.show', $child) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-md border border-indigo-200 bg-indigo-50 text-indigo-700 text-sm font-medium hover:bg-indigo-100 transition">
                            {{ $child->name }}
                            <span class="ml-1.5 text-indigo-400 text-xs">/{{ $child->slug }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Services assigned --}}
            @if($category->type === 'service')
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Assigned Services
                        <span class="ml-2 bg-purple-100 text-purple-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $category->services->count() }}</span>
                    </h3>
                </div>
                @if($category->services->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($category->services as $service)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $service->name }}</div>
                                    <div class="text-xs text-gray-400">/{{ $service->slug }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ ucfirst(str_replace('_', ' ', $service->service_type ?? 'Standard')) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="px-6 py-10 text-center">
                    <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-sm text-gray-500">No services assigned to this category yet.</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Blog posts assigned --}}
            @if($category->type === 'post')
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Assigned Blog Posts
                        <span class="ml-2 bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $category->posts->count() }}</span>
                    </h3>
                </div>
                @if($category->posts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($category->posts as $post)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $post->title }}</div>
                                    <div class="text-xs text-gray-400">/{{ $post->slug }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $post->is_active ?? true ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ($post->is_active ?? true) ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if(Route::has('admin.posts.edit'))
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-gray-500">No posts assigned to this category yet.</p>
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
