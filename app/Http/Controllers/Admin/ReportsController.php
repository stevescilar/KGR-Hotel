<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Room, RoomType, Payment, Order, GateTicket};
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function occupancy(Request $request): View
    {
        $month = $request->month ?? now()->format('Y-m');
        $start = Carbon::parse($month)->startOfMonth();
        $end   = Carbon::parse($month)->endOfMonth();

        $totalRooms  = Room::count();
        $daysInMonth = $start->daysInMonth;
        $capacity    = $totalRooms * $daysInMonth;

        // Count room-nights that were occupied
        $occupiedNights = Booking::whereIn('status', ['checked_in', 'checked_out'])
            ->where('check_in', '<=', $end)
            ->where('check_out', '>=', $start)
            ->get()
            ->sum(function (Booking $b) use ($start, $end) {
                $from = $b->check_in->max($start);
                $to   = $b->check_out->min($end);
                return max(0, $from->diffInDays($to));
            });

        $occupancyRate = $capacity > 0
            ? round(($occupiedNights / $capacity) * 100, 1)
            : 0;

        $byRoomType = RoomType::withCount('rooms as total_rooms')->get()->map(function (RoomType $type) use ($start, $end) {
            $bookingCount = Booking::whereHas('room', fn($q) => $q->where('room_type_id', $type->id))
                ->whereIn('status', ['checked_in', 'checked_out'])
                ->where('check_in', '<=', $end)
                ->where('check_out', '>=', $start)
                ->count();

            return [
                'type'         => $type->name,
                'total_rooms'  => $type->total_rooms,
                'bookings'     => $bookingCount,
            ];
        });

        return view('admin.reports.occupancy', compact(
            'occupancyRate', 'occupiedNights', 'capacity', 'byRoomType', 'month'
        ));
    }

    public function revenue(Request $request): View
    {
        $year = (int) ($request->year ?? now()->year);

        $data = collect(range(1, 12))->map(function (int $month) use ($year) {
            $base = Payment::where('status', 'completed')
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $month);

            return [
                'month'   => Carbon::create($year, $month)->format('M'),
                'rooms'   => (clone $base)->whereHasMorph('payable', [Booking::class])->sum('amount'),
                'fnb'     => (clone $base)->whereHasMorph('payable', [Order::class])->sum('amount'),
                'tickets' => (clone $base)->whereHasMorph('payable', [GateTicket::class])->sum('amount'),
            ];
        });

        $total = $data->sum(fn($m) => $m['rooms'] + $m['fnb'] + $m['tickets']);

        return view('admin.reports.revenue', compact('data', 'year', 'total'));
    }

    public function guests(Request $request): View
    {
        $guests = \App\Models\Guest::withCount('bookings')
            ->withSum(['payments as total_spent' => fn($q) => $q->where('status', 'completed')], 'amount')
            ->orderByDesc('total_spent')
            ->paginate(30);

        return view('admin.reports.guests', compact('guests'));
    }
}
