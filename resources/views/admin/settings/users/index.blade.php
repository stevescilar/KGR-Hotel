@extends('layouts.admin')
@section('title', 'Staff Accounts')
@section('page-title', 'Staff Accounts')
@section('breadcrumb', 'Settings / Users')

@section('content')
<div class="space-y-4 max-w-4xl">

    <div class="flex justify-end">
        <a href="{{ route('admin.settings.users.create') }}"
           class="bg-[#1e3a2f] text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#2e5c42] transition-colors">
            + Add Staff Member
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Added</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#1e3a2f] flex items-center justify-center text-white text-xs font-bold font-mono-kgr">
                                {{ $user->avatar_initials }}
                            </div>
                            <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                            @if($user->id === auth()->id())
                                <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">You</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $user->email }}</td>
                    <td class="px-5 py-4">
                        <span class="text-xs bg-[#f0e9d8] text-[#c8974a] font-semibold px-2.5 py-1 rounded-full capitalize">
                            {{ str_replace('_', ' ', $user->getRoleNames()->first() ?? '—') }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-gray-400 text-xs">{{ $user->created_at->format('M j, Y') }}</td>
                    <td class="px-5 py-4 flex gap-3">
                        <a href="{{ route('admin.settings.users.edit', $user) }}" class="text-[#4a8060] text-xs font-semibold hover:underline">Edit</a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.settings.users.destroy', $user) }}"
                              onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 text-xs font-semibold hover:underline">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
