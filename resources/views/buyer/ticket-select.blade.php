@extends('layouts.app')

@section('title', 'Pilih Tiket - ' . $event->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('event.detail', $event->slug) }}" class="inline-flex items-center text-xs font-semibold text-[#52525B] hover:text-[#0A0A0A]">
        &larr; Kembali ke Detail Event
    </a>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 text-red-900 border border-red-200 text-xs font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-zinc-200/80 shadow-xs space-y-6">
        <div class="border-b border-zinc-100 pb-4">
            <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-zinc-100 text-[#0A0A0A] rounded-full mb-1.5">
                Langkah 1 dari 3 • Pilih Kategori Tiket
            </span>
            <h1 class="text-xl font-bold text-[#0A0A0A]">{{ $event->title }}</h1>
            <p class="text-xs text-[#52525B] mt-0.5">{{ $event->event_date->format('d M Y') }} • {{ $event->venue_name }}</p>
        </div>

        <div class="p-3.5 rounded-xl bg-[#FAFAFA] border border-zinc-200/80 flex items-center justify-between text-xs">
            <span class="text-[#52525B]">Batas pembelian per transaksi: <strong class="text-[#0A0A0A]">Maksimal 5 tiket</strong></span>
            <span id="selected-counter" class="font-bold text-[#0A0A0A]">Terpilih: 0 / 5 tiket</span>
        </div>

        <form action="{{ route('checkout.select', $event->slug) }}" method="POST" class="space-y-4">
            @csrf

            @foreach ($event->ticketCategories as $cat)
                @php
                    $remaining = max(0, $cat->quota - $cat->sold);
                    $maxForInput = min(5, $remaining);
                @endphp
                <div class="p-4 rounded-xl border border-zinc-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $remaining === 0 ? 'opacity-50 bg-zinc-50' : 'bg-white' }}">
                    <div>
                        <h3 class="text-sm font-bold text-[#0A0A0A]">{{ $cat->name }}</h3>
                        <p class="text-sm font-semibold text-[#0A0A0A] mt-0.5">
                            {{ $cat->price == 0 ? 'Gratis' : 'Rp ' . number_format($cat->price, 0, ',', '.') }}
                        </p>
                        <p class="text-[11px] text-[#52525B] mt-0.5">
                            {{ $remaining > 0 ? "Sisa kuota: {$remaining} tiket" : 'Tiket Habis (Sold Out)' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs text-[#52525B]">Jumlah:</label>
                        <select name="quantities[{{ $cat->id }}]" data-price="{{ $cat->price }}"
                                {{ $remaining === 0 ? 'disabled' : '' }}
                                onchange="recalculateTickets()"
                                class="ticket-qty-select px-3 py-2 text-sm font-semibold bg-[#FAFAFA] border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                            @for ($i = 0; $i <= $maxForInput; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            @endforeach

            <p id="limit-warning" class="hidden text-xs font-semibold text-red-600">
                Total tiket melebihi batas maksimal 5 tiket per transaksi. Kurangi salah satu kategori.
            </p>

            <div class="pt-4 border-t border-zinc-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-xs text-[#52525B]">Estimasi Subtotal</p>
                    <p id="subtotal-display" class="text-lg font-bold text-[#0A0A0A]">Rp 0</p>
                </div>
                <button id="submit-tickets-btn" type="submit" disabled
                        class="px-6 py-3 text-xs sm:text-sm font-semibold text-white bg-[#0A0A0A] disabled:bg-zinc-300 disabled:cursor-not-allowed hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                    Lanjut Isi Data Pemesan &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function recalculateTickets() {
    const selects = document.querySelectorAll('.ticket-qty-select');
    let totalQty = 0;
    let subtotal = 0;

    selects.forEach(sel => {
        const qty = parseInt(sel.value || '0', 10);
        const price = parseInt(sel.dataset.price || '0', 10);
        totalQty += qty;
        subtotal += (qty * price);
    });

    document.getElementById('selected-counter').textContent = `Terpilih: ${totalQty} / 5 tiket`;
    document.getElementById('subtotal-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');

    const warning = document.getElementById('limit-warning');
    const submitBtn = document.getElementById('submit-tickets-btn');

    if (totalQty > 5) {
        warning.classList.remove('hidden');
        submitBtn.disabled = true;
    } else if (totalQty < 1) {
        warning.classList.add('hidden');
        submitBtn.disabled = true;
    } else {
        warning.classList.add('hidden');
        submitBtn.disabled = false;
    }
}
</script>
@endsection