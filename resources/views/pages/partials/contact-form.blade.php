<div id="contact-form" class="max-w-4xl mx-auto sm:px-6 lg:px-8 scroll-mt-8 mt-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-8 not-prose">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Support</h3>
            <p class="text-indigo-600 font-medium mb-6">
                <a href="mailto:{{ \App\Models\Setting::get('contact_email', 'support@TrafficVai.example.com') }}">
                    {{ \App\Models\Setting::get('contact_email', 'support@TrafficVai.example.com') }}
                </a>
            </p>
            
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Business Hours</h3>
            <p class="text-gray-600 mb-6">{{ \App\Models\Setting::get('contact_hours', 'Monday - Friday: 9:00 AM - 6:00 PM (EST)') }}</p>
            
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Office Headquarters</h3>
            <div class="text-gray-600">
                {!! nl2br(e(\App\Models\Setting::get('contact_address', "123 Search Engine Blvd, Suite 400\nNew York, NY 10001"))) !!}
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8 not-prose">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Send us a Message</h3>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Subject</label>
                    <input type="text" name="subject" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Message</label>
                    <textarea name="message" rows="4" required class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition">Send Message</button>
            </form>
        </div>
    </div>
    
    <div class="mt-8 text-sm text-gray-500 not-prose">
        Are you an existing client? Log in to your <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Dashboard</a> and send us a direct message on your specific order for immediate assistance.
    </div>
</div>
