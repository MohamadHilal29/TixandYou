<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketCategory;
use App\Services\BrevoTicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function selectTickets(Request $request, Event $event): RedirectResponse
    {
        abort_if($event->status !== 'published', 404);

        $quantities = $request->input('quantities', []);
        $selectedItems = [];
        $totalTickets = 0;
        $subtotal = 0;

        $categories = TicketCategory::where('event_id', $event->id)->get()->keyBy('id');

        foreach ($quantities as $categoryId => $qty) {
            $qty = (int) $qty;
            if ($qty <= 0) {
                continue;
            }

            $category = $categories->get((int) $categoryId);
            if (! $category) {
                return back()->withErrors(['tickets' => 'Kategori tiket tidak valid.']);
            }

            $available = max(0, $category->quota - $category->sold);
            if ($qty > $available) {
                return back()->withErrors([
                    'tickets' => "Sisa kuota untuk kategori {$category->name} hanya tersisa {$available} tiket.",
                ]);
            }

            $selectedItems[] = [
                'category_id' => $category->id,
                'name' => $category->name,
                'price' => $category->price,
                'qty' => $qty,
                'line_total' => $category->price * $qty,
            ];

            $totalTickets += $qty;
            $subtotal += ($category->price * $qty);
        }

        if ($totalTickets < 1) {
            return back()->withErrors(['tickets' => 'Pilih minimal 1 tiket untuk melanjutkan.']);
        }

        if ($totalTickets > 5) {
            return back()->withErrors(['tickets' => 'Maksimal pembelian adalah 5 tiket per transaksi.']);
        }

        $serviceFee = (int) round($subtotal * 0.05);

        session([
            'checkout_cart' => [
                'event_id' => $event->id,
                'event_slug' => $event->slug,
                'event_title' => $event->title,
                'event_date' => $event->event_date->format('d M Y'),
                'venue_name' => $event->venue_name,
                'items' => $selectedItems,
                'total_tickets' => $totalTickets,
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'total_amount' => $subtotal + $serviceFee,
            ],
        ]);

        return redirect()->route('checkout.participant');
    }

    public function participantForm(): View|RedirectResponse
    {
        $cart = session('checkout_cart');
        if (! $cart) {
            return redirect()->route('home')->withErrors(['tickets' => 'Sesi pemilihan tiket telah berakhir. Silakan pilih tiket kembali.']);
        }

        $user = Auth::user();

        return view('buyer.participant-info', compact('cart', 'user'));
    }

    public function storeParticipant(Request $request): RedirectResponse
    {
        $cart = session('checkout_cart');
        if (! $cart) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_email' => ['required', 'email', 'max:255'],
            'buyer_phone' => ['required', 'string', 'max:30'],
            'holders' => ['required', 'array', 'size:' . $cart['total_tickets']],
            'holders.*.name' => ['required', 'string', 'max:255'],
            'holders.*.identity_number' => ['required', 'string', 'max:50'],
        ], [
            'holders.*.name.required' => 'Nama lengkap setiap pemegang tiket wajib diisi.',
            'holders.*.identity_number.required' => 'Nomor identitas (NIK/SIM/Kartu Pelajar) wajib diisi.',
        ]);

        session(['checkout_participant' => $validated]);

        return redirect()->route('checkout.review');
    }

    public function review(): View|RedirectResponse
    {
        $cart = session('checkout_cart');
        $participant = session('checkout_participant');

        if (! $cart || ! $participant) {
            return redirect()->route('home');
        }

        return view('buyer.checkout-review', compact('cart', 'participant'));
    }

    public function processOrder(Request $request): RedirectResponse
    {
        $cart = session('checkout_cart');
        $participant = session('checkout_participant');

        if (! $cart || ! $participant) {
            return redirect()->route('home');
        }

        $totalQty = collect($cart['items'])->sum('qty');
        if ($totalQty < 1 || $totalQty > 5) {
            return redirect()->route('home')->withErrors(['tickets' => 'Jumlah tiket tidak valid (maksimal 5 tiket per transaksi).']);
        }

        try {
            $orderId = DB::transaction(function () use ($cart, $participant, $totalQty, $request) {
                $recalculatedSubtotal = 0;

                foreach ($cart['items'] as $item) {
                    $category = TicketCategory::where('id', $item['category_id'])
                        ->where('event_id', $cart['event_id'])
                        ->lockForUpdate()
                        ->first();

                    if (! $category) {
                        throw ValidationException::withMessages([
                            'checkout' => 'Salah satu kategori tiket sudah tidak tersedia.',
                        ]);
                    }

                    $remaining = $category->quota - $category->sold;
                    if ($remaining < $item['qty']) {
                        throw ValidationException::withMessages([
                            'checkout' => "Maaf, kuota tiket {$category->name} baru saja habis atau tersisa {$remaining} tiket.",
                        ]);
                    }

                    $category->increment('sold', $item['qty']);
                    $recalculatedSubtotal += ($category->price * $item['qty']);
                }

                $serviceFee = (int) round($recalculatedSubtotal * 0.05);
                $totalAmount = $recalculatedSubtotal + $serviceFee;
                $orderCode = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

                $columns = Schema::getColumnListing('orders');
                $payload = [
                    'user_id' => Auth::id(),
                    'event_id' => $cart['event_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (in_array('order_code', $columns)) $payload['order_code'] = $orderCode;
                if (in_array('order_number', $columns)) $payload['order_number'] = $orderCode;
                if (in_array('invoice_number', $columns)) $payload['invoice_number'] = $orderCode;
                if (in_array('buyer_name', $columns)) $payload['buyer_name'] = $participant['buyer_name'];
                if (in_array('buyer_email', $columns)) $payload['buyer_email'] = $participant['buyer_email'];
                if (in_array('buyer_phone', $columns)) $payload['buyer_phone'] = $participant['buyer_phone'];
                if (in_array('total_tickets', $columns)) $payload['total_tickets'] = $totalQty;
                if (in_array('quantity', $columns)) $payload['quantity'] = $totalQty;
                if (in_array('subtotal', $columns)) $payload['subtotal'] = $recalculatedSubtotal;
                if (in_array('service_fee', $columns)) $payload['service_fee'] = $serviceFee;
                if (in_array('total_amount', $columns)) $payload['total_amount'] = $totalAmount;
                if (in_array('payment_status', $columns)) $payload['payment_status'] = 'pending';
                if (in_array('status', $columns)) $payload['status'] = 'pending';

                $insertedOrderId = DB::table('orders')->insertGetId($payload);

                session([
                    'last_order_details' => [
                        'order_id' => $insertedOrderId,
                        'order_code' => $orderCode,
                        'cart' => $cart,
                        'participant' => $participant,
                        'total_amount' => $totalAmount,
                    ],
                ]);

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'ORDER_CREATED',
                    'description' => "Pesanan {$orderCode} dibuat oleh {$participant['buyer_name']} ({$totalQty} tiket) untuk event '{$cart['event_title']}'.",
                    'ip_address' => $request->ip(),
                ]);

                return $insertedOrderId;
            });
        } catch (ValidationException $e) {
            return redirect()
                ->route('event.tickets', $cart['event_slug'])
                ->withErrors($e->errors());
        }

        session()->forget(['checkout_cart', 'checkout_participant']);

        return redirect()->route('checkout.payment', $orderId);
    }

    public function paymentPage(Order $order): View|RedirectResponse
    {
        $isPaid = ($order->payment_status ?? $order->status ?? '') === 'paid';
        if ($isPaid) {
            return redirect()->route('checkout.confirm', $order->id);
        }

        $order->load('event');
        $details = session('last_order_details');

        return view('buyer.payment-method', compact('order', 'details'));
    }

    public function confirmPayment(Request $request, Order $order, BrevoTicketService $brevoService): RedirectResponse
    {
        $request->validate([
            'payment_method' => ['required', 'string', 'max:100'],
        ]);

        $details = session('last_order_details');

        DB::transaction(function () use ($order, $request, $details) {
            $lockedOrder = DB::table('orders')->where('id', $order->id)->lockForUpdate()->first();
            $alreadyPaid = (($lockedOrder->payment_status ?? $lockedOrder->status ?? '') === 'paid');

            if ($alreadyPaid) {
                return;
            }

            $orderCols = collect(DB::select('SHOW COLUMNS FROM orders'))->keyBy('Field');
            $orderUpdate = ['updated_at' => now()];

            if ($orderCols->has('payment_status')) {
                $orderUpdate['payment_status'] = 'paid';
            }
            if ($orderCols->has('status')) {
                $type = $orderCols['status']->Type;
                if (str_contains($type, "'paid'") || ! str_starts_with($type, 'enum')) {
                    $orderUpdate['status'] = 'paid';
                } elseif (str_contains($type, "'completed'")) {
                    $orderUpdate['status'] = 'completed';
                } elseif (str_contains($type, "'confirmed'")) {
                    $orderUpdate['status'] = 'confirmed';
                }
            }
            if ($orderCols->has('payment_method')) {
                $orderUpdate['payment_method'] = $request->payment_method;
            }
            if ($orderCols->has('paid_at')) {
                $orderUpdate['paid_at'] = now();
            }

            DB::table('orders')->where('id', $order->id)->update($orderUpdate);

            $existingCount = DB::table('tickets')->where('order_id', $order->id)->count();
            if ($existingCount > 0) {
                return;
            }

            $ticketCols = collect(DB::select('SHOW COLUMNS FROM tickets'))->keyBy('Field');
            $initialTicketStatus = 'valid';
            if ($ticketCols->has('status')) {
                $tType = $ticketCols['status']->Type;
                if (str_contains($tType, "'valid'")) {
                    $initialTicketStatus = 'valid';
                } elseif (str_contains($tType, "'unused'")) {
                    $initialTicketStatus = 'unused';
                } elseif (str_contains($tType, "'active'")) {
                    $initialTicketStatus = 'active';
                }
            }

            $ticketsToIssue = [];
            if ($details && ($details['order_id'] ?? null) == $order->id) {
                $holders = $details['participant']['holders'] ?? [];
                $idx = 0;
                foreach ($details['cart']['items'] as $item) {
                    for ($q = 0; $q < $item['qty']; $q++) {
                        $ticketsToIssue[] = [
                            'category_id' => $item['category_id'],
                            'holder_name' => $holders[$idx]['name'] ?? ($lockedOrder->buyer_name ?? 'Pemegang Tiket'),
                            'holder_identity' => $holders[$idx]['identity_number'] ?? '-',
                        ];
                        $idx++;
                    }
                }
            } else {
                $fallbackCat = TicketCategory::where('event_id', $order->event_id)->first();
                $qty = (int) ($lockedOrder->total_tickets ?? $lockedOrder->quantity ?? 1);
                for ($q = 0; $q < max(1, $qty); $q++) {
                    $ticketsToIssue[] = [
                        'category_id' => $fallbackCat?->id,
                        'holder_name' => $lockedOrder->buyer_name ?? (Auth::user()?->name ?? 'Pemegang Tiket'),
                        'holder_identity' => '-',
                    ];
                }
            }

            foreach ($ticketsToIssue as $unit) {
                $code = 'TIX-' . strtoupper(Str::random(8));
                $row = [
                    'order_id' => $order->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($ticketCols->has('event_id')) $row['event_id'] = $order->event_id;
                if ($ticketCols->has('ticket_category_id')) $row['ticket_category_id'] = $unit['category_id'];
                if ($ticketCols->has('user_id')) $row['user_id'] = $lockedOrder->user_id ?? Auth::id();
                if ($ticketCols->has('ticket_code')) $row['ticket_code'] = $code;
                if ($ticketCols->has('code')) $row['code'] = $code;
                if ($ticketCols->has('qr_code')) $row['qr_code'] = $code;
                if ($ticketCols->has('holder_name')) $row['holder_name'] = $unit['holder_name'];
                if ($ticketCols->has('participant_name')) $row['participant_name'] = $unit['holder_name'];
                if ($ticketCols->has('attendee_name')) $row['attendee_name'] = $unit['holder_name'];
                if ($ticketCols->has('holder_identity')) $row['holder_identity'] = $unit['holder_identity'];
                if ($ticketCols->has('identity_number')) $row['identity_number'] = $unit['holder_identity'];
                if ($ticketCols->has('status')) $row['status'] = $initialTicketStatus;

                DB::table('tickets')->insert($row);
            }

            $orderRef = $lockedOrder->order_code ?? $lockedOrder->order_number ?? ('ORD-' . $order->id);
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'PAYMENT_CONFIRMED',
                'description' => "Pembayaran pesanan {$orderRef} lunas via {$request->payment_method}. " . count($ticketsToIssue) . " e-ticket diterbitkan.",
                'ip_address' => $request->ip(),
            ]);
        });

        $emailResult = $brevoService->sendOrderTickets($order->fresh());

        return redirect()
            ->route('checkout.confirm', $order->id)
            ->with('success', 'Pembayaran berhasil dikonfirmasi! E-Ticket Anda telah diterbitkan.')
            ->with('email_status', $emailResult);
    }

    public function orderConfirm(Order $order): View
    {
        $order->load('event.eo');

        $tickets = DB::table('tickets')
            ->leftJoin('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->where('tickets.order_id', $order->id)
            ->select('tickets.*', 'ticket_categories.name as category_name', 'ticket_categories.price as category_price')
            ->get();

        return view('buyer.order-confirm', compact('order', 'tickets'));
    }

    public function downloadPdf(Order $order, BrevoTicketService $brevoService): Response
    {
        $bundle = $brevoService->buildPdfForOrder($order);

        return response($bundle['binary'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $bundle['filename'] . '"',
        ]);
    }
}