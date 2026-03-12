<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.leads.index') }}" class="text-gray-400 hover:text-gray-600">&larr; Back</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Lead Message') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-gray-100 p-8">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900">{{ $lead->name }}</h3>
                        <p class="text-indigo-600 font-bold">{{ $lead->email }}</p>
                        @if($lead->phone) <p class="text-sm text-gray-500">Phone: {{ $lead->phone }}</p> @endif
                    </div>
                    <div class="text-right">
                        <span class="px-4 py-2 rounded-full text-xs font-black uppercase tracking-widest @if($lead->status == 'pending') bg-yellow-100 text-yellow-800 @elseif($lead->status == 'contacted') bg-blue-100 text-blue-800 @else bg-green-100 text-green-800 @endif">
                            {{ $lead->status }}
                        </span>
                        <p class="text-xs text-gray-400 mt-2">{{ $lead->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 mb-8">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Subject: {{ $lead->subject ?? 'No Subject' }}</h4>
                    <p class="text-gray-800 leading-loose">
                        {{ $lead->message }}
                    </p>
                </div>

                <div class="flex space-x-4">
                    <form action="{{ route('admin.leads.update', $lead) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="contacted">
                        <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 transition">Mark as Contacted</button>
                    </form>
                    <form action="{{ route('admin.leads.update', $lead) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="closed">
                        <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition">Close Lead</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
