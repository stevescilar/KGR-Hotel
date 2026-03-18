@extends('layouts.app')
@section('title', 'Booking Confirmed!')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .confirm-section { background:var(--cream); padding:5rem 0; margin-top:72px; min-height:calc(100vh - 72px); }
    .confirm-card { background:white; border-radius:20px; box-shadow:0 4px 40px rgba(0,0,0,0.08); max-width:660px; margin:0 auto; overflow:hidden; }
    .confirm-header { background:linear-gradient(135deg,var(--forest),var(--moss)); padding:3rem 2rem; text-align:center; color:white; }
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

    /* Payment status box */
    .payment-box { border-radius:12px; padding:1.25rem 1.5rem; margin-bottom:1.25rem; }
    .payment-box.paid-full { background:#f0fdf4; border:1px solid #bbf7d0; }
    .payment-box.paid-deposit { background:#fef9c3; border:1px solid #fde68a; }
    .payment-box .box-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem; }
    .payment-box .box-title { font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; }
    .payment-box.paid-full .box-title { color:#166534; }
    .payment-box.paid-deposit .box-title { color:#92400e; }
    .payment-box .status-badge { font-size:0.72rem; font-weight:700; padding:0.25rem 0.7rem; border-radius:20px; }
    .payment-box.paid-full .status-badge { background:#dcfce7; color:#166534; }
    .payment-box.paid-deposit .status-badge { background:#fde68a; color:#92400e; }
    .payment-box .amounts { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .payment-box .amt-item .amt-label { font-size:0.72rem; color:#6b7280; margin-bottom:0.2rem; }
    .payment-box .amt-item .amt-value { font-family:'Playfair Display',serif; font-size:1.25rem; }
    .payment-box.paid-full .amt-value { color:#16a34a; }
    .payment-box.paid-deposit .paid-val { color:#16a34a; }
    .payment-box.paid-deposit .balance-val { color:#d97706; font-weight:700; }
    .payment-box .method { font-size:0.75rem; color:#9ca3af; margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid rgba(0,0,0,0.06); }

    /* Account creation prompt */
    .account-prompt { background:linear-gradient(135deg,var(--forest),var(--moss)); border-radius:14px; padding:1.5rem; margin-bottom:1.25rem; color:white; display:flex; align-items:center; gap:1.25rem; }
    .account-prompt .icon { font-size:2.5rem; flex-shrink:0; }
    .account-prompt h3 { font-family:'Playfair Display',serif; font-size:1.1rem; font-weight:400; margin-bottom:0.3rem; }
    .account-prompt p { font-size:0.8rem; color:rgba(255,255,255,0.75); margin-bottom:0.75rem; }
    .btn-create-account { display:inline-block; background:var(--gold); color:white; padding:0.6rem 1.25rem; border-radius:8px; font-size:0.78rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; text-decoration:none; transition:background 0.2s; }
    .btn-create-account:hover { background:#b5863a; }

    .checkin-notice { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:1.25rem; margin-bottom:1.5rem; font-size:0.85rem; color:#166534; }
    .checkin-notice strong { display:block; margin-bottom:0.4rem; font-size:0.9rem; }
    .actions { display:flex; gap:0.75rem; }
    .btn-print { flex:1; text-align:center; border:2px solid var(--mist); color:var(--fern); padding:0.85rem; border-radius:10px; font-size:0.85rem; font-weight:600; cursor:pointer; background:none; font-family:'Jost',sans-serif; }
    .btn-print:hover { border-color:var(--fern); }
    .btn-home { flex:1; text-align:center; background:var(--forest); color:white; padding:0.85rem; border-radius:10px; font-size:0.85rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; text-decoration:none; transition:background 0.2s; }
    .btn-home:hover { background:var(--moss); }

    @media print {
        @page { size:landscape; margin:1.5cm; }
        a[href]::after { content:none !important; }
        body > *:not(.confirm-section) { display:none !important; }
        .confirm-section { margin:0 !important; padding:0 !important; background:white !important; }
        .confirm-card { box-shadow:none !important; max-width:100% !important; border:1px solid #ddd; display:grid; grid-template-columns:260px 1fr; }
        .confirm-header { background:#1e3a2f !important; -webkit-print-color-adjust:exact; }
        .actions,.account-prompt { display:none !important; }
        .details-grid { grid-template-columns:repeat(3,1fr); }
    }
    @media(max-width:540px) { .confirm-body { padding:1.5rem; } .details-grid { grid-template-columns:1fr; } .detail-item:nth-child(even) { padding-left:0; border-left:none; } .actions { flex-direction:column; } .payment-box .amounts { grid-template-columns:1fr; } }
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

                @php
                    $isDeposit   = ($booking->payment_option ?? 'full') === 'deposit';
                    $paidAmount  = $booking->paid_amount ?? 0;
                    $totalAmount = $booking->total_amount;
                    $balance     = max(0, $totalAmount - $paidAmount);
                    $payMethod   = $booking->payments->first()?->method ?? 'mpesa';
                @endphp

                <div class="details-grid">
                    <div class="detail-item"><div class="detail-label">Guest Name</div><div class="detail-value">{{ $booking->guest->first_name }} {{ $booking->guest->last_name }}</div></div>
                    <div class="detail-item"><div class="detail-label">Room Type</div><div class="detail-value">{{ $booking->room->roomType->name }}</div></div>
                    <div class="detail-item"><div class="detail-label">Room Number</div><div class="detail-value">{{ $booking->room->room_number }}</div></div>
                    <div class="detail-item"><div class="detail-label">Guests</div><div class="detail-value">{{ $booking->adults }} adult{{ $booking->adults !== 1 ? 's' : '' }}{{ $booking->children ? ", {$booking->children} child".($booking->children!==1?'ren':'') : '' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Check-in</div><div class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in)->format('D, M j Y') }}</div></div>
                    <div class="detail-item"><div class="detail-label">Check-out</div><div class="detail-value">{{ \Carbon\Carbon::parse($booking->check_out)->format('D, M j Y') }}</div></div>
                    <div class="detail-item"><div class="detail-label">Duration</div><div class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} nights</div></div>
                    @if(($booking->meal_plan ?? 'room_only') !== 'room_only')
                    <div class="detail-item"><div class="detail-label">Meal Plan</div><div class="detail-value">{{ $booking->meal_plan_label }}</div></div>
                    @else
                    <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value" style="color:#16a34a;">✓ Confirmed</div></div>
                    @endif
                </div>

                {{-- Payment status --}}
                @if($isDeposit && $balance > 0)
                {{-- Deposit paid, balance outstanding --}}
                <div class="payment-box paid-deposit">
                    <div class="box-header">
                        <span class="box-title">💛 Deposit Paid · Balance Due on Arrival</span>
                        <span class="status-badge">Partial Payment</span>
                    </div>
                    <div class="amounts">
                        <div class="amt-item">
                            <div class="amt-label">Deposit Paid Now</div>
                            <div class="amt-value paid-val">KES {{ number_format($paidAmount) }}</div>
                        </div>
                        <div class="amt-item">
                            <div class="amt-label">Balance Due on Arrival</div>
                            <div class="amt-value balance-val">KES {{ number_format($balance) }}</div>
                        </div>
                    </div>
                    <div class="amounts" style="margin-top:0.75rem;">
                        <div class="amt-item">
                            <div class="amt-label">Grand Total</div>
                            <div style="font-weight:600;color:#374151;">KES {{ number_format($totalAmount) }}</div>
                        </div>
                    </div>
                    <div class="method">✓ Paid via {{ ucfirst($payMethod) }} · Please bring KES {{ number_format($balance) }} on arrival</div>
                </div>
                @else
                {{-- Fully paid --}}
                <div class="payment-box paid-full">
                    <div class="box-header">
                        <span class="box-title">✅ Payment Complete</span>
                        <span class="status-badge">Fully Paid</span>
                    </div>
                    <div class="amounts">
                        <div class="amt-item">
                            <div class="amt-label">Amount Paid</div>
                            <div class="amt-value">KES {{ number_format($paidAmount) }}</div>
                        </div>
                        <div class="amt-item">
                            <div class="amt-label">Balance Due</div>
                            <div class="amt-value" style="color:#16a34a;">KES 0</div>
                        </div>
                    </div>
                    <div class="method">✓ Paid via {{ ucfirst($payMethod) }}</div>
                </div>
                @endif

                {{-- Account creation prompt --}}
                @if(isset($accountToken) && $accountToken)
                <div class="account-prompt">
                    <div class="icon">🌿</div>
                    <div>
                        <h3>Create your KGR account</h3>
                        <p>Earn loyalty points, view booking history, and get exclusive member offers.</p>
                        <a href="{{ route('account.create', $accountToken) }}" class="btn-create-account">
                            Set Up My Account →
                        </a>
                    </div>
                </div>
                @elseif(!$booking->guest->user_id)
                <div style="background:var(--cream);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.25rem;font-size:0.82rem;color:#6b7280;text-align:center;">
                    Account setup link sent to <strong>{{ $booking->guest->phone }}</strong> via SMS
                </div>
                @endif

                <div class="checkin-notice">
                    <strong>📋 Check-in Information</strong>
                    Check-in from <strong>2:00 PM</strong>. Bring this confirmation + valid ID.
                    @if($isDeposit && $balance > 0)
                    <br><br>💰 <strong>Balance of KES {{ number_format($balance) }} payable at reception on arrival.</strong>
                    @endif
                    <br><br>📞 <strong>+254 113 262 688</strong> · Thika–Garissa Road, Ukasi, Kitui County
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