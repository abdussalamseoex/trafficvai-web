@props([
    'badge'        => null,
    'title'        => '',
    'description'  => null,
    'ctaLabel'     => null,
    'ctaHref'      => null,
    'ctaScroll'    => null,   // JS element ID to smooth-scroll to
    'cta2Label'    => null,
    'cta2Href'     => null,
    'breadcrumb'   => null,   // ['label' => '...', 'url' => '...'] for parent crumb
])

<section class="w-full">
    <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 relative overflow-hidden">
        {{-- Decorative blobs --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            {{-- Breadcrumb --}}
            @if($breadcrumb)
            <nav class="flex items-center space-x-2 text-sm text-gray-400 mb-8">
                <a href="{{ $breadcrumb['url'] }}" class="hover:text-orange-400 transition-colors">{{ $breadcrumb['label'] }}</a>
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-300 font-medium">{{ $title }}</span>
            </nav>
            @endif

            <div class="text-center max-w-3xl mx-auto">
                {{-- Badge --}}
                @if($badge)
                <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-6">
                    <div class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                    <span class="text-orange-400 text-xs font-bold uppercase tracking-widest">{{ $badge }}</span>
                </div>
                @endif

                {{-- Title --}}
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                    {!! $title !!}
                </h1>

                {{-- Description --}}
                @if($description)
                <p class="text-lg md:text-xl text-gray-300 leading-relaxed mb-10 max-w-2xl mx-auto">
                    {!! $description !!}
                </p>
                @endif

                {{-- CTAs --}}
                @if($ctaLabel || $cta2Label)
                <div class="flex flex-wrap items-center justify-center gap-4">
                    @if($ctaLabel)
                        @if($ctaScroll)
                        <a href="#{{ $ctaScroll }}"
                           onclick="event.preventDefault(); document.getElementById('{{ $ctaScroll }}').scrollIntoView({behavior:'smooth',block:'start'});"
                           class="inline-flex items-center gap-2 bg-[#E8470A] hover:bg-orange-600 text-white font-black px-8 py-4 rounded-2xl transition-all duration-300 shadow-xl shadow-orange-600/30 hover:scale-105 active:scale-95">
                            {!! $ctaLabel !!}
                        </a>
                        @else
                        <a href="{{ $ctaHref ?? '#' }}"
                           class="inline-flex items-center gap-2 bg-[#E8470A] hover:bg-orange-600 text-white font-black px-8 py-4 rounded-2xl transition-all duration-300 shadow-xl shadow-orange-600/30 hover:scale-105 active:scale-95">
                            {!! $ctaLabel !!}
                        </a>
                        @endif
                    @endif

                    @if($cta2Label)
                    <a href="{{ $cta2Href ?? '#' }}"
                       class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold px-8 py-4 rounded-2xl transition-all duration-300 hover:scale-105 active:scale-95 backdrop-blur-sm">
                        {!! $cta2Label !!}
                    </a>
                    @endif
                </div>
                @endif

                {{-- Extra slot for custom content (stats, badges, etc.) --}}
                @if($slot->isNotEmpty())
                <div class="mt-10 pt-10 border-t border-white/10">
                    {{ $slot }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
