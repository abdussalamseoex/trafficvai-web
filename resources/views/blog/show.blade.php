<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-seo-tags />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-white">
    
    <!-- Navigation -->
    <x-frontend-header />

    <main class="py-12 md:py-20">
        <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <header class="text-center mb-12">
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500 font-medium mb-6">
                    <a href="{{ route('blog.index') }}" class="hover:text-indigo-600 transition">Blog</a>
                    <span>/</span>
                    <span>Article</span>
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-tight mb-8">
                    {{ $post->title }}
                </h1>
                
                <div class="flex items-center justify-center space-x-4">
                    @if($post->category)
                    <div class="flex items-center text-sm font-semibold text-indigo-600 bg-indigo-50 px-4 py-2 rounded-full">
                        {{ $post->category->name }}
                    </div>
                    @endif
                    <div class="flex items-center text-sm text-gray-500 bg-gray-50 px-4 py-2 rounded-full">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <time datetime="{{ $post->created_at->format('Y-m-d') }}">
                            Published on {{ $post->created_at->format('F d, Y') }}
                        </time>
                    </div>
                </div>
            </header>

            @if($post->featured_image)
            <figure class="mb-16 rounded-3xl overflow-hidden shadow-2xl border border-gray-100 aspect-video bg-gray-50">
                <img src="{{ Storage::disk('public')->url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </figure>
            @endif

            <div class="prose prose-lg md:prose-xl prose-indigo max-w-none text-gray-600 leading-relaxed font-serif">
                {!! $post->content !!}
            </div>
            
        </article>
    </main>

    @if($relatedPosts->count() > 0)
    <section class="bg-gray-50 py-20 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-10 text-center">Read Next</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($relatedPosts as $related)
                <a href="{{ route('blog.show', $related->slug) }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col h-full">
                    <div class="aspect-video bg-gray-100 overflow-hidden relative">
                        @if($related->featured_image)
                            <img src="{{ Storage::disk('public')->url($related->featured_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-indigo-50">
                                <svg class="w-10 h-10 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <time class="text-xs text-gray-500 font-medium mb-2">{{ $related->created_at->format('M d, Y') }}</time>
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2">{{ $related->title }}</h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="bg-indigo-600 py-16">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-6">Ready to rank higher on Google?</h2>
            <p class="text-indigo-100 mb-8 text-lg">Our expert SEO services are designed to drive targeted traffic to your site.</p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('services.index') }}" class="bg-white text-indigo-600 font-bold px-8 py-4 rounded-xl shadow-lg hover:bg-gray-50 transition transform hover:scale-105">
                    View SEO Services
                </a>
                <a href="{{ route('contact') }}" class="bg-indigo-700 text-white border border-indigo-500 font-bold px-8 py-4 rounded-xl shadow-lg hover:bg-indigo-800 transition">
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    <x-frontend-footer />
</body>
</html>
