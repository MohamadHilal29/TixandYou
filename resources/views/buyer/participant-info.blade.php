@extends('layouts.app')

@section('title', 'Data Pemesan & Peserta - Tix and You')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('event.tickets', $cart['event_slug']) }}" class="inline-flex items-center text-xs font-semibold text-[#52525B] hover:text-[#0A0A0A]">
        &larr; Ubah Pilihan Tiket
    </a>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 text-red-900 border border-red-200 text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('checkout.participant.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Data Pemesan Utama -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-zinc-200/80 shadow-xs space-y-4">
            <div class="border-b border-zinc-100 pb-3">
                <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-zinc-100 text-[#0A0A0A] rounded-full mb-1.5">
                    Langkah 2 dari 3 • Data Pemesan & Pemegang Tiket
                </span>
                <h1 class="text-lg font-bold text-[#0A0A0A]">Informasi Pemesan</h1>
                <p class="text-xs text-[#52525B]">E-ticket dan bukti transaksi akan dikirim ke email ini.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-[#0A0A0A] mb-1">Nama Lengkap Pemesan *</label>
                    <input type="text" name="buyer_name" value="{{ old('buyer_name', $user->name ?? '') }}" required
                           class="w-full px-3.5 py-2.5 text-sm bg-[#FAFAFA] border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#0A0A0A] mb-1">Alamat Email *</label>
                    <input type="email" name="buyer_email" value="{{ old('buyer_email', $user->email ?? '') }}" required
                           class="w-full px-3.5 py-2.5 text-sm bg-[#FAFAFA] border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#0A0A0A] mb-1">Nomor WhatsApp / HP *</label>
                    <input type="text" name="buyer_phone" value="{{ old('buyer_phone', $user->phone ?? '') }}" required placeholder="0812xxxxxxxx"
                           class="w-full px-3.5 py-2.5 text-sm bg-[#FAFAFA] border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                </div>
            </div>
        </div>

        <!-- Data Pemegang Setiap Tiket -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-zinc-200/80 shadow-xs space-y-4">
            <h2 class="text-base font-bold text-[#0A0A0A]">Data Pemegang Tiket ({{ $cart['total_tickets'] }} Tiket)</h2>

            @php $ticketIndex = 0; @endphp
            @foreach ($cart['items'] as $item)
                @for ($q = 1; $q <= $item['qty']; $q++)
                    <div class="p-4 rounded-xl bg-[#FAFAFA] border border-zinc-200/80 space-y-3">
                        <p class="text-xs font-bold text-[#0A0A0A]">
                            Tiket #{{ $ticketIndex + 1 }} — Kategori {{ $item['name'] }}
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-[#52525B] mb-1">Nama Sesuai Identitas *</label>
                                <input type="text" name="holders[{{ $ticketIndex }}][name]"
                                       value="{{ old("holders.{$ticketIndex}.name", $ticketIndex === 0 ? ($user->name ?? '') : '') }}" required
                                       class="w-full px-3 py-2 text-sm bg-white border border-zinc-300 rounded-lg focus:outline-none focus:border-[#0A0A0A]">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#52525B] mb-1">No. KTP / SIM / Kartu Pelajar *</label>
                                <input type="text" name="holders[{{ $ticketIndex }}][identity_number]"
                                       value="{{ old("holders.{$ticketIndex}.identity_number") }}" required
                                       class="w-full px-3 py-2 text-sm bg-white border border-zinc-300 rounded-lg focus:outline-none focus:border-[#0A0A0A]">
                            </div>
                        </div>
                    </div>
                    @php $ticketIndex++; @endphp
                @endfor
            @endforeach

            <div class="pt-3 flex justify-end">
                <button type="submit" class="px-6 py-3 text-xs sm:text-sm font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                    Lanjut ke Ringkasan Pesanan &rarr;
                </button>
            </div>
        </div>
    </form>
</div>
@endsection