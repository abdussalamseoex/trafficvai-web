<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' . ($global_settings['site_name'] ?? config('app.name')) : ($global_settings['home_seo_title'] ?? config('app.name')) }}</title>
    <meta name="description" content="{{ $description ?? ($global_settings['home_seo_description'] ?? '') }}">
    
    <!-- OpenGraph Meta Tags -->
    <meta property="og:title" content="{{ isset($title) ? $title . ' - ' . ($global_settings['site_name'] ?? config('app.name')) : ($global_settings['home_seo_title'] ?? config('app.name')) }}">
    <meta property="og:description" content="{{ $description ?? ($global_settings['home_seo_description'] ?? '') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    @php 
        $favicon = \App\Models\Setting::get('site_favicon');
        $faviconUrl = $favicon ? Storage::disk('public')->url(str_replace('storage/', '', $favicon)) : null;
    @endphp
    @if($faviconUrl)
    <link rel="icon" href="{{ $faviconUrl }}?v={{ file_exists(public_path(str_replace('storage/', '', $favicon))) ? filemtime(public_path(str_replace('storage/', '', $favicon))) : '1' }}">
    @endif

    <!-- Fonts & Styles -->
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 flex flex-col min-h-screen">
    <x-frontend-header />

    <main class="flex-grow w-full">
        {{ $slot }}
    </main>
    <x-frontend-footer />
    <x-currency-script />
</body>
</html>
