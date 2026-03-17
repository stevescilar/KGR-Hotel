@extends('layouts.admin')
@section('title', 'Room Types')

@section('content')
<div class="p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display text-gray-900">Room Types & Pricing</h1>
            <p class="text-sm text-gray-500 mt-1">Manage room categories and their nightly rates</p>
        </div>
        <a href="{{ route('admin.rooms.types.create') }}"
           class="bg-green-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-800">
            + Add Room Type
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 text-sm">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-4">
        @forelse($roomTypes as $type)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-5 p-5">

                {{-- Status dot --}}
                <div class="w-3 h-3 rounded-full flex-shrink-0 {{ $type->is_active ? 'bg-green-400' : 'bg-gray-300' }}"></div>

                {{-- Name + meta --}}
                <div class="flex-1 min-w-0">
                    <div class="font-display text-lg text-gray-900">{{ $type->name }}</div>
                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-400 flex-wrap">
                        <span>{{ $type->rooms_count }} room{{ $type->rooms_count !== 1 ? 's' : '' }}</span>
                        <span>·</span>
                        <span>Up to {{ $type->max_adults }} adults</span>
                        @if($type->weekend_price)
                        <span>·</span>
                        <span>Weekend: KES {{ number_format($type->weekend_price) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Price — prominent --}}
                <div class="text-right flex-shrink-0">
                    <div class="text-xs text-gray-400 uppercase tracking-wide">Base rate</div>
                    <div class="font-display text-2xl text-green-900 font-semibold">
                        KES {{ number_format($type->base_price) }}
                    </div>
                    <div class="text-xs text-gray-400">per night</div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('admin.rooms.types.edit', $type) }}"
                       class="bg-green-900 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-green-800 transition-colors">
                        Edit / Change Price
                    </a>
                </div>
            </div>

            {{-- Quick price update inline --}}
            <div class="border-t border-gray-50 bg-gray-50 px-5 py-3" x-data="{ open: false }">
                <button @click="open = !open"
                        class="text-xs text-green-700 font-semibold hover:text-green-900 flex items-center gap-1">
                    <span x-text="open ? '▲ Hide quick update' : '▼ Quick price update'"></span>
                </button>
                <div x-show="open" x-transition class="mt-3">
                    <form method="POST" action="{{ route('admin.rooms.types.update', $type) }}"
                          class="flex items-end gap-3">
                        @csrf @method('PUT')
                        {{-- Pass all required fields as hidden so validation passes --}}
                        <input type="hidden" name="name"          value="{{ $type->name }}">
                        <input type="hidden" name="description"   value="{{ $type->description }}">
                        <input type="hidden" name="max_adults"    value="{{ $type->max_adults }}">
                        <input type="hidden" name="max_children"  value="{{ $type->max_children }}">
                        <input type="hidden" name="sort_order"    value="{{ $type->sort_order }}">
                        <input type="hidden" name="is_active"     value="{{ $type->is_active ? '1' : '0' }}">
                        @foreach($type->amenities ?? [] as $a)
                        <input type="hidden" name="amenities[]" value="{{ $a }}">
                        @endforeach

                        <div>
                            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Base Price (KES)</label>
                            <input type="number" name="base_price" value="{{ $type->base_price }}"
                                   required min="0" step="100"
                                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-36 focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Weekend Price (KES)</label>
                            <input type="number" name="weekend_price" value="{{ $type->weekend_price }}"
                                   min="0" step="100"
                                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-36 focus:outline-none focus:border-green-500">
                        </div>
                        <button type="submit"
                                class="bg-green-900 text-white px-5 py-2 rounded-lg text-xs font-bold hover:bg-green-800">
                            Update Price
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl p-10 text-center text-gray-400">
            <div class="text-4xl mb-3">🛏</div>
            <p>No room types yet. <a href="{{ route('admin.rooms.types.create') }}" class="text-green-700 underline">Add the first one.</a></p>
        </div>
        @endforelse
    </div>
</div>
@endsection