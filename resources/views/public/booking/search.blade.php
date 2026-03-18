@extends('layouts.app')

@section('title', 'Book a Room')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .booking-hero { margin-top:72px; background:var(--forest); padding:3rem 0; text-align:center; color:white; }
    .booking-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(1.75rem,3vw,2.5rem); font-weight:400; }
    .booking-hero p { color:rgba(255,255,255,0.7); margin-top:0.5rem; }

    .steps { display:flex; justify-content:center; gap:0.5rem; padding:1.5rem 0; background:var(--cream); border-bottom:1px solid var(--mist); }
    .step { display:flex; align-items:center; gap:0.4rem; font-size:0.78rem; color:#9ca3af; }
    .step.active { color:var(--forest); font-weight:700; }
    .step-num { width:24px; height:24px; border-radius:50%; border:2px solid currentColor; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; }
    .step-sep { width:40px; height:2px; background:#e5e7eb; }

    .search-section { background:var(--cream); padding:3rem 0 5rem; }
    .search-layout { display:grid; grid-template-columns:320px 1fr; gap:2.5rem; align-items:start; }

    .search-card { background:white; border-radius:16px; padding:1.75rem; box-shadow:0 4px 24px rgba(0,0,0,0.08); position:sticky; top:88px; }
    .search-card h2 { font-family:'Playfair Display',serif; font-size:1.2rem; color:var(--forest); margin-bottom:1.5rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input, .form-group select { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.9rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus, .form-group select:focus { border-color:var(--fern); }
    .btn-search { width:100%; background:var(--forest); color:white; border:none; cursor:pointer; padding:0.85rem; border-radius:8px; font-size:0.875rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-family:'Jost',sans-serif; transition:background 0.2s; margin-top:0.5rem; }
    .btn-search:hover { background:var(--moss); }
    .btn-search:disabled { opacity:0.6; cursor:not-allowed; }

    /* Selected room type banner */
    .selected-type-banner {
        background:linear-gradient(135deg, var(--forest), var(--moss));
        border-radius:10px; padding:0.85rem 1rem; margin-bottom:1.25rem;
        display:flex; align-items:center; gap:0.75rem; color:white;
    }
    .selected-type-banner .type-name { font-weight:700; font-size:0.9rem; }
    .selected-type-banner .type-sub { font-size:0.75rem; color:rgba(255,255,255,0.7); }
    .clear-selection { margin-left:auto; font-size:0.7rem; color:rgba(255,255,255,0.6); cursor:pointer; text-decoration:underline; background:none; border:none; }

    /* Results */
    .results-area h2 { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--forest); margin-bottom:1.25rem; }
    .no-search { background:white; border-radius:14px; padding:3rem; text-align:center; color:#9ca3af; box-shadow:0 2px 12px rgba(0,0,0,0.04); }
    .no-search .icon { font-size:3rem; margin-bottom:1rem; }

    .room-result {
        background:white; border-radius:14px; overflow:hidden;
        box-shadow:0 2px 14px rgba(0,0,0,0.06); margin-bottom:1rem;
        display:grid; grid-template-columns:260px 1fr auto; gap:0;
        transition:box-shadow 0.2s; min-height:160px;
    }
    .room-result:hover { box-shadow:0 6px 24px rgba(0,0,0,0.1); }
    .room-result.highlighted { border:2px solid var(--gold); }
    .result-img { width:260px; height:100%; object-fit:cover; display:block; min-height:160px; }
    .result-img-placeholder { width:260px; min-height:160px; background:linear-gradient(135deg,var(--forest),var(--moss)); display:flex; align-items:center; justify-content:center; font-size:2.5rem; }
    .result-body { padding:1.25rem 1.5rem; display:flex; flex-direction:column; justify-content:center; }
    .result-name { font-family:'Playfair Display',serif; font-size:1.15rem; color:var(--forest); margin-bottom:0.35rem; }
    .result-meta { display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:0.75rem; }
    .result-meta span { font-size:0.78rem; color:#6b7280; }
    .result-amenities { display:flex; flex-wrap:wrap; gap:0.4rem; }
    .amenity-pill { background:var(--cream); color:var(--fern); font-size:0.68rem; font-weight:600; padding:0.2rem 0.6rem; border-radius:12px; }
    .result-price-col { padding:1.25rem 1.5rem; display:flex; flex-direction:column; align-items:flex-end; justify-content:center; gap:0.5rem; border-left:1px solid #f3f4f6; min-width:160px; }
    .result-nights { font-size:0.7rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em; }
    .result-amount { font-family:'Playfair Display',serif; font-size:1.6rem; color:var(--forest); font-weight:500; }
    .result-vat { font-size:0.72rem; color:#9ca3af; }
    .result-avail { font-size:0.75rem; color:var(--fern); font-weight:600; }
    .btn-select { display:block; background:var(--gold); color:white; padding:0.65rem 1.5rem; border-radius:8px; font-size:0.78rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; text-decoration:none; transition:background 0.2s; white-space:nowrap; text-align:center; }
    .btn-select:hover { background:#b5863a; }

    .loading { text-align:center; padding:3rem; color:#9ca3af; }
    .spinner { display:inline-block; width:28px; height:28px; border:3px solid var(--mist); border-top-color:var(--fern); border-radius:50%; animation:spin 0.7s linear infinite; margin-bottom:0.75rem; }
    @keyframes spin { to { transform:rotate(360deg); } }

    @media(max-width:900px) { .search-layout { grid-template-columns:1fr; } .search-card { position:static; } }
    @media(max-width:640px) { .room-result { grid-template-columns:1fr; } .result-img, .result-img-placeholder { width:100%; height:220px; } .result-price-col { border-left:none; border-top:1px solid #f3f4f6; flex-direction:row; justify-content:space-between; align-items:center; } }
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

                {{-- Pre-selected room type banner --}}
                <div id="selectedTypeBanner" class="selected-type-banner" style="display:none;">
                    <div>
                        <div class="type-name" id="bannerTypeName"></div>
                        <div class="type-sub">Pre-selected from room listing</div>
                    </div>
                    <button class="clear-selection" onclick="clearRoomType()">Clear</button>
                </div>

                <input type="hidden" id="preselectedRoomType" value="{{ request('room_type') }}">

                <div class="form-group">
                    <label>Check-in Date</label>
                    <input type="date" id="checkIn"
                           min="{{ today()->toDateString() }}"
                           value="{{ request('check_in', today()->toDateString()) }}">
                </div>
                <div class="form-group">
                    <label>Check-out Date</label>
                    <input type="date" id="checkOut"
                           value="{{ request('check_out', today()->addDay()->toDateString()) }}">
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
// ── Room type images (for result cards) ──────────────────────────
const roomImages = {
    'standard':            'https://static.wixstatic.com/media/87c8f7_93a0afa668464496a6f8636744e86fc4~mv2.jpg/v1/fill/w_400,h_300,al_c,q_80/87c8f7_93a0afa668464496a6f8636744e86fc4~mv2.jpg',
    'deluxe':              'https://static.wixstatic.com/media/87c8f7_a8fdf9b4baa3478d8bcbf501772e7b9d~mv2.jpg/v1/fill/w_400,h_300,al_c,q_80/87c8f7_a8fdf9b4baa3478d8bcbf501772e7b9d~mv2.jpg',
    'penthouse':           'https://static.wixstatic.com/media/87c8f7_ae6b2ce224f54eff8127c7e9fe0e7266~mv2.jpg/v1/fill/w_400,h_300,al_c,q_80/87c8f7_ae6b2ce224f54eff8127c7e9fe0e7266~mv2.jpg',
    'presidential-family': 'https://static.wixstatic.com/media/87c8f7_953ad41ed0c1461d89a835b7c51270e2~mv2.jpg/v1/fill/w_400,h_300,al_c,q_80/87c8f7_953ad41ed0c1461d89a835b7c51270e2~mv2.jpg',
    'royal-presidential':  'https://static.wixstatic.com/media/87c8f7_f9c30fa9dbce489b9b7b7920acb5a8a9~mv2.jpg/v1/fill/w_400,h_300,al_c,q_80/87c8f7_f9c30fa9dbce489b9b7b7920acb5a8a9~mv2.jpg',
};

const checkInEl  = document.getElementById('checkIn');
const checkOutEl = document.getElementById('checkOut');
const preselectedType = document.getElementById('preselectedRoomType').value;

// ── Init: set min checkout, show banner if room_type passed ──────
checkInEl.addEventListener('change', function() {
    const next = new Date(this.value);
    next.setDate(next.getDate() + 1);
    checkOutEl.min = next.toISOString().split('T')[0];
    if (!checkOutEl.value || checkOutEl.value <= this.value) {
        checkOutEl.value = next.toISOString().split('T')[0];
    }
});

if (preselectedType) {
    const banner   = document.getElementById('selectedTypeBanner');
    const nameEl   = document.getElementById('bannerTypeName');
    const readable = preselectedType.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    nameEl.textContent = readable;
    banner.style.display = 'flex';
    // Auto-search when room_type is in URL
    window.addEventListener('DOMContentLoaded', () => searchRooms());
}

function clearRoomType() {
    document.getElementById('preselectedRoomType').value = '';
    document.getElementById('selectedTypeBanner').style.display = 'none';
}

// ── Search ───────────────────────────────────────────────────────
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
        document.getElementById('resultsArea').innerHTML =
            `<div class="no-search"><div class="icon">⚠️</div><p>${data.message || 'Please check your dates and try again.'}</p></div>`;
        return;
    }

    if (!data.rooms || data.rooms.length === 0) {
        document.getElementById('resultsArea').innerHTML =
            `<div class="no-search"><div class="icon">😔</div><p>No rooms available for those dates. Try different dates or <a href="{{ route('contact') }}" style="color:var(--fern)">contact us</a> directly.</p></div>`;
        return;
    }

    const params = new URLSearchParams({
        check_in:  checkInEl.value,
        check_out: checkOutEl.value,
        adults:    document.getElementById('adults').value,
        children:  document.getElementById('children').value,
    });

    const selectedSlug = document.getElementById('preselectedRoomType').value;

    // Sort: put the pre-selected room type first
    const sorted = [...data.rooms].sort((a, b) => {
        if (selectedSlug && a.room_type.slug === selectedSlug) return -1;
        if (selectedSlug && b.room_type.slug === selectedSlug) return 1;
        return 0;
    });

    document.getElementById('resultsArea').innerHTML = `
        <h2>${data.rooms.length} room type${data.rooms.length !== 1 ? 's' : ''} available · ${data.nights} night${data.nights !== 1 ? 's' : ''}</h2>
        ${sorted.map(r => {
            const slug      = r.room_type.slug;
            const img       = roomImages[slug];
            const isHighlit = selectedSlug && slug === selectedSlug;
            const amenities = (r.room_type.amenities || []).slice(0,4)
                .map(a => `<span class="amenity-pill">${a}</span>`).join('');
            return `
            <div class="room-result ${isHighlit ? 'highlighted' : ''}">
                ${img
                    ? `<img src="${img}" alt="${r.room_type.name}" class="result-img" loading="lazy">`
                    : `<div class="result-img-placeholder">🛏</div>`
                }
                <div class="result-body">
                    <div class="result-name">
                        ${isHighlit ? '⭐ ' : ''}${r.room_type.name}
                    </div>
                    <div class="result-meta">
                        <span>👥 Up to ${r.room_type.max_adults} adults</span>
                        <span>🏠 ${r.available_count} room${r.available_count !== 1 ? 's' : ''} left</span>
                    </div>
                    <div class="result-amenities">${amenities}</div>
                </div>
                <div class="result-price-col">
                    <div class="result-nights">${r.nights} night${r.nights !== 1 ? 's' : ''}</div>
                    <div class="result-amount">KES ${r.total.toLocaleString()}</div>
                    <div class="result-vat">inc. 16% VAT</div>
                    <div class="result-avail">✓ ${r.available_count} available</div>
                    <a href="/book/select/${r.sample_room_id}?${params}" class="btn-select">Select Room</a>
                </div>
            </div>`;
        }).join('')}`;

    // Scroll to highlighted result
    if (selectedSlug) {
        setTimeout(() => {
            const el = document.querySelector('.room-result.highlighted');
            if (el) el.scrollIntoView({ behavior:'smooth', block:'center' });
        }, 200);
    }
}
</script>
@endpush