<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{MenuItem, MenuCategory};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(): View
    {
        $categories = MenuCategory::with(['items' => fn($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')->get();
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
            'is_available'     => 'boolean',
            'is_featured'      => 'boolean',
            'sort_order'       => 'integer|min:0',
            'image'            => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

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
            'is_available'     => 'boolean',
            'is_featured'      => 'boolean',
            'sort_order'       => 'integer|min:0',
            'image'            => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) Storage::disk('public')->delete($menu->image);
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        $menu->update($data);

        return redirect()->route('admin.restaurant.menu.index')
            ->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        if ($menu->image) Storage::disk('public')->delete($menu->image);
        $menu->delete();
        return redirect()->route('admin.restaurant.menu.index')
            ->with('success', 'Menu item removed.');
    }

    // ── Category management ───────────────────────────────────

    public function createCategory(): View
    {
        return view('admin.restaurant.menu.category-create');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:80',
            'description' => 'nullable|string',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu/categories', 'public');
        }

        MenuCategory::create($data);

        return redirect()->route('admin.restaurant.menu.index')
            ->with('success', "Category '{$data['name']}' created.");
    }
}