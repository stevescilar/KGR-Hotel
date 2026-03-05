<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kitonga Garden Resort') — Ukasi, Kitui County</title>
    <meta name="description" content="@yield('meta_description', 'Kitonga Garden Resort — Your luxurious home away from home in Ukasi, Kitui County, Kenya.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400&family=Jost:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --forest: #1e3a2f;
            --moss:   #2e5c42;
            --fern:   #4a8060;
            --sage:   #7aaa8a;
            --mist:   #b8cebc;
            --cream:  #f7f3ec;
            --warm:   #ede7da;
            --gold:   #c8974a;
            --amber:  #e4b36a;
            --ink:    #1c1c18;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Jost', sans-serif;
            background: #fff;
            color: var(--ink);
            line-height: 1.6;
        }

        .font-display { font-family: 'Playfair Display', serif; }
        .font-mono    { font-family: 'DM Mono', monospace; }

        /* ── NAVBAR ── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem; height: 72px;
            transition: background 0.3s, box-shadow 0.3s;
        }
        .navbar.scrolled {
            background: white;
            box-shadow: 0 1px 20px rgba(0,0,0,0.08);
        }
        .navbar.transparent { background: transparent; }

        .navbar-logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 500;
            color: white;
            text-decoration: none;
            letter-spacing: 0.02em;
            transition: color 0.3s;
        }
        .navbar.scrolled .navbar-logo { color: var(--forest); }

        .navbar-links {
            display: flex; align-items: center; gap: 2rem;
            list-style: none;
        }
        .navbar-links a {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            transition: color 0.2s;
        }
        .navbar.scrolled .navbar-links a { color: var(--forest); }
        .navbar-links a:hover { color: var(--gold); }

        .navbar-book {
            background: var(--gold);
            color: white !important;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.1em;
            transition: background 0.2s !important;
        }
        .navbar-book:hover { background: var(--amber) !important; color: white !important; }

        /* Mobile nav toggle */
        .nav-toggle {
            display: none;
            background: none; border: none; cursor: pointer;
            flex-direction: column; gap: 5px; padding: 4px;
        }
        .nav-toggle span {
            display: block; width: 22px; height: 2px;
            background: white; border-radius: 2px; transition: background 0.3s;
        }
        .navbar.scrolled .nav-toggle span { background: var(--forest); }

        /* ── FOOTER ── */
        .footer {
            background: var(--forest);
            color: rgba(255,255,255,0.7);
            padding: 4rem 2rem 2rem;
        }
        .footer-grid {
            max-width: 1100px; margin: 0 auto;
            display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem; margin-bottom: 3rem;
        }
        .footer-brand { font-family: 'Playfair Display', serif; }
        .footer-brand h3 { color: white; font-size: 1.4rem; margin-bottom: 0.75rem; }
        .footer-brand p { font-size: 0.875rem; line-height: 1.7; }
        .footer h4 {
            color: var(--gold); font-size: 0.7rem; font-weight: 700;
            letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 1rem;
        }
        .footer ul { list-style: none; }
        .footer ul li { margin-bottom: 0.5rem; }
        .footer ul a {
            color: rgba(255,255,255,0.6); text-decoration: none;
            font-size: 0.875rem; transition: color 0.2s;
        }
        .footer ul a:hover { color: white; }
        .footer-bottom {
            max-width: 1100px; margin: 0 auto;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.8rem;
        }
        .footer-social { display: flex; gap: 1rem; }
        .footer-social a {
            color: rgba(255,255,255,0.5); text-decoration: none;
            font-size: 0.8rem; transition: color 0.2s;
        }
        .footer-social a:hover { color: var(--gold); }

        /* ── FLASH MESSAGES ── */
        .flash {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin: 1rem 0;
            font-size: 0.9rem;
        }
        .flash.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .flash.error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        /* ── UTILITIES ── */
        .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; }
        .section { padding: 5rem 0; }

        @media (max-width: 768px) {
            .nav-toggle { display: flex; }
            .navbar-links {
                display: none; position: fixed; top: 72px; left: 0; right: 0;
                background: white; flex-direction: column; align-items: flex-start;
                padding: 1.5rem 2rem; gap: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            }
            .navbar-links.open { display: flex; }
            .navbar-links a { color: var(--forest) !important; font-size: 0.9rem; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
        }

        @media (max-width: 480px) {
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ── NAVBAR ── --}}
    <nav class="navbar @yield('navbar-class', 'transparent')" id="navbar">
        <a href="{{ route('home') }}" class="navbar-logo">🌿 Kitonga Garden</a>

        <button class="nav-toggle" id="navToggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>

        <ul class="navbar-links" id="navLinks">
            <li><a href="{{ route('rooms') }}">Rooms</a></li>
            <li><a href="{{ route('menu') }}">Food & Drinks</a></li>
            <li><a href="{{ route('events') }}">Events</a></li>
            <li><a href="{{ route('tickets.index') }}">Activities</a></li>
            <li><a href="{{ route('careers.index') }}">Careers</a></li>
            <li><a href="{{ route('contact') }}">Contact</a></li>
            <li><a href="{{ route('booking.index') }}" class="navbar-book">Book Now</a></li>
        </ul>
    </nav>

    {{-- ── FLASH MESSAGES ── --}}
    @if(session('success'))
        <div style="position:fixed;top:80px;right:1.5rem;z-index:200;max-width:400px;">
            <div class="flash success">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div style="position:fixed;top:80px;right:1.5rem;z-index:200;max-width:400px;">
            <div class="flash error">{{ session('error') }}</div>
        </div>
    @endif

    {{-- ── PAGE CONTENT ── --}}
    @yield('content')

    {{-- ── FOOTER ── --}}
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3>Kitonga Garden Resort</h3>
                <p>Your luxurious home away from home, nestled in the heart of Ukasi, Kitui County — where the Yatta Plateau meets the sky.</p>
                <p style="margin-top:1rem;font-size:0.8rem;">📍 Thika–Garissa Road, Ukasi, Kitui County</p>
                <p style="font-size:0.8rem;">📞 +254 113 262 688</p>
                <p style="font-size:0.8rem;">✉️ info@kitongagardenresort.com</p>
            </div>
            <div>
                <h4>Stay</h4>
                <ul>
                    <li><a href="{{ route('rooms') }}">Rooms & Suites</a></li>
                    <li><a href="{{ route('booking.index') }}">Book a Room</a></li>
                    <li><a href="{{ route('tickets.index') }}">Day Activities</a></li>
                </ul>
            </div>
            <div>
                <h4>Experience</h4>
                <ul>
                    <li><a href="{{ route('menu') }}">Food & Drinks</a></li>
                    <li><a href="{{ route('events') }}">Events & Weddings</a></li>
                    <li><a href="{{ route('gift-cards.index') }}">Gift Cards</a></li>
                    @auth
                    <li><a href="{{ route('loyalty.index') }}">My Loyalty</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4>Company</h4>
                <ul>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="{{ route('careers.index') }}">Careers</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} Kitonga Garden Resort. All rights reserved.</span>
            <div class="footer-social">
                <a href="https://facebook.com/profile.php?id=100094895558030" target="_blank">Facebook</a>
                <a href="https://instagram.com/kitongagardenresort" target="_blank">Instagram</a>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        const isTransparent = navbar.classList.contains('transparent');
        if (isTransparent) {
            window.addEventListener('scroll', () => {
                navbar.classList.toggle('scrolled', window.scrollY > 50);
                navbar.classList.toggle('transparent', window.scrollY <= 50);
            });
        }

        // Mobile nav toggle
        document.getElementById('navToggle').addEventListener('click', () => {
            document.getElementById('navLinks').classList.toggle('open');
        });
    </script>

    @stack('scripts')
</body>
</html>