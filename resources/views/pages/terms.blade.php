@extends('layouts.app')

@section('title', 'Terms of Service - Tix and You')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl p-8 sm:p-10 shadow-xs border border-zinc-100">
        <span class="inline-block px-3 py-1 text-xs font-semibold text-[#0A0A0A] bg-zinc-100 rounded-full mb-3">
            Ketentuan Layanan
        </span>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#0A0A0A] mb-4">Terms of Service</h1>

        <!-- Intro Box -->
        <div class="p-4 sm:p-5 rounded-xl bg-[#FAFAFA] border border-zinc-200/70 text-sm text-[#52525B] leading-relaxed mb-8">
            <strong class="text-[#0A0A0A]">Tix and You</strong> merupakan platform penyedia layanan pemesanan dan pengelolaan tiket elektronik (e-ticket) yang menghubungkan Penyelenggara Acara (Event Organizer / EO) dengan Pembeli (Buyer). Dengan menggunakan layanan ini, pengguna dianggap telah memahami dan menyetujui seluruh aturan yang berlaku di dalam sistem.
        </div>

        <!-- 4 Pasal Ketentuan -->
        <div class="space-y-8 text-sm text-[#52525B] leading-relaxed">
            <!-- Pasal 1 -->
            <section>
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-[#0A0A0A] text-white text-xs font-bold flex items-center justify-center shrink-0">1</span>
                    <h2 class="text-base font-bold text-[#0A0A0A]">Pemesanan dan Pembayaran Tiket</h2>
                </div>
                <ul class="space-y-2.5 pl-10">
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                        <span>Seluruh transaksi pembelian tiket menerapkan skema pembayaran lunas (full payment) dan tidak mendukung pembayaran bertahap maupun uang muka (DP).</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                        <span>Kategori tiket yang dijual menggunakan sistem General Admission (seperti Reguler dan Early Bird) tanpa pemilihan nomor kursi (non-reserved seating).</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                        <span>Pesanan dinyatakan sah dan kuota tiket otomatis berkurang setelah sistem menerima konfirmasi pembayaran lunas (paid).</span>
                    </li>
                </ul>
            </section>

            <hr class="border-zinc-100">

            <!-- Pasal 2 -->
            <section>
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-[#0A0A0A] text-white text-xs font-bold flex items-center justify-center shrink-0">2</span>
                    <h2 class="text-base font-bold text-[#0A0A0A]">Penerbitan dan Penggunaan E-Ticket</h2>
                </div>
                <ul class="space-y-2.5 pl-10">
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                        <span>E-ticket diterbitkan secara otomatis dalam format dokumen PDF yang dilengkapi kode unik serta gambar QR Code, lalu dikirimkan ke alamat email utama yang didaftarkan pembeli.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                        <span>Setiap QR Code hanya berlaku untuk satu kali pemindaian (check-in) di lokasi acara.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                        <span>Tiket yang sudah berstatus terpakai (used) atau dibatalkan (refunded) akan ditolak secara otomatis oleh sistem pemindai.</span>
                    </li>
                </ul>
            </section>

            <hr class="border-zinc-100">

            <!-- Pasal 3 -->
            <section>
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-[#0A0A0A] text-white text-xs font-bold flex items-center justify-center shrink-0">3</span>
                    <h2 class="text-base font-bold text-[#0A0A0A]">Pembatalan Acara dan Pengembalian Dana (Refund)</h2>
                </div>
                <ul class="space-y-2.5 pl-10">
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                        <span>Pengembalian dana secara massal (mass refund) hanya akan diproses apabila acara dibatalkan secara resmi oleh pihak penyelenggara.</span>
                    </li>
                </ul>
            </section>

            <hr class="border-zinc-100">

            <!-- Pasal 4 -->
            <section>
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-7 h-7 rounded-lg bg-[#0A0A0A] text-white text-xs font-bold flex items-center justify-center shrink-0">4</span>
                    <h2 class="text-base font-bold text-[#0A0A0A]">Ketentuan Penyelenggara Acara (EO) & Pencairan Dana (Payout)</h2>
                </div>
                <ul class="space-y-2.5 pl-10">
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                        <span>Setiap pengajuan draf acara baru oleh EO akan melalui proses peninjauan (review) dan persetujuan oleh tim Admin sebelum dipublikasikan secara resmi.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                        <span>EO wajib melengkapi profil usaha atau komunitas serta mendaftarkan rekening bank yang valid untuk kebutuhan pencairan dana.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                        <span>Pencairan dana hasil penjualan tiket ke rekening EO diproses maksimal H+7 setelah acara berakhir dengan pemotongan komisi layanan platform secara otomatis.</span>
                    </li>
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection