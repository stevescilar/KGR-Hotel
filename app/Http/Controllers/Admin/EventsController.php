<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventBooking;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class EventsController extends Controller
{
    public function index(): View
    {
        $eventBookings = EventBooking::with('package')
            ->orderBy('event_date')
            ->paginate(20);

        return view('admin.events.index', compact('eventBookings'));
    }

    public function show(EventBooking $event): View
    {
        return view('admin.events.show', compact('event'));
    }

    public function updateStatus(EventBooking $event, Request $request): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:inquiry,quoted,confirmed,completed,cancelled',
            'notes'  => 'nullable|string|max:1000',
            'quoted_amount'  => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
        ]);

        $event->update($request->only('status', 'notes', 'quoted_amount', 'deposit_amount'));

        return back()->with('success', "Event status updated to {$request->status}.");
    }
}
