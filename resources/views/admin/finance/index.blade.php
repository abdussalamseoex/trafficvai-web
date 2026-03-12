<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Financial Analytics') }}
        </h2>
    </x-slot>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Top Metrics Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- MRR Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <p class="text-sm font-medium text-gray-500 truncate">Monthly Recurring Revenue (MRR)</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($mrr, 2) }}</p>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">From active SEO packages</p>
                </div>

                <!-- Total Revenue Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500 truncate">All-Time Revenue</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
                    </div>
                </div>

                <!-- Client Growth Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ $growthRate >= 0 ? 'border-emerald-500' : 'border-red-500' }}">
                    <p class="text-sm font-medium text-gray-500 truncate">Client Growth (This Month)</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-bold text-gray-900">{{ $newClientsThisMonth }}</p>
                        <p class="ml-2 flex items-baseline text-sm font-semibold {{ $growthRate >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($growthRate >= 0)
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                +{{ number_format($growthRate, 1) }}%
                            @else
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                                {{ number_format($growthRate, 1) }}%
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Revenue Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Revenue Trend (6 Months)</h3>
                    <div class="relative h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Top Services Table/Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Top Selling Packages</h3>
                    @if($topPackages->count() > 0)
                        <div class="flow-root">
                            <ul role="list" class="-my-5 divide-y divide-gray-200">
                                @foreach($topPackages as $stat)
                                    <li class="py-4 flex items-center justify-between">
                                        <div class="flex flex-col">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $stat->package->name }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $stat->package->service->title ?? 'Package' }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ $stat->total }} Sold
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No data available yet.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Chart Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [{
                        label: 'Revenue ($)',
                        data: {!! json_encode($monthlyRevenue) !!},
                        borderColor: 'rgb(79, 70, 229)', // Indigo 600
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
