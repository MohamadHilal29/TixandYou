<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'buyer') {
                $user->update(['role' => 'user']);
            }

            $this->logAudit($user->id, 'USER_LOGIN', "Pengguna {$user->email} ({$user->role}) berhasil masuk ke sistem.", $request->ip());

            return match ($user->role) {
                'superadmin' => redirect()->intended(route('superadmin.dashboard')),
                'admin' => redirect()->intended(route('admin.dashboard')),
                'eo' => redirect()->intended(route('eo.dashboard')),
                default => redirect()->intended(route('home')),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan langsung masuk melalui halaman Log in.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        // Auto-claim riwayat transaksi guest yang memakai email yang sama
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'user_id')) {
            $emailCol = collect(['buyer_email', 'customer_email', 'email'])->first(fn ($c) => Schema::hasColumn('orders', $c));
            if ($emailCol) {
                DB::table('orders')
                    ->whereNull('user_id')
                    ->where($emailCol, $user->email)
                    ->update(['user_id' => $user->id]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        $this->logAudit($user->id, 'USER_REGISTER', "Akun user baru {$user->email} berhasil dibuat.", $request->ip());

        return redirect()->route('home')->with('success', 'Selamat datang di Tix and You! Akun Anda berhasil dibuat.');
    }

    public function showJoinEo(): View
    {
        return view('auth.join-eo');
    }

    public function registerEo(Request $request): RedirectResponse
    {
        return $this->processJoinEo($request);
    }

    public function submitJoinEo(Request $request): RedirectResponse
    {
        return $this->processJoinEo($request);
    }

    public function joinEo(Request $request): RedirectResponse
    {
        return $this->processJoinEo($request);
    }

    protected function processJoinEo(Request $request): RedirectResponse
    {
        $isLoggedIn = Auth::check();
        $currentUser = Auth::user();
        $inputEmail = strtolower(trim((string) $request->input('email')));
        $isUpgradingSelf = $isLoggedIn && strtolower(trim($currentUser->email)) === $inputEmail;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'organization_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:100'],
            'bank_account_name' => ['required', 'string', 'max:255'],
        ];

        // Password wajib diisi jika Guest atau jika User yang login mendaftarkan email EO baru yang terpisah
        if (!$isUpgradingSelf) {
            $rules['password'] = ['required', 'string', 'min:6'];
        }

        $validated = $request->validate($rules, [
            'password.required' => 'Kata sandi wajib diisi untuk membuat/menautkan akun EO dengan email terpisah.',
            'password.min' => 'Kata sandi akun EO minimal 6 karakter.',
        ]);

        // KASUS 1: User login & memakai email yang sama -> Upgrade akun aktif
        if ($isUpgradingSelf) {
            if ($currentUser->role === 'eo') {
                return redirect()->route('eo.dashboard');
            }

            $currentUser->update([
                'name' => $validated['name'],
                'organization_name' => $validated['organization_name'],
                'phone' => $validated['phone'],
                'bank_name' => $validated['bank_name'],
                'bank_account_number' => $validated['bank_account_number'],
                'bank_account_name' => $validated['bank_account_name'],
                'eo_status' => 'pending',
                'eo_rejection_reason' => null,
            ]);

            $this->logAudit($currentUser->id, 'EO_APPLICATION', "User {$currentUser->email} mengajukan upgrade akun menjadi Event Organizer ({$validated['organization_name']}).", $request->ip());

            return redirect()->route('profile')->with('success', 'Pengajuan upgrade Event Organizer berhasil dikirim! Tim Admin akan memverifikasi data Anda sebelum akun EO diaktifkan.');
        }

        // KASUS 2: Guest ATAU User login yang memasukkan email terpisah untuk organisasi
        $existingUser = User::whereRaw('LOWER(email) = ?', [$inputEmail])->first();

        if ($existingUser) {
            if (!Hash::check($validated['password'], $existingUser->password)) {
                return back()->withErrors([
                    'email' => 'Email organisasi ini sudah terdaftar di sistem. Masukkan kata sandi akun tersebut dengan benar atau gunakan alamat email baru.',
                ])->withInput();
            }

            if ($existingUser->role === 'eo') {
                $targetRoute = $isLoggedIn ? 'profile' : 'login';
                return redirect()->route($targetRoute)->with('success', 'Akun dengan email tersebut sudah berstatus Event Organizer.');
            }

            $existingUser->update([
                'name' => $validated['name'],
                'organization_name' => $validated['organization_name'],
                'phone' => $validated['phone'],
                'bank_name' => $validated['bank_name'],
                'bank_account_number' => $validated['bank_account_number'],
                'bank_account_name' => $validated['bank_account_name'],
                'eo_status' => 'pending',
                'eo_rejection_reason' => null,
            ]);

            $this->logAudit($existingUser->id, 'EO_APPLICATION', "Pengajuan EO dari akun {$existingUser->email} ({$validated['organization_name']}) masuk antrean verifikasi.", $request->ip());
        } else {
            $newUser = User::create([
                'name' => $validated['name'],
                'email' => trim($validated['email']),
                'password' => Hash::make($validated['password']),
                'role' => 'user',
                'phone' => $validated['phone'],
                'organization_name' => $validated['organization_name'],
                'bank_name' => $validated['bank_name'],
                'bank_account_number' => $validated['bank_account_number'],
                'bank_account_name' => $validated['bank_account_name'],
                'eo_status' => 'pending',
            ]);

            $this->logAudit($newUser->id, 'EO_APPLICATION', "Pendaftaran akun EO terpisah {$newUser->email} ({$validated['organization_name']}) masuk antrean verifikasi.", $request->ip());
        }

        if ($isLoggedIn) {
            return redirect()->route('profile')->with('success', "Pengajuan akun EO terpisah ({$validated['email']}) berhasil dikirim ke antrean verifikasi Admin! Akun pribadi Anda tetap aktif dan bisa digunakan seperti biasa.");
        }

        return redirect()->route('login')->with('success', 'Pendaftaran Event Organizer berhasil dikirim! Data Anda telah masuk ke antrean verifikasi Admin. Silakan Log in untuk memantau status persetujuan.');
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->logAudit($user->id, 'USER_LOGOUT', "Pengguna {$user->email} keluar dari sistem.", $request->ip());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function logAudit(?int $userId, string $action, string $description, ?string $ip): void
    {
        if (class_exists(AuditLog::class) && Schema::hasTable('audit_logs')) {
            try {
                AuditLog::create([
                    'user_id' => $userId,
                    'action' => $action,
                    'description' => $description,
                    'ip_address' => $ip,
                ]);
            } catch (\Throwable $e) {}
        }
    }
}