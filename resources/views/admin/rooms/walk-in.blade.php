@extends('layouts.admin')
@section('title', 'Walk-in Booking')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.rooms.index') }}" class="text-sm text-green-700 hover:text-green-900">← Back to Rooms</a>
        <h1 class="text-2xl font-display text-gray-900 mt-1">Walk-in Booking</h1>
        <p class="text-sm text-gray-500 mt-1">A resort access ticket (KES 1,500/person) will be auto-generated for each guest.</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.rooms.walk-in.store') }}"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf

        {{-- Guest info --}}
        <div>
            <div class="text-xs font-bold text-amber-600 uppercase tracking-widest mb-3">Guest Details</div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Phone *</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           placeholder="+254 7XX XXX XXX"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">ID / Passport No.</label>
                    <input type="text" name="id_number" value="{{ old('id_number') }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
            </div>
        </div>

        {{-- Room & Dates --}}
        <div>
            <div class="text-xs font-bold text-amber-600 uppercase tracking-widest mb-3">Room & Stay</div>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Room *</label>
                    <select name="room_id" required id="roomSelect"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500"
                            onchange="updateRoomPrice()">
                        <option value="">Select available room…</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}"
                                    data-price="{{ $room->roomType->base_price }}"
                                    data-type="{{ $room->roomType->name }}"
                                    {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                Room {{ $room->room_number }} — {{ $room->roomType->name }}
                                (KES {{ number_format($room->roomType->base_price) }}/night)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Check-in *</label>
                    <input type="date" name="check_in" value="{{ old('check_in', today()->toDateString()) }}" required
                           id="checkIn" onchange="calcTotal()"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Check-out *</label>
                    <input type="date" name="check_out" value="{{ old('check_out', today()->addDay()->toDateString()) }}" required
                           id="checkOut" onchange="calcTotal()"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Adults *</label>
                    <input type="number" name="adults" value="{{ old('adults', 1) }}" required min="1" max="6"
                           id="adults" onchange="calcTotal()"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Children</label>
                    <input type="number" name="children" value="{{ old('children', 0) }}" min="0"
                           id="children" onchange="calcTotal()"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Special Requests</label>
                <textarea name="special_requests" rows="2"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">{{ old('special_requests') }}</textarea>
            </div>
        </div>

        {{-- Cost summary --}}
        <div class="bg-gray-50 rounded-xl p-4" id="costSummary">
            <div class="text-xs font-bold text-amber-600 uppercase tracking-widest mb-3">Cost Summary</div>
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between text-gray-600"><span id="roomLine">Room rate</span><span id="subtotalVal">—</span></div>
                <div class="flex justify-between text-gray-600"><span>VAT (16%)</span><span id="taxVal">—</span></div>
                <div class="flex justify-between text-gray-600">
                    <span>Resort tickets (<span id="ticketCount">0</span> × KES 1,500)</span>
                    <span id="ticketTotal">KES 0</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-200 text-base">
                    <span>Total</span><span id="grandTotal">—</span>
                </div>
            </div>
        </div>

        {{-- Payment --}}
        <div>
            <div class="text-xs font-bold text-amber-600 uppercase tracking-widest mb-3">Payment</div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Method *</label>
                    <select name="payment_method" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mpesa" {{ old('payment_method') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                        <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Amount Paid (KES) *</label>
                    <input type="number" name="amount_paid" value="{{ old('amount_paid') }}" required min="0" step="100"
                           id="amountPaid"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('admin.rooms.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
            <button type="submit" class="bg-green-900 text-white px-8 py-2.5 rounded-lg text-sm font-semibold hover:bg-green-800">
                Create Booking & Generate Tickets
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let roomPrice = 0;

function updateRoomPrice() {
    const sel = document.getElementById('roomSelect');
    const opt = sel.options[sel.selectedIndex];
    roomPrice = parseFloat(opt.dataset.price || 0);
    calcTotal();
}

function calcTotal() {
    if (!roomPrice) return;
    const checkIn  = new Date(document.getElementById('checkIn').value);
    const checkOut = new Date(document.getElementById('checkOut').value);
    const nights   = Math.max(0, (checkOut - checkIn) / 86400000);
    const adults   = parseInt(document.getElementById('adults').value) || 0;
    const children = parseInt(document.getElementById('children').value) || 0;
    const guests   = adults + children;

    const subtotal    = roomPrice * nights;
    const tax         = subtotal * 0.16;
    const ticketCost  = guests * 1500;
    const grand       = subtotal + tax;

    const sel  = document.getElementById('roomSelect');
    const name = sel.options[sel.selectedIndex]?.dataset.type || 'Room';

    document.getElementById('roomLine').textContent    = `${name} × ${nights} night${nights !== 1 ? 's' : ''}`;
    document.getElementById('subtotalVal').textContent = 'KES ' + subtotal.toLocaleString();
    document.getElementById('taxVal').textContent      = 'KES ' + Math.round(tax).toLocaleString();
    document.getElementById('ticketCount').textContent = guests;
    document.getElementById('ticketTotal').textContent = 'KES ' + ticketCost.toLocaleString();
    document.getElementById('grandTotal').textContent  = 'KES ' + Math.round(grand).toLocaleString();
    document.getElementById('amountPaid').value        = Math.round(grand);
}
</script>
@endpush