@extends('layouts.admin')
@section('title', 'Ticket Types')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display text-gray-900">Gate Ticket Types</h1>
            <p class="text-sm text-gray-500 mt-1">Manage day visit ticket categories shown on the Activities page</p>
        </div>
        <a href="{{ route('admin.tickets.types.create') }}"
           class="bg-green-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-800">
            + Add Ticket Type
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 text-sm">✓ {{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Price (KES)</th>
                    <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($ticketTypes as $type)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $type->name }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">{{ $type->description ?? '—' }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-800">{{ number_format($type->price) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $type->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $type->is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.tickets.types.edit', $type) }}" class="text-green-700 hover:text-green-900 font-medium text-xs">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No ticket types yet. <a href="{{ route('admin.tickets.types.create') }}" class="text-green-700 underline">Add the first one.</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection