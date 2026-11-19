@extends('layouts.app')

@section('title', 'About Us - Tix and You')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Hero / Narasi Utama -->
    <div class="bg-white rounded-2xl p-8 sm:p-10 shadow-xs border border-zinc-100">
        <span class="inline-block px-3 py-1 text-xs font-semibold text-[#0A0A0A] bg-zinc-100 rounded-full mb-3">
            Tentang Kami
        </span>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#0A0A0A] mb-5">
            About <x-logo size="md" />
        </h1>

        <div class="space-y-4 text-sm sm:text-[15px] text-[#52525B] leading-relaxed">
            <p class="text-[#0A0A0A] font-medium text-base leading-relaxed">
                Tix and You adalah platform penjualan dan pengelolaan tiket acara (event) berbasis web dan seluler yang dirancang untuk menghadirkan pengalaman transaksi tiket yang cepat, transparan, dan mudah diakses.
            </p>
            <p>
                Kami memahami bahwa penyelenggara acara berskala kecil seperti komunitas lokal, organisasi mahasiswa, seminar independen, hingga gigs musik sering kali kesulitan menggunakan platform tiket raksasa karena biaya layanan yang tinggi, verifikasi yang berbelit, serta antarmuka yang rumit bagi pengguna awam.
            </p>
            <p>
                Akibatnya, banyak penyelenggara terpaksa mengandalkan pencatatan manual lewat spreadsheet atau pesan singkat yang rawan kesalahan rekapitulasi. <strong class="text-[#0A0A0A]">Tix and You</strong> hadir untuk menjawab tantangan tersebut dengan menyediakan solusi ticketing digital yang terjangkau, praktis, dan ramah digunakan oleh siapa saja.
            </p>
        </div>
    </div>

    <!-- Solusi yang Kami Tawarkan (2 Kartu Sejajar) -->
    <div>
        <h2 class="text-lg sm:text-xl font-bold text-[#0A0A0A] mb-4 px-1">Solusi yang Kami Tawarkan</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kartu 1: Pembeli -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xs border border-zinc-100 flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 text-[#0A0A0A] flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-[#0A0A0A] mb-3">Kemudahan bagi Pembeli</h3>
                    <ul class="space-y-3 text-sm text-[#52525B] leading-relaxed">
                        <li class="flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                            <span>Pencarian acara yang praktis melalui katalog terkurasi.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                            <span>Pembayaran lunas yang aman melalui payment gateway resmi (QRIS, Virtual Account, dan Kartu Debit).</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                            <span>Pengiriman e-ticket PDF berlampiran QR Code unik langsung ke kotak masuk email pembeli.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Kartu 2: Penyelenggara (EO) -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xs border border-zinc-100 flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 text-[#0A0A0A] flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-[#0A0A0A] mb-3">Transparansi bagi Penyelenggara (EO)</h3>
                    <ul class="space-y-3 text-sm text-[#52525B] leading-relaxed">
                        <li class="flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                            <span>Pengelolaan kategori dan kuota tiket secara real-time yang dilengkapi perlindungan anti-oversell.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                            <span>Laporan rekapitulasi penjualan otomatis di dashboard EO.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                            <span>Pemindaian tiket praktis lewat kamera ponsel menggunakan fitur Web Scanner.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0A0A0A] mt-2 shrink-0"></span>
                            <span>Kepastian pencairan dana (payout) maksimal H+7 setelah acara selesai.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection