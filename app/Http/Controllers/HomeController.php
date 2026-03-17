<?php

namespace App\Http\Controllers;

use App\Models\{RoomType, EventPackage};
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $roomTypes   = RoomType::where('is_active', true)
            ->withCount('rooms')
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        $eventPackages = EventPackage::where('is_active', true)
            ->take(3)
            ->get();

        return view('public.home', compact('roomTypes', 'eventPackages'));
    }

    public function rooms(): View
    {
        $roomTypes = RoomType::where('is_active', true)
            ->withCount(['rooms' => fn($q) => $q->where('status', 'available')])
            ->orderBy('sort_order')
            ->get();

        return view('public.rooms.index', compact('roomTypes'));
    }

    public function roomType(RoomType $roomType): View
    {
        abort_unless($roomType->is_active, 404);

        $roomType->loadCount('rooms');

        $relatedTypes = RoomType::where('is_active', true)
            ->where('id', '!=', $roomType->id)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('public.rooms.show', [
            'roomType'     => $roomType,
            'relatedTypes' => $relatedTypes,
        ]);
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function contactSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name'  => 'required|string|max:80',
            'email'      => 'required|email',
            'subject'    => 'required|string',
            'message'    => 'required|string|max:3000',
        ]);

        \Illuminate\Support\Facades\Log::info('Contact form submission', $request->only(['first_name','last_name','email','subject','message']));

        return back()->with('success', "Thank you! Your message has been received. We'll get back to you within 24 hours.");
    }
}