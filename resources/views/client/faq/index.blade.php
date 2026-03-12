<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Knowledge Base / FAQ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    
                    <div class="text-center mb-10">
                        <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">How can we help you?</h3>
                        <p class="mt-4 text-lg text-gray-500">Browse our frequently asked questions to find quick answers about our services, processes, and tools.</p>
                    </div>

                    @forelse($faqsByCategory as $category => $faqs)
                        <div class="mb-10">
                            <h4 class="text-xl font-bold text-indigo-600 mb-4 pb-2 border-b border-gray-200 capitalize">
                                {{ $category ?? 'General' }}
                            </h4>
                            
                            <div class="space-y-4">
                                @foreach($faqs as $index => $faq)
                                    <div x-data="{ expanded: false }" class="border border-gray-200 rounded-lg bg-gray-50 hover:bg-white transition-colors duration-200">
                                        <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-5 focus:outline-none">
                                            <span class="font-semibold text-gray-800 text-left text-lg">{{ $faq->question }}</span>
                                            <span class="ml-4 flex-shrink-0 text-gray-500">
                                                <svg x-show="!expanded" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                                <svg x-show="expanded" style="display: none;" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            </span>
                                        </button>
                                        
                                        <div x-show="expanded" 
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 -translate-y-2"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 translate-y-0"
                                             x-transition:leave-end="opacity-0 -translate-y-2"
                                             class="p-5 border-t border-gray-200 bg-white rounded-b-lg" 
                                             style="display: none;">
                                            <div class="prose prose-indigo max-w-none text-gray-600">
                                                {!! $faq->answer !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No FAQs available yet.</h3>
                            <p class="mt-1 text-sm text-gray-500">Please check back later or contact support.</p>
                        </div>
                    @endforelse

                    <div class="mt-12 text-center bg-indigo-50 rounded-xl p-8 border border-indigo-100">
                        <h4 class="text-lg font-bold text-gray-900 mb-2">Still need help?</h4>
                        <p class="text-gray-600 mb-6">Our support team is ready to assist you with any questions you have.</p>
                        <a href="{{ route('client.support.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Open Support Ticket
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
