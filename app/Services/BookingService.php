<?php

namespace App\Services;

use App\Models\{Room, Guest, Booking};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BookingService
{
    /**
     * Find available rooms for given dates, optionally filtered by room type.
     */
    public function getAvailableRooms(Carbon $checkIn, Carbon $checkOut, ?int $roomTypeId = null): Collection
    {
        return Room::with('roomType')
            ->where('status', 'available')
            ->when($roomTypeId, fn($q) => $q->where('room_type_id', $roomTypeId))
            ->whereDoesntHave('blocks', function ($q) use ($checkIn, $checkOut) {
                $q->where('blocked_from', '<=', $checkOut)
                  ->where('blocked_to', '>=', $checkIn);
            })
            ->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['confirmed', 'checked_in'])
                  ->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
            })
            ->get();
    }

    /**
     * Calculate nightly cost breakdown for a room over a date range.
     */
    public function calculateCost(Room $room, Carbon $checkIn, Carbon $checkOut): array
    {
        $nights   = $checkIn->diffInDays($checkOut);
        $subtotal = 0;

        $current = $checkIn->copy();
        while ($current->lt($checkOut)) {
            $subtotal += $room->roomType->getPriceForDate($current);
            $current->addDay();
        }

        $tax   = round($subtotal * 0.16, 2);
        $total = $subtotal + $tax;

        return compact('nights', 'subtotal', 'tax', 'total');
    }

    /**
     * Create a pending booking. Throws if the room is unavailable.
     */
    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $checkIn  = Carbon::parse($data['check_in']);
            $checkOut = Carbon::parse($data['check_out']);
            $room     = Room::findOrFail($data['room_id']);

            if (!$room->isAvailable($checkIn, $checkOut)) {
                throw new \Exception("Room {$room->room_number} is not available for the selected dates.");
            }

            $costs = $this->calculateCost($room, $checkIn, $checkOut);

            return Booking::create([
                'room_id'          => $room->id,
                'guest_id'         => $data['guest_id'],
                'user_id'          => $data['user_id'] ?? null,
                'check_in'         => $checkIn,
                'check_out'        => $checkOut,
                'adults'           => $data['adults'] ?? 1,
                'children'         => $data['children'] ?? 0,
                'room_rate'        => $room->roomType->getPriceForDate($checkIn),
                'subtotal'         => $costs['subtotal'],
                'tax_amount'       => $costs['tax'],
                'discount_amount'  => $data['discount'] ?? 0,
                'total_amount'     => $costs['total'] - ($data['discount'] ?? 0),
                'source'           => $data['source'] ?? 'website',
                'special_requests' => $data['special_requests'] ?? null,
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
            ]);
        });
    }

    /**
     * Confirm a booking after successful payment. Awards loyalty points.
     */
    public function confirmBooking(Booking $booking): void
    {
        $booking->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // 1 point per KES 100 spent
        $points = (int) floor($booking->total_amount / 100);
        if ($points > 0) {
            $booking->guest->addPoints(
                $points, 'booking',
                "Points earned – booking {$booking->booking_ref}",
                $booking
            );
        }
    }

    /**
     * Check in a guest. Updates room and booking status.
     */
    public function checkIn(Booking $booking, int $staffUserId): void
    {
        $booking->update([
            'status'        => 'checked_in',
            'checked_in_at' => now(),
            'user_id'       => $staffUserId,
        ]);

        $booking->room->update(['status' => 'occupied']);
    }

    /**
     * Check out a guest. Puts the room into cleaning.
     */
    public function checkOut(Booking $booking): void
    {
        $booking->update([
            'status'          => 'checked_out',
            'checked_out_at'  => now(),
        ]);

        $booking->room->update(['status' => 'cleaning']);

        // Bonus points for completing a stay
        $booking->guest->addPoints(
            50, 'checkin',
            "Stay completed – {$booking->booking_ref}",
            $booking
        );
    }

    /**
     * Cancel a booking with an optional reason.
     */
    public function cancelBooking(Booking $booking, ?string $reason = null): void
    {
        $booking->update([
            'status'         => 'cancelled',
            'cancelled_at'   => now(),
            'internal_notes' => $reason,
        ]);
    }
}
