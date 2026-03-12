<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-black text-gray-900 leading-tight">
                URL Redirect Manager
            </h2>
            <button onclick="window.location.href='{{ route('admin.seo.redirects.create') }}'" class="px-6 py-3 bg-indigo-600 text-white text-xs font-black rounded-xl shadow-lg shadow-indigo-900/20 hover:bg-indigo-700 transition-all uppercase tracking-widest">
                + New Redirect
            </button>
        </div>
    </x-slot>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-100 bg-gray-50/30">
            <h3 class="text-xl font-black text-gray-900">Active Forwarding Rules</h3>
            <p class="text-gray-500 text-sm mt-1">Manage 301 (Permanent) and 302 (Temporary) redirects to maintain SEO equity.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">From Path</th>
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest">To Path</th>
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Type</th>
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Hits</th>
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($redirects as $redirect)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="px-8 py-5">
                            <span class="text-sm font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded">{{ $redirect->from_path }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-sm font-bold text-indigo-600">{{ $redirect->to_path }}</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-2 py-1 {{ $redirect->type == '301' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }} text-[10px] font-black rounded uppercase">
                                {{ $redirect->type }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center font-bold text-gray-900 text-sm">
                            {{ number_format($redirect->hits) }}
                        </td>
                        <td class="px-8 py-5 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.seo.redirects.edit', $redirect) }}" class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('admin.seo.redirects.destroy', $redirect) }}" method="POST" onsubmit="return confirm('Delete this redirect?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center text-gray-400 italic text-sm">No redirects configured yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($redirects->hasPages())
        <div class="p-8 border-t border-gray-100 bg-gray-50/10">
            {{ $redirects->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
