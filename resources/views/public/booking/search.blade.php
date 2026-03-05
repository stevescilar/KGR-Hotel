@extends('layouts.app')

@section('title', 'Book a Room')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .booking-hero {
        margin-top: 72px; background: var(--forest); padding: 3rem 0;
        text-align: center; color: white;
    }
    .booking-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(1.75rem,3vw,2.5rem); font-weight:400; }
    .booking-hero p { color:rgba(255,255,255,0.7); margin-top:0.5rem; }

    /* Step bar */
    .steps { display:flex; justify-content:center; gap:0.5rem; padding:1.5rem 0; background:var(--cream); border-bottom:1px solid var(--mist); }
    .step { display:flex; align-items:center; gap:0.4rem; font-size:0.78rem; color:#9ca3af; }
    .step.active { color:var(--forest); font-weight:700; }
    .step.done { color:var(--fern); }
    .step-num { width:24px; height:24px; border-radius:50%; border:2px solid currentColor; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; flex-shrink:0; }
    .step-sep { width:40px; height:2px; background:#e5e7eb; }

    .search-section { background:var(--cream); padding:3rem 0 5rem; }
    .search-layout { display:grid; grid-template-columns:340px 1fr; gap:2.5rem; align-items:start; }

    /* Search form */
    .search-card { background:white; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.08); position:sticky; top:88px; }
    .search-card h2 { font-family:'Playfair Display',serif; font-size:1.2rem; color:var(--forest); margin-bottom:1.5rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input, .form-group select { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.9rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus, .form-group select:focus { border-color:var(--fern); }
    .btn-search { width:100%; background:var(--forest); color:white; border:none; cursor:pointer; padding:0.85rem; border-radius:8px; font-size:0.875rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-family:'Jost',sans-serif; transition:background 0.2s; margin-top:0.5rem; }
    .btn-search:hover { background:var(--moss); }
    .btn-search:disabled { opacity:0.6; cursor:not-allowed; }

    /* Results */
    .results-area h2 { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--forest); margin-bottom:1.25rem; }
    .no-search { background:white; border-radius:14px; padding:3rem; text-align:center; color:#9ca3af; box-shadow:0 2px 12px rgba(0,0,0,0.04); }
    .no-search .icon { font-size:3rem; margin-bottom:1rem; }

    .room-result {
        background:white; border-radius:14px; padding:1.5rem 1.75rem;
        box-shadow:0 2px 14px rgba(0,0,0,0.06); margin-bottom:1rem;
        display:grid; grid-template-columns:1fr auto; gap:1.5rem; align-items:center;
        transition:box-shadow 0.2s;
    }
    .room-result:hover { box-shadow:0 6px 24px rgba(0,0,0,0.1); }
    .result-name { font-family:'Playfair Display',serif; font-size:1.15rem; color:var(--forest); margin-bottom:0.3rem; }
    .result-meta { display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:0.75rem; }
    .result-meta span { font-size:0.8rem; color:#6b7280; }
    .result-amenities { display:flex; flex-wrap:wrap; gap:0.4rem; }
    .amenity-pill { background:var(--cream); color:var(--fern); font-size:0.7rem; font-weight:600; padding:0.25rem 0.65rem; border-radius:12px; }
    .result-price { text-align:right; }
    .result-price .nights { font-size:0.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; }
    .result-price .amount { font-family:'Playfair Display',serif; font-size:1.75rem; color:var(--forest); font-weight:500; }
    .result-price .per { font-size:0.8rem; color:#9ca3af; }
    .result-price .avail { font-size:0.75rem; color:var(--fern); font-weight:600; margin:0.4rem 0; }
    .btn-select { display:block; background:var(--gold); color:white; padding:0.65rem 1.5rem; border-radius:8px; font-size:0.8rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; text-decoration:none; transition:background 0.2s; white-space:nowrap; }
    .btn-select:hover { background:#b5863a; }

    .loading { text-align:center; padding:3rem; color:#9ca3af; }
    .spinner { display:inline-block; width:28px; height:28px; border:3px solid var(--mist); border-top-color:var(--fern); border-radius:50%; animation:spin 0.7s linear infinite; margin-bottom:0.75rem; }
    @keyframes spin { to { transform:rotate(360deg); } }

    @media(max-width:900px) { .search-layout { grid-template-columns:1fr; } .search-card { position:static; } }
    @media(max-width:600px) { .room-result { grid-template-columns:1fr; } .result-price { text-align:left; } }
</style>
@endpush

@section('content')

<div class="booking-hero">
    <h1>Book a Room</h1>
    <p>Check availability and reserve your stay at Kitonga Garden Resort</p>
</div>

<div class="steps">
    <div class="step active"><span class="step-num">1</span> Search</div>
    <div class="step-sep"></div>
    <div class="step"><span class="step-num">2</span> Details</div>
    <div class="step-sep"></div>
    <div class="step"><span class="step-num">3</span> Pay</div>
    <div class="step-sep"></div>
    <div class="step"><span class="step-num">4</span> Confirm</div>
</div>

<section class="search-section">
    <div class="container">
        <div class="search-layout">

            {{-- Search form --}}
            <div class="search-card">
                <h2>🔍 Find Available Rooms</h2>
                <div class="form-group">
                    <label>Check-in Date</label>
                    <input type="date" id="checkIn" min="{{ today()->toDateString() }}" value="{{ request('check_in', today()->toDateString()) }}">
                </div>
                <div class="form-group">
                    <label>Check-out Date</label>
                    <input type="date" id="checkOut" value="{{ request('check_out', today()->addDay()->toDateString()) }}">
                </div>
                <div class="form-group">
                    <label>Adults</label>
                    <select id="adults">
                        @foreach(range(1,6) as $n)
                            <option value="{{ $n }}" @selected($n == request('adults', 2))>{{ $n }} Adult{{ $n > 1 ? 's' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Children</label>
                    <select id="children">
                        @foreach(range(0,4) as $n)
                            <option value="{{ $n }}" @selected($n == request('children', 0))>{{ $n }} {{ $n === 1 ? 'Child' : 'Children' }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-search" id="searchBtn" onclick="searchRooms()">Check Availability</button>
            </div>

            {{-- Results --}}
            <div>
                <div id="resultsArea">
                    <div class="no-search">
                        <div class="icon">🛏</div>
                        <p>Select your dates and click <strong>Check Availability</strong> to see available rooms.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
const checkInEl  = document.getElementById('checkIn');
const checkOutEl = document.getElementById('checkOut');

checkInEl.addEventListener('change', function() {
    const next = new Date(this.value);
    next.setDate(next.getDate() + 1);
    checkOutEl.min = next.toISOString().split('T')[0];
    if (!checkOutEl.value || checkOutEl.value <= this.value) {
        checkOutEl.value = next.toISOString().split('T')[0];
    }
});

async function searchRooms() {
    const btn = document.getElementById('searchBtn');
    btn.disabled = true; btn.textContent = 'Searching…';

    document.getElementById('resultsArea').innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            <p>Finding available rooms…</p>
        </div>`;

    const res  = await fetch('{{ route("booking.check") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
        body: JSON.stringify({
            check_in:  checkInEl.value,
            check_out: checkOutEl.value,
            adults:    document.getElementById('adults').value,
            children:  document.getElementById('children').value,
        }),
    });
    const data = await res.json();

    btn.disabled = false; btn.textContent = 'Check Availability';

    if (!res.ok) {
        document.getElementById('resultsArea').innerHTML = `<div class="no-search"><div class="icon">⚠️</div><p>${data.message || 'Please check your dates and try again.'}</p></div>`;
        return;
    }

    if (!data.rooms || data.rooms.length === 0) {
        document.getElementById('resultsArea').innerHTML = `<div class="no-search"><div class="icon">😔</div><p>No rooms available for the selected dates. Try different dates or contact us directly.</p></div>`;
        return;
    }

    const params = new URLSearchParams({ check_in: checkInEl.value, check_out: checkOutEl.value, adults: document.getElementById('adults').value, children: document.getElementById('children').value });

    document.getElementById('resultsArea').innerHTML = `
        <h2>${data.rooms.length} room type${data.rooms.length !== 1 ? 's' : ''} available for ${data.nights} night${data.nights !== 1 ? 's' : ''}</h2>
        ${data.rooms.map(r => `
        <div class="room-result">
            <div>
                <div class="result-name">${r.room_type.name}</div>
                <div class="result-meta">
                    <span>👥 Up to ${r.room_type.max_adults} adults</span>
                    <span>🏠 ${r.available_count} available</span>
                </div>
                ${r.room_type.amenities ? `<div class="result-amenities">${r.room_type.amenities.slice(0,4).map(a => `<span class="amenity-pill">${a}</span>`).join('')}</div>` : ''}
            </div>
            <div class="result-price">
                <div class="nights">${r.nights} night${r.nights !== 1 ? 's'  : ''}</div>
                <div class="amount">KES ${r.total.toLocaleString()}</div>
                <div class="per">inc. 16% VAT</div>
                <div class="avail">✓ ${r.available_count} room${r.available_count !== 1 ? 's' : ''} left</div>
                <a href="/book/select/${r.sample_room_id}?${params}" class="btn-select">Select</a>
            </div>
        </div>`).join('')}`;
}
</script>
@endpush