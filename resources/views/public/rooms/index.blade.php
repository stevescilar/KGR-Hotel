@extends('layouts.app')

@section('title', 'Rooms & Suites')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    /* ── Hero ── */
    .rooms-hero {
        margin-top: 72px;
        height: 480px;
        background: linear-gradient(rgba(10,25,18,0.45), rgba(10,25,18,0.55)),
                    url('https://static.wixstatic.com/media/87c8f7_942a7b5568c544bd88a0356c938d06a6~mv2.jpg') center/cover no-repeat;
        display: flex; align-items: center; justify-content: center; text-align: center; color: white;
    }
    .rooms-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(2.25rem,5vw,3.5rem); font-weight:400; letter-spacing:0.02em; }
    .rooms-hero p { color:rgba(255,255,255,0.75); margin-top:0.75rem; font-size:1.05rem; max-width:540px; }
    .hero-cta {
        display:inline-block; margin-top:1.75rem;
        background:var(--gold); color:white;
        padding:0.85rem 2.25rem; border-radius:6px;
        font-size:0.8rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase;
        text-decoration:none; transition:background 0.2s;
    }
    .hero-cta:hover { background:#b5863a; }

    /* ── Intro ── */
    .intro-section { background:var(--cream); padding:4rem 0; text-align:center; }
    .intro-section p { color:#4b5563; max-width:720px; margin:0 auto; line-height:1.9; font-size:1rem; }

    /* ── Room rows ── */
    .rooms-section { background:white; padding:0; }
    .room-row {
        display:grid; grid-template-columns:1fr 1fr;
        min-height:520px;
    }
    .room-row:nth-child(even) { direction:rtl; }
    .room-row:nth-child(even) .room-content { direction:ltr; }

    .room-image {
        position:relative; overflow:hidden;
        background:#f3f4f6;
    }
    .room-image img {
        width:100%; height:100%; object-fit:cover;
        transition:transform 0.6s ease;
    }
    .room-row:hover .room-image img { transform:scale(1.03); }

    .room-content {
        padding:4rem 3.5rem;
        display:flex; flex-direction:column; justify-content:center;
        background:white;
    }
    .room-eyebrow {
        font-size:0.65rem; font-weight:700; letter-spacing:0.3em;
        text-transform:uppercase; color:var(--gold); margin-bottom:0.75rem;
    }
    .room-name {
        font-family:'Playfair Display',serif;
        font-size:clamp(1.75rem,2.5vw,2.25rem);
        color:var(--forest); font-weight:400; margin-bottom:1rem;
    }
    .room-meta {
        display:flex; gap:1.5rem; margin-bottom:1.25rem; flex-wrap:wrap;
    }
    .room-meta span {
        font-size:0.82rem; color:#6b7280;
        display:flex; align-items:center; gap:0.35rem;
    }
    .room-desc {
        color:#4b5563; line-height:1.9; font-size:0.92rem;
        margin-bottom:1.75rem;
    }
    .room-amenities {
        display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:2rem;
    }
    .amenity-tag {
        background:var(--cream); color:var(--fern);
        font-size:0.72rem; font-weight:600;
        padding:0.3rem 0.8rem; border-radius:20px;
    }
    .room-price {
        margin-bottom:1.75rem;
    }
    .room-price .from { font-size:0.75rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.1em; }
    .room-price .amount {
        font-family:'Playfair Display',serif;
        font-size:2rem; color:var(--forest); font-weight:400;
    }
    .room-price .per { font-size:0.8rem; color:#9ca3af; }

    .btn-book {
        display:inline-block;
        background:var(--forest); color:white;
        padding:0.85rem 2.25rem; border-radius:6px;
        font-size:0.8rem; font-weight:700; letter-spacing:0.12em;
        text-transform:uppercase; text-decoration:none;
        transition:background 0.2s; align-self:flex-start;
    }
    .btn-book:hover { background:var(--moss); }

    /* ── Divider ── */
    .room-divider { height:1px; background:linear-gradient(to right, transparent, var(--mist), transparent); }

    /* ── CTA bottom ── */
    .cta-section {
        background:var(--forest); padding:5rem 0; text-align:center; color:white;
    }
    .cta-section h2 { font-family:'Playfair Display',serif; font-size:clamp(1.75rem,3vw,2.5rem); font-weight:400; margin-bottom:1rem; }
    .cta-section p { color:rgba(255,255,255,0.7); margin-bottom:2rem; max-width:480px; margin-left:auto; margin-right:auto; }
    .btn-cta {
        display:inline-block; background:var(--gold); color:white;
        padding:1rem 2.5rem; border-radius:6px;
        font-size:0.85rem; font-weight:700; letter-spacing:0.12em;
        text-transform:uppercase; text-decoration:none; transition:background 0.2s;
    }
    .btn-cta:hover { background:#b5863a; }

    @media(max-width:900px) {
        .room-row, .room-row:nth-child(even) { grid-template-columns:1fr; direction:ltr; }
        .room-image { min-height:300px; }
        .room-content { padding:2.5rem 1.75rem; }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="rooms-hero">
    <div>
        <h1>Rooms & Suites</h1>
        <p>Where luxury meets the tranquility of nature — each room a private sanctuary in the gardens</p>
        <a href="{{ route('booking.index') }}" class="hero-cta">Check Availability</a>
    </div>
</div>

{{-- Intro --}}
<section class="intro-section">
    <div class="container">
        <p>
            Welcome to KGR Rooms & Suites, where luxury meets affordability. Our charming cottages,
            each housing thoughtfully designed rooms, along with exclusive presidential and family suites,
            offer a unique blend of comfort and style in the heart of Ukasi, Kitui County.
        </p>
    </div>
</section>

{{-- Room rows --}}
<section class="rooms-section">
    @php
    $roomImages = [
        'standard'           => 'https://static.wixstatic.com/media/87c8f7_93a0afa668464496a6f8636744e86fc4~mv2.jpg/v1/fill/w_886,h_576,al_c,q_85/87c8f7_93a0afa668464496a6f8636744e86fc4~mv2.jpg',
        'deluxe'             => 'https://static.wixstatic.com/media/87c8f7_a8fdf9b4baa3478d8bcbf501772e7b9d~mv2.jpg/v1/fill/w_917,h_576,al_c,q_85/87c8f7_a8fdf9b4baa3478d8bcbf501772e7b9d~mv2.jpg',
        'penthouse'          => 'https://static.wixstatic.com/media/87c8f7_ae6b2ce224f54eff8127c7e9fe0e7266~mv2.jpg/v1/fill/w_576,h_576,al_c,q_80/87c8f7_ae6b2ce224f54eff8127c7e9fe0e7266~mv2.jpg',
        'presidential-family'=> 'https://static.wixstatic.com/media/87c8f7_953ad41ed0c1461d89a835b7c51270e2~mv2.jpg/v1/fill/w_864,h_576,al_c,q_85/87c8f7_953ad41ed0c1461d89a835b7c51270e2~mv2.jpg',
        'royal-presidential' => 'https://static.wixstatic.com/media/87c8f7_f9c30fa9dbce489b9b7b7920acb5a8a9~mv2.jpg/v1/fill/w_905,h_576,al_c,q_85/87c8f7_f9c30fa9dbce489b9b7b7920acb5a8a9~mv2.jpg',
    ];
    @endphp

    @forelse($roomTypes as $i => $type)

    @if($i > 0)<div class="room-divider"></div>@endif

    <div class="room-row">

        {{-- Image --}}
        <div class="room-image">
            @php
                $img = $roomImages[$type->slug] ?? null;
                if (!$img && $type->images && count($type->images)) {
                    $img = Storage::url($type->images[0]);
                }
            @endphp
            @if($img)
                <img src="{{ $img }}" alt="{{ $type->name }}">
            @else
                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--forest),var(--moss));display:flex;align-items:center;justify-content:center;font-size:4rem;">🛏</div>
            @endif
        </div>

        {{-- Content --}}
        <div class="room-content">
            <div class="room-eyebrow">Accommodation</div>
            <h2 class="room-name">{{ $type->name }}</h2>

            <div class="room-meta">
                <span>👥 Accommodates: {{ $type->max_adults }} adult{{ $type->max_adults !== 1 ? 's' : '' }}{{ $type->max_children ? ', ' . $type->max_children . ' child' . ($type->max_children !== 1 ? 'ren' : '') : '' }}</span>
                @if($type->rooms_count ?? 0)
                <span>🏠 {{ $type->rooms_count }} room{{ $type->rooms_count !== 1 ? 's' : '' }} available</span>
                @endif
            </div>

            @if($type->description)
            <p class="room-desc">{{ $type->description }}</p>
            @endif

            @if($type->amenities && count($type->amenities))
            <div class="room-amenities">
                @foreach(array_slice($type->amenities, 0, 6) as $amenity)
                    <span class="amenity-tag">{{ $amenity }}</span>
                @endforeach
            </div>
            @endif

            <div class="room-price">
                <div class="from">From</div>
                <div>
                    <span class="amount">KES {{ number_format($type->base_price) }}</span>
                    <span class="per"> / night</span>
                </div>
            </div>

            <a href="{{ route('booking.index') }}?room_type={{ $type->slug }}" class="btn-book">
                Book Now
            </a>
        </div>

    </div>

    @empty
    <div style="padding:5rem;text-align:center;color:#9ca3af;">
        <div style="font-size:3rem;margin-bottom:1rem;">🛏</div>
        <p>Room types coming soon.</p>
    </div>
    @endforelse
</section>

{{-- Bottom CTA --}}
<section class="cta-section">
    <div class="container">
        <h2>Ready to Experience KGR?</h2>
        <p>Book directly with us for the best rates and personalised service.</p>
        <a href="{{ route('booking.index') }}" class="btn-cta">Check Room Availability</a>
    </div>
</section>

@endsection