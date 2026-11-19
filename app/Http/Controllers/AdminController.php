<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $pendingEos = collect();
        if (Schema::hasColumn('users', 'eo_status')) {
            $pendingEos = User::where('eo_status', 'pending')
                ->whereIn('role', ['user', 'user'])
                ->latest()
                ->get();
        }

        $pendingEvents = Event::with(['eo', 'ticketCategories'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $allEvents = Event::with(['eo', 'ticketCategories'])
            ->latest()
            ->get();

        $paidOrdersCount = DB::table('orders')
            ->when(Schema::hasColumn('orders', 'payment_status'), function ($q) {
                $q->whereIn('payment_status', ['paid', 'completed', 'confirmed', 'settlement']);
            })
            ->count();

        return view('admin.dashboard', compact('pendingEos', 'pendingEvents', 'allEvents', 'paidOrdersCount'));
    }

    public function approveEo(Request $request, User $user): RedirectResponse
    {
        $update = ['role' => 'eo'];
        if (Schema::hasColumn('users', 'eo_status')) {
            $update['eo_status'] = 'approved';
        }
        $user->update($update);

        $admin = Auth::user();
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'EO_APPLICATION_APPROVED',
            'description' => "Admin {$admin->name} menyetujui pendaftaran EO untuk {$user->name} ({$user->email}).",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Akun {$user->name} resmi diaktifkan menjadi Event Organizer (EO).");
    }

    public function rejectEo(Request $request, User $user): RedirectResponse
    {
        if (Schema::hasColumn('users', 'eo_status')) {
            $user->update(['eo_status' => 'rejected']);
        }

        $admin = Auth::user();
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'EO_APPLICATION_REJECTED',
            'description' => "Admin {$admin->name} menolak pendaftaran EO untuk {$user->name} ({$user->email}).",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Pengajuan pendaftaran EO dari {$user->name} telah ditolak.");
    }

    public function approveEvent(Request $request, Event $event): RedirectResponse
    {
        $event->update(['status' => 'published']);

        $admin = Auth::user();
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'EVENT_APPROVED',
            'description' => "Admin {$admin->name} menyetujui dan menayangkan event '{$event->title}'.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Event '{$event->title}' berhasil disetujui dan langsung tayang di homepage.");
    }

    public function rejectEvent(Request $request, Event $event): RedirectResponse
    {
        $event->update(['status' => 'rejected']);

        $admin = Auth::user();
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'EVENT_REJECTED',
            'description' => "Admin {$admin->name} menolak pengajuan event '{$event->title}'.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Event '{$event->title}' telah ditolak.");
    }
}