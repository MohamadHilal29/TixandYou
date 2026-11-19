@extends('layouts.app')

@section('title', 'FAQs - Tix and You')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl p-8 sm:p-10 shadow-xs border border-zinc-100">
        <span class="inline-block px-3 py-1 text-xs font-semibold text-[#0A0A0A] bg-zinc-100 rounded-full mb-3">
            Pusat Bantuan
        </span>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#0A0A0A] mb-2">Frequently Asked Questions (FAQs)</h1>
        <p class="text-sm text-[#52525B] mb-8">Klik pertanyaan di bawah ini untuk melihat jawaban seputar pembelian tiket dan penyelenggaraan acara.</p>

        @php
            $faqs = [
                [
                    'tag' => 'Pembeli',
                    'q' => 'Bagaimana cara membeli tiket di Tix and You?',
                    'a' => 'Pilih acara yang diinginkan pada katalog, tentukan kategori tiket (seperti Reguler atau Early Bird) beserta jumlahnya, lalu selesaikan pembayaran lunas melalui metode yang tersedia seperti QRIS, Virtual Account, atau Kartu Debit.'
                ],
                [
                    'tag' => 'Pembeli',
                    'q' => 'Di mana saya bisa melihat e-ticket setelah membayar?',
                    'a' => 'Setelah pembayaran terkonfirmasi lunas, sistem otomatis membuat e-ticket dalam format PDF berlampiran QR Code unik dan mengirimkannya ke alamat email utama yang kamu daftarkan.'
                ],
                [
                    'tag' => 'Pembeli',
                    'q' => 'Apakah saya bisa memilih nomor kursi saat memesan tiket?',
                    'a' => 'Saat ini Tix and You melayani penjualan tiket dengan sistem General Admission tanpa pemilihan nomor kursi (reserved seating).'
                ],
                [
                    'tag' => 'Pembeli',
                    'q' => 'Bagaimana cara masuk ke lokasi acara menggunakan e-ticket?',
                    'a' => 'Tunjukkan QR Code pada file PDF e-ticket kamu kepada petugas di pintu masuk untuk dipindai menggunakan Web Scanner. Pastikan menjaga kerahasiaan QR Code milikmu karena sistem akan menolak tiket yang sudah pernah dipindai sebelumnya.'
                ],
                [
                    'tag' => 'Pembeli',
                    'q' => 'Bagaimana jika acara yang sudah saya beli tiketnya dibatalkan?',
                    'a' => 'Apabila acara dibatalkan secara resmi oleh penyelenggara, tim Admin akan memicu pengembalian dana (refund) massal dan status tiket kamu diperbarui menjadi refunded.'
                ],
                [
                    'tag' => 'Event Organizer',
                    'q' => 'Bagaimana cara mendaftarkan acara di Tix and You?',
                    'a' => 'Daftarkan akun sebagai Event Organizer, lengkapi profil komunitas atau usaha beserta data rekening bank, lalu ajukan draf acara lengkap dengan kategori tiket dan jadwalnya untuk ditinjau oleh tim Admin.'
                ],
                [
                    'tag' => 'Event Organizer',
                    'q' => 'Kapan hasil penjualan tiket dicairkan ke rekening EO?',
                    'a' => 'Permohonan pencairan dana (payout) dapat diajukan setelah acara selesai, dan Admin akan mentransfer dana bersih (setelah dipotong komisi platform) ke rekening terdaftar maksimal H+7 setelah acara berakhir.'
                ],
                [
                    'tag' => 'Event Organizer',
                    'q' => 'Apakah penyelenggara perlu menyewa alat khusus untuk memindai tiket di lokasi?',
                    'a' => 'Tidak perlu, karena petugas maupun penyelenggara cukup membuka fitur Web Scanner langsung melalui peramban (browser) ponsel pintar untuk memindai QR Code pengunjung.'
                ],
            ];
        @endphp

        <div class="space-y-3">
            @foreach($faqs as $index => $item)
            <details {{ $index === 0 ? 'open' : '' }} class="group rounded-xl bg-[#FAFAFA] border border-zinc-200/70 open:bg-white open:border-[#0A0A0A]/20 open:shadow-xs transition">
                <summary class="flex items-center justify-between gap-4 p-5 cursor-pointer list-none select-none">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                        <span class="self-start px-2.5 py-0.5 text-[11px] font-semibold rounded-md {{ $item['tag'] === 'Pembeli' ? 'bg-zinc-200/70 text-[#0A0A0A]' : 'bg-zinc-200 text-[#0A0A0A]' }}">
                            {{ $item['tag'] }}
                        </span>
                        <span class="text-sm sm:text-[15px] font-bold text-[#0A0A0A] group-hover:text-[#0A0A0A] transition">
                            {{ $item['q'] }}
                        </span>
                    </div>
                    <svg class="w-4 h-4 text-[#52525B] shrink-0 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </summary>
                <div class="px-5 pb-5 pt-1 text-sm text-[#52525B] leading-relaxed border-t border-zinc-100">
                    {{ $item['a'] }}
                </div>
            </details>
            @endforeach
        </div>
    </div>
</div>
@endsection