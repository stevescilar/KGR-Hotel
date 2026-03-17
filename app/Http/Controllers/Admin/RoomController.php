<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Room, RoomType, WalkInTicket, Guest, Booking, Payment};
use App\Services\SmsService;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms     = Room::with('roomType')->orderBy('room_number')->get();
        $roomTypes = RoomType::withCount('rooms')->orderBy('sort_order')->get();
        return view('admin.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function housekeeper(): View
    {
        $rooms = Room::with(['roomType', 'bookings' => fn($q) => $q->where('status', 'checked_in')->with('guest')])
            ->orderBy('status')->orderBy('room_number')->get();
        return view('admin.rooms.housekeeper', compact('rooms'));
    }

    public function create(): View
    {
        $roomTypes = RoomType::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.rooms.create', compact('roomTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'room_number'  => 'required|string|max:20|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,id',
            'floor'        => 'nullable|integer|min:0',
            'cottage'      => 'nullable|string|max:50',
            'notes'        => 'nullable|string',
            'status'       => 'required|in:available,occupied,cleaning,maintenance',
            'image'        => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }

        Room::create($data);

        return redirect()->route('admin.rooms.index')
            ->with('success', "Room {$data['room_number']} created.");
    }

    public function edit(Room $room): View
    {
        $roomTypes = RoomType::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.rooms.edit', compact('room', 'roomTypes'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $data = $request->validate([
            'room_number'  => 'required|string|max:20|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor'        => 'nullable|integer|min:0',
            'cottage'      => 'nullable|string|max:50',
            'notes'        => 'nullable|string',
            'status'       => 'required|in:available,occupied,cleaning,maintenance',
            'image'        => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($room->image) Storage::disk('public')->delete($room->image);
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }

        $room->update($data);

        return redirect()->route('admin.rooms.index')
            ->with('success', "Room {$room->room_number} updated.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->bookings()->whereIn('status', ['confirmed', 'checked_in'])->exists()) {
            return back()->with('error', 'Cannot delete a room with active bookings.');
        }
        if ($room->image) Storage::disk('public')->delete($room->image);
        $room->delete();
        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted.');
    }

    public function updateStatus(Room $room, Request $request): RedirectResponse
    {
        $request->validate(['status' => 'required|in:available,occupied,cleaning,maintenance']);
        $room->update(['status' => $request->status]);
        return back()->with('success', "Room {$room->room_number} marked as {$request->status}.");
    }

    // ── Walk-in booking with auto ticket ─────────────────────────────────────

    public function walkIn(): View
    {
        $rooms = Room::with('roomType')
            ->where('status', 'available')
            ->orderBy('room_number')->get();
        return view('admin.rooms.walk-in', compact('rooms'));
    }

    public function storeWalkIn(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'    => 'required|string|max:80',
            'last_name'     => 'required|string|max:80',
            'phone'         => 'required|string',
            'id_number'     => 'nullable|string',
            'room_id'       => 'required|exists:rooms,id',
            'check_in'      => 'required|date',
            'check_out'     => 'required|date|after:check_in',
            'adults'        => 'required|integer|min:1',
            'children'      => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string',
            'payment_method'   => 'required|in:cash,mpesa,card',
            'amount_paid'      => 'required|numeric|min:0',
        ]);

        // Find or create guest
        $guest = Guest::firstOrCreate(
            ['phone' => $data['phone']],
            [
                'first_name'  => $data['first_name'],
                'last_name'   => $data['last_name'],
                'id_number'   => $data['id_number'] ?? null,
            ]
        );

        $room     = Room::findOrFail($data['room_id']);
        $checkIn  = \Carbon\Carbon::parse($data['check_in']);
        $checkOut = \Carbon\Carbon::parse($data['check_out']);
        $nights   = $checkIn->diffInDays($checkOut);
        $subtotal = $room->roomType->base_price * $nights;
        $tax      = round($subtotal * 0.16, 2);
        $total    = $subtotal + $tax;

        // Create booking
        $booking = Booking::create([
            'booking_ref'    => 'KGR-WI-' . strtoupper(substr(uniqid(), -6)),
            'room_id'        => $room->id,
            'guest_id'       => $guest->id,
            'check_in'       => $checkIn,
            'check_out'      => $checkOut,
            'adults'         => $data['adults'],
            'children'       => $data['children'] ?? 0,
            'subtotal'       => $subtotal,
            'tax_amount'     => $tax,
            'total_amount'   => $total,
            'paid_amount'    => $data['amount_paid'],
            'payment_status' => $data['amount_paid'] >= $total ? 'paid' : 'partial',
            'status'         => 'confirmed',
            'source'         => 'walk_in',
            'special_requests' => $data['special_requests'] ?? null,
        ]);

        // Record payment
        Payment::create([
            'reference'    => 'PAY-WI-' . strtoupper(uniqid()),
            'payable_type' => Booking::class,
            'payable_id'   => $booking->id,
            'guest_id'     => $guest->id,
            'amount'       => $data['amount_paid'],
            'currency'     => 'KES',
            'method'       => $data['payment_method'],
            'status'       => 'completed',
            'paid_at'      => now(),
        ]);

        // Mark room occupied
        $room->update(['status' => 'occupied']);

        // ── AUTO WALK-IN TICKET (KES 1,500 per person) ────────
        $ticketCount = $data['adults'] + ($data['children'] ?? 0);
        $tickets = [];
        for ($i = 0; $i < $ticketCount; $i++) {
            $isFirst = $i === 0;
            $tickets[] = WalkInTicket::create([
                'booking_id'      => $booking->id,
                'guest_id'        => $guest->id,
                'guest_name'      => $isFirst
                    ? $data['first_name'] . ' ' . $data['last_name']
                    : $data['first_name'] . ' ' . $data['last_name'] . ' (Guest ' . ($i + 1) . ')',
                'guest_phone'     => $data['phone'],
                'guest_id_number' => $data['id_number'] ?? null,
                'valid_date'      => $checkIn->toDateString(),
                'amount'          => 1500,
                'status'          => 'active',
            ]);
        }

        // SMS confirmation
        try {
            app(SmsService::class)->send(
                $data['phone'],
                "Welcome to Kitonga Garden Resort! Booking ref: {$booking->booking_ref}. " .
                "Room {$room->room_number}. Check-out: {$checkOut->format('M j, Y')}. " .
                "Walk-in ticket(s): " . implode(', ', array_column($tickets, 'ticket_number')) . "."
            );
        } catch (\Exception $e) {}

        session(['walk_in_booking_id' => $booking->id]);

        return redirect()->route('admin.rooms.walk-in.receipt', $booking)
            ->with('success', 'Walk-in booking created with ' . count($tickets) . ' resort ticket(s).');
    }

    public function walkInReceipt(Booking $booking): View
    {
        $booking->load(['room.roomType', 'guest', 'payments']);
        $tickets = WalkInTicket::where('booking_id', $booking->id)->get();
        return view('admin.rooms.walk-in-receipt', compact('booking', 'tickets'));
    }
}