<footer class="bg-[#0F1117] relative pt-20 pb-10 w-full overflow-hidden border-t border-gray-800">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-brand/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-indigo-500/10 rounded-full blur-[80px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-8 mb-16">
            
            <!-- Brand Column -->
            <div class="lg:col-span-2">
                <a href="{{ route('home') }}" class="inline-block mb-6">
                    @php $logoPath = \App\Models\Setting::get('site_logo') ? asset(\App\Models\Setting::get('site_logo')) : asset('images/logo.png'); @endphp
                    <img src="{{ $logoPath }}" class="h-10 md:h-12 w-auto object-contain" alt="TrafficVai">
                </a>
                <p class="text-gray-400 text-sm leading-relaxed mb-6 max-w-sm">
                    {{ \App\Models\Setting::get('footer_description', 'Elevating brands through data-driven SEO strategies, premium guest posts, and targeted digital marketing solutions designed for scalable growth.') }}
                </p>
                <div class="flex items-center space-x-4">
                    @if(!\App\Models\Setting::get('footer_social_twitter') && !\App\Models\Setting::get('footer_social_instagram') && !\App\Models\Setting::get('footer_social_linkedin') && !\App\Models\Setting::get('footer_social_facebook'))
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-brand hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-brand hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-brand hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                    @else
                        @if(\App\Models\Setting::get('footer_social_twitter'))
                            <a href="{{ \App\Models\Setting::get('footer_social_twitter') }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-brand hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                        @endif
                        @if(\App\Models\Setting::get('footer_social_instagram'))
                            <a href="{{ \App\Models\Setting::get('footer_social_instagram') }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-brand hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        @endif
                        @if(\App\Models\Setting::get('footer_social_linkedin'))
                            <a href="{{ \App\Models\Setting::get('footer_social_linkedin') }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-brand hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                        @endif
                        @if(\App\Models\Setting::get('footer_social_facebook'))
                            <a href="{{ \App\Models\Setting::get('footer_social_facebook') }}" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-brand hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Solutions -->
            @php
                $col1LinksStr = \App\Models\Setting::get('footer_col_1_links');
                $col1Links = is_string($col1LinksStr) ? json_decode($col1LinksStr, true) : null;
                $col1Title = \App\Models\Setting::get('footer_col_1_title', 'Solutions');
            @endphp
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6 relative inline-block">
                    {{ $col1Title }}
                    <span class="absolute -bottom-2 -left-2 w-8 h-1 bg-brand rounded-full"></span>
                </h3>
                <ul class="space-y-4 text-sm">
                    @if(is_array($col1Links) && count($col1Links) > 0)
                        @foreach($col1Links as $link)
                            @if(isset($link['name']) && isset($link['url']))
                                <li><a href="{{ $link['url'] }}" class="text-gray-400 hover:text-brand font-medium transition-colors flex items-center group"><span class="w-1.5 h-1.5 rounded-full bg-gray-700 mr-2 group-hover:bg-brand transition-colors"></span>{{ $link['name'] }}</a></li>
                            @endif
                        @endforeach
                    @else
                        <li><a href="{{ route('campaigns.index', 'seo-campaigns') }}" class="text-gray-400 hover:text-brand font-medium transition-colors flex items-center group"><span class="w-1.5 h-1.5 rounded-full bg-gray-700 mr-2 group-hover:bg-brand transition-colors"></span> SEO Campaigns</a></li>
                        <li><a href="{{ route('services.index') }}" class="text-gray-400 hover:text-brand font-medium transition-colors flex items-center group"><span class="w-1.5 h-1.5 rounded-full bg-gray-700 mr-2 group-hover:bg-brand transition-colors"></span> Link Building</a></li>
                        <li><a href="{{ route('guest_posts.index') }}" class="text-gray-400 hover:text-brand font-medium transition-colors flex items-center group"><span class="w-1.5 h-1.5 rounded-full bg-gray-700 mr-2 group-hover:bg-brand transition-colors"></span> Guest Posts</a></li>
                        <li><a href="{{ route('traffic.index') }}" class="text-gray-400 hover:text-brand font-medium transition-colors flex items-center group"><span class="w-1.5 h-1.5 rounded-full bg-gray-700 mr-2 group-hover:bg-brand transition-colors"></span> Website Traffic</a></li>
                    @endif
                </ul>
            </div>

            <!-- Resources -->
            @php
                $col2LinksStr = \App\Models\Setting::get('footer_col_2_links');
                $col2Links = is_string($col2LinksStr) ? json_decode($col2LinksStr, true) : null;
                $col2Title = \App\Models\Setting::get('footer_col_2_title', 'Resources');
            @endphp
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6 relative inline-block">
                    {{ $col2Title }}
                    <span class="absolute -bottom-2 -left-2 w-8 h-1 bg-indigo-500 rounded-full"></span>
                </h3>
                <ul class="space-y-4 text-sm">
                    @if(is_array($col2Links) && count($col2Links) > 0)
                        @foreach($col2Links as $link)
                            @if(isset($link['name']) && isset($link['url']))
                                <li><a href="{{ $link['url'] }}" class="text-gray-400 hover:text-white font-medium transition-colors">{{ $link['name'] }}</a></li>
                            @endif
                        @endforeach
                    @else
                        <li><a href="{{ route('blog.index') }}" class="text-gray-400 hover:text-white font-medium transition-colors">Latest News</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white font-medium transition-colors">About TrafficVai</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white font-medium transition-colors">Contact Support</a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white font-medium transition-colors">Client Portal</a></li>
                    @endif
                </ul>
            </div>

            <!-- Legal -->
            @php
                $col3LinksStr = \App\Models\Setting::get('footer_col_3_links');
                $col3Links = is_string($col3LinksStr) ? json_decode($col3LinksStr, true) : null;
                $col3Title = \App\Models\Setting::get('footer_col_3_title', 'Legal');
            @endphp
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6 relative inline-block">
                    {{ $col3Title }}
                    <span class="absolute -bottom-2 -left-2 w-8 h-1 bg-gray-600 rounded-full"></span>
                </h3>
                <ul class="space-y-4 text-sm">
                    @if(is_array($col3Links) && count($col3Links) > 0)
                        @foreach($col3Links as $link)
                            @if(isset($link['name']) && isset($link['url']))
                                <li><a href="{{ $link['url'] }}" class="text-gray-400 hover:text-white font-medium transition-colors">{{ $link['name'] }}</a></li>
                            @endif
                        @endforeach
                    @else
                        <li><a href="{{ route('privacy') }}" class="text-gray-400 hover:text-white font-medium transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="text-gray-400 hover:text-white font-medium transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white font-medium transition-colors">Refund Policy</a></li>
                    @endif
                </ul>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div class="mt-16 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center text-center gap-4">
            <p class="text-gray-500 text-sm font-medium">
                @if(\App\Models\Setting::get('footer_copyright_text'))
                    {!! \App\Models\Setting::get('footer_copyright_text') !!}
                @else
                    &copy; {{ date('Y') }} <span class="text-gray-300 font-bold">TrafficVai</span>. All rights reserved.
                @endif
            </p>
            <div class="flex items-center space-x-2 text-sm text-gray-500 font-medium">
                <span>{{ \App\Models\Setting::get('footer_attribution_1', 'Designed for Growth') }}</span>
                <svg class="w-4 h-4 text-red-500 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                <span>{{ \App\Models\Setting::get('footer_attribution_2', 'by SEO Experts') }}</span>
            </div>
        </div>
    </div>
</footer>
