<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Email Lists') }}
        </h2>
    </x-slot>
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Email Lists</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your custom address books for bulk campaigns.</p>
        </div>
    </div>

    <!-- Create List Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Create New List</h2>
        <form action="{{ route('admin.email-lists.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">List Name</label>
                    <input type="text" name="name" id="name" required placeholder="e.g. VIP Customers"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div class="lg:col-span-2 flex items-end">
                    <div class="w-full flex space-x-4">
                        <div class="flex-grow">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                            <input type="text" name="description" id="description" placeholder="Notes about this list..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <button type="submit"
                            class="mb-0.5 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create List
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Lists Table -->
    <div class="bg-white shadow relative sm:rounded-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3">List Name</th>
                        <th scope="col" class="px-4 py-3">Description</th>
                        <th scope="col" class="px-4 py-3">Contacts</th>
                        <th scope="col" class="px-4 py-3">Created</th>
                        <th scope="col" class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lists as $list)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $list->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $list->description ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $list->contacts_count }}</span>
                            </td>
                            <td class="px-4 py-3">{{ $list->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right flex justify-end space-x-3 items-center">
                                <a href="{{ route('admin.email-lists.show', $list) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Manage Contacts</a>
                                
                                <form action="{{ route('admin.email-lists.destroy', $list) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this list? All contacts inside it will be removed permanently.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <p>No email lists found. Create your first list above.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lists->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $lists->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
