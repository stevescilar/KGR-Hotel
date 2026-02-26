<?php

namespace App\Http\Controllers;

use App\Models\{EventPackage, EventBooking};
use App\Services\SmsService;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $packages = EventPackage::where('is_active', true)->get();
        return view('public.events.index', compact('packages'));
    }

    public function packages(): View
    {
        $packages = EventPackage::where('is_active', true)->get();
        return view('public.events.packages', compact('packages'));
    }

    public function inquire(Request $request): RedirectResponse
    {
        $request->validate([
            'event_type'    => 'required|string|max:80',
            'contact_name'  => 'required|string|max:100',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'event_date'    => 'required|date|after:today',
            'guest_count'   => 'required|integer|min:1',
            'requirements'  => 'nullable|string|max:2000',
            'package_id'    => 'nullable|exists:event_packages,id',
        ]);

        $booking = EventBooking::create([
            'event_package_id' => $request->package_id,
            'event_type'       => $request->event_type,
            'contact_name'     => $request->contact_name,
            'contact_email'    => $request->contact_email,
            'contact_phone'    => $request->contact_phone,
            'event_date'       => $request->event_date,
            'guest_count'      => $request->guest_count,
            'requirements'     => $request->requirements,
            'status'           => 'inquiry',
        ]);

        // SMS to client
        app(SmsService::class)->send(
            $request->contact_phone,
            "Hi {$request->contact_name}! We've received your event inquiry (Ref: {$booking->reference}). Our team will contact you within 24 hours. Kitonga Garden Resort."
        );

        return back()->with('success', "Thank you! Your inquiry has been submitted. Reference: {$booking->reference}. We'll be in touch within 24 hours.");
    }
}
