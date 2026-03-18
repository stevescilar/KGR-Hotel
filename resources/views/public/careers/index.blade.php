@extends('layouts.app')

@section('title', 'Careers')
@section('navbar-class', 'scrolled')

@push('styles')
<style>
    .page-hero {
        margin-top:72px; height:320px;
        background:linear-gradient(rgba(10,30,20,0.55),rgba(10,30,20,0.65)),
                   linear-gradient(135deg,#1e3a2f,#4a8060);
        display:flex; align-items:center; justify-content:center; text-align:center; color:white;
    }
    .page-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(2rem,4vw,3rem); font-weight:400; }
    .page-hero p  { color:rgba(255,255,255,0.75); margin-top:0.5rem; max-width:480px; }

    .careers-section { background:var(--cream); padding:5rem 0; }
    .section-header { text-align:center; margin-bottom:3.5rem; }
    .section-eyebrow { font-size:0.7rem; font-weight:700; letter-spacing:0.25em; text-transform:uppercase; color:var(--gold); display:block; margin-bottom:0.75rem; }
    .section-header h2 { font-family:'Playfair Display',serif; font-size:clamp(1.75rem,3vw,2.25rem); color:var(--forest); }

    .department-block { margin-bottom:2.5rem; }
    .dept-label { font-size:0.7rem; font-weight:700; letter-spacing:0.2em; text-transform:uppercase; color:var(--gold); padding-bottom:0.5rem; border-bottom:2px solid var(--mist); margin-bottom:1rem; }

    .job-card {
        background:white; border-radius:12px; padding:1.5rem 1.75rem;
        box-shadow:0 2px 12px rgba(0,0,0,0.05); margin-bottom:0.75rem;
        display:flex; align-items:center; justify-content:space-between; gap:1.5rem;
        transition:box-shadow 0.2s, transform 0.2s; text-decoration:none; color:inherit;
    }
    .job-card:hover { box-shadow:0 6px 24px rgba(0,0,0,0.1); transform:translateX(4px); }
    .job-info h3 { font-family:'Playfair Display',serif; font-size:1.1rem; color:var(--forest); margin-bottom:0.35rem; }
    .job-meta { display:flex; gap:1rem; flex-wrap:wrap; }
    .job-meta span { font-size:0.78rem; color:#6b7280; }
    .job-arrow { font-size:1.25rem; color:var(--mist); flex-shrink:0; transition:color 0.2s; }
    .job-card:hover .job-arrow { color:var(--gold); }

    .empty-state { text-align:center; padding:4rem 1rem; color:#9ca3af; }
    .empty-state .icon { font-size:3rem; margin-bottom:1rem; }

    /* Culture */
    .culture-section { background:white; padding:5rem 0; }
    .culture-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1.5rem; margin-top:3rem; }
    .culture-card { background:var(--cream); border-radius:12px; padding:1.75rem; text-align:center; }
    .culture-card .icon { font-size:2rem; margin-bottom:0.75rem; }
    .culture-card h4 { font-size:0.95rem; font-weight:700; color:var(--forest); margin-bottom:0.4rem; }
    .culture-card p  { font-size:0.8rem; color:#6b7280; }

    /* Speculative CTA */
    .spec-section { background:var(--forest); padding:4rem 0; text-align:center; color:white; }
    .spec-section h2 { font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:400; margin-bottom:0.75rem; }
    .spec-section p  { color:rgba(255,255,255,0.65); margin-bottom:1.5rem; }
    .btn-spec { display:inline-block; background:var(--gold); color:white; padding:0.85rem 2rem; border-radius:8px; font-size:0.85rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; transition:background 0.2s; }
    .btn-spec:hover { background:#b5863a; }
</style>
@endpush

@section('content')

<div class="page-hero">
    <div>
        <h1>Join Our Team</h1>
        <p>Build your career in hospitality at one of Kenya's most beautiful resorts</p>
    </div>
</div>

{{-- Culture --}}
<section class="culture-section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Why KGR</span>
            <h2>Life at Kitonga Garden</h2>
        </div>
        <div class="culture-grid">
            @foreach([
                ['🌿','Beautiful Setting','Work surrounded by lush gardens and stunning natural scenery'],
                ['📈','Career Growth','Structured paths and mentorship in hospitality excellence'],
                ['🤝','Great Team','A warm, collaborative team that feels like family'],
                ['🎓','Training & Dev','Ongoing training to sharpen your hospitality skills'],
            ] as [$icon,$title,$desc])
            <div class="culture-card">
                <div class="icon">{{ $icon }}</div>
                <h4>{{ $title }}</h4>
                <p>{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Open positions from DB --}}
<section class="careers-section">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Open Positions</span>
            <h2>Current Opportunities</h2>
        </div>

        @if($jobs->isEmpty())
        <div class="empty-state">
            <div class="icon">💼</div>
            <p>No open positions at the moment. Check back soon or send a speculative application below.</p>
        </div>
        @else
            @foreach($jobs as $department => $listings)
            <div class="department-block">
                <div class="dept-label">{{ $department ?? 'General' }}</div>
                @foreach($listings as $job)
                <a href="{{ route('careers.show', $job) }}" class="job-card">
                    <div class="job-info">
                        <h3>{{ $job->title }}</h3>
                        <div class="job-meta">
                            <span>📍 {{ $job->location ?? 'Ukasi, Kitui' }}</span>
                            <span>⏰ {{ ucfirst(str_replace('_',' ', $job->type ?? $job->employment_type ?? 'full_time')) }}</span>
                            @if($job->closing_date)
                                <span>📅 Closes {{ \Carbon\Carbon::parse($job->closing_date)->format('M j, Y') }}</span>
                            @endif
                            @if($job->salary_min && $job->salary_max)
                                <span>💰 KES {{ number_format($job->salary_min) }}–{{ number_format($job->salary_max) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="job-arrow">→</div>
                </a>
                @endforeach
            </div>
            @endforeach
        @endif
    </div>
</section>

{{-- Speculative --}}
<section class="spec-section">
    <div class="container">
        <h2>Don't See Your Role?</h2>
        <p>We're always looking for talented hospitality professionals. Send us your CV and we'll keep you in mind.</p>
        <a href="mailto:careers@kitongagardenresort.com" class="btn-spec">Send Speculative Application</a>
    </div>
</section>

@endsection