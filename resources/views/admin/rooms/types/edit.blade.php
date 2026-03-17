@extends('layouts.admin')
@section('title', 'Edit Room Type')

@section('content')
<div class="p-6 max-w-2xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.rooms.types.index') }}" class="text-sm text-green-700 hover:text-green-900">← Back to Room Types</a>
        <h1 class="text-2xl font-display text-gray-900 mt-1">Edit: {{ $type->name }}</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-5 text-sm">✓ {{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.rooms.types.update', $type) }}"
          class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Pricing — most important, put it first and make it stand out --}}
        <div class="bg-green-900 rounded-xl p-5 text-white">
            <div class="text-xs font-bold uppercase tracking-widest text-green-300 mb-4">💰 Pricing</div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-green-300 uppercase tracking-widest mb-1">Base Price (KES/night) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-green-400 font-semibold text-sm">KES</span>
                        <input type="number" name="base_price"
                               value="{{ old('base_price', $type->base_price) }}"
                               required min="0" step="100"
                               class="w-full bg-green-800 border border-green-700 rounded-lg pl-12 pr-3 py-3 text-white text-lg font-bold focus:outline-none focus:border-green-400 placeholder-green-600"
                               placeholder="8500">
                    </div>
                    @error('base_price')<p class="text-red-300 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-300 uppercase tracking-widest mb-1">Weekend Price (KES/night)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-green-400 font-semibold text-sm">KES</span>
                        <input type="number" name="weekend_price"
                               value="{{ old('weekend_price', $type->weekend_price) }}"
                               min="0" step="100"
                               class="w-full bg-green-800 border border-green-700 rounded-lg pl-12 pr-3 py-3 text-white text-lg font-bold focus:outline-none focus:border-green-400 placeholder-green-600"
                               placeholder="Optional">
                    </div>
                </div>
            </div>
            <p class="text-green-400 text-xs mt-3">Weekend price applies Friday & Saturday nights. Leave blank to use base price.</p>
        </div>

        {{-- Basic info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
            <div class="text-xs font-bold text-amber-600 uppercase tracking-widest">Room Details</div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $type->name) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $type->sort_order) }}" min="0"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">{{ old('description', $type->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Max Adults *</label>
                    <input type="number" name="max_adults" value="{{ old('max_adults', $type->max_adults) }}" required min="1"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Max Children *</label>
                    <input type="number" name="max_children" value="{{ old('max_children', $type->max_children) }}" required min="0"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                </div>
            </div>
        </div>

        {{-- Amenities --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="text-xs font-bold text-amber-600 uppercase tracking-widest mb-3">Amenities</div>
            <div class="grid grid-cols-3 gap-2">
                @foreach(['WiFi','Air Conditioning','Private Balcony','Garden View','En-suite Bathroom','Hot Water','TV','Mini Bar','Safe','Room Service','Bathtub','King Bed','Twin Beds','Living Area','Kitchen','Outdoor Shower','Kitchenette','Sofa Bed'] as $amenity)
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                           {{ in_array($amenity, old('amenities', $type->amenities ?? [])) ? 'checked' : '' }}
                           class="accent-green-700">
                    {{ $amenity }}
                </label>
                @endforeach
            </div>
        </div>

        {{-- Visibility --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $type->is_active) ? 'checked' : '' }}
                       class="accent-green-700 w-4 h-4">
                <div>
                    <div class="text-sm font-semibold text-gray-800">Visible on public site</div>
                    <div class="text-xs text-gray-400">Uncheck to hide this room type from guests</div>
                </div>
            </label>
        </div>

        <div class="flex items-center justify-between pt-2">
            <div class="flex gap-3">
                <a href="{{ route('admin.rooms.types.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                <form method="POST" action="{{ route('admin.rooms.types.destroy', $type) }}"
                      onsubmit="return confirm('Delete {{ $type->name }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">Delete Type</button>
                </form>
            </div>
            <button type="submit"
                    class="bg-green-900 text-white px-8 py-2.5 rounded-lg text-sm font-bold hover:bg-green-800 transition-colors">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection