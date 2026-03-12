<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Gateway Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.gateway-settings.update') }}" method="POST">
                @csrf
                <div x-data="{ activeCategory: 'global', activeTab: 'stripe' }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                    
                    {{-- Desktop Sidebar Navigation --}}
                    <div class="flex flex-col md:flex-row min-h-[600px]">
                        {{-- Category Sidebar --}}
                        <div class="w-full md:w-64 bg-gray-50 border-r border-gray-100 p-6 flex flex-col space-y-6">
                            @foreach($gatewaysConfig as $category => $gateways)
                                <div>
                                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ ucfirst($category) }} Gateways</h3>
                                    <div class="space-y-1">
                                        @foreach($gateways as $slug => $gateway)
                                            <button type="button" 
                                                @click="activeCategory = '{{ $category }}'; activeTab = '{{ $slug }}'"
                                                :class="{ 'bg-indigo-50 text-indigo-700': activeTab === '{{ $slug }}', 'text-gray-600 hover:bg-gray-100': activeTab !== '{{ $slug }}' }"
                                                class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-3">
                                                <img src="{{ $gateway['logo'] }}" alt="{{ $gateway['name'] }}" class="w-5 h-5 object-contain rounded">
                                                <span class="truncate">{{ $gateway['name'] }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Content Area --}}
                        <div class="flex-1 p-6 md:p-8 bg-white">
                            @foreach($gatewaysConfig as $category => $gateways)
                                @foreach($gateways as $slug => $gateway)
                                    <div x-show="activeTab === '{{ $slug }}'" style="display: none;" class="animate-fade-in-up">
                                        <div class="flex items-center justify-between border-b border-gray-100 pb-5 mb-6">
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">{{ $gateway['name'] }} Configuration</h3>
                                                @if(isset($gateway['description']))
                                                    <p class="text-sm text-gray-500 mt-1">{{ $gateway['description'] }}</p>
                                                @endif
                                            </div>
                                            
                                            {{-- Enable/Disable Toggle --}}
                                            @if($slug !== 'wallet')
                                                <label class="flex items-center cursor-pointer">
                                                    <div class="relative">
                                                        <input type="checkbox" name="gateway_{{ $slug }}_enabled" class="sr-only" 
                                                               {{ old('gateway_'.$slug.'_enabled', $settings['gateway_'.$slug.'_enabled']->value ?? ($slug === 'bank_transfer' ? ($settings['bank_transfer_enabled']->value ?? '0') : '0')) == '1' ? 'checked' : '' }}>
                                                        <div class="block bg-gray-200 w-10 h-6 rounded-full transition-colors duration-300 peer-checked:bg-indigo-600"></div>
                                                        <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform peer-checked:translate-x-4"></div>
                                                    </div>
                                                    <div class="ml-3 text-sm font-medium text-gray-700">Enable {{ $gateway['name'] }}</div>
                                                </label>
                                            @else
                                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Always Active</span>
                                            @endif
                                        </div>

                                        <div class="space-y-6">
                                            @foreach($gateway['fields'] as $fieldKey => $field)
                                                @php
                                                    $settingKey = "gateway_{$slug}_{$fieldKey}";
                                                    // Mapping legacy keys if required
                                                    if ($slug === 'stripe') {
                                                        $settingKey = "stripe_{$fieldKey}";
                                                    }
                                                @endphp
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                        {{ $field['label'] }}
                                                    </label>
                                                    @if($field['type'] === 'textarea')
                                                        <textarea name="{{ $settingKey }}" rows="4" 
                                                                  class="w-full border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">{{ old($settingKey, $settings[$settingKey]->value ?? '') }}</textarea>
                                                    @elseif($field['type'] === 'password')
                                                        <div x-data="{ show: false }" class="relative">
                                                            <input :type="show ? 'text' : 'password'" name="{{ $settingKey }}" 
                                                                   value="{{ old($settingKey, $settings[$settingKey]->value ?? '') }}"
                                                                   class="w-full pl-4 pr-12 py-3 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                                                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                                <svg x-show="show" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <input type="text" name="{{ $settingKey }}" 
                                                               value="{{ old($settingKey, $settings[$settingKey]->value ?? '') }}"
                                                               class="w-full pl-4 pr-4 py-3 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                                    @endif
                                                    
                                                    @if(isset($field['hint']))
                                                        <p class="mt-2 text-xs text-gray-500">{{ $field['hint'] }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Save Configurations
                    </button>
                </div>
            </form>
        </div>
    </div>
    <style>
        .dot { transition: all 0.3s ease-in-out; }
        input:checked ~ .dot { transform: translateX(100%); background-color: #fff; }
        input:checked ~ .block { background-color: #4f46e5; }
    </style>
</x-app-layout>
