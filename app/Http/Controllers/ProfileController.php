<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $orders = collect();
        if (class_exists(Order::class) && Schema::hasTable('orders')) {
            $orderModel = new Order();
            $relations = [];
            foreach (['event', 'items', 'items.ticketCategory', 'tickets', 'tickets.ticketCategory'] as $rel) {
                $baseRel = explode('.', $rel)[0];
                if (method_exists($orderModel, $baseRel)) {
                    $relations[] = $rel;
                }
            }

            $query = Order::with(array_unique($relations));
            $hasUserId = Schema::hasColumn('orders', 'user_id');
            $emailCol = collect(['buyer_email', 'customer_email', 'email'])->first(fn ($c) => Schema::hasColumn('orders', $c));

            $query->where(function ($q) use ($user, $hasUserId, $emailCol) {
                if ($hasUserId) {
                    $q->where('user_id', $user->id);
                }
                if ($emailCol) {
                    $hasUserId ? $q->orWhere($emailCol, $user->email) : $q->where($emailCol, $user->email);
                }
            });

            $orders = $query->orderByDesc('created_at')->get();
        }

        $paidOrders = $orders->filter(fn ($o) => in_array(strtolower((string) ($o->payment_status ?? $o->status ?? '')), ['paid', 'settlement', 'success', 'completed']));
        $pendingOrders = $orders->filter(fn ($o) => in_array(strtolower((string) ($o->payment_status ?? $o->status ?? '')), ['pending', 'unpaid', 'waiting']));

        $eticketRoute = 'checkout.confirm';
        $pdfRoute = 'checkout.pdf';
        $payRoute = 'checkout.payment';

        return view('user.profile', compact(
            'user',
            'orders',
            'paidOrders',
            'pendingOrders',
            'eticketRoute',
            'pdfRoute',
            'payRoute'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:25'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Informasi profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini tidak sesuai.',
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi akun berhasil diperbarui.');
    }
}