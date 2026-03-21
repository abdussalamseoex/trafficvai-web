<x-app-layout>
    <x-slot name="header">
        <!-- TinyMCE Editor -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
        
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.bulk-emails.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Compose Custom Bulk Email') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.bulk-emails.store') }}" method="POST">
                        @csrf
                        
                        <!-- Saved Email Lists -->
                        <div class="mb-6">
                            <label for="email_lists" class="block text-sm font-medium text-gray-700 mb-1">Select Saved Email Lists</label>
                            <select id="email_lists" name="email_lists[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-32">
                                @foreach($emailLists as $list)
                                    <option value="{{ $list->id }}">{{ $list->name }} ({{ $list->contacts()->count() }} contacts)</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Hold CTRL (or CMD on Mac) to select multiple lists. The system will merge all contacts automatically.</p>
                        </div>

                        <!-- Manual List of Emails -->
                        <div class="mb-6">
                            <label for="emails" class="block text-sm font-medium text-gray-700 mb-1">Additional Emails (Optional)</label>
                            <textarea id="emails" name="emails" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="john@example.com, jane@test.com">{{ old('emails') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Paste any extra emails here. They will be merged with the selected lists above.</p>
                            @error('emails') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Subject -->
                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject Title</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. Special Offer!">
                            @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-6">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message Content</label>
                            <textarea id="message" name="message" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('message') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Design your promotional email nicely using the editor.</p>
                            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end mt-6 space-x-4 border-t pt-4">
                            <button type="button" id="send-test-btn" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                                Send Test Email
                            </button>
                            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Send Bulk Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Note:</strong> Emails will be queued and sent dynamically in the background to prevent server timeouts and spam limits.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#message',
            height: 400,
            plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table directionality emoticons template',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | removeformat code',
            menubar: true,
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            }
        });

        document.getElementById('send-test-btn').addEventListener('click', async function() {
            tinymce.triggerSave();
            const subject = document.getElementById('subject').value;
            const message = tinymce.get('message') ? tinymce.get('message').getContent() : document.getElementById('message').value;

            if (!subject || !message) {
                alert('Please fill subject and message first.');
                return;
            }

            const originalText = this.innerText;
            this.disabled = true;
            this.innerText = 'Sending...';

            try {
                const response = await fetch('{{ route('admin.bulk-emails.send-test') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ subject, message })
                });

                const data = await response.json();
                if (response.ok) {
                    alert(data.message);
                } else {
                    if (response.status === 422) {
                         alert('Validation Error: ' + JSON.stringify(data.errors));
                    } else {
                         alert('Error: ' + data.message);
                    }
                }
            } catch (e) {
                console.error(e);
                alert('An error occurred while sending the test email. Please check the network log.');
            } finally {
                this.disabled = false;
                this.innerText = originalText;
            }
        });
    </script>
</x-app-layout>
