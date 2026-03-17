@extends('layouts.admin')
@section('title', 'Add Event Package')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.events.packages.index') }}" class="text-sm text-green-700 hover:text-green-900">← Back to Packages</a>
        <h1 class="text-2xl font-display text-gray-900 mt-1">Add Event Package</h1>
    </div>

    <form method="POST" action="{{ route('admin.events.packages.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Package Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       oninput="autoSlug(this.value)"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Slug *</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:border-green-500">
                @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Starting Price (KES) *</label>
                <input type="number" name="starting_price" value="{{ old('starting_price') }}" required min="0" step="1000"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Price Per Person (KES)</label>
                <input type="number" name="price_per_person" value="{{ old('price_per_person') }}" min="0" step="100"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Min Guests *</label>
                <input type="number" name="min_guests" value="{{ old('min_guests', 20) }}" required min="1"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Max Guests *</label>
                <input type="number" name="max_guests" value="{{ old('max_guests', 200) }}" required min="1"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Inclusions (one per line)</label>
            <textarea name="inclusions_text" rows="5"
                      placeholder="Venue hire&#10;Catering (3-course meal)&#10;Decoration setup&#10;Sound system"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500 font-mono">{{ old('inclusions_text') }}</textarea>
        </div>

        {{-- Image --}}
        <div>
            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-2">Event Photo</label>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center cursor-pointer hover:border-green-400 transition-colors"
                 onclick="document.getElementById('imgInput').click()">
                <img id="imgPreview" src="" alt="" class="mx-auto mb-3 rounded-lg max-h-40 object-cover hidden">
                <div id="imgPrompt">
                    <div class="text-3xl mb-2">🎪</div>
                    <div class="text-sm text-gray-500">Click to upload event photo</div>
                    <div class="text-xs text-gray-400 mt-1">JPG, PNG · max 4MB</div>
                </div>
                <input type="file" id="imgInput" name="image" accept="image/*" class="hidden" onchange="previewImg(this)">
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="accent-green-700">
                Active (visible on public site)
            </label>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('admin.events.packages.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
            <button type="submit" class="bg-green-900 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-green-800">
                Create Package
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function autoSlug(v) { document.getElementById('slug').value = v.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,''); }
function previewImg(input) {
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => { const i = document.getElementById('imgPreview'); i.src = e.target.result; i.classList.remove('hidden'); document.getElementById('imgPrompt').classList.add('hidden'); };
        r.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush