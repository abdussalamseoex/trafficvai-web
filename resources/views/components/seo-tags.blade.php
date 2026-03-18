@php 
    $favicon = \App\Models\Setting::get('site_favicon');
    $faviconUrl = $favicon ? Storage::disk('public')->url(str_replace('storage/', '', $favicon)) : null;
@endphp
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

@if($faviconUrl)
<link rel="icon" href="{{ $faviconUrl }}?v={{ file_exists(public_path(str_replace('storage/', '', $favicon))) ? filemtime(public_path(str_replace('storage/', '', $favicon))) : '1' }}">
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
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
