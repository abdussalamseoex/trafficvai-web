<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-black text-gray-900 leading-tight">
                Manage Robots.txt
            </h2>
            <a href="{{ route('admin.seo.settings') }}" class="text-sm font-bold text-gray-400 hover:text-gray-600 flex items-center transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Global Settings
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-100">
            <h3 class="text-xl font-black text-gray-900">Direct Crawler Configuration</h3>
            <p class="text-gray-500 text-sm mt-1">Carefully edit your robots.txt content below. Errors here can impact search visibility.</p>
        </div>
        <form action="{{ route('admin.seo.robots.update') }}" method="POST" class="p-8">
            @csrf
            <div>
                <textarea name="robots_txt" rows="15" class="w-full bg-gray-900 border-gray-800 rounded-2xl font-mono text-sm text-yellow-500 p-8 leading-relaxed focus:ring-0">{{ old('robots_txt', $settings->robots_txt ?? "User-agent: *\nAllow: /") }}</textarea>
            </div>
            
            <div class="mt-8 flex items-center justify-between bg-blue-50 p-6 rounded-2xl border border-blue-100">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg mr-4">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-xs text-blue-700 font-medium">Your changes will be immediately reflected at <a href="/robots.txt" target="_blank" class="underline font-bold">/robots.txt</a></p>
                </div>
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white text-sm font-black rounded-xl shadow-lg shadow-indigo-900/20 hover:bg-indigo-700 transformation hover:-translate-y-0.5 transition-all uppercase tracking-widest">
                    Save Robots.txt
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
