@extends('layouts.admin')
@section('title', 'Walk-in Receipt')

@push('styles')
<style>
@media print {
    @page { size: A4; margin: 1cm; }
    body > *:not(.print-area) { display: none !important; }
    .print-area { display: block !important; }
    .no-print { display: none !important; }
    .ticket-card { break-inside: avoid; page-break-inside: avoid; }
}
</style>
@endpush

@section('content')
<div class="p-6 max-w-3xl mx-auto print-area">

    {{-- Actions (hidden on print) --}}
    <div class="flex items-center justify-between mb-6 no-print">
        <div>
            <a href="{{ route('admin.rooms.index') }}" class="text-sm text-green-700 hover:text-green-900">← Back to Rooms</a>
            <h1 class="text-2xl font-display text-gray-900 mt-1">Walk-in Receipt</h1>
        </div>
        <button onclick="window.print()" class="bg-green-900 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-green-800">
            🖨 Print Receipt & Tickets
        </button>
    </div>

    {{-- Receipt header --}}
    <div class="bg-green-900 text-white rounded-xl p-6 mb-4 text-center">
        <div class="font-display text-xl mb-1">🌿 Kitonga Garden Resort</div>
        <div class="text-green-300 text-xs uppercase tracking-widest">Walk-in Booking Receipt</div>
        <div class="font-mono text-lg font-bold mt-2">{{ $booking->booking_ref }}</div>
    </div>

    {{-- Booking details --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-4">
        <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
            <div><span class="text-gray-400 text-xs uppercase tracking-wide">Guest</span><div class="font-semibold text-gray-900">{{ $booking->guest->first_name }} {{ $booking->guest->last_name }}</div></div>
            <div><span class="text-gray-400 text-xs uppercase tracking-wide">Phone</span><div class="font-semibold text-gray-900">{{ $booking->guest->phone }}</div></div>
            <div><span class="text-gray-400 text-xs uppercase tracking-wide">Room</span><div class="font-semibold text-gray-900">{{ $booking->room->room_number }} — {{ $booking->room->roomType->name }}</div></div>
            <div><span class="text-gray-400 text-xs uppercase tracking-wide">Guests</span><div class="font-semibold text-gray-900">{{ $booking->adults }} adult{{ $booking->adults !== 1 ? 's' : '' }}{{ $booking->children ? ", {$booking->children} child" . ($booking->children !== 1 ? 'ren' : '') : '' }}</div></div>
            <div><span class="text-gray-400 text-xs uppercase tracking-wide">Check-in</span><div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->check_in)->format('D, M j Y') }}</div></div>
            <div><span class="text-gray-400 text-xs uppercase tracking-wide">Check-out</span><div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->check_out)->format('D, M j Y') }}</div></div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
            <span class="text-sm text-gray-500">Total Charged</span>
            <span class="font-display text-xl font-semibold text-green-900">KES {{ number_format($booking->total_amount) }}</span>
        </div>
        <div class="flex justify-between items-center mt-1">
            <span class="text-sm text-gray-500">Amount Paid</span>
            <span class="font-semibold text-green-700">KES {{ number_format($booking->paid_amount) }}
                @if($booking->payments->first())<span class="text-xs text-gray-400 font-normal">via {{ ucfirst($booking->payments->first()->method) }}</span>@endif
            </span>
        </div>
        @if($booking->paid_amount < $booking->total_amount)
        <div class="flex justify-between items-center mt-1">
            <span class="text-sm text-red-500">Balance Due</span>
            <span class="font-semibold text-red-600">KES {{ number_format($booking->total_amount - $booking->paid_amount) }}</span>
        </div>
        @endif
    </div>

    {{-- Resort Access Tickets --}}
    <div class="mb-2">
        <div class="text-xs font-bold text-amber-600 uppercase tracking-widest mb-3">Resort Access Tickets ({{ $tickets->count() }})</div>
        <div class="space-y-3">
            @foreach($tickets as $ticket)
            <div class="ticket-card bg-white border-2 border-dashed border-green-700 rounded-xl overflow-hidden">
                <div class="bg-green-900 px-5 py-3 flex items-center justify-between">
                    <div class="text-white">
                        <div class="text-xs text-green-300 uppercase tracking-widest">Resort Access Ticket</div>
                        <div class="font-mono font-bold text-lg">{{ $ticket->ticket_number }}</div>
                    </div>
                    <div class="text-right text-white">
                        <div class="text-xs text-green-300">Valid</div>
                        <div class="font-semibold text-sm">{{ $ticket->valid_date->format('M j, Y') }}</div>
                    </div>
                </div>
                <div class="px-5 py-4 flex items-center justify-between">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Guest Name</div>
                        <div class="font-semibold text-gray-900">{{ $ticket->guest_name }}</div>
                        @if($ticket->guest_phone)
                        <div class="text-xs text-gray-500 mt-0.5">{{ $ticket->guest_phone }}</div>
                        @endif
                        <div class="mt-2 inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs font-bold px-2 py-1 rounded-full">
                            🌿 KES {{ number_format($ticket->amount) }} · Kitonga Garden Resort
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-400 mb-1">QR Code</div>
                        <div class="bg-gray-100 rounded-lg p-2 font-mono text-xs text-gray-600">{{ $ticket->qr_code }}</div>
                        <div class="text-xs text-gray-400 mt-1">Show at gate</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="text-center text-xs text-gray-400 mt-6 no-print">
        Kitonga Garden Resort · Ukasi, Kitui County · +254 113 262 688
    </div>
</div>
@endsection