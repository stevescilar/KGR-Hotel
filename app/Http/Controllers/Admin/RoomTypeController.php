<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    public function index(): View
    {
        $roomTypes = RoomType::withCount('rooms')->orderBy('sort_order')->get();
        return view('admin.rooms.types.index', compact('roomTypes'));
    }

    public function create(): View
    {
        return view('admin.rooms.types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:80',
            'slug'          => 'required|string|unique:room_types,slug',
            'description'   => 'nullable|string',
            'max_adults'    => 'required|integer|min:1',
            'max_children'  => 'required|integer|min:0',
            'base_price'    => 'required|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'amenities'     => 'nullable|array',
            'sort_order'    => 'integer|min:0',
        ]);

        RoomType::create($data);

        return redirect()->route('admin.rooms.types.index')
            ->with('success', "Room type '{$data['name']}' created.");
    }

    public function edit(RoomType $type): View
    {
        return view('admin.rooms.types.edit', compact('type'));
    }

    public function update(Request $request, RoomType $type): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:80',
            'description'   => 'nullable|string',
            'max_adults'    => 'required|integer|min:1',
            'max_children'  => 'required|integer|min:0',
            'base_price'    => 'required|numeric|min:0',
            'weekend_price' => 'nullable|numeric|min:0',
            'amenities'     => 'nullable|array',
            'sort_order'    => 'integer|min:0',
            'is_active'     => 'boolean',
        ]);

        $type->update($data);

        return redirect()->route('admin.rooms.types.index')
            ->with('success', "Room type updated.");
    }

    public function destroy(RoomType $type): RedirectResponse
    {
        if ($type->rooms()->exists()) {
            return back()->with('error', "Cannot delete — this type has rooms assigned to it.");
        }

        $type->delete();

        return redirect()->route('admin.rooms.types.index')
            ->with('success', "Room type deleted.");
    }
}
