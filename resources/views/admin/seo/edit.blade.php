<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-black text-gray-900 leading-tight">
                Edit SEO: {{ $item->title ?? $item->name }}
            </h2>
            <a href="{{ url()->previous() }}" class="text-sm font-bold text-gray-400 hover:text-gray-600 flex items-center transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back
            </a>
        </div>
    </x-slot>

    <div x-data="{ tab: 'basic' }" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Tabs Header -->
        <div class="flex border-b border-gray-100 bg-gray-50/50">
            <button @click="tab = 'basic'" :class="tab === 'basic' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-4 px-6 text-sm font-black border-b-2 transition-all uppercase tracking-widest">
                1. Basic
            </button>
            <button @click="tab = 'social'" :class="tab === 'social' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-4 px-6 text-sm font-black border-b-2 transition-all uppercase tracking-widest">
                2. Social
            </button>
            <button @click="tab = 'advanced'" :class="tab === 'advanced' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-4 px-6 text-sm font-black border-b-2 transition-all uppercase tracking-widest">
                3. Advanced
            </button>
            <button @click="tab = 'schema'" :class="tab === 'schema' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-4 px-6 text-sm font-black border-b-2 transition-all uppercase tracking-widest">
                4. Schema
            </button>
        </div>

        <form action="{{ route('admin.seo.update', ['type' => $type, 'id' => $id]) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf

            <!-- Basic Tab -->
            <div x-show="tab === 'basic'" class="space-y-6">
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $seo->meta_title) }}" placeholder="SEO Optimized Title" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 font-medium p-4">
                    <p class="text-xs text-gray-400 mt-2 italic">Recommended: 50-60 characters.</p>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Meta Description</label>
                    <textarea name="meta_description" rows="4" placeholder="Brief summary of the page content..." class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 font-medium p-4">{{ old('meta_description', $seo->meta_description) }}</textarea>
                    <p class="text-xs text-gray-400 mt-2 italic">Recommended: 150-160 characters.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Meta Keywords</label>
                        <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seo->meta_keywords) }}" placeholder="keywords, separated, by, commas" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 font-medium p-4">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Focus Keyword</label>
                        <input type="text" name="focus_keyword" value="{{ old('focus_keyword', $seo->focus_keyword) }}" placeholder="Primary keyword for this page" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 font-medium p-4">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Canonical URL</label>
                    <input type="text" name="canonical_url" value="{{ old('canonical_url', $seo->canonical_url) }}" placeholder="{{ url($item->slug) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 font-medium p-4">
                </div>
            </div>

            <!-- Social Tab -->
            <div x-show="tab === 'social'" class="space-y-6" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <h4 class="font-black text-gray-900 border-b border-gray-100 pb-2">Open Graph (Facebook)</h4>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">OG Title</label>
                            <input type="text" name="og_title" value="{{ old('og_title', $seo->og_title) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl font-medium p-4">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">OG Description</label>
                            <textarea name="og_description" rows="3" class="w-full bg-gray-50 border-gray-200 rounded-xl font-medium p-4">{{ old('og_description', $seo->og_description) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">OG Image</label>
                            @if($seo->og_image)
                                <img src="{{ asset('storage/' . $seo->og_image) }}" class="w-32 h-20 object-cover rounded-lg mb-2">
                            @endif
                            <input type="file" name="og_image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                        </div>
                    </div>
                    <div class="space-y-6">
                        <h4 class="font-black text-gray-900 border-b border-gray-100 pb-2">Secondary Media</h4>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Featured Image</label>
                            @if($seo->featured_image)
                                <img src="{{ asset('storage/' . $seo->featured_image) }}" class="w-32 h-20 object-cover rounded-lg mb-2">
                            @endif
                            <input type="file" name="featured_image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Alt Text</label>
                            <input type="text" name="image_alt_text" value="{{ old('image_alt_text', $seo->image_alt_text) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl font-medium p-4">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Tab -->
            <div x-show="tab === 'advanced'" class="space-y-6" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Robots Indexing</label>
                        <select name="robots_index" class="w-full bg-gray-50 border-gray-200 rounded-xl font-medium p-4">
                            <option value="1" {{ $seo->robots_index ? 'selected' : '' }}>Index (Allow engines to show this page)</option>
                            <option value="0" {{ !$seo->robots_index ? 'selected' : '' }}>Noindex (Hide from search engines)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Robots Directives</label>
                        <select name="robots_directive" class="w-full bg-gray-50 border-gray-200 rounded-xl font-medium p-4">
                            <option value="index,follow" {{ $seo->robots_directive == 'index,follow' ? 'selected' : '' }}>index, follow</option>
                            <option value="noindex,follow" {{ $seo->robots_directive == 'noindex,follow' ? 'selected' : '' }}>noindex, follow</option>
                            <option value="index,nofollow" {{ $seo->robots_directive == 'index,nofollow' ? 'selected' : '' }}>index, nofollow</option>
                            <option value="noindex,nofollow" {{ $seo->robots_directive == 'noindex,nofollow' ? 'selected' : '' }}>noindex, nofollow</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Breadcrumb Title</label>
                    <input type="text" name="breadcrumb_title" value="{{ old('breadcrumb_title', $seo->breadcrumb_title) }}" placeholder="{{ $item->title ?? $item->name }}" class="w-full bg-gray-50 border-gray-200 rounded-xl font-medium p-4">
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Custom Slug (Advanced)</label>
                    <input type="text" name="slug" value="{{ old('slug', $seo->slug ?? $item->slug) }}" class="w-full bg-gray-50 border-gray-200 rounded-xl font-medium p-4 text-gray-500">
                    <p class="text-[10px] text-red-400 mt-2 font-bold uppercase tracking-widest">Warning: Changing this will auto-generate a 301 redirect.</p>
                </div>
            </div>

            <!-- Schema Tab -->
            <div x-show="tab === 'schema'" class="space-y-6" style="display: none;">
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">JSON-LD Schema Markup</label>
                    <textarea name="schema_json" rows="12" placeholder='{ "@context": "https://schema.org", "@type": "WebPage", ... }' class="w-full bg-gray-900 border-gray-800 rounded-xl font-mono text-xs text-green-400 p-6 leading-relaxed">{{ old('schema_json', $seo->schema_json) }}</textarea>
                    <p class="text-xs text-gray-400 mt-2 italic">Leave empty to use auto-generated schema based on content type.</p>
                </div>
            </div>

            <div class="mt-12 flex items-center justify-end space-x-4 border-t border-gray-100 pt-8">
                <button type="submit" class="px-8 py-4 bg-indigo-600 text-white text-sm font-black rounded-2xl shadow-xl shadow-indigo-900/20 hover:bg-indigo-700 transform hover:-translate-y-1 transition-all uppercase tracking-widest">
                    Save SEO Data
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
