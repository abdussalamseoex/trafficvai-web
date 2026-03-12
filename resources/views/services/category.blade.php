<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $category->name }} - SEO Services - {{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-2xl font-extrabold text-indigo-600 tracking-tighter">
                            TrafficVai<span class="text-gray-900">SEO</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('services.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Services</a>
                        <a href="{{ route('guest_posts.index') }}" class="text-gray-600 hover:text-gray-900 font-medium transition">Guest Posts</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium">Log in</a>
                            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="mb-8">
                <a href="{{ route('services.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to All Services
                </a>
            </div>

            <div class="text-center mb-16">
                @if($category->parent)
                <div class="mb-4">
                    <a href="{{ route('services.category', $category->parent->slug) }}" class="inline-block bg-indigo-50 text-indigo-700 px-4 py-1.5 rounded-full text-sm font-bold uppercase tracking-wider hover:bg-indigo-100 transition">
                        {{ $category->parent->name }}
                    </a>
                </div>
                @endif
                <h1 class="text-5xl font-extrabold tracking-tight text-gray-900 sm:text-6xl mb-4">
                    {{ $category->name }}
                </h1>
                @if($category->description)
                <p class="max-w-2xl mx-auto text-xl text-gray-500">
                    {{ $category->description }}
                </p>
                @endif
            </div>

            @if($category->children->count() > 0)
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Explore Subcategories</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($category->children as $child)
                    <a href="{{ route('services.category', $child->slug) }}" class="block bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-indigo-100 transition-all duration-300 group">
                        <h4 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 mb-2">{{ $child->name }}</h4>
                        @if($child->description)
                            <p class="text-gray-500 line-clamp-2">{{ $child->description }}</p>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            <div class="space-y-8">
                @if($category->services->count() > 0)
                    @foreach ($category->services as $service)
                    <div class="bg-white rounded-[2rem] p-8 md:p-12 shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-500 group">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                            <div class="max-w-xl">
                                <h3 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 group-hover:text-indigo-600 transition">{{ $service->name }}</h3>
                                <p class="text-gray-500 text-lg leading-relaxed">{{ \Illuminate\Support\Str::limit($service->description, 150) }}</p>
                                
                                <div class="mt-8 flex flex-wrap gap-3">
                                    @if($service->packages->count() > 0)
                                    <span class="bg-indigo-50 text-indigo-700 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">Starting from ${{ number_format($service->packages->min('price'), 0) }}</span>
                                    <span class="bg-green-50 text-green-700 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">{{ $service->packages->count() }} Multi-Tier Plans</span>
                                    @endif
                                    @if($service->addons && $service->addons->count() > 0)
                                    <span class="bg-purple-50 text-purple-700 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">+{{ $service->addons->count() }} Optional Addons</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="shrink-0">
                                <a href="{{ route('services.show', $service) }}" class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-black text-lg px-10 py-5 rounded-2xl transition duration-300 shadow-xl shadow-indigo-100 group-hover:scale-105 active:scale-95">
                                    View Packages
                                    <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    @if($category->children->count() == 0)
                    <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-gray-100">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="text-xl font-medium text-gray-900">No Services in this Category</h3>
                        <p class="mt-2 text-gray-500">Coming soon. Check back later!</p>
                    </div>
                    @endif
                @endif
            </div>
        </main>
    </div>
    <x-currency-script />
</body>
</html>
