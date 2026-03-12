<x-frontend-layout>
    @if($page->hero_badge || $page->hero_description)
        <x-page-hero
            :badge="$page->hero_badge"
            :title="$page->title"
            :description="$page->hero_description"
        />
    @endif

    <main class="py-16 md:py-24">
        <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(!$page->hero_badge && !$page->hero_description)
            <header class="mb-12 border-b border-gray-100 pb-8">
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">
                    {{ $page->title }}
                </h1>
                <p class="mt-4 text-sm text-gray-500">
                    Last updated on {{ $page->updated_at->format('F d, Y') }}
                </p>
            </header>
            @endif

            <div class="prose prose-lg prose-indigo max-w-none text-gray-600 leading-relaxed font-serif">
                {!! $page->content !!}
            </div>

            @if($page->slug === 'contact')
                @include('pages.partials.contact-form')
            @endif
            
        </article>
    </main>

</x-frontend-layout>
