<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $categories = MenuCategory::where('is_active', true)
            ->with(['items' => fn($q) => $q->where('is_available', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('public.menu', compact('categories'));
    }

    /**
     * Mobile-optimised menu page — linked from QR code on homepage.
     */
    public function mobile(): View
    {
        $categories = MenuCategory::where('is_active', true)
            ->with(['items' => fn($q) => $q->where('is_available', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('public.menu-mobile', compact('categories'));
    }
}