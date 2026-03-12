<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ isset($template) ? __('Edit Email Template') : __('Create Email Template') }}
            </h2>
            <a href="{{ route('admin.notifications.templates.index') }}" class="flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Templates
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <form action="{{ isset($template) ? route('admin.notifications.templates.update', $template) : route('admin.notifications.templates.store') }}" method="POST">
                    @csrf
                    @if(isset($template)) @method('PUT') @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <x-input-label for="name" :value="__('Template Display Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $template->name ?? '')" required />
                            <p class="mt-1 text-xs text-gray-400">Internal name for easy identification.</p>
                        </div>
                        <div>
                            <x-input-label for="slug" :value="__('Unique Identifier (Slug)')" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full bg-gray-50" :value="old('slug', $template->slug ?? '')" required :readonly="isset($template)" />
                            <p class="mt-1 text-xs text-gray-400">Used by the system to trigger this specific template. Only letters and underscores.</p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <x-input-label for="subject" :value="__('Email Subject Line')" />
                        <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full font-bold" :value="old('subject', $template->subject ?? '')" required />
                        <p class="mt-1 text-xs text-gray-400">The subject of the email sent to the user. You can use variables like <code>{order_id}</code>.</p>
                    </div>

                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-1">
                            <x-input-label for="body" :value="__('Email Body (HTML Supported)')" />
                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-tighter">Rich Content Supported</span>
                        </div>
                        <textarea id="body" name="body" rows="12" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm font-mono text-sm" required>{{ old('body', $template->body ?? '') }}</textarea>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-dashed border-gray-200">
                        <h4 class="text-sm font-bold text-gray-700 uppercase mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Available Variables
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @if(isset($template) && $template->variables_hint)
                                @foreach($template->variables_hint as $var)
                                    <span class="px-2 py-1 bg-white border border-gray-200 rounded text-xs font-mono text-indigo-600 shadow-sm">
                                        {{ '{' . $var . '}' }}
                                    </span>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500 italic">No specific variables defined for this type. Common variables: {order_id}, {user_name}, {link}.</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-900/20 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25">
                            {{ isset($template) ? __('Update Template') : __('Create Template') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
