@extends('layouts.dashboard')

@section('title', 'Super Admin Executive Panel - Tix and You')

@section('navbar-right')
    <span class="px-3.5 py-1.5 text-xs font-semibold text-white bg-zinc-500 rounded-lg">Super Admin</span>
@endsection

@section('content')
    <h1 class="text-2xl font-bold text-[#0A0A0A] mb-6">Executive Dashboard & System Control</h1>

    <!-- 4 Kartu Metrik Finansial & Sistem -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-zinc-100">
            <p class="text-xs font-medium text-[#52525B] mb-1.5">Total Gross Sales</p>
            <p class="text-2xl font-bold text-[#0A0A0A]">Rp 500.000.000</p>
            <p class="text-xs text-[#52525B] mt-1">Akumulasi seluruh transaksi</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-xs border-2 border-[#0A0A0A]">
            <p class="text-xs font-medium text-[#0A0A0A] mb-1.5">Profit Bersih Platform / Admin Fee</p>
            <p class="text-2xl font-bold text-[#0A0A0A]">Rp 20.000.000</p>
            <p class="text-xs text-[#52525B] mt-1">Total pendapatan admin fee</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-xs border border-zinc-100">
            <p class="text-xs font-medium text-[#52525B] mb-1.5">Total Event Terbit</p>
            <p class="text-2xl font-bold text-[#0A0A0A]">12 Event</p>
            <p class="text-xs text-[#52525B] mt-1">Event berstatus terbit</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-xs border border-zinc-100">
            <p class="text-xs font-medium text-[#52525B] mb-1.5">Total EO Terdaftar</p>
            <p class="text-2xl font-bold text-[#0A0A0A]">8 EO</p>
            <p class="text-xs text-[#52525B] mt-1">Event Organizer aktif</p>
        </div>
    </div>

    <!-- Tabel 1: Manajemen Akun & Role -->
    <div class="bg-white rounded-2xl p-6 shadow-xs border border-zinc-100 mb-8">
        <h2 class="text-lg font-bold text-[#0A0A0A] mb-4">Manajemen Akun & Role</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 text-[#52525B] text-xs font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4 rounded-l-lg">Nama User</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 rounded-r-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 text-sm">
                    @php
                        $users = [
                            ['name' => 'Budi Santoso', 'email' => 'budi.santoso@tixandyou.id', 'role' => 'Super Admin', 'status' => 'Aktif'],
                            ['name' => 'Sinergi Event Organizer', 'email' => 'sinergi@event.com', 'role' => 'Event Organizer', 'status' => 'Aktif'],
                            ['name' => 'Dimas Pratama', 'email' => 'dimas.eo@gmail.com', 'role' => 'Event Organizer', 'status' => 'Suspended'],
                            ['name' => 'Sari Lestari', 'email' => 'sari.lestari@tixandyou.id', 'role' => 'Admin', 'status' => 'Aktif'],
                        ];
                    @endphp

                    @foreach($users as $user)
                    <tr class="hover:bg-zinc-50/70 transition">
                        <td class="py-4 px-4 font-semibold text-[#0A0A0A]">{{ $user['name'] }}</td>
                        <td class="py-4 px-4 text-[#52525B]">{{ $user['email'] }}</td>
                        <td class="py-4 px-4 text-[#0A0A0A]">{{ $user['role'] }}</td>
                        <td class="py-4 px-4">
                            @if($user['status'] === 'Aktif')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-900 border border-emerald-200">Aktif</span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-50 text-red-900 border border-red-200">Suspended</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <button onclick="alert('Kelola Role / Status untuk {{ $user['name'] }}')" class="px-4 py-1.5 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-lg transition cursor-pointer">
                                Kelola Role
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel 2: Activity Log / Audit Trail Platform -->
    <div class="bg-white rounded-2xl p-6 shadow-xs border border-zinc-100">
        <h2 class="text-lg font-bold text-[#0A0A0A] mb-4">Activity Log / Audit Trail Platform</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 text-[#52525B] text-xs font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-4 rounded-l-lg">Timestamp</th>
                        <th class="py-3.5 px-4">Aktor</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Aktivitas</th>
                        <th class="py-3.5 px-4 rounded-r-lg">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 text-sm">
                    @php
                        $logs = [
                            ['time' => '05 Nov 2026, 14:32:08', 'actor' => 'Budi Santoso', 'role' => 'Super Admin', 'action' => 'Mengubah role user Sari Lestari menjadi Admin', 'ip' => '103.28.12.45'],
                            ['time' => '05 Nov 2026, 13:58:41', 'actor' => 'Sari Lestari', 'role' => 'Admin', 'action' => 'Menyetujui pengajuan EO Nusantara Live Entertainment', 'ip' => '114.122.8.101'],
                            ['time' => '05 Nov 2026, 11:20:15', 'actor' => 'Budi Santoso', 'role' => 'Super Admin', 'action' => 'Suspend akun user Dimas Pratama', 'ip' => '103.28.12.45'],
                            ['time' => '05 Nov 2026, 10:05:52', 'actor' => 'Sari Lestari', 'role' => 'Admin', 'action' => 'Memproses payout Rp 485.000.000 ke Nusantara Live', 'ip' => '114.122.8.101'],
                            ['time' => '04 Nov 2026, 16:47:30', 'actor' => 'Budi Santoso', 'role' => 'Super Admin', 'action' => 'Mengubah pengaturan admin fee platform', 'ip' => '103.28.12.45'],
                        ];
                    @endphp

                    @foreach($logs as $log)
                    <tr class="hover:bg-zinc-50/70 transition">
                        <td class="py-3.5 px-4 text-[#52525B] whitespace-nowrap">{{ $log['time'] }}</td>
                        <td class="py-3.5 px-4 font-semibold text-[#0A0A0A]">{{ $log['actor'] }}</td>
                        <td class="py-3.5 px-4 text-[#52525B]">{{ $log['role'] }}</td>
                        <td class="py-3.5 px-4 text-[#0A0A0A]">{{ $log['action'] }}</td>
                        <td class="py-3.5 px-4 text-[#52525B] font-mono text-xs">{{ $log['ip'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection