@extends('layouts.app')
@section('title', $job->title)
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .page-top { margin-top:72px; background:var(--forest); padding:3rem 0; color:white; }
    .page-top .back { display:inline-flex; align-items:center; gap:0.4rem; color:rgba(255,255,255,0.6); font-size:0.8rem; text-decoration:none; margin-bottom:1rem; transition:color 0.2s; }
    .page-top .back:hover { color:white; }
    .page-top h1 { font-family:'Playfair Display',serif; font-size:clamp(1.75rem,3vw,2.5rem); font-weight:400; margin-bottom:0.75rem; }
    .page-top-meta { display:flex; gap:1.5rem; flex-wrap:wrap; }
    .page-top-meta span { font-size:0.82rem; color:rgba(255,255,255,0.7); }
    .job-section { background:var(--cream); padding:4rem 0; }
    .job-layout { display:grid; grid-template-columns:1fr 380px; gap:3rem; align-items:start; }
    .content-box { background:white; border-radius:14px; padding:2rem; box-shadow:0 2px 14px rgba(0,0,0,0.05); margin-bottom:1.5rem; }
    .content-box h2 { font-family:'Playfair Display',serif; font-size:1.25rem; color:var(--forest); margin-bottom:1rem; }
    .content-box p, .content-box li { font-size:0.9rem; color:#4b5563; line-height:1.8; }
    .content-box ul { padding-left:1.25rem; }
    .content-box ul li { margin-bottom:0.4rem; }
    .apply-card { background:white; border-radius:16px; padding:2rem; box-shadow:0 4px 30px rgba(0,0,0,0.08); position:sticky; top:96px; }
    .apply-card h2 { font-family:'Playfair Display',serif; font-size:1.35rem; color:var(--forest); margin-bottom:1.5rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.7rem; font-weight:700; color:var(--fern); letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.4rem; }
    .form-group input, .form-group textarea { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.9rem; font-family:'Jost',sans-serif; outline:none; transition:border-color 0.2s; }
    .form-group input:focus, .form-group textarea:focus { border-color:var(--fern); }
    .file-hint { font-size:0.72rem; color:#9ca3af; margin-top:0.3rem; }
    .btn-apply { width:100%; background:var(--forest); color:white; border:none; cursor:pointer; padding:1rem; border-radius:10px; font-size:0.9rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; font-family:'Jost',sans-serif; margin-top:0.5rem; transition:background 0.2s; }
    .btn-apply:hover { background:var(--moss); }
    .success-msg { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:1rem 1.25rem; border-radius:8px; margin-bottom:1.5rem; font-size:0.875rem; }
    .error-msg { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:0.7rem 1rem; border-radius:6px; font-size:0.8rem; margin-top:0.25rem; }
    @media(max-width:900px) { .job-layout { grid-template-columns:1fr; } .apply-card { position:static; } }
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <a href="{{ route('careers.index') }}" class="back">← Back to all jobs</a>
        <h1>{{ $job->title }}</h1>
        <div class="page-top-meta">
            @if($job->department)<span>🏢 {{ $job->department }}</span>@endif
            <span>📍 {{ $job->location ?? 'Ukasi, Kitui County' }}</span>
            <span>⏰ {{ ucfirst(str_replace('_',' ',$job->type ?? $job->employment_type ?? 'full_time')) }}</span>
            @if($job->closing_date)<span>📅 Closes {{ \Carbon\Carbon::parse($job->closing_date)->format('M j, Y') }}</span>@endif
        </div>
    </div>
</div>

<section class="job-section">
    <div class="container">
        <div class="job-layout">
            <div>
                @if($job->description)
                <div class="content-box"><h2>About the Role</h2><p>{{ $job->description }}</p></div>
                @endif
                @if($job->requirements)
                <div class="content-box">
                    <h2>Requirements</h2>
                    @if(str_contains($job->requirements, "\n"))
                        <ul>@foreach(explode("\n", $job->requirements) as $req)@if(trim($req))<li>{{ ltrim(trim($req),'•-*') }}</li>@endif@endforeach</ul>
                    @else
                        <p>{{ $job->requirements }}</p>
                    @endif
                </div>
                @endif
            </div>

            <div>
                <div class="apply-card">
                    <h2>Apply Now</h2>
                    @if(session('success'))<div class="success-msg">✓ {{ session('success') }}</div>@endif
                    <form method="POST" action="{{ route('careers.apply', $job) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group"><label>First Name *</label><input type="text" name="first_name" value="{{ old('first_name') }}" required>@error('first_name')<div class="error-msg">{{ $message }}</div>@enderror</div>
                        <div class="form-group"><label>Last Name *</label><input type="text" name="last_name" value="{{ old('last_name') }}" required>@error('last_name')<div class="error-msg">{{ $message }}</div>@enderror</div>
                        <div class="form-group"><label>Email Address *</label><input type="email" name="email" value="{{ old('email') }}" required>@error('email')<div class="error-msg">{{ $message }}</div>@enderror</div>
                        <div class="form-group"><label>Phone Number *</label><input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+254 7XX XXX XXX"></div>
                        <div class="form-group"><label>Upload CV (PDF, DOC, DOCX · max 5MB) *</label><input type="file" name="cv" accept=".pdf,.doc,.docx" required>@error('cv')<div class="error-msg">{{ $message }}</div>@enderror</div>
                        <div class="form-group"><label>Cover Letter (optional)</label><input type="file" name="cover_letter" accept=".pdf,.doc,.docx"><p class="file-hint">PDF, DOC or DOCX · max 5MB</p></div>
                        <div class="form-group"><label>Additional Message</label><textarea name="message" rows="3" placeholder="Anything else you'd like us to know?">{{ old('message') }}</textarea></div>
                        <button type="submit" class="btn-apply">Submit Application</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection