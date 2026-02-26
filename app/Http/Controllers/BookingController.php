<?php

namespace App\Http\Controllers;

use App\Models\{Room, RoomType, Guest, Booking, Payment};
use App\Services\{BookingService, MpesaService, SmsService};
use Illuminate\Http\{Request, RedirectResponse, JsonResponse};
use Illuminate\View\View;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private MpesaService   $mpesaService,
        private SmsService     $smsService,
    ) {}

    public function index(): View
    {
        $roomTypes = RoomType::where('is_active', true)->orderBy('sort_order')->get();
        return view('public.booking.search', compact('roomTypes'));
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults'    => 'required|integer|min:1|max:6',
            'children'  => 'nullable|integer|min:0',
        ]);

        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights   = $checkIn->diffInDays($checkOut);

        $available = $this->bookingService
            ->getAvailableRooms($checkIn, $checkOut)
            ->filter(fn($r) => $r->roomType->max_adults >= $request->adults)
            ->groupBy('room_type_id')
            ->map(function ($rooms) use ($checkIn, $checkOut, $nights) {
                $room  = $rooms->first();
                $type  = $room->roomType;
                $costs = $this->bookingService->calculateCost($room, $checkIn, $checkOut);

                return [
                    'room_type'       => $type,
                    'sample_room_id'  => $room->id,
                    'available_count' => $rooms->count(),
                    'nights'          => $nights,
                    'price_per_night' => $type->getPriceForDate($checkIn),
                    'subtotal'        => $costs['subtotal'],
                    'tax'             => $costs['tax'],
                    'total'           => $costs['total'],
                ];
            })->values();

        return response()->json([
            'rooms'  => $available,
            'nights' => $nights,
        ]);
    }

    public function select(Room $room, Request $request): View
    {
        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $costs    = $this->bookingService->calculateCost($room, $checkIn, $checkOut);

        return view('public.booking.details', [
            'room'     => $room->load('roomType'),
            'checkIn'  => $checkIn,
            'checkOut' => $checkOut,
            'nights'   => $nights = $checkIn->diffInDays($checkOut),
            'adults'   => (int) $request->get('adults', 2),
            'children' => (int) $request->get('children', 0),
            'costs'    => $costs,
        ]);
    }

    public function reserve(Request $request): RedirectResponse
    {
        $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'check_in'         => 'required|date|after_or_equal:today',
            'check_out'        => 'required|date|after:check_in',
            'first_name'       => 'required|string|max:80',
            'last_name'        => 'required|string|max:80',
            'email'            => 'required|email',
            'phone'            => 'required|string',
            'adults'           => 'required|integer|min:1',
            'children'         => 'nullable|integer|min:0',
            'agree_terms'      => 'accepted',
        ]);

        $guest = Guest::firstOrCreate(
            ['email' => $request->email],
            [
                'first_name'  => $request->first_name,
                'last_name'   => $request->last_name,
                'phone'       => $request->phone,
                'nationality' => $request->nationality,
                'id_number'   => $request->id_number,
            ]
        );

        $booking = $this->bookingService->createBooking([
            'room_id'          => $request->room_id,
            'guest_id'         => $guest->id,
            'check_in'         => $request->check_in,
            'check_out'        => $request->check_out,
            'adults'           => $request->adults,
            'children'         => $request->children ?? 0,
            'special_requests' => $request->special_requests,
            'source'           => 'website',
        ]);

        session(['booking_id' => $booking->id]);

        return redirect()->route('booking.payment', $booking);
    }

    public function payment(Booking $booking): View
    {
        abort_if(session('booking_id') !== $booking->id, 403);

        $booking->load(['room.roomType', 'guest']);

        return view('public.booking.payment', compact('booking'));
    }

    public function payMpesa(Booking $booking, Request $request): JsonResponse
    {
        abort_if(session('booking_id') !== $booking->id, 403);

        $request->validate(['phone' => 'required|string']);

        $response = $this->mpesaService->stkPush(
            phone: $request->phone,
            amount: (int) $booking->total_amount,
            reference: $booking->booking_ref,
            description: "Room booking {$booking->booking_ref}"
        );

        if (($response['ResponseCode'] ?? null) === '0') {
            Payment::create([
                'reference'          => 'PAY-' . strtoupper(uniqid()),
                'payable_type'       => Booking::class,
                'payable_id'         => $booking->id,
                'guest_id'           => $booking->guest_id,
                'amount'             => $booking->total_amount,
                'currency'           => 'KES',
                'method'             => 'mpesa',
                'status'             => 'pending',
                'provider_reference' => $response['CheckoutRequestID'],
                'provider_response'  => $response,
            ]);

            return response()->json([
                'success'             => true,
                'checkout_request_id' => $response['CheckoutRequestID'],
                'message'             => 'STK push sent. Check your phone.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['errorMessage'] ?? 'Payment initiation failed. Try again.',
        ], 422);
    }

    public function pollPayment(Booking $booking): JsonResponse
    {
        $payment = Payment::where('payable_type', Booking::class)
            ->where('payable_id', $booking->id)
            ->where('method', 'mpesa')
            ->latest()
            ->first();

        return response()->json([
            'status'    => $payment?->status ?? 'pending',
            'reference' => $payment?->provider_reference,
        ]);
    }

    public function confirmation(Booking $booking): View
    {
        abort_if(
            session('booking_id') !== $booking->id
            && $booking->payment_status !== 'paid',
            403
        );

        $booking->load(['room.roomType', 'guest', 'payments']);

        return view('public.booking.confirmation', compact('booking'));
    }
}
