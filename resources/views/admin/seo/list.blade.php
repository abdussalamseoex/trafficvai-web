<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-black text-gray-900 leading-tight">
                {{ $type }} SEO Management
            </h2>
            <div class="flex items-center space-x-2 text-xs">
                <span class="flex items-center text-gray-500"><span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span> Complete</span>
                <span class="flex items-center text-gray-500"><span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span> Partial</span>
                <span class="flex items-center text-gray-500"><span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span> Missing Critical</span>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-100">
            <h3 class="text-xl font-black text-gray-900">Direct SEO Access</h3>
            <p class="text-gray-500 text-sm mt-1">Review and update SEO metadata for all {{ strtolower($type) }} items.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">Item Name</th>
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Status</th>
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        @php
                            $seo = $item->seoMeta;
                            $status = 'red';
                            if ($seo && $seo->meta_title && $seo->meta_description) {
                                $status = ($seo->og_image && $seo->meta_keywords && $seo->schema_json) ? 'green' : 'yellow';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <div class="font-bold text-gray-900">{{ $item->title ?? $item->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">/{{ $item->slug }}</div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <div class="flex items-center justify-center">
                                    @if($status == 'green')
                                        <div class="w-4 h-4 bg-green-500 rounded-full shadow-lg shadow-green-500/20" title="All critical fields filled"></div>
                                    @elseif($status == 'yellow')
                                        <div class="w-4 h-4 bg-yellow-500 rounded-full shadow-lg shadow-yellow-500/20" title="Partial completion"></div>
                                    @else
                                        <div class="w-4 h-4 bg-red-500 rounded-full shadow-lg shadow-red-500/20" title="Missing critical fields (Meta Title/Description)"></div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <a href="{{ route('admin.seo.edit', ['type' => $type, 'id' => $item->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-600 text-xs font-black rounded-xl hover:bg-indigo-100 transition-colors">
                                    EDIT SEO
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center text-gray-500 italic">No {{ strtolower($type) }} items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="p-8 border-t border-gray-100 bg-gray-50/30">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
