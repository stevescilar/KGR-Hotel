@extends('layouts.app')
@section('title', 'Gift Cards')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .page-hero { margin-top:72px; height:300px; background:linear-gradient(rgba(10,30,20,0.6),rgba(10,30,20,0.6)),linear-gradient(135deg,var(--forest),var(--moss)); display:flex; align-items:center; justify-content:center; text-align:center; color:white; }
    .page-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(2rem,4vw,3rem); font-weight:400; }
    .page-hero p { color:rgba(255,255,255,0.75); margin-top:0.5rem; }
    .gift-section { background:var(--cream); padding:5rem 0; }
    .gift-layout { display:grid; grid-template-columns:1fr 420px; gap:3rem; align-items:start; }
    .card-preview { background:linear-gradient(135deg,var(--forest),var(--moss)); border-radius:20px; padding:2.5rem; color:white; box-shadow:0 8px 40px rgba(30,58,47,0.3); position:sticky; top:96px; min-height:200px; }
    .card-preview .kgr-logo { font-family:'Playfair Display',serif; font-size:1.1rem; margin-bottom:1rem; opacity:0.75; }
    .card-preview .gift-label { font-size:0.7rem; font-weight:700; letter-spacing:0.2em; text-transform:uppercase; color:var(--amber); margin-bottom:0.5rem; }
    .card-preview .gift-amount { font-family:'Playfair Display',serif; font-size:3rem; font-weight:400; margin-bottom:0.5rem; }
    .card-preview .recipient { font-size:0.9rem; opacity:0.8; margin-bottom:0.4rem; }
    .card-preview .card-footer { margin-top:2rem; display:flex; justify-content:space-between; align-items:center; opacity:0.6; font-size:0.75rem; }
    .amounts-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:0.75rem; margin-bottom:1.5rem; }
    .amount-btn { border:2px solid var(--mist); background:white; border-radius:10px; padding:0.75rem; text-align:center; cursor:pointer; transition:all 0.2s; font-family:'Jost',sans-serif; }
    .amount-btn:hover, .amount-btn.selected { border-color:var(--forest); background:var(--forest); color:white; }
    .amount-btn .value { font-family:'Playfair Display',serif; font-size:1.1rem; display:block; }
    .gift-form { background:white; border-radius:16px; padding:2rem; box-shadow:0 2px 16px rgba(0,0,0,0.06); }
    .gift-form h2 { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--forest); margin-bottom:1.5rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input, .form-group select, .form-group textarea { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.9rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus, .form-group select:focus { border-color:var(--fern); }
    .section-divider { font-size:0.7rem; font-weight:700; color:var(--gold); letter-spacing:0.2em; text-transform:uppercase; border-bottom:1px solid var(--mist); padding-bottom:0.5rem; margin:1.5rem 0 1rem; }
    .btn-purchase { width:100%; background:var(--forest); color:white; border:none; cursor:pointer; padding:1rem; border-radius:10px; font-size:0.9rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-family:'Jost',sans-serif; margin-top:0.5rem; transition:background 0.2s; }
    .btn-purchase:hover { background:var(--moss); }
    .success-msg { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:1rem 1.25rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.875rem; }
    .redeem-section { background:white; padding:4rem 0; }
    .redeem-box { max-width:560px; margin:0 auto; text-align:center; }
    .redeem-box h2 { font-family:'Playfair Display',serif; font-size:1.75rem; color:var(--forest); margin-bottom:0.75rem; }
    .redeem-box p { color:#6b7280; margin-bottom:2rem; }
    .redeem-form { display:flex; gap:0.75rem; }
    .redeem-form input { flex:1; border:2px solid var(--mist); border-radius:10px; padding:0.8rem 1rem; font-size:1rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .redeem-form input:focus { border-color:var(--fern); }
    .redeem-form button { background:var(--forest); color:white; border:none; padding:0.8rem 1.5rem; border-radius:10px; font-weight:700; font-family:'Jost',sans-serif; cursor:pointer; white-space:nowrap; }
    .valid-card { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:1.25rem; margin-top:1rem; text-align:left; }
    .valid-card .card-val { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--forest); }
    @media(max-width:900px) { .gift-layout { grid-template-columns:1fr; } .card-preview { position:static; } }
    @media(max-width:480px) { .redeem-form { flex-direction:column; } .amounts-grid { grid-template-columns:repeat(2,1fr); } }
</style>
@endpush

@section('content')
<div class="page-hero">
    <div>
        <h1>Gift Cards</h1>
        <p>Give the gift of a memorable stay at Kitonga Garden Resort</p>
    </div>
</div>

<section class="gift-section">
    <div class="container">
        <div class="gift-layout">
            <div class="card-preview" id="cardPreview">
                <div class="kgr-logo">🌿 Kitonga Garden Resort</div>
                <div class="gift-label">Gift Card</div>
                <div class="gift-amount" id="previewAmount">KES 5,000</div>
                <div class="recipient" id="previewRecipient">For: —</div>
                <div id="previewMessage" style="font-size:0.8rem;opacity:0.65;font-style:italic;">A special gift from KGR</div>
                <div class="card-footer"><span>kitongagardenresort.com</span><span>Valid for stays & dining</span></div>
            </div>

            <div>
                <div class="gift-form">
                    <h2>Purchase a Gift Card</h2>
                    @if(session('success'))<div class="success-msg">✓ {{ session('success') }}</div>@endif
                    <form method="POST" action="{{ route('gift-cards.purchase') }}">
                        @csrf
                        <div class="form-group">
                            <label>Select Amount *</label>
                            <div class="amounts-grid">
                                @foreach([1000,2000,3000,5000,10000,15000] as $amt)
                                <div class="amount-btn {{ old('amount', 5000) == $amt ? 'selected' : '' }}" onclick="selectAmount({{ $amt }}, this)">
                                    <span class="value">KES {{ number_format($amt) }}</span>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="amount" id="amountInput" value="{{ old('amount', 5000) }}">
                            <input type="number" placeholder="Or enter custom amount (min KES 1,000)" min="1000" step="100" oninput="customAmount(this.value)" style="margin-top:0.5rem;width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:0.65rem 0.9rem;font-size:0.9rem;font-family:'Jost',sans-serif;outline:none;">
                        </div>
                        <div class="section-divider">Your Details</div>
                        <div class="form-group"><label>Your Name *</label><input type="text" name="purchased_by_name" value="{{ old('purchased_by_name') }}" required placeholder="Jane Mwangi"></div>
                        <div class="form-group"><label>Your Email *</label><input type="email" name="purchased_by_email" value="{{ old('purchased_by_email') }}" required placeholder="jane@example.com"></div>
                        <div class="section-divider">Recipient Details</div>
                        <div class="form-group"><label>Recipient Name *</label><input type="text" name="recipient_name" value="{{ old('recipient_name') }}" required placeholder="John Kamau" oninput="document.getElementById('previewRecipient').textContent='For: '+(this.value||'—')"></div>
                        <div class="form-group"><label>Recipient Email (for digital delivery)</label><input type="email" name="recipient_email" value="{{ old('recipient_email') }}" placeholder="john@example.com"></div>
                        <div class="form-group"><label>Personal Message</label><textarea name="message" rows="3" placeholder="Write a heartfelt message…" oninput="document.getElementById('previewMessage').textContent=this.value||'A special gift from KGR'">{{ old('message') }}</textarea></div>
                        <div class="form-group"><label>Validity</label><select name="expires_months"><option value="12">12 months (recommended)</option><option value="6">6 months</option><option value="3">3 months</option></select></div>
                        <button type="submit" class="btn-purchase" id="purchaseBtn">Purchase Gift Card · KES <span id="btnAmount">5,000</span></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="redeem-section">
    <div class="container">
        <div class="redeem-box">
            <h2>Check a Gift Card Balance</h2>
            <p>Enter your gift card code to check the remaining balance or validate it before use.</p>
            @if(session('valid_card'))
            @php $card = session('valid_card') @endphp
            <div class="valid-card">
                <div style="font-size:0.75rem;color:#16a34a;font-weight:700;margin-bottom:0.5rem;">✓ Valid Gift Card</div>
                <div class="card-val">KES {{ number_format($card->remaining_value) }}</div>
                <div style="font-size:0.8rem;color:#6b7280;margin-top:0.25rem;">Remaining balance · For: {{ $card->recipient_name }}</div>
            </div>
            @endif
            @if($errors->has('code'))<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:0.85rem;">{{ $errors->first('code') }}</div>@endif
            <form method="POST" action="{{ route('gift-cards.redeem') }}" class="redeem-form">
                @csrf
                <input type="text" name="code" placeholder="Enter gift card code e.g. KGR-XXXX-XXXX" style="text-transform:uppercase;" value="{{ old('code') }}">
                <button type="submit">Check</button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function selectAmount(amount, el) {
    document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('amountInput').value = amount;
    document.getElementById('previewAmount').textContent = 'KES ' + amount.toLocaleString();
    document.getElementById('btnAmount').textContent = amount.toLocaleString();
}
function customAmount(val) {
    if (val >= 1000) {
        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
        document.getElementById('amountInput').value = val;
        document.getElementById('previewAmount').textContent = 'KES ' + parseInt(val).toLocaleString();
        document.getElementById('btnAmount').textContent = parseInt(val).toLocaleString();
    }
}
</script>
@endpush