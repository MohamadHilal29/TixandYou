<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoTicketService
{
    public function buildPdfForOrder(Order $order): array
    {
        $order->load('event.eo');
        $hasGd = extension_loaded('gd');

        $tickets = DB::table('tickets')
            ->leftJoin('ticket_categories', 'tickets.ticket_category_id', '=', 'ticket_categories.id')
            ->where('tickets.order_id', $order->id)
            ->select('tickets.*', 'ticket_categories.name as category_name', 'ticket_categories.price as category_price')
            ->get()
            ->map(function ($ticket) use ($order, $hasGd) {
                $code = $ticket->ticket_code ?? $ticket->code ?? $ticket->qr_code ?? ('TIX-' . $ticket->id);
                $ticket->display_code = $code;
                $ticket->display_holder = $ticket->holder_name ?? $ticket->participant_name ?? $order->buyer_name ?? 'Pemegang Tiket';
                $ticket->display_identity = $ticket->holder_identity ?? $ticket->identity_number ?? '-';
                $ticket->qr_data_uri = null;

                try {
                    if ($hasGd) {
                        // Jika ekstensi GD aktif, pakai PNG resolusi tinggi agar 100% tajam di DomPDF
                        $qrResponse = Http::timeout(8)->get('https://api.qrserver.com/v1/create-qr-code/', [
                            'size' => '300x300',
                            'margin' => 8,
                            'data' => $code,
                        ]);

                        if ($qrResponse->successful()) {
                            $ticket->qr_data_uri = 'data:image/png;base64,' . base64_encode($qrResponse->body());
                        }
                    } else {
                        // Fallback SVG: skalakan viewBox & koordinat rect ke 140x140 agar tidak mengecil di DomPDF
                        $qrResponse = Http::timeout(8)->get('https://api.qrserver.com/v1/create-qr-code/', [
                            'size' => '300x300',
                            'format' => 'svg',
                            'data' => $code,
                        ]);

                        if ($qrResponse->successful()) {
                            $svg = $qrResponse->body();
                            $targetSize = 140;

                            if (preg_match('/viewBox\s*=\s*["\']0\s+0\s+([0-9.]+)\s+([0-9.]+)["\']/i', $svg, $vb)) {
                                $vbWidth = (float) $vb[1];
                                if ($vbWidth > 0) {
                                    $scale = round($targetSize / $vbWidth, 4);
                                    $svg = preg_replace(
                                        '/<svg\b[^>]*>/i',
                                        '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="' . $targetSize . '" height="' . $targetSize . '" viewBox="0 0 ' . $targetSize . ' ' . $targetSize . '"><g transform="scale(' . $scale . ')">',
                                        $svg,
                                        1
                                    );
                                    $svg = preg_replace('/<\/svg>\s*$/i', '</g></svg>', $svg, 1);
                                }
                            }

                            $ticket->qr_data_uri = 'data:image/svg+xml;base64,' . base64_encode($svg);
                        }
                    }
                } catch (\Throwable $e) {
                    $ticket->qr_data_uri = null;
                }

                return $ticket;
            });

        $orderCode = $order->order_code ?? $order->order_number ?? ('ORD-' . $order->id);
        $buyerName = $order->buyer_name ?? 'Pembeli';
        $buyerEmail = $order->buyer_email ?? '';
        $totalAmount = $order->total_amount ?? 0;

        $pdf = Pdf::loadView('pdf.eticket', [
            'order' => $order,
            'event' => $order->event,
            'tickets' => $tickets,
            'orderCode' => $orderCode,
            'buyerName' => $buyerName,
            'buyerEmail' => $buyerEmail,
            'totalAmount' => $totalAmount,
        ])->setPaper('a4', 'portrait');

        return [
            'binary' => $pdf->output(),
            'filename' => "E-Ticket-{$orderCode}.pdf",
            'orderCode' => $orderCode,
            'buyerName' => $buyerName,
            'buyerEmail' => $buyerEmail,
            'ticketsCount' => $tickets->count(),
        ];
    }

    public function sendOrderTickets(Order $order): array
    {
        $apiKey = trim((string) config('services.brevo.api_key'));
        $senderEmail = trim((string) config('services.brevo.sender_email'));
        $senderName = trim((string) config('services.brevo.sender_name', 'Tix and You'));

        if ($apiKey === '' || str_contains($apiKey, 'isi-api-key') || str_contains($apiKey, 'tempel-api-key') || $senderEmail === '') {
            return [
                'sent' => false,
                'message' => 'E-Ticket PDF siap diunduh, namun BREVO_API_KEY atau BREVO_SENDER_EMAIL di file .env belum diisi dengan benar.',
            ];
        }

        $bundle = $this->buildPdfForOrder($order);

        if (empty($bundle['buyerEmail'])) {
            return [
                'sent' => false,
                'message' => 'Alamat email pembeli tidak ditemukan pada data pesanan.',
            ];
        }

        $eventTitle = $order->event->title ?? 'Event Tix and You';
        $eventDate = optional($order->event->event_date)->format('d M Y') ?? '-';
        $venueName = $order->event->venue_name ?? '-';

        $htmlContent = "
            <div style='font-family: Arial, sans-serif; color: #0A0A0A; max-width: 560px; margin: 0 auto; padding: 20px; border: 1px solid #E4E4E7; border-radius: 12px;'>
                <h2 style='margin-top: 0;'>Pembayaran Berhasil! Ini E-Ticket Anda</h2>
                <p>Halo <strong>{$bundle['buyerName']}</strong>,</p>
                <p>Terima kasih telah melakukan pembelian tiket di <strong>Tix and You</strong>. Pesanan Anda telah lunas dan <strong>{$bundle['ticketsCount']} E-Ticket PDF</strong> terlampir pada email ini.</p>
                <div style='background: #FAFAFA; padding: 14px; border-radius: 8px; border: 1px solid #E4E4E7; margin: 16px 0; font-size: 13px;'>
                    <p style='margin: 4px 0;'><strong>Kode Pesanan:</strong> {$bundle['orderCode']}</p>
                    <p style='margin: 4px 0;'><strong>Acara:</strong> {$eventTitle}</p>
                    <p style='margin: 4px 0;'><strong>Jadwal:</strong> {$eventDate}</p>
                    <p style='margin: 4px 0;'><strong>Lokasi:</strong> {$venueName}</p>
                </div>
                <p style='font-size: 12px; color: #52525B;'>Unduh lampiran PDF di bawah dan tunjukkan QR Code kepada petugas saat masuk ke venue.</p>
            </div>
        ";

        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->timeout(15)->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => $senderName,
                    'email' => $senderEmail,
                ],
                'to' => [
                    [
                        'email' => $bundle['buyerEmail'],
                        'name' => $bundle['buyerName'],
                    ],
                ],
                'subject' => "E-Ticket Anda: {$eventTitle} [{$bundle['orderCode']}]",
                'htmlContent' => $htmlContent,
                'attachment' => [
                    [
                        'content' => base64_encode($bundle['binary']),
                        'name' => $bundle['filename'],
                    ],
                ],
            ]);

            if ($response->successful()) {
                return [
                    'sent' => true,
                    'message' => "E-Ticket PDF berhasil dikirim ke email {$bundle['buyerEmail']} via Brevo!",
                ];
            }

            $brevoDetail = $response->json('message') ?? $response->body();
            Log::error('Brevo API Error', ['status' => $response->status(), 'body' => $response->body()]);

            return [
                'sent' => false,
                'message' => "Brevo API menolak pengiriman ({$response->status()}): {$brevoDetail}",
            ];
        } catch (\Throwable $e) {
            Log::error('Brevo Exception', ['error' => $e->getMessage()]);

            return [
                'sent' => false,
                'message' => 'Pembayaran lunas & PDF siap diunduh, namun koneksi ke Brevo API gagal: ' . $e->getMessage(),
            ];
        }
    }
}