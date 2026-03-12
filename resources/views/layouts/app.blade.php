<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $seo['title'] }}</title>
        <meta name="description" content="{{ $seo['description'] }}">
        @if($seo['keywords'])
        <meta name="keywords" content="{{ $seo['keywords'] }}">
        @endif
        <link rel="canonical" href="{{ $seo['canonical'] }}">
        <meta name="robots" content="{{ $seo['robots'] }}">

        @php $favicon = \App\Models\Setting::get('site_favicon'); @endphp
        @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset($favicon) }}">
        @endif


        <!-- Open Graph -->
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $seo['og']['title'] }}">
        <meta property="og:description" content="{{ $seo['og']['description'] }}">
        <meta property="og:url" content="{{ $seo['canonical'] }}">
        <meta property="og:site_name" content="{{ $seo['og']['site_name'] }}">
        @if($seo['og']['image'])
        <meta property="og:image" content="{{ $seo['og']['image'] }}">
        @endif

        <!-- Twitter -->
        <meta name="twitter:card" content="{{ $seo['twitter']['card'] }}">
        @if($seo['twitter']['site'])
        <meta name="twitter:site" content="{{ $seo['twitter']['site'] }}">
        @endif
        <meta name="twitter:title" content="{{ $seo['og']['title'] }}">
        <meta name="twitter:description" content="{{ $seo['og']['description'] }}">
        @if($seo['og']['image'])
        <meta name="twitter:image" content="{{ $seo['og']['image'] }}">
        @endif

        <!-- Schema markup -->
        @if($seo['schema'])
        <script type="application/ld+json">
            {!! $seo['schema'] !!}
        </script>
        @endif

        <!-- Google Analytics -->
        @if($seo['scripts']['ga'])
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $seo['scripts']['ga'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $seo['scripts']['ga'] }}');
        </script>
        @endif

        <!-- GSC Verification -->
        @if($seo['scripts']['gsc'])
            {!! $seo['scripts']['gsc'] !!}
        @endif

        <!-- Header Scripts -->
        {!! $seo['scripts']['header'] !!}

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="{{ request()->routeIs('client.dashboard') ? 'font-dashboard bg-[#F5F6FA]' : 'font-sans bg-gray-50' }} antialiased text-gray-900" x-data="{ sidebarOpen: false }">
        <div class="flex min-h-screen {{ request()->routeIs('client.dashboard') ? 'bg-[#F5F6FA]' : 'bg-gray-50' }}">
            <!-- Sidebar Desktop/Mobile overlay -->
            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm md:hidden" @click="sidebarOpen = false"></div>
            
            @include('layouts.sidebar')

            <!-- Main Content flex container -->
            <div class="flex flex-col flex-1 w-full min-w-0 md:pl-64 transition-all duration-300">
                @include('layouts.navigation')

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden pt-8 pb-12 px-4 sm:px-6 lg:px-8">
                    <!-- Page Heading -->
                    @isset($header)
                        <header class="mb-8">
                            {{ $header }}
                        </header>
                    @endisset
                    
                    {{ $slot }}
                </main>
            </div>
        </div>

        @if(!auth()->user()->is_admin)
            <x-support-chat-popup />
        @endif

        <!-- Footer Scripts -->
        {!! $seo['scripts']['footer'] !!}
        <x-currency-script />
    </body>
</html>
