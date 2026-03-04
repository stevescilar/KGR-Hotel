@extends('layouts.admin')
@section('title', 'Events')
@section('page-title', 'Events & Bookings')
@section('breadcrumb', 'Event inquiries and confirmations')

@section('content')
<div class="space-y-4">

    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-display text-lg text-[#1e3a2f]">Event Bookings</h3>
            <a href="{{ route('admin.events.packages.index') }}"
               class="text-xs text-[#4a8060] border border-[#4a8060] px-3 py-1.5 rounded-lg hover:bg-[#4a8060] hover:text-white transition-colors font-semibold">
                Manage Packages
            </a>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Reference</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Contact</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Event</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Guests</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($eventBookings as $event)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-4 font-mono-kgr text-xs text-gray-600 font-medium">{{ $event->reference }}</td>
                    <td class="px-5 py-4">
                        <div class="font-medium text-gray-800">{{ $event->contact_name }}</div>
                        <div class="text-xs text-gray-400">{{ $event->contact_phone }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="font-medium text-gray-700">{{ $event->event_type }}</div>
                        @if($event->package)
                        <div class="text-xs text-gray-400">{{ $event->package->name }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-700">{{ $event->event_date->format('M j, Y') }}</td>
                    <td class="px-5 py-4 text-gray-700">{{ number_format($event->guest_count) }}</td>
                    <td class="px-5 py-4">
                        <span @class(['text-xs px-2.5 py-1 rounded-full font-semibold',
                            'bg-amber-100 text-amber-700'  => $event->status === 'inquiry',
                            'bg-blue-100 text-blue-700'    => $event->status === 'quoted',
                            'bg-green-100 text-green-700'  => $event->status === 'confirmed',
                            'bg-gray-100 text-gray-500'    => $event->status === 'completed',
                            'bg-red-100 text-red-600'      => $event->status === 'cancelled',
                        ])>{{ ucfirst($event->status) }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('admin.events.show', $event) }}" class="text-[#4a8060] text-xs font-semibold hover:underline">View →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <div class="text-4xl mb-2">🎪</div>No event bookings yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($eventBookings->hasPages())
            <div class="px-5 py-4 border-t border-gray-50">{{ $eventBookings->links() }}</div>
        @endif
    </div>
</div>
@endsection
