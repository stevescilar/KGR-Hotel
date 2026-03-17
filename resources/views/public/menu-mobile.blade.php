<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Menu — Kitonga Garden Resort</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --forest:#1e3a2f; --gold:#c8974a; --cream:#f7f3ec; --fern:#4a8060; }
        body { font-family: 'Jost', sans-serif; background: var(--cream); min-height: 100vh; }

        /* Header */
        .header { background: var(--forest); padding: 1.25rem 1rem 1rem; text-align: center; position: sticky; top: 0; z-index: 10; }
        .header h1 { font-family: 'Playfair Display', serif; color: white; font-size: 1.25rem; font-weight: 400; }
        .header p { color: rgba(255,255,255,0.6); font-size: 0.75rem; margin-top: 0.2rem; }

        /* Category tabs */
        .tabs { display: flex; gap: 0.5rem; overflow-x: auto; padding: 0.75rem 1rem; background: white; border-bottom: 1px solid #e5e7eb; scrollbar-width: none; position: sticky; top: 72px; z-index: 9; }
        .tabs::-webkit-scrollbar { display: none; }
        .tab { flex-shrink: 0; padding: 0.4rem 0.9rem; border-radius: 20px; border: 1.5px solid #e5e7eb; font-size: 0.78rem; font-weight: 600; color: #6b7280; cursor: pointer; transition: all 0.2s; background: white; }
        .tab.active { background: var(--forest); color: white; border-color: var(--forest); }

        /* Category sections */
        .category { padding: 1.5rem 1rem 0; }
        .category-header { margin-bottom: 1rem; }
        .category-header h2 { font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--forest); }
        .category-header p { font-size: 0.78rem; color: #9ca3af; margin-top: 0.2rem; }

        /* Item cards */
        .items { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; }
        .item { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,0.06); display: flex; }
        .item-img { width: 90px; height: 90px; object-fit: cover; flex-shrink: 0; }
        .item-img-placeholder { width: 90px; height: 90px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .item-body { padding: 0.85rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .item-name { font-weight: 600; font-size: 0.9rem; color: #1c1c18; }
        .item-desc { font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem; line-height: 1.4; }
        .item-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 0.5rem; }
        .item-price { font-family: 'Playfair Display', serif; font-size: 1rem; color: var(--forest); font-weight: 500; }
        .item-badge { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.05em; padding: 0.2rem 0.5rem; border-radius: 10px; }
        .badge-featured { background: #fef9c3; color: #92400e; }
        .badge-unavail { background: #f3f4f6; color: #9ca3af; }

        /* Footer */
        .footer { background: var(--forest); color: rgba(255,255,255,0.6); text-align: center; padding: 1.5rem 1rem; font-size: 0.75rem; margin-top: 1rem; }
        .footer strong { color: white; display: block; font-family: 'Playfair Display', serif; font-size: 1rem; margin-bottom: 0.25rem; }
    </style>
</head>
<body>

<div class="header">
    <h1>🌿 Kitonga Garden Resort</h1>
    <p>Food & Drinks Menu</p>
</div>

@if($categories->isNotEmpty())
<div class="tabs" id="tabs">
    @foreach($categories as $i => $cat)
    <button class="tab {{ $i === 0 ? 'active' : '' }}" onclick="scrollToCategory('cat-{{ $cat->id }}', this)">
        {{ $cat->name }}
    </button>
    @endforeach
</div>
@endif

<div id="menuContent">
    @forelse($categories as $cat)
    <div class="category" id="cat-{{ $cat->id }}">
        <div class="category-header">
            <h2>{{ $cat->name }}</h2>
            @if($cat->description)<p>{{ $cat->description }}</p>@endif
        </div>
        <div class="items">
            @forelse($cat->items as $item)
            <div class="item">
                @if($item->image)
                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="item-img">
                @else
                    <div class="item-img-placeholder">🍽</div>
                @endif
                <div class="item-body">
                    <div>
                        <div class="item-name">{{ $item->name }}</div>
                        @if($item->description)
                        <div class="item-desc">{{ Str::limit($item->description, 60) }}</div>
                        @endif
                    </div>
                    <div class="item-footer">
                        <span class="item-price">KES {{ number_format($item->price) }}</span>
                        @if(!$item->is_available)
                            <span class="item-badge badge-unavail">Unavailable</span>
                        @elseif($item->is_featured ?? false)
                            <span class="item-badge badge-featured">⭐ Featured</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <p style="color:#9ca3af;font-size:0.8rem;padding:0.5rem 0;">No items in this category.</p>
            @endforelse
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:4rem 1rem;color:#9ca3af;">
        <div style="font-size:3rem;margin-bottom:1rem;">🍽</div>
        <p>Menu coming soon. Ask our staff for today's specials.</p>
    </div>
    @endforelse
</div>

<div class="footer">
    <strong>Kitonga Garden Resort</strong>
    Ukasi, Kitui County · +254 113 262 688
</div>

<script>
function scrollToCategory(id, tab) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Highlight tab on scroll
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            const id = e.target.id;
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            const match = document.querySelector(`.tab[onclick*="${id}"]`);
            if (match) match.classList.add('active');
        }
    });
}, { threshold: 0.4 });

document.querySelectorAll('.category').forEach(el => observer.observe(el));
</script>
</body>
</html>