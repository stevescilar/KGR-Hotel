@extends('layouts.app')

@section('title', 'Kitonga Garden Resort — Ukasi, Kitui County')
@section('navbar-class', 'transparent')

@push('styles')
<style>
/* ── Video Hero ───────────────────────────────────────────── */
.video-hero {
    position: relative;
    height: 100vh;
    min-height: 620px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
.video-hero video {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 0;
}
.video-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.2) 0%,
        rgba(0,0,0,0.35) 50%,
        rgba(0,0,0,0.65) 100%
    );
    z-index: 1;
}
.video-hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
    padding: 0 1.5rem;
    max-width: 820px;
}
.hero-welcome {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: var(--amber);
    display: block;
    margin-bottom: 1.25rem;
}
.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.75rem, 7vw, 5rem);
    font-weight: 400;
    line-height: 1.1;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 20px rgba(0,0,0,0.3);
}
.hero-title em {
    font-style: italic;
    color: var(--amber);
}
.hero-subtitle {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: rgba(255,255,255,0.8);
    margin-bottom: 2.5rem;
    font-weight: 300;
    letter-spacing: 0.05em;
}
.hero-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
.btn-hero-primary {
    background: var(--gold);
    color: white;
    padding: 1rem 2.5rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    text-decoration: none;
    transition: background 0.2s, transform 0.2s;
}
.btn-hero-primary:hover { background: #b5863a; transform: translateY(-1px); }
.btn-hero-outline {
    border: 2px solid rgba(255,255,255,0.6);
    color: white;
    padding: 1rem 2.5rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-hero-outline:hover { border-color: white; background: rgba(255,255,255,0.1); }

/* Scroll indicator */
.scroll-down {
    position: absolute;
    bottom: 2.5rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255,255,255,0.5);
    font-size: 0.65rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
}
.scroll-down-line {
    width: 1px;
    height: 50px;
    background: linear-gradient(to bottom, rgba(255,255,255,0.5), transparent);
    animation: scrollLine 2s ease-in-out infinite;
}
@keyframes scrollLine {
    0% { transform: scaleY(0); transform-origin: top; }
    50% { transform: scaleY(1); transform-origin: top; }
    51% { transform: scaleY(1); transform-origin: bottom; }
    100% { transform: scaleY(0); transform-origin: bottom; }
}

/* ── Stats bar ───────────────────────────────────────────── */
.stats-bar {
    background: var(--forest);
    padding: 1.75rem 0;
}
.stats-inner {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    max-width: 900px;
    margin: 0 auto;
    padding: 0 1.5rem;
}
.stat-item {
    text-align: center;
    color: white;
    border-right: 1px solid rgba(255,255,255,0.1);
    padding: 0 1.5rem;
}
.stat-item:last-child { border-right: none; }
.stat-number {
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    font-weight: 400;
    color: var(--amber);
    display: block;
}
.stat-label {
    font-size: 0.7rem;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    letter-spacing: 0.12em;
}

/* ── Section shared styles ───────────────────────────────── */
.section-eyebrow {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--gold);
    display: block;
    margin-bottom: 0.75rem;
}
.section-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.75rem, 3vw, 2.5rem);
    font-weight: 400;
    color: var(--forest);
    margin-bottom: 1rem;
}
.section-sub {
    color: #6b7280;
    max-width: 520px;
    line-height: 1.8;
}

