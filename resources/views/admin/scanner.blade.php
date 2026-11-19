@extends('layouts.app')

@section('title', 'Web Scanner Check-In - Tix and You')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Scanner -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-zinc-100 text-[#0A0A0A] rounded-full mb-1.5">
                Gate Check-In System
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#0A0A0A]">Web Scanner Tiket Masuk</h1>
            <p class="text-xs text-[#52525B] mt-0.5">Pindai QR Code melalui kamera atau masukkan kode tiket unik (<code>TIX-XXXXXXXX</code>).</p>
        </div>

        <a href="{{ auth()->user()->role === 'eo' ? route('eo.dashboard') : route('admin.dashboard') }}"
           class="px-4 py-2 text-xs font-semibold text-[#0A0A0A] bg-[#FAFAFA] border border-zinc-300 hover:bg-zinc-100 rounded-xl transition self-start sm:self-auto">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <!-- AlertSukses / AlertGagal sesuai Activity Diagram -->
    @if (session('scan_result'))
        @php $res = session('scan_result'); @endphp
        <div id="scan-notification"
             data-valid="{{ $res['valid'] ? '1' : '0' }}"
             data-title="{{ $res['title'] }}"
             class="rounded-2xl p-6 border-2 shadow-sm transition-all {{ $res['valid'] ? 'bg-emerald-50 border-emerald-500 text-emerald-950' : 'bg-red-50 border-red-500 text-red-950' }}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b {{ $res['valid'] ? 'border-emerald-200' : 'border-red-200' }}">
                <div class="flex items-center gap-3">
                    @if ($res['valid'])
                        <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-full {{ $res['valid'] ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white' }}">
                            {{ $res['badge'] ?? ($res['valid'] ? 'ALERT SUKSES' : 'ALERT GAGAL') }}
                        </span>
                        <h2 class="text-base sm:text-lg font-bold mt-1">{{ $res['title'] }}</h2>
                    </div>
                </div>

                <code class="px-3 py-1.5 text-xs font-mono font-bold break-all rounded-lg bg-white/80 border {{ $res['valid'] ? 'border-emerald-300 text-emerald-950' : 'border-red-300 text-red-950' }} self-start sm:self-center">
                    {{ $res['code'] }}
                </code>
            </div>

            <p class="text-xs sm:text-sm font-medium mt-3 break-words">{{ $res['message'] }}</p>

            @if (!empty($res['holder']))
                <div class="mt-4 pt-3 border-t {{ $res['valid'] ? 'border-emerald-200' : 'border-red-200' }} grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-2.5 rounded-xl bg-white/70">
                        <span class="block text-[10px] uppercase opacity-70">Pemegang Tiket</span>
                        <strong class="text-sm">{{ $res['holder'] }}</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/70">
                        <span class="block text-[10px] uppercase opacity-70">Kategori Tiket</span>
                        <strong class="text-sm">{{ $res['category'] }}</strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white/70">
                        <span class="block text-[10px] uppercase opacity-70">Event</span>
                        <strong class="text-sm">{{ $res['event'] }}</strong>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Statistik Kehadiran -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Total E-Ticket Terbit</p>
            <p class="text-2xl font-bold text-[#0A0A0A] mt-1">{{ $totalIssued }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Sudah Check-In di Gate</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $totalCheckedIn }}</p>
        </div>
    </div>

    <!-- Form Input & Kamera Scanner -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs space-y-5">
        <form id="scanner-form" action="{{ route('admin.scanner.verify') }}" method="POST" class="space-y-3">
            @csrf
            <label for="ticket_code" class="block text-xs font-bold text-[#0A0A0A]">
                Input Kode Tiket / Hasil Scan Barcode
            </label>
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" id="ticket_code" name="ticket_code" autofocus required
                       placeholder="Contoh: TIX-5TPSAQMR"
                       class="flex-1 px-4 py-3 text-sm font-mono uppercase bg-[#FAFAFA] border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                <button type="submit" class="px-6 py-3 text-xs sm:text-sm font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer shrink-0">
                    Verifikasi & Check-In
                </button>
            </div>
        </form>

        <!-- Kamera QR Scanner -->
        <div class="pt-4 border-t border-zinc-100 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-[#0A0A0A]">Pindai Menggunakan Kamera Perangkat</p>
                    <p id="camera-status-text" class="text-[11px] text-[#52525B] break-all">Arahkan QR Code dari E-Ticket PDF ke depan kamera.</p>
                </div>
                <button type="button" id="start-camera-btn" onclick="startQrCamera()"
                        class="px-4 py-2 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer shrink-0 self-start sm:self-auto">
                    Aktifkan Kamera QR
                </button>
            </div>
            <div id="qr-reader" class="hidden max-w-sm mx-auto rounded-xl overflow-hidden border border-zinc-200"></div>
        </div>
    </div>

    <!-- Riwayat Check-In Terbaru -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs">
        <h2 class="text-base font-bold text-[#0A0A0A] mb-4">Riwayat Check-In Terbaru</h2>
        @if ($recentCheckIns->isEmpty())
            <p class="text-xs text-[#52525B] py-6 text-center bg-[#FAFAFA] rounded-xl">Belum ada tiket yang melakukan check-in.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-[#52525B]">
                            <th class="py-2.5 pr-4 font-semibold">Kode Tiket</th>
                            <th class="py-2.5 px-4 font-semibold">Pemegang Tiket</th>
                            <th class="py-2.5 px-4 font-semibold">Kategori</th>
                            <th class="py-2.5 px-4 font-semibold">Event</th>
                            <th class="py-2.5 pl-4 font-semibold">Waktu Scan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($recentCheckIns as $item)
                            <tr>
                                <td class="py-3 pr-4 font-mono font-bold text-[#0A0A0A]">{{ $item->ticket_code ?? $item->code ?? $item->qr_code }}</td>
                                <td class="py-3 px-4 text-[#0A0A0A]">{{ $item->holder_name ?? $item->participant_name ?? $item->buyer_name }}</td>
                                <td class="py-3 px-4 text-[#52525B]">{{ $item->category_name ?? 'Reguler' }}</td>
                                <td class="py-3 px-4 text-[#52525B]">{{ $item->event_title }}</td>
                                <td class="py-3 pl-4 text-[#52525B]">{{ $item->checkin_at ?? $item->checked_in_at ?? $item->updated_at ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const notif = document.getElementById('scan-notification');
    if (!notif) return;

    const isValid = notif.dataset.valid === '1';
    try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        const ctx = new AudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);

        if (isValid) {
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            osc.frequency.setValueAtTime(1174.66, ctx.currentTime + 0.12);
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.28);
        } else {
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(200, ctx.currentTime);
            gain.gain.setValueAtTime(0.18, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.38);
        }
    } catch (e) {}
});

