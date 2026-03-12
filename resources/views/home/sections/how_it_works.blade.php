@php
    $c = $section->content;
@endphp
<div class="bg-gray-50 py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <h2 class="text-base font-semibold leading-7 text-blue-600 uppercase tracking-widest">{{ $c['super_title'] ?? 'Process' }}</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $c['headline'] ?? 'How We Rank Your Site' }}</p>
        </div>
        <div class="mx-auto grid max-w-2xl grid-cols-1 gap-8 overflow-hidden lg:mx-0 lg:max-w-none lg:grid-cols-3">
            @foreach($c['steps'] ?? [] as $step)
            <div class="relative p-10 bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 group hover:border-blue-100 transition duration-300">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 text-white font-black text-xl mb-6 group-hover:scale-110 transition">
                    {{ $step['number'] }}
                </div>
                <h3 class="text-xl font-bold leading-7 text-gray-900 mb-4">{{ $step['title'] }}</h3>
                <p class="text-base leading-7 text-gray-600">{{ $step['description'] }}</p>
                <div class="absolute -bottom-2 -right-2 w-16 h-16 bg-blue-50 rounded-tl-3xl -z-10 group-hover:bg-blue-100 transition"></div>
            </div>
            @endforeach
        </div>
    </div>
</div>
