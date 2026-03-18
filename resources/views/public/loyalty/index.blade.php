@extends('layouts.app')
@section('title', 'My Loyalty Points')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .loyalty-section { background:var(--cream); padding:5rem 0; margin-top:72px; min-height:calc(100vh - 72px); }
    .loyalty-header { text-align:center; margin-bottom:3rem; }
    .loyalty-header h1 { font-family:'Playfair Display',serif; font-size:clamp(1.75rem,3vw,2.5rem); color:var(--forest); }
    .loyalty-header p { color:#6b7280; margin-top:0.5rem; }
    .points-card { background:linear-gradient(135deg,var(--forest),var(--moss)); border-radius:20px; padding:2.5rem; color:white; margin-bottom:2rem; display:flex; align-items:center; justify-content:space-between; gap:2rem; flex-wrap:wrap; }
    .points-card .label { font-size:0.75rem; font-weight:700; letter-spacing:0.2em; text-transform:uppercase; color:var(--amber); }
    .points-card .amount { font-family:'Playfair Display',serif; font-size:4rem; font-weight:400; line-height:1; }
    .points-card .sub { color:rgba(255,255,255,0.6); font-size:0.85rem; margin-top:0.25rem; }
    .tier-badge { background:rgba(255,255,255,0.15); border-radius:12px; padding:1.25rem 1.75rem; text-align:center; }
    .tier-badge .tier-icon { font-size:2rem; }
    .tier-badge .tier-name { font-size:1rem; font-weight:700; margin-top:0.3rem; }
    .tier-badge .tier-sub { font-size:0.75rem; color:rgba(255,255,255,0.6); }
    .progress-card { background:white; border-radius:14px; padding:1.75rem; box-shadow:0 2px 14px rgba(0,0,0,0.05); margin-bottom:2rem; }
    .progress-card h3 { font-family:'Playfair Display',serif; font-size:1.15rem; color:var(--forest); margin-bottom:1.25rem; }
    .tier-bar-wrap { background:#f3f4f6; border-radius:20px; height:10px; margin:0.5rem 0; }
    .tier-bar { height:10px; border-radius:20px; background:linear-gradient(90deg,var(--forest),var(--sage)); transition:width 0.8s ease; }
    .tier-ticks { display:flex; justify-content:space-between; font-size:0.7rem; margin-top:0.5rem; }
    .tier-tick .name { color:var(--forest); font-weight:700; font-size:0.7rem; }
    .tier-tick .pts { color:#9ca3af; }
    .transactions-card { background:white; border-radius:14px; padding:1.75rem; box-shadow:0 2px 14px rgba(0,0,0,0.05); }
    .transactions-card h3 { font-family:'Playfair Display',serif; font-size:1.15rem; color:var(--forest); margin-bottom:1.25rem; display:flex; justify-content:space-between; align-items:center; }
    .transactions-card h3 a { font-size:0.8rem; font-weight:600; color:var(--fern); text-decoration:none; font-family:'Jost',sans-serif; }
    .txn-row { display:flex; align-items:center; gap:1rem; padding:0.85rem 0; border-bottom:1px solid #f3f4f6; }
    .txn-row:last-child { border:none; }
    .txn-icon { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
    .txn-icon.earn { background:#f0fdf4; } .txn-icon.spend { background:#fef2f2; }
    .txn-info { flex:1; }
    .txn-info .desc { font-size:0.875rem; color:var(--ink); font-weight:500; }
    .txn-info .date { font-size:0.75rem; color:#9ca3af; }
    .txn-pts { font-weight:700; font-size:0.95rem; }
    .txn-pts.earn { color:#16a34a; } .txn-pts.spend { color:#dc2626; }
    .how-it-works { background:white; border-radius:14px; padding:1.75rem; box-shadow:0 2px 14px rgba(0,0,0,0.05); margin-top:1.5rem; }
    .how-it-works h3 { font-family:'Playfair Display',serif; font-size:1.15rem; color:var(--forest); margin-bottom:1.25rem; }
    .how-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
    .how-item { background:var(--cream); border-radius:10px; padding:1.25rem; text-align:center; }
    .how-item .icon { font-size:1.5rem; margin-bottom:0.5rem; }
    .how-item h4 { font-size:0.82rem; font-weight:700; color:var(--forest); margin-bottom:0.25rem; }
    .how-item p { font-size:0.75rem; color:#6b7280; }
    @media(max-width:600px) { .how-grid { grid-template-columns:1fr; } .points-card { flex-direction:column; } }
</style>
@endpush

@section('content')
<section class="loyalty-section">
    <div class="container" style="max-width:800px;">
        <div class="loyalty-header">
            <h1>My Loyalty Points</h1>
            <p>Welcome back, {{ $guest->first_name }}. Here's your rewards overview.</p>
        </div>
        <div class="points-card">
            <div>
                <div class="label">Total Points</div>
                <div class="amount">{{ number_format($guest->loyalty_points) }}</div>
                <div class="sub">≈ KES {{ number_format($guest->loyalty_points * 0.5) }} in rewards value</div>
            </div>
            @php $tier = $guest->vip_tier ?? 'none'; $icons = ['none'=>'🌱','bronze'=>'🥉','silver'=>'🥈','gold'=>'🥇']; @endphp
            <div class="tier-badge">
                <div class="tier-icon">{{ $icons[$tier] ?? '🌱' }}</div>
                <div class="tier-name">{{ ucfirst($tier) }} Member</div>
                <div class="tier-sub">{{ $tier === 'gold' ? 'Top tier!' : 'Keep earning' }}</div>
            </div>
        </div>
        <div class="progress-card">
            <h3>Progress to Next Tier</h3>
            @php $pts = $guest->loyalty_points; $maxPts = 10000; $pct = min(100, ($pts / $maxPts) * 100); @endphp
            <div class="tier-bar-wrap"><div class="tier-bar" style="width:{{ $pct }}%"></div></div>
            <div class="tier-ticks">
                @foreach($tierThresholds as $name => $threshold)
                <div class="tier-tick"><div class="name">{{ ucfirst($name) }}</div><div class="pts">{{ number_format($threshold) }}</div></div>
                @endforeach
            </div>
            @if($tier !== 'gold')
                @php $nextThreshold = collect($tierThresholds)->filter(fn($t) => $t > $pts)->first() @endphp
                @if($nextThreshold)<p style="font-size:0.82rem;color:#6b7280;margin-top:1rem;">Earn <strong>{{ number_format($nextThreshold - $pts) }} more points</strong> to reach the next tier.</p>@endif
            @else
                <p style="font-size:0.82rem;color:#16a34a;margin-top:1rem;font-weight:600;">🥇 You've reached Gold — our highest tier!</p>
            @endif
        </div>
        <div class="transactions-card">
            <h3>Recent Activity <a href="{{ route('loyalty.transactions') }}">View all →</a></h3>
            @forelse($recentTransactions as $txn)
            @php $isEarn = $txn->points > 0 @endphp
            <div class="txn-row">
                <div class="txn-icon {{ $isEarn ? 'earn' : 'spend' }}">{{ $isEarn ? '➕' : '➖' }}</div>
                <div class="txn-info"><div class="desc">{{ $txn->description }}</div><div class="date">{{ $txn->created_at->format('D, M j Y') }}</div></div>
                <div class="txn-pts {{ $isEarn ? 'earn' : 'spend' }}">{{ $isEarn ? '+' : '' }}{{ number_format($txn->points) }} pts</div>
            </div>
            @empty
            <p style="color:#9ca3af;font-size:0.875rem;padding:1rem 0;">No transactions yet. Book a room or visit the resort to start earning!</p>
            @endforelse
        </div>
        <div class="how-it-works">
            <h3>How It Works</h3>
            <div class="how-grid">
                <div class="how-item"><div class="icon">🛏</div><h4>Book & Stay</h4><p>Earn 1 point per KES 100 spent on room bookings</p></div>
                <div class="how-item"><div class="icon">🍽</div><h4>Dine With Us</h4><p>Earn points on restaurant orders and room service</p></div>
                <div class="how-item"><div class="icon">🎁</div><h4>Redeem</h4><p>Use points as credit on your next booking or dining bill</p></div>
            </div>
        </div>
    </div>
</section>
@endsection