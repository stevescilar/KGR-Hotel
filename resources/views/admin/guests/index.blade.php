@extends('layouts.admin')
@section('title', 'Guests')
@section('page-title', 'Guests')
@section('breadcrumb', 'Guest registry')

@section('content')
<div class="space-y-4">

    {{-- Search bar --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name, email or phone…"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060] flex-1 min-w-48">
            <select name="tier" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-[#4a8060]"
                    onchange="this.form.submit()">
                <option value="">All Tiers</option>
                @foreach(['none','bronze','silver','gold'] as $t)
                    <option value="{{ $t }}" @selected(request('tier') === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-[#1e3a2f] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#2e5c42]">
                Search
            </button>
            @if(request()->hasAny(['search','tier']))
                <a href="{{ route('admin.guests.index') }}" class="text-sm text-gray-400 hover:text-gray-600 self-center">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Guest</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Contact</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tier</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Stays</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Spent</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($guests as $guest)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-[#1e3a2f] flex items-center justify-center text-white text-xs font-bold font-mono-kgr flex-shrink-0">
                                {{ strtoupper(substr($guest->first_name,0,1).substr($guest->last_name,0,1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">{{ $guest->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $guest->nationality ?? 'Kenya' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-gray-700">{{ $guest->email }}</div>
                        <div class="text-xs text-gray-400">{{ $guest->phone }}</div>
                    </td>
                    <td class="px-5 py-4">
                        @php $tierColors = ['none'=>'bg-gray-100 text-gray-500','bronze'=>'bg-orange-100 text-orange-700','silver'=>'bg-slate-100 text-slate-600','gold'=>'bg-yellow-100 text-yellow-700'] @endphp
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize {{ $tierColors[$guest->vip_tier ?? 'none'] }}">
                            {{ $guest->vip_tier ?? 'none' }}
                        </span>
                        <div class="text-xs text-gray-400 mt-1">{{ number_format($guest->loyalty_points) }} pts</div>
                    </td>
                    <td class="px-5 py-4 font-medium text-gray-700">{{ $guest->bookings_count }}</td>
                    <td class="px-5 py-4 font-semibold text-gray-800">KES {{ number_format($guest->total_spent ?? 0) }}</td>
                    <td class="px-5 py-4">
                        <a href="{{ route('admin.guests.show', $guest) }}" class="text-[#4a8060] text-xs font-semibold hover:underline">View →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center text-gray-400">
                        <div class="text-4xl mb-2">👥</div>No guests found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($guests->hasPages())
            <div class="px-5 py-4 border-t border-gray-50">{{ $guests->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
