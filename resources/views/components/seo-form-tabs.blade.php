@props(['model' => null])

@php
    $seo = $model ? ($model->seoMeta ?? new \App\Models\SeoMeta()) : new \App\Models\SeoMeta();
@endphp

<div x-data="{ seoTab: 'basic' }" class="mt-8 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-indigo-600">
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-lg font-black text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Comprehensive SEO Settings
        </h3>
        <p class="text-xs text-gray-500 mt-1 uppercase tracking-widest font-bold">Manage all 17 SEO fields across 4 tabs</p>
    </div>

    <!-- Tabs Header -->
    <div class="flex border-b border-gray-100 flex-wrap sm:flex-nowrap">
        <button type="button" @click="seoTab = 'basic'" :class="seoTab === 'basic' ? 'bg-white border-indigo-600 text-indigo-600' : 'bg-gray-50 border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-3 px-4 text-[10px] font-black border-b-2 transition-all uppercase tracking-widest">
            1. Basic
        </button>
        <button type="button" @click="seoTab = 'social'" :class="seoTab === 'social' ? 'bg-white border-indigo-600 text-indigo-600' : 'bg-gray-50 border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-3 px-4 text-[10px] font-black border-b-2 transition-all uppercase tracking-widest">
            2. Social
        </button>
        <button type="button" @click="seoTab = 'advanced'" :class="seoTab === 'advanced' ? 'bg-white border-indigo-600 text-indigo-600' : 'bg-gray-50 border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-3 px-4 text-[10px] font-black border-b-2 transition-all uppercase tracking-widest">
            3. Advanced
        </button>
        <button type="button" @click="seoTab = 'schema'" :class="seoTab === 'schema' ? 'bg-white border-indigo-600 text-indigo-600' : 'bg-gray-50 border-transparent text-gray-500 hover:text-gray-700'" class="flex-1 py-3 px-4 text-[10px] font-black border-b-2 transition-all uppercase tracking-widest">
            4. Schema
        </button>
    </div>

    <div class="p-6">
        <!-- Tab 1: Basic SEO -->
        <div x-show="seoTab === 'basic'" class="space-y-5">
            <div>
                <x-input-label value="Meta Title (Max 60 chars)" class="text-[10px] uppercase tracking-tighter" />
                <x-text-input name="meta_title" value="{{ old('meta_title', $seo->meta_title) }}" class="block mt-1 w-full text-sm" placeholder="Search engine title..." />
            </div>
            <div>
                <x-input-label value="Meta Description (Max 160 chars)" class="text-[10px] uppercase tracking-tighter" />
                <textarea name="meta_description" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm" placeholder="Brief summary of the content...">{{ old('meta_description', $seo->meta_description) }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Meta Keywords" class="text-[10px] uppercase tracking-tighter" />
                    <x-text-input name="meta_keywords" value="{{ old('meta_keywords', $seo->meta_keywords) }}" class="block mt-1 w-full text-sm" placeholder="keyword1, keyword2" />
                </div>
                <div>
                    <x-input-label value="Focus Keyword" class="text-[10px] uppercase tracking-tighter" />
                    <x-text-input name="focus_keyword" value="{{ old('focus_keyword', $seo->focus_keyword) }}" class="block mt-1 w-full text-sm" placeholder="Main keyword target" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Custom URL Slug" class="text-[10px] uppercase tracking-tighter text-indigo-600 font-bold" />
                    <x-text-input name="custom_slug" value="{{ old('custom_slug', $seo->custom_slug) }}" class="block mt-1 w-full text-sm border-indigo-200" placeholder="override-default-slug" />
                </div>
                <div>
                    <x-input-label value="Canonical URL" class="text-[10px] uppercase tracking-tighter" />
                    <x-text-input name="canonical_url" value="{{ old('canonical_url', $seo->canonical_url) }}" class="block mt-1 w-full text-sm" placeholder="https://example.com/original-url" />
                </div>
            </div>
        </div>

        <!-- Tab 2: Social Media -->
        <div x-show="seoTab === 'social'" class="space-y-5" style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h4 class="font-black text-gray-900 text-xs border-b pb-1 uppercase tracking-widest text-indigo-600">Facebook (OG)</h4>
                    <div>
                        <x-input-label value="OG Title" class="text-[10px] uppercase" />
                        <x-text-input name="og_title" value="{{ old('og_title', $seo->og_title) }}" class="block mt-1 w-full text-xs" />
                    </div>
                    <div>
                        <x-input-label value="OG Description" class="text-[10px] uppercase" />
                        <textarea name="og_description" rows="2" class="border-gray-300 rounded-md block mt-1 w-full text-xs">{{ old('og_description', $seo->og_description) }}</textarea>
                    </div>
                    <div>
                        <x-input-label value="OG Image" class="text-[10px] uppercase" />
                        @if($seo->og_image)
                            <div class="relative group w-20 h-12 mb-2">
                                <img src="{{ Storage::url($seo->og_image) }}" class="w-full h-full object-cover rounded border shadow-sm">
                            </div>
                        @endif
                        <input type="file" name="og_image" class="block w-full text-[10px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:font-black file:bg-indigo-50 file:text-indigo-600">
                    </div>
                </div>
                <div class="space-y-4">
                    <h4 class="font-black text-gray-900 text-xs border-b pb-1 uppercase tracking-widest text-blue-600">Twitter & Media</h4>
                    <div>
                        <x-input-label value="Image Alt Text" class="text-[10px] uppercase" />
                        <x-text-input name="image_alt_text" value="{{ old('image_alt_text', $seo->image_alt_text) }}" class="block mt-1 w-full text-xs" />
                    </div>
                    <div>
                        <x-input-label value="Twitter Card Type" class="text-[10px] uppercase" />
                        <select class="block mt-1 w-full text-xs border-gray-300 rounded-md bg-gray-50" readonly disabled>
                            <option selected>summary_large_image (Auto)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Advanced -->
        <div x-show="seoTab === 'advanced'" class="space-y-5" style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Robots Directive" class="text-[10px] uppercase tracking-tighter" />
                    <select name="robots_directive" class="block mt-1 w-full text-sm border-gray-300 rounded-md">
                        <option value="index,follow" {{ (old('robots_directive', $seo->robots_directive) == 'index,follow') ? 'selected' : '' }}>index, follow</option>
                        <option value="noindex,nofollow" {{ (old('robots_directive', $seo->robots_directive) == 'noindex,nofollow') ? 'selected' : '' }}>noindex, nofollow</option>
                        <option value="noindex,follow" {{ (old('robots_directive', $seo->robots_directive) == 'noindex,follow') ? 'selected' : '' }}>noindex, follow</option>
                        <option value="index,nofollow" {{ (old('robots_directive', $seo->robots_directive) == 'index,nofollow') ? 'selected' : '' }}>index, nofollow</option>
                    </select>
                </div>
                <div>
                    <x-input-label value="Indexing Eligibility" class="text-[10px] uppercase tracking-tighter" />
                    <select name="robots_index" class="block mt-1 w-full text-sm border-gray-300 rounded-md">
                        <option value="1" {{ old('robots_index', $seo->robots_index) == 1 ? 'selected' : '' }}>🟢 Allow Indexing</option>
                        <option value="0" {{ old('robots_index', $seo->robots_index) == 0 ? 'selected' : '' }}>🔴 Noindex (Hide)</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Breadcrumb Title" class="text-[10px] uppercase tracking-tighter" />
                    <x-text-input name="breadcrumb_title" value="{{ old('breadcrumb_title', $seo->breadcrumb_title) }}" class="block mt-1 w-full text-sm" placeholder="Short name for breadcrumbs" />
                </div>
                <div>
                    <x-input-label value="Publish Date" class="text-[10px] uppercase tracking-tighter" />
                    <x-text-input name="publish_date" type="date" value="{{ old('publish_date', $seo->publish_date ? \Carbon\Carbon::parse($seo->publish_date)->format('Y-m-d') : '') }}" class="block mt-1 w-full text-sm" />
                </div>
            </div>
        </div>

        <!-- Tab 4: Schema -->
        <div x-show="seoTab === 'schema'" class="space-y-5" style="display: none;">
            <div>
                <x-input-label value="Primary Entity Type" class="text-[10px] uppercase tracking-tighter" />
                <select name="schema_type" class="block mt-1 w-full text-sm border-gray-300 rounded-md bg-white">
                    <option value="Auto" {{ (old('schema_type', $seo->schema_type) == 'Auto') ? 'selected' : '' }}>Auto-detect (Recommended)</option>
                    <option value="Article" {{ (old('schema_type', $seo->schema_type) == 'Article') ? 'selected' : '' }}>Article / Blog Post</option>
                    <option value="Service" {{ (old('schema_type', $seo->schema_type) == 'Service') ? 'selected' : '' }}>Service / Product</option>
                    <option value="Organization" {{ (old('schema_type', $seo->schema_type) == 'Organization') ? 'selected' : '' }}>Organization / Brand</option>
                    <option value="FAQPage" {{ (old('schema_type', $seo->schema_type) == 'FAQPage') ? 'selected' : '' }}>FAQ Page</option>
                </select>
            </div>
            <div>
                <x-input-label value="JSON-LD Editor (Advanced Manual Override)" class="text-[10px] uppercase tracking-tighter" />
                <textarea name="schema_json" rows="8" class="bg-gray-900 text-green-400 font-mono text-[10px] rounded-xl block mt-1 w-full p-4" placeholder='{ "@@context": "https://schema.org", "@@type": "WebPage" }'>{{ old('schema_json', $seo->schema_json) }}</textarea>
                <p class="text-[10px] text-gray-500 mt-2 italic font-medium">Auto-generated fallback schema will be used if JSON is left empty.</p>
            </div>
        </div>
    </div>
</div>
