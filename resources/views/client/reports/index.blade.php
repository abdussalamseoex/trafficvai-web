<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Performance Reports') }}
        </h2>
    </x-slot>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Dashboard Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Analytics Overview</h3>
                    <p class="mt-1 text-sm text-gray-500">Track your SEO progress and organic growth.</p>
                </div>
                <!-- Mock property selector -->
                <div class="w-full md:w-64">
                    <select class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                        <option>All Properties (Aggregated)</option>
                        @forelse($activeCampaigns as $campaign)
                            @if($campaign->orderable_type === \App\Models\Package::class)
                                <option>{{ $campaign->orderable->name }} Campaign</option>
                            @endif
                        @empty
                            <option>Demo Property</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <!-- Notice / Call to Action -->
            <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100 flex items-start sm:items-center justify-between flex-col sm:flex-row">
                <div class="flex items-center mb-4 sm:mb-0">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-indigo-800">Connect Google Analytics & Search Console</h3>
                        <p class="text-sm text-indigo-700 mt-1">For real-time exact data, please connect your tracking accounts.</p>
                    </div>
                </div>
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    Connect Accounts
                </button>
            </div>

            <!-- Top Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat 1 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 -mr-8 -mt-8 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-32 h-32 text-indigo-500" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Organic Traffic (30 Days)</p>
                    <div class="mt-2 flex items-baseline">
                        <p class="text-3xl font-bold text-gray-900">4,281</p>
                        <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                            <svg class="self-center flex-shrink-0 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                            <span class="sr-only">Increased by</span>
                            12.5%
                        </p>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 -mr-8 -mt-8 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-32 h-32 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Top 10 Keywords</p>
                    <div class="mt-2 flex items-baseline">
                        <p class="text-3xl font-bold text-gray-900">34</p>
                        <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                            <svg class="self-center flex-shrink-0 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                            <span class="sr-only">Increased by</span>
                            +5 this week
                        </p>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 -mr-8 -mt-8 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-32 h-32 text-purple-500" fill="currentColor" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">New Backlinks</p>
                    <div class="mt-2 flex items-baseline">
                        <p class="text-3xl font-bold text-gray-900">112</p>
                        <p class="ml-2 flex items-baseline text-sm font-semibold text-gray-500">
                            Last 30 days
                        </p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Traffic Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Traffic Growth (6 Months)</h4>
                    <div class="relative h-72 w-full">
                        <canvas id="trafficChart"></canvas>
                    </div>
                </div>

                <!-- Keyword Distribution Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Keyword Distribution</h4>
                    <div class="relative h-72 w-full flex justify-center">
                        <canvas id="keywordChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Active Campaigns List -->
            @if(count($activeCampaigns) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Active SEO Campaigns</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($activeCampaigns as $campaign)
                        <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-bold text-gray-900">
                                        @if($campaign->orderable_type === \App\Models\Package::class)
                                            {{ $campaign->orderable->service->title ?? 'SEO Package' }} - {{ $campaign->orderable->name }}
                                        @else
                                            Custom SEO Service
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-500">Started on {{ $campaign->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Optimizing</span>
                                <a href="{{ route('client.orders.show', $campaign->id) }}" class="block mt-1 text-sm text-indigo-600 hover:text-indigo-900 font-medium">View Project &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Chart Implementations -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Traffic Line Chart
            const trafficCtx = document.getElementById('trafficChart').getContext('2d');
            
            // Create gradient
            let gradient = trafficCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)'); // indigo-600
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

            new Chart(trafficCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Organic Visitors',
                        data: [1200, 1900, 2100, 2800, 3400, 4281],
                        borderColor: '#4f46e5', // indigo-600
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // smooth curve
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            padding: 10,
                            titleFont: { size: 13 },
                            bodyFont: { size: 14, weight: 'bold' },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' Visitors';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6', drawBorder: false },
                            ticks: { color: '#6b7280', padding: 10 }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { color: '#6b7280', padding: 10 }
                        }
                    }
                }
            });

            // Keyword Distribution Doughnut Chart
            const keywordCtx = document.getElementById('keywordChart').getContext('2d');
            new Chart(keywordCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Top 3', 'Pos 4-10', 'Pos 11-20', 'Pos 21-50'],
                    datasets: [{
                        data: [12, 22, 45, 120],
                        backgroundColor: [
                            '#10b981', // green-500
                            '#3b82f6', // blue-500
                            '#8b5cf6', // purple-500
                            '#e5e7eb'  // gray-200
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                color: '#4b5563',
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.parsed + ' Keywords';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
