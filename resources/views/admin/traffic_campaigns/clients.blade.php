<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-500/10 text-purple-600 border border-purple-500/20">Clients Overview</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-500/10 text-orange-600 border border-orange-500/20">Core Automation Engine</span>
                </div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">Traffic Campaign — Clients Overview</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.traffic_campaigns.index') }}" class="px-4 py-2 rounded-xl bg-gray-800 text-white font-bold text-xs shadow hover:bg-gray-700 transition">All Campaigns</a>
                <a href="{{ route('admin.traffic_campaigns.active') }}" class="px-4 py-2 rounded-xl bg-orange-500 text-white font-bold text-xs shadow hover:bg-orange-600 transition">Active Running</a>
                <a href="{{ route('admin.traffic_campaigns.clients') }}" class="px-4 py-2 rounded-xl bg-purple-600 text-white font-bold text-xs shadow">👥 Clients Overview</a>
                <a href="{{ route('admin.traffic_campaigns.ledger') }}" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs shadow hover:bg-blue-700 transition">Points Ledger & Topups</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold text-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-red-50 text-red-800 border border-red-200 font-semibold text-sm">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            {{-- Summary Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">মোট Clients</span>
                    <span class="text-3xl font-black text-gray-900">{{ number_format($overallStats['total_clients']) }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm">
                    <span class="text-xs font-bold text-red-500 uppercase tracking-wider block mb-1">⚠️ পয়েন্ট শেষ</span>
                    <span class="text-3xl font-black text-red-600">{{ number_format($overallStats['zero_balance']) }}</span>
                    <span class="text-xs text-red-400 block mt-1">ক্লায়েন্টের পয়েন্ট নেই</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm">
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block mb-1">🟢 Active Campaigns</span>
                    <span class="text-3xl font-black text-emerald-700">{{ number_format($overallStats['total_active']) }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-sm">
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block mb-1">⏸ Paused Campaigns</span>
                    <span class="text-3xl font-black text-amber-700">{{ number_format($overallStats['total_paused']) }}</span>
                </div>
            </div>

            {{-- Search --}}
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('admin.traffic_campaigns.clients') }}" class="flex items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="ক্লায়েন্টের নাম বা ইমেইল খুঁজুন..."
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                    <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white rounded-xl font-bold text-sm hover:bg-purple-700 transition">খুঁজুন</button>
                    @if(request('search'))
                        <a href="{{ route('admin.traffic_campaigns.clients') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition">✕ Clear</a>
                    @endif
                </form>
            </div>

            {{-- Clients Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Points Balance</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-emerald-600 uppercase tracking-wider">🟢 Active</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-amber-600 uppercase tracking-wider">⏸ Paused</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">✅ Done</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Hits</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($clients as $i => $client)
                                @php
                                    $isZeroBalance = $client->traffic_points <= 0;
                                    $hasPaused = $client->paused_campaigns > 0;
                                    $hasActive = $client->active_campaigns > 0;
                                @endphp
                                <tr class="{{ $isZeroBalance && $hasPaused ? 'bg-red-50/40' : 'hover:bg-gray-50/50' }} transition">
                                    <td class="px-5 py-4 text-gray-400 font-medium text-xs">
                                        {{ $clients->firstItem() + $i }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm flex-shrink-0">
                                                {{ strtoupper(substr($client->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 text-sm">{{ $client->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $client->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($isZeroBalance)
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-red-100 text-red-700 font-black text-sm border border-red-200">
                                                ⚠️ 0 pts
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-sm border border-emerald-100">
                                                {{ number_format($client->traffic_points) }} pts
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($client->active_campaigns > 0)
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 font-black text-base border border-emerald-200">
                                                {{ $client->active_campaigns }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 font-bold">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($client->paused_campaigns > 0)
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-amber-100 text-amber-700 font-black text-base border border-amber-200">
                                                {{ $client->paused_campaigns }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 font-bold">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if($client->completed_campaigns > 0)
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 text-gray-600 font-black text-base border border-gray-200">
                                                {{ $client->completed_campaigns }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 font-bold">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="font-bold text-gray-700 text-sm">{{ number_format($client->total_hits ?? 0) }}</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- View Campaigns --}}
                                            <a href="{{ route('admin.traffic_campaigns.index') }}?search={{ urlencode($client->email) }}"
                                                class="px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-bold hover:bg-gray-700 transition"
                                                title="এই ক্লায়েন্টের সব ক্যাম্পেইন দেখুন">
                                                📋 দেখুন
                                            </a>
                                            {{-- Add Points Button (triggers modal) --}}
                                            <button
                                                onclick="openPointsModal({{ $client->id }}, '{{ addslashes($client->name) }}', {{ $client->traffic_points }})"
                                                class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition"
                                                title="পয়েন্ট যোগ করুন">
                                                ➕ পয়েন্ট
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-16 text-center text-gray-400">
                                        <div class="text-5xl mb-3">👥</div>
                                        <div class="font-bold text-lg">কোনো ক্লায়েন্ট পাওয়া যায়নি</div>
                                        <div class="text-sm mt-1">এখনো কোনো ক্লায়েন্ট ট্রাফিক ক্যাম্পেইন তৈরি করেনি।</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($clients->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $clients->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Quick Add Points Modal --}}
    <div id="pointsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-black text-gray-900">পয়েন্ট যোগ করুন</h3>
                    <p class="text-sm text-gray-500 mt-0.5" id="modalClientName">Client Name</p>
                </div>
                <button onclick="closePointsModal()" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 text-lg font-bold transition">✕</button>
            </div>

            <div class="mb-4 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                <span class="text-xs text-gray-500 block mb-1">বর্তমান Balance</span>
                <span id="modalCurrentBalance" class="text-2xl font-black text-gray-900">0 pts</span>
            </div>

            <form id="pointsForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">কত পয়েন্ট যোগ করবেন?</label>
                        <input type="number" name="points" id="modalPoints" min="1" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-lg font-black text-gray-900 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                            placeholder="যেমন: 50000">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2 uppercase tracking-wide">নোট (ঐচ্ছিক)</label>
                        <input type="text" name="description"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100"
                            placeholder="যেমন: জানুয়ারি টপ আপ">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closePointsModal()"
                        class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition">বাতিল</button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-emerald-600 text-white font-black hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">
                        ✅ পয়েন্ট যোগ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openPointsModal(userId, clientName, currentBalance) {
        document.getElementById('modalClientName').textContent = clientName;
        document.getElementById('modalCurrentBalance').textContent = Number(currentBalance).toLocaleString() + ' pts';
        document.getElementById('pointsForm').action = '/admin/traffic-campaigns/clients/' + userId + '/add-points';
        document.getElementById('modalPoints').value = '';
        const modal = document.getElementById('pointsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => document.getElementById('modalPoints').focus(), 100);
    }

    function closePointsModal() {
        const modal = document.getElementById('pointsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close on backdrop click
    document.getElementById('pointsModal').addEventListener('click', function(e) {
        if (e.target === this) closePointsModal();
    });
    </script>
</x-app-layout>
