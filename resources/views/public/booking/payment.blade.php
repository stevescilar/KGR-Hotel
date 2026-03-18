@extends('layouts.app')
@section('title', 'Complete Payment')
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
    .payment-section { background:var(--cream); padding:4rem 0 5rem; }
    .payment-layout { display:grid; grid-template-columns:1fr 380px; gap:2.5rem; align-items:start; max-width:900px; margin:0 auto; }
    .summary-card { background:white; border-radius:16px; padding:1.75rem; box-shadow:0 2px 16px rgba(0,0,0,0.06); }
    .summary-card h2 { font-family:'Playfair Display',serif; font-size:1.25rem; color:var(--forest); margin-bottom:1.25rem; }
    .summary-row { display:flex; justify-content:space-between; padding:0.65rem 0; border-bottom:1px solid #f3f4f6; font-size:0.875rem; }
    .summary-row:last-of-type { border:none; }
    .summary-row .lbl { color:#6b7280; } .summary-row .val { font-weight:600; color:var(--ink); }
    .summary-total { display:flex; justify-content:space-between; border-top:2px solid var(--mist); padding-top:0.85rem; margin-top:0.5rem; }
    .summary-total .lbl { font-weight:700; font-size:1rem; color:var(--forest); }
    .summary-total .val { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--forest); }
    .ref-badge { background:var(--cream); border-radius:8px; padding:0.65rem 1rem; font-family:'DM Mono',monospace; font-size:0.85rem; color:var(--forest); margin-top:1rem; text-align:center; letter-spacing:0.05em; }
    .payment-card { background:white; border-radius:16px; padding:2rem; box-shadow:0 4px 30px rgba(0,0,0,0.08); position:sticky; top:88px; }
    .payment-card h2 { font-family:'Playfair Display',serif; font-size:1.25rem; color:var(--forest); margin-bottom:0.4rem; }
    .payment-card .subtitle { font-size:0.85rem; color:#6b7280; margin-bottom:1.5rem; }
    .mpesa-badge { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:1rem; text-align:center; margin-bottom:1.25rem; }
    .mpesa-badge span { font-size:0.8rem; font-weight:700; color:#166534; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.75rem 1rem; font-size:1rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus { border-color:var(--fern); }
    .btn-pay { width:100%; background:#00a651; color:white; border:none; cursor:pointer; padding:1rem; border-radius:10px; font-size:1rem; font-weight:700; font-family:'Jost',sans-serif; transition:background 0.2s; }
    .btn-pay:hover { background:#008a44; }
    .btn-pay:disabled { opacity:0.6; cursor:not-allowed; }
    .status-msg { padding:0.9rem 1rem; border-radius:8px; margin-top:1rem; font-size:0.85rem; display:none; }
    .status-msg.pending { background:#fef9c3; border:1px solid #fde68a; color:#92400e; }
    .status-msg.success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .status-msg.error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
    @media(max-width:768px) { .payment-layout { grid-template-columns:1fr; } .payment-card { position:static; } }
</style>
@endpush

@section('content')
<div class="booking-hero"><h1>Complete Your Payment</h1></div>
<div class="steps">
    <div class="step done"><span class="step-num">✓</span> Search</div><div class="step-sep"></div>
    <div class="step done"><span class="step-num">✓</span> Details</div><div class="step-sep"></div>
    <div class="step active"><span class="step-num">3</span> Pay</div><div class="step-sep"></div>
    <div class="step"><span class="step-num">4</span> Confirm</div>
</div>

<section class="payment-section">
    <div class="container">
        <div class="payment-layout">
            <div class="summary-card">
                <h2>Booking Summary</h2>
                <div class="summary-row"><span class="lbl">Guest</span><span class="val">{{ $booking->guest->first_name }} {{ $booking->guest->last_name }}</span></div>
                <div class="summary-row"><span class="lbl">Room</span><span class="val">{{ $booking->room->roomType->name }} · Room {{ $booking->room->room_number }}</span></div>
                <div class="summary-row"><span class="lbl">Check-in</span><span class="val">{{ \Carbon\Carbon::parse($booking->check_in)->format('D, M j Y') }}</span></div>
                <div class="summary-row"><span class="lbl">Check-out</span><span class="val">{{ \Carbon\Carbon::parse($booking->check_out)->format('D, M j Y') }}</span></div>
                <div class="summary-row"><span class="lbl">Nights</span><span class="val">{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }}</span></div>
                <div class="summary-row"><span class="lbl">Guests</span><span class="val">{{ $booking->adults }} adult{{ $booking->adults !== 1 ? 's' : '' }}{{ $booking->children ? ", {$booking->children} child".($booking->children!==1?'ren':'') : '' }}</span></div>
                <div class="summary-row"><span class="lbl">Subtotal</span><span class="val">KES {{ number_format($booking->subtotal ?? $booking->total_amount) }}</span></div>
                <div class="summary-row"><span class="lbl">VAT (16%)</span><span class="val">KES {{ number_format($booking->tax_amount ?? 0) }}</span></div>
                <div class="summary-total"><span class="lbl">Grand Total</span><span class="val">KES {{ number_format($booking->total_amount) }}</span></div>
                @if(($booking->payment_option ?? 'full') === 'deposit')
                <div style="display:flex;justify-content:space-between;background:#fef9c3;border-radius:8px;padding:0.75rem 1rem;margin-top:0.75rem;">
                    <span style="font-size:0.82rem;color:#92400e;">50% deposit due now</span>
                    <span style="font-weight:700;color:#92400e;font-family:'Playfair Display',serif;font-size:1.1rem;">KES {{ number_format($booking->deposit_amount) }}</span>
                </div>
                <div style="font-size:0.75rem;color:#9ca3af;margin-top:0.4rem;text-align:right;">
                    Balance KES {{ number_format($booking->total_amount - $booking->deposit_amount) }} payable on arrival
                </div>
                @endif
                <div class="ref-badge">Ref: {{ $booking->booking_ref }}</div>
            </div>

            <div class="payment-card">
                <h2>Pay via M-Pesa</h2>
                <p class="subtitle">Enter your Safaricom number to receive an STK push prompt.</p>
                <div class="mpesa-badge"><span>🟢 Lipa Na M-Pesa · Secure Payment</span></div>
                <div class="form-group">
                    <label>M-Pesa Phone Number</label>
                    <input type="tel" id="mpesaPhone" placeholder="0712 345 678" value="{{ $booking->guest->phone }}">
                </div>
                <button class="btn-pay" id="payBtn" onclick="initiatePay()">
                    @if(($booking->payment_option ?? 'full') === 'deposit')
                        Pay Deposit · KES {{ number_format($booking->deposit_amount) }}
                    @else
                        Pay KES {{ number_format($booking->total_amount) }}
                    @endif
                </button>
                <div class="status-msg pending" id="statusPending">⏳ STK push sent to your phone. Enter your M-Pesa PIN to complete payment…</div>
                <div class="status-msg success" id="statusSuccess">✅ Payment confirmed! Redirecting to your booking confirmation…</div>
                <div class="status-msg error" id="statusError"></div>
                <p style="text-align:center;font-size:0.75rem;color:#9ca3af;margin-top:1rem;">🔒 Secure payment powered by Safaricom M-Pesa Daraja API</p>

                @if(!app()->isProduction())
                <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px dashed #e5e7eb;">
                    <p style="font-size:0.72rem;color:#9ca3af;text-align:center;margin-bottom:0.75rem;">⚙️ DEV MODE — Skip M-Pesa</p>
                    <a href="{{ route('booking.pay.test', $booking) }}"
                       style="display:block;text-align:center;background:#f3f4f6;color:#6b7280;padding:0.75rem;border-radius:8px;font-size:0.8rem;font-weight:600;text-decoration:none;">
                        Simulate Successful Payment →
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
@php $amountDue = ($booking->payment_option ?? 'full') === 'deposit' ? $booking->deposit_amount : $booking->total_amount; @endphp
<script>
const PAY_LABEL = '{{ ($booking->payment_option ?? "full") === "deposit" ? "Pay Deposit · KES ".number_format($amountDue) : "Pay KES ".number_format($amountDue) }}';
async function initiatePay() {
    const phone = document.getElementById('mpesaPhone').value.trim();
    const btn   = document.getElementById('payBtn');
    if (!phone) { alert('Please enter your M-Pesa phone number.'); return; }
    btn.disabled = true; btn.textContent = 'Sending STK push…';
    showStatus('pending');
    const res  = await fetch('{{ route("booking.pay.mpesa", $booking) }}', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({ phone }),
    });
    const data = await res.json();
    if (data.success) { btn.textContent = 'Waiting for payment…'; pollPayment(); }
    else { showStatus('error', data.message||'Payment failed. Please try again.'); btn.disabled=false; btn.textContent=PAY_LABEL; }
}
async function pollPayment() {
    for (let i=0;i<24;i++) {
        await new Promise(r=>setTimeout(r,5000));
        const res=await fetch('{{ route("booking.pay.mpesa.poll",$booking) }}');
        const data=await res.json();
        if(data.status==='completed'){ showStatus('success'); setTimeout(()=>window.location.href='{{ route("booking.confirmation",$booking) }}',1500); return; }
        if(data.status==='failed'){ showStatus('error','Payment failed. Please try again.'); const btn=document.getElementById('payBtn'); btn.disabled=false; btn.textContent=PAY_LABEL; return; }
    }
    showStatus('error','Payment timed out. If you completed the payment, contact us with reference: {{ $booking->booking_ref }}');
}
function showStatus(type,msg){
    ['Pending','Success','Error'].forEach(t=>document.getElementById('status'+t).style.display='none');
    const el=document.getElementById('status'+type.charAt(0).toUpperCase()+type.slice(1));
    if(msg)el.textContent=msg; el.style.display='block';
}
</script>
@endpush