@extends('layouts.app')

@section('title', 'Pencairan Dana (Payout) - Tix and You')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-zinc-100 text-[#0A0A0A] rounded-full mb-1.5">
                Keuangan Event Organizer
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#0A0A0A]">Pencairan Dana (Payout)</h1>
            <p class="text-xs text-[#52525B] mt-0.5">Ajukan penarikan saldo penjualan tiket ke rekening terdaftar Anda.</p>
        </div>
        <a href="{{ route('eo.dashboard') }}"
           class="px-4 py-2 text-xs font-semibold text-[#0A0A0A] bg-[#FAFAFA] border border-zinc-300 hover:bg-zinc-100 rounded-xl transition self-start sm:self-auto">
            &larr; Kembali ke Dashboard EO
        </a>
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
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-300 text-red-900 text-xs space-y-1">
            @foreach ($errors->all() as $err)
                <p>&bull; {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <!-- Ringkasan Saldo -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Total Pendapatan Lunas</p>
            <p class="text-lg sm:text-xl font-bold text-[#0A0A0A] mt-1">Rp {{ number_format((float) $grossRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Sudah Dicairkan</p>
            <p class="text-lg sm:text-xl font-bold text-emerald-700 mt-1">Rp {{ number_format((float) $approvedAmount, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Menunggu Verifikasi</p>
            <p class="text-lg sm:text-xl font-bold text-amber-600 mt-1">Rp {{ number_format((float) $pendingAmount, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-[#0A0A0A] shadow-xs">
            <p class="text-xs text-[#52525B]">Saldo Tersedia</p>
            <p class="text-lg sm:text-xl font-bold text-[#0A0A0A] mt-1">Rp {{ number_format((float) $availableBalance, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Form Pengajuan Payout -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs">
        <h2 class="text-base font-bold text-[#0A0A0A] mb-4">Form Pengajuan Pencairan Dana</h2>
        <form action="{{ route('eo.payouts.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-[#0A0A0A] mb-1">Nominal Pencairan (Min. Rp 50.000)</label>
                <input type="number" name="amount" min="50000" max="{{ (int) $availableBalance }}" step="1000"
                       value="{{ old('amount', min(100000, (int) $availableBalance)) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
                <p class="text-[11px] text-[#52525B] mt-1">Maksimal penarikan: Rp {{ number_format((float) $availableBalance, 0, ',', '.') }}</p>
            </div>

            <div>
                <label class="block font-semibold text-[#0A0A0A] mb-1">Nama Bank</label>
                <select name="bank_name" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 bg-white focus:outline-none focus:border-[#0A0A0A]">
                    @foreach (['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga', 'Permata', 'BSI'] as $bank)
                        <option value="{{ $bank }}" {{ old('bank_name') === $bank ? 'selected' : '' }}>{{ $bank }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-[#0A0A0A] mb-1">Nomor Rekening</label>
                <input type="text" name="account_number"
                       value="{{ old('account_number', auth()->user()->bank_account_number ?? '') }}" required
                       placeholder="Contoh: 1234567890"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
            </div>

            <div>
                <label class="block font-semibold text-[#0A0A0A] mb-1">Nama Pemilik Rekening</label>
                <input type="text" name="account_holder"
                       value="{{ old('account_holder', auth()->user()->bank_account_name ?? auth()->user()->name ?? '') }}" required
                       placeholder="Sesuai buku tabungan"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
            </div>

            <div class="sm:col-span-2 flex justify-end pt-2">
                <button type="submit"
                        class="px-5 py-2.5 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                    Kirim Pengajuan Pencairan
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Pengajuan Payout -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs">
        <h2 class="text-base font-bold text-[#0A0A0A] mb-4">Riwayat Pengajuan Pencairan ({{ $payouts->count() }})</h2>
        @if ($payouts->isEmpty())
            <p class="text-xs text-[#52525B] py-6 text-center bg-[#FAFAFA] rounded-xl">
                Belum ada riwayat pengajuan pencairan dana.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-zinc-200 text-[#52525B]">
                            <th class="py-2.5 pr-4 font-semibold">Tanggal</th>
                            <th class="py-2.5 px-4 font-semibold">Rekening Tujuan</th>
                            <th class="py-2.5 px-4 font-semibold">Nominal</th>
                            <th class="py-2.5 px-4 font-semibold">Status</th>
                            <th class="py-2.5 pl-4 font-semibold">Catatan Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($payouts as $item)
                            <tr>
                                <td class="py-3 pr-4 text-[#52525B]">{{ $item->created_at ?? '-' }}</td>
                                <td class="py-3 px-4 font-medium text-[#0A0A0A]">
                                    {{ $item->bank_name ?? '-' }} &bull; {{ $item->display_account_number ?? '-' }}
                                    <span class="block text-[11px] text-[#52525B]">a.n. {{ $item->display_account_holder ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 font-bold text-[#0A0A0A]">
                                    Rp {{ number_format((float) ($item->display_amount ?? 0), 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full uppercase
                                        {{ in_array($item->status, ['approved', 'paid', 'completed'], true) ? 'bg-emerald-100 text-emerald-800' : (in_array($item->status, ['rejected', 'declined'], true) ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="py-3 pl-4 text-[#52525B]">{{ $item->display_notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection