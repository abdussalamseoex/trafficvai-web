<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @php 
            $favicon = \App\Models\Setting::get('site_favicon');
            $faviconUrl = $favicon ? Storage::disk('public')->url(str_replace('storage/', '', $favicon)) : null;
        @endphp
        @if($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}?v={{ file_exists(public_path(str_replace('storage/', '', $favicon))) ? filemtime(public_path(str_replace('storage/', '', $favicon))) : '1' }}">
        @endif


        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-blue-600">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="TrafficVai" class="h-16 w-auto object-contain drop-shadow-md mb-4" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-2xl border-t-8 border-brand">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">Welcome to TrafficVai</h2>
                    <p class="text-sm font-medium text-gray-500 mt-2">Sign in to manage your growth</p>
                </div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
