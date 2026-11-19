@extends('layouts.app')

@section('title', 'Review Pesanan - Tix and You')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('checkout.participant') }}" class="inline-flex items-center text-xs font-semibold text-[#52525B] hover:text-[#0A0A0A]">
        &larr; Kembali Ubah Data Peserta
    </a>

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-zinc-200/80 shadow-xs space-y-6">
        <div class="border-b border-zinc-100 pb-4">
            <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-zinc-100 text-[#0A0A0A] rounded-full mb-1.5">
                Langkah 3 dari 3 • Konfirmasi & Kunci Kuota
            </span>
            <h1 class="text-xl font-bold text-[#0A0A0A]">Ringkasan Checkout</h1>
            <p class="text-xs text-[#52525B] mt-0.5">{{ $cart['event_title'] }} • {{ $cart['event_date'] }} ({{ $cart['venue_name'] }})</p>
        </div>

        <!-- Detail Pemesan -->
        <div class="p-4 rounded-xl bg-[#FAFAFA] border border-zinc-200/80 text-xs space-y-1">
            <p class="font-bold text-[#0A0A0A]">Pemesan: {{ $participant['buyer_name'] }}</p>
            <p class="text-[#52525B]">{{ $participant['buyer_email'] }} • {{ $participant['buyer_phone'] }}</p>
        </div>

        <!-- Rincian Tiket -->
        <div class="space-y-2.5">
            <h2 class="text-xs font-bold uppercase tracking-wider text-[#52525B]">Rincian Tiket ({{ $cart['total_tickets'] }} Tiket)</h2>
            @foreach ($cart['items'] as $item)
                <div class="flex items-center justify-between text-sm py-2 border-b border-zinc-100">
                    <div>
                        <p class="font-semibold text-[#0A0A0A]">{{ $item['name'] }} <span class="text-xs text-[#52525B]">x{{ $item['qty'] }}</span></p>
                    </div>
                    <p class="font-semibold text-[#0A0A0A]">Rp {{ number_format($item['line_total'], 0, ',', '.') }}</p>
                </div>
            @endforeach

            <div class="flex items-center justify-between text-xs text-[#52525B] pt-1">
                <span>Biaya Layanan Platform (5%)</span>
                <span>Rp {{ number_format($cart['service_fee'], 0, ',', '.') }}</span>
            </div>

            <div class="flex items-center justify-between text-base font-bold text-[#0A0A0A] pt-3 border-t border-zinc-200">
                <span>Total Pembayaran</span>
                <span>Rp {{ number_format($cart['total_amount'], 0, ',', '.') }}</span>
            </div>
        </div>

        <form action="{{ route('checkout.process') }}" method="POST" class="pt-2">
            @csrf
            <button type="submit" class="w-full py-3.5 text-xs sm:text-sm font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                Konfirmasi Pesanan & Lanjut Pembayaran
            </button>
        </form>
    </div>
</div>
@endsection