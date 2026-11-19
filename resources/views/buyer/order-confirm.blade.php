@extends('layouts.app')

@section('title', 'E-Ticket Anda - Tix and You')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-900 border border-emerald-200 text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if (session('email_status'))
        @php $mail = session('email_status'); @endphp
        <div class="p-4 rounded-xl border text-xs font-medium {{ $mail['sent'] ? 'bg-zinc-900 text-white border-zinc-800' : 'bg-amber-50 text-amber-900 border-amber-200' }}">
            {{ $mail['message'] }}
        </div>
    @endif

    <!-- Ringkasan Transaksi Lunas -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-zinc-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-100 pb-4">
            <div>
                <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-emerald-100 text-emerald-900 rounded-full mb-1.5">
                    Pembayaran Lunas (PAID)
                </span>
                <h1 class="text-xl font-bold text-[#0A0A0A]">{{ $order->event->title ?? 'Event Tix and You' }}</h1>
                <p class="text-xs text-[#52525B] mt-0.5">
                    {{ optional($order->event->event_date)->format('d M Y') }} • {{ $order->event->start_time ?? '' }} WIB • {{ $order->event->venue_name ?? '' }}
                </p>
            </div>
            <div class="sm:text-right text-xs">
                <p class="text-[#52525B]">Kode Pesanan</p>
                <p class="font-bold text-[#0A0A0A]">{{ $order->order_code ?? $order->order_number ?? ('ORD-' . $order->id) }}</p>
                <p class="text-[#52525B] mt-1">Total: <strong class="text-[#0A0A0A]">Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</strong></p>
            </div>
        </div>

        <p class="text-xs text-[#52525B]">
            Tunjukkan QR Code atau kode tiket di bawah ini kepada petugas pintu masuk (<em>Web Scanner</em>) saat tiba di lokasi acara. Setiap tiket hanya berlaku untuk <strong>1 kali masuk</strong>.
        </p>
    </div>

    <!-- Daftar E-Ticket Individual -->
    <div class="space-y-4">
        @foreach ($tickets as $idx => $ticket)
            @php
                $code = $ticket->ticket_code ?? $ticket->code ?? $ticket->qr_code ?? ('TIX-' . $ticket->id);
                $holder = $ticket->holder_name ?? $ticket->participant_name ?? $order->buyer_name ?? 'Pemegang Tiket';
                $identity = $ticket->holder_identity ?? $ticket->identity_number ?? '-';
                $isUsed = in_array($ticket->status ?? '', ['checked_in', 'used'], true);
            @endphp

            <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="space-y-2 flex-1 w-full">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 text-[11px] font-semibold bg-zinc-100 text-[#0A0A0A] rounded-full">
                            Tiket #{{ $idx + 1 }} • {{ $ticket->category_name ?? 'Reguler' }}
                        </span>
                        @if ($isUsed)
                            <span class="px-2.5 py-0.5 text-[11px] font-semibold bg-zinc-200 text-zinc-700 rounded-full">
                                Sudah Check-In
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 text-[11px] font-semibold bg-emerald-100 text-emerald-900 rounded-full">
                                Aktif / Valid
                            </span>
                        @endif
                    </div>

                    <h2 class="text-base font-bold text-[#0A0A0A]">{{ $holder }}</h2>
                    <p class="text-xs text-[#52525B]">No. Identitas: <span class="font-medium text-[#0A0A0A]">{{ $identity }}</span></p>

                    <div class="pt-2 flex items-center gap-2">
                        <code class="px-3 py-1.5 text-xs font-bold tracking-wider bg-[#FAFAFA] border border-zinc-300 rounded-lg text-[#0A0A0A]">
                            {{ $code }}
                        </code>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $code }}'); this.textContent='Tersalin!'; setTimeout(()=>this.textContent='Salin Kode', 1500);"
                                class="px-3 py-1.5 text-[11px] font-semibold bg-white border border-zinc-300 hover:bg-zinc-100 rounded-lg transition cursor-pointer">
                            Salin Kode
                        </button>
                    </div>
                </div>

                <!-- QR Code Visual -->
                <div class="shrink-0 flex flex-col items-center p-3 rounded-xl bg-[#FAFAFA] border border-zinc-200/80">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($code) }}"
                         alt="QR {{ $code }}"
                         class="w-32 h-32 object-contain bg-white p-1.5 rounded-lg border border-zinc-200">
                    <span class="text-[10px] font-mono font-semibold text-[#52525B] mt-1.5">{{ $code }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex justify-between items-center pt-2">
        <a href="{{ route('home') }}" class="text-xs font-semibold text-[#52525B] hover:text-[#0A0A0A]">
            &larr; Kembali ke Beranda
        </a>
        <a href="{{ route('checkout.pdf', $order->id) }}"
           class="px-5 py-2.5 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition">
            Download E-Ticket (PDF)
        </a>
    </div>
</div>
@endsection