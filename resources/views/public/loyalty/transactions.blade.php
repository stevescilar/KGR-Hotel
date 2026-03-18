@extends('layouts.app')
@section('title', 'Points History')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .section { background:var(--cream); padding:5rem 0; margin-top:72px; min-height:calc(100vh - 72px); }
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { font-family:'Playfair Display',serif; font-size:2rem; color:var(--forest); }
    .page-header a { font-size:0.82rem; color:var(--fern); text-decoration:none; font-weight:600; }
    .balance-strip { background:var(--forest); color:white; border-radius:12px; padding:1.25rem 1.75rem; display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
    .balance-strip .pts { font-family:'Playfair Display',serif; font-size:2rem; }
    .balance-strip .lbl { font-size:0.75rem; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:0.1em; }
    .txn-table { background:white; border-radius:14px; box-shadow:0 2px 14px rgba(0,0,0,0.05); overflow:hidden; }
    .txn-table table { width:100%; border-collapse:collapse; font-size:0.875rem; }
    .txn-table thead { background:#f9fafb; }
    .txn-table th { text-align:left; padding:0.85rem 1.25rem; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280; border-bottom:1px solid #f3f4f6; }
    .txn-table td { padding:0.9rem 1.25rem; border-bottom:1px solid #f9fafb; color:#374151; }
    .txn-table tr:last-child td { border:none; }
    .txn-table tr:hover td { background:#fafafa; }
    .pts-earn { color:#16a34a; font-weight:700; } .pts-spend { color:#dc2626; font-weight:700; }
    .pagination-wrap { padding:1rem 1.25rem; border-top:1px solid #f3f4f6; }
</style>
@endpush

@section('content')
<section class="section">
    <div class="container" style="max-width:800px;">
        <div class="page-header">
            <h1>Points History</h1>
            <a href="{{ route('loyalty.index') }}">← Back to dashboard</a>
        </div>
        <div class="balance-strip">
            <div><div class="lbl">Current Balance</div><div class="pts">{{ number_format($guest->loyalty_points) }} pts</div></div>
            <div style="text-align:right;"><div class="lbl">Tier</div><div style="font-size:1rem;font-weight:700;text-transform:capitalize;">{{ $guest->vip_tier ?? 'None' }}</div></div>
        </div>
        <div class="txn-table">
            <table>
                <thead><tr><th>Date</th><th>Description</th><th>Points</th><th>Balance</th></tr></thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr>
                        <td style="color:#9ca3af;white-space:nowrap;">{{ $txn->created_at->format('M j, Y') }}</td>
                        <td>{{ $txn->description }}</td>
                        <td class="{{ $txn->points > 0 ? 'pts-earn' : 'pts-spend' }}">{{ $txn->points > 0 ? '+' : '' }}{{ number_format($txn->points) }}</td>
                        <td style="font-weight:600;">{{ number_format($txn->balance_after ?? 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;padding:3rem;color:#9ca3af;">No transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($transactions->hasPages())
            <div class="pagination-wrap">{{ $transactions->links() }}</div>
            @endif
        </div>
    </div>
</section>
@endsection