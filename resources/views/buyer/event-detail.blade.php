@extends('layouts.app')

@section('title', $event->title . ' - Tix and You')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('home') }}" class="inline-flex items-center text-xs font-semibold text-[#52525B] hover:text-[#0A0A0A]">
        &larr; Kembali ke Daftar Event
    </a>

    <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-xs overflow-hidden">
        <!-- Area Flyer Event (Ukuran Tetap h-64 sm:h-80, Kosong Jika Belum Ada Banner) -->
        <div class="relative h-64 sm:h-80 bg-zinc-100 border-b border-zinc-200/80 overflow-hidden">
            @if ($event->banner_path)
                <img src="{{ asset('storage/' . $event->banner_path) }}"
                     alt="{{ $event->title }}"
                     class="w-full h-full object-cover">
            @endif
            <div class="absolute bottom-4 left-6 flex items-center gap-2">
                <span class="px-3 py-1 text-[11px] font-semibold bg-white text-[#0A0A0A] border border-zinc-200/80 rounded-full shadow-xs">
                    {{ $event->category }}
                </span>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <div class="space-y-1.5">
                <h1 class="text-2xl sm:text-3xl font-bold text-[#0A0A0A]">{{ $event->title }}</h1>
                <p class="text-xs text-[#52525B]">
                    Diselenggarakan oleh <strong class="text-[#0A0A0A]">{{ $event->eo->organization_name ?? $event->eo->name }}</strong>
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-[#FAFAFA] border border-zinc-200/80 text-xs">
                <div>
                    <p class="text-[#52525B]">Tanggal & Waktu</p>
                    <p class="font-bold text-[#0A0A0A] mt-0.5">{{ $event->event_date->format('d M Y') }}</p>
                    <p class="text-[#52525B]">{{ $event->start_time }} - {{ $event->end_time }} WIB {{ $event->open_gate ? "(Open Gate {$event->open_gate})" : '' }}</p>
                </div>
                <div>
                    <p class="text-[#52525B]">Lokasi Venue</p>
                    <p class="font-bold text-[#0A0A0A] mt-0.5">{{ $event->venue_name }}</p>
                    <p class="text-[#52525B]">{{ $event->venue_address }}</p>
                </div>
            </div>

            <div class="space-y-2">
                <h2 class="text-sm font-bold text-[#0A0A0A]">Deskripsi Acara</h2>
                <p class="text-xs sm:text-sm text-[#52525B] leading-relaxed whitespace-pre-line">{{ $event->description }}</p>
            </div>

            <!-- Daftar Kategori Tiket -->
            <div class="space-y-3 pt-2 border-t border-zinc-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-[#0A0A0A]">Kategori Tiket Tersedia</h2>
                    <span class="text-[11px] text-[#52525B]">Maks. 5 tiket per transaksi</span>
                </div>

                <div class="space-y-2.5">
                    @foreach ($event->ticketCategories as $cat)
                        @php $remaining = max(0, $cat->quota - $cat->sold); @endphp
                        <div class="p-4 rounded-xl bg-[#FAFAFA] border border-zinc-200/80 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-[#0A0A0A]">{{ $cat->name }}</p>
                                <p class="text-xs text-[#52525B]">
                                    {{ $remaining > 0 ? "Tersisa {$remaining} dari {$cat->quota} tiket" : 'Kuota Habis (Sold Out)' }}
                                </p>
                            </div>
                            <p class="text-sm font-bold text-[#0A0A0A]">
                                {{ $cat->price == 0 ? 'Gratis' : 'Rp ' . number_format($cat->price, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <a href="{{ route('event.tickets', $event->slug) }}"
                   class="w-full sm:w-auto text-center px-6 py-3 text-xs sm:text-sm font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition">
                    Pilih & Beli Tiket Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection