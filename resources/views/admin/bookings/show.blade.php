@extends('layouts.admin')
@section('title', $booking->booking_ref)
@section('page-title', $booking->booking_ref)
@section('breadcrumb', 'Bookings / ' . $booking->booking_ref)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── LEFT: Booking Details ──────────────────────────── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Status bar --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 flex flex-wrap items-center gap-4">
            <div>
                <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Status</div>
                <span @class([
                    'text-sm px-3 py-1.5 rounded-full font-semibold',
                    'bg-amber-100 text-amber-700'  => $booking->status === 'pending',
                    'bg-green-100 text-green-700'  => $booking->status === 'confirmed',
                    'bg-blue-100 text-blue-700'    => $booking->status === 'checked_in',
                    'bg-gray-100 text-gray-500'    => $booking->status === 'checked_out',
                    'bg-red-100 text-red-600'      => $booking->status === 'cancelled',
                ])>{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
            </div>
            <div>
                <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Payment</div>
                <span @class([
                    'text-sm px-3 py-1.5 rounded-full font-semibold',
                    'bg-green-100 text-green-700' => $booking->payment_status === 'paid',
                    'bg-amber-100 text-amber-700' => $booking->payment_status === 'partial',
                    'bg-red-100 text-red-600'     => $booking->payment_status === 'unpaid',
                ])>{{ ucfirst($booking->payment_status) }}</span>
            </div>
            <div class="ml-auto flex gap-2 flex-wrap">
                @if($booking->status === 'confirmed')
                    <form method="POST" action="{{ route('admin.bookings.check-in', $booking) }}">
                        @csrf @method('PATCH')
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                            ✓ Check In
                        </button>
                    </form>
                @endif
                @if($booking->status === 'checked_in')
                    <form method="POST" action="{{ route('admin.bookings.check-out', $booking) }}">
                        @csrf @method('PATCH')
                        <button class="bg-[#1e3a2f] hover:bg-[#2e5c42] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                            ✓ Check Out
                        </button>
                    </form>
                @endif
                @if(!in_array($booking->status, ['checked_out','cancelled']))
                    <button onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                            class="border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Cancel
                    </button>
                @endif
            </div>
        </div>

        {{-- Room & Dates --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">Stay Details</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach([
                    ['Room Type', $booking->room->roomType->name],
                    ['Room Number', $booking->room->room_number],
                    ['Check-in', $booking->check_in->format('D, M j Y')],
                    ['Check-out', $booking->check_out->format('D, M j Y')],
                    ['Nights', $booking->nights],
                    ['Adults', $booking->adults],
                    ['Children', $booking->children],
                    ['Source', ucfirst(str_replace('_',' ',$booking->source ?? 'website'))],
                ] as [$label, $value])
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ $label }}</div>
                        <div class="font-semibold text-gray-800 text-sm">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
            @if($booking->special_requests)
                <div class="mt-4 bg-amber-50 border border-amber-100 rounded-lg p-3">
                    <div class="text-xs text-amber-700 font-semibold uppercase tracking-wide mb-1">Special Requests</div>
                    <div class="text-sm text-amber-900">{{ $booking->special_requests }}</div>
                </div>
            @endif
        </div>

        {{-- Payments --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">Payments</h3>
            @forelse($booking->payments as $payment)
                <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ strtoupper($payment->method) }}</div>
                        <div class="text-xs text-gray-400 font-mono-kgr">{{ $payment->provider_reference ?? $payment->reference }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-gray-800">KES {{ number_format($payment->amount) }}</div>
                        <span @class([
                            'text-xs px-1.5 py-0.5 rounded font-medium',
                            'bg-green-100 text-green-700'  => $payment->status === 'completed',
                            'bg-amber-100 text-amber-700'  => $payment->status === 'pending',
                            'bg-red-100 text-red-600'      => $payment->status === 'failed',
                        ])>{{ ucfirst($payment->status) }}</span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-4">No payment records yet</p>
            @endforelse

            <div class="mt-4 pt-4 border-t border-dashed border-gray-200 flex justify-between text-sm">
                <span class="text-gray-500">Total Charged</span>
                <span class="font-bold text-gray-800">KES {{ number_format($booking->total_amount) }}</span>
            </div>
            <div class="flex justify-between text-sm mt-1">
                <span class="text-gray-500">Total Paid</span>
                <span class="font-bold text-green-600">KES {{ number_format($booking->paid_amount) }}</span>
            </div>
            @if($booking->balance > 0)
                <div class="flex justify-between text-sm mt-1">
                    <span class="text-gray-500">Balance Due</span>
                    <span class="font-bold text-red-600">KES {{ number_format($booking->balance) }}</span>
                </div>
            @endif
        </div>

        {{-- Orders (Room Service) --}}
        @if($booking->orders->count())
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">Room Service Orders</h3>
            @foreach($booking->orders as $order)
                <div class="border border-gray-100 rounded-lg p-4 mb-3">
                    <div class="flex justify-between items-start mb-2">
                        <div class="text-sm font-semibold text-gray-700 font-mono-kgr">{{ $order->order_number }}</div>
                        <div class="text-sm font-bold text-gray-800">KES {{ number_format($order->total) }}</div>
                    </div>
                    @foreach($order->items as $item)
                        <div class="text-xs text-gray-500">{{ $item->quantity }}× {{ $item->menuItem->name }}</div>
                    @endforeach
                </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── RIGHT: Guest Card ───────────────────────────────── --}}
    <div class="space-y-5">
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">Guest</h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-[#1e3a2f] flex items-center justify-center text-white font-bold font-mono-kgr">
                    {{ strtoupper(substr($booking->guest->first_name,0,1).substr($booking->guest->last_name,0,1)) }}
                </div>
                <div>
                    <div class="font-semibold text-gray-800">{{ $booking->guest->full_name }}</div>
                    <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full capitalize">
                        {{ $booking->guest->vip_tier ?? 'none' }} tier
                    </span>
                </div>
            </div>
            @foreach([
                ['📧', $booking->guest->email],
                ['📱', $booking->guest->phone],
                ['🌍', $booking->guest->nationality],
                ['🪪', $booking->guest->id_number ? '••••' . substr($booking->guest->id_number, -4) : '—'],
                ['⭐', number_format($booking->guest->loyalty_points) . ' pts'],
            ] as [$icon, $val])
                @if($val)
                <div class="flex items-center gap-2 py-1.5 text-sm text-gray-600">
                    <span>{{ $icon }}</span> {{ $val }}
                </div>
                @endif
            @endforeach

            <a href="{{ route('admin.guests.show', $booking->guest) }}"
               class="mt-3 block text-center text-xs text-[#4a8060] hover:underline font-semibold">
               View full guest profile →
            </a>
        </div>

        {{-- Price Summary --}}
        <div class="bg-[#f0e9d8] rounded-xl border border-amber-100 p-5">
            <div class="text-xs text-[#c8974a] font-semibold uppercase tracking-wide mb-3">Price Summary</div>
            @foreach([
                ['Room subtotal', 'KES ' . number_format($booking->subtotal)],
                ['VAT (16%)', 'KES ' . number_format($booking->tax_amount)],
                ['Discount', $booking->discount_amount > 0 ? '− KES ' . number_format($booking->discount_amount) : '—'],
            ] as [$l, $v])
                <div class="flex justify-between text-sm py-1.5 border-b border-amber-100 last:border-0">
                    <span class="text-gray-600">{{ $l }}</span>
                    <span class="font-medium text-gray-800">{{ $v }}</span>
                </div>
            @endforeach
            <div class="flex justify-between items-center mt-3 pt-3 border-t border-amber-200">
                <span class="font-bold text-[#1e3a2f]">Total</span>
                <span class="font-display text-2xl font-bold text-[#1e3a2f]">KES {{ number_format($booking->total_amount) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full">
        <h3 class="font-display text-xl text-[#1e3a2f] mb-2">Cancel Booking?</h3>
        <p class="text-sm text-gray-500 mb-4">This action cannot be undone. Please provide a reason.</p>
        <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}">
            @csrf @method('PATCH')
            <textarea name="reason" placeholder="Reason for cancellation…"
                      class="w-full border border-gray-200 rounded-lg p-3 text-sm outline-none focus:border-red-300 mb-4" rows="3"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-lg text-sm font-semibold hover:bg-gray-50">
                    Keep Booking
                </button>
                <button type="submit"
                        class="flex-1 bg-red-600 text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-red-700">
                    Yes, Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
