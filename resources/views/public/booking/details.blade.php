@extends('layouts.app')

@section('title', 'Complete Your Booking')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .booking-hero { margin-top:72px; background:var(--forest); padding:2.5rem 0; text-align:center; color:white; }
    .booking-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(1.5rem,3vw,2rem); font-weight:400; }

    .steps { display:flex; justify-content:center; gap:0.5rem; padding:1.5rem 0; background:var(--cream); border-bottom:1px solid var(--mist); }
    .step { display:flex; align-items:center; gap:0.4rem; font-size:0.78rem; color:#9ca3af; }
    .step.active { color:var(--forest); font-weight:700; }
    .step.done { color:var(--fern); }
    .step-num { width:24px; height:24px; border-radius:50%; border:2px solid currentColor; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; }
    .step-sep { width:40px; height:2px; background:#e5e7eb; }

    .details-section { background:var(--cream); padding:4rem 0 5rem; }
    .details-layout { display:grid; grid-template-columns:1fr 360px; gap:2.5rem; align-items:start; }

    /* Guest form */
    .form-card { background:white; border-radius:16px; padding:2rem; box-shadow:0 2px 16px rgba(0,0,0,0.06); }
    .form-card h2 { font-family:'Playfair Display',serif; font-size:1.25rem; color:var(--forest); margin-bottom:1.5rem; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input, .form-group select, .form-group textarea { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.9rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--fern); }
    .form-section-label { font-size:0.7rem; font-weight:700; color:var(--gold); letter-spacing:0.2em; text-transform:uppercase; border-bottom:1px solid var(--mist); padding-bottom:0.5rem; margin:1.5rem 0 1rem; }
    .checkbox-group { display:flex; align-items:flex-start; gap:0.65rem; }
    .checkbox-group input[type=checkbox] { width:auto; margin-top:0.2rem; flex-shrink:0; }
    .checkbox-group label { font-size:0.85rem; color:#4b5563; font-weight:400; letter-spacing:normal; text-transform:none; }

    .btn-reserve { width:100%; background:var(--forest); color:white; border:none; cursor:pointer; padding:1rem; border-radius:10px; font-size:0.95rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-family:'Jost',sans-serif; margin-top:1rem; transition:background 0.2s; }
    .btn-reserve:hover { background:var(--moss); }

    /* Summary sidebar */
    .summary-card { background:white; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.08); position:sticky; top:88px; }
    .summary-card h3 { font-family:'Playfair Display',serif; font-size:1.15rem; color:var(--forest); margin-bottom:1.25rem; }
    .room-thumb { height:140px; background:var(--warm); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:3rem; margin-bottom:1.25rem; background-size:cover; background-position:center; }
    .room-name { font-family:'Playfair Display',serif; font-size:1.1rem; color:var(--forest); margin-bottom:0.25rem; }
    .room-sub { font-size:0.82rem; color:#6b7280; margin-bottom:1.25rem; }
    .summary-row { display:flex; justify-content:space-between; padding:0.6rem 0; border-bottom:1px solid #f3f4f6; font-size:0.875rem; }
    .summary-row:last-of-type { border:none; }
    .summary-row .lbl { color:#6b7280; }
    .summary-row .val { font-weight:600; color:var(--ink); }
    .summary-total { display:flex; justify-content:space-between; border-top:2px solid var(--mist); padding-top:0.85rem; margin-top:0.5rem; }
    .summary-total .lbl { font-weight:700; color:var(--forest); }
    .summary-total .val { font-family:'Playfair Display',serif; font-size:1.4rem; color:var(--forest); }

    .error-msg { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:0.7rem 1rem; border-radius:6px; font-size:0.8rem; margin-top:0.25rem; }

    @media(max-width:900px) { .details-layout { grid-template-columns:1fr; } .summary-card { position:static; } }
    @media(max-width:540px) { .form-row { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

<div class="booking-hero">
    <h1>Your Booking Details</h1>
</div>

<div class="steps">
    <div class="step done"><span class="step-num">✓</span> Search</div>
    <div class="step-sep"></div>
    <div class="step active"><span class="step-num">2</span> Details</div>
    <div class="step-sep"></div>
    <div class="step"><span class="step-num">3</span> Pay</div>
    <div class="step-sep"></div>
    <div class="step"><span class="step-num">4</span> Confirm</div>
</div>

<section class="details-section">
    <div class="container">
        <div class="details-layout">

            {{-- Guest form --}}
            <div class="form-card">
                <h2>Guest Information</h2>

                <form method="POST" action="{{ route('booking.reserve') }}">
                    @csrf
                    <input type="hidden" name="room_id"   value="{{ $room->id }}">
                    <input type="hidden" name="check_in"  value="{{ $checkIn->toDateString() }}">
                    <input type="hidden" name="check_out" value="{{ $checkOut->toDateString() }}">
                    <input type="hidden" name="adults"    value="{{ $adults }}">
                    <input type="hidden" name="children"  value="{{ $children }}">

                    <div class="form-section-label">Personal Details</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name *</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Last Name *</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required>
                            @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+254 7XX XXX XXX">
                            @error('phone')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nationality</label>
                            <input type="text" name="nationality" value="{{ old('nationality') }}" placeholder="Kenyan">
                        </div>
                        <div class="form-group">
                            <label>ID / Passport No.</label>
                            <input type="text" name="id_number" value="{{ old('id_number') }}">
                        </div>
                    </div>

                    <div class="form-section-label">Stay Preferences</div>
                    <div class="form-group">
                        <label>Special Requests</label>
                        <textarea name="special_requests" rows="3" placeholder="Early check-in, dietary requirements, accessibility needs…">{{ old('special_requests') }}</textarea>
                    </div>

                    <div class="form-section-label">Terms</div>
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" name="agree_terms" id="agreeTerms" value="1" required @checked(old('agree_terms'))>
                            <label for="agreeTerms">I agree to the cancellation policy — free cancellation up to 24 hours before check-in. Late cancellations may be charged one night's fee.</label>
                        </div>
                        @error('agree_terms')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn-reserve">Continue to Payment</button>
                </form>
            </div>

            {{-- Summary sidebar --}}
            <div>
                <div class="summary-card">
                    <h3>Booking Summary</h3>
                    <div class="room-thumb">🛏</div>
                    <div class="room-name">{{ $room->roomType->name }}</div>
                    <div class="room-sub">Room {{ $room->room_number }}@if($room->floor) · Floor {{ $room->floor }}@endif</div>

                    <div class="summary-row"><span class="lbl">Check-in</span><span class="val">{{ $checkIn->format('D, M j Y') }}</span></div>
                    <div class="summary-row"><span class="lbl">Check-out</span><span class="val">{{ $checkOut->format('D, M j Y') }}</span></div>
                    <div class="summary-row"><span class="lbl">Duration</span><span class="val">{{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</span></div>
                    <div class="summary-row"><span class="lbl">Guests</span><span class="val">{{ $adults }} adult{{ $adults !== 1 ? 's' : '' }}{{ $children ? ", {$children} child" . ($children !== 1 ? 'ren' : '') : '' }}</span></div>
                    <div class="summary-row"><span class="lbl">Room rate</span><span class="val">KES {{ number_format($costs['nights'] > 0 ? $costs['subtotal'] / $costs['nights'] : 0) }} / night</span></div>
                    <div class="summary-row"><span class="lbl">Subtotal</span><span class="val">KES {{ number_format($costs['subtotal']) }}</span></div>
                    <div class="summary-row"><span class="lbl">VAT (16%)</span><span class="val">KES {{ number_format($costs['tax']) }}</span></div>
                    <div class="summary-total">
                        <span class="lbl">Total</span>
                        <span class="val">KES {{ number_format($costs['total']) }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection