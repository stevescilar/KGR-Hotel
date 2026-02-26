<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\{Request, RedirectResponse, JsonResponse};
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['guest', 'table', 'items.menuItem'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $openCount     = Order::whereIn('status', ['open', 'preparing'])->count();
        $todayRevenue  = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        return view('admin.restaurant.orders.index', compact('orders', 'openCount', 'todayRevenue'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.menuItem', 'guest', 'table', 'booking.guest']);
        return view('admin.restaurant.orders.show', compact('order'));
    }

    public function updateStatus(Order $order, Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'status' => 'required|in:open,preparing,served,billed,paid,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $order->status]);
        }

        return back()->with('success', "Order #{$order->order_number} status updated.");
    }
}
