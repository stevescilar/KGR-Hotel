<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — KGR Admin</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400&family=Jost:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- Styles --}}
    
    @stack('styles')

    <style>
        :root {
            --forest: #1e3a2f; --moss: #2e5c42; --fern: #4a8060; --sage: #7aaa8a;
            --mist: #b8cebc; --cream: #f7f3ec; --warm: #ede7da; --parchment: #f0e9d8;
            --gold: #c8974a; --amber: #e4b36a; --ink: #1c1c18;
        }
        body { font-family: 'Jost', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
        .font-mono-kgr { font-family: 'DM Mono', monospace; }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gray-50">

<div class="flex h-full" x-data="{ sidebarOpen: false }">

    {{-- ── SIDEBAR ──────────────────────────────────────── --}}
    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-transition.opacity
         @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-black/50 lg:hidden"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col bg-[#1e3a2f] transition-transform duration-200 lg:relative lg:translate-x-0">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 h-16 border-b border-white/10 flex-shrink-0">
            <div class="w-8 h-8 rounded-md bg-[#c8974a] flex items-center justify-content text-white font-bold text-sm flex items-center justify-center">🌿</div>
            <div>
                <div class="font-display text-white text-sm font-medium leading-tight">Kitonga Garden</div>
                <div class="text-white/50 text-xs">Admin Panel</div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

            @php
                $nav = [
                    ['route' => 'admin.dashboard', 'icon' => '▦', 'label' => 'Dashboard', 'roles' => []],
                    ['route' => 'admin.bookings.partial-payments', 'icon' => '💛', 'label' => 'Balance Due'],
                    ['route' => 'admin.bookings.index', 'icon' => '🛏', 'label' => 'Bookings',
                        'badge' => \App\Models\Booking::where('status','pending')->count(), 'roles' => []],
                    ['route' => 'admin.rooms.index', 'icon' => '🏠', 'label' => 'Rooms', 'roles' => []],
                    ['route' => 'admin.guests.index', 'icon' => '👥', 'label' => 'Guests', 'roles' => []],
                    ['route' => 'admin.restaurant.orders', 'icon' => '🍽', 'label' => 'Restaurant',
                        'badge' => \App\Models\Order::whereIn('status',['open','preparing'])->count(), 'roles' => ['super_admin','manager','fnb_staff']],
                    ['route' => 'admin.events.index', 'icon' => '🎪', 'label' => 'Events',
                        'badge' => \App\Models\EventBooking::where('status','inquiry')->count(), 'roles' => []],
                    ['route' => 'admin.tickets.index', 'icon' => '🎫', 'label' => 'Gate Tickets', 'roles' => []],
                    ['route' => 'admin.gift-cards.index', 'icon' => '🎁', 'label' => 'Gift Cards', 'roles' => []],
                    ['route' => 'admin.careers.jobs.index', 'icon' => '💼', 'label' => 'HR / Careers',
                        'badge' => \App\Models\JobApplication::where('status','received')->count(), 'roles' => ['super_admin','manager','hr_admin']],
                    ['route' => 'admin.reports.occupancy', 'icon' => '📊', 'label' => 'Reports', 'roles' => ['super_admin','manager']],
                    ['route' => 'admin.settings.index', 'icon' => '⚙️', 'label' => 'Settings', 'roles' => ['super_admin']],
                ];
            @endphp

            @foreach($nav as $item)
                @if(empty($item['roles']) || auth()->user()->hasAnyRole($item['roles']))
                    @php $active = request()->routeIs(rtrim($item['route'], '.index') . '*') @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ $active ? 'bg-white/15 text-white' : 'text-white/65 hover:text-white hover:bg-white/10' }}">
                        <span class="text-base w-5 text-center">{{ $item['icon'] }}</span>
                        <span class="flex-1">{{ $item['label'] }}</span>
                        @if(!empty($item['badge']) && $item['badge'] > 0)
                            <span class="bg-[#c8974a] text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- User footer --}}
        <div class="p-4 border-t border-white/10 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-[#c8974a] flex items-center justify-center text-white text-xs font-bold font-mono-kgr">
                    {{ auth()->user()->avatar_initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="text-white/50 text-xs capitalize">{{ auth()->user()->getRoleNames()->first() ?? 'Staff' }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-white/40 hover:text-white transition-colors" title="Sign out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN CONTENT AREA ─────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="h-16 bg-white border-b border-gray-100 flex items-center gap-4 px-6 flex-shrink-0">
            {{-- Mobile menu toggle --}}
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="flex-1">
                <h1 class="font-display text-lg font-medium text-[#1e3a2f]">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                    <div class="text-xs text-gray-400 mt-0.5">@yield('breadcrumb')</div>
                @endif
            </div>

            <div class="flex items-center gap-3">
                {{-- Date --}}
                <span class="hidden md:block text-xs text-gray-400">{{ now()->format('D, M j Y') }}</span>

                {{-- New booking shortcut --}}
                <a href="{{ route('admin.bookings.create') }}"
                   class="bg-[#1e3a2f] text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-[#2e5c42] transition-colors tracking-wide uppercase">
                    + New Booking
                </a>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-lg flex items-center gap-2"
                 x-data x-init="setTimeout(() => $el.remove(), 4000)">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
                <span>⚠</span> {{ session('error') }}
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
<script>
    // Setup AJAX CSRF
    window.axios && (window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content);
</script>
</body>
</html>