@extends('layouts.admin')
@section('title', $guest->full_name)
@section('page-title', $guest->full_name)
@section('breadcrumb', 'Guests / ' . $guest->full_name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Guest profile card --}}
    <div class="space-y-5">
        <div class="bg-white rounded-xl border border-gray-100 p-6 text-center">
            <div class="w-20 h-20 rounded-full bg-[#1e3a2f] flex items-center justify-center text-white text-2xl font-bold font-mono-kgr mx-auto mb-4">
                {{ strtoupper(substr($guest->first_name,0,1).substr($guest->last_name,0,1)) }}
            </div>
            <h2 class="font-display text-xl text-gray-800">{{ $guest->full_name }}</h2>
            @php $tierColors = ['none'=>'bg-gray-100 text-gray-500','bronze'=>'bg-orange-100 text-orange-700','silver'=>'bg-slate-100 text-slate-600','gold'=>'bg-yellow-100 text-yellow-700'] @endphp
            <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full capitalize mt-2 {{ $tierColors[$guest->vip_tier ?? 'none'] }}">
                ⭐ {{ $guest->vip_tier ?? 'none' }} · {{ number_format($guest->loyalty_points) }} pts
            </span>

            <div class="mt-4 space-y-2 text-sm text-left">
                @foreach([['📧','Email',$guest->email],['📱','Phone',$guest->phone],['🌍','Nationality',$guest->nationality ?? '—'],['🏠','Address',$guest->address ?? '—']] as [$icon,$label,$val])
                <div class="flex items-center gap-2 text-gray-600">
                    <span class="w-5 text-center">{{ $icon }}</span>
                    <span class="text-xs text-gray-400 w-20">{{ $label }}</span>
                    <span class="flex-1 text-xs font-medium">{{ $val }}</span>
                </div>
                @endforeach
            </div>

            <a href="{{ route('admin.guests.edit', $guest) }}"
               class="mt-4 block text-center text-xs text-[#4a8060] border border-[#4a8060] px-4 py-2 rounded-lg hover:bg-[#4a8060] hover:text-white transition-colors font-semibold">
                Edit Profile
            </a>
        </div>

        {{-- Loyalty summary --}}
        <div class="bg-[#f0e9d8] rounded-xl border border-amber-100 p-5">
            <div class="text-xs text-[#c8974a] font-semibold uppercase tracking-wide mb-3">Loyalty Points</div>
            <div class="text-3xl font-bold font-display text-[#1e3a2f] mb-1">{{ number_format($guest->loyalty_points) }}</div>
            <div class="text-xs text-gray-500 mb-4">points accumulated</div>
            @php
            $tiers = ['bronze'=>1000,'silver'=>5000,'gold'=>10000];
            $next = collect($tiers)->filter(fn($t) => $t > $guest->loyalty_points)->first();
            @endphp
            @if($next)
            <div class="w-full bg-amber-200 rounded-full h-2 mb-1">
                <div class="bg-[#c8974a] h-2 rounded-full" style="width: {{ min(100, ($guest->loyalty_points / $next) * 100) }}%"></div>
            </div>
            <div class="text-xs text-gray-500">{{ number_format($next - $guest->loyalty_points) }} pts to next tier</div>
            @else
            <div class="text-xs text-yellow-700 font-semibold">🏆 Gold member!</div>
            @endif
            <a href="{{ route('admin.guests.loyalty', $guest) }}" class="block text-xs text-[#4a8060] hover:underline font-semibold mt-3">
                View full history →
            </a>
        </div>
    </div>

    {{-- Booking history --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-lg text-[#1e3a2f]">Stay History</h3>
                <span class="text-xs text-gray-400">{{ $guest->bookings->count() }} total stays</span>
            </div>
            @forelse($guest->bookings as $booking)
            <a href="{{ route('admin.bookings.show', $booking) }}"
               class="flex items-center gap-4 py-3.5 border-b border-gray-50 last:border-0 hover:bg-gray-50 -mx-3 px-3 rounded-lg transition-colors">
                <div class="w-2 h-2 rounded-full flex-shrink-0 {{ ['confirmed'=>'bg-green-400','checked_in'=>'bg-blue-400','checked_out'=>'bg-gray-300','pending'=>'bg-amber-400','cancelled'=>'bg-red-300'][$booking->status] ?? 'bg-gray-300' }}"></div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-gray-800">{{ $booking->room->roomType->name }}</div>
                    <div class="text-xs text-gray-400 font-mono-kgr">{{ $booking->booking_ref }}</div>
                </div>
                <div class="text-xs text-gray-500 hidden md:block">
                    {{ $booking->check_in->format('M j') }} → {{ $booking->check_out->format('M j, Y') }}
                </div>
                <div class="text-right">
                    <div class="text-sm font-semibold text-gray-800">KES {{ number_format($booking->total_amount) }}</div>
                    <span @class(['text-xs px-2 py-0.5 rounded-full font-medium',
                        'bg-green-100 text-green-700' => $booking->status === 'confirmed',
                        'bg-blue-100 text-blue-700'   => $booking->status === 'checked_in',
                        'bg-gray-100 text-gray-500'   => $booking->status === 'checked_out',
                        'bg-amber-100 text-amber-700' => $booking->status === 'pending',
                        'bg-red-100 text-red-600'     => $booking->status === 'cancelled',
                    ])>{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                </div>
            </a>
            @empty
            <p class="text-sm text-gray-400 text-center py-8">No booking history</p>
            @endforelse
        </div>

        {{-- Recent loyalty transactions --}}
        @if($guest->loyaltyTransactions->count())
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="font-display text-lg text-[#1e3a2f] mb-4">Recent Points Activity</h3>
            @foreach($guest->loyaltyTransactions as $txn)
            <div class="flex items-center gap-3 py-2.5 border-b border-gray-50 last:border-0">
                <span class="text-lg">{{ $txn->points > 0 ? '➕' : '➖' }}</span>
                <div class="flex-1">
                    <div class="text-sm text-gray-700">{{ $txn->description }}</div>
                    <div class="text-xs text-gray-400">{{ $txn->created_at->format('M j, Y') }}</div>
                </div>
                <div class="font-bold text-sm {{ $txn->points > 0 ? 'text-green-600' : 'text-red-500' }}">
                    {{ $txn->points > 0 ? '+' : '' }}{{ number_format($txn->points) }}
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
