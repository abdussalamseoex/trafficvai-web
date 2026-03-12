@php
    $c = $section->content;
@endphp
<div class="bg-white py-24 sm:py-32 overflow-hidden">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 relative">
         <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50 -z-10"></div>
         <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-orange-50 rounded-full blur-3xl opacity-50 -z-10"></div>
         
        <div class="mx-auto max-w-2xl text-center mb-16">
            <h2 class="text-base font-semibold leading-7 text-blue-600 uppercase tracking-widest">{{ $c['super_title'] ?? 'Success Stories' }}</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $c['headline'] ?? 'Don\'t just take our word for it' }}</p>
        </div>
        <div class="mx-auto grid max-w-2xl grid-cols-1 lg:mx-0 lg:max-w-none lg:grid-cols-3 gap-8">
            @foreach($c['items'] ?? [] as $item)
            <div class="flex flex-col justify-between bg-white p-10 shadow-2xl shadow-gray-200/50 rounded-3xl border border-gray-100 hover:border-blue-100 transition duration-300 transform hover:-translate-y-2">
                <blockquote class="text-lg leading-8 text-gray-700 italic">
                    <p>“{{ $item['quote'] }}”</p>
                </blockquote>
                <div class="mt-8 flex items-center gap-x-4">
                    <div class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                        {{ $item['initials'] }}
                    </div>
                    <div class="text-sm leading-6">
                        <p class="font-bold text-gray-900">{{ $item['name'] }}</p>
                        <p class="text-gray-500">{{ $item['role'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
