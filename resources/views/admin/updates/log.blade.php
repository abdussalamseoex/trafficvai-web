<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Update Log Details') }}
            </h2>
            <a href="{{ route('admin.updates.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-bold transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Updates
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Execution Output</h3>
                        <p class="text-xs text-gray-500 mt-1">Logged on {{ $log->executed_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                    <div>
                        @if($log->status == 'success')
                            <span class="px-4 py-1.5 text-xs font-black rounded-full bg-green-500 text-white uppercase tracking-widest">Success</span>
                        @elseif($log->status == 'error')
                            <span class="px-4 py-1.5 text-xs font-black rounded-full bg-red-500 text-white uppercase tracking-widest">Error</span>
                        @endif
                    </div>
                </div>
                <div class="p-8 bg-gray-900">
                    <pre class="text-green-400 font-mono text-sm whitespace-pre-wrap overflow-x-auto leading-relaxed">{{ $log->output ?: 'No output logged for this session.' }}</pre>
                </div>
            </div>

            @if($log->changes)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Changes in this Update</h3>
                </div>
                <div class="p-6">
                    <pre class="bg-gray-50 p-4 rounded-xl text-sm text-gray-700 font-mono whitespace-pre-wrap">{{ $log->changes }}</pre>
                </div>
            </div>
            @endif
            
        </div>
    </div>
</x-app-layout>
