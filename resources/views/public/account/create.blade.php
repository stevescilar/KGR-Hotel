@extends('layouts.app')
@section('title', 'Create Your Account')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .account-section { background:var(--cream); padding:5rem 0; margin-top:72px; min-height:calc(100vh - 72px); display:flex; align-items:center; }
    .account-card { background:white; border-radius:20px; box-shadow:0 4px 40px rgba(0,0,0,0.08); max-width:480px; margin:0 auto; overflow:hidden; width:100%; }
    .card-header { background:linear-gradient(135deg,var(--forest),var(--moss)); padding:2.5rem 2rem; text-align:center; color:white; }
    .card-header .icon { font-size:2.5rem; margin-bottom:0.75rem; }
    .card-header h1 { font-family:'Playfair Display',serif; font-size:1.6rem; font-weight:400; margin-bottom:0.4rem; }
    .card-header p { color:rgba(255,255,255,0.75); font-size:0.9rem; }
    .card-body { padding:2rem; }
    .guest-summary { background:var(--cream); border-radius:10px; padding:1rem 1.25rem; margin-bottom:1.75rem; display:flex; align-items:center; gap:0.75rem; }
    .guest-avatar { width:40px; height:40px; border-radius:50%; background:var(--forest); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1rem; flex-shrink:0; }
    .guest-name { font-weight:600; color:var(--forest); font-size:0.95rem; }
    .guest-email { font-size:0.8rem; color:#6b7280; }
    .form-group { margin-bottom:1.25rem; }
    .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.75rem 1rem; font-size:0.95rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus { border-color:var(--fern); box-shadow:0 0 0 3px rgba(74,128,96,0.08); }
    .password-hint { font-size:0.75rem; color:#9ca3af; margin-top:0.35rem; }
    .btn-create { width:100%; background:var(--forest); color:white; border:none; cursor:pointer; padding:1rem; border-radius:10px; font-size:0.95rem; font-weight:700; letter-spacing:0.05em; font-family:'Jost',sans-serif; transition:background 0.2s; margin-top:0.5rem; }
    .btn-create:hover { background:var(--moss); }
    .skip-link { display:block; text-align:center; margin-top:1rem; font-size:0.82rem; color:#9ca3af; text-decoration:none; }
    .skip-link:hover { color:var(--fern); }
    .error-msg { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:0.75rem 1rem; border-radius:8px; font-size:0.83rem; margin-bottom:1.25rem; }
    .perks { display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.75rem; margin-bottom:1.75rem; }
    .perk { background:var(--cream); border-radius:10px; padding:0.85rem; text-align:center; }
    .perk .icon { font-size:1.4rem; margin-bottom:0.3rem; }
    .perk p { font-size:0.72rem; color:#6b7280; }
</style>
@endpush

@section('content')
<section class="account-section">
    <div class="container">
        <div class="account-card">
            <div class="card-header">
                <div class="icon">🌿</div>
                <h1>You're almost done!</h1>
                <p>Create a password to access your KGR account and loyalty rewards</p>
            </div>

            <div class="card-body">

                {{-- Guest summary --}}
                <div class="guest-summary">
                    <div class="guest-avatar">{{ strtoupper(substr($guest->first_name, 0, 1)) }}</div>
                    <div>
                        <div class="guest-name">{{ $guest->first_name }} {{ $guest->last_name }}</div>
                        <div class="guest-email">{{ $guest->email }}</div>
                    </div>
                </div>

                {{-- Perks --}}
                <div class="perks">
                    <div class="perk"><div class="icon">⭐</div><p>Earn loyalty points</p></div>
                    <div class="perk"><div class="icon">📋</div><p>View booking history</p></div>
                    <div class="perk"><div class="icon">🎁</div><p>Exclusive member offers</p></div>
                </div>

                @if($errors->any())
                    <div class="error-msg">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('account.create.store', $token) }}">
                    @csrf

                    <div class="form-group">
                        <label>Create Password *</label>
                        <input type="password" name="password" required minlength="8"
                               placeholder="Minimum 8 characters" autocomplete="new-password">
                        <p class="password-hint">At least 8 characters. Keep it secure!</p>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                               placeholder="Re-enter your password" autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn-create">Create My Account</button>
                </form>

                <a href="{{ route('home') }}" class="skip-link">Skip for now — I'll create an account later</a>
            </div>
        </div>
    </div>
</section>
@endsection