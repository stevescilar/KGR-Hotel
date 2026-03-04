@extends('layouts.admin')
@section('title', 'Settings')
@section('page-title', 'Settings')
@section('breadcrumb', 'System settings')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
        @csrf

        @php
        $groupLabels = [
            'hotel'  => ['🏨', 'Hotel Information'],
            'policy' => ['📋', 'Policies'],
            'billing'=> ['💳', 'Billing & Finance'],
            'mpesa'  => ['📱', 'M-Pesa'],
            'sms'    => ['💬', 'SMS / Notifications'],
            'social' => ['🌐', 'Social Media'],
        ];
        $friendlyKeys = [
            'hotel_name' => 'Hotel Name', 'hotel_email' => 'Email', 'hotel_phone' => 'Phone',
            'hotel_address' => 'Address', 'hotel_website' => 'Website',
            'checkin_time' => 'Check-in Time', 'checkout_time' => 'Check-out Time',
            'cancellation_hours' => 'Free Cancellation (hours)', 'child_age_limit' => 'Child Age Limit',
            'vat_rate' => 'VAT Rate (%)', 'loyalty_rate' => 'Loyalty Rate (pts per KES 100)', 'currency' => 'Currency', 'currency_symbol' => 'Symbol',
            'mpesa_shortcode' => 'Shortcode', 'mpesa_account_ref' => 'Account Reference', 'mpesa_sandbox' => 'Sandbox Mode',
            'at_sender_id' => 'SMS Sender ID', 'sms_booking_confirm' => 'Booking Confirmation SMS', 'sms_checkin_reminder' => 'Check-in Reminder SMS',
            'facebook_url' => 'Facebook', 'instagram_url' => 'Instagram', 'twitter_url' => 'Twitter / X', 'whatsapp_number' => 'WhatsApp Number',
        ];
        @endphp

        @foreach($groups as $groupKey => $keys)
        @php [$icon, $title] = $groupLabels[$groupKey] @endphp
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4 flex items-center gap-2">
                <span>{{ $icon }}</span> {{ $title }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($keys as $key)
                <div class="{{ in_array($key, ['hotel_address','sms_booking_confirm','sms_checkin_reminder']) ? 'md:col-span-2' : '' }}">
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">
                        {{ $friendlyKeys[$key] ?? ucwords(str_replace('_', ' ', $key)) }}
                    </label>
                    @if(in_array($key, ['hotel_address','sms_booking_confirm','sms_checkin_reminder']))
                        <textarea name="{{ $key }}" rows="2"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060] resize-none">{{ $settings[$key] ?? '' }}</textarea>
                    @elseif($key === 'mpesa_sandbox')
                        <select name="{{ $key }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                            <option value="1" @selected(($settings[$key] ?? '1') == '1')>Enabled (sandbox)</option>
                            <option value="0" @selected(($settings[$key] ?? '1') == '0')>Disabled (live)</option>
                        </select>
                    @else
                        <input type="text" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-[#1e3a2f] text-white px-8 py-3 rounded-xl font-semibold text-sm hover:bg-[#2e5c42] transition-colors">
                Save All Settings
            </button>
        </div>
    </form>
</div>
@endsection
