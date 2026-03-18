<?php

namespace App\Http\Controllers;

use App\Models\{JobListing, JobApplication};
use App\Services\SmsService;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(): View
    {
        $jobs = JobListing::where('is_active', true)
            ->where(fn($q) => $q->whereNull('closing_date')->orWhere('closing_date', '>=', today()))
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('department');

        return view('public.careers.index', compact('jobs'));
    }

    public function show(JobListing $job): View
    {
        abort_unless($job->is_active, 404);
        return view('public.careers.show', compact('job'));
    }

    public function apply(JobListing $job, Request $request): RedirectResponse
    {
        abort_unless($job->is_active, 410);

        $request->validate([
            'first_name'   => 'required|string|max:80',
            'last_name'    => 'required|string|max:80',
            'email'        => 'required|email',
            'phone'        => 'required|string',
            'cv'           => 'required|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'message'      => 'nullable|string|max:2000',
        ]);

        $cvPath          = $request->file('cv')->store('applications/cvs', 'private');
        $coverLetterPath = $request->file('cover_letter')?->store('applications/cover-letters', 'private');

        $application = JobApplication::create([
            'job_listing_id'    => $job->id,
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'cv_path'           => $cvPath,
            'cover_letter_path' => $coverLetterPath,
            'message'           => $request->message,
            'status'            => 'received',
        ]);

        try {
            app(SmsService::class)->send(
                $request->phone,
                "Hi {$request->first_name}! We've received your application for {$job->title} at Kitonga Garden Resort. Ref: {$application->reference}. We'll be in touch soon."
            );
        } catch (\Exception $e) {}

        return back()->with('success', "Application submitted! Reference: {$application->reference}. We'll review and get back to you.");
    }
}