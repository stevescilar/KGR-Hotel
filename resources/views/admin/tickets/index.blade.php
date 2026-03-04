@extends('layouts.admin')
@section('title', 'Gate Tickets')
@section('page-title', 'Gate Tickets')
@section('breadcrumb', 'Ticket sales & gate scanner')

@section('content')
<div class="space-y-5">

    {{-- Stats + Scanner button --}}
    <div class="flex flex-wrap items-center gap-4">
        <div class="bg-white rounded-xl border border-gray-100 px-5 py-4 flex items-center gap-3">
            <span class="text-2xl">🎫</span>
            <div>
                <div class="text-2xl font-bold font-display text-gray-800">{{ number_format($todayCount) }}</div>
                <div class="text-xs text-gray-400 uppercase tracking-wide">Today's Visitors</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 px-5 py-4 flex items-center gap-3">
            <span class="text-2xl">💵</span>
            <div>
                <div class="text-2xl font-bold font-display text-gray-800">KES {{ number_format($todayRevenue) }}</div>
                <div class="text-xs text-gray-400 uppercase tracking-wide">Today's Revenue</div>
            </div>
        </div>
        <a href="{{ route('admin.tickets.scan') }}"
           class="ml-auto bg-[#1e3a2f] text-white px-5 py-3 rounded-xl text-sm font-semibold hover:bg-[#2e5c42] transition-colors flex items-center gap-2">
            📷 Gate Scanner
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="date" name="date" value="{{ request('date', today()->toDateString()) }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060]"
                   onchange="this.form.submit()">
            <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060]"
                    onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['active','used','expired','cancelled'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Ticket #</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Guest</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Visit Date</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3.5 font-mono-kgr text-xs text-gray-600">{{ $ticket->ticket_number }}</td>
                    <td class="px-5 py-3.5">
                        <div class="font-medium text-gray-800 text-sm">{{ $ticket->guest_name }}</div>
                        <div class="text-xs text-gray-400">{{ $ticket->guest_phone }}</div>
                    </td>
                    <td class="px-5 py-3.5 text-gray-700">{{ $ticket->ticketType->name }}</td>
                    <td class="px-5 py-3.5 text-gray-700">{{ $ticket->visit_date->format('M j, Y') }}</td>
                    <td class="px-5 py-3.5 font-semibold text-gray-700">{{ $ticket->quantity }}</td>
                    <td class="px-5 py-3.5 font-semibold text-gray-800">KES {{ number_format($ticket->total_price) }}</td>
                    <td class="px-5 py-3.5">
                        <span @class(['text-xs px-2.5 py-1 rounded-full font-semibold',
                            'bg-green-100 text-green-700' => $ticket->status === 'active',
                            'bg-gray-100 text-gray-500'   => $ticket->status === 'used',
                            'bg-red-100 text-red-600'     => in_array($ticket->status, ['expired','cancelled']),
                        ])>
                            {{ $ticket->status === 'used' && $ticket->scanned_at ? '✓ ' : '' }}{{ ucfirst($ticket->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <div class="text-4xl mb-2">🎫</div>No tickets for this date
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($tickets->hasPages())
            <div class="px-5 py-4 border-t border-gray-50">{{ $tickets->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
