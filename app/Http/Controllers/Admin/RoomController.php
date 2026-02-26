<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Room, RoomType};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms     = Room::with('roomType')->orderBy('room_number')->get();
        $roomTypes = RoomType::withCount('rooms')->get();

        return view('admin.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function housekeeper(): View
    {
        $rooms = Room::with([
            'roomType',
            'bookings' => fn($q) => $q->where('status', 'checked_in')->with('guest'),
        ])
        ->orderByRaw("FIELD(status, 'cleaning', 'occupied', 'available', 'maintenance')")
        ->get();

        return view('admin.rooms.housekeeper', compact('rooms'));
    }

    public function updateStatus(Room $room, Request $request): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:available,occupied,maintenance,cleaning',
        ]);

        $room->update(['status' => $request->status]);

        return back()->with('success', "Room {$room->room_number} status updated to {$request->status}.");
    }
}
