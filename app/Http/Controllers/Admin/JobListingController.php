<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class JobListingController extends Controller
{
    public function index(): View
    {
        $jobs = JobListing::withCount('applications')->latest()->paginate(20);
        return view('admin.careers.jobs.index', compact('jobs'));
    }

    public function create(): View
    {
        return view('admin.careers.jobs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'        => 'required|string|max:120',
            'department'   => 'required|string|max:80',
            'location'     => 'required|string|max:80',
            'type'         => 'required|in:full_time,part_time,contract,internship',
            'description'  => 'required|string',
            'requirements' => 'nullable|string',
            'salary_min'   => 'nullable|numeric|min:0',
            'salary_max'   => 'nullable|numeric|min:0',
            'closing_date' => 'nullable|date|after:today',
            'is_active'    => 'boolean',
        ]);

        JobListing::create($data);

        return redirect()->route('admin.careers.jobs.index')
            ->with('success', "Job listing '{$data['title']}' created.");
    }

    public function edit(JobListing $job): View
    {
        return view('admin.careers.jobs.edit', compact('job'));
    }

    public function update(Request $request, JobListing $job): RedirectResponse
    {
        $data = $request->validate([
            'title'        => 'required|string|max:120',
            'department'   => 'required|string|max:80',
            'location'     => 'required|string|max:80',
            'type'         => 'required|in:full_time,part_time,contract,internship',
            'description'  => 'required|string',
            'requirements' => 'nullable|string',
            'salary_min'   => 'nullable|numeric|min:0',
            'salary_max'   => 'nullable|numeric|min:0',
            'closing_date' => 'nullable|date',
            'is_active'    => 'boolean',
        ]);

        $job->update($data);

        return redirect()->route('admin.careers.jobs.index')
            ->with('success', 'Job listing updated.');
    }

    public function destroy(JobListing $job): RedirectResponse
    {
        $job->delete();
        return redirect()->route('admin.careers.jobs.index')
            ->with('success', 'Job listing deleted.');
    }
}
