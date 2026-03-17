@extends('layouts.app')

@section('title', 'Kitonga Garden Resort')
@section('navbar-class', 'transparent')

@push('styles')
<style>
    /* ── HERO ── */
    .hero {
        height: 100vh; min-height: 600px;
        background: linear-gradient(to bottom, rgba(10,30,20,0.55) 0%, rgba(10,30,20,0.3) 60%, rgba(10,30,20,0.7) 100%),
                    url('https://static.wixstatic.com/media/87c8f7_a8fdf9b4baa3478d8bcbf501772e7b9d~mv2.jpg') center/cover no-repeat;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        text-align: center; color: white;
        padding: 2rem;
    }
    .hero-eyebrow {
        font-size: 0.75rem; font-weight: 600; letter-spacing: 0.3em;
        text-transform: uppercase; color: var(--amber);
        margin-bottom: 1.25rem;
    }
    .hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.5rem, 6vw, 5rem);
        font-weight: 400; line-height: 1.15;
        margin-bottom: 1.25rem;
    }
    .hero h1 em { font-style: italic; color: var(--amber); }
    .hero p {
        font-size: 1.1rem; color: rgba(255,255,255,0.8);
        max-width: 520px; margin-bottom: 2.5rem;
    }
    .hero-actions { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; }
    .btn-primary {
        background: var(--gold); color: white;
        padding: 0.9rem 2rem; border-radius: 8px;
        font-size: 0.85rem; font-weight: 700; letter-spacing: 0.1em;
        text-transform: uppercase; text-decoration: none;
        transition: background 0.2s, transform 0.2s;
        display: inline-block;
    }
    .btn-primary:hover { background: var(--amber); transform: translateY(-1px); }
    .btn-outline {
        border: 2px solid rgba(255,255,255,0.6); color: white;
        padding: 0.9rem 2rem; border-radius: 8px;
        font-size: 0.85rem; font-weight: 600; letter-spacing: 0.08em;
        text-decoration: none; transition: border-color 0.2s, background 0.2s;
        display: inline-block;
    }
    .btn-outline:hover { border-color: white; background: rgba(255,255,255,0.1); }

    /* ── QUICK BOOKING BAR ── */
    .booking-bar {
        background: white;
        box-shadow: 0 8px 40px rgba(0,0,0,0.12);
        border-radius: 12px;
        padding: 1.5rem 2rem;
        margin: -3rem 1.5rem 0;
        position: relative; z-index: 10;
        max-width: 900px; margin-left: auto; margin-right: auto;
        display: grid; grid-template-columns: 1fr 1fr 1fr auto;
        gap: 1rem; align-items: end;
    }
    .booking-bar label {
        display: block; font-size: 0.7rem; font-weight: 700;
        color: var(--fern); letter-spacing: 0.12em; text-transform: uppercase;
        margin-bottom: 0.4rem;
    }
    .booking-bar input, .booking-bar select {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 8px;
        padding: 0.65rem 0.9rem; font-size: 0.9rem; font-family: 'Jost', sans-serif;
        outline: none; transition: border-color 0.2s;
    }
    .booking-bar input:focus, .booking-bar select:focus { border-color: var(--fern); }
    .booking-bar .btn-search {
        background: var(--forest); color: white; border: none; cursor: pointer;
        padding: 0.7rem 1.75rem; border-radius: 8px;
        font-size: 0.85rem; font-weight: 700; letter-spacing: 0.08em;
        text-transform: uppercase; font-family: 'Jost', sans-serif;
        transition: background 0.2s; white-space: nowrap;
    }
    .booking-bar .btn-search:hover { background: var(--moss); }

    /* ── SECTION HEADER ── */
    .section-header { text-align: center; margin-bottom: 3rem; }
    .section-eyebrow {
        font-size: 0.7rem; font-weight: 700; letter-spacing: 0.25em;
        text-transform: uppercase; color: var(--gold);
        display: block; margin-bottom: 0.75rem;
    }
    .section-header h2 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.75rem, 3vw, 2.5rem);
        font-weight: 400; color: var(--forest); line-height: 1.25;
    }
    .section-header p {
        color: #6b7280; margin-top: 0.75rem;
        max-width: 520px; margin-left: auto; margin-right: auto;
    }

    /* ── ROOM CARDS ── */
    .rooms-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    .room-card {
        border-radius: 12px; overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.07);
        transition: transform 0.3s, box-shadow 0.3s;
        text-decoration: none; color: inherit; display: block;
        background: white;
    }
    .room-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
    .room-card-image {
        height: 200px; background: var(--warm);
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem; background-size: cover; background-position: center;
    }
    .room-card-body { padding: 1.25rem 1.5rem 1.5rem; }
    .room-card-body h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem; color: var(--forest); margin-bottom: 0.4rem;
    }
    .room-card-body p { font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem; }
    .room-card-footer {
        display: flex; align-items: center; justify-content: space-between;
        border-top: 1px solid #f3f4f6; padding-top: 1rem; margin-top: 0.5rem;
    }
    .room-price { font-family: 'Playfair Display', serif; }
    .room-price .amount { font-size: 1.35rem; font-weight: 500; color: var(--forest); }
    .room-price .per { font-size: 0.75rem; color: #9ca3af; }
    .btn-view {
        font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em;
        text-transform: uppercase; color: var(--fern);
        text-decoration: none; transition: color 0.2s;
    }
    .btn-view:hover { color: var(--forest); }

    /* ── FEATURES STRIP ── */
    .features {
        background: var(--cream);
        padding: 4rem 1.5rem;
    }
    .features-grid {
        max-width: 1100px; margin: 0 auto;
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 2rem; text-align: center;
    }
    .feature-icon { font-size: 2rem; margin-bottom: 0.75rem; }
    .feature h4 { font-size: 0.9rem; font-weight: 700; color: var(--forest); margin-bottom: 0.4rem; }
    .feature p { font-size: 0.8rem; color: #6b7280; }

    /* ── EVENTS CTA ── */
    .events-cta {
        background: linear-gradient(135deg, var(--forest) 0%, var(--moss) 100%);
        padding: 5rem 1.5rem; text-align: center; color: white;
    }
    .events-cta h2 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.75rem, 3vw, 2.5rem);
        margin-bottom: 1rem;
    }
    .events-cta p { color: rgba(255,255,255,0.75); max-width: 500px; margin: 0 auto 2rem; }

    /* ── TICKETS CTA ── */
    .tickets-cta {
        background: var(--warm); padding: 4rem 1.5rem;
        display: flex; align-items: center; justify-content: center; gap: 4rem;
        flex-wrap: wrap;
    }
    .tickets-text h2 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.5rem, 2.5vw, 2rem);
        color: var(--forest); margin-bottom: 0.5rem;
    }
    .tickets-text p { color: #6b7280; max-width: 380px; }

    @media (max-width: 768px) {
        .booking-bar { grid-template-columns: 1fr 1fr; }
        .features-grid { grid-template-columns: repeat(2, 1fr); }
        .tickets-cta { flex-direction: column; gap: 1.5rem; text-align: center; }
    }
    @media (max-width: 480px) {
        .booking-bar { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

    {{-- ── HERO ── --}}
    <section class="hero">
        <p class="hero-eyebrow">Ukasi · Kitui County · Kenya</p>
        <h1>Your <em>Luxurious</em><br>Home Away From Home</h1>
        <p>Nestled where the Yatta Plateau meets the sky — gardens, suites, and unforgettable moments await.</p>
        <div class="hero-actions">
            <a href="{{ route('booking.index') }}" class="btn-primary">Book a Room</a>
            <a href="{{ route('rooms') }}" class="btn-outline">Explore Rooms</a>
        </div>
    </section>

    {{-- ── QUICK BOOKING BAR ── --}}
    <div style="background: var(--cream); padding-bottom: 4rem;">
        <form class="booking-bar" action="{{ route('booking.index') }}" method="GET">
            <div>
                <label for="check_in">Check-in</label>
                <input type="date" id="check_in" name="check_in" min="{{ today()->toDateString() }}">
            </div>
            <div>
                <label for="check_out">Check-out</label>
                <input type="date" id="check_out" name="check_out">
            </div>
            <div>
                <label for="adults">Guests</label>
                <select id="adults" name="adults">
                    @foreach(range(1, 6) as $n)
                        <option value="{{ $n }}">{{ $n }} Adult{{ $n > 1 ? 's' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn-search">Check Availability</button>
            </div>
        </form>
    </div>

    {{-- ── ROOM TYPES ── --}}
    <section class="section" style="background: var(--cream); padding-top: 0;">
        <div class="container">
            <div class="section-header">
                <span class="section-eyebrow">Accommodation</span>
                <h2>Rooms & Suites</h2>
                <p>Five room categories, each designed to deliver comfort, privacy, and sweeping garden views.</p>
            </div>

            <div class="rooms-grid">
                @forelse($roomTypes as $type)
                <a href="{{ route('rooms.show', $type->slug) }}" class="room-card">
                    <div class="room-card-image">🛏</div>
                    <div class="room-card-body">
                        <h3>{{ $type->name }}</h3>
                        <p>{{ Str::limit($type->description, 90) }}</p>
                        <div class="room-card-footer">
                            <div class="room-price">
                                <span class="amount">KES {{ number_format($type->base_price) }}</span>
                                <span class="per"> / night</span>
                            </div>
                            <span class="btn-view">View Details →</span>
                        </div>
                    </div>
                </a>
                @empty
                <p style="color:#9ca3af;grid-column:1/-1;text-align:center;padding:3rem;">No rooms available yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ── FEATURES ── --}}
    <div class="features">
        <div class="features-grid">
            @foreach([
                ['🌿', 'Garden Setting',    'Lush botanical gardens with panoramic Yatta Plateau views'],
                ['📶', 'High-Speed WiFi',  'Complimentary high-speed internet in all rooms'],
                ['🍽',  'Restaurant & Bar', 'Farm-to-table cuisine and a curated beverage menu'],
                ['🎪', 'Events & Weddings', 'Tailored packages for weddings, conferences and retreats'],
            ] as [$icon, $title, $desc])
            <div class="feature">
                <div class="feature-icon">{{ $icon }}</div>
                <h4>{{ $title }}</h4>
                <p>{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── EVENTS CTA ── --}}
    <section class="events-cta">
        <span class="section-eyebrow" style="color: var(--amber);">Weddings & Events</span>
        <h2>Make Every Moment Unforgettable</h2>
        <p>From intimate garden weddings to corporate retreats — our team crafts every detail around your vision.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('events') }}" class="btn-primary">Explore Packages</a>
            <a href="{{ route('events.inquire') }}" class="btn-outline" onclick="event.preventDefault(); document.getElementById('inquireModal') && document.getElementById('inquireModal').classList.remove('hidden')">
                Send an Enquiry
            </a>
        </div>
    </section>

    {{-- ── DAY TICKETS CTA ── --}}
    <section class="tickets-cta">
        <div class="tickets-text">
            <h2>Day Activities & Gate Tickets</h2>
            <p>Experience the resort for the day — gardens, pool, and dining. Book your gate ticket online and skip the queue.</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn-primary" style="background:var(--forest);">
            Buy Day Ticket
        </a>
    </section>
    {{--
    ══════════════════════════════════════════════════════
    ADD THIS SECTION TO home.blade.php
    Paste it just before the closing @endsection tag
    ══════════════════════════════════════════════════════
--}}

