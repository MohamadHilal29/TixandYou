@extends('layouts.app')

@section('title', 'Pembayaran Pesanan - Tix and You')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 text-red-900 border border-red-200 text-xs font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-zinc-200/80 shadow-xs space-y-6">
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
            <p class="font-bold">Kuota Tiket Terkunci • Menunggu Pembayaran</p>
            <p>Pilih metode pembayaran di bawah ini untuk menyelesaikan transaksi dan menerbitkan E-Ticket QR Code Anda.</p>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-zinc-100">
            <div>
                <p class="text-xs text-[#52525B]">Kode Pesanan</p>
                <h1 class="text-lg font-bold text-[#0A0A0A]">
                    {{ $order->order_code ?? $order->order_number ?? ($details['order_code'] ?? 'ORD-' . $order->id) }}
                </h1>
                <p class="text-xs text-[#52525B] mt-0.5">Event: <strong class="text-[#0A0A0A]">{{ $order->event->title ?? '-' }}</strong></p>
            </div>
            <div class="sm:text-right">
                <p class="text-xs text-[#52525B]">Total Tagihan</p>
                <p class="text-xl font-bold text-[#0A0A0A]">
                    Rp {{ number_format($order->total_amount ?? ($details['total_amount'] ?? 0), 0, ',', '.') }}
                </p>
            </div>
        </div>

        <form action="{{ route('checkout.payment.confirm', $order->id) }}" method="POST" class="space-y-4">
            @csrf
            <p class="text-xs font-bold uppercase tracking-wider text-[#52525B]">Pilih Metode Pembayaran</p>

            <div class="space-y-2.5">
                @foreach ([
                    ['value' => 'QRIS Instan', 'label' => 'QRIS (GoPay, OVO, DANA, ShopeePay, Mobile Banking)', 'tag' => 'Otomatis'],
                    ['value' => 'BCA Virtual Account', 'label' => 'BCA Virtual Account', 'tag' => 'Instan'],
                    ['value' => 'Mandiri Virtual Account', 'label' => 'Mandiri Virtual Account', 'tag' => 'Instan'],
                ] as $index => $method)
                    <label class="flex items-center justify-between p-4 rounded-xl border border-zinc-200/80 bg-[#FAFAFA] hover:border-[#0A0A0A] cursor-pointer transition">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="{{ $method['value'] }}" {{ $index === 0 ? 'checked' : '' }}
                                   class="w-4 h-4 accent-[#0A0A0A]">
                            <div>
                                <p class="text-sm font-bold text-[#0A0A0A]">{{ $method['value'] }}</p>
                                <p class="text-[11px] text-[#52525B]">{{ $method['label'] }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 text-[11px] font-semibold bg-white border border-zinc-200 rounded-full text-[#0A0A0A]">
                            {{ $method['tag'] }}
                        </span>
                    </label>
                @endforeach
            </div>

            <button type="submit" class="w-full py-3.5 text-xs sm:text-sm font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                Konfirmasi & Bayar Sekarang (Simulasi Lunas)
            </button>
        </form>
    </div>
</div>
@endsection