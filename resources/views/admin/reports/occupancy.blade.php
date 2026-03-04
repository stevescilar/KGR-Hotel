@extends('layouts.admin')
@section('title', 'Occupancy Report')
@section('page-title', 'Occupancy Report')
@section('breadcrumb', 'Reports / Occupancy')

@section('content')
<div class="space-y-5">

    {{-- Month picker --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-center gap-4">
        <form method="GET" class="flex items-center gap-3">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Month</label>
            <input type="month" name="month" value="{{ $month }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060]"
                   onchange="this.form.submit()">
        </form>
        <a href="{{ route('admin.reports.revenue') }}" class="ml-auto text-xs text-[#4a8060] font-semibold hover:underline">
            Revenue Report →
        </a>
    </div>

    {{-- Headline stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach([
            ['Occupancy Rate', $occupancyRate . '%', '📊'],
            ['Occupied Nights', number_format($occupiedNights), '🛏'],
            ['Total Capacity', number_format($capacity) . ' room-nights', '🏠'],
        ] as [$label, $value, $icon])
        <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
            <span class="text-3xl">{{ $icon }}</span>
            <div>
                <div class="text-3xl font-bold font-display text-[#1e3a2f]">{{ $value }}</div>
                <div class="text-xs text-gray-400 uppercase tracking-wide">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Occupancy gauge --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-display text-lg text-[#1e3a2f]">Overall Occupancy</h3>
            <span class="text-2xl font-bold text-[#1e3a2f]">{{ $occupancyRate }}%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-4">
            <div class="h-4 rounded-full transition-all duration-700
                        {{ $occupancyRate >= 80 ? 'bg-green-500' : ($occupancyRate >= 50 ? 'bg-amber-500' : 'bg-red-400') }}"
                 style="width: {{ $occupancyRate }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mt-1.5">
            <span>0%</span><span>50%</span><span>100%</span>
        </div>
    </div>

    {{-- By room type --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-display text-lg text-[#1e3a2f]">By Room Type</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Room Type</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Rooms</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Bookings</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($byRoomType as $row)
                <tr>
                    <td class="px-5 py-3.5 font-medium text-gray-800">{{ $row['type'] }}</td>
                    <td class="px-5 py-3.5 text-gray-600">{{ $row['total_rooms'] }}</td>
                    <td class="px-5 py-3.5 font-semibold text-gray-800">{{ $row['bookings'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
