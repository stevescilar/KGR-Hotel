<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\View\View;

class SettingsController extends Controller
{
    private array $groups = [
        'hotel'    => ['hotel_name', 'hotel_phone', 'hotel_email', 'hotel_address', 'hotel_website'],
        'policy'   => ['checkin_time', 'checkout_time', 'cancellation_hours', 'child_age_limit'],
        'billing'  => ['vat_rate', 'loyalty_rate', 'currency', 'currency_symbol'],
        'mpesa'    => ['mpesa_shortcode', 'mpesa_account_ref', 'mpesa_sandbox'],
        'sms'      => ['at_sender_id', 'sms_booking_confirm', 'sms_checkin_reminder'],
        'social'   => ['facebook_url', 'instagram_url', 'twitter_url', 'whatsapp_number'],
    ];

    public function index(): View
    {
        $settings = Setting::all()->pluck('value', 'key');
        $groups   = $this->groups;

        return view('admin.settings.index', compact('settings', 'groups'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Settings saved successfully.');
    }
}
