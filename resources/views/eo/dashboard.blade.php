@extends('layouts.app')

@section('title', 'Dashboard EO - Tix and You')

@section('content')
<div class="space-y-6">
    <!-- Header EO (Tanpa Web Scanner) -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-zinc-100 text-[#0A0A0A] rounded-full mb-1.5">
                Event Organizer Panel
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#0A0A0A]">{{ $eo->organization_name ?: $eo->name }}</h1>
            <p class="text-xs text-[#52525B] mt-0.5">
                PIC: {{ $eo->name }} &bull; Rekening Payout: <strong class="text-[#0A0A0A]">{{ $eo->bank_name ?? '-' }} {{ $eo->bank_account_number ?? '-' }}</strong> (a.n. {{ $eo->bank_account_name ?? $eo->name }})
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 self-start sm:self-auto">
            <a href="{{ route('eo.payouts.index') }}"
               class="px-4 py-2 text-xs font-semibold text-[#0A0A0A] bg-[#FAFAFA] border border-zinc-300 hover:bg-zinc-100 rounded-xl transition">
                Pencairan Dana (Payout)
            </a>
            <button type="button" onclick="document.getElementById('modalAjukanEvent').classList.remove('hidden')"
                    class="px-4 py-2 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                + Ajukan Event Baru
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-300 text-red-900 text-xs space-y-1">
            @foreach ($errors->all() as $err)
                <p>&bull; {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <!-- Statistik EO -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Total Event Diajukan</p>
            <p class="text-2xl font-bold text-[#0A0A0A] mt-1">{{ $events->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Total Tiket Terjual</p>
            <p class="text-2xl font-bold text-[#0A0A0A] mt-1">{{ number_format($totalTicketsSold, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Estimasi Pendapatan Kotor</p>
            <p class="text-2xl font-bold text-[#0A0A0A] mt-1">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Daftar Event EO -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs space-y-4">
        <h2 class="text-base font-bold text-[#0A0A0A]">Daftar Pengajuan & Acara Anda</h2>
        @if ($events->isEmpty())
            <p class="text-xs text-[#52525B] py-8 text-center bg-[#FAFAFA] rounded-xl">
                Belum ada acara yang diajukan. Klik <strong>+ Ajukan Event Baru</strong> untuk membuat acara pertama Anda.
            </p>
        @else
            <div class="space-y-3">
                @foreach ($events as $event)
                    <div class="p-4 rounded-xl border border-zinc-200/80 bg-[#FAFAFA] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm font-bold text-[#0A0A0A]">{{ $event->title }}</h3>
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full capitalize
                                    {{ $event->status === 'published' ? 'bg-emerald-100 text-emerald-800' : ($event->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $event->status === 'published' ? 'Published (Tayang)' : ($event->status === 'rejected' ? 'Rejected' : 'Menunggu Review Admin') }}
                                </span>
                            </div>
                            <p class="text-xs text-[#52525B]">
                                {{ $event->category ?? 'Event' }} &bull; {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }} ({{ $event->start_time ?? '18:00' }} - {{ $event->end_time ?? '23:00' }}) &bull; {{ $event->venue_name }}
                            </p>
                            <div class="flex flex-wrap gap-2 pt-1">
                                @foreach ($event->ticketCategories as $cat)
                                    <span class="px-2.5 py-1 text-[11px] bg-white border border-zinc-200 rounded-lg text-[#0A0A0A]">
                                        {{ $cat->name }}: <strong>Rp {{ number_format((float) $cat->price, 0, ',', '.') }}</strong> (Kuota {{ $cat->quota }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Modal Form Pengajuan Event Baru + Upload Poster + Kategori Tiket -->
<div id="modalAjukanEvent" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-xl space-y-6">
        <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#0A0A0A]">Ajukan Event Baru</h2>
                <p class="text-xs text-[#52525B]">Lengkapi detail acara, poster, dan kategori tiket untuk diverifikasi Admin.</p>
            </div>
            <button type="button" onclick="document.getElementById('modalAjukanEvent').classList.add('hidden')"
                    class="px-3 py-1.5 text-xs font-bold text-[#52525B] hover:text-[#0A0A0A] rounded-lg cursor-pointer">
                &#10005; Tutup
            </button>
        </div>

        <form action="{{ route('eo.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf

            <!-- Bagian Detail Acara -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-[#0A0A0A]">1. Detail Pengajuan Acara (Event)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#0A0A0A] mb-1">Judul Acara *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Senja Akustik Vol. 2"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
                    </div>
                    <div>
                        <label class="block font-semibold text-[#0A0A0A] mb-1">Kategori Acara *</label>
                        <select name="category" required class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 bg-white focus:outline-none focus:border-[#0A0A0A]">
                            @foreach (['Musik & Konser', 'Festival', 'Workshop', 'Seminar & Talkshow', 'Pameran & Seni', 'Olahraga'] as $catOption)
                                <option value="{{ $catOption }}" {{ old('category') === $catOption ? 'selected' : '' }}>{{ $catOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block font-semibold text-[#0A0A0A] mb-1">Tanggal Acara *</label>
                        <input type="date" name="event_date" value="{{ old('event_date') }}" required
                               class="w-full px-3 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
                    </div>
                    <div>
                        <label class="block font-semibold text-[#0A0A0A] mb-1">Jam Mulai *</label>
                        <input type="time" name="start_time" value="{{ old('start_time', '18:30') }}" required
                               class="w-full px-3 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
                    </div>
                    <div>
                        <label class="block font-semibold text-[#0A0A0A] mb-1">Jam Selesai *</label>
                        <input type="time" name="end_time" value="{{ old('end_time', '23:00') }}" required
                               class="w-full px-3 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
                    </div>
                    <div>
                        <label class="block font-semibold text-[#0A0A0A] mb-1">Open Gate</label>
                        <input type="time" name="open_gate" value="{{ old('open_gate', '17:00') }}"
                               class="w-full px-3 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-[#0A0A0A] mb-1">Nama Lokasi / Venue *</label>
                        <input type="text" name="venue_name" value="{{ old('venue_name') }}" required placeholder="Contoh: Gedung Kesenian Jakarta"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
                    </div>
                    <div>
                        <label class="block font-semibold text-[#0A0A0A] mb-1">Alamat Lengkap Venue *</label>
                        <input type="text" name="venue_address" value="{{ old('venue_address') }}" required placeholder="Jl. Gedung Kesenian No. 1, Jakarta"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-[#0A0A0A] mb-1">Deskripsi Acara *</label>
                    <textarea name="description" rows="3" required placeholder="Jelaskan konsep acara, pengisi acara, dan informasi penting untuk pengunjung..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-300 focus:outline-none focus:border-[#0A0A0A]">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-[#0A0A0A] mb-1">Poster / Banner Acara (JPG/PNG/WEBP, Maks. 2MB)</label>
                    <input type="file" name="poster" accept="image/*"
                           class="w-full px-3.5 py-2 rounded-xl border border-zinc-300 bg-[#FAFAFA]">
                </div>
            </div>

            <!-- Bagian Kategori Tiket -->
            <div class="space-y-3 pt-4 border-t border-zinc-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-[#0A0A0A]">2. Kategori Tiket (General Admission)</h3>
                        <p class="text-[11px] text-[#52525B]">Maksimal pembelian oleh 1 pembeli adalah 5 tiket per transaksi.</p>
                    </div>
                    <button type="button" onclick="tambahKategoriTiket()"
                            class="px-3 py-1.5 text-[11px] font-semibold text-[#0A0A0A] bg-[#FAFAFA] border border-zinc-300 hover:bg-zinc-100 rounded-lg cursor-pointer">
                        + Tambah Kategori
                    </button>
                </div>

                <div id="ticketCategoriesContainer" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 rounded-xl bg-[#FAFAFA] border border-zinc-200">
                        <div>
                            <label class="block font-semibold text-[#0A0A0A] mb-1">Nama Kategori *</label>
                            <input type="text" name="categories[0][name]" value="Early Bird" required
                                   class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-white">
                        </div>
                        <div>
                            <label class="block font-semibold text-[#0A0A0A] mb-1">Harga Tiket (Rp) *</label>
                            <input type="number" name="categories[0][price]" value="100000" min="0" required
                                   class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-white">
                        </div>
                        <div>
                            <label class="block font-semibold text-[#0A0A0A] mb-1">Kuota Tiket *</label>
                            <input type="number" name="categories[0][quota]" value="100" min="1" required
                                   class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                <button type="button" onclick="document.getElementById('modalAjukanEvent').classList.add('hidden')"
                        class="px-4 py-2.5 text-xs font-semibold text-[#52525B] hover:text-[#0A0A0A] cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                    Ajukan Event
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let catIndex = 1;
    function tambahKategoriTiket() {
        const container = document.getElementById('ticketCategoriesContainer');
        const row = document.createElement('div');
        row.className = 'grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 rounded-xl bg-[#FAFAFA] border border-zinc-200 relative';
        row.innerHTML = `
            <div>
                <label class="block font-semibold text-[#0A0A0A] mb-1">Nama Kategori *</label>
                <input type="text" name="categories[${catIndex}][name]" placeholder="Reguler / VIP" required class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-white">
            </div>
            <div>
                <label class="block font-semibold text-[#0A0A0A] mb-1">Harga Tiket (Rp) *</label>
                <input type="number" name="categories[${catIndex}][price]" value="150000" min="0" required class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-white">
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block font-semibold text-[#0A0A0A] mb-1">Kuota Tiket *</label>
                    <input type="number" name="categories[${catIndex}][quota]" value="100" min="1" required class="w-full px-3 py-2 rounded-lg border border-zinc-300 bg-white">
                </div>
                <button type="button" onclick="this.closest('.grid').remove()" class="px-2.5 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg cursor-pointer">&#10005;</button>
            </div>
        `;
        container.appendChild(row);
        catIndex++;
    }
</script>
@endsection