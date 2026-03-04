<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Kitonga Garden Resort</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Jost', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="min-h-screen bg-[#1e3a2f] flex items-center justify-center p-4">

    {{-- Background texture --}}
    <div class="fixed inset-0 opacity-5"
         style="background-image: radial-gradient(circle at 25% 25%, #7aaa8a 0%, transparent 50%), radial-gradient(circle at 75% 75%, #c8974a 0%, transparent 50%);">
    </div>

    <div class="relative w-full max-w-sm">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-[#c8974a] rounded-2xl text-3xl mb-4 shadow-lg">🌿</div>
            <h1 class="font-display text-3xl text-white font-light">Kitonga Garden</h1>
            <p class="text-white/50 text-sm mt-1 tracking-widest uppercase">Admin Portal</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-5">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-widest mb-2">
                        Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800 outline-none
                                  focus:border-[#4a8060] focus:ring-2 focus:ring-[#4a8060]/10 transition-colors"
                           placeholder="you@kgr.co.ke">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#4a8060] uppercase tracking-widest mb-2">
                        Password
                    </label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-800 outline-none
                                  focus:border-[#4a8060] focus:ring-2 focus:ring-[#4a8060]/10 transition-colors"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="accent-[#4a8060]">
                        <span class="text-sm text-gray-500">Remember me</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-[#1e3a2f] hover:bg-[#2e5c42] text-white font-semibold py-3 rounded-lg
                               transition-colors text-sm tracking-widest uppercase">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-white/30 text-xs mt-6">
            © {{ date('Y') }} Kitonga Garden Resort · Ukasi, Kitui County
        </p>
    </div>
</body>
</html>
