<x-app-layout>
    <x-slot name="header">
        <!-- TinyMCE Editor -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
        
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.announcements.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Compose Announcement') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.announcements.store') }}" method="POST">
                        @csrf
                        
                        <!-- Subject -->
                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject Title</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. 50% Off Black Friday Sale!">
                            @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-6">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message Content</label>
                            <textarea id="message" name="message" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('message') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Use the rich text editor to format your announcement or email beautifully.</p>
                            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Method</label>
                            <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                                <div class="flex items-center">
                                    <input id="type_email" name="type" type="radio" value="email" {{ old('type') == 'email' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="type_email" class="ml-3 block text-sm font-medium text-gray-700">
                                        Email Only
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="type_notice" name="type" type="radio" value="notice" {{ old('type') == 'notice' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="type_notice" class="ml-3 block text-sm font-medium text-gray-700">
                                        Client Dashboard Notice Only
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="type_both" name="type" type="radio" value="both" {{ old('type', 'both') == 'both' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="type_both" class="ml-3 block text-sm font-medium text-gray-700">
                                        Both (Email & Dashboard)
                                    </label>
                                </div>
                            </div>
                            @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end mt-6 space-x-4 border-t pt-4">
                            <button type="button" id="send-test-btn" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                                Send Test Email
                            </button>
                            <button type="submit" name="action" value="draft" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Save as Draft
                            </button>
                            <button type="submit" name="action" value="send" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Publish & Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Pre-requisite Note -->
            <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Note:</strong> Bulk Emails are queued to run in the background. If you select "Email Only" or "Both", the background worker will slowly send the emails to prevent your server from timing out or hitting rate limits.
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
            const message = document.getElementById('message').value;

            if (!subject || !message) {
                alert('Please fill subject and message first.');
                return;
            }

            const originalText = this.innerText;
            this.disabled = true;
            this.innerText = 'Sending...';

            try {
                const response = await fetch('{{ route('admin.announcements.send-test') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ subject, message })
                });

                const data = await response.json();
                if (response.ok) {
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (e) {
                console.error(e);
                alert('An error occurred while sending the test email.');
            } finally {
                this.disabled = false;
                this.innerText = originalText;
            }
        });
    </script>
</x-app-layout>
