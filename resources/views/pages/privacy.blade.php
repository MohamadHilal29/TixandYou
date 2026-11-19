@extends('layouts.app')

@section('title', 'Privacy Policy - Tix and You')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl p-8 sm:p-10 shadow-xs border border-zinc-100 space-y-8">
        <div>
            <span class="inline-block px-3 py-1 text-xs font-semibold text-[#0A0A0A] bg-zinc-100 rounded-full mb-3">
                Kebijakan Privasi
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#0A0A0A] mb-3">Privacy Policy</h1>
            <p class="text-sm text-[#52525B] leading-relaxed">
                <strong class="text-[#0A0A0A]">Tix and You</strong> menghargai privasi setiap pengguna dan hanya mengumpulkan data yang dibutuhkan untuk kelancaran transaksi serta operasional acara:
            </p>
        </div>

        <!-- Kategori Data yang Dikumpulkan (2 Kartu) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="p-5 rounded-xl bg-[#FAFAFA] border border-zinc-200/70">
                <p class="text-xs font-bold uppercase tracking-wider text-[#0A0A0A] mb-2">Data Pembeli</p>
                <p class="text-sm text-[#52525B] leading-relaxed">
                    Nama lengkap, alamat email aktif, kata sandi terenkripsi, serta riwayat transaksi pemesanan tiket.
                </p>
            </div>

            <div class="p-5 rounded-xl bg-[#FAFAFA] border border-zinc-200/70">
                <p class="text-xs font-bold uppercase tracking-wider text-[#0A0A0A] mb-2">Data Penyelenggara (EO)</p>
                <p class="text-sm text-[#52525B] leading-relaxed">
                    Identitas penanggung jawab, nama organisasi atau komunitas, serta informasi rekening bank tujuan pencairan dana.
                </p>
            </div>
        </div>

        <!-- Penggunaan Informasi -->
        <section class="text-sm text-[#52525B] leading-relaxed">
            <h2 class="text-base font-bold text-[#0A0A0A] mb-3">Informasi Pengguna Digunakan Secara Terbatas Untuk:</h2>
            <ul class="space-y-2.5">
                <li class="flex items-start gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                    <span>Memproses pesanan dan mengirimkan dokumen PDF e-ticket ke email pembeli.</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                    <span>Memvalidasi keabsahan tiket pengunjung saat proses check-in di pintu masuk acara.</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                    <span>Memverifikasi kelayakan acara serta menyalurkan pencairan dana (payout) kepada pihak EO.</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                    <span>Mencatat rekam jejak aktivitas sistem guna menjaga keamanan dan transparansi audit transaksi.</span>
                </li>
            </ul>
        </section>

        <hr class="border-zinc-100">

        <!-- Keamanan dan Penyimpanan Data -->
        <section class="text-sm text-[#52525B] leading-relaxed">
            <h2 class="text-base font-bold text-[#0A0A0A] mb-3">Keamanan dan Penyimpanan Data</h2>
            <ul class="space-y-2.5">
                <li class="flex items-start gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                    <span>Seluruh pertukaran data di dalam platform dilindungi enkripsi HTTPS/TLS, sementara kata sandi pengguna diamankan dengan algoritma hashing terenkripsi.</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                    <span>Platform tidak menyimpan informasi sensitif kartu pembayaran maupun kredensial perbankan pembeli karena seluruh proses pembayaran ditangani langsung oleh mitra payment gateway resmi.</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                    <span>Seluruh catatan transaksi finansial dijaga integritasnya menggunakan mekanisme yang ketat agar riwayat audit tetap terlindungi dan tidak terhapus secara permanen.</span>
                </li>
            </ul>
        </section>
    </div>
</div>
@endsection