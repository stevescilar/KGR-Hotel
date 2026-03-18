@extends('layouts.admin')
@section('title', 'Balance Due Tracker')

@section('content')
<div class="p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display text-gray-900">Balance Due Tracker</h1>
            <p class="text-sm text-gray-500 mt-1">Guests who paid a deposit — balance collectible on arrival</p>
        </div>
        <div class="text-right">
            <div class="text-xs text-gray-400 uppercase tracking-wide">Total Outstanding</div>
            <div class="text-2xl font-display font-semibold text-amber-600">
                KES {{ number_format($bookings->sum(fn($b) => $b->total_amount - $b->paid_amount)) }}
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 text-sm">✓ {{ session('success') }}</div>
    @endif

    {{-- Filter tabs --}}
    <div class="flex gap-2 mb-5">
        @foreach(['all' => 'All', 'arriving_today' => 'Arriving Today', 'checked_in' => 'Checked In', 'overdue' => 'Overdue'] as $val => $label)
        <a href="?filter={{ $val }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors
                  {{ (request('filter', 'all') === $val) ? 'bg-green-900 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Booking Ref</th>
                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Guest</th>
                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Room</th>
                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Check-in</th>
                    <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Paid</th>
                    <th class="text-right px-4 py-3 text-xs font-bold text-amber-600 uppercase tracking-wider">Balance Due</th>
                    <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bookings as $booking)
                @php
                    $balance    = $booking->total_amount - $booking->paid_amount;
                    $checkIn    = \Carbon\Carbon::parse($booking->check_in);
                    $isToday    = $checkIn->isToday();
                    $isPast     = $checkIn->isPast() && $booking->status !== 'checked_in';
                @endphp
                <tr class="hover:bg-gray-50 {{ $isPast ? 'bg-red-50/30' : ($isToday ? 'bg-amber-50/30' : '') }}">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $booking->booking_ref }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $booking->guest->first_name }} {{ $booking->guest->last_name }}</div>
                        <div class="text-xs text-gray-400">{{ $booking->guest->phone }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        <div>{{ $booking->room->roomType->name }}</div>
                        <div class="text-xs text-gray-400">Room {{ $booking->room->room_number }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-700">{{ $checkIn->format('M j, Y') }}</div>
                        @if($isToday)
                            <span class="inline-block bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">Today</span>
                        @elseif($isPast)
                            <span class="inline-block bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">Overdue</span>
                        @else
                            <div class="text-xs text-gray-400">In {{ $checkIn->diffForHumans() }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">KES {{ number_format($booking->total_amount) }}</td>
                    <td class="px-4 py-3 text-right text-green-700 font-semibold">KES {{ number_format($booking->paid_amount) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-amber-600 text-base">KES {{ number_format($balance) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $booking->status === 'checked_in' ? 'bg-blue-100 text-blue-700' :
                               ($booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500') }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('admin.bookings.show', $booking) }}"
                               class="text-green-700 hover:text-green-900 font-medium text-xs">View</a>
                            <form method="POST" action="{{ route('admin.bookings.collect-balance', $booking) }}"
                                  onsubmit="return confirm('Mark balance of KES {{ number_format($balance) }} as collected?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="bg-amber-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-amber-600 transition-colors whitespace-nowrap">
                                    Collect Balance
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                        <div class="text-3xl mb-2">✅</div>
                        <p>No outstanding balances. All bookings are fully paid.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($bookings->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $bookings->links() }}</div>
        @endif
    </div>
</div>
@endsection