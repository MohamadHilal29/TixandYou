@extends('layouts.app')

@section('title', 'Super Admin Monitoring - Tix and You')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold bg-[#0A0A0A] text-white rounded-full mb-1.5">
                Super Admin Command Center
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-[#0A0A0A]">Global Monitoring & Audit Logs</h1>
            <p class="text-xs text-[#52525B] mt-0.5">Pantau performa transaksi platform, kelola role pengguna, dan telusuri log aktivitas sistem.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.payouts.index') }}" class="px-4 py-2 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-xl transition">
                Verifikasi Payout ({{ $stats['pending_payouts'] }})
            </a>
            <a href="{{ route('admin.scanner') }}" class="px-4 py-2 text-xs font-semibold text-[#0A0A0A] bg-[#FAFAFA] border border-zinc-300 hover:bg-zinc-100 rounded-xl transition">
                Web Scanner
            </a>
        </div>
    </div>

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

    <!-- KPI Global Platform -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Total GMV Transaksi</p>
            <p class="text-lg font-bold text-[#0A0A0A] mt-1">Rp {{ number_format($stats['total_gmv'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">E-Ticket Terbit</p>
            <p class="text-lg font-bold text-[#0A0A0A] mt-1">{{ $stats['total_tickets'] }} Tiket</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Event Tayang / Total</p>
            <p class="text-lg font-bold text-emerald-700 mt-1">{{ $stats['published_events'] }} / {{ $stats['total_events'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-xs">
            <p class="text-xs text-[#52525B]">Total Pengguna Aktif</p>
            <p class="text-lg font-bold text-[#0A0A0A] mt-1">{{ $stats['total_users'] }} Akun</p>
        </div>
    </div>

    <!-- Manajemen Role Pengguna -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs">
        <h2 class="text-base font-bold text-[#0A0A0A] mb-4">Manajemen Akun & Hak Akses Role</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-zinc-200 text-[#52525B]">
                        <th class="py-2.5 pr-4 font-semibold">Nama Pengguna</th>
                        <th class="py-2.5 px-4 font-semibold">Email</th>
                        <th class="py-2.5 px-4 font-semibold">Role Saat Ini</th>
                        <th class="py-2.5 pl-4 font-semibold">Ubah Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach ($users as $u)
                        <tr>
                            <td class="py-3 pr-4 font-semibold text-[#0A0A0A]">{{ $u->name }}</td>
                            <td class="py-3 px-4 text-[#52525B]">{{ $u->email }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase rounded-full bg-zinc-100 text-[#0A0A0A]">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-3 pl-4">
                                @if ($u->id !== auth()->id())
                                    <form action="{{ route('superadmin.users.role', $u->id) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="px-2.5 py-1.5 text-xs bg-[#FAFAFA] border border-zinc-300 rounded-lg">
                                            @foreach (['user', 'eo', 'admin', 'superadmin'] as $roleOption)
                                                <option value="{{ $roleOption }}" {{ $u->role === $roleOption ? 'selected' : '' }}>
                                                    {{ strtoupper($roleOption) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-white bg-[#0A0A0A] hover:bg-zinc-800 rounded-lg cursor-pointer">
                                            Simpan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-[#52525B]">Akun Aktif Anda</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- System Audit Logs -->
    <div class="bg-white rounded-2xl p-6 border border-zinc-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-base font-bold text-[#0A0A0A]">Audit Trail Aktivitas Sistem</h2>
            <form method="GET" action="{{ route('superadmin.monitoring') }}" class="flex items-center gap-2">
                <input type="text" name="action" value="{{ $actionFilter }}" placeholder="Filter aksi (misal: TICKET, PAYOUT)"
                       class="px-3 py-1.5 text-xs bg-[#FAFAFA] border border-zinc-300 rounded-lg">
                <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-[#0A0A0A] rounded-lg cursor-pointer">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-zinc-200 text-[#52525B]">
                        <th class="py-2.5 pr-4 font-semibold">Waktu</th>
                        <th class="py-2.5 px-4 font-semibold">Aktor</th>
                        <th class="py-2.5 px-4 font-semibold">Aksi</th>
                        <th class="py-2.5 px-4 font-semibold">Deskripsi Aktivitas</th>
                        <th class="py-2.5 pl-4 font-semibold">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($auditLogs as $log)
                        <tr>
                            <td class="py-2.5 pr-4 text-[#52525B] whitespace-nowrap">{{ $log->created_at ?? '-' }}</td>
                            <td class="py-2.5 px-4 font-medium text-[#0A0A0A]">
                                {{ $log->actor_name ?? 'System' }}
                                <span class="text-[10px] uppercase text-[#52525B]">({{ $log->actor_role ?? '-' }})</span>
                            </td>
                            <td class="py-2.5 px-4 font-mono font-bold text-[#0A0A0A]">{{ $log->action }}</td>
                            <td class="py-2.5 px-4 text-[#52525B]">{{ $log->description }}</td>
                            <td class="py-2.5 pl-4 font-mono text-[11px] text-[#52525B]">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-[#52525B]">Belum ada catatan aktivitas di audit_logs.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection