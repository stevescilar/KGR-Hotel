@extends('layouts.admin')
@section('title', 'Bookings')
@section('page-title', 'Bookings')
@section('breadcrumb', 'All reservations')

@section('content')
<div class="space-y-4">

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3 bg-white rounded-xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3 flex-1">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name, email or reference…"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060] w-64">

            <select name="status"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060]"
                    onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-[#1e3a2f] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#2e5c42]">
                Search
            </button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.bookings.index') }}" class="text-sm text-gray-400 hover:text-gray-600 self-center">Clear</a>
            @endif
        </form>

        <a href="{{ route('admin.bookings.create') }}"
           class="bg-[#c8974a] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#b5863a] ml-auto">
            + New Booking
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Reference</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Guest</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Room</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Dates</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-4 font-mono-kgr text-xs text-gray-600 font-medium">{{ $booking->booking_ref }}</td>
                    <td class="px-5 py-4">
                        <div class="font-medium text-gray-800">{{ $booking->guest->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $booking->guest->phone }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="font-medium text-gray-700">{{ $booking->room->roomType->name }}</div>
                        <div class="text-xs text-gray-400">Room {{ $booking->room->room_number }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-gray-700">{{ $booking->check_in->format('M j') }} → {{ $booking->check_out->format('M j, Y') }}</div>
                        <div class="text-xs text-gray-400">{{ $booking->nights }} night{{ $booking->nights > 1 ? 's' : '' }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="font-semibold text-gray-800">KES {{ number_format($booking->total_amount) }}</div>
                        <span @class([
                            'text-xs px-1.5 py-0.5 rounded font-medium',
                            'bg-green-100 text-green-700' => $booking->payment_status === 'paid',
                            'bg-amber-100 text-amber-700' => $booking->payment_status === 'partial',
                            'bg-red-100 text-red-600'     => $booking->payment_status === 'unpaid',
                        ])>{{ ucfirst($booking->payment_status) }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span @class([
                            'text-xs px-2.5 py-1 rounded-full font-semibold',
                            'bg-amber-100 text-amber-700'  => $booking->status === 'pending',
                            'bg-green-100 text-green-700'  => $booking->status === 'confirmed',
                            'bg-blue-100 text-blue-700'    => $booking->status === 'checked_in',
                            'bg-gray-100 text-gray-500'    => $booking->status === 'checked_out',
                            'bg-red-100 text-red-600'      => $booking->status === 'cancelled',
                        ])>{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('admin.bookings.show', $booking) }}"
                           class="text-[#4a8060] text-xs font-semibold hover:underline">View →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <div class="text-4xl mb-2">🛏</div>
                        No bookings found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($bookings->hasPages())
            <div class="px-5 py-4 border-t border-gray-50">{{ $bookings->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
