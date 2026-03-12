<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-gray-900 leading-tight">
            {{ __('SEO Manager Overview') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($stats as $type => $count)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-indigo-50 rounded-xl">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-3xl font-black text-gray-900">{{ $count }}</span>
            </div>
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest">{{ ucfirst($type) }}</h3>
            <a href="{{ route('admin.seo.' . $type) }}" class="mt-4 inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-700">
                Manage SEO <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-900">SEO Technical Tools</h3>
                        <p class="text-gray-500 text-sm mt-1">Manage sitemaps, robots.txt and redirections</p>
                    </div>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="{{ route('admin.seo.redirects.index') }}" class="flex items-start p-4 rounded-2xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all group">
                        <div class="p-3 bg-blue-50 rounded-xl group-hover:bg-blue-100 transition-colors mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Redirect Manager</h4>
                            <p class="text-xs text-gray-500 mt-1">Setup 301 and 302 forwards</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.seo.settings') }}" class="flex items-start p-4 rounded-2xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all group">
                        <div class="p-3 bg-purple-50 rounded-xl group-hover:bg-purple-100 transition-colors mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Global Config</h4>
                            <p class="text-xs text-gray-500 mt-1">Sitewide SEO settings & defaults</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="space-y-8">
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-3xl p-8 text-white shadow-xl shadow-indigo-200">
                <h3 class="text-xl font-black mb-4">Quick SEO Check</h3>
                <p class="text-indigo-100 text-sm mb-6 leading-relaxed">Ensure your critical pages have Meta Titles and Descriptions for better ranking.</p>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                        <span class="text-xs font-bold uppercase tracking-widest">Sitemap Status</span>
                        <span class="px-2 py-0.5 bg-green-400/20 text-green-300 text-[10px] rounded-full border border-green-400/30 font-bold uppercase">Active</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                        <span class="text-xs font-bold uppercase tracking-widest">Robots.txt</span>
                        <span class="px-2 py-0.5 bg-green-400/20 text-green-300 text-[10px] rounded-full border border-green-400/30 font-bold uppercase">Configured</span>
                    </div>
                </div>
                <button onclick="window.location.href='/sitemap.xml'" class="w-full mt-8 py-3 bg-white text-indigo-600 font-bold rounded-xl shadow-lg shadow-indigo-900/20 transform hover:-translate-y-1 transition-all">
                    View Public Sitemap
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
