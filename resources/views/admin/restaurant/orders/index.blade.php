@extends('layouts.admin')
@section('title', 'Restaurant Orders')
@section('page-title', 'Restaurant Orders')
@section('breadcrumb', 'Restaurant / Orders')

@section('content')
<div class="space-y-4">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['Open Orders',      $openCount,                           '🔴', 'bg-red-50 text-red-700'],
            ["Today's Revenue",  'KES ' . number_format($todayRevenue), '💵', 'bg-green-50 text-green-700'],
        ] as [$label, $value, $icon, $style])
        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-center gap-3">
            <span class="text-2xl">{{ $icon }}</span>
            <div>
                <div class="text-xl font-bold font-display text-gray-800">{{ $value }}</div>
                <div class="text-xs text-gray-400 uppercase tracking-wide">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060]"
                    onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach(['open','preparing','served','billed','paid','cancelled'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060]"
                    onchange="this.form.submit()">
                <option value="">All Types</option>
                @foreach(['dine_in','room_service','takeaway'] as $t)
                    <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                @endforeach
            </select>
            @if(request()->hasAny(['status','type']))
                <a href="{{ route('admin.restaurant.orders') }}" class="text-sm text-gray-400 hover:text-gray-600 self-center">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Order #</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Table / Room</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Items</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Time</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3.5 font-mono-kgr text-xs text-gray-600 font-medium">{{ $order->order_number }}</td>
                    <td class="px-5 py-3.5">
                        @if($order->table)
                            <div class="font-medium text-gray-800">Table {{ $order->table->table_number }}</div>
                            <div class="text-xs text-gray-400">{{ ucfirst($order->table->section) }}</div>
                        @elseif($order->booking)
                            <div class="font-medium text-gray-800">Room {{ $order->booking->room->room_number }}</div>
                            <div class="text-xs text-gray-400">Room Service</div>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-gray-600">{{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}</td>
                    <td class="px-5 py-3.5 font-semibold text-gray-800">KES {{ number_format($order->total) }}</td>
                    <td class="px-5 py-3.5">
                        <form method="POST" action="{{ route('admin.restaurant.orders.status', $order) }}" class="inline">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()"
                                    class="border border-gray-200 rounded-lg px-2 py-1 text-xs outline-none focus:border-[#4a8060]">
                                @foreach(['open','preparing','served','billed','paid','cancelled'] as $s)
                                    <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</td>
                    <td class="px-5 py-3.5">
                        <a href="{{ route('admin.restaurant.orders.show', $order) }}" class="text-[#4a8060] text-xs font-semibold hover:underline">View →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <div class="text-4xl mb-2">🍽</div>No orders found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($orders->hasPages())
            <div class="px-5 py-4 border-t border-gray-50">{{ $orders->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
