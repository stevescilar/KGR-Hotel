@extends('layouts.app')

@section('title', $roomType->name)
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .room-hero {
        margin-top: 72px; height: 480px;
        background: var(--warm);
        display: flex; align-items: center; justify-content: center;
        font-size: 6rem; position: relative; overflow: hidden;
        background-size: cover; background-position: center;
    }
    .room-hero-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(10,25,15,0.7) 0%, transparent 60%);
    }
    .room-hero-title {
        position: absolute; bottom: 2.5rem; left: 0; right: 0;
        text-align: center; color: white;
    }
    .room-hero-title h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2rem, 4vw, 3rem); font-weight: 400;
    }
    .room-hero-title p { color: rgba(255,255,255,0.75); margin-top: 0.5rem; }

    .room-detail { background: var(--cream); padding: 5rem 0; }
    .room-grid { display: grid; grid-template-columns: 1fr 380px; gap: 3rem; align-items: start; }

    /* Left column */
    .room-description { background: white; border-radius: 16px; padding: 2.5rem; margin-bottom: 1.5rem; }
    .room-description h2 {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem; color: var(--forest); margin-bottom: 1rem;
    }
    .room-description p { color: #4b5563; line-height: 1.8; }

    .room-meta-grid {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 1rem; margin-bottom: 1.5rem;
    }
    .meta-card {
        background: white; border-radius: 12px; padding: 1.25rem;
        text-align: center;
    }
    .meta-card .icon { font-size: 1.5rem; margin-bottom: 0.4rem; }
    .meta-card .label { font-size: 0.7rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.1em; }
    .meta-card .value { font-size: 1rem; font-weight: 600; color: var(--forest); margin-top: 0.2rem; }

    .amenities-box { background: white; border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; }
    .amenities-box h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.1rem; color: var(--forest); margin-bottom: 1rem;
    }
    .amenities-list { display: flex; flex-wrap: wrap; gap: 0.6rem; }
    .amenity { background: var(--cream); color: var(--fern); border: 1px solid var(--mist); border-radius: 20px; padding: 0.35rem 0.9rem; font-size: 0.8rem; font-weight: 500; }

    /* Right column — booking card */
    .booking-card {
        background: white; border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0,0,0,0.08);
        padding: 2rem; position: sticky; top: 96px;
    }
    .booking-card .price {
        font-family: 'Playfair Display', serif;
        font-size: 2.25rem; color: var(--forest); font-weight: 500;
    }
    .booking-card .price-sub { font-size: 0.8rem; color: #9ca3af; margin-bottom: 1.5rem; }
    .booking-card label {
        display: block; font-size: 0.7rem; font-weight: 700;
        color: var(--fern); letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.4rem;
    }
    .booking-card input, .booking-card select {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 8px;
        padding: 0.65rem 0.9rem; font-size: 0.9rem; font-family: 'Jost', sans-serif;
        margin-bottom: 1rem; outline: none; transition: border-color 0.2s;
    }
    .booking-card input:focus, .booking-card select:focus { border-color: var(--fern); }
    .btn-book {
        width: 100%; background: var(--forest); color: white; border: none; cursor: pointer;
        padding: 1rem; border-radius: 10px; font-size: 0.9rem; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase; font-family: 'Jost', sans-serif;
        transition: background 0.2s; margin-top: 0.5rem;
    }
    .btn-book:hover { background: var(--moss); }
    .price-breakdown { background: var(--cream); border-radius: 8px; padding: 1rem; margin-top: 1rem; font-size: 0.85rem; }
    .price-breakdown .row { display: flex; justify-content: space-between; padding: 0.3rem 0; color: #4b5563; }
    .price-breakdown .total { font-weight: 700; color: var(--forest); border-top: 1px solid var(--mist); margin-top: 0.5rem; padding-top: 0.5rem; }

    /* Related rooms */
    .related-section { padding: 4rem 0; background: white; }
    .related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
    .related-card {
        border-radius: 12px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        text-decoration: none; color: inherit; display: block; transition: transform 0.3s;
    }
    .related-card:hover { transform: translateY(-3px); }
    .related-card-img { height: 160px; background: var(--warm); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; }
    .related-card-body { padding: 1.25rem; }
    .related-card-body h4 { font-family: 'Playfair Display', serif; color: var(--forest); margin-bottom: 0.3rem; }
    .related-card-body p { font-size: 0.8rem; color: #6b7280; }

    @media (max-width: 900px) {
        .room-grid { grid-template-columns: 1fr; }
        .booking-card { position: static; }
        .room-meta-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')

    <div class="room-hero" style="{{ $roomType->images ? 'background-image:url('.($roomType->images[0] ?? '').')' : '' }}">
        @if(!$roomType->images)<span style="position:relative;z-index:1;">🛏</span>@endif
        <div class="room-hero-overlay"></div>
        <div class="room-hero-title">
            <h1>{{ $roomType->name }}</h1>
            <p>Up to {{ $roomType->max_adults }} adults · From KES {{ number_format($roomType->base_price) }} / night</p>
        </div>
    </div>

    <section class="room-detail">
        <div class="container">
            <div class="room-grid">

                {{-- LEFT --}}
                <div>
                    {{-- Meta cards --}}
                    <div class="room-meta-grid">
                        <div class="meta-card">
                            <div class="icon">👥</div>
                            <div class="label">Max Adults</div>
                            <div class="value">{{ $roomType->max_adults }}</div>
                        </div>
                        @if($roomType->max_children > 0)
                        <div class="meta-card">
                            <div class="icon">🧒</div>
                            <div class="label">Max Children</div>
                            <div class="value">{{ $roomType->max_children }}</div>
                        </div>
                        @endif
                        @if($roomType->weekend_price)
                        <div class="meta-card">
                            <div class="icon">📅</div>
                            <div class="label">Weekend Rate</div>
                            <div class="value">KES {{ number_format($roomType->weekend_price) }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div class="room-description">
                        <h2>About This Room</h2>
                        <p>{{ $roomType->description ?? 'A beautifully appointed room combining luxury and comfort. Featuring private balcony with garden views, luxury bathroom with shower, high-speed internet and comfortable furnishings for a memorable stay.' }}</p>
                    </div>

                    {{-- Amenities --}}
                    @if($roomType->amenities)
                    <div class="amenities-box">
                        <h3>Room Amenities</h3>
                        <div class="amenities-list">
                            @foreach($roomType->amenities as $amenity)
                                <span class="amenity">✓ {{ $amenity }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Pricing rules if any --}}
                    @if($roomType->pricingRules->where('is_active', true)->count())
                    <div class="amenities-box">
                        <h3>Special Rates</h3>
                        @foreach($roomType->pricingRules->where('is_active', true) as $rule)
                        <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid var(--cream);font-size:0.9rem;">
                            <span style="color:#4b5563;">{{ $rule->name }} <span style="color:#9ca3af;font-size:0.8rem;">({{ \Carbon\Carbon::parse($rule->start_date)->format('M j') }} – {{ \Carbon\Carbon::parse($rule->end_date)->format('M j, Y') }})</span></span>
                            <strong style="color:var(--forest);">KES {{ number_format($rule->price) }}</strong>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- RIGHT — Booking Card --}}
                <div>
                    <div class="booking-card">
                        <div class="price">KES {{ number_format($roomType->base_price) }}</div>
                        <div class="price-sub">per night · inc. VAT</div>

                        <form action="{{ route('booking.check') }}" method="POST" id="roomBookingForm">
                            @csrf
                            <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">

                            <label>Check-in</label>
                            <input type="date" name="check_in" id="checkIn" required min="{{ today()->toDateString() }}">

                            <label>Check-out</label>
                            <input type="date" name="check_out" id="checkOut" required>

                            <label>Adults</label>
                            <select name="adults">
                                @for($i = 1; $i <= $roomType->max_adults; $i++)
                                    <option value="{{ $i }}" @selected($i == min(2, $roomType->max_adults))>{{ $i }} Adult{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>

                            @if($roomType->max_children > 0)
                            <label>Children</label>
                            <select name="children">
                                @for($i = 0; $i <= $roomType->max_children; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'Child' : 'Children' }}</option>
                                @endfor
                            </select>
                            @endif

                            <div class="price-breakdown" id="priceBreakdown" style="display:none;">
                                <div class="row"><span>Room rate</span><span id="pbRate">—</span></div>
                                <div class="row"><span id="pbNights">—</span></div>
                                <div class="row"><span>Subtotal</span><span id="pbSubtotal">—</span></div>
                                <div class="row"><span>VAT (16%)</span><span id="pbVat">—</span></div>
                                <div class="row total"><span>Total</span><span id="pbTotal">—</span></div>
                            </div>

                            <button type="submit" class="btn-book">Check Availability & Book</button>
                        </form>

                        <p style="text-align:center;font-size:0.75rem;color:#9ca3af;margin-top:1rem;">
                            Free cancellation up to 24 hours before check-in
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Related rooms --}}
    @if($relatedTypes->count())
    <section class="related-section">
        <div class="container">
            <div style="text-align:center;margin-bottom:2.5rem;">
                <span style="font-size:0.7rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:var(--gold);">More Options</span>
                <h2 style="font-family:'Playfair Display',serif;font-size:1.75rem;color:var(--forest);margin-top:0.5rem;">You May Also Like</h2>
            </div>
            <div class="related-grid">
                @foreach($relatedTypes as $rel)
                <a href="{{ route('rooms.show', $rel->slug) }}" class="related-card">
                    <div class="related-card-img">🛏</div>
                    <div class="related-card-body">
                        <h4>{{ $rel->name }}</h4>
                        <p>From KES {{ number_format($rel->base_price) }} / night · Up to {{ $rel->max_adults }} adults</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

