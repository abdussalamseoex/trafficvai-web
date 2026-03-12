<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog - {{ config('app.name', 'TrafficVai') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @php $favicon = \App\Models\Setting::get('site_favicon'); @endphp
    @if($favicon)
    <link rel="icon" type="image/x-icon" href="{{ asset($favicon) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 flex flex-col min-h-screen">
    
    <!-- Navigation -->
    <x-frontend-header />


    <!-- Hero Section -->
    <x-page-hero
        badge="SEO Insights & News"
        title="Insights &amp; Updates"
        description="The latest SEO strategies, industry trends, and platform updates to help you rank higher and grow faster."
    />

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full">
        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                @foreach($posts as $post)
                <article class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 flex flex-col h-full">
                    <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-video bg-gray-100 overflow-hidden relative group">
                        @if($post->featured_image)
                            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-indigo-50">
                                <svg class="w-12 h-12 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-300"></div>
                    </a>
                    
                    <div class="p-8 flex flex-col flex-grow">
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            @if($post->category)
                                <span class="font-medium text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full text-xs">{{ $post->category->name }}</span>
                            @else
                                <span class="font-medium text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full text-xs">Article</span>
                            @endif
                            <span class="mx-2">â€¢</span>
                            <time datetime="{{ $post->created_at->format('Y-m-d') }}">{{ $post->created_at->format('M d, Y') }}</time>
                        </div>
                        
                        <a href="{{ route('blog.show', $post->slug) }}" class="block group">
                            <h2 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors mb-3 line-clamp-2">
                                {{ $post->title }}
                            </h2>
                        </a>
                        
                        <p class="text-gray-600 line-clamp-3 mb-6 flex-grow">
                            {{ $post->meta_description ?? strip_tags($post->content) }}
                        </p>
                        
                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-800 transition">
                                Read Article
                                <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            
            <div class="mt-16">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm max-w-3xl mx-auto">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Articles Yet</h3>
                <p class="text-gray-500">We're currently writing some amazing content. Check back soon!</p>
            </div>
        @endif
        
    </main>

    <x-frontend-footer />
</body>
</html>
