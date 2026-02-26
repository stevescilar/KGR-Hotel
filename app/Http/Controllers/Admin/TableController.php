<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Table, TableReservation};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(): View
    {
        $tables = Table::withCount([
            'reservations as todays_reservations' => fn($q) => $q->whereDate('reserved_at', today()),
            'orders as open_orders' => fn($q) => $q->whereIn('status', ['open', 'preparing']),
        ])->orderBy('section')->orderBy('table_number')->get();

        return view('admin.restaurant.tables.index', compact('tables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'table_number' => 'required|string|unique:tables,table_number',
            'capacity'     => 'required|integer|min:1',
            'section'      => 'required|in:garden,indoor,poolside,vip',
        ]);

        Table::create($data);

        return redirect()->route('admin.restaurant.tables.index')
            ->with('success', "Table {$data['table_number']} added.");
    }

    public function update(Request $request, Table $table): RedirectResponse
    {
        $data = $request->validate([
            'capacity' => 'required|integer|min:1',
            'section'  => 'required|in:garden,indoor,poolside,vip',
            'status'   => 'required|in:available,occupied,reserved,maintenance',
        ]);

        $table->update($data);

        return back()->with('success', "Table {$table->table_number} updated.");
    }

    public function destroy(Table $table): RedirectResponse
    {
        $table->delete();
        return redirect()->route('admin.restaurant.tables.index')
            ->with('success', 'Table removed.');
    }

    public function reservations(Request $request): View
    {
        $reservations = TableReservation::with('table')
            ->when($request->date, fn($q) => $q->whereDate('reserved_at', $request->date))
            ->orderBy('reserved_at')
            ->paginate(30)
            ->withQueryString();

        return view('admin.restaurant.reservations', compact('reservations'));
    }
}