@endsection

@push('scripts')
<script>
const basePrice = {{ $roomType->base_price }};
const checkIn  = document.getElementById('checkIn');
const checkOut = document.getElementById('checkOut');

function updatePrice() {
    if (!checkIn.value || !checkOut.value) return;
    const nights = Math.ceil((new Date(checkOut.value) - new Date(checkIn.value)) / 86400000);
    if (nights < 1) return;
    const subtotal = basePrice * nights;
    const vat      = Math.round(subtotal * 0.16);
    const total    = subtotal + vat;

    document.getElementById('pbRate').textContent     = `KES ${basePrice.toLocaleString()}`;
    document.getElementById('pbNights').textContent   = `${nights} night${nights > 1 ? 's' : ''}`;
    document.getElementById('pbSubtotal').textContent = `KES ${subtotal.toLocaleString()}`;
    document.getElementById('pbVat').textContent      = `KES ${vat.toLocaleString()}`;
    document.getElementById('pbTotal').textContent    = `KES ${total.toLocaleString()}`;
    document.getElementById('priceBreakdown').style.display = 'block';
}

checkIn.addEventListener('change', function () {
    const next = new Date(this.value);
    next.setDate(next.getDate() + 1);
    checkOut.min   = next.toISOString().split('T')[0];
    if (!checkOut.value || checkOut.value <= this.value) {
        checkOut.value = next.toISOString().split('T')[0];
    }
    updatePrice();
});
checkOut.addEventListener('change', updatePrice);
</script>
@endpush