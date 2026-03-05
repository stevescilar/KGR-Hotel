@extends('layouts.app')

@section('title', 'Rooms & Suites')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .page-hero {
        background: linear-gradient(to bottom, rgba(10,30,20,0.6), rgba(10,30,20,0.4)),
                    url('https://static.wixstatic.com/media/87c8f7_942a7b5568c544bd88a0356c938d06a6~mv2.jpg') center/cover no-repeat;
        height: 340px; display: flex; align-items: center; justify-content: center;
        text-align: center; color: white; margin-top: 72px;
    }
    .page-hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2rem, 4vw, 3.25rem); font-weight: 400;
    }
    .page-hero p { color: rgba(255,255,255,0.75); margin-top: 0.75rem; font-size: 1rem; }

    .rooms-section { background: var(--cream); padding: 5rem 0; }

    .room-row {
        background: white; border-radius: 16px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.06);
        display: grid; grid-template-columns: 420px 1fr;
        overflow: hidden; margin-bottom: 2rem;
        transition: box-shadow 0.3s;
    }
    .room-row:hover { box-shadow: 0 8px 40px rgba(0,0,0,0.1); }
    .room-row:nth-child(even) { direction: rtl; }
    .room-row:nth-child(even) > * { direction: ltr; }

    .room-image {
        min-height: 280px; background: var(--warm);
        display: flex; align-items: center; justify-content: center;
        font-size: 4rem; background-size: cover; background-position: center;
    }
    .room-content { padding: 2.5rem 3rem; display: flex; flex-direction: column; justify-content: center; }
    .room-badge {
        display: inline-block; font-size: 0.65rem; font-weight: 700;
        letter-spacing: 0.2em; text-transform: uppercase;
        color: var(--gold); margin-bottom: 0.75rem;
    }
    .room-content h2 {
        font-family: 'Playfair Display', serif;
        font-size: 1.75rem; color: var(--forest); margin-bottom: 0.75rem;
    }
    .room-content p { color: #6b7280; font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.5rem; }

    .room-meta { display: flex; gap: 1.5rem; margin-bottom: 1.75rem; flex-wrap: wrap; }
    .room-meta-item { display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; color: #6b7280; }
    .room-meta-item strong { color: var(--forest); }

    .room-amenities { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.75rem; }
    .amenity-tag {
        background: var(--cream); color: var(--fern);
        font-size: 0.75rem; font-weight: 600; padding: 0.3rem 0.75rem;
        border-radius: 20px; border: 1px solid var(--mist);
    }

    .room-footer { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
    .room-price .from { font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
    .room-price .amount {
        font-family: 'Playfair Display', serif;
        font-size: 2rem; color: var(--forest); font-weight: 500;
    }
    .room-price .per { font-size: 0.8rem; color: #9ca3af; }
    .room-actions { display: flex; gap: 0.75rem; }
    .btn-primary {
        background: var(--forest); color: white;
        padding: 0.75rem 1.75rem; border-radius: 8px;
        font-size: 0.8rem; font-weight: 700; letter-spacing: 0.08em;
        text-transform: uppercase; text-decoration: none;
        transition: background 0.2s;
    }
    .btn-primary:hover { background: var(--moss); }
    .btn-ghost {
        border: 2px solid var(--mist); color: var(--fern);
        padding: 0.75rem 1.5rem; border-radius: 8px;
        font-size: 0.8rem; font-weight: 600; text-decoration: none;
        transition: border-color 0.2s, color 0.2s;
    }
    .btn-ghost:hover { border-color: var(--fern); color: var(--forest); }

    @media (max-width: 900px) {
        .room-row, .room-row:nth-child(even) { grid-template-columns: 1fr; direction: ltr; }
        .room-image { min-height: 220px; }
        .room-content { padding: 1.75rem; }
    }
</style>
@endpush

@section('content')

    <div class="page-hero">
        <div>
            <h1>Rooms & Suites</h1>
            <p>Five categories of accommodation, each thoughtfully designed for your comfort</p>
        </div>
    </div>

    <section class="rooms-section">
        <div class="container">

            @forelse($roomTypes as $index => $type)
            <div class="room-row">
                <div class="room-image" style="{{ $type->images ? 'background-image:url('.($type->images[0] ?? '').')' : '' }}">
                    @if(!$type->images)🛏@endif
                </div>
                <div class="room-content">
                    <span class="room-badge">Accommodation</span>
                    <h2>{{ $type->name }}</h2>
                    <p>{{ $type->description ?? 'A beautifully appointed room combining luxury and comfort for a memorable stay at Kitonga Garden Resort.' }}</p>

                    <div class="room-meta">
                        <div class="room-meta-item">
                            👥 <span>Up to <strong>{{ $type->max_adults }} adults</strong></span>
                        </div>
                        @if($type->max_children > 0)
                        <div class="room-meta-item">
                            🧒 <span>Up to <strong>{{ $type->max_children }} children</strong></span>
                        </div>
                        @endif
                        <div class="room-meta-item">
                            🏠 <span><strong>{{ $type->rooms_count }}</strong> room{{ $type->rooms_count !== 1 ? 's' : '' }} available</span>
                        </div>
                    </div>

                    @if($type->amenities)
                    <div class="room-amenities">
                        @foreach(array_slice($type->amenities, 0, 6) as $amenity)
                            <span class="amenity-tag">{{ $amenity }}</span>
                        @endforeach
                    </div>
                    @endif

                    <div class="room-footer">
                        <div class="room-price">
                            <div class="from">From</div>
                            <span class="amount">KES {{ number_format($type->base_price) }}</span>
                            <span class="per"> / night</span>
                        </div>
                        <div class="room-actions">
                            <a href="{{ route('rooms.show', $type->slug) }}" class="btn-ghost">Details</a>
                            <a href="{{ route('booking.index') }}?room_type={{ $type->id }}" class="btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:5rem;color:#9ca3af;">
                <div style="font-size:3rem;margin-bottom:1rem;">🛏</div>
                <p>No rooms available at the moment. Please check back soon.</p>
            </div>
            @endforelse

        </div>
    </section>

@endsection