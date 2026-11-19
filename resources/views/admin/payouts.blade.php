@extends('layouts.app')

@section('title', 'Verifikasi Payout EO - Tix and You')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-zinc-100 text-[#0A0A0A] rounded-full mb-1.5">
                Verifikasi Keuangan
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#0A0A0A]">Verifikasi Pencairan Dana (Payout EO)</h1>
            <p class="text-xs text-[#52525B] mt-0.5">Setujui atau tolak permintaan pencairan dana dari Event Organizer.</p>
        </div>
        <a href="{{ auth()->user()->role === 'superadmin' ? route('superadmin.dashboard') : route('admin.dashboard') }}"
           class="px-4 py-2 text-xs font-semibold text-[#0A0A0A] bg-[#FAFAFA] border border-zinc-300 hover:bg-zinc-100 rounded-xl transition self-start sm:self-auto">
            &larr; Kembali ke Dashboard
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

    <!-- Tabel Pengajuan Payout -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs">
        <h2 class="text-base font-bold text-[#0A0A0A] mb-4">Daftar Pengajuan Payout ({{ $payouts->count() }})</h2>
        @if ($payouts->isEmpty())
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
                            <th class="py-2.5 pl-4 font-semibold">Verifikasi & Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($payouts as $item)
                            <tr>
                                <td class="py-3 pr-4">
                                    <strong class="text-[#0A0A0A] block">{{ $item->eo_name ?? 'EO' }}</strong>
                                    <span class="text-[11px] text-[#52525B]">{{ $item->eo_email ?? '' }}</span>
                                </td>
                                <td class="py-3 px-4 text-[#52525B]">
                                    <strong class="text-[#0A0A0A]">{{ $item->bank_name ?? '-' }}</strong> &bull; {{ $item->display_account_number ?? '-' }}
                                    <span class="block text-[11px]">a.n. {{ $item->display_account_holder ?? '-' }}</span>
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
                                <td class="py-3 pl-4">
                                    @if ($item->status === 'pending')
                                        <form action="{{ route('admin.payouts.update', $item->id) }}" method="POST" class="flex flex-wrap items-center gap-1.5">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="notes" placeholder="Catatan (opsional)"
                                                   class="px-2.5 py-1 text-[11px] rounded-lg border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
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
                                        <span class="text-[11px] text-[#52525B]">{{ $item->display_notes ?? 'Selesai diproses' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection