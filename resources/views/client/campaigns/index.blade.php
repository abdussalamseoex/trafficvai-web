<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($title . ' Packages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-12">
                <p class="text-xl text-gray-500">
                    Choose from our result-driven {{ strtolower($title) }} packages.
                </p>
            </div>
            
            @foreach ($categories as $category)
                @if($category->services->count() > 0)
                <div class="mb-16">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-6 tracking-tight">{{ $category->name }}</h2>
                    @if($category->description)
                        <p class="text-gray-500 mb-8">{{ $category->description }}</p>
                    @endif
                    <div class="space-y-6">
                        @foreach ($category->services as $service)
                        <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="max-w-xl">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-indigo-600 transition">{{ $service->name }}</h3>
                                    <p class="text-gray-500 leading-relaxed">{{ Str::limit($service->description, 120) }}</p>
                                    
                                    <div class="mt-6 flex flex-wrap gap-2">
                                        @if($service->packages->count() > 0)
                                        <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Starting from <span class="price-convert" data-base-price="{{ $service->packages->min('price') }}">${{ number_format($service->packages->min('price'), 0) }}</span></span>
                                        <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">{{ $service->packages->count() }} Plans</span>
                                        @endif
                                        @if($service->addons && $service->addons->count() > 0)
                                        <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">+{{ $service->addons->count() }} Addons</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="shrink-0 mt-6 md:mt-0">
                                    <a href="{{ route('client.campaigns.show', ['type' => $type, 'service' => $service->slug]) }}" class="inline-flex items-center justify-center w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition duration-150 shadow-md">
                                        View Details
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            @if ($uncategorizedServices->count() > 0)
                <div class="mb-16">
                    @if($categories->count() > 0)
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-6 tracking-tight">Other Packages</h2>
                    @endif
                    <div class="space-y-6">
                        @foreach ($uncategorizedServices as $service)
                        <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="max-w-xl">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-indigo-600 transition">{{ $service->name }}</h3>
                                    <p class="text-gray-500 leading-relaxed">{{ Str::limit($service->description, 120) }}</p>
                                    
                                    <div class="mt-6 flex flex-wrap gap-2">
                                        @if($service->packages->count() > 0)
                                        <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Starting from <span class="price-convert" data-base-price="{{ $service->packages->min('price') }}">${{ number_format($service->packages->min('price'), 0) }}</span></span>
                                        <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">{{ $service->packages->count() }} Plans</span>
                                        @endif
                                        @if($service->addons && $service->addons->count() > 0)
                                        <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">+{{ $service->addons->count() }} Addons</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="shrink-0 mt-6 md:mt-0">
                                    <a href="{{ route('client.campaigns.show', ['type' => $type, 'service' => $service->slug]) }}" class="inline-flex items-center justify-center w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition duration-150 shadow-md">
                                        View Details
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
