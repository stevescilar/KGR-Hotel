<?php

namespace App\Http\Controllers;

use App\Models\{Room, RoomType, Guest, Booking, Payment, User};
use App\Services\{BookingService, MpesaService, SmsService};
use Illuminate\Http\{Request, RedirectResponse, JsonResponse};
use Illuminate\View\View;
use Illuminate\Support\Facades\{Hash, Auth};
use Illuminate\Support\Str;
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

        return response()->json(['rooms' => $available, 'nights' => $nights]);
    }

    public function select(Room $room, Request $request): View
    {
        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $costs    = $this->bookingService->calculateCost($room, $checkIn, $checkOut);
        $nights   = $checkIn->diffInDays($checkOut);

        // Meal plan pricing additions (per person per night)
        $mealPlanPrices = [
            'room_only'    => 0,
            'bed_breakfast'=> 750,    // KES 750 per person per night
            'half_board'   => 1800,   // KES 1,800 per person per night
            'full_board'   => 2800,   // KES 2,800 per person per night
        ];

        return view('public.booking.details', [
            'room'           => $room->load('roomType'),
            'checkIn'        => $checkIn,
            'checkOut'       => $checkOut,
            'nights'         => $nights,
            'adults'         => (int) $request->get('adults', 2),
            'children'       => (int) $request->get('children', 0),
            'costs'          => $costs,
            'mealPlanPrices' => $mealPlanPrices,
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
            'meal_plan'        => 'required|in:room_only,bed_breakfast,half_board,full_board',
            'payment_option'   => 'required|in:full,deposit',
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

        // Calculate meal plan surcharge
        $room     = Room::findOrFail($request->room_id);
        $checkIn  = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights   = $checkIn->diffInDays($checkOut);
        $guests   = $request->adults + ($request->children ?? 0);
        $costs    = $this->bookingService->calculateCost($room, $checkIn, $checkOut);

        $mealPlanRates = [
            'room_only'     => 0,
            'bed_breakfast' => 750,
            'half_board'    => 1800,
            'full_board'    => 2800,
        ];
        $mealSurcharge  = ($mealPlanRates[$request->meal_plan] ?? 0) * $guests * $nights;
        $subtotal       = $costs['subtotal'] + $mealSurcharge;
        $tax            = round($subtotal * 0.16, 2);
        $total          = $subtotal + $tax;
        $depositAmount  = $request->payment_option === 'deposit' ? round($total * 0.5, 2) : 0;

        $booking = Booking::create([
            'booking_ref'      => 'KGR-' . date('Y') . '-' . strtoupper(Str::random(5)),
            'room_id'          => $request->room_id,
            'guest_id'         => $guest->id,
            'check_in'         => $checkIn,
            'check_out'        => $checkOut,
            'adults'           => $request->adults,
            'children'         => $request->children ?? 0,
            'room_rate'        => $room->roomType->base_price,
            'meal_plan'        => $request->meal_plan ?? 'room_only',
            'subtotal'         => $subtotal,
            'tax_amount'       => $tax,
            'total_amount'     => $total,
            'deposit_amount'   => $depositAmount,
            'payment_option'   => $request->payment_option ?? 'full',
            'paid_amount'      => 0,
            'payment_status'   => 'unpaid',
            'status'           => 'pending',
            'source'           => 'website',
            'special_requests' => $request->special_requests,
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

        // Pay deposit or full amount
        $amountToPay = $booking->payment_option === 'deposit'
            ? (int) $booking->deposit_amount
            : (int) $booking->total_amount;

        $response = $this->mpesaService->stkPush(
            phone: $request->phone,
            amount: $amountToPay,
            reference: $booking->booking_ref,
            description: "Room booking {$booking->booking_ref}"
        );

        if (($response['ResponseCode'] ?? null) === '0') {
            Payment::create([
                'reference'          => 'PAY-' . strtoupper(uniqid()),
                'payable_type'       => Booking::class,
                'payable_id'         => $booking->id,
                'guest_id'           => $booking->guest_id,
                'amount'             => $amountToPay,
                'currency'           => 'KES',
                'method'             => 'mpesa',
                'status'             => 'pending',
                'provider_reference' => $response['CheckoutRequestID'],
                'provider_response'  => $response,
            ]);

            return response()->json([
                'success' => true,
                'checkout_request_id' => $response['CheckoutRequestID'],
                'message' => 'STK push sent. Check your phone.',
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
            session('booking_id') !== $booking->id && $booking->payment_status !== 'paid',
            403
        );

        $booking->load(['room.roomType', 'guest', 'payments']);

        // Generate account creation token for guest if they don't have an account
        $accountToken = null;
        if (!$booking->guest->user_id && !$booking->guest->account_token) {
            $token = Str::random(40);
            $booking->guest->update([
                'account_token'            => $token,
                'account_token_expires_at' => now()->addHours(48),
            ]);
            $accountToken = $token;

            // Send SMS with account creation link
            try {
                $this->smsService->send(
                    $booking->guest->phone,
                    "Hi {$booking->guest->first_name}! Your KGR booking {$booking->booking_ref} is confirmed. Create your account to earn loyalty points: " . url("/account/create/{$token}")
                );
            } catch (\Exception $e) {}
        }

        return view('public.booking.confirmation', compact('booking', 'accountToken'));
    }

    /**
     * TEST ONLY — bypass M-Pesa and mark booking as paid directly.
     */
    public function testPayment(Booking $booking): RedirectResponse
    {
        abort_if(app()->isProduction(), 404);
        abort_if(session('booking_id') !== $booking->id, 403);

        $amountPaid = $booking->payment_option === 'deposit'
            ? $booking->deposit_amount
            : $booking->total_amount;

        Payment::create([
            'reference'          => 'TEST-' . strtoupper(uniqid()),
            'payable_type'       => Booking::class,
            'payable_id'         => $booking->id,
            'guest_id'           => $booking->guest_id,
            'amount'             => $amountPaid,
            'currency'           => 'KES',
            'method'             => 'mpesa',
            'status'             => 'completed',
            'provider_reference' => 'TEST-MPESA-' . strtoupper(uniqid()),
            'provider_response'  => ['test' => true],
            'paid_at'            => now(),
        ]);

        $booking->update([
            'paid_amount'    => $amountPaid,
            'payment_status' => $amountPaid >= $booking->total_amount ? 'paid' : 'partial',
            'status'         => 'confirmed',
        ]);

        return redirect()->route('booking.confirmation', $booking);
    }
}