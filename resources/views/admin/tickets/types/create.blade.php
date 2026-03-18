@extends('layouts.admin')
@section('title', 'Add Ticket Type')

@section('content')
<div class="p-6 max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.tickets.types.index') }}" class="text-sm text-green-700 hover:text-green-900">← Back to Ticket Types</a>
        <h1 class="text-2xl font-display text-gray-900 mt-1">Add Ticket Type</h1>
    </div>

    <form method="POST" action="{{ route('admin.tickets.types.store') }}"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Ticket Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   placeholder="e.g. Adult Day Visit, Child Day Visit"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Description</label>
            <textarea name="description" rows="2"
                      placeholder="What this ticket includes…"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Price (KES) *</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold">KES</span>
                <input type="number" name="price" value="{{ old('price', 1500) }}" required min="0" step="100"
                       class="w-full border border-gray-200 rounded-lg pl-12 pr-3 py-2 text-sm focus:outline-none focus:border-green-500">
            </div>
            @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="accent-green-700">
                Active (visible on Activities page)
            </label>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('admin.tickets.types.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
            <button type="submit" class="bg-green-900 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-green-800">
                Create Ticket Type
            </button>
        </div>
    </form>
</div>
@endsection