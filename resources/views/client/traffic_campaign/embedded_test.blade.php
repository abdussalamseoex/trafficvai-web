<x-app-layout>
    <div class="min-h-screen bg-gray-50 text-gray-900 py-12 relative overflow-hidden transition-colors duration-300">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-10 left-1/4 w-96 h-96 bg-brand/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 right-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header Section -->
            <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-b border-gray-200 pb-8">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-500/10 text-purple-600 border border-purple-500/20">White-Label Embedded Preview</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Embedded Client Dashboard Test</h1>
                    <p class="text-gray-600 mt-2 text-sm sm:text-base">Testing the new iframe-based core automation engine UI directly from the core server.</p>
                </div>
            </div>

            <div class="rounded-3xl bg-white border border-gray-200 shadow-xl overflow-hidden p-0 sm:p-2">
                <iframe 
                  src="{{ config('services.surf_engine.url', 'https://surf.abguestpost.net') }}/dashboard?apikey={{ $apiKey }}&embedded=true&points={{ $points }}" 
                  width="100%" 
                  height="1100px" 
                  style="border: none; background: transparent; min-height: 1100px;"
                  title="Embedded Client Dashboard"
                ></iframe>
            </div>
        </div>
    </div>
</x-app-layout>
