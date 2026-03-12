<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Global Notification Settings') }}
            </h2>
            <a href="{{ route('admin.notifications.index') }}" class="flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Overview
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <form action="{{ route('admin.notifications.settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 mb-12">
                        <!-- Sender Configuration -->
                        <div class="col-span-full border-b border-gray-100 pb-2">
                            <h3 class="text-lg font-bold text-gray-900">Sender Identity</h3>
                            <p class="text-sm text-gray-500">How your emails appear in the recipient's inbox.</p>
                        </div>

                        <div>
                            <x-input-label for="mail_from_address" :value="__('From Email Address')" />
                            <x-text-input id="mail_from_address" name="settings[mail_from_address]" type="email" class="mt-1 block w-full" :value="$settings->where('key', 'mail_from_address')->first()->value ?? ''" required />
                        </div>

                        <div>
                            <x-input-label for="mail_from_name" :value="__('Sender Name')" />
                            <x-text-input id="mail_from_name" name="settings[mail_from_name]" type="text" class="mt-1 block w-full" :value="$settings->where('key', 'mail_from_name')->first()->value ?? ''" required />
                        </div>

                        <!-- SMTP Configuration -->
                        <div class="col-span-full border-b border-gray-100 pb-2 mt-4">
                            <h3 class="text-lg font-bold text-gray-900">SMTP Server Configuration</h3>
                            <p class="text-sm text-gray-500">Connection details for your outgoing mail server.</p>
                        </div>

                        <div>
                            <x-input-label for="mail_host" :value="__('SMTP Host')" />
                            <x-text-input id="mail_host" name="settings[mail_host]" type="text" class="mt-1 block w-full" :value="$settings->where('key', 'mail_host')->first()->value ?? ''" required />
                        </div>

                        <div>
                            <x-input-label for="mail_port" :value="__('SMTP Port')" />
                            <x-text-input id="mail_port" name="settings[mail_port]" type="text" class="mt-1 block w-full" :value="$settings->where('key', 'mail_port')->first()->value ?? ''" required />
                        </div>

                        <div>
                            <x-input-label for="mail_username" :value="__('SMTP Username')" />
                            <x-text-input id="mail_username" name="settings[mail_username]" type="text" class="mt-1 block w-full" :value="$settings->where('key', 'mail_username')->first()->value ?? ''" />
                        </div>

                        <div>
                            <x-input-label for="mail_password" :value="__('SMTP Password')" />
                            <x-text-input id="mail_password" name="settings[mail_password]" type="password" class="mt-1 block w-full" :value="$settings->where('key', 'mail_password')->first()->value ?? ''" />
                            <p class="mt-1 text-[10px] text-gray-400">Passwords are encrypted before storage.</p>
                        </div>

                        <div>
                            <x-input-label for="mail_encryption" :value="__('Encryption Type')" />
                            <select id="mail_encryption" name="settings[mail_encryption]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                                <option value="tls" {{ ($settings->where('key', 'mail_encryption')->first()->value ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($settings->where('key', 'mail_encryption')->first()->value ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="none" {{ ($settings->where('key', 'mail_encryption')->first()->value ?? '') == 'none' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-100 pt-8">
                        <div class="flex items-center space-x-2">
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <span class="text-sm font-medium text-emerald-800 bg-emerald-50 px-3 py-1 rounded-full">System Online & Connected</span>
                        </div>
                        <button type="submit" class="inline-flex items-center px-10 py-4 bg-indigo-600 border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-2xl shadow-indigo-900/40 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25">
                            Save Global Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
