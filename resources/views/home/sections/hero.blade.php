@php
    $c = $section->content;
@endphp
<div class="relative overflow-hidden bg-white pt-16">
    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
        <div class="relative left-[calc(50%-11rem)] aspect-1155/678 w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-orange-200 to-blue-400 opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>

    <div class="mx-auto max-w-7xl px-6 lg:px-8 pb-24 pt-10 sm:pb-32 lg:flex lg:px-8 lg:pt-20">
        <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-xl lg:flex-shrink-0 lg:pt-8 text-center lg:text-left">
            @if($c['badge_text'] ?? false)
            <div class="mt-10 sm:mt-16 lg:mt-6">
                <a href="{{ $c['badge_link'] ?? '#' }}" class="inline-flex space-x-6">
                    <span class="rounded-full bg-blue-500/10 px-3 py-1 text-sm font-semibold leading-6 text-blue-600 ring-1 ring-inset ring-orange-500/20">{{ $c['badge_text'] }}</span>
                    @if($c['badge_subtext'] ?? false)
                    <span class="inline-flex items-center space-x-2 text-sm font-medium leading-6 text-gray-600">
                        <span>{{ $c['badge_subtext'] }}</span>
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                    </span>
                    @endif
                </a>
            </div>
            @endif
            
            <h1 class="mt-10 text-4xl font-extrabold tracking-tight text-blue-600 sm:text-6xl md:text-7xl leading-[1.1] mb-8">
                {!! $c['headline'] ?? 'Dominant SEO & <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-blue-600">Premium Links</span>' !!}
            </h1>
            <p class="mt-6 text-lg leading-8 text-gray-600 max-w-lg mx-auto lg:mx-0">
                {{ $c['subheadline'] ?? 'Propel your organic visibility with white-hat, contextual link building and elite guest posts on real publisher websites. We build authority that sticks.' }}
            </p>
            <div class="mt-10 flex items-center justify-center lg:justify-start gap-x-6">
                @if($c['primary_button_text'] ?? false)
                <a href="{{ $c['primary_button_link'] ?? '#' }}" class="rounded-full bg-blue-600 px-8 py-3.5 text-sm font-bold text-white shadow-xl shadow-blue-200 hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600 transition transform hover:-translate-y-1">
                    {{ $c['primary_button_text'] }}
                </a>
                @endif
                @if($c['secondary_button_text'] ?? false)
                <a href="{{ $c['secondary_button_link'] ?? '#' }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-blue-600 transition flex items-center gap-2">
                    {{ $c['secondary_button_text'] }} <span aria-hidden="true">→</span>
                </a>
                @endif
            </div>
        </div>
        <div class="mx-auto mt-16 lg:mt-8 flex max-w-2xl sm:mt-24 lg:ml-10 lg:mr-0 lg:max-w-none lg:flex-none xl:ml-32">
            <div class="max-w-3xl flex-none sm:max-w-5xl lg:max-w-none relative animate-[float_6s_ease-in-out_infinite]">
                 <img src="{{ asset($c['image'] ?? 'images/hero-seo.png') }}" alt="SEO Analytics Platform" class="w-[20rem] sm:w-[35rem] rounded-3xl opacity-0 md:opacity-100 hidden md:block rotate-[-2deg] transition-all duration-500">
            </div>
        </div>
    </div>
</div>
