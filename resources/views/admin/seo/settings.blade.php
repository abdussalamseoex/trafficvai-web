<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-gray-900 leading-tight">
            SEO Global Settings
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('admin.seo.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <h4 class="font-black text-gray-900 border-b border-gray-100 pb-2">Site Defaults</h4>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Global Site Name</label>
                                <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl p-4">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Twitter Handle</label>
                                <input type="text" name="twitter_handle" value="{{ old('twitter_handle', $settings->twitter_handle) }}" placeholder="@username" class="w-full bg-gray-50 border-gray-200 rounded-xl p-4">
                            </div>
                        </div>
                        <div class="space-y-6">
                            <h4 class="font-black text-gray-900 border-b border-gray-100 pb-2">Default Media</h4>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Default OG Image</label>
                                @if($settings->default_og_image)
                                    <img src="{{ asset('storage/' . $settings->default_og_image) }}" class="w-full h-32 object-cover rounded-2xl mb-4 border border-gray-100">
                                @endif
                                <input type="file" name="default_og_image" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-gray-100 file:text-gray-600">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-4 bg-indigo-600 text-white text-sm font-black rounded-2xl shadow-xl shadow-indigo-900/20 hover:bg-indigo-700 transition-all uppercase tracking-widest">
                            Update General Settings
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-black text-gray-900">Analytics & External Scripts</h3>
                </div>
                <form action="{{ route('admin.seo.analytics.update') }}" method="POST" class="p-8 space-y-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Google Analytics Property ID</label>
                            <input type="text" name="ga_code" value="{{ old('ga_code', $settings->ga_code) }}" placeholder="G-XXXXXXXXXX" class="w-full bg-gray-50 border-gray-200 rounded-xl p-4">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Google Search Console Verification Tag</label>
                            <input type="text" name="gsc_verification" value="{{ old('gsc_verification', $settings->gsc_verification) }}" placeholder='<meta name="google-site-verification" content="..." />' class="w-full bg-gray-50 border-gray-200 rounded-xl p-4">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Custom Header Scripts (Inside &lt;head&gt;)</label>
                        <textarea name="header_scripts" rows="5" class="w-full bg-gray-900 border-gray-800 rounded-xl font-mono text-xs text-blue-400 p-6 leading-relaxed">{{ old('header_scripts', $settings->header_scripts) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Custom Footer Scripts (Before &lt;/body&gt;)</label>
                        <textarea name="footer_scripts" rows="5" class="w-full bg-gray-900 border-gray-800 rounded-xl font-mono text-xs text-blue-400 p-6 leading-relaxed">{{ old('footer_scripts', $settings->footer_scripts) }}</textarea>
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-8 py-4 bg-blue-600 text-white text-sm font-black rounded-2xl shadow-xl shadow-blue-900/20 hover:bg-blue-700 transition-all uppercase tracking-widest">
                            Update Analytics & Scripts
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-black text-gray-900">Search Assets</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div>
                        <h4 class="font-black text-gray-900 text-sm uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Sitemap (XML)</h4>
                        <p class="text-sm text-gray-500 mb-4 leading-relaxed">Your sitemap is automatically generated at <code class="bg-gray-100 px-1 rounded text-red-500">/sitemap.xml</code></p>
                        <a href="/sitemap.xml" target="_blank" class="inline-flex items-center text-indigo-600 font-bold hover:underline">
                            View Live Sitemap <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </div>
                    
                    <div class="pt-6 border-t border-gray-100">
                        <h4 class="font-black text-gray-900 text-sm uppercase tracking-widest border-b border-gray-100 pb-2 mb-4">Robots.txt</h4>
                        <p class="text-sm text-gray-500 mb-4 leading-relaxed">Manage your search engine crawler directives.</p>
                        <a href="{{ route('admin.seo.robots') }}" class="inline-flex items-center text-indigo-600 font-bold hover:underline">
                            Edit Robots.txt <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
