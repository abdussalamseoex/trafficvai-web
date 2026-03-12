<header class="bg-white border-b border-gray-100 sticky top-0 z-30">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <!-- Mobile Sidebar Toggle -->
        <div class="flex items-center md:hidden">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 rounded-md p-2">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Left side (Empty on mobile, title/breadcrumbs on desktop) -->
        <div class="hidden md:flex flex-col flex-1 pl-4">
            <h1 class="text-xl font-heading font-extrabold text-gray-900 leading-tight">{{ $pageTitle ?? 'Dashboard' }}</h1>
            <p class="text-xs font-semibold text-gray-500 mt-0.5">{{ $pageSubtitle ?? (Auth::user()->is_admin ? 'Admin Panel' : 'Client Panel · Overview') }}</p>
        </div>

        <!-- Right side User Menu -->
        <div class="flex items-center space-x-4">
            <!-- Notifications Dropdown -->
            @php
                $unreadCount = \App\Models\NotificationHub::where(function($q) {
                    $q->where('user_id', auth()->id());
                    if (auth()->user()->is_admin) {
                        $q->orWhereNull('user_id');
                    }
                })->where('is_read', false)->count();

                $notifications = \App\Models\NotificationHub::where(function($q) {
                    $q->where('user_id', auth()->id());
                    if (auth()->user()->is_admin) {
                        $q->orWhereNull('user_id');
                    }
                })->latest()->take(5)->get();
            @endphp

            <x-dropdown align="right" width="96" contentClasses="p-0 bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden">
                <x-slot name="trigger">
                    <button class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        <span class="sr-only">View notifications</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($unreadCount > 0)
                        <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                        @endif
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                        @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">
                                Mark all read
                            </button>
                        </form>
                        @endif
                    </div>
                    
                    <div class="max-h-96 overflow-y-auto w-full sm:w-96">
                        @forelse($notifications as $notification)
                        <a href="{{ route('notifications.read', $notification->id) }}" class="block px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0 {{ !$notification->is_read ? 'bg-indigo-50/50' : '' }}">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    @if($notification->type == 'order')
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg></div>
                                    @elseif($notification->type == 'payment')
                                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                                    @elseif($notification->type == 'message')
                                        <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg></div>
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                                    @endif
                                </div>
                                <div class="ml-3 w-0 flex-1">
                                    <p class="text-sm font-semibold {{ !$notification->is_read ? 'text-gray-900' : 'text-gray-600' }}">
                                        {{ $notification->title }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                        {{ $notification->message }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-1 text-right">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="px-4 py-8 text-center sm:w-96">
                            <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                            <p class="text-sm text-gray-500 font-medium">No new notifications</p>
                        </div>
                        @endforelse
                    </div>

                    @if(auth()->user()->is_admin)
                    <div class="border-t border-gray-100 px-4 py-2 bg-gray-50 text-center">
                        <a href="{{ route('admin.notifications.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">View Notification Hub</a>
                    </div>
                    @endif
                </x-slot>
            </x-dropdown>

            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 px-3 py-1.5 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-1">
                        <div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center font-bold mr-2.5 shadow-sm text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="hidden sm:block mr-2 font-semibold">{{ Auth::user()->name }} <span class="text-gray-400 font-normal">({{ Auth::user()->is_admin ? 'Admin' : 'Client' }})</span></div>
                        <svg class="fill-current h-4 w-4 text-gray-400 hidden sm:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile Settings') }}
                    </x-dropdown-link>
                    
                    <div class="border-t border-gray-100 my-1"></div>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();" class="text-red-600 hover:text-red-700 font-medium">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>
