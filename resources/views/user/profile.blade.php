@extends('layouts.app')

@section('title', 'Profil & Tiket Saya - Tix and You')

@section('content')
<div class="space-y-8">
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs text-red-700 space-y-1">
            @foreach ($errors->all() as $error)
                <p>&bull; {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Header Ringkasan Akun & Status Mitra EO -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-zinc-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-[#0A0A0A] text-white text-2xl font-extrabold flex items-center justify-center shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-[#0A0A0A]">{{ $user->name }}</h1>
                    <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase bg-zinc-100 text-[#0A0A0A] border border-zinc-200 rounded-full">
                        {{ $user->role === 'user' ? 'user' : $user->role }}
                    </span>
                </div>
                <p class="text-xs text-[#52525B]">{{ $user->email }} &bull; {{ $user->phone ?: 'Nomor WhatsApp belum diisi' }}</p>
            </div>
        </div>

        <!-- Status Pengajuan EO -->
        <div class="flex flex-col sm:items-end gap-2">
            @if ($user->eo_status === 'pending')
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 rounded-xl">
                    &#9203; Pengajuan EO: Menunggu Verifikasi Admin
                </span>
                <p class="text-[11px] text-[#52525B]">Organisasi: <strong>{{ $user->organization_name }}</strong></p>
            @elseif ($user->eo_status === 'rejected')
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold bg-red-50 text-red-700 border border-red-200 rounded-xl">
                    Pengajuan EO Ditolak
                </span>
                <a href="{{ route('join.eo') }}" class="text-xs font-bold text-[#0A0A0A] underline">Ajukan Ulang sebagai EO &rarr;</a>
            @else
                <a href="{{ route('join.eo') }}"
                   class="px-4 py-2.5 text-xs font-semibold text-[#0A0A0A] bg-white hover:bg-zinc-100 border border-zinc-300 rounded-xl transition">
                    Ingin buat event? Daftar sebagai EO
                </a>
            @endif
        </div>
    </div>

    <!-- Navigasi Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-zinc-200 pb-3">
        <button type="button" onclick="switchProfileTab('tickets', this)"
                class="profile-tab-btn px-4 py-2 text-xs font-semibold rounded-xl bg-[#0A0A0A] text-white transition cursor-pointer">
            Tiket Saya ({{ $paidOrders->count() }})
        </button>
        <button type="button" onclick="switchProfileTab('pending', this)"
                class="profile-tab-btn px-4 py-2 text-xs font-semibold rounded-xl bg-white text-[#52525B] border border-zinc-200 hover:border-[#0A0A0A] transition cursor-pointer">
            Menunggu Pembayaran ({{ $pendingOrders->count() }})
        </button>
        <button type="button" onclick="switchProfileTab('history', this)"
                class="profile-tab-btn px-4 py-2 text-xs font-semibold rounded-xl bg-white text-[#52525B] border border-zinc-200 hover:border-[#0A0A0A] transition cursor-pointer">
            Semua Riwayat ({{ $orders->count() }})
        </button>
        <button type="button" onclick="switchProfileTab('settings', this)"
                class="profile-tab-btn px-4 py-2 text-xs font-semibold rounded-xl bg-white text-[#52525B] border border-zinc-200 hover:border-[#0A0A0A] transition cursor-pointer">
            Pengaturan Akun
        </button>
    </div>

    <!-- TAB 1: TIKET SAYA (LUNAS - E-TICKET QR & PDF SAMA PERSIS DENGAN EMAIL) -->
    <section id="tab-tickets" class="profile-tab-panel space-y-4">
        @if ($paidOrders->isEmpty())
            <div class="bg-white rounded-2xl p-10 text-center border border-zinc-200/80 space-y-3">
                <p class="text-sm font-bold text-[#0A0A0A]">Belum ada E-Ticket aktif</p>
                <p class="text-xs text-[#52525B]">Tiket yang sudah Anda bayar akan otomatis tersimpan di sini lengkap dengan QR Code check-in dan file PDF.</p>
                <a href="{{ route('home') }}" class="inline-block px-5 py-2.5 text-xs font-semibold text-white bg-[#0A0A0A] rounded-xl">
                    Jelajahi Event
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach ($paidOrders as $order)
                    @php
                        $invoiceCode = $order->invoice_number ?? $order->order_code ?? ('ORD-' . $order->id);
                        $firstTicket = $order->tickets->first() ?? null;
                        $ticketParam = $firstTicket?->ticket_code ?? $firstTicket?->qr_token ?? $order->invoice_number ?? $order->order_code ?? $order->id;
                        $eticketUrl = route('checkout.confirm', $order->id);
                        $pdfUrl = route('checkout.pdf', $order->id);
                        $totalQty = $order->tickets->count() ?: ($order->items->sum('quantity') ?: 1);
                    @endphp
                    <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
                        <div class="p-5 space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-full">
                                    E-TICKET AKTIF &bull; {{ $totalQty }} TIKET
                                </span>
                                <span class="text-[11px] font-mono text-[#52525B]">{{ $invoiceCode }}</span>
                            </div>

                            <div>
                                <h3 class="text-base font-extrabold text-[#0A0A0A]">{{ $order->event->title ?? 'Event Tix and You' }}</h3>
                                @if ($order->event)
                                    <p class="text-xs text-[#52525B] mt-1">
                                        {{ \Carbon\Carbon::parse($order->event->event_date)->translatedFormat('l, d F Y') }} &bull; {{ $order->event->start_time ?? '18:30' }} WIB
                                    </p>
                                    <p class="text-xs text-[#52525B] mt-0.5">&#128205; {{ $order->event->venue_name }}</p>
                                @endif
                            </div>

                            @if ($order->tickets->isNotEmpty())
                                <div class="pt-2 border-t border-zinc-100 space-y-1">
                                    @foreach ($order->tickets as $t)
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="font-semibold text-[#0A0A0A]">{{ $t->ticketCategory->name ?? 'Tiket Masuk' }}</span>
                                            <span class="font-mono text-[#52525B]">{{ $t->ticket_code ?? $t->qr_token }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="px-5 py-3.5 bg-[#FAFAFA] border-t border-zinc-200/80 flex items-center justify-between gap-2">
                            <a href="{{ $eticketUrl }}" target="_blank"
                               class="flex-1 text-center py-2 px-3 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition">
                                Lihat E-Ticket QR
                            </a>
                            @if ($pdfUrl)
                                <a href="{{ $pdfUrl }}" target="_blank"
                                   class="py-2 px-3.5 text-xs font-semibold text-[#0A0A0A] bg-white hover:bg-zinc-100 border border-zinc-300 rounded-xl transition">Download PDF</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- TAB 2: MENUNGGU PEMBAYARAN -->
    <section id="tab-pending" class="profile-tab-panel hidden space-y-4">
        @if ($pendingOrders->isEmpty())
            <div class="bg-white rounded-2xl p-10 text-center border border-zinc-200/80 text-xs text-[#52525B]">
                Tidak ada pesanan yang menunggu pembayaran saat ini.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($pendingOrders as $order)
                    @php
                        $invoiceCode = $order->invoice_number ?? $order->order_code ?? ('ORD-' . $order->id);
                        $totalNominal = $order->total_amount ?? $order->total_price ?? 0;
                        $payUrl = route('checkout.payment', $order->id);
                    @endphp
                    <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full">PENDING</span>
                                <span class="text-xs font-mono text-[#52525B]">{{ $invoiceCode }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-[#0A0A0A]">{{ $order->event->title ?? 'Pesanan Tiket' }}</h4>
                            <p class="text-xs text-[#52525B]">Total Tagihan: <strong class="text-[#0A0A0A]">Rp {{ number_format((float) $totalNominal, 0, ',', '.') }}</strong></p>
                        </div>

                        <a href="{{ $payUrl }}"
                           class="px-4 py-2.5 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition text-center">
                            Lanjutkan Pembayaran
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- TAB 3: SEMUA RIWAYAT TRANSAKSI -->
    <section id="tab-history" class="profile-tab-panel hidden">
        <div class="bg-white rounded-2xl border border-zinc-200/80 overflow-hidden">
            @if ($orders->isEmpty())
                <div class="p-10 text-center text-xs text-[#52525B]">Belum ada riwayat transaksi.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#FAFAFA] border-b border-zinc-200 text-[#52525B]">
                            <tr>
                                <th class="py-3.5 px-5 font-bold">Kode Invoice</th>
                                <th class="py-3.5 px-5 font-bold">Acara</th>
                                <th class="py-3.5 px-5 font-bold">Tanggal Order</th>
                                <th class="py-3.5 px-5 font-bold">Total</th>
                                <th class="py-3.5 px-5 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            @foreach ($orders as $order)
                                @php
                                    $invoiceCode = $order->invoice_number ?? $order->order_code ?? ('ORD-' . $order->id);
                                    $totalNominal = $order->total_amount ?? $order->total_price ?? 0;
                                    $st = strtoupper((string) ($order->payment_status ?? $order->status ?? 'PENDING'));
                                @endphp
                                <tr>
                                    <td class="py-3.5 px-5 font-mono text-[#0A0A0A]">{{ $invoiceCode }}</td>
                                    <td class="py-3.5 px-5 font-semibold text-[#0A0A0A]">{{ $order->event->title ?? '-' }}</td>
                                    <td class="py-3.5 px-5 text-[#52525B]">{{ $order->created_at?->format('d M Y, H:i') }}</td>
                                    <td class="py-3.5 px-5 font-bold text-[#0A0A0A]">Rp {{ number_format((float) $totalNominal, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-5">
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full {{ in_array($st, ['PAID', 'SETTLEMENT', 'SUCCESS']) ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-100 text-zinc-700' }}">
                                            {{ $st }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    <!-- TAB 4: PENGATURAN AKUN -->
    <section id="tab-settings" class="profile-tab-panel hidden grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Edit Data Diri -->
        <form action="{{ route('profile.update') }}" method="POST" class="bg-white rounded-2xl p-6 border border-zinc-200/80 space-y-4">
            @csrf
            @method('PUT')
            <h3 class="text-sm font-bold text-[#0A0A0A] pb-2 border-b border-zinc-100">Edit Data Diri</h3>

            <div>
                <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Alamat Email</label>
                <input type="email" value="{{ $user->email }}" readonly
                       class="w-full px-4 py-2.5 text-sm bg-zinc-100 text-zinc-500 border border-zinc-200 rounded-xl">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Nomor WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                       placeholder="081234567890"
                       class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
            </div>

            <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                Simpan Perubahan
            </button>
        </form>

        <!-- Ubah Kata Sandi -->
        <form action="{{ route('profile.password') }}" method="POST" class="bg-white rounded-2xl p-6 border border-zinc-200/80 space-y-4">
            @csrf
            @method('PUT')
            <h3 class="text-sm font-bold text-[#0A0A0A] pb-2 border-b border-zinc-100">Ubah Kata Sandi</h3>

            <div>
                <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" required
                       class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Kata Sandi Baru</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter"
                       class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
            </div>

            <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                Perbarui Kata Sandi
            </button>
        </form>
    </section>
</div>

@push('scripts')
<script>
    function switchProfileTab(tabName, btn) {
        document.querySelectorAll('.profile-tab-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById('tab-' + tabName)?.classList.remove('hidden');

        document.querySelectorAll('.profile-tab-btn').forEach(b => {
            b.classList.remove('bg-[#0A0A0A]', 'text-white');
            b.classList.add('bg-white', 'text-[#52525B]', 'border', 'border-zinc-200');
        });
        btn.classList.remove('bg-white', 'text-[#52525B]', 'border', 'border-zinc-200');
        btn.classList.add('bg-[#0A0A0A]', 'text-white');
    }
</script>
@endpush
@endsection