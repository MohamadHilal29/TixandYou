<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function monitoring(Request $request): View
    {
        $orderCols = Schema::getColumnListing('orders');
        $ordersQuery = DB::table('orders');
        if (in_array('payment_status', $orderCols, true)) {
            $ordersQuery->whereIn('payment_status', ['paid', 'completed', 'confirmed', 'settlement']);
        }

        $stats = [
            'total_gmv' => (float) (clone $ordersQuery)->sum('total_amount'),
            'total_orders' => (int) (clone $ordersQuery)->count(),
            'total_tickets' => (int) DB::table('tickets')->count(),
            'total_events' => (int) DB::table('events')->count(),
            'published_events' => (int) DB::table('events')->where('status', 'published')->count(),
            'pending_payouts' => (int) DB::table('payouts')->where('status', 'pending')->count(),
            'total_users' => (int) User::count(),
        ];

        $users = User::orderBy('id', 'asc')->get();

        $actionFilter = trim((string) $request->query('action', ''));
        $logsQuery = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.name as actor_name', 'users.role as actor_role')
            ->orderByDesc('audit_logs.id');

        if ($actionFilter !== '') {
            $logsQuery->where('audit_logs.action', 'like', "%{$actionFilter}%");
        }

        $auditLogs = $logsQuery->limit(25)->get();

        return view('superadmin.monitoring', compact('stats', 'users', 'auditLogs', 'actionFilter'));
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'in:buyer,eo,admin,superadmin'],
        ]);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $oldRole = $user->role;
        $newRole = $request->input('role');
        $user->update(['role' => $newRole]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'USER_ROLE_UPDATED',
            'description' => "Super Admin mengubah role {$user->email} dari '{$oldRole}' menjadi '{$newRole}'.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Role pengguna {$user->name} berhasil diubah menjadi {$newRole}.");
    }
}