let isSubmittingScan = false;

function startQrCamera() {
    const readerEl = document.getElementById('qr-reader');
    const btn = document.getElementById('start-camera-btn');
    const statusText = document.getElementById('camera-status-text');

    readerEl.classList.remove('hidden');
    btn.disabled = true;
    btn.textContent = 'Kamera Aktif...';
    statusText.textContent = 'Mencari QR Code di depan kamera...';

    const html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 220, height: 220 } },
        (decodedText) => {
            if (isSubmittingScan) return;
            isSubmittingScan = true;

            const cleanCode = decodedText.trim();
            const preview = cleanCode.length > 36 ? cleanCode.substring(0, 36) + '...' : cleanCode;
            document.getElementById('ticket_code').value = cleanCode;
            statusText.innerHTML = '<strong class="text-zinc-900">QR Terbaca (' + preview + '). Memvalidasi ke server...</strong>';

            html5QrCode.stop().finally(() => {
                document.getElementById('scanner-form').submit();
            });
        },
        () => {}
    ).catch(() => {
        readerEl.innerHTML = '<p class="p-4 text-xs text-red-600 text-center">Kamera tidak terdeteksi atau izin ditolak. Gunakan kolom input kode di atas.</p>';
        btn.disabled = false;
        btn.textContent = 'Coba Lagi';
        statusText.textContent = 'Gagal mengakses kamera perangkat.';
    });
}
</script>
@endsection