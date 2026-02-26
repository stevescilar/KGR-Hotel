<?php

namespace App\Http\Controllers;

use App\Models\{RoomType, EventPackage};
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $roomTypes      = RoomType::where('is_active', true)->orderBy('sort_order')->get();
        $eventPackages  = EventPackage::where('is_active', true)->take(3)->get();

        return view('public.home', compact('roomTypes', 'eventPackages'));
    }

    public function rooms(): View
    {
        $roomTypes = RoomType::where('is_active', true)
            ->withCount('rooms')
            ->orderBy('sort_order')
            ->get();

        return view('public.rooms.index', compact('roomTypes'));
    }

    public function roomType(RoomType $roomType): View
    {
        abort_unless($roomType->is_active, 404);

        $roomType->load('pricingRules');

        $related = RoomType::where('is_active', true)
            ->where('id', '!=', $roomType->id)
            ->orderBy('sort_order')
            ->take(2)
            ->get();

        return view('public.rooms.show', compact('roomType', 'related'));
    }

    public function contact(): View
    {
        return view('public.contact');
    }
}
