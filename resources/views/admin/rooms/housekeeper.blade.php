@extends('layouts.admin')
@section('title', 'Housekeeping')
@section('page-title', 'Housekeeping')
@section('breadcrumb', 'Rooms / Housekeeping view')

@section('content')
<div class="space-y-5">

    {{-- Priority: Cleaning first, then available, occupied, maintenance --}}
    @php
    $grouped = $rooms->groupBy('status');
    $order   = ['cleaning', 'maintenance', 'available', 'occupied'];
    @endphp

    @foreach($order as $status)
    @if($grouped->has($status))
    @php
    $labels = ['cleaning' => '🧹 Needs Cleaning', 'maintenance' => '🔧 Maintenance', 'available' => '✓ Ready', 'occupied' => '🛏 Occupied'];
    $bgHead = ['cleaning' => 'bg-amber-500', 'maintenance' => 'bg-gray-500', 'available' => 'bg-green-500', 'occupied' => 'bg-red-500'];
    @endphp
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 {{ $bgHead[$status] }} flex items-center justify-between">
            <h3 class="text-white font-semibold">{{ $labels[$status] }}</h3>
            <span class="bg-white/20 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $grouped[$status]->count() }}</span>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($grouped[$status] as $room)
            <div class="flex items-center gap-4 px-5 py-3">
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center font-bold text-gray-700 font-mono-kgr flex-shrink-0">
                    {{ $room->room_number }}
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-800 text-sm">{{ $room->roomType->name }}</div>
                    <div class="text-xs text-gray-400">
                        {{ $room->floor ? 'Floor ' . $room->floor : '' }}
                        {{ $room->cottage ? ' · ' . $room->cottage : '' }}
                    </div>
                    @if($status === 'occupied' && $room->bookings->first())
                    @php $b = $room->bookings->first() @endphp
                    <div class="text-xs text-gray-500 mt-0.5">
                        {{ $b->guest->full_name }} · checks out {{ $b->check_out->format('M j') }}
                    </div>
                    @endif
                </div>
                @if(in_array($status, ['cleaning', 'maintenance']))
                <form method="POST" action="{{ route('admin.rooms.status', $room) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="available">
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                        Mark Ready
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach
</div>
@endsection
