@php
    $c = $section->content;
@endphp
<div class="bg-gray-900 py-16 sm:py-24">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-y-16 text-center lg:grid-cols-4">
            <div class="flex flex-col gap-y-4">
                <dt class="text-base leading-7 text-gray-400">{{ $c['stat_1_label'] ?? 'Placements Built' }}</dt>
                <dd class="order-first text-3xl font-black tracking-tight text-white sm:text-5xl">{{ $c['stat_1_value'] ?? '15,000+' }}</dd>
            </div>
            <div class="flex flex-col gap-y-4">
                <dt class="text-base leading-7 text-gray-400">{{ $c['stat_2_label'] ?? 'Active Agencies' }}</dt>
                <dd class="order-first text-3xl font-black tracking-tight text-white sm:text-5xl">{{ $c['stat_2_value'] ?? '1,200+' }}</dd>
            </div>
            <div class="flex flex-col gap-y-4">
                <dt class="text-base leading-7 text-gray-400">{{ $c['stat_3_label'] ?? 'Average DA' }}</dt>
                <dd class="order-first text-3xl font-black tracking-tight text-white sm:text-5xl">{{ $c['stat_3_value'] ?? '45+' }}</dd>
            </div>
            <div class="flex flex-col gap-y-4">
                <dt class="text-base leading-7 text-gray-400">{{ $c['stat_4_label'] ?? 'Delivery Success' }}</dt>
                <dd class="order-first text-3xl font-black tracking-tight text-white sm:text-5xl">{{ $c['stat_4_value'] ?? '99.8%' }}</dd>
            </div>
        </div>
    </div>
</div>
