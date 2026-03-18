<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <x-seo-tags />
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
        @stack('scripts')
    </body>
</html>
