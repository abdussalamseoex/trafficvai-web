<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.site-faqs.index') }}" class="text-gray-400 hover:text-gray-600">&larr; Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Site FAQ') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-gray-100 p-8">
                <form action="{{ route('admin.site-faqs.update', $siteFaq) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Question</label>
                        <input type="text" name="question" value="{{ $siteFaq->question }}" required class="w-full rounded-2xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Answer</label>
                        <textarea name="answer" rows="5" required class="w-full rounded-2xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ $siteFaq->answer }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                            <input type="text" name="category" value="{{ $siteFaq->category }}" class="w-full rounded-2xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ $siteFaq->sort_order }}" class="w-full rounded-2xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="is_active" value="1" @checked($siteFaq->is_active) class="rounded-lg text-indigo-600 focus:ring-indigo-500">
                        <label class="text-sm font-bold text-gray-700">Display this FAQ on the site</label>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-bootstrap-primary text-white py-4 rounded-2xl font-bold shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition">Update FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
