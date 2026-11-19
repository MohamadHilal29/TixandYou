<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PayoutController extends Controller
{
    private function getPayoutSchemaMeta(): array
    {
        $columns = collect(DB::select('SHOW COLUMNS FROM payouts'))->keyBy('Field');
        $names = $columns->keys()->all();

        $amountCol = collect(['amount', 'total_amount', 'requested_amount', 'payout_amount', 'net_amount', 'gross_amount', 'nominal'])
            ->first(fn ($col) => in_array($col, $names, true));

        if (! $amountCol) {
            $numericCol = $columns->first(function ($meta, $field) {
                if ($field === 'id' || str_ends_with($field, '_id') || str_contains($field, 'fee')) {
                    return false;
                }
                return preg_match('/decimal|int|double|float|numeric/i', $meta->Type) === 1;
            });
            $amountCol = $numericCol ? $numericCol->Field : 'net_amount';
        }

        $eoCol = in_array('eo_id', $names, true) ? 'eo_id' : 'user_id';
        $accNumCol = collect(['bank_account_number', 'account_number', 'rekening_number'])
            ->first(fn ($col) => in_array($col, $names, true));
        $accHolderCol = collect(['bank_account_holder', 'bank_account_name', 'account_holder', 'account_holder_name', 'account_name', 'holder_name'])
            ->first(fn ($col) => in_array($col, $names, true));
        $noteCol = collect(['notes', 'admin_note', 'admin_notes', 'rejection_reason', 'remarks'])
            ->first(fn ($col) => in_array($col, $names, true));

        return [
            'columns' => $columns,
            'names' => $names,
            'amountCol' => $amountCol,
            'eoCol' => $eoCol,
            'accNumCol' => $accNumCol,
            'accHolderCol' => $accHolderCol,
            'noteCol' => $noteCol,
        ];
    }

    private function normalizePayoutRow(object $row, array $meta): object
    {
        $amountCol = $meta['amountCol'];
        $accNumCol = $meta['accNumCol'];
        $accHolderCol = $meta['accHolderCol'];
        $noteCol = $meta['noteCol'];

        $row->display_amount = (float) ($row->{$amountCol} ?? $row->net_amount ?? $row->gross_amount ?? $row->amount ?? $row->total_amount ?? 0);
        $row->display_account_number = $accNumCol ? ($row->{$accNumCol} ?? '-') : '-';
        $row->display_account_holder = $accHolderCol ? ($row->{$accHolderCol} ?? '-') : '-';
        $row->display_notes = $noteCol ? ($row->{$noteCol} ?? '-') : '-';

        return $row;
    }

    private function getEoBalanceStats(int $eoId): array
    {
        $orderCols = Schema::getColumnListing('orders');
        $meta = $this->getPayoutSchemaMeta();

        $ordersQuery = DB::table('orders')
            ->join('events', 'orders.event_id', '=', 'events.id')
            ->where('events.eo_id', $eoId);

        if (in_array('payment_status', $orderCols, true)) {
            $ordersQuery->whereIn('orders.payment_status', ['paid', 'completed', 'confirmed', 'settlement']);
        }

        $grossRevenue = (float) $ordersQuery->sum('orders.total_amount');

        $payouts = DB::table('payouts')
            ->where($meta['eoCol'], $eoId)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($row) => $this->normalizePayoutRow($row, $meta));

        $approvedAmount = (float) $payouts->whereIn('status', ['approved', 'paid', 'completed'])->sum('display_amount');
        $pendingAmount = (float) $payouts->where('status', 'pending')->sum('display_amount');
        $availableBalance = max(0, $grossRevenue - ($approvedAmount + $pendingAmount));

        return [
            'grossRevenue' => $grossRevenue,
            'approvedAmount' => $approvedAmount,
            'pendingAmount' => $pendingAmount,
            'availableBalance' => $availableBalance,
            'payouts' => $payouts,
        ];
    }

    public function eoIndex(): View
    {
        $user = Auth::user();
        $stats = $this->getEoBalanceStats($user->id);

        return view('eo.payouts', $stats);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:50000'],
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:120'],
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');
        $stats = $this->getEoBalanceStats($user->id);

        if ($amount > $stats['availableBalance']) {
            return back()->with('error', 'Nominal pengajuan melebihi saldo tersedia (Rp ' . number_format($stats['availableBalance'], 0, ',', '.') . ').');
        }

        $meta = $this->getPayoutSchemaMeta();
        $names = $meta['names'];
        $now = now();

        $data = [
            $meta['amountCol'] => $amount,
            'status' => 'pending',
        ];

        if (in_array('eo_id', $names, true)) $data['eo_id'] = $user->id;
        if (in_array('user_id', $names, true)) $data['user_id'] = $user->id;

        if (in_array('event_id', $names, true)) {
            $eventId = $request->input('event_id')
                ?: DB::table('events')->where('eo_id', $user->id)->orderByDesc('id')->value('id')
                ?: DB::table('events')->orderBy('id')->value('id');
            if ($eventId) {
                $data['event_id'] = $eventId;
            }
        }

        if (in_array('payout_code', $names, true)) $data['payout_code'] = 'PAY-' . strtoupper(Str::random(8));
        if (in_array('code', $names, true)) $data['code'] = 'PAY-' . strtoupper(Str::random(8));
        if (in_array('gross_amount', $names, true)) $data['gross_amount'] = $amount;
        if (in_array('net_amount', $names, true)) $data['net_amount'] = $amount;
        if (in_array('admin_fee', $names, true)) $data['admin_fee'] = 0;
        if (in_array('platform_fee', $names, true)) $data['platform_fee'] = 0;
        if (in_array('fee', $names, true)) $data['fee'] = 0;
        if (in_array('bank_name', $names, true)) $data['bank_name'] = trim($request->input('bank_name'));
        if ($meta['accNumCol']) $data[$meta['accNumCol']] = trim($request->input('account_number'));
        if ($meta['accHolderCol']) $data[$meta['accHolderCol']] = trim($request->input('account_holder'));
        if (in_array('created_at', $names, true)) $data['created_at'] = $now;
        if (in_array('updated_at', $names, true)) $data['updated_at'] = $now;

        foreach ($meta['columns'] as $colName => $colMeta) {
            if (
                $colMeta->Null === 'NO'
                && $colMeta->Default === null
                && ! str_contains((string) $colMeta->Extra, 'auto_increment')
                && ! array_key_exists($colName, $data)
            ) {
                if (preg_match('/int|decimal|float|double|numeric/i', (string) $colMeta->Type)) {
                    $data[$colName] = 0;
                } elseif (preg_match('/date|time/i', (string) $colMeta->Type)) {
                    $data[$colName] = $now;
                } else {
                    $data[$colName] = '-';
                }
            }
        }

        DB::table('payouts')->insert($data);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'PAYOUT_REQUESTED',
            'description' => "EO {$user->name} mengajukan pencairan dana sebesar Rp " . number_format($amount, 0, ',', '.') . " ke {$request->input('bank_name')}.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Pengajuan pencairan dana berhasil dikirim dan menunggu verifikasi Admin.');
    }

    public function adminIndex(): View
    {
        $meta = $this->getPayoutSchemaMeta();
        $eoColumn = 'payouts.' . $meta['eoCol'];

        $payouts = DB::table('payouts')
            ->leftJoin('users', $eoColumn, '=', 'users.id')
            ->select('payouts.*', 'users.name as eo_name', 'users.email as eo_email')
            ->orderByRaw("CASE WHEN payouts.status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('payouts.id')
            ->get()
            ->map(fn ($row) => $this->normalizePayoutRow($row, $meta));

        return view('admin.payouts', compact('payouts'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $meta = $this->getPayoutSchemaMeta();
        $payout = DB::table('payouts')->where('id', $id)->first();
        if (! $payout) {
            return back()->with('error', 'Data pengajuan payout tidak ditemukan.');
        }

        $payout = $this->normalizePayoutRow($payout, $meta);
        $admin = Auth::user();
        $requestedStatus = $request->input('status');
        $notes = trim((string) $request->input('notes', ''));
        $names = $meta['names'];

        $targetStatus = $requestedStatus;
        if (isset($meta['columns']['status'])) {
            $enumType = $meta['columns']['status']->Type;
            if ($requestedStatus === 'approved') {
                if (str_contains($enumType, "'approved'")) $targetStatus = 'approved';
                elseif (str_contains($enumType, "'paid'")) $targetStatus = 'paid';
                elseif (str_contains($enumType, "'completed'")) $targetStatus = 'completed';
            } else {
                if (str_contains($enumType, "'rejected'")) $targetStatus = 'rejected';
                elseif (str_contains($enumType, "'declined'")) $targetStatus = 'declined';
            }
        }

        $updateData = ['status' => $targetStatus];
        if ($meta['noteCol']) $updateData[$meta['noteCol']] = $notes ?: null;
        if (in_array('processed_by', $names, true)) $updateData['processed_by'] = $admin->id;
        if (in_array('approved_by', $names, true)) $updateData['approved_by'] = $admin->id;
        if (in_array('processed_at', $names, true)) $updateData['processed_at'] = now();
        if (in_array('approved_at', $names, true) && $requestedStatus === 'approved') $updateData['approved_at'] = now();
        if (in_array('paid_at', $names, true) && $requestedStatus === 'approved') $updateData['paid_at'] = now();
        if (in_array('updated_at', $names, true)) $updateData['updated_at'] = now();

        DB::table('payouts')->where('id', $id)->update($updateData);

        $label = $requestedStatus === 'approved' ? 'menyetujui' : 'menolak';
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'PAYOUT_' . strtoupper($targetStatus),
            'description' => "Admin {$admin->name} {$label} pencairan dana #{$id} sebesar Rp " . number_format($payout->display_amount, 0, ',', '.') . '.',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Status pencairan dana berhasil diperbarui menjadi ' . strtoupper($targetStatus) . '.');
    }
}