@extends('layouts.admin')
@section('title', 'Add Staff Member')
@section('page-title', 'Add Staff Member')
@section('breadcrumb', 'Settings / Users / New')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.settings.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060] @error('name') border-red-300 @enderror">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060] @error('email') border-red-300 @enderror">
                @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Role *</label>
                <select name="role" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(old('role') === $role->name)>
                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Password *</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060] @error('password') border-red-300 @enderror">
                @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-wide mb-1.5">Confirm Password *</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-[#4a8060]">
            </div>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.settings.users.index') }}"
                   class="px-5 py-2.5 border border-gray-200 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50">Cancel</a>
                <button type="submit"
                        class="flex-1 py-2.5 bg-[#1e3a2f] text-white rounded-lg text-sm font-semibold hover:bg-[#2e5c42] transition-colors">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
