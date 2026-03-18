@extends('layouts.app')

@section('title', 'Contact Us')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .page-hero { margin-top:72px; height:280px; background:linear-gradient(rgba(10,30,20,0.6),rgba(10,30,20,0.6)),linear-gradient(135deg,var(--forest),var(--moss)); display:flex; align-items:center; justify-content:center; text-align:center; color:white; }
    .page-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(2rem,4vw,3rem); font-weight:400; }
    .page-hero p  { color:rgba(255,255,255,0.75); margin-top:0.5rem; }

    .contact-section { background:var(--cream); padding:5rem 0; }
    .contact-grid { display:grid; grid-template-columns:1fr 1fr; gap:3rem; }

    .info-block { display:flex; flex-direction:column; gap:1.5rem; }
    .info-card { background:white; border-radius:14px; padding:1.75rem; box-shadow:0 2px 14px rgba(0,0,0,0.05); }
    .info-card h3 { font-family:'Playfair Display',serif; font-size:1.15rem; color:var(--forest); margin-bottom:1.25rem; }
    .info-row { display:flex; align-items:flex-start; gap:0.75rem; margin-bottom:1rem; }
    .info-row:last-child { margin-bottom:0; }
    .info-icon { font-size:1.2rem; flex-shrink:0; margin-top:0.1rem; }
    .info-label { font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--fern); }
    .info-value { font-size:0.9rem; color:#374151; margin-top:0.15rem; line-height:1.5; }
    .info-value a { color:var(--fern); text-decoration:none; }
    .info-value a:hover { color:var(--forest); }

    .hours-grid { display:grid; grid-template-columns:1fr 1fr; gap:0.4rem; font-size:0.85rem; }
    .hours-day  { color:#6b7280; }
    .hours-time { font-weight:600; color:#374151; }

    .contact-form { background:white; border-radius:16px; padding:2rem; box-shadow:0 2px 16px rgba(0,0,0,0.06); }
    .contact-form h2 { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--forest); margin-bottom:1.5rem; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input, .form-group select, .form-group textarea { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.9rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--fern); }
    .btn-send { background:var(--forest); color:white; border:none; cursor:pointer; padding:0.85rem 2rem; border-radius:8px; font-size:0.875rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-family:'Jost',sans-serif; transition:background 0.2s; }
    .btn-send:hover { background:var(--moss); }
    .success-msg { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:1rem 1.25rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.875rem; }

    .map-section { padding:0 0 5rem; background:var(--cream); }
    .map-embed { border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.1); }

    @media(max-width:900px) { .contact-grid { grid-template-columns:1fr; } .form-row { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

<div class="page-hero">
    <div>
        <h1>Contact Us</h1>
        <p>We'd love to hear from you. Our team responds within 24 hours.</p>
    </div>
</div>

<section class="contact-section">
    <div class="container">
        <div class="contact-grid">

            {{-- Info --}}
            <div class="info-block">
                <div class="info-card">
                    <h3>Get in Touch</h3>
                    <div class="info-row">
                        <span class="info-icon">📍</span>
                        <div><div class="info-label">Address</div><div class="info-value">Thika–Garissa Road<br>Ukasi, Kitui County, Kenya</div></div>
                    </div>
                    <div class="info-row">
                        <span class="info-icon">📞</span>
                        <div><div class="info-label">Phone</div><div class="info-value"><a href="tel:+254113262688">+254 113 262 688</a></div></div>
                    </div>
                    <div class="info-row">
                        <span class="info-icon">✉️</span>
                        <div><div class="info-label">Email</div><div class="info-value"><a href="mailto:info@kitongagardenresort.com">info@kitongagardenresort.com</a></div></div>
                    </div>
                    <div class="info-row">
                        <span class="info-icon">💬</span>
                        <div><div class="info-label">WhatsApp</div><div class="info-value"><a href="https://wa.me/254113262688" target="_blank">Chat with us</a></div></div>
                    </div>
                </div>

                <div class="info-card">
                    <h3>Reception Hours</h3>
                    <div class="hours-grid">
                        @foreach(['Monday–Friday'=>'7:00 AM – 10:00 PM','Saturday'=>'7:00 AM – 11:00 PM','Sunday'=>'8:00 AM – 10:00 PM','Public Holidays'=>'9:00 AM – 9:00 PM'] as $day => $time)
                            <div class="hours-day">{{ $day }}</div>
                            <div class="hours-time">{{ $time }}</div>
                        @endforeach
                    </div>
                </div>

                <div class="info-card">
                    <h3>Find Us on Social</h3>
                    <div style="display:flex;gap:1rem;">
                        <a href="https://facebook.com/profile.php?id=100094895558030" target="_blank" style="flex:1;text-align:center;background:var(--cream);border-radius:8px;padding:0.75rem;font-size:0.8rem;font-weight:600;color:var(--forest);text-decoration:none;">📘 Facebook</a>
                        <a href="https://instagram.com/kitongagardenresort" target="_blank" style="flex:1;text-align:center;background:var(--cream);border-radius:8px;padding:0.75rem;font-size:0.8rem;font-weight:600;color:var(--forest);text-decoration:none;">📸 Instagram</a>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="contact-form">
                <h2>Send a Message</h2>
                @if(session('success'))
                    <div class="success-msg">✓ {{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('contact.submit') }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group"><label>First Name *</label><input type="text" name="first_name" value="{{ old('first_name') }}" required></div>
                        <div class="form-group"><label>Last Name *</label><input type="text" name="last_name" value="{{ old('last_name') }}" required></div>
                    </div>
                    <div class="form-group"><label>Email Address *</label><input type="email" name="email" value="{{ old('email') }}" required></div>
                    <div class="form-group"><label>Phone</label><input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+254 7XX XXX XXX"></div>
                    <div class="form-group">
                        <label>Subject *</label>
                        <select name="subject" required>
                            <option value="">Select a subject…</option>
                            @foreach(['Room Booking Enquiry','Events & Weddings','Restaurant Reservation','Group / Corporate','General Enquiry','Feedback','Other'] as $s)
                                <option value="{{ $s }}" @selected(old('subject') === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Message *</label><textarea name="message" rows="5" required placeholder="How can we help you?">{{ old('message') }}</textarea></div>
                    <button type="submit" class="btn-send">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="map-section">
    <div class="container">
        <div class="map-embed">
            <iframe src="https://maps.google.com/maps?q=Kitonga+Garden+Resort+Ukasi+Kitui&t=&z=13&ie=UTF8&iwloc=&output=embed"
                    width="100%" height="400" style="border:0;" allowfullscreen loading="lazy"></iframe>
        </div>
    </div>
</div>

@endsection