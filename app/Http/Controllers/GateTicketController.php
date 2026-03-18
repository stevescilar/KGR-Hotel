<?php

namespace App\Http\Controllers;

use App\Models\{GateTicket, TicketType, Payment};
use App\Services\{GateTicketService, MpesaService, SmsService};
use Illuminate\Http\{Request, RedirectResponse, JsonResponse};
use Illuminate\View\View;

class GateTicketController extends Controller
{
    public function __construct(
        private GateTicketService $ticketService,
        private MpesaService      $mpesaService,
        private SmsService        $smsService,
    ) {}

    public function index(): View
    {
        $ticketTypes = TicketType::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('public.tickets.index', compact('ticketTypes'));
    }

    public function purchase(Request $request): RedirectResponse
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'visit_date'     => 'required|date|after_or_equal:today',
            'quantity'       => 'required|integer|min:1|max:50',
            'guest_name'     => 'required|string|max:100',
            'guest_phone'    => 'required|string',
            'guest_email'    => 'nullable|email',
        ]);

        $ticket = $this->ticketService->purchaseTickets($request->all());
        session(['ticket_id' => $ticket->id]);
        return redirect()->route('tickets.payment', $ticket);
    }

    public function payment(GateTicket $ticket): View
    {
        abort_if(session('ticket_id') !== $ticket->id, 403);
        $ticket->load('ticketType');
        return view('public.tickets.payment', compact('ticket'));
    }

    public function payMpesa(GateTicket $ticket, Request $request): JsonResponse
    {
        abort_if(session('ticket_id') !== $ticket->id, 403);
        $request->validate(['phone' => 'required|string']);

        $response = $this->mpesaService->stkPush(
            phone: $request->phone,
            amount: (int) $ticket->total_price,
            reference: $ticket->ticket_number,
            description: "Gate ticket {$ticket->ticket_number}"
        );

        if (($response['ResponseCode'] ?? null) === '0') {
            Payment::create([
                'reference'          => 'PAY-' . strtoupper(uniqid()),
                'payable_type'       => GateTicket::class,
                'payable_id'         => $ticket->id,
                'amount'             => $ticket->total_price,
                'currency'           => 'KES',
                'method'             => 'mpesa',
                'status'             => 'pending',
                'provider_reference' => $response['CheckoutRequestID'],
                'provider_response'  => $response,
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['errorMessage'] ?? 'Payment failed. Try again.',
        ], 422);
    }

    public function confirmation(GateTicket $ticket): View
    {
        abort_if(
            session('ticket_id') !== $ticket->id && $ticket->status !== 'active',
            403
        );
        $ticket->load('ticketType');
        return view('public.tickets.confirmation', compact('ticket'));
    }
}