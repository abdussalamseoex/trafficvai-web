@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.email-lists.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $emailList->name }}</h1>
                <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded ml-2">{{ $contacts->total() }} Contacts</span>
            </div>
            @if($emailList->description)
                <p class="mt-1 text-sm text-gray-500 ml-7">{{ $emailList->description }}</p>
            @endif
        </div>
        
        <form action="{{ route('admin.email-lists.destroy', $emailList) }}" method="POST" onsubmit="return confirm('Delete this list completely?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Delete List
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Add Contacts Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sticky top-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Add Contacts</h2>
                <form action="{{ route('admin.email-lists.contacts.store', $emailList) }}" method="POST">
                    @csrf
                    <div>
                        <label for="emails" class="block text-sm font-medium text-gray-700 mb-2">Paste Emails (Comma or newline separated)</label>
                        <textarea id="emails" name="emails" rows="10" required placeholder="john@example.com&#10;jane@company.com"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md font-mono text-sm"></textarea>
                        <p class="mt-2 text-xs text-gray-500">Sytem will automatically remove duplicates and invalid addresses.</p>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Add to List
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contacts Table -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3">Email Address</th>
                                <th scope="col" class="px-4 py-3">Added On</th>
                                <th scope="col" class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-gray-900">{{ $contact->email }}</td>
                                    <td class="px-4 py-3">{{ $contact->created_at->format('M d, Y h:i A') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form action="{{ route('admin.email-lists.contacts.destroy', [$emailList, $contact]) }}" method="POST" class="inline-block" onsubmit="return confirm('Remove this email?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                        No contacts found in this list.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($contacts->hasPages())
                    <div class="px-4 py-3 border-t">
                        {{ $contacts->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
