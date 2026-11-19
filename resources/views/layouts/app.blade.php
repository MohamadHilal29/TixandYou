<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tix and You - Your Local Event Ticketing Partner')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAFAFA] text-[#0A0A0A] antialiased min-h-screen flex flex-col">

    <!-- Public Navbar -->
    <header class="bg-white border-b border-zinc-200/80 sticky top-0 z-30">
        <div class="max-w-[1152px] mx-auto px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="shrink-0 flex items-center">
                <x-logo size="md" />
            </a>

            <!-- Search & Buttons -->
            <div class="flex items-center gap-3 flex-1 justify-end">
                <form action="{{ route('home') }}" method="GET" class="hidden md:flex items-center w-full max-w-xs">
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-3.5 flex items-center text-zinc-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 11 5 11a6 6 0 0 1 12 0z"/></svg>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Find Events..." class="w-full pl-10 pr-4 py-2 text-sm bg-zinc-100 text-[#0A0A0A] placeholder-zinc-500 border border-zinc-200 rounded-full focus:outline-none focus:bg-white focus:border-zinc-400 transition">
                    </div>
                </form>

                @auth
                    @php
                        $dashRoute = match(auth()->user()->role) {
                            'superadmin' => route('superadmin.dashboard'),
                            'admin' => route('admin.dashboard'),
                            'eo' => route('eo.dashboard'),
                            default => route('profile'),
                        };
                    @endphp

                    @if(!in_array(auth()->user()->role, ['user', 'user']))
                        <a href="{{ $dashRoute }}" class="px-4 py-2 text-sm font-semibold text-[#0A0A0A] bg-white border border-zinc-300 hover:bg-zinc-100 rounded-lg transition">
                            Dashboard
                        </a>
                    @else
                        @if(auth()->user()->eo_status !== 'pending')
                            <a href="{{ route('join.eo') }}" class="hidden sm:inline-block px-3.5 py-2 text-xs font-semibold text-[#0A0A0A] bg-white border border-zinc-300 hover:bg-zinc-100 rounded-lg transition">
                                Join EO
                            </a>
                        @endif

                        <a href="{{ route('profile') }}" title="Buka Profil & Tiket Saya"
                           class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-[#0A0A0A] bg-zinc-100 hover:bg-zinc-200/80 border border-zinc-200 rounded-lg transition">
                            <span class="w-5 h-5 rounded-full bg-[#0A0A0A] text-white text-[10px] font-bold flex items-center justify-center">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span>Halo, {{ auth()->user()->name }}</span>
                        </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-lg transition cursor-pointer">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('join.eo') }}" class="px-4 py-2 text-sm font-semibold text-[#0A0A0A] bg-white border border-zinc-300 hover:bg-zinc-100 rounded-lg transition">
                        Join EO
                    </a>
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-lg transition">
                        Log in
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Optional Sub-Banner -->
    @yield('top-banner')

    <!-- Main Content -->
    <main class="flex-1 max-w-[1152px] w-full mx-auto px-6 py-8">
        @yield('content')
    </main>

    <!-- Public Footer -->
    <footer class="bg-white border-t border-zinc-200/80 mt-16">
        <div class="max-w-[1152px] mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-8 text-sm">
            <div>
                <a href="{{ route('home') }}" class="inline-block mb-3">
                    <x-logo size="sm" />
                </a>
                <p class="text-[#52525B] mt-1 leading-relaxed">Platform manajemen dan penjualan tiket event lokal yang cepat, transparan, dan mudah diakses.</p>
            </div>

            <div>
                <h4 class="font-semibold text-[#0A0A0A] mb-3">Quick Links</h4>
                <ul class="space-y-2 text-[#52525B]">
                    <li><a href="{{ route('about') }}" class="hover:text-[#0A0A0A] transition">About Us</a></li>
                    <li><a href="https://wa.me/6289677818438" target="_blank" rel="noopener noreferrer" class="hover:text-[#0A0A0A] transition">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-[#0A0A0A] mb-3">Support</h4>
                <ul class="space-y-2 text-[#52525B]">
                    <li><a href="https://wa.me/6289677818438" target="_blank" rel="noopener noreferrer" class="hover:text-[#0A0A0A] transition">Help Center</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-[#0A0A0A] transition">Terms and Services</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-[#0A0A0A] transition">Privacy Policy</a></li>
                    <li><a href="{{ route('faqs') }}" class="hover:text-[#0A0A0A] transition">FAQs</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-[#0A0A0A] mb-3">Info</h4>
                <ul class="space-y-2 text-[#52525B]">
                    <li>&copy; {{ date('Y') }} Tix and You</li>
                </ul>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>