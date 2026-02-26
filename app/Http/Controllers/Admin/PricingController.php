<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{PricingRule, RoomType};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(): View
    {
        $rules     = PricingRule::with('roomType')->orderBy('start_date')->paginate(30);
        $roomTypes = RoomType::orderBy('sort_order')->get();

        return view('admin.settings.pricing.index', compact('rules', 'roomTypes'));
    }

    public function create(): View
    {
        $roomTypes = RoomType::orderBy('sort_order')->get();
        return view('admin.settings.pricing.create', compact('roomTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'name'         => 'required|string|max:80',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'price'        => 'required|numeric|min:0',
            'min_nights'   => 'nullable|integer|min:1',
            'is_active'    => 'boolean',
        ]);

        PricingRule::create($data);

        return redirect()->route('admin.settings.pricing.index')
            ->with('success', "Pricing rule '{$data['name']}' created.");
    }

    public function edit(PricingRule $pricing): View
    {
        $roomTypes = RoomType::orderBy('sort_order')->get();
        return view('admin.settings.pricing.edit', compact('pricing', 'roomTypes'));
    }

    public function update(Request $request, PricingRule $pricing): RedirectResponse
    {
        $data = $request->validate([
            'name'       => 'required|string|max:80',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'price'      => 'required|numeric|min:0',
            'min_nights' => 'nullable|integer|min:1',
            'is_active'  => 'boolean',
        ]);

        $pricing->update($data);

        return redirect()->route('admin.settings.pricing.index')
            ->with('success', 'Pricing rule updated.');
    }

    public function destroy(PricingRule $pricing): RedirectResponse
    {
        $pricing->delete();
        return redirect()->route('admin.settings.pricing.index')
            ->with('success', 'Pricing rule deleted.');
    }
}
