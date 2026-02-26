<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class GuestController extends Controller
{
    public function index(Request $request): View
    {
        $guests = Guest::withCount('bookings')
            ->withSum(['payments as total_spent' => fn($q) => $q->where('status', 'completed')], 'amount')
            ->when($request->search, fn($q) => $q
                ->where('email', 'like', "%{$request->search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$request->search}%"])
                ->orWhere('phone', 'like', "%{$request->search}%")
            )
            ->when($request->tier, fn($q) => $q->where('vip_tier', $request->tier))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.guests.index', compact('guests'));
    }

    public function show(Guest $guest): View
    {
        $guest->load([
            'bookings.room.roomType',
            'loyaltyTransactions' => fn($q) => $q->latest()->take(20),
        ]);

        return view('admin.guests.show', compact('guest'));
    }

    public function edit(Guest $guest): View
    {
        return view('admin.guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest): RedirectResponse
    {
        $data = $request->validate([
            'first_name'  => 'required|string|max:80',
            'last_name'   => 'required|string|max:80',
            'email'       => "required|email|unique:guests,email,{$guest->id}",
            'phone'       => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:60',
            'address'     => 'nullable|string',
            'vip_tier'    => 'required|in:none,bronze,silver,gold',
        ]);

        $guest->update($data);

        return redirect()->route('admin.guests.show', $guest)
            ->with('success', 'Guest profile updated.');
    }

    public function destroy(Guest $guest): RedirectResponse
    {
        $guest->delete();
        return redirect()->route('admin.guests.index')
            ->with('success', 'Guest record deleted.');
    }

    public function loyalty(Guest $guest): View
    {
        $transactions = $guest->loyaltyTransactions()->latest()->paginate(30);
        return view('admin.guests.loyalty', compact('guest', 'transactions'));
    }
}
