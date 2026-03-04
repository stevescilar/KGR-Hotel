@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ── STAT CARDS ────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        @php
        $cards = [
            ['label' => "Today's Check-ins",  'value' => $stats['todays_checkins'],  'icon' => '🛬', 'color' => 'green'],
            ['label' => "Today's Check-outs", 'value' => $stats['todays_checkouts'], 'icon' => '🛫', 'color' => 'amber'],
            ['label' => "Today's Revenue",    'value' => 'KES ' . number_format($stats['todays_revenue']),  'icon' => '💵', 'color' => 'blue'],
            ['label' => "Monthly Revenue",    'value' => 'KES ' . number_format($stats['monthly_revenue']), 'icon' => '📈', 'color' => 'purple'],
            ['label' => 'Occupied Rooms',     'value' => $stats['occupied_rooms'] . ' / ' . $stats['total_rooms'], 'icon' => '🏠', 'color' => 'green'],
            ['label' => 'Pending Bookings',   'value' => $stats['pending_bookings'], 'icon' => '⏳', 'color' => 'amber'],
            ['label' => 'Open Orders',        'value' => $stats['open_orders'],      'icon' => '🍽', 'color' => 'blue'],
            ['label' => 'New Applications',   'value' => $stats['new_applications'], 'icon' => '💼', 'color' => 'purple'],
        ];
        $colors = [
            'green'  => 'bg-green-50 text-green-700 border-green-100',
            'amber'  => 'bg-amber-50 text-amber-700 border-amber-100',
            'blue'   => 'bg-blue-50 text-blue-700 border-blue-100',
            'purple' => 'bg-purple-50 text-purple-700 border-purple-100',
        ];
        @endphp

        @foreach($cards as $card)
        <div class="bg-white rounded-xl border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="text-2xl">{{ $card['icon'] }}</div>
                <div class="text-xs font-semibold px-2 py-1 rounded-full border {{ $colors[$card['color']] }}">
                    Live
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-800 font-display mb-1">{{ $card['value'] }}</div>
            <div class="text-xs text-gray-400 font-medium uppercase tracking-wide">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── ROOM OCCUPANCY ─────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-lg text-[#1e3a2f]">Room Status</h3>
                <span class="text-2xl font-bold text-[#1e3a2f]">{{ $occupancyRate }}%</span>
            </div>

            {{-- Occupancy bar --}}
            @php
            $rooms = \App\Models\Room::with('roomType')->orderBy('room_number')->get();
            $statusColors = ['available' => 'bg-green-400', 'occupied' => 'bg-red-400', 'cleaning' => 'bg-amber-400', 'maintenance' => 'bg-gray-400'];
            @endphp
            <div class="flex flex-wrap gap-1.5 mb-4">
                @foreach($rooms as $room)
                    <div title="{{ $room->room_number }} — {{ ucfirst($room->status) }}"
                         class="w-7 h-7 rounded-md text-white text-xs font-bold flex items-center justify-center cursor-default
                                {{ $statusColors[$room->status] ?? 'bg-gray-300' }}">
                        {{ substr($room->room_number, -2) }}
                    </div>
                @endforeach
            </div>

            {{-- Legend --}}
            <div class="flex flex-wrap gap-3 text-xs text-gray-500">
                @foreach($statusColors as $status => $color)
                    <span class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-sm {{ $color }}"></span>
                        {{ ucfirst($status) }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- ── REVENUE CHART ───────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 lg:col-span-2">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">7-Day Revenue</h3>
            <canvas id="revenueChart" height="120"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ── TODAY'S ARRIVALS ────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-lg text-[#1e3a2f]">Today's Arrivals</h3>
                <span class="text-xs text-gray-400">{{ $todayCheckins->count() }} guests</span>
            </div>
            @forelse($todayCheckins as $booking)
                <div class="flex items-center gap-3 py-3 border-b border-gray-50 last:border-0">
                    <div class="w-9 h-9 rounded-full bg-[#1e3a2f] flex items-center justify-center text-white text-xs font-bold font-mono-kgr flex-shrink-0">
                        {{ strtoupper(substr($booking->guest->first_name, 0, 1) . substr($booking->guest->last_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-800 truncate">{{ $booking->guest->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $booking->room->roomType->name }} · Rm {{ $booking->room->room_number }}</div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span @class([
                            'text-xs font-semibold px-2 py-1 rounded-full',
                            'bg-green-100 text-green-700' => $booking->status === 'checked_in',
                            'bg-amber-100 text-amber-700' => $booking->status === 'confirmed',
                        ])>
                            {{ $booking->status === 'checked_in' ? '✓ In' : 'Arriving' }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-6">No arrivals scheduled today</p>
            @endforelse
        </div>

        {{-- ── RECENT BOOKINGS ─────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-lg text-[#1e3a2f]">Recent Bookings</h3>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs text-[#4a8060] hover:underline">View all →</a>
            </div>
            @foreach($recentBookings as $booking)
                <a href="{{ route('admin.bookings.show', $booking) }}"
                   class="flex items-center gap-3 py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded-lg transition-colors">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-800 truncate">{{ $booking->guest->full_name }}</div>
                        <div class="text-xs text-gray-400 font-mono-kgr">{{ $booking->booking_ref }}</div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-xs font-semibold text-gray-600">KES {{ number_format($booking->total_amount) }}</div>
                        <span @class([
                            'text-xs px-2 py-0.5 rounded-full font-medium',
                            'bg-green-100 text-green-700'  => $booking->status === 'confirmed',
                            'bg-blue-100 text-blue-700'    => $booking->status === 'checked_in',
                            'bg-gray-100 text-gray-500'    => $booking->status === 'checked_out',
                            'bg-amber-100 text-amber-700'  => $booking->status === 'pending',
                            'bg-red-100 text-red-600'      => $booking->status === 'cancelled',
                        ])>{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const data = @json($revenueChart);
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: data.map(d => d.date),
        datasets: [
            { label: 'Rooms', data: data.map(d => d.rooms),   backgroundColor: '#2e5c42' },
            { label: 'F&B',   data: data.map(d => d.fnb),     backgroundColor: '#7aaa8a' },
            { label: 'Tickets', data: data.map(d => d.tickets), backgroundColor: '#c8974a' },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } } },
        scales: {
            x: { stacked: true, grid: { display: false }, ticks: { font: { size: 11 } } },
            y: { stacked: true, ticks: { callback: v => 'KES ' + (v/1000).toFixed(0) + 'k', font: { size: 11 } }, grid: { color: '#f0f0f0' } }
        }
    }
});
</script>
@endpush
