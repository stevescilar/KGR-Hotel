@extends('layouts.app')
@section('title', 'Complete Payment')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .payment-section { background:var(--cream); padding:5rem 0; margin-top:72px; min-height:calc(100vh - 72px); }
    .payment-layout { display:grid; grid-template-columns:1fr 380px; gap:3rem; align-items:start; max-width:900px; margin:0 auto; }
    .summary-card { background:white; border-radius:16px; padding:2rem; box-shadow:0 2px 16px rgba(0,0,0,0.06); }
    .summary-card h2 { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--forest); margin-bottom:1.5rem; }
    .summary-row { display:flex; justify-content:space-between; padding:0.7rem 0; border-bottom:1px solid #f3f4f6; font-size:0.9rem; }
    .summary-row:last-of-type { border:none; }
    .summary-row .label { color:#6b7280; } .summary-row .value { font-weight:600; color:var(--ink); }
    .summary-total { display:flex; justify-content:space-between; padding-top:1rem; margin-top:0.5rem; border-top:2px solid var(--mist); }
    .summary-total .label { font-weight:700; color:var(--forest); font-size:1rem; }
    .summary-total .value { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--forest); }
    .payment-card { background:white; border-radius:16px; padding:2rem; box-shadow:0 4px 30px rgba(0,0,0,0.08); position:sticky; top:96px; }
    .payment-card h2 { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--forest); margin-bottom:0.4rem; }
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
    .status-msg { padding:0.85rem 1rem; border-radius:8px; margin-top:1rem; font-size:0.85rem; display:none; }
    .status-msg.pending { background:#fef9c3; border:1px solid #fde68a; color:#92400e; }
    .status-msg.success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .status-msg.error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
    .steps { display:flex; justify-content:center; gap:0.5rem; padding:1.25rem 0; background:white; border-bottom:1px solid var(--mist); margin-bottom:0; }
    .step { display:flex; align-items:center; gap:0.4rem; font-size:0.78rem; color:#9ca3af; }
    .step.active { color:var(--forest); font-weight:700; }
    .step.done { color:var(--fern); }
    .step-num { width:24px; height:24px; border-radius:50%; border:2px solid currentColor; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; }
    .step-sep { width:32px; height:2px; background:#e5e7eb; }
    @media(max-width:768px) { .payment-layout { grid-template-columns:1fr; } .payment-card { position:static; } }
</style>
@endpush

@section('content')
<div class="steps" style="margin-top:72px;">
    <div class="step done"><span class="step-num">✓</span> Select</div>
    <div class="step-sep"></div>
    <div class="step active"><span class="step-num">2</span> Pay</div>
    <div class="step-sep"></div>
    <div class="step"><span class="step-num">3</span> Confirm</div>
</div>
<section class="payment-section">
    <div class="container">
        <div class="payment-layout">
            <div class="summary-card">
                <h2>Order Summary</h2>
                <div class="summary-row"><span class="label">Ticket Type</span><span class="value">{{ $ticket->ticketType->name }}</span></div>
                <div class="summary-row"><span class="label">Visit Date</span><span class="value">{{ $ticket->visit_date->format('D, M j Y') }}</span></div>
                <div class="summary-row"><span class="label">Quantity</span><span class="value">{{ $ticket->quantity }} person{{ $ticket->quantity > 1 ? 's' : '' }}</span></div>
                <div class="summary-row"><span class="label">Guest Name</span><span class="value">{{ $ticket->guest_name }}</span></div>
                <div class="summary-row"><span class="label">Ticket #</span><span class="value" style="font-family:'DM Mono',monospace;font-size:0.85rem;">{{ $ticket->ticket_number }}</span></div>
                <div class="summary-total"><span class="label">Total</span><span class="value">KES {{ number_format($ticket->total_price) }}</span></div>
            </div>
            <div class="payment-card">
                <h2>Pay via M-Pesa</h2>
                <p class="subtitle">Enter your Safaricom number to receive an STK push prompt on your phone.</p>
                <div class="mpesa-badge"><span>🟢 M-Pesa · Lipa Na M-Pesa</span></div>
                <div class="form-group">
                    <label>M-Pesa Phone Number</label>
                    <input type="tel" id="mpesaPhone" placeholder="0712 345 678" value="{{ $ticket->guest_phone }}">
                </div>
                <button class="btn-pay" id="payBtn" onclick="initiatePay()">Pay KES {{ number_format($ticket->total_price) }}</button>
                <div class="status-msg pending" id="statusPending">⏳ STK push sent. Check your phone and enter your M-Pesa PIN…</div>
                <div class="status-msg success" id="statusSuccess">✓ Payment confirmed! Redirecting to your ticket…</div>
                <div class="status-msg error" id="statusError"></div>
                <p style="font-size:0.75rem;color:#9ca3af;margin-top:1rem;text-align:center;">🔒 Secure payment powered by Safaricom M-Pesa</p>

                @if(!app()->isProduction())
                <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px dashed #e5e7eb;">
                    <p style="font-size:0.72rem;color:#9ca3af;text-align:center;margin-bottom:0.75rem;">⚙️ DEV MODE — Skip M-Pesa</p>
                    <a href="{{ route('tickets.confirmation', $ticket) }}"
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
<script>
async function initiatePay() {
    const phone = document.getElementById('mpesaPhone').value.trim();
    const btn   = document.getElementById('payBtn');
    if (!phone) { alert('Please enter your M-Pesa phone number.'); return; }
    btn.disabled = true; btn.textContent = 'Sending…';
    showStatus('pending');
    const res  = await fetch('{{ route("tickets.pay.mpesa", $ticket) }}', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
        body:JSON.stringify({ phone }),
    });
    const data = await res.json();
    if (data.success) { btn.textContent = 'Waiting for payment…'; pollPayment(); }
    else { showStatus('error', data.message||'Payment failed. Please try again.'); btn.disabled=false; btn.textContent='Pay KES {{ number_format($ticket->total_price) }}'; }
}
async function pollPayment() {
    for (let i=0;i<20;i++) {
        await new Promise(r=>setTimeout(r,5000));
        const res=await fetch('{{ route("booking.pay.mpesa.poll", ["booking"=>0]) }}'.replace('/0/','/{{ $ticket->id }}/'));
        const data=await res.json();
        if(data.status==='completed'){ showStatus('success'); setTimeout(()=>window.location.href='{{ route("tickets.confirmation",$ticket) }}',1500); return; }
    }
    showStatus('error','Payment timed out. If you completed the payment, contact us with ticket number: {{ $ticket->ticket_number }}');
}
function showStatus(type,msg){
    ['Pending','Success','Error'].forEach(t=>document.getElementById('status'+t).style.display='none');
    const el=document.getElementById('status'+type.charAt(0).toUpperCase()+type.slice(1));
    if(msg)el.textContent=msg; el.style.display='block';
}
</script>
@endpush