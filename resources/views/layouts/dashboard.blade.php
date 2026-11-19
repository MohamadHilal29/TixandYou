<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - Tix and You')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAFAFA] text-[#0A0A0A] antialiased min-h-screen">

    <!-- Panel Navbar -->
    <header class="bg-white border-b border-zinc-200/80 sticky top-0 z-30">
        <div class="max-w-[1351px] mx-auto px-6 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center">
                    <x-logo size="sm" />
                </a>
                @hasSection('page-subtitle')
                    <span class="text-zinc-300">|</span>
                    <span class="text-sm font-medium text-[#52525B]">@yield('page-subtitle')</span>
                @endif
            </div>

            <div class="flex items-center gap-3">
                @yield('navbar-right')
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-[1351px] mx-auto px-6 py-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>