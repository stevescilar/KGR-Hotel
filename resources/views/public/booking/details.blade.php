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
    .form-card { background:white; border-radius:16px; padding:2rem; box-shadow:0 2px 16px rgba(0,0,0,0.06); }
    .form-card h2 { font-family:'Playfair Display',serif; font-size:1.25rem; color:var(--forest); margin-bottom:1.5rem; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input, .form-group select, .form-group textarea { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.9rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--fern); }
    .form-section-label { font-size:0.7rem; font-weight:700; color:var(--gold); letter-spacing:0.2em; text-transform:uppercase; border-bottom:1px solid var(--mist); padding-bottom:0.5rem; margin:1.5rem 0 1rem; }
    .meal-grid { display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:0.5rem; }
    .meal-option { border:2px solid #e5e7eb; border-radius:10px; padding:0.85rem 1rem; cursor:pointer; transition:all 0.2s; }
    .meal-option.selected { border-color:var(--forest); background:#f0fdf4; }
    .meal-option:hover { border-color:var(--fern); }
    .meal-name { font-size:0.85rem; font-weight:700; color:var(--forest); margin-bottom:0.2rem; }
    .meal-desc { font-size:0.72rem; color:#6b7280; }
    .meal-price { font-size:0.75rem; font-weight:700; color:var(--gold); margin-top:0.3rem; }
    .payment-options { display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; }
    .pay-option { border:2px solid #e5e7eb; border-radius:10px; padding:1rem; cursor:pointer; transition:all 0.2s; text-align:center; }
    .pay-option.selected { border-color:var(--forest); background:#f0fdf4; }
    .pay-option:hover { border-color:var(--fern); }
    .pay-amount { font-family:'Playfair Display',serif; font-size:1.2rem; color:var(--forest); }
    .pay-label { font-size:0.72rem; color:#6b7280; margin-top:0.2rem; }
    .pay-badge { display:inline-block; background:var(--cream); color:var(--gold); font-size:0.65rem; font-weight:700; padding:0.15rem 0.5rem; border-radius:10px; margin-top:0.3rem; }
    .deposit-note { background:#fef9c3; border:1px solid #fde68a; border-radius:8px; padding:0.75rem 1rem; font-size:0.8rem; color:#92400e; margin-top:0.75rem; display:none; }
    .checkbox-group { display:flex; align-items:flex-start; gap:0.65rem; }
    .checkbox-group input[type=checkbox] { width:auto; margin-top:0.2rem; flex-shrink:0; }
    .checkbox-group label { font-size:0.85rem; color:#4b5563; font-weight:400; letter-spacing:normal; text-transform:none; }
    .btn-reserve { width:100%; background:var(--forest); color:white; border:none; cursor:pointer; padding:1rem; border-radius:10px; font-size:0.95rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-family:'Jost',sans-serif; margin-top:1rem; transition:background 0.2s; }
    .btn-reserve:hover { background:var(--moss); }
    .error-msg { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:0.7rem 1rem; border-radius:6px; font-size:0.8rem; margin-top:0.25rem; }
    .summary-card { background:white; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); position:sticky; top:88px; }
    .room-hero-img { width:100%; height:190px; object-fit:cover; display:block; }
    .room-hero-placeholder { width:100%; height:190px; background:linear-gradient(135deg,var(--forest),var(--moss)); display:flex; align-items:center; justify-content:center; font-size:4rem; }
    .summary-body { padding:1.5rem; }
    .room-name { font-family:'Playfair Display',serif; font-size:1.1rem; color:var(--forest); margin-bottom:0.2rem; }
    .room-sub { font-size:0.8rem; color:#6b7280; margin-bottom:1.25rem; }
    .summary-row { display:flex; justify-content:space-between; padding:0.55rem 0; border-bottom:1px solid #f3f4f6; font-size:0.85rem; }
    .summary-row:last-of-type { border:none; }
    .summary-row .lbl { color:#6b7280; } .summary-row .val { font-weight:600; color:var(--ink); }
    .summary-total { display:flex; justify-content:space-between; border-top:2px solid var(--mist); padding-top:0.85rem; margin-top:0.5rem; }
    .summary-total .lbl { font-weight:700; color:var(--forest); }
    .summary-total .val { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--forest); }
    .summary-due { display:flex; justify-content:space-between; padding:0.5rem 0; margin-top:0.35rem; background:var(--cream); border-radius:8px; padding:0.6rem 0.75rem; }
    .summary-due .lbl { font-size:0.78rem; color:#6b7280; }
    .summary-due .val { font-size:0.9rem; font-weight:700; color:var(--gold); }
    @media(max-width:900px) { .details-layout { grid-template-columns:1fr; } .summary-card { position:static; } }
    @media(max-width:540px) { .form-row,.meal-grid,.payment-options { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="booking-hero"><h1>Your Booking Details</h1></div>
<div class="steps">
    <div class="step done"><span class="step-num">✓</span> Search</div><div class="step-sep"></div>
    <div class="step active"><span class="step-num">2</span> Details</div><div class="step-sep"></div>
    <div class="step"><span class="step-num">3</span> Pay</div><div class="step-sep"></div>
    <div class="step"><span class="step-num">4</span> Confirm</div>
</div>

<section class="details-section">
    <div class="container">
        <div class="details-layout">

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
                        <div class="form-group"><label>First Name *</label><input type="text" name="first_name" value="{{ old('first_name') }}" required>@error('first_name')<div class="error-msg">{{ $message }}</div>@enderror</div>
                        <div class="form-group"><label>Last Name *</label><input type="text" name="last_name" value="{{ old('last_name') }}" required>@error('last_name')<div class="error-msg">{{ $message }}</div>@enderror</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Email Address *</label><input type="email" name="email" value="{{ old('email') }}" required>@error('email')<div class="error-msg">{{ $message }}</div>@enderror</div>
                        <div class="form-group"><label>Phone Number *</label><input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+254 7XX XXX XXX"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Nationality</label><input type="text" name="nationality" value="{{ old('nationality') }}" placeholder="Kenyan"></div>
                        <div class="form-group"><label>ID / Passport No.</label><input type="text" name="id_number" value="{{ old('id_number') }}"></div>
                    </div>

                    <div class="form-section-label">Meal Plan</div>
                    @php
                    $plans = [
                        'room_only'     => ['🛏 Room Only',       'No meals included',            0],
                        'bed_breakfast' => ['☕ Bed & Breakfast',  'Daily breakfast included',     $mealPlanPrices['bed_breakfast']],
                        'half_board'    => ['🍽 Half Board',       'Breakfast & dinner included',  $mealPlanPrices['half_board']],
                        'full_board'    => ['🍴 Full Board',       'All 3 meals daily included',   $mealPlanPrices['full_board']],
                    ];
                    $selectedPlan = old('meal_plan', 'room_only');
                    @endphp
                    <div class="meal-grid">
                        @foreach($plans as $value => [$name, $desc, $rate])
                        <div class="meal-option {{ $selectedPlan === $value ? 'selected' : '' }}"
                             onclick="selectMeal('{{ $value }}', this)">
                            <input type="radio" name="meal_plan" value="{{ $value }}"
                                   {{ $selectedPlan === $value ? 'checked' : '' }} style="display:none;">
                            <div class="meal-name">{{ $name }}</div>
                            <div class="meal-desc">{{ $desc }}</div>
                            <div class="meal-price">{{ $rate > 0 ? '+KES '.number_format($rate).'/person/night' : 'Included' }}</div>
                        </div>
                        @endforeach
                    </div>

                    <div class="form-section-label">Payment Option</div>
                    <div class="payment-options">
                        <div class="pay-option {{ old('payment_option','full') === 'full' ? 'selected' : '' }}"
                             onclick="selectPayOption('full', this)">
                            <input type="radio" name="payment_option" value="full"
                                   {{ old('payment_option','full') === 'full' ? 'checked' : '' }} style="display:none;">
                            <div class="pay-amount" id="fullAmount">KES {{ number_format($costs['total']) }}</div>
                            <div class="pay-label">Pay in full now</div>
                            <div class="pay-badge">✓ Best value</div>
                        </div>
                        <div class="pay-option {{ old('payment_option') === 'deposit' ? 'selected' : '' }}"
                             onclick="selectPayOption('deposit', this)">
                            <input type="radio" name="payment_option" value="deposit"
                                   {{ old('payment_option') === 'deposit' ? 'checked' : '' }} style="display:none;">
                            <div class="pay-amount" id="depositAmount">KES {{ number_format($costs['total'] / 2) }}</div>
                            <div class="pay-label">Pay 50% deposit</div>
                            <div class="pay-badge">Balance on arrival</div>
                        </div>
                    </div>
                    <div class="deposit-note" id="depositNote">
                        ℹ️ Deposit of <strong id="depositNoteAmt">KES {{ number_format($costs['total'] / 2) }}</strong> due now. Balance payable on arrival.
                    </div>

                    <div class="form-section-label">Stay Preferences</div>
                    <div class="form-group"><label>Special Requests</label><textarea name="special_requests" rows="3" placeholder="Early check-in, dietary requirements, accessibility needs…">{{ old('special_requests') }}</textarea></div>

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

            <div>
                <div class="summary-card">
                    @php
                    $fallbackImages = [
                        'standard'            => 'https://static.wixstatic.com/media/87c8f7_93a0afa668464496a6f8636744e86fc4~mv2.jpg/v1/fill/w_800,h_500,al_c,q_85/87c8f7_93a0afa668464496a6f8636744e86fc4~mv2.jpg',
                        'deluxe'              => 'https://static.wixstatic.com/media/87c8f7_a8fdf9b4baa3478d8bcbf501772e7b9d~mv2.jpg/v1/fill/w_800,h_500,al_c,q_85/87c8f7_a8fdf9b4baa3478d8bcbf501772e7b9d~mv2.jpg',
                        'penthouse'           => 'https://static.wixstatic.com/media/87c8f7_ae6b2ce224f54eff8127c7e9fe0e7266~mv2.jpg/v1/fill/w_800,h_500,al_c,q_80/87c8f7_ae6b2ce224f54eff8127c7e9fe0e7266~mv2.jpg',
                        'presidential-family' => 'https://static.wixstatic.com/media/87c8f7_953ad41ed0c1461d89a835b7c51270e2~mv2.jpg/v1/fill/w_800,h_500,al_c,q_85/87c8f7_953ad41ed0c1461d89a835b7c51270e2~mv2.jpg',
                        'royal-presidential'  => 'https://static.wixstatic.com/media/87c8f7_f9c30fa9dbce489b9b7b7920acb5a8a9~mv2.jpg/v1/fill/w_800,h_500,al_c,q_85/87c8f7_f9c30fa9dbce489b9b7b7920acb5a8a9~mv2.jpg',
                    ];
                    $roomType = $room->roomType;
                    $heroImg  = null;
                    if (!empty($roomType->images) && count($roomType->images)) $heroImg = Storage::url($roomType->images[0]);
                    elseif ($room->image ?? false) $heroImg = Storage::url($room->image);
                    elseif (isset($fallbackImages[$roomType->slug])) $heroImg = $fallbackImages[$roomType->slug];
                    @endphp
                    @if($heroImg)<img src="{{ $heroImg }}" alt="{{ $roomType->name }}" class="room-hero-img">
                    @else<div class="room-hero-placeholder">🛏</div>@endif

                    <div class="summary-body">
                        <div class="room-name">{{ $roomType->name }}</div>
                        <div class="room-sub">Room {{ $room->room_number }}@if($room->floor ?? false) · Floor {{ $room->floor }}@endif</div>
                        <div class="summary-row"><span class="lbl">Check-in</span><span class="val">{{ $checkIn->format('D, M j Y') }}</span></div>
                        <div class="summary-row"><span class="lbl">Check-out</span><span class="val">{{ $checkOut->format('D, M j Y') }}</span></div>
                        <div class="summary-row"><span class="lbl">Duration</span><span class="val">{{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</span></div>
                        <div class="summary-row"><span class="lbl">Guests</span><span class="val">{{ $adults }} adult{{ $adults !== 1 ? 's' : '' }}{{ $children ? ", {$children} child".($children!==1?'ren':'') : '' }}</span></div>
                        <div class="summary-row"><span class="lbl">Room rate</span><span class="val">KES {{ number_format($costs['nights'] > 0 ? $costs['subtotal'] / $costs['nights'] : 0) }}/night</span></div>
                        <div class="summary-row" id="mealSummaryRow" style="display:none;"><span class="lbl">Meal plan</span><span class="val" id="mealSummaryVal">—</span></div>
                        <div class="summary-row"><span class="lbl">Subtotal</span><span class="val" id="sSubtotal">KES {{ number_format($costs['subtotal']) }}</span></div>
                        <div class="summary-row"><span class="lbl">VAT (16%)</span><span class="val" id="sTax">KES {{ number_format($costs['tax']) }}</span></div>
                        <div class="summary-total"><span class="lbl">Total</span><span class="val" id="sTotal">KES {{ number_format($costs['total']) }}</span></div>
                        <div class="summary-due" id="sDueRow" style="display:none;"><span class="lbl">Due now (50% deposit)</span><span class="val" id="sDue">—</span></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const BASE_SUBTOTAL = {{ $costs['subtotal'] }};
const NIGHTS = {{ $nights }};
const GUESTS = {{ $adults + $children }};
const MEAL_RATES = @json($mealPlanPrices);
const MEAL_NAMES = {
    room_only:'No extra meals', bed_breakfast:'Bed & Breakfast',
    half_board:'Half Board', full_board:'Full Board'
};
let meal = '{{ old("meal_plan","room_only") }}';
let payOpt = '{{ old("payment_option","full") }}';

function selectMeal(val, el) {
    meal = val;
    document.querySelectorAll('.meal-option').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    recalc();
}
function selectPayOption(val, el) {
    payOpt = val;
    document.querySelectorAll('.pay-option').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    recalc();
}
function recalc() {
    const rate      = MEAL_RATES[meal] || 0;
    const mealExtra = rate * GUESTS * NIGHTS;
    const subtotal  = BASE_SUBTOTAL + mealExtra;
    const tax       = Math.round(subtotal * 0.16);
    const total     = subtotal + tax;
    const deposit   = Math.round(total * 0.5);

    document.getElementById('sSubtotal').textContent = 'KES ' + subtotal.toLocaleString();
    document.getElementById('sTax').textContent      = 'KES ' + tax.toLocaleString();
    document.getElementById('sTotal').textContent    = 'KES ' + total.toLocaleString();
    document.getElementById('fullAmount').textContent    = 'KES ' + total.toLocaleString();
    document.getElementById('depositAmount').textContent = 'KES ' + deposit.toLocaleString();
    document.getElementById('depositNoteAmt').textContent = 'KES ' + deposit.toLocaleString();

    if (rate > 0) {
        document.getElementById('mealSummaryRow').style.display = '';
        document.getElementById('mealSummaryVal').textContent = MEAL_NAMES[meal] + ' (+KES ' + mealExtra.toLocaleString() + ')';
    } else {
        document.getElementById('mealSummaryRow').style.display = 'none';
    }

    if (payOpt === 'deposit') {
        document.getElementById('depositNote').style.display = 'block';
        document.getElementById('sDueRow').style.display = '';
        document.getElementById('sDue').textContent = 'KES ' + deposit.toLocaleString();
    } else {
        document.getElementById('depositNote').style.display = 'none';
        document.getElementById('sDueRow').style.display = 'none';
    }
}
if (payOpt === 'deposit') recalc();
</script>
@endpush