<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.support.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ticket #') }}{{ $ticket->id }} - {{ $ticket->subject }}
            </h2>
            @if($ticket->status === 'open')
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 uppercase tracking-wide">Open</span>
            @elseif($ticket->status === 'in-progress')
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase tracking-wide">In Progress</span>
            @else
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 uppercase tracking-wide">Closed</span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Main Content: Ticket Details -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-4 mb-4">Original Request</h3>
                        
                        <div class="prose max-w-none text-gray-700">
                            <!-- In a real app we'd have an initial message or body. For now we just have the subject since our model only has subject/status/priority -->
                            <p class="font-semibold text-gray-900 text-lg mb-2">{{ $ticket->subject }}</p>
                            <p class="text-sm text-gray-500">Submitted by {{ $ticket->user->name }} on {{ $ticket->created_at->format('l, F j, Y \a\t g:i A') }}</p>
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-100 italic text-gray-600">
                                Note: This ticket currently relies on the messaging hub or external email for detailed back-and-forth communication. Future updates will include inline ticket replies.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Ticket Meta & Actions -->
            <div class="space-y-6">
                <!-- Status Update Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-4 mb-4">Manage Ticket</h3>
                        
                        <form action="{{ route('admin.support.update', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700">Update Status</label>
                                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in-progress" {{ $ticket->status === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Client Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-4 mb-4">Client Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="text-sm text-gray-900 font-semibold">{{ $ticket->user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                <dd class="text-sm text-indigo-600"><a href="mailto:{{ $ticket->user->email }}">{{ $ticket->user->email }}</a></dd>
                            </div>
                            <div class="pt-4 border-t mt-4">
                                <a href="{{ route('inbox', ['user' => $ticket->user->id]) }}" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    Message Client in Hub
                                </a>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
