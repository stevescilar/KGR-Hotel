<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Room, Guest, Payment};
use App\Services\BookingService;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with(['room.roomType', 'guest', 'payments'])
            ->orderByDesc('created_at');

        // Filter
        $filter = $request->get('filter', '');
        match($filter) {
            'pending'     => $query->where('status', 'pending'),
            'confirmed'   => $query->where('status', 'confirmed'),
            'checked_in'  => $query->where('status', 'checked_in'),
            'checked_out' => $query->where('status', 'checked_out'),
            'cancelled'   => $query->where('status', 'cancelled'),
            'deposit'     => $query->where('payment_option', 'deposit')
                                   ->whereColumn('paid_amount', '<', 'total_amount'),
            default => null,
        };

        $bookings = $query->paginate(25)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function create(): View
    {
        $rooms = Room::with('roomType')
            ->where('status', 'available')
            ->orderBy('room_number')
            ->get();
        return view('admin.bookings.create', compact('rooms'));
    }

    public function checkAvailability(Request $request)
    {
        // Returns available rooms as JSON for the create form
        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        $rooms = Room::with('roomType')
            ->where('status', 'available')
            ->get()
            ->filter(fn($r) => !$r->bookings()
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where(fn($q) => $q
                    ->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(fn($q2) => $q2->where('check_in', '<=', $checkIn)->where('check_out', '>=', $checkOut))
                )->exists()
            );

        return response()->json($rooms->values());
    }

    public function store(Request $request): RedirectResponse
    {
        // Admin-side booking creation handled by RoomController::walkIn
        return redirect()->route('admin.rooms.walk-in');
    }

    public function show(Booking $booking): View
    {
        $booking->load(['room.roomType', 'guest', 'payments']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        $booking->update([
            'status'        => 'checked_in',
            'checked_in_at' => now(),
        ]);
        $booking->room->update(['status' => 'occupied']);
        return back()->with('success', "Guest checked in to Room {$booking->room->room_number}.");
    }

    public function checkOut(Booking $booking): RedirectResponse
    {
        $booking->update([
            'status'         => 'checked_out',
            'checked_out_at' => now(),
        ]);
        $booking->room->update(['status' => 'cleaning']);
        return back()->with('success', "Guest checked out. Room {$booking->room->room_number} marked for cleaning.");
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'cancelled']);
        $booking->room->update(['status' => 'available']);
        return back()->with('success', "Booking {$booking->booking_ref} cancelled.");
    }


    /**
     * Show all deposit bookings with outstanding balance.
     */
    public function partialPayments(Request $request): View
    {
        $query = Booking::with(['room.roomType', 'guest'])
            ->where('payment_option', 'deposit')
            ->whereColumn('paid_amount', '<', 'total_amount')
            ->whereNotIn('status', ['cancelled']);

        $filter = $request->get('filter', 'all');
        if ($filter === 'arriving_today') {
            $query->whereDate('check_in', today());
        } elseif ($filter === 'checked_in') {
            $query->where('status', 'checked_in');
        } elseif ($filter === 'overdue') {
            $query->where('check_in', '<', today())->where('status', '!=', 'checked_in');
        }

        $bookings = $query->orderBy('check_in')->paginate(25);
        return view('admin.bookings.partial-payments', compact('bookings'));
    }

    /**
     * Mark the outstanding balance as collected on arrival.
     */
    public function collectBalance(Booking $booking): RedirectResponse
    {
        $balance = $booking->total_amount - $booking->paid_amount;

        if ($balance <= 0) {
            return back()->with('success', 'This booking is already fully paid.');
        }

        Payment::create([
            'reference'    => 'BAL-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'payable_type' => Booking::class,
            'payable_id'   => $booking->id,
            'guest_id'     => $booking->guest_id,
            'amount'       => $balance,
            'currency'     => 'KES',
            'method'       => 'cash',
            'status'       => 'completed',
            'paid_at'      => now(),
        ]);

        $booking->update([
            'paid_amount'    => $booking->total_amount,
            'payment_status' => 'paid',
        ]);

        return back()->with('success', "Balance of KES " . number_format($balance) . " collected for {$booking->booking_ref}.");
    }
}