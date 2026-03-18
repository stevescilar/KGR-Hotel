@extends('layouts.app')

@section('title', 'Day Activities & Gate Tickets')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .page-hero {
        margin-top:72px; height:380px;
        background:linear-gradient(rgba(10,30,20,0.45),rgba(10,30,20,0.65)),
                   url('https://static.wixstatic.com/media/9c608a_2f0a80ebe92f4657852b48447144c505.jpg') center/cover;
        display:flex; align-items:center; justify-content:center; text-align:center; color:white;
    }
    .page-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.25rem); font-weight:400; }
    .page-hero p  { color:rgba(255,255,255,0.75); margin-top:0.75rem; max-width:520px; }

    .tickets-section { background:var(--cream); padding:5rem 0; }
    .tickets-layout { display:grid; grid-template-columns:1fr 400px; gap:3rem; align-items:start; }

    /* Activities included */
    .activities-section { background:white; padding:5rem 0; }
    .section-header { text-align:center; margin-bottom:3rem; }
    .section-eyebrow { font-size:0.7rem; font-weight:700; letter-spacing:0.25em; text-transform:uppercase; color:var(--gold); display:block; margin-bottom:0.75rem; }
    .section-header h2 { font-family:'Playfair Display',serif; font-size:clamp(1.75rem,3vw,2.25rem); color:var(--forest); }
    .activities-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1.25rem; margin-top:2.5rem; }
    .activity-card { background:var(--cream); border-radius:14px; padding:1.75rem; text-align:center; }
    .activity-card .icon { font-size:2.5rem; margin-bottom:0.75rem; }
    .activity-card h4 { font-size:0.9rem; font-weight:700; color:var(--forest); margin-bottom:0.25rem; }
    .activity-card p  { font-size:0.78rem; color:#6b7280; }

    /* Ticket type cards */
    .ticket-types { display:flex; flex-direction:column; gap:1rem; }
    .ticket-type-card {
        background:white; border-radius:14px; padding:1.5rem 1.75rem;
        box-shadow:0 2px 14px rgba(0,0,0,0.06);
        display:flex; align-items:center; justify-content:space-between; gap:1.5rem;
        cursor:pointer; border:2px solid transparent; transition:all 0.2s;
    }
    .ticket-type-card:hover, .ticket-type-card.selected { border-color:var(--forest); box-shadow:0 6px 24px rgba(0,0,0,0.1); }
    .ticket-icon { font-size:2.25rem; flex-shrink:0; }
    .ticket-info h3 { font-family:'Playfair Display',serif; font-size:1.15rem; color:var(--forest); margin-bottom:0.25rem; }
    .ticket-info p  { font-size:0.82rem; color:#6b7280; }
    .ticket-price { text-align:right; flex-shrink:0; }
    .ticket-price .amount { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--forest); }
    .ticket-price .label  { font-size:0.72rem; color:#9ca3af; }

    .includes-box { background:white; border-radius:14px; padding:1.75rem; margin-top:1.5rem; box-shadow:0 2px 14px rgba(0,0,0,0.05); }
    .includes-box h3 { font-family:'Playfair Display',serif; color:var(--forest); margin-bottom:1rem; font-size:1.1rem; }
    .includes-list { display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; }
    .includes-list li { font-size:0.82rem; color:#4b5563; list-style:none; display:flex; align-items:center; gap:0.4rem; }

    /* Purchase form */
    .purchase-card { background:white; border-radius:16px; box-shadow:0 4px 30px rgba(0,0,0,0.08); padding:2rem; position:sticky; top:96px; }
    .purchase-card h3 { font-family:'Playfair Display',serif; font-size:1.25rem; color:var(--forest); margin-bottom:1.5rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input, .form-group select { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.9rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus, .form-group select:focus { border-color:var(--fern); }
    .price-summary { background:var(--cream); border-radius:10px; padding:1rem 1.25rem; margin:1rem 0; font-size:0.875rem; }
    .price-summary .row   { display:flex; justify-content:space-between; padding:0.25rem 0; color:#4b5563; }
    .price-summary .total { font-weight:700; color:var(--forest); border-top:1px solid var(--mist); margin-top:0.5rem; padding-top:0.5rem; font-size:1rem; }
    .btn-purchase { width:100%; background:var(--forest); color:white; border:none; cursor:pointer; padding:1rem; border-radius:10px; font-size:0.9rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-family:'Jost',sans-serif; transition:background 0.2s; }
    .btn-purchase:hover { background:var(--moss); }
    .success-msg { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:1rem 1.25rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.875rem; }

    @media(max-width:900px) { .tickets-layout { grid-template-columns:1fr; } .purchase-card { position:static; } .includes-list { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

<div class="page-hero">
    <div>
        <h1>Day Activities</h1>
        <p>Visit KGR for the day — gardens, pool, nature walks and dining without an overnight stay</p>
    </div>
</div>

{{-- What's included --}}
<section class="activities-section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Your Day at KGR</span>
            <h2>What's Included With Your Ticket</h2>
        </div>
        <div class="activities-grid">
            @foreach([
                ['🌿','Lush Gardens','Stroll through beautifully maintained tropical gardens'],
                ['🏊','Swimming Pool','Relax at our resort pool with sunbeds'],
                ['🥾','Nature Walks','Guided and self-guided trails through the grounds'],
                ['🏔','Scenic Views','Panoramic views of Yatta Plateau & Mt Kenya'],
                ['🍽','Dining','Access to our restaurant and bar'],
                ['🚗','Free Parking','Secure on-site parking for all guests'],
            ] as [$icon,$title,$desc])
            <div class="activity-card">
                <div class="icon">{{ $icon }}</div>
                <h4>{{ $title }}</h4>
                <p>{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Tickets + purchase form --}}
<section class="tickets-section">
    <div class="container">
        <div class="tickets-layout">

            {{-- Left: ticket types --}}
            <div>
                <h2 style="font-family:'Playfair Display',serif;font-size:1.75rem;color:var(--forest);margin-bottom:1.5rem;">Choose Your Ticket</h2>
                <div class="ticket-types">
                    @forelse($ticketTypes as $type)
                    <div class="ticket-type-card" onclick="selectTicket({{ $type->id }}, {{ $type->price }}, '{{ $type->name }}', this)">
                        <div class="ticket-icon">🎫</div>
                        <div class="ticket-info">
                            <h3>{{ $type->name }}</h3>
                            @if($type->description)<p>{{ $type->description }}</p>@endif
                        </div>
                        <div class="ticket-price">
                            <div class="amount">KES {{ number_format($type->price) }}</div>
                            <div class="label">per person</div>
                        </div>
                    </div>
                    @empty
                    {{-- Fallback default ticket if none in DB --}}
                    <div class="ticket-type-card selected" onclick="selectTicket('default', 1500, 'Day Visit Ticket', this)">
                        <div class="ticket-icon">🎫</div>
                        <div class="ticket-info">
                            <h3>Day Visit Ticket</h3>
                            <p>Full day access to all resort facilities</p>
                        </div>
                        <div class="ticket-price">
                            <div class="amount">KES 1,500</div>
                            <div class="label">per person</div>
                        </div>
                    </div>
                    @endforelse
                </div>

                <div class="includes-box">
                    <h3>Includes</h3>
                    <ul class="includes-list">
                        @foreach(['Access to resort gardens','Swimming pool','Free parking','Recreational areas','Nature walks','Viewpoint access'] as $item)
                            <li>✓ {{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Right: purchase form --}}
            <div>
                <div class="purchase-card">
                    <h3>Book Your Visit</h3>
                    @if(session('success'))
                        <div class="success-msg">✓ {{ session('success') }}</div>
                    @endif
                    <form method="POST" action="{{ route('tickets.purchase') }}">
                        @csrf
                        <input type="hidden" name="ticket_type_id" id="ticketTypeId"
                               value="{{ $ticketTypes->first()?->id ?? 'default' }}">

                        <div class="form-group">
                            <label>Ticket Type *</label>
                            <select name="ticket_type_id" id="ticketTypeSelect" required onchange="updateFromSelect(this)">
                                @forelse($ticketTypes as $type)
                                    <option value="{{ $type->id }}" data-price="{{ $type->price }}" data-name="{{ $type->name }}">
                                        {{ $type->name }} — KES {{ number_format($type->price) }}
                                    </option>
                                @empty
                                    <option value="default" data-price="1500" data-name="Day Visit Ticket">Day Visit Ticket — KES 1,500</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Visit Date *</label>
                            <input type="date" name="visit_date" required
                                   min="{{ today()->toDateString() }}"
                                   value="{{ today()->toDateString() }}">
                        </div>
                        <div class="form-group">
                            <label>Number of People *</label>
                            <input type="number" name="quantity" id="ticketQty" min="1" max="50" value="1" required oninput="updateTotal()">
                        </div>
                        <div class="form-group">
                            <label>Your Name *</label>
                            <input type="text" name="guest_name" required placeholder="Jane Mwangi">
                        </div>
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="guest_phone" required placeholder="+254 7XX XXX XXX">
                        </div>
                        <div class="form-group">
                            <label>Email (for ticket delivery)</label>
                            <input type="email" name="guest_email" placeholder="jane@example.com">
                        </div>
                        <div class="price-summary" id="priceSummary">
                            <div class="row"><span id="psType">Day Visit Ticket</span><span id="psPrice">KES 1,500</span></div>
                            <div class="row"><span>Quantity</span><span id="psQty">1</span></div>
                            <div class="row total"><span>Total</span><span id="psTotal">KES 1,500</span></div>
                        </div>
                        <button type="submit" class="btn-purchase">Proceed to Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
let selectedPrice = {{ $ticketTypes->first()?->price ?? 1500 }};
let selectedName  = '{{ $ticketTypes->first()?->name ?? "Day Visit Ticket" }}';

function selectTicket(id, price, name, el) {
    selectedPrice = price; selectedName = name;
    document.getElementById('ticketTypeSelect').value = id;
    document.querySelectorAll('.ticket-type-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    updateTotal();
}
function updateFromSelect(sel) {
    const opt = sel.options[sel.selectedIndex];
    selectedPrice = parseFloat(opt.dataset.price || 0);
    selectedName  = opt.dataset.name || '';
    updateTotal();
}
function updateTotal() {
    const qty = parseInt(document.getElementById('ticketQty').value) || 1;
    const total = selectedPrice * qty;
    document.getElementById('psType').textContent  = selectedName;
    document.getElementById('psPrice').textContent = 'KES ' + selectedPrice.toLocaleString();
    document.getElementById('psQty').textContent   = qty;
    document.getElementById('psTotal').textContent = 'KES ' + total.toLocaleString();
}
// Init
document.addEventListener('DOMContentLoaded', () => {
    const first = document.querySelector('.ticket-type-card');
    if (first) first.classList.add('selected');
    updateTotal();
});
</script>
@endpush