@extends('layouts.app')

@section('title', 'Food & Drinks')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .page-hero {
        margin-top:72px; height:340px;
        background:linear-gradient(rgba(10,30,20,0.55),rgba(10,30,20,0.6)),
                   url('https://static.wixstatic.com/media/87c8f7_c10f97cf44c24fb8a5cab4df2e8b1226~mv2.jpg') center/cover;
        display:flex; align-items:center; justify-content:center; text-align:center; color:white;
    }
    .page-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(2rem,4vw,3rem); font-weight:400; }
    .page-hero p  { color:rgba(255,255,255,0.75); margin-top:0.5rem; max-width:480px; }

    .menu-section { background:var(--cream); padding:5rem 0; }

    /* Category tabs */
    .category-tabs { display:flex; gap:0.5rem; flex-wrap:wrap; justify-content:center; margin-bottom:3.5rem; }
    .tab-btn {
        padding:0.5rem 1.4rem; border-radius:24px; border:2px solid var(--mist);
        background:white; color:var(--fern); font-size:0.78rem; font-weight:700;
        cursor:pointer; transition:all 0.2s; font-family:'Jost',sans-serif;
        letter-spacing:0.06em; text-transform:uppercase;
    }
    .tab-btn.active, .tab-btn:hover { background:var(--forest); color:white; border-color:var(--forest); }

    /* Category section */
    .menu-category { display:none; }
    .menu-category.active { display:block; }
    .category-header { text-align:center; margin-bottom:2.5rem; }
    .category-header h2 { font-family:'Playfair Display',serif; font-size:1.75rem; color:var(--forest); }
    .category-header p  { color:#6b7280; margin-top:0.4rem; font-size:0.9rem; }

    /* Menu grid */
    .menu-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1.25rem; }

    .menu-item {
        background:white; border-radius:14px; overflow:hidden;
        box-shadow:0 2px 12px rgba(0,0,0,0.05);
        transition:box-shadow 0.2s, transform 0.2s;
        display:flex; flex-direction:column;
    }
    .menu-item:hover { box-shadow:0 6px 24px rgba(0,0,0,0.1); transform:translateY(-2px); }

    .menu-item-img { width:100%; height:180px; object-fit:cover; display:block; }
    .menu-item-img-placeholder {
        width:100%; height:180px;
        background:linear-gradient(135deg, var(--warm), var(--cream));
        display:flex; align-items:center; justify-content:center; font-size:3rem;
    }

    .menu-item-body { padding:1.25rem; flex:1; display:flex; flex-direction:column; justify-content:space-between; }
    .menu-item-header { display:flex; justify-content:space-between; align-items:flex-start; gap:0.75rem; margin-bottom:0.4rem; }
    .menu-item-name  { font-family:'Playfair Display',serif; font-size:1.05rem; color:var(--forest); }
    .menu-item-price { font-weight:700; color:var(--gold); white-space:nowrap; font-size:1rem; }
    .menu-item-desc  { font-size:0.82rem; color:#6b7280; line-height:1.6; margin-bottom:0.75rem; }
    .menu-item-tags  { display:flex; gap:0.4rem; flex-wrap:wrap; }
    .tag { font-size:0.65rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; padding:0.2rem 0.6rem; border-radius:10px; }
    .tag-featured { background:#fef9c3; color:#92400e; }
    .tag-unavail  { background:#f3f4f6; color:#9ca3af; }

    /* QR section */
    .qr-strip { background:var(--forest); padding:3.5rem 0; }
    .qr-inner { display:flex; align-items:center; justify-content:center; gap:3rem; flex-wrap:wrap; color:white; text-align:left; }
    .qr-text h3 { font-family:'Playfair Display',serif; font-size:1.5rem; margin-bottom:0.5rem; }
    .qr-text p  { color:rgba(255,255,255,0.65); font-size:0.9rem; }
    .qr-box { background:white; padding:1rem; border-radius:12px; flex-shrink:0; }
    .qr-box img { display:block; border-radius:6px; }
</style>
@endpush

@section('content')

<div class="page-hero">
    <div>
        <h1>Food & Drinks</h1>
        <p>Farm-to-table cuisine crafted from the freshest local ingredients</p>
    </div>
</div>

<section class="menu-section">
    <div class="container">

        @if($categories->isEmpty())
            <div style="text-align:center;padding:4rem;color:#9ca3af;">
                <div style="font-size:3rem;margin-bottom:1rem;">🍽</div>
                <p>Menu coming soon. Please call us for today's specials.</p>
            </div>
        @else

        {{-- Tabs --}}
        <div class="category-tabs">
            @foreach($categories as $i => $category)
                <button class="tab-btn {{ $i === 0 ? 'active' : '' }}"
                        onclick="showCategory('cat-{{ $category->id }}', this)">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        {{-- Categories --}}
        @foreach($categories as $i => $category)
        <div class="menu-category {{ $i === 0 ? 'active' : '' }}" id="cat-{{ $category->id }}">
            <div class="category-header">
                <h2>{{ $category->name }}</h2>
                @if($category->description ?? false)
                    <p>{{ $category->description }}</p>
                @endif
            </div>

            <div class="menu-grid">
                @forelse($category->items as $item)
                <div class="menu-item">
                    @if($item->image ?? false)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="menu-item-img" loading="lazy">
                    @else
                        <div class="menu-item-img-placeholder">🍽</div>
                    @endif
                    <div class="menu-item-body">
                        <div>
                            <div class="menu-item-header">
                                <span class="menu-item-name">{{ $item->name }}</span>
                                <span class="menu-item-price">KES {{ number_format($item->price) }}</span>
                            </div>
                            @if($item->description ?? false)
                                <p class="menu-item-desc">{{ $item->description }}</p>
                            @endif
                        </div>
                        <div class="menu-item-tags">
                            @if(!($item->is_available ?? true))
                                <span class="tag tag-unavail">Unavailable</span>
                            @elseif($item->is_featured ?? false)
                                <span class="tag tag-featured">⭐ Featured</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <p style="color:#9ca3af;grid-column:1/-1;text-align:center;padding:2rem;">No items in this category yet.</p>
                @endforelse
            </div>
        </div>
        @endforeach

        @endif
    </div>
</section>

{{-- QR Code strip --}}
<div class="qr-strip">
    <div class="container">
        <div class="qr-inner">
            <div class="qr-text">
                <h3>Always have the menu with you</h3>
                <p>Scan the QR code to open this menu on your phone — great for sharing with your group.</p>
            </div>
            <div class="qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(url('/menu')) }}&bgcolor=ffffff&color=1e3a2f&margin=8"
                     alt="Menu QR code" width="120" height="120">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showCategory(id, btn) {
    document.querySelectorAll('.menu-category').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    btn.classList.add('active');
}
</script>
@endpush