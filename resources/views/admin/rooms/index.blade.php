@extends('layouts.admin')
@section('title', 'Rooms')

@section('content')
<div class="p-6 space-y-5">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-display text-gray-900">Rooms</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.rooms.housekeeper') }}"
               class="border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50">
                🧹 Housekeeping
            </a>
            <a href="{{ route('admin.rooms.walk-in') }}"
               class="bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-amber-600">
                🚶 Walk-in Booking
            </a>
            <a href="{{ route('admin.rooms.create') }}"
               class="bg-green-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-800">
                + Add Room
            </a>
        </div>
    </div>

    {{-- Summary bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $statusCounts = $rooms->groupBy('status')->map->count();
        $statusMeta = [
            'available'   => ['label' => 'Available',   'icon' => '🟢', 'bg' => 'bg-green-50',  'text' => 'text-green-700'],
            'occupied'    => ['label' => 'Occupied',    'icon' => '🔴', 'bg' => 'bg-red-50',    'text' => 'text-red-700'],
            'cleaning'    => ['label' => 'Cleaning',    'icon' => '🧹', 'bg' => 'bg-amber-50',  'text' => 'text-amber-700'],
            'maintenance' => ['label' => 'Maintenance', 'icon' => '🔧', 'bg' => 'bg-gray-50',   'text' => 'text-gray-600'],
        ];
        @endphp
        @foreach($statusMeta as $status => $meta)
        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-center gap-3">
            <span class="text-2xl">{{ $meta['icon'] }}</span>
            <div>
                <div class="text-2xl font-bold font-display text-gray-800">{{ $statusCounts[$status] ?? 0 }}</div>
                <div class="text-xs text-gray-400 font-medium uppercase tracking-wide">{{ $meta['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Room type tabs --}}
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-1 p-4 border-b border-gray-100 overflow-x-auto">
            <button onclick="filterType('all')" id="tab-all"
                    class="tab-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap bg-[#1e3a2f] text-white">
                All Rooms
            </button>
            @foreach($roomTypes as $type)
            <button onclick="filterType('{{ $type->id }}')" id="tab-{{ $type->id }}"
                    class="tab-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap text-gray-500 hover:bg-gray-100">
                {{ $type->name }} <span class="text-xs opacity-60">({{ $type->rooms_count }})</span>
            </button>
            @endforeach
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="roomsGrid">
            @forelse($rooms as $room)
            @php
            $statusStyle = [
                'available'   => 'border-green-200 bg-green-50/40',
                'occupied'    => 'border-red-200 bg-red-50/40',
                'cleaning'    => 'border-amber-200 bg-amber-50/40',
                'maintenance' => 'border-gray-200 bg-gray-50',
            ][$room->status] ?? 'border-gray-100';
            $dotColor = [
                'available'   => 'bg-green-400',
                'occupied'    => 'bg-red-400',
                'cleaning'    => 'bg-amber-400',
                'maintenance' => 'bg-gray-400',
            ][$room->status] ?? 'bg-gray-300';
            @endphp
            <div class="room-card border-2 rounded-xl overflow-hidden {{ $statusStyle }}" data-type="{{ $room->room_type_id }}">
                {{-- Room image --}}
                @if($room->image)
                    <img src="{{ Storage::url($room->image) }}" alt="Room {{ $room->room_number }}"
                         class="w-full h-32 object-cover">
                @endif
                <div class="p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }} flex-shrink-0"></span>
                                <span class="font-bold text-gray-800 text-lg">{{ $room->room_number }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $room->roomType->name }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.rooms.edit', $room) }}"
                               class="text-xs text-green-700 font-semibold hover:text-green-900">Edit</a>
                            <span class="text-gray-200">|</span>
                            <div class="text-right">
                                <div class="text-xs text-gray-400">{{ $room->floor ? 'Floor ' . $room->floor : ($room->cottage ?? '') }}</div>
                                <div class="text-xs font-semibold text-gray-600 capitalize">{{ str_replace('_', ' ', $room->status) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Active booking --}}
                    @if($room->status === 'occupied')
                    @php $activeBooking = $room->bookings()->where('status','checked_in')->with('guest')->first() @endphp
                    @if($activeBooking)
                    <div class="bg-white/60 rounded-lg p-2.5 mb-3 text-xs">
                        <div class="font-semibold text-gray-700">{{ $activeBooking->guest->first_name }} {{ $activeBooking->guest->last_name }}</div>
                        <div class="text-gray-400">Until {{ \Carbon\Carbon::parse($activeBooking->check_out)->format('M j') }}</div>
                    </div>
                    @endif
                    @endif

                    {{-- Status update --}}
                    <form method="POST" action="{{ route('admin.rooms.status', $room) }}" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <select name="status" class="flex-1 border border-gray-200 bg-white rounded-lg px-2 py-1.5 text-xs outline-none focus:border-[#4a8060]">
                            @foreach(['available','occupied','cleaning','maintenance'] as $s)
                                <option value="{{ $s }}" @selected($room->status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-[#1e3a2f] text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-[#2e5c42]">
                            Update
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 text-gray-400">
                <div class="text-4xl mb-3">🛏</div>
                <p class="mb-3">No rooms yet.</p>
                <a href="{{ route('admin.rooms.create') }}" class="text-green-700 font-semibold underline">Add your first room</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Room types management link --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-center justify-between">
        <div>
            <div class="font-semibold text-gray-700 text-sm">Room Types</div>
            <div class="text-xs text-gray-400 mt-0.5">Manage categories like Standard, Deluxe, Presidential…</div>
        </div>
        <a href="{{ route('admin.rooms.types.index') }}" class="text-sm text-green-700 font-semibold hover:text-green-900">
            Manage Types →
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterType(typeId) {
    document.querySelectorAll('.room-card').forEach(card => {
        card.style.display = typeId === 'all' || card.dataset.type === typeId ? '' : 'none';
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        const active = btn.id === 'tab-' + typeId;
        btn.classList.toggle('bg-[#1e3a2f]', active);
        btn.classList.toggle('text-white', active);
        btn.classList.toggle('text-gray-500', !active);
    });
}
</script>
@endpush