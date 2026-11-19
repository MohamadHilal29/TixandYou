<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EoController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

// Halaman Publik Buyer (Katalog & Detail Event)
Route::get('/', [BuyerController::class, 'home'])->name('home');
Route::get('/event/{event:slug}', [BuyerController::class, 'detail'])->name('event.detail');
Route::get('/event/{event:slug}/tickets', [BuyerController::class, 'tickets'])->name('event.tickets');
Route::post('/event/{event:slug}/tickets', [CheckoutController::class, 'selectTickets'])->name('checkout.select');

// Alur Checkout, Pembayaran & E-Ticket Buyer
Route::get('/checkout/participant', [CheckoutController::class, 'participantForm'])->name('checkout.participant');
Route::post('/checkout/participant', [CheckoutController::class, 'storeParticipant'])->name('checkout.participant.store');
Route::get('/checkout/review', [CheckoutController::class, 'review'])->name('checkout.review');
Route::post('/checkout/process', [CheckoutController::class, 'processOrder'])->name('checkout.process');
Route::get('/checkout/payment/{order}', [CheckoutController::class, 'paymentPage'])->name('checkout.payment');
Route::post('/checkout/payment/{order}', [CheckoutController::class, 'confirmPayment'])->name('checkout.payment.confirm');
Route::get('/checkout/confirm/{order}', [CheckoutController::class, 'orderConfirm'])->name('checkout.confirm');
Route::get('/checkout/confirm/{order}/pdf', [CheckoutController::class, 'downloadPdf'])->name('checkout.pdf');

// Autentikasi & Daftar EO (Guest & Buyer)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/join-eo', [AuthController::class, 'showJoinEo'])->name('join.eo');
Route::post('/join-eo', [AuthController::class, 'registerEo'])->name('join.eo.post');

// Dashboard, Pengajuan Event & Payout EO
Route::middleware(['auth', 'role:eo'])->group(function () {
    Route::get('/eo/dashboard', [EoController::class, 'dashboard'])->name('eo.dashboard');
    Route::post('/eo/events', [EoController::class, 'storeEvent'])->name('eo.events.store');

    // EO Payouts
    Route::get('/eo/payouts', [PayoutController::class, 'eoIndex'])->name('eo.payouts.index');
    Route::post('/eo/payouts', [PayoutController::class, 'store'])->name('eo.payouts.store');
});

// Web Scanner Check-In (Dikunci khusus Admin & Super Admin)
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/admin/scanner', [ScannerController::class, 'index'])->name('admin.scanner');
    Route::post('/admin/scanner/verify', [ScannerController::class, 'verify'])->name('admin.scanner.verify');

    // Admin & Super Admin Payout Verification
    Route::get('/admin/payouts', [PayoutController::class, 'adminIndex'])->name('admin.payouts.index');
    Route::patch('/admin/payouts/{id}', [PayoutController::class, 'updateStatus'])->name('admin.payouts.update');
});

// Dashboard Admin (Moderasi Pendaftaran EO & Approval Event)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/eo-applications/{user}/approve', [AdminController::class, 'approveEo'])->name('admin.eo.approve');
    Route::post('/admin/eo-applications/{user}/reject', [AdminController::class, 'rejectEo'])->name('admin.eo.reject');
    Route::post('/admin/events/{event}/approve', [AdminController::class, 'approveEvent'])->name('admin.events.approve');
    Route::post('/admin/events/{event}/reject', [AdminController::class, 'rejectEvent'])->name('admin.events.reject');
});

// Dashboard & Global Monitoring Super Admin
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminController::class, 'monitoring'])->name('superadmin.dashboard');
    Route::get('/superadmin/monitoring', [SuperAdminController::class, 'monitoring'])->name('superadmin.monitoring');
    Route::patch('/superadmin/users/{user}/role', [SuperAdminController::class, 'updateUserRole'])->name('superadmin.users.role');
});

// Halaman Footer
Route::view('/about', 'pages.about')->name('about');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/faqs', 'pages.faqs')->name('faqs');

// User Registration & Profile Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});