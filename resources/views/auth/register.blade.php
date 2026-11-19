@extends('layouts.app')

@section('title', 'Daftar Akun - Tix and You')

@section('content')
<div class="max-w-md mx-auto my-6 bg-white rounded-3xl p-8 sm:p-10 border border-zinc-200/80 shadow-xs">
    <div class="text-center space-y-2 mb-7">
        <div class="inline-flex justify-center mb-1">
            <x-logo size="md" />
        </div>
        <h1 class="text-2xl font-extrabold text-[#0A0A0A]">Buat Akun Baru</h1>
        <p class="text-xs text-[#52525B]">Daftar untuk membeli tiket, menyimpan E-Ticket QR, dan melihat riwayat transaksi</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 font-medium space-y-1">
            @foreach ($errors->all() as $error)
                <p>&bull; {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Nama Lengkap *</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   placeholder="Contoh: Andi Saputra"
                   class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A] transition">
        </div>

        <div>
            <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Alamat Email *</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   placeholder="nama@email.com"
                   class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A] transition">
        </div>

        <div>
            <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Nomor WhatsApp Aktif *</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required
                   placeholder="081234567890"
                   class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A] transition">
        </div>

        <div>
            <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Kata Sandi *</label>
            <input type="password" name="password" required
                   placeholder="Minimal 6 karakter"
                   class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A] transition">
        </div>

        <div>
            <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Konfirmasi Kata Sandi *</label>
            <input type="password" name="password_confirmation" required
                   placeholder="Ulangi kata sandi"
                   class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A] transition">
        </div>

        <button type="submit"
                class="w-full py-3 px-4 text-sm font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
            Daftar Sekarang
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-zinc-200/80 text-center space-y-2 text-xs text-[#52525B]">
        <p>Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-[#0A0A0A] hover:underline">Log in</a></p>
        <p>Ingin menyelenggarakan acara? <a href="{{ route('join.eo') }}" class="font-bold text-[#0A0A0A] hover:underline">Daftar sebagai EO</a></p>
    </div>
</div>
@endsection