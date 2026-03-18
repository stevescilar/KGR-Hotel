<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class TicketTypeController extends Controller
{
    public function index(): View
    {
        $ticketTypes = TicketType::orderBy('price')->get();
        return view('admin.tickets.types.index', compact('ticketTypes'));
    }

    public function create(): View
    {
        return view('admin.tickets.types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'is_active'   => 'boolean',
        ]);

        TicketType::create($data);

        return redirect()->route('admin.tickets.types.index')
            ->with('success', "Ticket type '{$data['name']}' created.");
    }

    public function edit(TicketType $ticketType): View
    {
        return view('admin.tickets.types.edit', compact('ticketType'));
    }

    public function update(Request $request, TicketType $ticketType): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'is_active'   => 'boolean',
        ]);

        $ticketType->update($data);

        return redirect()->route('admin.tickets.types.index')
            ->with('success', 'Ticket type updated.');
    }

    public function destroy(TicketType $ticketType): RedirectResponse
    {
        $ticketType->delete();
        return redirect()->route('admin.tickets.types.index')
            ->with('success', 'Ticket type deleted.');
    }
}