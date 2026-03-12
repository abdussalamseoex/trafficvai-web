@props(['title', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="mb-1">
    <button @click="open = !open" 
            class="flex items-center justify-between w-full px-4 py-2.5 text-sm font-medium transition-all duration-200 rounded-xl focus:outline-none 
                   {{ $active ? 'bg-brand/10 text-brand border-r-4 border-brand' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
        <div class="flex items-center">
            <div class="w-5 h-5 mr-3 flex-shrink-0 {{ $active ? 'text-brand' : 'text-gray-500' }}">
                {{ $icon ?? '' }}
            </div>
            <span class="{{ $active ? 'font-semibold' : '' }}">{{ $title }}</span>
        </div>
        <svg class="w-4 h-4 transition-transform duration-200" 
             :class="{'rotate-180': open}" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="pl-11 pr-4 py-1 space-y-1 mt-1 origin-top" x-cloak>
        {{ $slot }}
    </div>
</div>
