@extends('layouts.admin')
@section('title', 'New Booking')
@section('page-title', 'New Booking')
@section('breadcrumb', 'Bookings / New')

@section('content')
<div class="max-w-4xl space-y-5">

    <form method="POST" action="{{ route('admin.bookings.store') }}" id="bookingForm">
        @csrf

        {{-- Step 1: Dates & Availability --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">1. Select Dates</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Check-in *</label>
                    <input type="date" name="check_in" id="checkIn" required min="{{ today()->toDateString() }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Check-out *</label>
                    <input type="date" name="check_out" id="checkOut" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Adults *</label>
                    <select name="adults" id="adults" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                        @foreach(range(1,6) as $n)<option value="{{ $n }}" @selected($n==2)>{{ $n }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Children</label>
                    <select name="children" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                        @foreach(range(0,4) as $n)<option value="{{ $n }}">{{ $n }}</option>@endforeach
                    </select>
                </div>
            </div>
            <button type="button" id="checkAvailBtn"
                    class="mt-4 bg-[#1e3a2f] text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-[#2e5c42] transition-colors">
                Check Availability
            </button>
        </div>

        {{-- Step 2: Room Selection (populated via AJAX) --}}
        <div id="roomsSection" class="hidden bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">2. Select Room</h3>
            <div id="roomsList" class="grid grid-cols-1 md:grid-cols-2 gap-3"></div>
            <input type="hidden" name="room_id" id="roomIdInput">
        </div>

        {{-- Step 3: Guest Details --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">3. Guest Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">First Name *</label>
                    <input type="text" name="guest[first_name]" value="{{ old('guest.first_name') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Last Name *</label>
                    <input type="text" name="guest[last_name]" value="{{ old('guest.last_name') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Email *</label>
                    <input type="email" name="guest[email]" value="{{ old('guest.email') }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Phone *</label>
                    <input type="tel" name="guest[phone]" value="{{ old('guest.phone') }}" required
                           placeholder="+254 7XX XXX XXX"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Special Requests</label>
                    <textarea name="special_requests" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060] resize-none">{{ old('special_requests') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Price summary (shown after room selected) --}}
        <div id="priceSummary" class="hidden bg-[#f0e9d8] rounded-xl border border-amber-100 p-5">
            <div class="text-xs text-[#c8974a] font-semibold uppercase tracking-wide mb-3">Price Summary</div>
            <div id="summaryDetails" class="space-y-1.5 text-sm"></div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        @endif

        <div class="flex gap-3">
            <a href="{{ route('admin.bookings.index') }}" class="px-5 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" id="submitBtn" disabled
                    class="flex-1 md:flex-none md:px-8 py-2.5 bg-[#1e3a2f] text-white rounded-lg text-sm font-semibold
                           hover:bg-[#2e5c42] disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                Create Booking
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('checkAvailBtn').addEventListener('click', async () => {
    const checkIn  = document.getElementById('checkIn').value;
    const checkOut = document.getElementById('checkOut').value;
    const adults   = document.getElementById('adults').value;
    if (!checkIn || !checkOut) return alert('Please select check-in and check-out dates.');

    const btn = document.getElementById('checkAvailBtn');
    btn.textContent = 'Checking…'; btn.disabled = true;

    const res  = await fetch(`{{ route('admin.bookings.check-availability') }}?check_in=${checkIn}&check_out=${checkOut}&adults=${adults}`);
    const data = await res.json();
    btn.textContent = 'Check Availability'; btn.disabled = false;

    const list = document.getElementById('roomsList');
    list.innerHTML = '';

    if (!data.length) {
        list.innerHTML = '<p class="col-span-2 text-sm text-gray-400 py-4 text-center">No rooms available for those dates.</p>';
    } else {
        data.forEach(room => {
            const card = document.createElement('label');
            card.className = 'flex items-start gap-3 p-4 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-[#4a8060] transition-colors has-[:checked]:border-[#1e3a2f] has-[:checked]:bg-green-50';
            card.innerHTML = `
                <input type="radio" name="_room_select" value="${room.id}" class="mt-0.5 accent-[#1e3a2f]"
                       onchange="selectRoom(${room.id}, ${room.roomType.base_price}, '${room.roomType.name}')">
                <div class="flex-1">
                    <div class="font-semibold text-gray-800 text-sm">${room.roomType.name}</div>
                    <div class="text-xs text-gray-400">Room ${room.room_number} · Floor ${room.floor ?? '—'}</div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-[#1e3a2f] text-sm">KES ${Number(room.roomType.base_price).toLocaleString()}<span class="text-xs font-normal text-gray-400">/night</span></div>
                </div>`;
            list.appendChild(card);
        });
    }

    document.getElementById('roomsSection').classList.remove('hidden');
});

function selectRoom(id, price, name) {
    document.getElementById('roomIdInput').value = id;
    document.getElementById('submitBtn').disabled = false;

    const checkIn  = document.getElementById('checkIn').value;
    const checkOut = document.getElementById('checkOut').value;
    const nights   = Math.ceil((new Date(checkOut) - new Date(checkIn)) / 86400000);
    const subtotal = price * nights;
    const vat      = Math.round(subtotal * 0.16);
    const total    = subtotal + vat;

    document.getElementById('summaryDetails').innerHTML = `
        <div class="flex justify-between"><span class="text-gray-600">${name} × ${nights} nights</span><span class="font-medium">KES ${subtotal.toLocaleString()}</span></div>
        <div class="flex justify-between"><span class="text-gray-600">VAT (16%)</span><span class="font-medium">KES ${vat.toLocaleString()}</span></div>
        <div class="flex justify-between font-bold text-[#1e3a2f] border-t border-amber-200 pt-2 mt-2 text-base"><span>Total</span><span>KES ${total.toLocaleString()}</span></div>`;
    document.getElementById('priceSummary').classList.remove('hidden');
}

// Sync check-out min date
document.getElementById('checkIn').addEventListener('change', function() {
    const next = new Date(this.value); next.setDate(next.getDate() + 1);
    document.getElementById('checkOut').min = next.toISOString().split('T')[0];
});
</script>
@endpush
