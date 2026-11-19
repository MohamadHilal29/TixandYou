@extends('layouts.app')

@section('title', 'Admin Dashboard - Tix and You')

@section('content')
@php
    $pendingEoList = $pendingEos ?? collect();
    $pendingEventsList = $pendingEvents ?? collect();
    $allEventsList = $allEvents ?? collect();
    $publishedCount = $allEventsList->where('status', 'published')->count();
    $paidOrdersTotal = $paidOrdersCount ?? 0;

    $payoutCols = \Illuminate\Support\Facades\Schema::getColumnListing('payouts');
    $eoCol = in_array('eo_id', $payoutCols, true) ? 'payouts.eo_id' : 'payouts.user_id';
    $pendingPayoutCount = \Illuminate\Support\Facades\DB::table('payouts')->where('status', 'pending')->count();
    $recentPayouts = \Illuminate\Support\Facades\DB::table('payouts')
        ->leftJoin('users', $eoCol, '=', 'users.id')
        ->select('payouts.*', 'users.name as eo_name', 'users.email as eo_email')
        ->orderByRaw("CASE WHEN payouts.status = 'pending' THEN 0 ELSE 1 END")
        ->orderByDesc('payouts.id')
        ->limit(5)
        ->get();
@endphp

<div class="space-y-6">
    <!-- Header Admin -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-zinc-100 text-[#0A0A0A] rounded-full mb-1.5">
                Admin Operasional
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#0A0A0A]">Panel Moderasi & Approval</h1>
            <p class="text-xs text-[#52525B] mt-0.5">Verifikasi pendaftaran mitra EO, pengajuan acara beserta poster, dan pencairan dana.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 self-start sm:self-auto">
            <a href="{{ route('admin.payouts.index') }}"
               class="px-4 py-2.5 text-xs font-semibold text-[#0A0A0A] bg-[#FAFAFA] border border-zinc-300 hover:bg-zinc-100 rounded-xl transition inline-flex items-center gap-2">
                <span>Verifikasi Payout EO</span>
                @if ($pendingPayoutCount > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-500 text-white rounded-full">
                        {{ $pendingPayoutCount }} Pending
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.scanner') }}"
               class="px-4 py-2.5 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition">
                Buka Web Scanner
            </a>
        </div>
    </div>

    <!-- Flash Notification -->
    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-300 text-red-900 text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Pendaftaran EO Baru</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingEoList->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Event Menunggu Approval</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingEventsList->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Event Tayang (Published)</p>
            <p class="text-2xl font-bold text-[#0A0A0A] mt-1">{{ $publishedCount }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Transaksi Lunas</p>
            <p class="text-2xl font-bold text-[#0A0A0A] mt-1">{{ $paidOrdersTotal }}</p>
        </div>
    </div>

    <!-- 1. Antrean Pendaftaran Event Organizer (Card 1 Join EO) -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs">
        <h2 class="text-base font-bold text-[#0A0A0A] mb-4">Antrean Pendaftaran EO ({{ $pendingEoList->count() }})</h2>
        @if ($pendingEoList->isEmpty())
            <p class="text-xs text-[#52525B] py-6 text-center bg-[#FAFAFA] rounded-xl">
                Tidak ada pendaftaran mitra Event Organizer baru yang menunggu verifikasi.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-[#52525B]">
                            <th class="py-2.5 pr-4 font-semibold">Organisasi / EO</th>
                            <th class="py-2.5 px-4 font-semibold">Penanggung Jawab (PIC)</th>
                            <th class="py-2.5 px-4 font-semibold">Kontak WhatsApp</th>
                            <th class="py-2.5 px-4 font-semibold">Rekening Payout</th>
                            <th class="py-2.5 pl-4 font-semibold">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($pendingEoList as $eoApp)
                            <tr>
                                <td class="py-3 pr-4 font-bold text-[#0A0A0A]">{{ $eoApp->organization_name ?? $eoApp->name }}</td>
                                <td class="py-3 px-4 text-[#52525B]">
                                    <span class="font-semibold text-[#0A0A0A] block">{{ $eoApp->name }}</span>
                                    <span class="text-[11px]">{{ $eoApp->email }}</span>
                                </td>
                                <td class="py-3 px-4 text-[#0A0A0A] font-medium">{{ $eoApp->phone ?? '-' }}</td>
                                <td class="py-3 px-4 text-[#52525B]">
                                    <strong class="text-[#0A0A0A]">{{ $eoApp->bank_name ?? '-' }}</strong> &bull; {{ $eoApp->bank_account_number ?? '-' }}
                                    <span class="block text-[11px]">a.n. {{ $eoApp->bank_account_name ?? '-' }}</span>
                                </td>
                                <td class="py-3 pl-4">
                                    <div class="inline-flex flex-wrap items-center gap-1.5">
                                        <button type="button" onclick="document.getElementById('modalEo-{{ $eoApp->id }}').classList.remove('hidden')"
                                                class="px-2.5 py-1.5 text-[11px] font-semibold text-[#0A0A0A] bg-[#FAFAFA] border border-zinc-300 hover:bg-zinc-100 rounded-lg cursor-pointer">
                                            Lihat Detail
                                        </button>
                                        <form action="{{ route('admin.eo.approve', $eoApp->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 text-[11px] font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg cursor-pointer">
                                                Approve EO
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.eo.reject', $eoApp->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 text-[11px] font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg cursor-pointer">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Detail Pendaftaran EO -->
                            <div id="modalEo-{{ $eoApp->id }}" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
                                <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-5 text-xs">
                                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                                        <h3 class="text-base font-bold text-[#0A0A0A]">Detail Pendaftaran Event Organizer</h3>
                                        <button type="button" onclick="document.getElementById('modalEo-{{ $eoApp->id }}').classList.add('hidden')"
                                                class="text-[#52525B] hover:text-[#0A0A0A] font-bold cursor-pointer">&#10005;</button>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 bg-[#FAFAFA] p-4 rounded-xl border border-zinc-200/80">
                                        <div>
                                            <p class="text-[11px] text-[#52525B]">Nama Organisasi / EO</p>
                                            <p class="font-bold text-[#0A0A0A] mt-0.5">{{ $eoApp->organization_name ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[11px] text-[#52525B]">Nama Penanggung Jawab (PIC)</p>
                                            <p class="font-bold text-[#0A0A0A] mt-0.5">{{ $eoApp->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[11px] text-[#52525B]">Email Akun</p>
                                            <p class="font-semibold text-[#0A0A0A] mt-0.5">{{ $eoApp->email }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[11px] text-[#52525B]">Nomor WhatsApp Aktif</p>
                                            <p class="font-bold text-emerald-700 mt-0.5">{{ $eoApp->phone ?? '-' }}</p>
                                        </div>
                                        <div class="col-span-2 pt-2 border-t border-zinc-200">
                                            <p class="text-[11px] text-[#52525B]">Rekening Pencairan Dana (Payout)</p>
                                            <p class="font-bold text-[#0A0A0A] mt-0.5">
                                                {{ $eoApp->bank_name ?? '-' }} &bull; {{ $eoApp->bank_account_number ?? '-' }} (a.n. {{ $eoApp->bank_account_name ?? '-' }})
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2 pt-2">
                                        <button type="button" onclick="document.getElementById('modalEo-{{ $eoApp->id }}').classList.add('hidden')"
                                                class="px-4 py-2 text-xs font-semibold text-[#52525B] cursor-pointer">Tutup</button>
                                        <form action="{{ route('admin.eo.reject', $eoApp->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl cursor-pointer">Reject</button>
                                        </form>
                                        <form action="{{ route('admin.eo.approve', $eoApp->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl cursor-pointer">Approve & Aktifkan EO</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 2. Antrean Pengajuan Event (Beserta Preview Gambar Poster & Detail Tiket) -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs">
        <h2 class="text-base font-bold text-[#0A0A0A] mb-4">Antrean Pengajuan Event ({{ $pendingEventsList->count() }})</h2>
        @if ($pendingEventsList->isEmpty())
            <p class="text-xs text-[#52525B] py-6 text-center bg-[#FAFAFA] rounded-xl">
                Tidak ada pengajuan acara baru yang menunggu persetujuan saat ini.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-[#52525B]">
                            <th class="py-2.5 pr-4 font-semibold">Judul & Kategori</th>
                            <th class="py-2.5 px-4 font-semibold">Penyelenggara (EO)</th>
                            <th class="py-2.5 px-4 font-semibold">Jadwal & Lokasi</th>
                            <th class="py-2.5 pl-4 font-semibold">Aksi Moderasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($pendingEventsList as $event)
                            @php
                                $posterFile = $event->poster ?? $event->image ?? null;
                                $posterSrc = $posterFile ? asset('storage/' . ltrim($posterFile, '/')) : ($event->banner_url ?? null);
                            @endphp
                            <tr>
                                <td class="py-3 pr-4">
                                    <strong class="text-[#0A0A0A] block">{{ $event->title }}</strong>
                                    <span class="text-[11px] text-[#52525B]">{{ $event->category ?? 'Umum' }}</span>
                                </td>
                                <td class="py-3 px-4 text-[#52525B]">
                                    <span class="font-semibold text-[#0A0A0A] block">{{ $event->eo->organization_name ?? $event->eo->name ?? 'Event Organizer' }}</span>
                                    <span class="text-[11px]">WA: {{ $event->eo->phone ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 text-[#52525B]">
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }} ({{ $event->start_time ?? '18:00' }} - {{ $event->end_time ?? '23:00' }}) &bull; {{ $event->venue_name }}
                                </td>
                                <td class="py-3 pl-4">
                                    <div class="inline-flex flex-wrap items-center gap-1.5">
                                        <button type="button" onclick="document.getElementById('modalEvent-{{ $event->id }}').classList.remove('hidden')"
                                                class="px-2.5 py-1.5 text-[11px] font-semibold text-[#0A0A0A] bg-[#FAFAFA] border border-zinc-300 hover:bg-zinc-100 rounded-lg cursor-pointer">
                                            Lihat Detail & Poster
                                        </button>
                                        <form action="{{ route('admin.events.approve', $event->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 text-[11px] font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg cursor-pointer">
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.events.reject', $event->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 text-[11px] font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg cursor-pointer">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Detail Pengajuan Event + Preview Poster + Tiket -->
                            <div id="modalEvent-{{ $event->id }}" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 overflow-y-auto">
                                <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl space-y-5 text-xs">
                                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                                        <div>
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-zinc-100 rounded-full">{{ $event->category ?? 'Event' }}</span>
                                            <h3 class="text-base font-bold text-[#0A0A0A] mt-1">{{ $event->title }}</h3>
                                        </div>
                                        <button type="button" onclick="document.getElementById('modalEvent-{{ $event->id }}').classList.add('hidden')"
                                                class="text-[#52525B] hover:text-[#0A0A0A] font-bold cursor-pointer">&#10005;</button>
                                    </div>

                                    <!-- Preview Poster / Banner -->
                                    <div class="rounded-xl overflow-hidden border border-zinc-200 bg-zinc-900">
                                        @if ($posterSrc)
                                            <img src="{{ $posterSrc }}" alt="Poster {{ $event->title }}" class="w-full max-h-64 object-cover">
                                        @else
                                            <div class="py-10 text-center text-zinc-400">
                                                Belum ada poster/banner gambar yang diunggah EO untuk acara ini.
                                            </div>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-[#FAFAFA] p-4 rounded-xl border border-zinc-200/80">
                                        <div>
                                            <p class="text-[11px] text-[#52525B]">Penyelenggara (EO)</p>
                                            <p class="font-bold text-[#0A0A0A] mt-0.5">{{ $event->eo->organization_name ?? $event->eo->name ?? '-' }}</p>
                                            <p class="text-[11px] text-[#52525B]">PIC: {{ $event->eo->name ?? '-' }} &bull; WA: {{ $event->eo->phone ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[11px] text-[#52525B]">Tanggal & Waktu</p>
                                            <p class="font-bold text-[#0A0A0A] mt-0.5">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</p>
                                            <p class="text-[11px] text-[#52525B]">{{ $event->start_time ?? '18:00' }} - {{ $event->end_time ?? '23:00' }} WIB (Open Gate {{ $event->open_gate ?? '-' }})</p>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <p class="text-[11px] text-[#52525B]">Lokasi Venue</p>
                                            <p class="font-bold text-[#0A0A0A] mt-0.5">{{ $event->venue_name }}</p>
                                            <p class="text-[11px] text-[#52525B]">{{ $event->venue_address ?? $event->location ?? '-' }}</p>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <p class="text-[11px] text-[#52525B]">Deskripsi Acara</p>
                                            <p class="text-[#0A0A0A] mt-0.5 leading-relaxed">{{ $event->description }}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <p class="font-bold text-[#0A0A0A] mb-2">Kategori & Harga Tiket yang Diajukan</p>
                                        <div class="space-y-1.5">
                                            @foreach ($event->ticketCategories as $cat)
                                                <div class="flex items-center justify-between px-3.5 py-2 rounded-lg bg-[#FAFAFA] border border-zinc-200">
                                                    <span class="font-semibold text-[#0A0A0A]">{{ $cat->name }} (Kuota: {{ $cat->quota }})</span>
                                                    <span class="font-bold text-[#0A0A0A]">Rp {{ number_format((float) $cat->price, 0, ',', '.') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-100">
                                        <button type="button" onclick="document.getElementById('modalEvent-{{ $event->id }}').classList.add('hidden')"
                                                class="px-4 py-2 text-xs font-semibold text-[#52525B] cursor-pointer">Tutup</button>
                                        <form action="{{ route('admin.events.reject', $event->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl cursor-pointer">Reject</button>
                                        </form>
                                        <form action="{{ route('admin.events.approve', $event->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl cursor-pointer">Approve & Tayangkan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 3. Status Pengajuan Payout EO -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-[#0A0A0A]">Status Pengajuan Payout EO ({{ $recentPayouts->count() }})</h2>
            <a href="{{ route('admin.payouts.index') }}" class="text-xs font-semibold text-[#0A0A0A] underline">
                Kelola Semua Payout &rarr;
            </a>
        </div>

        @if ($recentPayouts->isEmpty())
            <p class="text-xs text-[#52525B] py-6 text-center bg-[#FAFAFA] rounded-xl">
                Belum ada pengajuan pencairan dana dari Event Organizer.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-[#52525B]">
                            <th class="py-2.5 pr-4 font-semibold">Penyelenggara (EO)</th>
                            <th class="py-2.5 px-4 font-semibold">Rekening Tujuan</th>
                            <th class="py-2.5 px-4 font-semibold">Nominal</th>
                            <th class="py-2.5 px-4 font-semibold">Status</th>
                            <th class="py-2.5 pl-4 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($recentPayouts as $p)
                            @php
                                $pNominal = (float) ($p->net_amount ?? $p->gross_amount ?? $p->amount ?? $p->total_amount ?? $p->requested_amount ?? $p->payout_amount ?? 0);
                                $pRekening = $p->bank_account_number ?? $p->account_number ?? '-';
                                $pPemilik = $p->bank_account_holder ?? $p->bank_account_name ?? $p->account_holder ?? '-';
                            @endphp
                            <tr>
                                <td class="py-3 pr-4">
                                    <strong class="text-[#0A0A0A] block">{{ $p->eo_name ?? 'EO' }}</strong>
                                    <span class="text-[11px] text-[#52525B]">{{ $p->eo_email ?? '' }}</span>
                                </td>
                                <td class="py-3 px-4 text-[#52525B]">
                                    <strong class="text-[#0A0A0A]">{{ $p->bank_name ?? '-' }}</strong> &bull; {{ $pRekening }}
                                    <span class="block text-[11px]">a.n. {{ $pPemilik }}</span>
                                </td>
                                <td class="py-3 px-4 font-bold text-[#0A0A0A]">
                                    Rp {{ number_format($pNominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full uppercase
                                        {{ in_array($p->status, ['approved', 'paid', 'completed'], true) ? 'bg-emerald-100 text-emerald-800' : (in_array($p->status, ['rejected', 'declined'], true) ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td class="py-3 pl-4">
                                    @if ($p->status === 'pending')
                                        <form action="{{ route('admin.payouts.update', $p->id) }}" method="POST" class="inline-flex gap-1.5">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="status" value="approved"
                                                    class="px-2.5 py-1 text-[11px] font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg cursor-pointer">
                                                Setujui
                                            </button>
                                            <button type="submit" name="status" value="rejected"
                                                    class="px-2.5 py-1 text-[11px] font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg cursor-pointer">
                                                Tolak
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-[#52525B]">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 4. Seluruh Event di Sistem -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs">
        <h2 class="text-base font-bold text-[#0A0A0A] mb-4">Seluruh Event di Sistem ({{ $allEventsList->count() }})</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-zinc-200 text-[#52525B]">
                        <th class="py-2.5 pr-4 font-semibold">Judul Event</th>
                        <th class="py-2.5 px-4 font-semibold">Penyelenggara (EO)</th>
                        <th class="py-2.5 px-4 font-semibold">Tanggal</th>
                        <th class="py-2.5 pl-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach ($allEventsList as $event)
                        <tr>
                            <td class="py-3 pr-4 font-semibold text-[#0A0A0A]">{{ $event->title }}</td>
                            <td class="py-3 px-4 text-[#52525B]">{{ $event->eo->organization_name ?? $event->eo->name ?? 'Event Organizer' }}</td>
                            <td class="py-3 px-4 text-[#52525B]">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</td>
                            <td class="py-3 pl-4">
                                <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full capitalize
                                    {{ $event->status === 'published' ? 'bg-emerald-100 text-emerald-800' : ($event->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $event->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection