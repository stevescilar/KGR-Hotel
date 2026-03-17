@extends('layouts.admin')
@section('title', 'Add Menu Item')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.restaurant.menu.index') }}" class="text-sm text-green-700 hover:text-green-900">← Back to Menu</a>
            <h1 class="text-2xl font-display text-gray-900 mt-1">Add Menu Item</h1>
        </div>
        <a href="{{ route('admin.restaurant.menu.categories.create') }}" class="text-sm text-amber-600 hover:text-amber-800 font-semibold">
            + New Category
        </a>
    </div>

    <form method="POST" action="{{ route('admin.restaurant.menu.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Category *</label>
                <select name="menu_category_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                    <option value="">Select category…</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('menu_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('menu_category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Item Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Description</label>
            <textarea name="description" rows="2"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Price (KES) *</label>
                <input type="number" name="price" value="{{ old('price') }}" required min="0" step="50"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
            </div>
        </div>

        {{-- Image upload --}}
        <div>
            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-2">Item Photo</label>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center cursor-pointer hover:border-green-400 transition-colors"
                 onclick="document.getElementById('imgInput').click()">
                <img id="imgPreview" src="" alt="" class="mx-auto mb-3 rounded-lg max-h-36 object-cover hidden">
                <div id="imgPrompt">
                    <div class="text-3xl mb-2">🍽</div>
                    <div class="text-sm text-gray-500">Click to upload food photo</div>
                    <div class="text-xs text-gray-400 mt-1">JPG, PNG · max 4MB</div>
                </div>
                <input type="file" id="imgInput" name="image" accept="image/*" class="hidden" onchange="previewImg(this)">
            </div>
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }} class="accent-green-700">
                Available
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="accent-green-700">
                Featured
            </label>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('admin.restaurant.menu.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
            <button type="submit" class="bg-green-900 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-green-800">Add Item</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => { const i = document.getElementById('imgPreview'); i.src = e.target.result; i.classList.remove('hidden'); document.getElementById('imgPrompt').classList.add('hidden'); };
        r.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush