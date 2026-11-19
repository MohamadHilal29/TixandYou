<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ScannerController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();$ticketCols = Schema::getColumnListing('tickets');

        $eventsQuery = Event::where('status', 'published')->orderBy('event_date', 'asc');
        if ($user->role === 'eo') {
            $eventsQuery->where('eo_id',$user->id);
        }
        $events =$eventsQuery->get();
        $eventIds =$events->pluck('id');

        $ticketsQuery = DB::table('tickets')
            ->join('orders', 'tickets.order_id', '=', 'orders.id')
            ->join('events', 'orders.event_id', '=', 'events.id')
            ->leftJoin('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->whereIn('events.id', $eventIds)
            ->select(
                'tickets.*',
                'events.title as event_title',
                'ticket_categories.name as category_name',
                'orders.buyer_name'
            );

        $allTickets = (clone$ticketsQuery)->get();
        $totalIssued =$allTickets->count();
        $totalCheckedIn =$allTickets->filter(function ($t) {$state = $t->checkin_status ?? $t->status ?? '';
            return in_array($state, ['used', 'checked_in'], true)
                || ! empty($t->checkin_at ?? $t->checked_in_at ?? $t->scanned_at ?? null);
        })->count();

        $recentQuery = clone$ticketsQuery;
        if (in_array('checkin_status', $ticketCols, true)) {$recentQuery->where('tickets.checkin_status', 'used');
        } elseif (in_array('status', $ticketCols, true)) {$recentQuery->whereIn('tickets.status', ['used', 'checked_in']);
        } elseif (in_array('checkin_at', $ticketCols, true)) {$recentQuery->whereNotNull('tickets.checkin_at');
        } elseif (in_array('checked_in_at', $ticketCols, true)) {$recentQuery->whereNotNull('tickets.checked_in_at');
        }

        $orderCol = in_array('checkin_at',$ticketCols, true)
            ? 'tickets.checkin_at'
            : (in_array('checked_in_at', $ticketCols, true)
                ? 'tickets.checked_in_at'
                : (in_array('updated_at', $ticketCols, true) ? 'tickets.updated_at' : 'tickets.id'));

        $recentCheckIns =$recentQuery
            ->orderByDesc($orderCol)
            ->limit(10)
            ->get();

        return view('admin.scanner', compact('events', 'totalIssued', 'totalCheckedIn', 'recentCheckIns'));
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'ticket_code' => ['required', 'string', 'max:2000'],
        ]);

        $rawInput = trim((string) $request->input('ticket_code'));$normalizedCode = strtoupper($rawInput);$displayCode = Str::limit($rawInput, 42, '...');$user = Auth::user();

        // 1. Cek format konteks QR Tix and You (harus diawali TIX- dan alfanumerik)
        if (! preg_match('/^TIX-[A-Z0-9]{4,32}$/',$normalizedCode)) {
            return back()->with('scan_result', [
                'valid' => false,
                'state' => 'TiketNotFound',
                'badge' => 'ALERT GAGAL • QR BEDA KONTEKS',
                'code' => $displayCode,
                'title' => 'Peringatan: Tiket Tidak Valid!',
                'message' => 'QR Code yang dipindai bukan format E-Ticket resmi Tix and You atau berasal dari konteks/aplikasi lain.',
            ]);
        }

        $ticketCols = Schema::getColumnListing('tickets');$orderCols = Schema::getColumnListing('orders');

        $codeColumn = in_array('ticket_code',$ticketCols, true)
            ? 'ticket_code'
            : (in_array('code', $ticketCols, true) ? 'code' : 'qr_code');

        $selectFields = [
            'tickets.*',
            'events.id as event_id',
            'events.eo_id',
            'events.title as event_title',
            'ticket_categories.name as category_name',
        ];

        if (in_array('payment_status', $orderCols, true)) {$selectFields[] = 'orders.payment_status as order_payment_status';
        }
        if (in_array('status', $orderCols, true)) {$selectFields[] = 'orders.status as order_status';
        }
        if (in_array('buyer_name', $orderCols, true)) {$selectFields[] = 'orders.buyer_name';
        }

        $ticket = DB::table('tickets')
            ->join('orders', 'tickets.order_id', '=', 'orders.id')
            ->join('events', 'orders.event_id', '=', 'events.id')
            ->leftJoin('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->where("tickets.{$codeColumn}", $normalizedCode)
            ->select($selectFields)
            ->first();

        // 2. Branch TiketNotFound -> AlertGagal ("Peringatan: Tiket Tidak Valid!")
        if (! $ticket) {
            return back()->with('scan_result', [
                'valid' => false,
                'state' => 'TiketNotFound',
                'badge' => 'ALERT GAGAL • TIKET TIDAK DITEMUKAN',
                'code' => $normalizedCode,
                'title' => 'Peringatan: Tiket Tidak Valid!',
                'message' => "Kode tiket {$normalizedCode} tidak terdaftar di database sistem.",
            ]);
        }

        $holderName =$ticket->holder_name ?? $ticket->participant_name ?? $ticket->buyer_name ?? 'Pemegang Tiket';
        $categoryName =$ticket->category_name ?? 'Reguler';

        // 3. Validasi kepemilikan event jika petugas adalah EO
        if ($user->role === 'eo' && (int) $ticket->eo_id !== (int)$user->id) {
            return back()->with('scan_result', [
                'valid' => false,
                'state' => 'TiketNotFound',
                'badge' => 'ALERT GAGAL • BEDA EVENT / EO',
                'code' => $normalizedCode,
                'title' => 'Peringatan: Tiket Tidak Valid!',
                'message' => "Tiket {$normalizedCode} terdaftar untuk event '{$ticket->event_title}' milik penyelenggara lain.",
                'holder' => $holderName,
                'event' => $ticket->event_title,
                'category' => $categoryName,
            ]);
        }

        // 4. Validasi status lunas pembayaran
        $paymentState = $ticket->order_payment_status ?? $ticket->order_status ?? 'paid';
        $isOrderPaid = in_array($paymentState, ['paid', 'completed', 'confirmed', 'settlement'], true);
        if (! $isOrderPaid) {
            return back()->with('scan_result', [
                'valid' => false,
                'state' => 'TiketNotFound',
                'badge' => 'ALERT GAGAL • BELUM LUNAS',
                'code' => $normalizedCode,
                'title' => 'Peringatan: Tiket Tidak Valid!',
                'message' => "Pembayaran untuk tiket {$normalizedCode} belum lunas (status: {$paymentState}).",
                'holder' => $holderName,
                'event' => $ticket->event_title,
                'category' => $categoryName,
            ]);
        }

        // 5. Branch TiketAlreadyUsed (checkin_status == 'used') -> AlertGagal ("Peringatan: Tiket Sudah Pernah Digunakan!")
        $currentStatus = $ticket->checkin_status ?? $ticket->status ?? 'unused';
        $alreadyUsed = in_array($currentStatus, ['used', 'checked_in'], true)
            || ! empty($ticket->checkin_at ?? $ticket->checked_in_at ?? $ticket->scanned_at ?? null);

        if ($alreadyUsed) {$usedTime = $ticket->checkin_at ?? $ticket->checked_in_at ?? $ticket->scanned_at ?? $ticket->updated_at ?? '-';
            return back()->with('scan_result', [
                'valid' => false,
                'state' => 'TiketAlreadyUsed',
                'badge' => 'ALERT GAGAL • ANTI-DOUBLE ENTRY',
                'code' => $normalizedCode,
                'title' => 'Peringatan: Tiket Sudah Pernah Digunakan!',
                'message' => "Tiket {$normalizedCode} sudah dipakai check-in sebelumnya pada {$usedTime}.",
                'holder' => $holderName,
                'event' => $ticket->event_title,
                'category' => $categoryName,
                'scanned_time' => $usedTime,
            ]);
        }

        // 6. Branch TiketValid (checkin_status == 'unused') -> UpdateStatus -> AlertSukses ("Check-in Berhasil! Silakan Masuk")
        $targetStatus = 'used';$ticketColumnInfo = collect(DB::select('SHOW COLUMNS FROM tickets'))->keyBy('Field');
        if ($ticketColumnInfo->has('status')) {
            $tType =$ticketColumnInfo['status']->Type;
            if (str_contains($tType, "'used'")) {
                $targetStatus = 'used';
            } elseif (str_contains($tType, "'checked_in'")) {
                $targetStatus = 'checked_in';
            }
        }

        $now = now();$updateData = [];
        if (in_array('checkin_status', $ticketCols, true))$updateData['checkin_status'] = 'used';
        if (in_array('status', $ticketCols, true)) $updateData['status'] =$targetStatus;
        if (in_array('checkin_at', $ticketCols, true)) $updateData['checkin_at'] =$now;
        if (in_array('checked_in_at', $ticketCols, true)) $updateData['checked_in_at'] =$now;
        if (in_array('scanned_at', $ticketCols, true)) $updateData['scanned_at'] =$now;
        if (in_array('updated_at', $ticketCols, true)) $updateData['updated_at'] =$now;

        if (! empty($updateData)) {
            DB::table('tickets')->where('id', $ticket->id)->update($updateData);
        }

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'TICKET_CHECKED_IN',
            'description' => "Tiket {$normalizedCode} ({$holderName}) berhasil check-in pada event '{$ticket->event_title}'.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('scan_result', [
            'valid' => true,
            'state' => 'TiketValid',
            'badge' => 'ALERT SUKSES • TIKET VALID',
            'code' => $normalizedCode,
            'title' => 'Check-in Berhasil! Silakan Masuk',
            'message' => 'Status tiket berhasil diperbarui menjadi used dan waktu check-in telah dicatat.',
            'holder' => $holderName,
            'event' => $ticket->event_title,
            'category' => $categoryName,
            'scanned_time' => $now->format('d M Y, H:i:s') . ' WIB',
        ]);
    }
}