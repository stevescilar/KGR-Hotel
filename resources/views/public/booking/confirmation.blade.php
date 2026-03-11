@extends('layouts.app')

@section('title', 'Booking Confirmed!')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    @media print {
        @page { size: landscape; margin: 1.5cm; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        a[href]::after { content: none !important; }
        body > *:not(.confirm-section) { display: none !important; }
        .confirm-section { margin: 0 !important; padding: 0 !important; background: white !important; }
        .confirm-card { box-shadow: none !important; max-width: 100% !important; border: 1px solid #ddd; display: grid; grid-template-columns: 280px 1fr; }
        .confirm-header { background: #1e3a2f !important; -webkit-print-color-adjust: exact; }
        .actions { display: none !important; }
        .navbar, footer { display: none !important; }
        .details-grid { grid-template-columns: repeat(3, 1fr); }
        .checkin-notice { font-size: 0.75rem; }
    }

    .confirm-section { background:var(--cream); padding:5rem 0; margin-top:72px; min-height:calc(100vh - 72px); }
    .confirm-card { background:white; border-radius:20px; box-shadow:0 4px 40px rgba(0,0,0,0.08); max-width:640px; margin:0 auto; overflow:hidden; }

    .confirm-header { background:linear-gradient(135deg, var(--forest), var(--moss)); padding:3rem 2rem; text-align:center; color:white; }
    .confirm-header .check { font-size:3.5rem; margin-bottom:1rem; }
    .confirm-header h1 { font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:400; margin-bottom:0.5rem; }
    .confirm-header p { color:rgba(255,255,255,0.75); }

    .confirm-body { padding:2rem 2.5rem; }

    .ref-box { background:var(--cream); border-radius:10px; padding:1.25rem; text-align:center; margin-bottom:2rem; }
    .ref-box .label { font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.15em; color:var(--gold); margin-bottom:0.3rem; }
    .ref-box .ref { font-family:'DM Mono',monospace; font-size:1.5rem; font-weight:700; color:var(--forest); letter-spacing:0.1em; }

    .details-grid { display:grid; grid-template-columns:1fr 1fr; margin-bottom:1.5rem; }
    .detail-item { padding:0.85rem 0; border-bottom:1px solid #f3f4f6; }
    .detail-item:nth-child(odd) { padding-right:1rem; }
    .detail-item:nth-child(even) { padding-left:1rem; border-left:1px solid #f3f4f6; }
    .detail-item:nth-last-child(-n+2) { border-bottom:none; }
    .detail-label { font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; color:#9ca3af; margin-bottom:0.25rem; }
    .detail-value { font-size:0.9rem; font-weight:600; color:var(--ink); }

    .checkin-notice { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:1.25rem; margin-bottom:1.5rem; font-size:0.85rem; color:#166534; }
    .checkin-notice strong { display:block; margin-bottom:0.4rem; font-size:0.9rem; }

    .payment-confirmed { background:var(--cream); border-radius:10px; padding:1rem 1.25rem; margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; }
    .payment-confirmed .lbl { font-size:0.8rem; color:#6b7280; }
    .payment-confirmed .amount { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--forest); }
    .payment-confirmed .status { font-size:0.8rem; font-weight:700; color:#16a34a; }

    .actions { display:flex; gap:0.75rem; }
    .btn-print { flex:1; text-align:center; border:2px solid var(--mist); color:var(--fern); padding:0.85rem; border-radius:10px; font-size:0.85rem; font-weight:600; text-decoration:none; transition:all 0.2s; cursor:pointer; background:none; font-family:'Jost',sans-serif; }
    .btn-print:hover { border-color:var(--fern); }
    .btn-home { flex:1; text-align:center; background:var(--forest); color:white; padding:0.85rem; border-radius:10px; font-size:0.85rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; text-decoration:none; transition:background 0.2s; }
    .btn-home:hover { background:var(--moss); }

    @media(max-width:540px) { .confirm-body { padding:1.5rem; } .details-grid { grid-template-columns:1fr; } .detail-item:nth-child(even) { padding-left:0; border-left:none; } .actions { flex-direction:column; } }
</style>
@endpush

@section('content')
<section class="confirm-section">
    <div class="container">
        <div class="confirm-card">

            <div class="confirm-header">
                <div class="check">🎉</div>
                <h1>Booking Confirmed!</h1>
                <p>We can't wait to welcome you to Kitonga Garden Resort</p>
            </div>

            <div class="confirm-body">

                <div class="ref-box">
                    <div class="label">Booking Reference</div>
                    <div class="ref">{{ $booking->booking_ref }}</div>
                </div>

                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Guest Name</div>
                        <div class="detail-value">{{ $booking->guest->first_name }} {{ $booking->guest->last_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Room Type</div>
                        <div class="detail-value">{{ $booking->room->roomType->name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Room Number</div>
                        <div class="detail-value">{{ $booking->room->room_number }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Guests</div>
                        <div class="detail-value">{{ $booking->adults }} adult{{ $booking->adults !== 1 ? 's' : '' }}{{ $booking->children ? ", {$booking->children} child" . ($booking->children !== 1 ? 'ren' : '') : '' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Check-in</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in)->format('D, M j Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Check-out</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($booking->check_out)->format('D, M j Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Duration</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} nights</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value" style="color:#16a34a;">✓ Confirmed</div>
                    </div>
                </div>

                <div class="payment-confirmed">
                    <div>
                        <div class="lbl">Amount Paid</div>
                        <div class="amount">KES {{ number_format($booking->total_amount) }}</div>
                    </div>
                    <div class="status">✓ Paid via M-Pesa</div>
                </div>

                <div class="checkin-notice">
                    <strong>📋 Check-in Information</strong>
                    Check-in time is from <strong>2:00 PM</strong>. Please bring a copy of this confirmation and a valid ID. Early check-in is subject to availability — call us in advance.
                    <br><br>
                    📞 <strong>+254 113 262 688</strong> · Thika–Garissa Road, Ukasi, Kitui County
                </div>

                <div class="actions">
                    <button class="btn-print" onclick="window.print()">🖨 Print Confirmation</button>
                    <a href="{{ route('home') }}" class="btn-home">Back to Home</a>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection