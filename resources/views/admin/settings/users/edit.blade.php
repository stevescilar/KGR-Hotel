@extends('layouts.admin')
@section('title', 'Edit ' . $user->name)
@section('page-title', 'Edit Staff Account')
@section('breadcrumb', 'Settings / Users / ' . $user->name)

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.settings.users.update', $user) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Role *</label>
                <select name="role" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>
                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">New Password <span class="text-gray-400 font-normal">(leave blank to keep current)</span></label>
                <input type="password" name="password" minlength="8"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
            </div>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.settings.users.index') }}"
                   class="px-5 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50">Cancel</a>
                <button type="submit"
                        class="flex-1 py-2.5 bg-[#1e3a2f] text-white rounded-lg text-sm font-semibold hover:bg-[#2e5c42] transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
