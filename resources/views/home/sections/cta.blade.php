@php
    $c = $section->content;
@endphp
<div class="bg-white">
    <div class="mx-auto max-w-7xl py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="relative isolate overflow-hidden bg-blue-600 px-6 pt-16 shadow-2xl sm:rounded-3xl sm:px-16 md:pt-24 lg:flex lg:gap-x-20 lg:px-24 lg:pt-0">
            <svg viewBox="0 0 1024 1024" class="absolute left-1/2 top-1/2 -z-10 h-[64rem] w-[64rem] -translate-y-1/2 [mask-image:radial-gradient(closest-side,white,transparent)] sm:left-full sm:-ml-80 lg:left-1/2 lg:ml-0 lg:-translate-x-1/2 lg:translate-y-0" aria-hidden="true">
                <circle cx="512" cy="512" r="512" fill="url(#759c1415-0410-454c-8f7c-9a820de03641)" fill-opacity="0.7" />
                <defs>
                    <radialGradient id="759c1415-0410-454c-8f7c-9a820de03641">
                        <stop stop-color="#7775D6" />
                        <stop offset="1" stop-color="#E935C1" />
                    </radialGradient>
                </defs>
            </svg>
            <div class="mx-auto max-w-md text-center lg:mx-0 lg:flex-auto lg:py-32 lg:text-left">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl leading-tight">{{ $c['headline'] ?? 'Ready to outflow your competitors?' }}</h2>
                <p class="mt-6 text-lg leading-8 text-blue-100 italic">{{ $c['subheadline'] ?? 'Create a free account in seconds and get immediate access to our exclusive agency-level outreach and traffic services.' }}</p>
                <div class="mt-10 flex items-center justify-center gap-x-6 lg:justify-start">
                    <a href="{{ $c['button_link'] ?? '#' }}" class="rounded-full bg-white px-8 py-4 text-sm font-bold text-blue-600 shadow-xl hover:bg-blue-50 transition transform hover:-translate-y-1">{{ $c['button_text'] ?? 'Start Growing Today' }}</a>
                    <a href="/contact" class="text-sm font-semibold leading-6 text-white hover:text-blue-100 transition">Contact Sales <span aria-hidden="true">→</span></a>
                </div>
            </div>
            <div class="relative mt-16 h-80 lg:mt-8 flex justify-center lg:justify-end items-center">
                <div class="bg-white/10 backdrop-blur-md p-8 rounded-3xl border border-white/20 shadow-2xl animate-pulse">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-10 w-10 bg-white/20 rounded-full"></div>
                        <div class="h-4 w-32 bg-white/20 rounded-full"></div>
                    </div>
                     <div class="h-4 w-48 bg-white/20 rounded-full mb-2"></div>
                     <div class="h-4 w-40 bg-white/20 rounded-full"></div>
                </div>
            </div>
        </div>
    </div>
</div>
