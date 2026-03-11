<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventPackage;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class EventPackageController extends Controller
{
    public function index(): View
    {
        $packages = EventPackage::withCount('bookings')->orderBy('starting_price')->get();
        return view('admin.events.packages.index', compact('packages'));
    }

    public function create(): View
    {
        return view('admin.events.packages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'slug'           => 'required|string|unique:event_packages,slug',
            'description'    => 'nullable|string',
            'starting_price' => 'required|numeric|min:0',
            'min_guests'     => 'required|integer|min:1',
            'max_guests'     => 'required|integer|min:1',
            'inclusions_text'=> 'nullable|string',
            'is_active'      => 'boolean',
        ]);

        $data['inclusions'] = array_filter(array_map('trim', explode("\n", $data['inclusions_text'] ?? '')));
        unset($data['inclusions_text']);

        EventPackage::create($data);

        return redirect()->route('admin.events.packages.index')
            ->with('success', "Event package '{$data['name']}' created.");
    }

    public function edit(EventPackage $package): View
    {
        return view('admin.events.packages.edit', compact('package'));
    }

    public function update(Request $request, EventPackage $package): RedirectResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'description'    => 'nullable|string',
            'starting_price' => 'required|numeric|min:0',
            'min_guests'     => 'required|integer|min:1',
            'max_guests'     => 'required|integer|min:1',
            'inclusions_text'=> 'nullable|string',
            'is_active'      => 'boolean',
        ]);

        $data['inclusions'] = array_filter(array_map('trim', explode("\n", $data['inclusions_text'] ?? '')));
        unset($data['inclusions_text']);

        $package->update($data);

        return redirect()->route('admin.events.packages.index')
            ->with('success', 'Event package updated.');
    }

    public function destroy(EventPackage $package): RedirectResponse
    {
        if ($package->bookings()->whereIn('status', ['confirmed', 'completed'])->exists()) {
            return back()->with('error', 'Cannot delete a package with confirmed bookings.');
        }

        $package->delete();

        return redirect()->route('admin.events.packages.index')
            ->with('success', 'Package deleted.');
    }
}