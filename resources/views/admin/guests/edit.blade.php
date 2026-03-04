@extends('layouts.admin')
@section('title', 'Edit ' . $guest->full_name)
@section('page-title', 'Edit Guest')
@section('breadcrumb', 'Guests / ' . $guest->full_name . ' / Edit')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.guests.update', $guest) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                @foreach([['first_name','First Name'],['last_name','Last Name']] as [$field,$label])
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">{{ $label }} *</label>
                    <input type="text" name="{{ $field }}" value="{{ old($field, $guest->$field) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060] @error($field) border-red-300 @enderror">
                    @error($field)<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $guest->email) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060] @error('email') border-red-300 @enderror">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Phone</label>
                    <input type="tel" name="phone" value="{{ old('phone', $guest->phone) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Nationality</label>
                    <input type="text" name="nationality" value="{{ old('nationality', $guest->nationality) }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">VIP Tier</label>
                    <select name="vip_tier" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                        @foreach(['none','bronze','silver','gold'] as $t)
                            <option value="{{ $t }}" @selected(old('vip_tier',$guest->vip_tier) === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Address</label>
                <textarea name="address" rows="2"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060] resize-none">{{ old('address', $guest->address) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.guests.show', $guest) }}"
                   class="px-5 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 md:flex-none md:px-8 py-2.5 bg-[#1e3a2f] text-white rounded-lg text-sm font-semibold hover:bg-[#2e5c42] transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
