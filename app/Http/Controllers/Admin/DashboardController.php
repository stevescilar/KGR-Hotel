<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Room, Guest, Order, EventBooking, GateTicket, JobApplication, Payment};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today()->toDateString();

        $stats = [
            'todays_checkins'  => Booking::whereDate('check_in', $today)->whereIn('status', ['confirmed', 'checked_in'])->count(),
            'todays_checkouts' => Booking::whereDate('check_out', $today)->where('status', 'checked_in')->count(),
            'occupied_rooms'   => Room::where('status', 'occupied')->count(),
            'available_rooms'  => Room::where('status', 'available')->count(),
            'total_rooms'      => Room::count(),
            'todays_revenue'   => Payment::where('status', 'completed')->whereDate('paid_at', $today)->sum('amount'),
            'monthly_revenue'  => Payment::where('status', 'completed')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount'),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'open_orders'      => Order::whereIn('status', ['open', 'preparing'])->count(),
            'pending_events'   => EventBooking::where('status', 'inquiry')->count(),
            'new_applications' => JobApplication::where('status', 'received')->count(),
        ];

        $occupancyRate = $stats['total_rooms'] > 0
            ? round(($stats['occupied_rooms'] / $stats['total_rooms']) * 100)
            : 0;

        $recentBookings = Booking::with(['guest', 'room.roomType'])
            ->latest()->take(8)->get();

        $todayCheckins = Booking::with(['guest', 'room.roomType'])
            ->whereDate('check_in', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->orderBy('check_in')
            ->get();

        $todayCheckouts = Booking::with(['guest', 'room.roomType'])
            ->whereDate('check_out', $today)
            ->where('status', 'checked_in')
            ->get();

        // Revenue for last 7 days (for chart)
        $revenueChart = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'date'    => $date->format('D j'),
                'rooms'   => Payment::where('status', 'completed')
                    ->whereHasMorph('payable', [Booking::class])
                    ->whereDate('paid_at', $date)->sum('amount'),
                'fnb'     => Payment::where('status', 'completed')
                    ->whereHasMorph('payable', [Order::class])
                    ->whereDate('paid_at', $date)->sum('amount'),
                'tickets' => Payment::where('status', 'completed')
                    ->whereHasMorph('payable', [GateTicket::class])
                    ->whereDate('paid_at', $date)->sum('amount'),
            ];
        });

        return view('admin.dashboard', compact(
            'stats', 'occupancyRate', 'recentBookings',
            'todayCheckins', 'todayCheckouts', 'revenueChart'
        ));
    }
}
