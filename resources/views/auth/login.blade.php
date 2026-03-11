<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login — Kitonga Garden Resort</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --forest:#1e3a2f; --moss:#2e5c42; --fern:#4a8060; --gold:#c8974a; --amber:#e4b36a; }
        body { font-family:'Jost',sans-serif; min-height:100vh; background:var(--forest); display:flex; align-items:center; justify-content:center; padding:1.5rem; }
        body::before { content:''; position:fixed; inset:0; background:radial-gradient(circle at 20% 20%,rgba(122,170,138,0.15) 0%,transparent 50%),radial-gradient(circle at 80% 80%,rgba(200,151,74,0.1) 0%,transparent 50%); pointer-events:none; }
        .login-card { background:white; border-radius:20px; width:100%; max-width:420px; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,0.3); }
        .card-header { background:linear-gradient(135deg,var(--forest),var(--moss)); padding:2.5rem 2rem; text-align:center; color:white; }
        .card-header .logo { font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:500; margin-bottom:0.25rem; }
        .card-header .subtitle { font-size:0.78rem; color:rgba(255,255,255,0.6); letter-spacing:0.15em; text-transform:uppercase; }
        .card-header .badge { display:inline-block; background:rgba(200,151,74,0.2); border:1px solid rgba(200,151,74,0.4); color:var(--amber); font-size:0.7rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; padding:0.3rem 0.9rem; border-radius:20px; margin-top:1rem; }
        .card-body { padding:2rem; }
        .form-group { margin-bottom:1.25rem; }
        .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
        .form-group input { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.75rem 1rem; font-size:0.95rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s,box-shadow 0.2s; color:#1c1c18; }
        .form-group input:focus { border-color:var(--fern); box-shadow:0 0 0 3px rgba(74,128,96,0.1); }
        .form-group input.is-error { border-color:#dc2626; }
        .error-msg { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:0.75rem 1rem; border-radius:8px; font-size:0.83rem; margin-bottom:1.25rem; }
        .remember-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
        .remember-row label { display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; color:#6b7280; cursor:pointer; }
        .remember-row input[type=checkbox] { width:16px; height:16px; accent-color:var(--forest); }
        .btn-login { width:100%; background:var(--forest); color:white; border:none; padding:0.9rem; border-radius:10px; font-size:0.95rem; font-weight:700; font-family:'Jost',sans-serif; cursor:pointer; transition:background 0.2s; }
        .btn-login:hover { background:var(--moss); }
        .card-footer { background:#f9fafb; padding:1rem 2rem; text-align:center; border-top:1px solid #f3f4f6; }
        .card-footer a { font-size:0.82rem; color:var(--fern); text-decoration:none; }
        .card-footer a:hover { color:var(--forest); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card-header">
            <div class="logo">🌿 Kitonga Garden Resort</div>
            <div class="subtitle">Ukasi · Kitui County · Kenya</div>
            <div class="badge">Staff Portal</div>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="error-msg">{{ $errors->first() }}</div>
            @endif
            @if(session('status'))
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:0.75rem 1rem;border-radius:8px;font-size:0.83rem;margin-bottom:1.25rem;">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="you@kitongagardenresort.com" class="{{ $errors->has('email') ? 'is-error' : '' }}">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                </div>
                <div class="remember-row">
                    <label><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember me</label>
                </div>
                <button type="submit" class="btn-login">Sign In to Dashboard</button>
            </form>
        </div>
        <div class="card-footer">
            <a href="{{ route('home') }}">← Back to Resort Website</a>
        </div>
    </div>
</body>
</html>