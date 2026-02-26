<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applications = JobApplication::with('jobListing')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->job_id, fn($q) => $q->where('job_listing_id', $request->job_id))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.careers.applications.index', compact('applications'));
    }

    public function show(JobApplication $application): View
    {
        $application->load('jobListing');
        return view('admin.careers.applications.show', compact('application'));
    }

    public function updateStatus(JobApplication $application, Request $request): RedirectResponse
    {
        $request->validate([
            'status'   => 'required|in:received,reviewing,shortlisted,interview,offered,hired,rejected',
            'hr_notes' => 'nullable|string|max:2000',
        ]);

        $application->update($request->only('status', 'hr_notes'));

        return back()->with('success', "Application status updated to '{$request->status}'.");
    }
}
