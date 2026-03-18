<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Traffic Package') }}: {{ $service->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.traffic.update', $service) }}" enctype="multipart/form-data" x-data="serviceEditForm()">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Info -->
                        <div class="mb-8 border-b pb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">General Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="name" :value="__('Traffic Package Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $service->name)" required autofocus x-on:input="generateSlug($event.target.value)" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="category_id" :value="__('Category')" />
                                    <select id="category_id" name="category_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                        <option value="">-- Select Category (Optional) --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="slug" :value="__('URL Slug')" />
                                    <x-text-input id="slug" class="block mt-1 w-full bg-gray-50" type="text" name="slug" x-model="slug" required />
                                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <x-input-label for="description" :value="__('Main Description')" />
                                <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('description', $service->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            
                            <div class="block mt-4">
                                <label for="is_active" class="inline-flex items-center">
                                    <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Service is active and visible on frontend') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Hero Section -->
                        <div class="mb-8 border-b pb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-1 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Hero Section (Frontend)
                            </h3>
                            <p class="text-sm text-gray-500 mb-5">These fields control the large hero banner at the top of the service page. If both image and video are set, the video takes priority.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="hero_image" value="Hero Image" />
                                    @if($service->hero_image)
                                    <div class="mt-2 mb-3 flex items-center gap-4">
                                        <img src="{{ Storage::disk('public')->url($service->hero_image) }}" alt="Current hero" class="h-24 w-40 object-cover rounded-lg border border-gray-200 shadow-sm">
                                        <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                                            <input type="checkbox" name="remove_hero_image" value="1" class="rounded border-gray-300 text-red-500">
                                            Remove current image
                                        </label>
                                    </div>
                                    @endif
                                    <input id="hero_image" type="file" name="hero_image" accept="image/*" class="block mt-1 w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="text-xs text-gray-400 mt-1">Recommended: 1200×700px, jpg/png/webp, max 4MB.</p>
                                    <x-input-error :messages="$errors->get('hero_image')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="hero_video_url" value="Hero Video URL (optional)" />
                                    <x-text-input id="hero_video_url" class="block mt-1 w-full" type="url" name="hero_video_url" :value="old('hero_video_url', $service->hero_video_url)" placeholder="https://youtube.com/watch?v=... or https://vimeo.com/..." />
                                    <p class="text-xs text-gray-400 mt-1">Supports YouTube, Vimeo, or a direct video URL. Takes priority over image.</p>
                                    <x-input-error :messages="$errors->get('hero_video_url')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="sample_link" value='"Sample / Download" Button Link (optional)' />
                                    <x-text-input id="sample_link" class="block mt-1 w-full" type="url" name="sample_link" :value="old('sample_link', $service->sample_link)" placeholder="https://drive.google.com/... or https://example.com/sample.pdf" />
                                    <p class="text-xs text-gray-400 mt-1">If set, a "Sample / Download" button will appear next to the Order Now button in the hero.</p>
                                    <x-input-error :messages="$errors->get('sample_link')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Fiverr-style Packages -->
                        <div class="mb-10">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Pricing Packages
                            </h3>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <template x-for="(pkg, pkgIndex) in packages" :key="pkgIndex">
                                    <div class="border rounded-2xl p-6 bg-gray-50 relative overflow-hidden">
                                        <div class="absolute top-0 left-0 w-full h-2" :class="{'bg-green-400': pkgIndex === 0, 'bg-indigo-400': pkgIndex === 1, 'bg-purple-400': pkgIndex === 2}"></div>
                                        
                                        <input type="hidden" x-bind:name="'packages['+pkgIndex+'][id]'" x-model="pkg.id">
                                        <input type="hidden" x-bind:name="'packages['+pkgIndex+'][name]'" x-model="pkg.name">
                                        
                                        <h4 class="text-lg font-bold mb-4" x-text="pkg.name"></h4>
                                        
                                        <div class="space-y-4">
                                            <div class="flex space-x-4">
                                                <div class="flex-1">
                                                    <x-input-label x-bind:for="'pkg_price_'+pkgIndex" value="Price ($)" />
                                                    <x-text-input x-bind:id="'pkg_price_'+pkgIndex" x-bind:name="'packages['+pkgIndex+'][price]'" class="block mt-1 w-full" type="number" step="0.01" x-model="pkg.price" required />
                                                </div>
                                                <div class="flex-1">
                                                    <x-input-label x-bind:for="'pkg_emergency_'+pkgIndex" value="Express Fee ($) *" />
                                                    <x-text-input x-bind:id="'pkg_emergency_'+pkgIndex" x-bind:name="'packages['+pkgIndex+'][emergency_fee]'" class="block mt-1 w-full" type="number" step="0.01" x-model="pkg.emergency_fee" placeholder="0.00" />
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 italic -mt-2">* Optional. Additional fee for fast delivery.</p>

                                            <div class="flex space-x-4">
                                                <div class="flex-1">
                                                    <x-input-label x-bind:for="'pkg_turnaround_'+pkgIndex" value="Standard Delivery (Days)" />
                                                    <x-text-input x-bind:id="'pkg_turnaround_'+pkgIndex" x-bind:name="'packages['+pkgIndex+'][turnaround_time_days]'" class="block mt-1 w-full" type="number" min="1" step="1" x-model="pkg.turnaround_time_days" placeholder="e.g. 5" />
                                                </div>
                                                <div class="flex-1">
                                                    <x-input-label x-bind:for="'pkg_express_'+pkgIndex" value="Express Delivery (Days) *" />
                                                    <x-text-input x-bind:id="'pkg_express_'+pkgIndex" x-bind:name="'packages['+pkgIndex+'][express_turnaround_time_days]'" class="block mt-1 w-full" type="number" min="1" step="1" x-model="pkg.express_turnaround_time_days" placeholder="e.g. 2" />
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 italic -mt-2">* Optional. Days needed for express delivery.</p>
                                            
                                            <div>
                                                <x-input-label x-bind:for="'pkg_desc_'+pkgIndex" value="Short Description" />
                                                <textarea x-bind:id="'pkg_desc_'+pkgIndex" x-bind:name="'packages['+pkgIndex+'][description]'" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm" rows="2" x-model="pkg.description"></textarea>
                                            </div>

                                            <div>
                                                <x-input-label value="Features (Bullet Points)" />
                                                <div class="space-y-2 mt-2">
                                                    <template x-for="(feature, fIndex) in pkg.features" :key="fIndex">
                                                        <div class="flex items-center space-x-2">
                                                            <input type="text" x-bind:name="'packages['+pkgIndex+'][features][]'" x-model="pkg.features[fIndex]" class="border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm block w-full text-xs py-1" required>
                                                            <button type="button" @click="removeFeature(pkgIndex, fIndex)" class="text-red-400 hover:text-red-600">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <button type="button" @click="addFeature(pkgIndex)" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                                        + Add Feature
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Addons Section -->
                        <div class="mb-10 p-6 bg-indigo-50 rounded-2xl border border-indigo-100">
                            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Traffic Addons
                            </h3>
                            <p class="text-sm text-gray-600 mb-6">Extra services clients can add to their order.</p>
                            
                            <div class="space-y-4">
                                <template x-for="(addon, aIndex) in addons" :key="aIndex">
                                    <div class="flex flex-wrap md:flex-nowrap items-start space-x-0 md:space-x-4 space-y-4 md:space-y-0 p-4 bg-white border border-indigo-100 rounded-xl shadow-sm relative">
                                        <input type="hidden" x-bind:name="'addons['+aIndex+'][id]'" x-model="addon.id">
                                        <div class="flex-1 min-w-[200px]">
                                            <x-input-label value="Addon Name" />
                                            <x-text-input x-bind:name="'addons['+aIndex+'][name]'" class="block mt-1 w-full text-sm" type="text" x-model="addon.name" required />
                                        </div>
                                        <div class="flex-1 min-w-[200px]">
                                            <x-input-label value="Description" />
                                            <x-text-input x-bind:name="'addons['+aIndex+'][description]'" class="block mt-1 w-full text-sm" type="text" x-model="addon.description" />
                                        </div>
                                        <div class="w-32">
                                            <x-input-label value="Price ($)" />
                                            <x-text-input x-bind:name="'addons['+aIndex+'][price]'" class="block mt-1 w-full text-sm" type="number" step="0.01" x-model="addon.price" required />
                                        </div>
                                        <div class="pt-7">
                                            <button type="button" @click="removeAddon(aIndex)" class="text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                
                                <button type="button" @click="addAddon()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">
                                    + Add New Addon
                                </button>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="mb-10 bg-gray-900 p-8 rounded-3xl border border-gray-800 shadow-2xl">
                            <h3 class="text-xl font-bold text-white mb-2 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Client Questionnaires
                            </h3>
                            <p class="text-sm text-gray-400 mb-8">What do you need from the client to fulfill this order?</p>
                            
                            <div class="space-y-4">
                                <template x-for="(req, index) in requirements" :key="index">
                                    <div class="flex items-center space-x-4 p-4 bg-gray-800 border border-gray-700 rounded-2xl">
                                        <input type="hidden" x-bind:name="'requirements['+index+'][id]'" x-model="req.id">
                                        <div class="flex-1">
                                            <input type="text" x-bind:name="'requirements['+index+'][name]'" class="bg-gray-700 border-none text-white focus:ring-2 focus:ring-indigo-500 rounded-xl block w-full text-sm" placeholder="Field Name" x-model="req.name" required />
                                        </div>
                                        <div class="w-1/4">
                                            <select x-bind:name="'requirements['+index+'][type]'" class="bg-gray-700 border-none text-white focus:ring-2 focus:ring-indigo-500 rounded-xl block w-full text-sm px-3" x-model="req.type">
                                                <option value="url">URL</option>
                                                <option value="text">Short Text</option>
                                                <option value="textarea">Long Text</option>
                                                <option value="file">File Upload</option>
                                            </select>
                                        </div>
                                        <div class="w-24">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="hidden" x-bind:name="'requirements['+index+'][is_required]'" value="0">
                                                <input type="checkbox" class="rounded border-none bg-gray-600 text-indigo-500 shadow-sm focus:ring-indigo-500" x-bind:name="'requirements['+index+'][is_required]'" value="1" x-model="req.is_required">
                                                <span class="ml-2 text-xs text-gray-400">Req.</span>
                                            </label>
                                        </div>
                                        <button type="button" @click="removeRequirement(index)" class="text-red-400 hover:text-red-600 transition p-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>

                                <button type="button" @click="addRequirement()" class="text-sm text-indigo-400 hover:text-indigo-300 font-bold flex items-center">
                                    <span class="text-lg mr-1">+</span> Add Question
                                </button>
                            </div>
                        </div>

                        <!-- FAQs -->
                        <div class="mb-10 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Frequently Asked Questions
                            </h3>
                            <p class="text-sm text-gray-500 mb-6">Anticipate client questions by adding FAQs that will show up on the service page.</p>

                            <div class="space-y-4">
                                <template x-for="(faq, index) in faqs" :key="index">
                                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl relative">
                                        <div class="mb-3">
                                            <x-input-label value="Question" />
                                            <x-text-input x-bind:name="'faqs['+index+'][question]'" class="block mt-1 w-full text-sm" type="text" x-model="faq.question" required placeholder="e.g. How long does the delivery take?" />
                                        </div>
                                        <div>
                                            <x-input-label value="Answer" />
                                            <textarea x-bind:name="'faqs['+index+'][answer]'" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm" rows="2" x-model="faq.answer" required placeholder="e.g. We typically deliver within 5-7 business days..."></textarea>
                                        </div>
                                        
                                        <button type="button" @click="removeFaq(index)" class="absolute top-4 right-4 text-red-400 hover:text-red-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>

                                <button type="button" @click="addFaq()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                    + Add FAQ
                                </button>
                            </div>
                        </div>

                        <!-- SEO Section -->
                        <x-seo-form-tabs :model="$service" />

                        <div class="flex items-center justify-between mt-8 pt-6 border-t">
                            <p class="text-sm text-gray-500 italic">* All 3 packages must be filled for a professional storefront.</p>
                            <div class="flex space-x-4">
                                <a href="{{ route('admin.traffic.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition shadow-sm">
                                    Cancel
                                </a>
                                <x-primary-button class="rounded-xl px-10 py-3 text-lg">
                                    {{ __('Save Changes') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function serviceEditForm() {
            return {
                slug: '{{ old('slug', $service->slug) }}',
                generateSlug(name) {
                    this.slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                },
                requirements: {!! json_encode(old('requirements', $service->requirements->map(function($req) {
                    return [
                        'id' => $req->id,
                        'name' => $req->name,
                        'type' => $req->type,
                        'is_required' => (bool) $req->is_required
                    ];
                }))) !!},
                packages: [
                    @foreach(['Basic', 'Standard', 'Premium'] as $index => $name)
                        @php 
                            $pkg = $service->packages->where('name', $name)->first();
                            $oldPkg = old('packages.' . $index);
                        @endphp
                        {
                            id: '{{ $oldPkg['id'] ?? ($pkg->id ?? '') }}',
                            name: '{{ $name }}',
                            price: '{{ $oldPkg['price'] ?? ($pkg->price ?? 0) }}',
                            emergency_fee: '{{ $oldPkg['emergency_fee'] ?? ($pkg->emergency_fee ?? '') }}',
                            turnaround_time_days: '{{ $oldPkg['turnaround_time_days'] ?? ($pkg->turnaround_time_days ?? '') }}',
                            express_turnaround_time_days: '{{ $oldPkg['express_turnaround_time_days'] ?? ($pkg->express_turnaround_time_days ?? '') }}',
                            description: '{{ addslashes($oldPkg['description'] ?? ($pkg->description ?? '')) }}',
                            features: {!! json_encode($oldPkg['features'] ?? (isset($pkg->features) ? $pkg->features : [])) !!}
                        },
                    @endforeach
                ],
                addons: {!! json_encode(old('addons', $service->addons->map(function($addon) {
                    return [
                        'id' => $addon->id,
                        'name' => $addon->name,
                        'description' => $addon->description,
                        'price' => $addon->price
                    ];
                }))) !!},
                faqs: {!! json_encode(old('faqs', is_array($service->faqs) ? $service->faqs : [])) !!},
                addRequirement() {
                    this.requirements.push({ id: '', name: '', type: 'text', is_required: true });
                },
                removeRequirement(index) {
                    this.requirements.splice(index, 1);
                },
                addFeature(pkgIndex) {
                    this.packages[pkgIndex].features.push('');
                },
                removeFeature(pkgIndex, fIndex) {
                    this.packages[pkgIndex].features.splice(fIndex, 1);
                },
                addAddon() {
                    this.addons.push({ id: '', name: '', description: '', price: 0 });
                },
                removeAddon(index) {
                    this.addons.splice(index, 1);
                },
                addFaq() {
                    this.faqs.push({ question: '', answer: '' });
                },
                removeFaq(index) {
                    this.faqs.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
