@extends('layouts.app')

@section('title', $event->title . ' - Tix and You')

@section('content')
@php
    $posterFile = $event->poster ?? $event->image ?? null;
    $posterSrc = $posterFile ? asset('storage/' . ltrim($posterFile, '/')) : ($event->banner_url ?? null);
    $minPrice = $event->ticketCategories->min('price');
    $totalRemaining = $event->ticketCategories->sum(fn ($c) => $c->remaining_quota ?? $c->quota);
@endphp

<div class="space-y-5">
    <!-- Breadcrumb -->
    <nav class="flex flex-wrap items-center gap-2 text-xs text-[#52525B]">
        <a href="{{ route('home') }}" class="hover:text-[#0A0A0A]">Home</a>
        <span>/</span>
        <span>{{ $event->category ?? 'Musik & Konser' }}</span>
        <span>/</span>
        <span class="font-semibold text-[#0A0A0A]">{{ $event->title }}</span>
    </nav>

    <!-- Layout 2 Kolom (Sesuai Desain Awal) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Kolom Kiri (2/3): Poster/Banner + Deskripsi Event + Daftar Tiket -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Banner / Poster Utama -->
            <div style="background: linear-gradient(135deg, #18181b 0%, #27272a 100%);"
                 class="relative h-64 sm:h-80 rounded-2xl overflow-hidden flex flex-col justify-end p-6 sm:p-8 text-white shadow-xs">
                @if ($posterSrc)
                    <img src="{{ $posterSrc }}" alt="{{ $event->title }}"
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
                @endif

                <div class="relative z-10 space-y-2">
                    <span class="inline-block px-3 py-1 text-[11px] font-bold bg-white/15 backdrop-blur-xs border border-white/20 rounded-full text-white">
                        {{ $event->category ?? 'Musik & Konser' }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                        {{ $event->title }}
                    </h1>
                    <p class="text-xs text-zinc-300">
                        Diselenggarakan oleh <strong class="text-white">{{ $event->eo->organization_name ?? $event->eo->name ?? 'Event Organizer' }}</strong>
                    </p>
                </div>
            </div>

            <!-- Deskripsi Event -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-zinc-200/80 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-[#0A0A0A]">Deskripsi Event</h2>
                <p class="text-xs sm:text-sm text-[#52525B] leading-relaxed whitespace-pre-line">{{ $event->description }}</p>

                <!-- Ringkasan Kategori Tiket -->
                <div class="pt-4 border-t border-zinc-100 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-[#0A0A0A]">Kategori Tiket Tersedia</h3>
                        <span class="text-[11px] text-[#52525B]">Maks. 5 tiket per transaksi</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach ($event->ticketCategories as $cat)
                            @php $sisa = $cat->remaining_quota ?? $cat->quota; @endphp
                            <div class="p-3.5 rounded-xl bg-[#FAFAFA] border border-zinc-200/80 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-bold text-[#0A0A0A]">{{ $cat->name }}</p>
                                    <p class="text-[11px] text-[#52525B]">Sisa {{ $sisa }} tiket</p>
                                </div>
                                <p class="text-xs font-extrabold text-[#0A0A0A]">
                                    Rp {{ number_format((float) $cat->price, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (1/3): Sidebar Detail Pelaksanaan & CTA -->
        <aside class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs space-y-5 lg:sticky lg:top-24">
            <h2 class="text-sm font-bold text-[#0A0A0A]">Detail Pelaksanaan</h2>

            <div class="space-y-4 text-xs">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 text-zinc-500">&#128197;</span>
                    <div>
                        <p class="text-[11px] text-[#52525B]">Tanggal</p>
                        <p class="font-bold text-[#0A0A0A] mt-0.5">{{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="mt-0.5 text-zinc-500">&#128337;</span>
                    <div>
                        <p class="text-[11px] text-[#52525B]">Waktu</p>
                        <p class="font-bold text-[#0A0A0A] mt-0.5">
                            {{ $event->start_time ?? '18:30' }} - {{ $event->end_time ?? '23:00' }} WIB
                            @if ($event->open_gate)
                                (Open Gate {{ $event->open_gate }})
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="mt-0.5 text-zinc-500">&#128205;</span>
                    <div>
                        <p class="text-[11px] text-[#52525B]">Lokasi Venue</p>
                        <p class="font-bold text-[#0A0A0A] mt-0.5">{{ $event->venue_name }}</p>
                        <p class="text-[11px] text-[#52525B] mt-0.5">{{ $event->venue_address ?? $event->location ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-100 space-y-4">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-[11px] text-[#52525B]">Harga tiket mulai dari</p>
                        <p class="text-xl font-extrabold text-[#0A0A0A] mt-0.5">
                            {{ $minPrice !== null ? 'Rp ' . number_format((float) $minPrice, 0, ',', '.') : 'Gratis' }}
                        </p>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $totalRemaining > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ $totalRemaining > 0 ? 'Tiket Tersedia' : 'Sold Out' }}
                    </span>
                </div>

                <a href="{{ route('event.tickets', $event->slug) }}"
                   class="block w-full text-center py-3 px-4 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition">
                    Beli Tiket Sekarang
                </a>
            </div>
        </aside>
    </div>
</div>
@endsection