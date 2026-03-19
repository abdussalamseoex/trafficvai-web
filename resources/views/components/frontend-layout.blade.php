<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-seo-tags />

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
