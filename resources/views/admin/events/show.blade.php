@extends('layouts.admin')
@section('title', $event->reference)
@section('page-title', 'Event · ' . $event->reference)
@section('breadcrumb', 'Events / ' . $event->reference)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl">

    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">Event Details</h3>
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['Event Type',  $event->event_type],
                    ['Package',     $event->package?->name ?? '— None selected'],
                    ['Event Date',  $event->event_date->format('l, F j Y')],
                    ['Guest Count', number_format($event->guest_count) . ' guests'],
                    ['Contact',     $event->contact_name],
                    ['Phone',       $event->contact_phone],
                    ['Email',       $event->contact_email],
                    ['Submitted',   $event->created_at->format('M j, Y')],
                ] as [$label, $value])
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ $label }}</div>
                    <div class="font-semibold text-gray-800 text-sm">{{ $value }}</div>
                </div>
                @endforeach
            </div>
            @if($event->requirements)
            <div class="mt-4 bg-amber-50 border border-amber-100 rounded-lg p-4">
                <div class="text-xs text-amber-700 font-semibold uppercase tracking-wide mb-1">Requirements</div>
                <div class="text-sm text-gray-700">{{ $event->requirements }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="space-y-5">
        {{-- Status update --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">Update Status</h3>
            <form method="POST" action="{{ route('admin.events.status', $event) }}" class="space-y-3">
                @csrf @method('PATCH')
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                    @foreach(['inquiry','quoted','confirmed','completed','cancelled'] as $s)
                        <option value="{{ $s }}" @selected($event->status === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Quoted Amount (KES)</label>
                    <input type="number" name="quoted_amount" value="{{ $event->quoted_amount }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Deposit (KES)</label>
                    <input type="number" name="deposit_amount" value="{{ $event->deposit_amount }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060] resize-none">{{ $event->notes }}</textarea>
                </div>
                <button type="submit"
                        class="w-full bg-[#1e3a2f] text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-[#2e5c42] transition-colors">
                    Save Update
                </button>
            </form>
        </div>

        {{-- Quick actions --}}
        <div class="bg-gray-50 rounded-xl p-4 space-y-2">
            <a href="tel:{{ $event->contact_phone }}"
               class="flex items-center gap-2 text-sm text-gray-700 hover:text-[#1e3a2f] font-medium">
               📞 Call {{ $event->contact_name }}
            </a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $event->contact_phone) }}" target="_blank"
               class="flex items-center gap-2 text-sm text-gray-700 hover:text-[#1e3a2f] font-medium">
               💬 WhatsApp
            </a>
            <a href="mailto:{{ $event->contact_email }}"
               class="flex items-center gap-2 text-sm text-gray-700 hover:text-[#1e3a2f] font-medium">
               ✉️ Send Email
            </a>
        </div>
    </div>
</div>
@endsection