/* ── Rooms preview ───────────────────────────────────────── */
.rooms-section {
    background: var(--cream);
    padding: 6rem 0;
}
.rooms-header {
    text-align: center;
    margin-bottom: 4rem;
}
.rooms-header .section-sub { margin: 0 auto; }
.rooms-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 3rem;
}
.room-card {
    border-radius: 16px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    transition: transform 0.3s, box-shadow 0.3s;
    text-decoration: none;
    color: inherit;
    display: block;
}
.room-card:hover { transform: translateY(-6px); box-shadow: 0 16px 48px rgba(0,0,0,0.12); }
.room-card-img { width: 100%; height: 220px; object-fit: cover; display: block; }
.room-card-img-placeholder { width: 100%; height: 220px; background: linear-gradient(135deg, var(--forest), var(--moss)); display: flex; align-items: center; justify-content: center; font-size: 3.5rem; }
.room-card-body { padding: 1.5rem; }
.room-card-name { font-family: 'Playfair Display', serif; font-size: 1.15rem; color: var(--forest); margin-bottom: 0.4rem; }
.room-card-meta { font-size: 0.8rem; color: #6b7280; margin-bottom: 0.75rem; }
.room-card-price { font-family: 'Playfair Display', serif; font-size: 1.25rem; color: var(--forest); }
.room-card-price span { font-size: 0.78rem; color: #9ca3af; font-family: 'Jost', sans-serif; }
.rooms-cta { text-align: center; }
.btn-outline-forest {
    display: inline-block;
    border: 2px solid var(--forest);
    color: var(--forest);
    padding: 0.85rem 2.5rem;
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-outline-forest:hover { background: var(--forest); color: white; }

/* ── Experience split section ────────────────────────────── */
.experience-section {
    background: white;
    padding: 6rem 0;
}
.experience-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5rem;
    align-items: center;
}
.experience-images {
    position: relative;
}
.exp-img-main {
    width: 100%;
    height: 480px;
    object-fit: cover;
    border-radius: 16px;
    display: block;
}
.exp-img-accent {
    position: absolute;
    bottom: -2rem;
    right: -2rem;
    width: 200px;
    height: 160px;
    object-fit: cover;
    border-radius: 12px;
    border: 6px solid white;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}
.experience-content { padding-left: 1rem; }
.experience-features { display: flex; flex-direction: column; gap: 1.25rem; margin: 2rem 0 2.5rem; }
.feature { display: flex; align-items: flex-start; gap: 1rem; }
.feature-icon { width: 42px; height: 42px; border-radius: 10px; background: var(--cream); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.feature-text h4 { font-size: 0.9rem; font-weight: 700; color: var(--forest); margin-bottom: 0.2rem; }
.feature-text p { font-size: 0.82rem; color: #6b7280; }

/* ── Events section ──────────────────────────────────────── */
.events-section {
    background: var(--forest);
    padding: 6rem 0;
    color: white;
    position: relative;
    overflow: hidden;
}
.events-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('https://static.wixstatic.com/media/87c8f7_666b501fe504474fa733b932f1118446~mv2.jpg') center/cover;
    opacity: 0.15;
}
.events-inner { position: relative; z-index: 1; }
.events-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}
.events-content .section-title { color: white; }
.events-content .section-sub { color: rgba(255,255,255,0.7); margin-bottom: 2rem; }
.event-types { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 2rem; }
.event-type { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; padding: 0.85rem 1rem; display: flex; align-items: center; gap: 0.6rem; font-size: 0.85rem; color: rgba(255,255,255,0.85); }
.btn-gold { display: inline-block; background: var(--gold); color: white; padding: 0.9rem 2.25rem; border-radius: 6px; font-size: 0.82rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; text-decoration: none; transition: background 0.2s; }
.btn-gold:hover { background: #b5863a; }
.events-image { border-radius: 16px; overflow: hidden; }
.events-image img { width: 100%; height: 400px; object-fit: cover; display: block; }

/* ── Menu QR section ─────────────────────────────────────── */
.qr-section { background: var(--cream); padding: 5rem 0; }
.qr-grid { display: grid; grid-template-columns: 1fr auto; gap: 4rem; align-items: center; max-width: 800px; margin: 0 auto; }
.qr-box { background: white; padding: 1.25rem; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); flex-shrink: 0; }
.qr-box img { display: block; border-radius: 8px; }
.btn-forest { display: inline-block; background: var(--forest); color: white; padding: 0.85rem 2rem; border-radius: 6px; font-size: 0.82rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; text-decoration: none; transition: background 0.2s; margin-top: 1.5rem; }
.btn-forest:hover { background: var(--moss); }

/* ── Location section ────────────────────────────────────── */
.location-section { background: white; padding: 6rem 0; }
.location-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; }
.location-map { border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.1); }
.location-map iframe { display: block; }
.location-details { display: flex; flex-direction: column; gap: 1.5rem; margin-top: 2rem; }
.location-item { display: flex; gap: 1rem; align-items: flex-start; }
.location-icon { font-size: 1.4rem; flex-shrink: 0; margin-top: 0.1rem; }
.location-text .label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--fern); margin-bottom: 0.25rem; }
.location-text .value { color: #374151; font-size: 0.9rem; line-height: 1.6; }
.location-text a { color: var(--fern); text-decoration: none; }

/* Responsive */
@media(max-width:1024px) { .rooms-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:900px) {
    .stats-inner { grid-template-columns: repeat(2,1fr); gap: 1.5rem; }
    .stat-item { border-right: none; padding: 0; }
    .experience-grid, .events-grid, .location-grid { grid-template-columns: 1fr; }
    .experience-images { margin-bottom: 3rem; }
    .exp-img-accent { display: none; }
    .qr-grid { grid-template-columns: 1fr; text-align: center; }
    .qr-box { margin: 0 auto; }
}
@media(max-width:640px) {
    .rooms-grid { grid-template-columns: 1fr; }
    .hero-actions { flex-direction: column; align-items: center; }
    .event-types { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

{{-- ── VIDEO HERO ─────────────────────────────────────────── --}}
<div class="video-hero">
    <video autoplay muted loop playsinline
           poster="https://static.wixstatic.com/media/87c8f7_942a7b5568c544bd88a0356c938d06a6~mv2.jpg/v1/fill/w_1920,h_960,al_c,q_85/87c8f7_942a7b5568c544bd88a0356c938d06a6~mv2.jpg">
        <source src="https://static.wixstatic.com/videos/9c608a_ae86c2c11ea7487b9e59d4e6e1f37bf5/file/mp4/720p/vid.mp4" type="video/mp4">
    </video>
    <div class="video-hero-overlay"></div>

    <div class="video-hero-content">
        <span class="hero-welcome">Welcome to</span>
        <h1 class="hero-title">
            Kitonga Garden<br>
            <em>Resort</em>
        </h1>
        <p class="hero-subtitle">Your luxurious home away from home · Ukasi, Kitui County</p>
        <div class="hero-actions">
            <a href="{{ route('booking.index') }}" class="btn-hero-primary">Book a Room</a>
            <a href="{{ route('rooms') }}" class="btn-hero-outline">Explore Rooms</a>
        </div>
    </div>

    <div class="scroll-down">
        <span>Scroll</span>
        <div class="scroll-down-line"></div>
    </div>
</div>

{{-- ── STATS BAR ───────────────────────────────────────────── --}}
<div class="stats-bar">
    <div class="stats-inner">
        @foreach([['5', 'Room Types'],['🌿', 'Garden Acres'],['📍', 'Ukasi, Kitui'],['⭐', 'Resort Experience']] as [$num, $label])
        <div class="stat-item">
            <span class="stat-number">{{ $num }}</span>
            <span class="stat-label">{{ $label }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- ── ROOMS PREVIEW ───────────────────────────────────────── --}}
<section class="rooms-section">
    <div class="container">
        <div class="rooms-header">
            <span class="section-eyebrow">Accommodation</span>
            <h2 class="section-title">Rooms & Suites</h2>
            <p class="section-sub">From garden-view standard rooms to presidential suites — each designed for comfort and elegance.</p>
        </div>

        @php
        $fallbackImages = [
            'standard'            => 'https://static.wixstatic.com/media/87c8f7_93a0afa668464496a6f8636744e86fc4~mv2.jpg/v1/fill/w_600,h_400,al_c,q_85/87c8f7_93a0afa668464496a6f8636744e86fc4~mv2.jpg',
            'deluxe'              => 'https://static.wixstatic.com/media/87c8f7_a8fdf9b4baa3478d8bcbf501772e7b9d~mv2.jpg/v1/fill/w_600,h_400,al_c,q_85/87c8f7_a8fdf9b4baa3478d8bcbf501772e7b9d~mv2.jpg',
            'penthouse'           => 'https://static.wixstatic.com/media/87c8f7_ae6b2ce224f54eff8127c7e9fe0e7266~mv2.jpg/v1/fill/w_600,h_400,al_c,q_80/87c8f7_ae6b2ce224f54eff8127c7e9fe0e7266~mv2.jpg',
            'presidential-family' => 'https://static.wixstatic.com/media/87c8f7_953ad41ed0c1461d89a835b7c51270e2~mv2.jpg/v1/fill/w_600,h_400,al_c,q_85/87c8f7_953ad41ed0c1461d89a835b7c51270e2~mv2.jpg',
            'royal-presidential'  => 'https://static.wixstatic.com/media/87c8f7_f9c30fa9dbce489b9b7b7920acb5a8a9~mv2.jpg/v1/fill/w_600,h_400,al_c,q_85/87c8f7_f9c30fa9dbce489b9b7b7920acb5a8a9~mv2.jpg',
        ];
        @endphp

        <div class="rooms-grid">
            @forelse($roomTypes as $type)
            <a href="{{ route('booking.index') }}?room_type={{ $type->slug }}&check_in={{ today()->toDateString() }}&check_out={{ today()->addDay()->toDateString() }}"
               class="room-card">
                @php $img = $fallbackImages[$type->slug] ?? null; @endphp
                @if($img)
                    <img src="{{ $img }}" alt="{{ $type->name }}" class="room-card-img" loading="lazy">
                @else
                    <div class="room-card-img-placeholder">🛏</div>
                @endif
                <div class="room-card-body">
                    <div class="room-card-name">{{ $type->name }}</div>
                    <div class="room-card-meta">👥 Up to {{ $type->max_adults }} adults@if($type->max_children) + {{ $type->max_children }} children@endif</div>
                    <div class="room-card-price">
                        KES {{ number_format($type->base_price) }}
                        <span>/ night</span>
                    </div>
                </div>
            </a>
            @empty
            <div style="grid-column:1/-1;text-align:center;color:#9ca3af;padding:3rem;">Rooms coming soon.</div>
            @endforelse
        </div>

        <div class="rooms-cta">
            <a href="{{ route('rooms') }}" class="btn-outline-forest">View All Rooms & Suites</a>
        </div>
    </div>
</section>

{{-- ── EXPERIENCE SECTION ──────────────────────────────────── --}}
<section class="experience-section">
    <div class="container">
        <div class="experience-grid">
            <div class="experience-images">
                <img src="https://static.wixstatic.com/media/9c608a_2f0a80ebe92f4657852b48447144c505.jpg/v1/fill/w_980,h_654,al_c,q_85/9c608a_2f0a80ebe92f4657852b48447144c505.jpg"
                     alt="KGR Gardens" class="exp-img-main" loading="lazy">
                <img src="https://static.wixstatic.com/media/87c8f7_c10f97cf44c24fb8a5cab4df2e8b1226~mv2.jpg/v1/fill/w_400,h_300,al_c,q_85/87c8f7_c10f97cf44c24fb8a5cab4df2e8b1226~mv2.jpg"
                     alt="KGR Food" class="exp-img-accent" loading="lazy">
            </div>
            <div class="experience-content">
                <span class="section-eyebrow">The KGR Experience</span>
                <h2 class="section-title">More than just a stay</h2>
                <p class="section-sub">Nestled in the heart of Ukasi, Kitonga Garden Resort offers a sanctuary where nature and luxury meet.</p>
                <div class="experience-features">
                    @foreach([
                        ['🌿', 'Lush Gardens', 'Acres of manicured gardens with indigenous flora and scenic viewpoints'],
                        ['🍽', 'Farm-to-Table Dining', 'Fresh local cuisine crafted daily by our culinary team'],
                        ['🎪', 'Events & Weddings', 'Tailored packages for weddings, corporate events, and celebrations'],
                        ['🎫', 'Day Visits', 'Enjoy the resort for the day — gardens, pool, and dining'],
                    ] as [$icon, $title, $desc])
                    <div class="feature">
                        <div class="feature-icon">{{ $icon }}</div>
                        <div class="feature-text">
                            <h4>{{ $title }}</h4>
                            <p>{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('tickets.index') }}" class="btn-outline-forest">Day Activities & Tickets</a>
            </div>
        </div>
    </div>
</section>

{{-- ── EVENTS ──────────────────────────────────────────────── --}}
<section class="events-section">
    <div class="container events-inner">
        <div class="events-grid">
            <div class="events-content">
                <span class="section-eyebrow" style="color:var(--amber);">Weddings & Events</span>
                <h2 class="section-title">Create unforgettable memories</h2>
                <p class="section-sub">From intimate garden weddings to grand corporate galas — our team handles every detail.</p>
                <div class="event-types">
                    @foreach(['💒 Weddings', '🎂 Birthdays', '🏢 Corporate', '🎓 Graduations', '💑 Anniversaries', '🎉 Private Parties'] as $type)
                    <div class="event-type">{{ $type }}</div>
                    @endforeach
                </div>
                <a href="{{ route('events') }}" class="btn-gold">Explore Event Packages</a>
            </div>
            <div class="events-image">
                <img src="https://static.wixstatic.com/media/87c8f7_666b501fe504474fa733b932f1118446~mv2.jpg/v1/fill/w_712,h_520,al_c,q_85/87c8f7_666b501fe504474fa733b932f1118446~mv2.jpg"
                     alt="Events at KGR" loading="lazy">
            </div>
        </div>
    </div>
</section>

{{-- ── MENU QR ─────────────────────────────────────────────── --}}
<section class="qr-section">
    <div class="container">
        <div class="qr-grid">
            <div>
                <span class="section-eyebrow">Food & Drinks</span>
                <h2 class="section-title">Farm-to-table cuisine</h2>
                <p class="section-sub">Scan the QR code with your phone to browse our full menu — from breakfast to sundowner cocktails, crafted with fresh local ingredients.</p>
                <a href="{{ route('menu') }}" class="btn-forest">View Full Menu</a>
            </div>
            <div class="qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode(url('/menu')) }}&bgcolor=ffffff&color=1e3a2f&margin=10"
                     alt="Menu QR Code" width="160" height="160">
            </div>
        </div>
    </div>
</section>

{{-- ── LOCATION ────────────────────────────────────────────── --}}
<section class="location-section">
    <div class="container">
        <div class="location-grid">
            <div class="location-map">
                <iframe
                    src="https://maps.google.com/maps?q=Kitonga+Garden+Resort+Ukasi+Kitui&t=&z=13&ie=UTF8&iwloc=&output=embed"
                    width="100%" height="380" style="border:0;" allowfullscreen loading="lazy">
                </iframe>
            </div>
            <div>
                <span class="section-eyebrow">Find Us</span>
                <h2 class="section-title">Getting to KGR</h2>
                <p class="section-sub">Located along the Thika–Garissa Road in Ukasi, easily accessible from Nairobi.</p>
                <div class="location-details">
                    <div class="location-item">
                        <span class="location-icon">📍</span>
                        <div class="location-text">
                            <div class="label">Address</div>
                            <div class="value">Thika–Garissa Road, Ukasi<br>Kitui County, Kenya</div>
                        </div>
                    </div>
                    <div class="location-item">
                        <span class="location-icon">📞</span>
                        <div class="location-text">
                            <div class="label">Phone</div>
                            <div class="value"><a href="tel:+254113262688">+254 113 262 688</a></div>
                        </div>
                    </div>
                    <div class="location-item">
                        <span class="location-icon">✉️</span>
                        <div class="location-text">
                            <div class="label">Email</div>
                            <div class="value"><a href="mailto:info@kitongagardenresort.com">info@kitongagardenresort.com</a></div>
                        </div>
                    </div>
                    <div class="location-item">
                        <span class="location-icon">🕐</span>
                        <div class="location-text">
                            <div class="label">Reception Hours</div>
                            <div class="value">Mon–Fri: 7am–10pm<br>Sat–Sun: 7am–11pm</div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('contact') }}" class="btn-forest" style="margin-top:1.5rem;">Contact Us</a>
            </div>
        </div>
    </div>
</section>

@endsection