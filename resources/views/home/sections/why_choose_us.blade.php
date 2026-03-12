@php
    $c = $section->content;
@endphp
<div class="bg-gray-50 py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-x-12 gap-y-16 lg:grid-cols-2 lg:items-center">
            <div>
                <h2 class="text-base font-semibold leading-7 text-orange-600 uppercase tracking-widest">{{ $c['super_title'] ?? 'The White-Hat Difference' }}</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl leading-tight">{{ $c['headline'] ?? 'Safe, Sustainable, and Scalable Organic Growth' }}</p>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    {{ $c['description_top'] ?? 'At TrafficVai, we distance ourselves from outdated, high-risk tactics. Our entire methodology is rooted in authentic relationship building and rigorous quality control.' }}
                </p>
                <div class="mt-10 space-y-8">
                    @foreach($c['list_items'] ?? [] as $item)
                    <div class="relative pl-10">
                        <dt class="inline font-bold text-gray-900">
                            <div class="absolute left-0 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-orange-100 ring-1 ring-orange-500/20">
                                <svg class="h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            </div>
                            {{ $item['title'] }}
                        </dt>
                        <dd class="inline text-gray-600 ml-1">{{ $item['text'] }}</dd>
                    </div>
                    @endforeach
                </div>
                <p class="mt-10 text-lg leading-8 text-gray-600">
                    {{ $c['description_bottom'] ?? 'Whether you\'re a boutique agency looking for a reliable white-label partner, or an enterprise brand demanding top-tier placements, our transparent dashboard puts you in full control.' }}
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-4 pt-12">
                    <div class="bg-white p-6 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 transform rotate-[-2deg]">
                        <div class="h-10 w-10 bg-blue-50 rounded-xl flex items-center justify-center mb-4 text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900">Elite Quality</h4>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 transform rotate-[2deg]">
                        <div class="h-10 w-10 bg-orange-50 rounded-xl flex items-center justify-center mb-4 text-orange-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900">Fast Delivery</h4>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-blue-600 p-6 rounded-3xl shadow-xl shadow-blue-200/50 transform rotate-[2deg]">
                        <div class="h-10 w-10 bg-blue-500/30 rounded-xl flex items-center justify-center mb-4 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2m4 0h.01M16 10h2M6 14h2m4 0h2m-6 4h12"></path></svg>
                        </div>
                        <h4 class="font-bold text-white">100% Transparent</h4>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 transform rotate-[-2deg]">
                        <div class="h-10 w-10 bg-green-50 rounded-xl flex items-center justify-center mb-4 text-green-600">
                             <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900">Data Driven</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
