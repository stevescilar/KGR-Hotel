<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Room, RoomType, Guest};
use App\Services\{BookingService, SmsService};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private SmsService     $smsService
    ) {}

    public function index(Request $request): View
    {
        $bookings = Booking::with(['guest', 'room.roomType'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q
                ->where('booking_ref', 'like', "%{$request->search}%")
                ->orWhereHas('guest', fn($g) => $g
                    ->where('email', 'like', "%{$request->search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$request->search}%"])
                )
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function create(): View
    {
        $roomTypes = RoomType::where('is_active', true)->with('rooms')->get();
        return view('admin.bookings.create', compact('roomTypes'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in'     => 'required|date|after_or_equal:today',
            'check_out'    => 'required|date|after:check_in',
            'room_type_id' => 'nullable|exists:room_types,id',
        ]);

        $rooms = $this->bookingService->getAvailableRooms(
            Carbon::parse($request->check_in),
            Carbon::parse($request->check_out),
            $request->room_type_id
        );

        return response()->json($rooms->load('roomType'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'check_in'         => 'required|date',
            'check_out'        => 'required|date|after:check_in',
            'guest.first_name' => 'required|string|max:80',
            'guest.last_name'  => 'required|string|max:80',
            'guest.email'      => 'required|email',
            'guest.phone'      => 'required|string',
            'adults'           => 'required|integer|min:1',
            'children'         => 'nullable|integer|min:0',
        ]);

        $guest = Guest::firstOrCreate(
            ['email' => $request->input('guest.email')],
            [
                'first_name' => $request->input('guest.first_name'),
                'last_name'  => $request->input('guest.last_name'),
                'phone'      => $request->input('guest.phone'),
            ]
        );

        $booking = $this->bookingService->createBooking([
            'room_id'          => $request->room_id,
            'guest_id'         => $guest->id,
            'user_id'          => auth()->id(),
            'check_in'         => $request->check_in,
            'check_out'        => $request->check_out,
            'adults'           => $request->adults,
            'children'         => $request->children ?? 0,
            'special_requests' => $request->special_requests,
            'source'           => 'walk_in',
        ]);

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('success', "Booking {$booking->booking_ref} created successfully.");
    }

    public function show(Booking $booking): View
    {
        $booking->load(['guest', 'room.roomType', 'payments', 'orders.items.menuItem']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed bookings can be checked in.');
        }

        $this->bookingService->checkIn($booking, auth()->id());

        return back()->with('success', "Guest checked in successfully.");
    }

    public function checkOut(Booking $booking): RedirectResponse
    {
        if ($booking->status !== 'checked_in') {
            return back()->with('error', 'Only checked-in bookings can be checked out.');
        }

        $this->bookingService->checkOut($booking);

        return back()->with('success', "Guest checked out. Room set to cleaning.");
    }

    public function cancel(Booking $booking, Request $request): RedirectResponse
    {
        $this->bookingService->cancelBooking($booking, $request->reason);

        return back()->with('success', "Booking {$booking->booking_ref} cancelled.");
    }
}
