@extends('layouts.app')

@section('title', 'Events & Weddings')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .page-hero {
        margin-top:72px; height:420px;
        background:linear-gradient(rgba(10,30,20,0.45),rgba(10,30,20,0.65)),
                   url('https://static.wixstatic.com/media/87c8f7_666b501fe504474fa733b932f1118446~mv2.jpg') center/cover;
        display:flex; align-items:center; justify-content:center; text-align:center; color:white;
    }
    .page-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:400; }
    .page-hero p  { color:rgba(255,255,255,0.75); margin-top:0.75rem; max-width:540px; font-size:1.05rem; }
    .hero-cta { display:inline-block; margin-top:1.5rem; background:var(--gold); color:white; padding:0.85rem 2.25rem; border-radius:6px; font-size:0.8rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; text-decoration:none; transition:background 0.2s; }
    .hero-cta:hover { background:#b5863a; }

    /* Packages */
    .packages-section { background:var(--cream); padding:5rem 0; }
    .section-header { text-align:center; margin-bottom:3.5rem; }
    .section-eyebrow { font-size:0.7rem; font-weight:700; letter-spacing:0.25em; text-transform:uppercase; color:var(--gold); display:block; margin-bottom:0.75rem; }
    .section-header h2 { font-family:'Playfair Display',serif; font-size:clamp(1.75rem,3vw,2.5rem); color:var(--forest); }
    .section-header p  { color:#6b7280; margin-top:0.75rem; max-width:520px; margin-left:auto; margin-right:auto; }

    .packages-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:1.75rem; margin-bottom:5rem; }
    .package-card {
        background:white; border-radius:16px; overflow:hidden;
        box-shadow:0 2px 16px rgba(0,0,0,0.06);
        transition:transform 0.3s, box-shadow 0.3s;
        display:flex; flex-direction:column;
    }
    .package-card:hover { transform:translateY(-4px); box-shadow:0 12px 40px rgba(0,0,0,0.1); }
    .package-img { width:100%; height:220px; object-fit:cover; display:block; }
    .package-img-placeholder { width:100%; height:220px; background:linear-gradient(135deg,var(--forest),var(--moss)); display:flex; align-items:center; justify-content:center; font-size:4rem; }
    .package-body { padding:1.75rem; flex:1; display:flex; flex-direction:column; }
    .package-name { font-family:'Playfair Display',serif; font-size:1.3rem; color:var(--forest); margin-bottom:0.5rem; }
    .package-desc { font-size:0.85rem; color:#6b7280; line-height:1.7; margin-bottom:1.25rem; flex:1; }
    .package-meta { display:flex; gap:1.25rem; margin-bottom:1.25rem; }
    .package-meta span { font-size:0.8rem; color:#6b7280; }
    .package-price { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--forest); margin-bottom:1.25rem; }
    .package-price span { font-size:0.8rem; color:#9ca3af; font-family:'Jost',sans-serif; }
    @if(isset($inclusions))
    .package-includes { list-style:none; margin-bottom:1.5rem; }
    .package-includes li { font-size:0.82rem; color:#4b5563; padding:0.3rem 0; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:0.5rem; }
    .package-includes li:last-child { border:none; }
    @endif
    .btn-inquire { display:block; text-align:center; background:var(--forest); color:white; padding:0.75rem; border-radius:8px; font-size:0.78rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; transition:background 0.2s; }
    .btn-inquire:hover { background:var(--moss); }

    /* Inquiry form */
    .inquiry-section { background:var(--forest); padding:5rem 0; }
    .inquiry-section .section-header h2 { color:white; }
    .inquiry-section .section-header p  { color:rgba(255,255,255,0.65); }
    .inquiry-form { background:white; border-radius:20px; padding:2.5rem; max-width:700px; margin:0 auto; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
    .form-group { display:flex; flex-direction:column; }
    .form-group.full { grid-column:1/-1; }
    .form-group label { font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input, .form-group select, .form-group textarea { border:1.5px solid #e5e7eb; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.9rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--fern); }
    .btn-submit { width:100%; background:var(--forest); color:white; border:none; cursor:pointer; padding:1rem; border-radius:10px; font-size:0.9rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-family:'Jost',sans-serif; margin-top:0.5rem; transition:background 0.2s; }
    .btn-submit:hover { background:var(--moss); }
    .success-msg { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:1rem 1.25rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.875rem; }

    /* Event types we host */
    .types-section { background:white; padding:5rem 0; }
    .types-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:1.5rem; margin-top:3rem; }
    .type-card { background:var(--cream); border-radius:12px; padding:2rem 1rem; text-align:center; }
    .type-card .icon { font-size:2.5rem; margin-bottom:0.75rem; }
    .type-card h4 { font-size:0.9rem; font-weight:700; color:var(--forest); }

    @media(max-width:640px) { .form-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

<div class="page-hero">
    <div>
        <h1>Events & Weddings</h1>
        <p>Create unforgettable memories in our lush garden setting, with tailored packages for every occasion</p>
        <a href="#inquire" class="hero-cta">Send an Enquiry</a>
    </div>
</div>

{{-- Event types --}}
<section class="types-section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">What We Host</span>
            <h2>Every Occasion, Perfectly Crafted</h2>
        </div>
        <div class="types-grid">
            @foreach([['💒','Weddings'],['🎂','Birthdays'],['🏢','Corporate'],['🎓','Graduations'],['💼','Team Building'],['🎉','Private Parties'],['💑','Anniversaries'],['🍽','Private Dining']] as [$icon,$name])
            <div class="type-card"><div class="icon">{{ $icon }}</div><h4>{{ $name }}</h4></div>
            @endforeach
        </div>
    </div>
</section>

{{-- Packages from DB --}}
<section class="packages-section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Our Packages</span>
            <h2>Find Your Perfect Package</h2>
            <p>From intimate gatherings to grand celebrations, each package is fully customisable.</p>
        </div>

        @if($packages->isNotEmpty())
        <div class="packages-grid">
            @foreach($packages as $package)
            <div class="package-card">
                @if($package->image)
                    <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}" class="package-img" loading="lazy">
                @else
                    <div class="package-img-placeholder">🎪</div>
                @endif
                <div class="package-body">
                    <h3 class="package-name">{{ $package->name }}</h3>
                    @if($package->description)
                        <p class="package-desc">{{ $package->description }}</p>
                    @endif
                    <div class="package-meta">
                        <span>👥 {{ $package->min_guests }}–{{ $package->max_guests }} guests</span>
                    </div>
                    <div class="package-price">
                        KES {{ number_format($package->starting_price) }}
                        <span>starting from</span>
                    </div>
                    @if(!empty($package->inclusions) && count($package->inclusions))
                    <ul class="package-includes">
                        @foreach(array_slice($package->inclusions, 0, 5) as $item)
                            <li>✓ {{ $item }}</li>
                        @endforeach
                    </ul>
                    @endif
                    <a href="#inquire" class="btn-inquire">Get a Quote</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- Inquiry Form --}}
<section class="inquiry-section" id="inquire">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow" style="color:var(--amber);">Get In Touch</span>
            <h2>Send an Enquiry</h2>
            <p>Tell us about your event and our team will get back to you within 24 hours.</p>
        </div>
        <div class="inquiry-form">
            @if(session('success'))
                <div class="success-msg">✓ {{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('events.inquire') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="contact_name" value="{{ old('contact_name') }}" required placeholder="Jane Mwangi">
                        @error('contact_name')<span style="color:#dc2626;font-size:0.75rem;">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="contact_phone" value="{{ old('contact_phone') }}" required placeholder="+254 7XX XXX XXX">
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email') }}" required placeholder="jane@example.com">
                    </div>
                    <div class="form-group">
                        <label>Event Type *</label>
                        <select name="event_type" required>
                            <option value="">Select event type…</option>
                            @foreach(['Wedding','Birthday Party','Corporate Conference','Team Building','Private Dinner','Anniversary','Graduation','Other'] as $type)
                                <option value="{{ $type }}" @selected(old('event_type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Event Date *</label>
                        <input type="date" name="event_date" value="{{ old('event_date') }}" required min="{{ today()->addDay()->toDateString() }}">
                    </div>
                    <div class="form-group">
                        <label>Expected Guests *</label>
                        <input type="number" name="guest_count" value="{{ old('guest_count') }}" required min="1" placeholder="e.g. 150">
                    </div>
                    @if($packages->isNotEmpty())
                    <div class="form-group">
                        <label>Package (optional)</label>
                        <select name="package_id">
                            <option value="">No preference</option>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}" @selected(old('package_id') == $pkg->id)>{{ $pkg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="form-group full">
                        <label>Special Requirements</label>
                        <textarea name="requirements" rows="4" placeholder="Tell us about your vision, theme, dietary needs, special requests…">{{ old('requirements') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Send Enquiry</button>
            </form>
        </div>
    </div>
</section>

@endsection