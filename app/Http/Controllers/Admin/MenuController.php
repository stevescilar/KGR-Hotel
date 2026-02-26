<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{MenuItem, MenuCategory};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $categories = MenuCategory::with(['items' => fn($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('admin.restaurant.menu.index', compact('categories'));
    }

    public function create(): View
    {
        $categories = MenuCategory::orderBy('name')->get();
        return view('admin.restaurant.menu.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name'             => 'required|string|max:120',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'tags'             => 'nullable|array',
            'is_available'     => 'boolean',
            'is_featured'      => 'boolean',
            'sort_order'       => 'integer|min:0',
        ]);

        MenuItem::create($data);

        return redirect()->route('admin.restaurant.menu.index')
            ->with('success', "Menu item '{$data['name']}' added.");
    }

    public function edit(MenuItem $menu): View
    {
        $categories = MenuCategory::orderBy('name')->get();
        return view('admin.restaurant.menu.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, MenuItem $menu): RedirectResponse
    {
        $data = $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name'             => 'required|string|max:120',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'tags'             => 'nullable|array',
            'is_available'     => 'boolean',
            'is_featured'      => 'boolean',
            'sort_order'       => 'integer|min:0',
        ]);

        $menu->update($data);

        return redirect()->route('admin.restaurant.menu.index')
            ->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        $menu->delete();
        return redirect()->route('admin.restaurant.menu.index')
            ->with('success', 'Menu item removed.');
    }
}
