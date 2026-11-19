@extends('layouts.app')

@section('title', 'Log in - Tix and You')

@section('content')
<div class="max-w-md mx-auto my-6 bg-white rounded-3xl p-8 sm:p-10 border border-zinc-200/80 shadow-xs">
    <div class="text-center space-y-2 mb-7">
        <div class="inline-flex justify-center mb-1">
            <x-logo size="md" />
        </div>
        <h1 class="text-2xl font-extrabold text-[#0A0A0A]">Selamat Datang Kembali</h1>
        <p class="text-xs text-[#52525B]">Masuk untuk mengelola tiket atau dashboard event Anda</p>
    </div>

    @if (session('success'))
        <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="nama@email.com"
                   class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A] transition">
        </div>

        <div>
            <label class="block text-xs font-bold text-[#0A0A0A] mb-1.5">Kata Sandi</label>
            <input type="password" name="password" required
                   placeholder="••••••••"
                   class="w-full px-4 py-2.5 text-sm bg-white border border-zinc-300 rounded-xl focus:outline-none focus:border-[#0A0A0A] transition">
        </div>

        <div class="flex items-center justify-between text-xs">
            <label class="inline-flex items-center gap-2 text-[#52525B] cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-zinc-300 text-[#0A0A0A] focus:ring-0">
                <span>Ingat saya</span>
            </label>
        </div>

        <button type="submit"
                class="w-full py-3 px-4 text-sm font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition cursor-pointer">
            Log in
        </button>
    </form>

    <!-- Tombol Daftar Akun & Daftar sebagai EO -->
    <div class="mt-6 pt-5 border-t border-zinc-200/80 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
        <a href="{{ route('register') }}"
           class="py-2.5 px-3 text-center text-xs font-semibold text-[#0A0A0A] bg-zinc-100 hover:bg-zinc-200/80 border border-zinc-200 rounded-xl transition">
            Daftar Akun
        </a>
        <a href="{{ route('join.eo') }}"
           class="py-2.5 px-3 text-center text-xs font-semibold text-[#0A0A0A] bg-white hover:bg-zinc-50 border border-zinc-300 rounded-xl transition">
            Daftar sebagai EO
        </a>
    </div>

    <!-- Akun Demo -->
    <div class="mt-6 p-4 rounded-2xl bg-[#FAFAFA] border border-zinc-200/80 text-xs text-[#52525B] space-y-1">
        <p class="font-bold text-[#0A0A0A]">Akun Demo (Password: password)</p>
        <p>&bull; EO: <span class="font-mono text-[#0A0A0A]">eo@tixandyou.id</span></p>
        <p>&bull; Admin: <span class="font-mono text-[#0A0A0A]">admin@tixandyou.id</span></p>
        <p>&bull; Super Admin: <span class="font-mono text-[#0A0A0A]">superadmin@tixandyou.id</span></p>
        <p>&bull; User: <span class="font-mono text-[#0A0A0A]">buyer@tixandyou.id</span></p>
    </div>
</div>
@endsection