{{-- ── Menu QR Section ──────────────────────────────────────── --}}
<section style="background:var(--forest);padding:5rem 0;">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;max-width:900px;margin:0 auto;">

            {{-- Text side --}}
            <div style="color:white;">
                <span style="font-size:0.7rem;font-weight:700;letter-spacing:0.25em;text-transform:uppercase;color:var(--amber);display:block;margin-bottom:0.75rem;">
                    Scan & Explore
                </span>
                <h2 style="font-family:'Playfair Display',serif;font-size:clamp(1.75rem,3vw,2.5rem);font-weight:400;margin-bottom:1rem;">
                    Our Food & Drinks Menu
                </h2>
                <p style="color:rgba(255,255,255,0.7);line-height:1.8;margin-bottom:1.5rem;">
                    Scan the QR code with your phone to browse our full menu — from breakfast to cocktails, all crafted with fresh local ingredients.
                </p>
                <a href="{{ route('menu.mobile') }}"
                   style="display:inline-flex;align-items:center;gap:0.5rem;background:var(--gold);color:white;padding:0.75rem 1.75rem;border-radius:8px;font-size:0.85rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;text-decoration:none;transition:background 0.2s;">
                    View Full Menu →
                </a>
            </div>

            {{-- QR side --}}
            <div style="display:flex;flex-direction:column;align-items:center;gap:1rem;">
                <div style="background:white;padding:1.25rem;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.2);">
                    {{-- QR code generated via Google Charts API --}}
                    @php
                        $menuUrl = url('/menu');
                        $qrUrl   = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($menuUrl) . '&bgcolor=ffffff&color=1e3a2f&margin=10';
                    @endphp
                    <img src="{{ $qrUrl }}"
                         alt="Scan for menu"
                         width="200" height="200"
                         style="display:block;border-radius:8px;">
                </div>
                <p style="color:rgba(255,255,255,0.6);font-size:0.8rem;text-align:center;">
                    Point your camera at the code<br>to open the menu on your phone
                </p>
            </div>

        </div>
    </div>
</section>

@push('styles')
<style>
@media(max-width:640px) {
    /* Stack QR section vertically on mobile */
    #qr-section-grid { grid-template-columns: 1fr !important; }
}
</style>
@endpush
@endsection

@push('scripts')
<script>
    // Sync check-out min date with check-in
    const checkIn  = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    if (checkIn && checkOut) {
        checkIn.addEventListener('change', function () {
            const next = new Date(this.value);
            next.setDate(next.getDate() + 1);
            checkOut.min   = next.toISOString().split('T')[0];
            checkOut.value = next.toISOString().split('T')[0];
        });
    }
</script>
@endpush