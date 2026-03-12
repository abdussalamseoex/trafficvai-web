@php
    $c = $section->content;
@endphp
<div class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-base font-semibold leading-7 text-blue-600 uppercase tracking-widest">{{ $c['super_title'] ?? 'Our Solutions' }}</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $c['headline'] ?? 'Comprehensive Digital Authority' }}</p>
            <p class="mt-6 text-lg leading-8 text-gray-600">{{ $c['subheadline'] ?? 'From fully-managed SEO to high-traffic guest placements, we provide the raw ranking power your brand needs.' }}</p>
        </div>
        <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
            <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-4">
                @foreach($c['cards'] ?? [] as $card)
                <div class="flex flex-col group p-8 rounded-3xl bg-gray-50 hover:bg-white border border-transparent hover:border-blue-100 transition duration-300 transform hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-100/50">
                    <dt class="text-xl font-bold leading-7 text-gray-900 mb-4 group-hover:text-blue-600 transition">
                        {{ $card['title'] }}
                    </dt>
                    <dd class="mt-1 flex flex-auto flex-col text-base leading-7 text-gray-600">
                        <p class="flex-auto">{{ $card['description'] }}</p>
                        <p class="mt-8">
                            <a href="{{ $card['link_url'] }}" class="text-sm font-bold leading-6 text-blue-600 group-hover:text-blue-700 flex items-center gap-2">
                                {{ $card['link_text'] }} <span aria-hidden="true">→</span>
                            </a>
                        </p>
                    </dd>
                </div>
                @endforeach
            </dl>
        </div>
    </div>
</div>
