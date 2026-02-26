<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GateTicket;
use App\Services\GateTicketService;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\View\View;

class GateTicketController extends Controller
{
    public function __construct(private GateTicketService $ticketService) {}

    public function index(Request $request): View
    {
        $tickets = GateTicket::with('ticketType')
            ->when($request->date, fn($q) => $q->whereDate('visit_date', $request->date))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $todayCount   = GateTicket::whereDate('visit_date', today())->sum('quantity');
        $todayRevenue = GateTicket::whereDate('visit_date', today())
            ->whereHas('payments', fn($q) => $q->where('status', 'completed'))
            ->sum('total_price');

        return view('admin.tickets.index', compact('tickets', 'todayCount', 'todayRevenue'));
    }

    public function scan(): View
    {
        return view('admin.tickets.scan');
    }

    public function processQr(Request $request): JsonResponse
    {
        $request->validate(['qr_code' => 'required|string']);

        $result = $this->ticketService->scanTicket($request->qr_code, auth()->id());

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
