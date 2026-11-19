@extends('layouts.app')

@section('title', 'Daftar Event Organizer - Tix and You')

@section('content')
@php
    $loggedInEmail = auth()->check() ? auth()->user()->email : '';
    $initialEmail = old('email', $loggedInEmail);
    $isSeparateEmailOnLoad = !auth()->check() || (strtolower(trim($initialEmail)) !== strtolower(trim($loggedInEmail)));
@endphp

<div class="max-w-2xl mx-auto space-y-6">
    <div class="space-y-2">
        <span class="inline-block px-3 py-1 text-[11px] font-bold bg-zinc-200/80 text-[#0A0A0A] rounded-full">
            Mitra Penyelenggara Acara
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A]">Daftar Event Organizer</h1>
        <p class="text-xs sm:text-sm text-[#52525B]">Lengkapi profil penanggung jawab dan rekening pencairan Anda untuk diverifikasi oleh tim Admin.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs text-red-700 space-y-1">
            @foreach ($errors->all() as $error)
                <p>&bull; {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('join.eo') }}" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-zinc-200/80 shadow-xs space-y-6">
        @csrf

        <div class="space-y-4">
            <h2 class="text-sm font-bold text-[#0A0A0A] pb-2 border-b border-zinc-100">Identitas Penanggung Jawab & Rekening Payout</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Nama Penanggung Jawab *</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required
                           placeholder="Nama lengkap PIC"
                           class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Nama Organisasi / Komunitas / EO *</label>
                    <input type="text" name="organization_name" value="{{ old('organization_name', auth()->user()->organization_name ?? '') }}" required
                           placeholder="Contoh: Sinergi Event Organizer"
                           class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Email Akun EO *</label>
                    <input type="email" id="eo_email_input" name="email" value="{{ $initialEmail }}" required
                           placeholder="eo@organisasi.com"
                           class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                    @auth
                        <p id="eo_email_mode_hint" class="text-[11px] text-[#52525B] mt-1.5 leading-relaxed">
                            @if ($isSeparateEmailOnLoad)
                                <span class="font-semibold text-[#0A0A0A]">Mode Akun Terpisah:</span> Sistem akan membuat akun EO baru dengan email ini (akun pribadi Anda tetap aktif).
                            @else
                                <span class="font-semibold text-[#0A0A0A]">Mode Upgrade Akun:</span> Akun aktif Anda akan di-upgrade menjadi EO setelah di-ACC Admin. Ubah email jika ingin membuat akun organisasi terpisah.
                            @endif
                        </p>
                    @else
                        <p class="text-[11px] text-[#52525B] mt-1">Akun akan otomatis aktif sebagai EO setelah di-ACC Admin.</p>
                    @endauth
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Nomor WhatsApp Aktif *</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required
                           placeholder="081234567890"
                           class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                </div>

                <div id="eo_password_wrapper" class="sm:col-span-2 {{ $isSeparateEmailOnLoad ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">
                        {{ auth()->check() ? 'Kata Sandi untuk Akun EO Baru *' : 'Kata Sandi Akun *' }}
                    </label>
                    <input type="password" id="eo_password_input" name="password"
                           {{ $isSeparateEmailOnLoad ? 'required' : '' }}
                           placeholder="Minimal 6 karakter (digunakan untuk Log in ke dashboard EO)"
                           class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                </div>
            </div>
        </div>

        <div class="pt-2 border-t border-zinc-100 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Nama Bank *</label>
                <select name="bank_name" required class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
                    @foreach (['BCA', 'Mandiri', 'BNI', 'BRI', 'BSI', 'CIMB Niaga', 'Permata'] as $bank)
                        <option value="{{ $bank }}" @selected(old('bank_name', auth()->user()->bank_name ?? '') === $bank)>{{ $bank }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Nomor Rekening *</label>
                <input type="text" name="bank_account_number" value="{{ old('bank_account_number', auth()->user()->bank_account_number ?? '') }}" required
                       placeholder="1234567890"
                       class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Nama Pemilik Rekening *</label>
                <input type="text" name="bank_account_name" value="{{ old('bank_account_name', auth()->user()->bank_account_name ?? '') }}" required
                       placeholder="Sesuai buku tabungan"
                       class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A]">
            </div>
        </div>

        <div class="pt-2 flex items-center justify-end gap-3">
            <a href="{{ route('home') }}" class="px-4 py-2.5 text-xs font-semibold text-[#52525B] hover:text-[#0A0A0A]">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                Daftar EO
            </button>
        </div>
    </form>
</div>

@auth
@push('scripts')
<script>
    (function () {
        const currentEmail = @json(strtolower(trim($loggedInEmail)));
        const emailInput = document.getElementById('eo_email_input');
        const passWrapper = document.getElementById('eo_password_wrapper');
        const passInput = document.getElementById('eo_password_input');
        const modeHint = document.getElementById('eo_email_mode_hint');

        if (!emailInput || !passWrapper || !passInput) return;

        function toggleEoAccountMode() {
            const typedEmail = emailInput.value.trim().toLowerCase();
            const isSeparate = typedEmail !== '' && typedEmail !== currentEmail;

            if (isSeparate) {
                passWrapper.classList.remove('hidden');
                passInput.required = true;
                if (modeHint) {
                    modeHint.innerHTML = '<span class="font-semibold text-[#0A0A0A]">Mode Akun Terpisah:</span> Sistem akan membuat akun EO baru dengan email ini (akun pribadi Anda tetap aktif).';
                }
            } else {
                passWrapper.classList.add('hidden');
                passInput.required = false;
                passInput.value = '';
                if (modeHint) {
                    modeHint.innerHTML = '<span class="font-semibold text-[#0A0A0A]">Mode Upgrade Akun:</span> Akun aktif Anda akan di-upgrade menjadi EO setelah di-ACC Admin. Ubah email jika ingin membuat akun organisasi terpisah.';
                }
            }
        }

        emailInput.addEventListener('input', toggleEoAccountMode);
    })();
</script>
@endpush
@endauth
@endsection