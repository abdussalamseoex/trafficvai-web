<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-gray-900 leading-tight">
            Add New Redirect
        </h2>
    </x-slot>

    <div class="max-w-4xl">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('admin.seo.redirects.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">From Path</label>
                        <input type="text" name="from_path" value="{{ old('from_path') }}" placeholder="old-page-url" class="w-full bg-gray-50 border-gray-200 rounded-xl p-4 font-mono text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">To Path</label>
                        <input type="text" name="to_path" value="{{ old('to_path') }}" placeholder="/new-page-url" class="w-full bg-gray-50 border-gray-200 rounded-xl p-4 font-mono text-sm" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Redirect Type</label>
                    <div class="flex space-x-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="type" value="301" class="hidden peer" checked>
                            <div class="p-4 border-2 border-gray-100 rounded-2xl peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all flex items-center">
                                <span class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-indigo-600 mr-3 flex-shrink-0"></span>
                                <div>
                                    <span class="block font-black text-gray-900">301 Permanent</span>
                                    <span class="text-[10px] text-gray-500 uppercase tracking-tighter">SEO Recommended</span>
                                </div>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="type" value="302" class="hidden peer">
                            <div class="p-4 border-2 border-gray-100 rounded-2xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all flex items-center">
                                <span class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-orange-500 mr-3 flex-shrink-0"></span>
                                <div>
                                    <span class="block font-black text-gray-900">302 Temporary</span>
                                    <span class="text-[10px] text-gray-500 uppercase tracking-tighter">Maintenance/Testing</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end pt-4 space-x-4">
                    <a href="{{ route('admin.seo.redirects.index') }}" class="px-8 py-4 text-sm font-black text-gray-500 uppercase tracking-widest">Cancel</a>
                    <button type="submit" class="px-8 py-4 bg-indigo-600 text-white text-sm font-black rounded-2xl shadow-xl shadow-indigo-900/20 hover:bg-indigo-700 transition-all uppercase tracking-widest">
                        Create Redirect
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
