@extends('layouts.admin')
@section('title', 'Edit Room')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.rooms.index') }}" class="text-sm text-green-700 hover:text-green-900">← Back to Rooms</a>
        <h1 class="text-2xl font-display text-gray-900 mt-1">Edit Room {{ $room->room_number }}</h1>
    </div>

    <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Room Number *</label>
                <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                @error('room_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Room Type *</label>
                <select name="room_type_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ old('room_type_id', $room->room_type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} — KES {{ number_format($type->base_price) }}/night
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Floor</label>
                <input type="number" name="floor" value="{{ old('floor', $room->floor) }}" min="0"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Cottage / Block</label>
                <input type="text" name="cottage" value="{{ old('cottage', $room->cottage) }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Status *</label>
                <select name="status" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">
                    @foreach(['available' => 'Available', 'occupied' => 'Occupied', 'cleaning' => 'Cleaning', 'maintenance' => 'Maintenance'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $room->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-1">Notes</label>
            <textarea name="notes" rows="2"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-500">{{ old('notes', $room->notes) }}</textarea>
        </div>

        {{-- Image --}}
        <div>
            <label class="block text-xs font-bold text-green-700 uppercase tracking-widest mb-2">Room Photo</label>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center cursor-pointer hover:border-green-400 transition-colors"
                 onclick="document.getElementById('imageInput').click()">
                @if($room->image)
                    <img id="imagePreview" src="{{ Storage::url($room->image) }}" alt="Room photo"
                         class="mx-auto mb-3 rounded-lg max-h-40 object-cover">
                    <div id="uploadPrompt" class="hidden">
                @else
                    <img id="imagePreview" src="" alt="" class="mx-auto mb-3 rounded-lg max-h-40 object-cover hidden">
                    <div id="uploadPrompt">
                @endif
                        <div class="text-3xl mb-2">🖼</div>
                        <div class="text-sm text-gray-500">Click to upload a new photo</div>
                        <div class="text-xs text-gray-400 mt-1">JPG, PNG · max 4MB</div>
                    </div>
                <p class="text-xs text-gray-400 mt-2">Click to replace photo</p>
                <input type="file" id="imageInput" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex gap-3">
                <a href="{{ route('admin.rooms.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Delete room {{ $room->room_number }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">Delete</button>
                </form>
            </div>
            <button type="submit" class="bg-green-900 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-green-800">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('imagePreview');
            img.src = e.target.result;
            img.classList.remove('hidden');
            document.getElementById('uploadPrompt').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush