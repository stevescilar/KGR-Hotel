@extends('layouts.app')
@section('title', 'Ticket Confirmed')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .confirm-section { background:var(--cream); padding:5rem 0; margin-top:72px; min-height:calc(100vh - 72px); }
    .confirm-card { background:white; border-radius:20px; box-shadow:0 4px 40px rgba(0,0,0,0.08); max-width:560px; margin:0 auto; overflow:hidden; }
    .confirm-header { background:linear-gradient(135deg,var(--forest),var(--moss)); padding:2.5rem 2rem; text-align:center; color:white; }
    .confirm-header .check { font-size:3rem; margin-bottom:0.75rem; }
    .confirm-header h1 { font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:400; margin-bottom:0.4rem; }
    .confirm-header p { color:rgba(255,255,255,0.75); font-size:0.9rem; }
    .ticket-body { padding:2rem 2.5rem; }
    .ticket-number { text-align:center; margin-bottom:2rem; }
    .ticket-number span { font-family:'DM Mono',monospace; font-size:1.5rem; font-weight:700; letter-spacing:0.15em; color:var(--forest); }
    .ticket-number p { font-size:0.75rem; color:#9ca3af; margin-top:0.25rem; }
    .qr-box { text-align:center; background:var(--cream); border-radius:12px; padding:1.5rem; margin-bottom:2rem; }
    .qr-box p { font-size:0.75rem; color:#9ca3af; margin-top:0.75rem; }
    .details-grid { display:grid; grid-template-columns:1fr 1fr; gap:0; margin-bottom:1.5rem; }
    .detail-item { padding:0.85rem 0; border-bottom:1px solid #f3f4f6; }
    .detail-item:nth-child(odd) { padding-right:1rem; }
    .detail-item:nth-child(even) { padding-left:1rem; border-left:1px solid #f3f4f6; }
    .detail-label { font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; color:#9ca3af; margin-bottom:0.25rem; }
    .detail-value { font-size:0.9rem; font-weight:600; color:var(--ink); }
    .important-note { background:#fef9c3; border:1px solid #fde68a; border-radius:8px; padding:1rem 1.25rem; margin-bottom:1.5rem; font-size:0.82rem; color:#92400e; }
    .btn-print { display:block; text-align:center; border:2px solid var(--mist); color:var(--fern); padding:0.85rem; border-radius:10px; font-size:0.85rem; font-weight:600; text-decoration:none; transition:all 0.2s; margin-bottom:0.75rem; cursor:pointer; background:none; width:100%; font-family:'Jost',sans-serif; }
    .btn-print:hover { border-color:var(--fern); }
    .btn-home { display:block; text-align:center; background:var(--forest); color:white; padding:0.85rem; border-radius:10px; font-size:0.85rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; text-decoration:none; transition:background 0.2s; }
    .btn-home:hover { background:var(--moss); }
    @media print {
        @page { size:landscape; margin:1.5cm; }
        a[href]::after { content:none !important; }
        body > *:not(.confirm-section) { display:none !important; }
        .confirm-section { margin:0 !important; padding:0 !important; background:white !important; }
        .confirm-card { box-shadow:none !important; max-width:100% !important; border:1px solid #ddd; display:grid; grid-template-columns:260px 1fr; }
        .confirm-header { background:#1e3a2f !important; -webkit-print-color-adjust:exact; }
        .ticket-body { padding:1.5rem; }
        .btn-print,.btn-home { display:none !important; }
    }
    @media(max-width:540px) { .ticket-body { padding:1.5rem; } .details-grid { grid-template-columns:1fr; } .detail-item:nth-child(even) { padding-left:0; border-left:none; } }
</style>
@endpush

@section('content')
<section class="confirm-section">
    <div class="container">
        <div class="confirm-card">
            <div class="confirm-header">
                <div class="check">🎫</div>
                <h1>Ticket Confirmed!</h1>
                <p>Present this QR code at the gate on your visit date</p>
            </div>
            <div class="ticket-body">
                <div class="ticket-number">
                    <span>{{ $ticket->ticket_number }}</span>
                    <p>Ticket Number</p>
                </div>
                <div class="qr-box">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($ticket->qr_code ?? $ticket->ticket_number) }}&bgcolor=ffffff&color=1e3a2f&margin=10"
                         alt="QR Code" width="180" height="180" style="border-radius:8px;">
                    <p>Scan at the gate · {{ $ticket->ticket_number }}</p>
                </div>
                <div class="details-grid">
                    <div class="detail-item"><div class="detail-label">Guest Name</div><div class="detail-value">{{ $ticket->guest_name }}</div></div>
                    <div class="detail-item"><div class="detail-label">Visit Date</div><div class="detail-value">{{ $ticket->visit_date->format('D, M j Y') }}</div></div>
                    <div class="detail-item"><div class="detail-label">Ticket Type</div><div class="detail-value">{{ $ticket->ticketType->name }}</div></div>
                    <div class="detail-item"><div class="detail-label">Quantity</div><div class="detail-value">{{ $ticket->quantity }} person{{ $ticket->quantity > 1 ? 's' : '' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Amount Paid</div><div class="detail-value">KES {{ number_format($ticket->total_price) }}</div></div>
                    <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value" style="color:#16a34a;">✓ Confirmed</div></div>
                </div>
                <div class="important-note">⚠️ Present this QR code at the resort gate on <strong>{{ $ticket->visit_date->format('D, M j Y') }}</strong>. Valid for one-time entry only.</div>
                <button class="btn-print" onclick="window.print()">🖨 Print Ticket</button>
                <a href="{{ route('home') }}" class="btn-home">Back to Home</a>
            </div>
        </div>
    </div>
</section>
@endsection 