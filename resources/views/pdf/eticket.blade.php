<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Ticket - {{ $orderCode }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0A0A0A;
            font-size: 12px;
            margin: 0;
            padding: 24px;
        }
        .header {
            border-bottom: 2px solid #0A0A0A;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .brand {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: -0.5px;
        }
        .sub-brand {
            font-size: 10px;
            color: #52525B;
            margin-top: 2px;
        }
        .summary-box {
            background-color: #FAFAFA;
            border: 1px solid #E4E4E7;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 22px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            vertical-align: top;
            padding: 3px 0;
        }
        .label {
            font-size: 10px;
            color: #52525B;
            text-transform: uppercase;
        }
        .value {
            font-size: 12px;
            font-weight: bold;
            color: #0A0A0A;
        }
        .ticket-card {
            border: 1.5px solid #0A0A0A;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 16px;
            page-break-inside: avoid;
        }
        .ticket-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ticket-info {
            width: 66%;
            vertical-align: top;
            padding-right: 14px;
        }
        .ticket-qr {
            width: 34%;
            text-align: center;
            vertical-align: middle;
            border-left: 1px dashed #A1A1AA;
            padding-left: 14px;
        }
        .badge {
            display: inline-block;
            background-color: #0A0A0A;
            color: #FFFFFF;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            margin-bottom: 8px;
        }
        .event-title {
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 6px 0;
        }
        .meta {
            font-size: 11px;
            color: #52525B;
            margin: 2px 0;
        }
        .holder-box {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #E4E4E7;
        }
        .code-text {
            font-family: monospace;
            font-size: 11px;
            font-weight: bold;
            margin-top: 8px;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .footer-note {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #E4E4E7;
            font-size: 10px;
            color: #52525B;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">TIX AND YOU</div>
        <div class="sub-brand">Official E-Ticket & Bukti Pembayaran Lunas</div>
    </div>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td style="width: 50%;">
                    <div class="label">Kode Pesanan</div>
                    <div class="value">{{ $orderCode }}</div>
                </td>
                <td style="width: 50%;">
                    <div class="label">Pemesan</div>
                    <div class="value">{{ $buyerName }} ({{ $buyerEmail }})</div>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 8px;">
                    <div class="label">Status Pembayaran</div>
                    <div class="value">LUNAS (PAID)</div>
                </td>
                <td style="padding-top: 8px;">
                    <div class="label">Total Pembayaran</div>
                    <div class="value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    @foreach ($tickets as $index => $ticket)
        <div class="ticket-card">
            <table class="ticket-table">
                <tr>
                    <td class="ticket-info">
                        <span class="badge">TIKET #{{ $index + 1 }} • {{ strtoupper($ticket->category_name ?? 'REGULER') }}</span>
                        <div class="event-title">{{ $event->title }}</div>
                        <div class="meta"><strong>Tanggal:</strong> {{ optional($event->event_date)->format('d M Y') }} • {{ $event->start_time }} - {{ $event->end_time }} WIB</div>
                        <div class="meta"><strong>Venue:</strong> {{ $event->venue_name }} ({{ $event->venue_address }})</div>

                        <div class="holder-box">
                            <div class="meta"><strong>Pemegang Tiket:</strong> {{ $ticket->display_holder }}</div>
                            <div class="meta"><strong>No. Identitas:</strong> {{ $ticket->display_identity }}</div>
                        </div>
                    </td>
                    <td class="ticket-qr">
                        @if ($ticket->qr_data_uri)
                            <img src="{{ $ticket->qr_data_uri }}"
                                 width="140"
                                 height="140"
                                 style="width: 140px; height: 140px; display: block; margin: 0 auto;"
                                 alt="QR">
                        @else
                            <div style="border: 1px solid #0A0A0A; padding: 18px 8px; font-weight: bold;">QR TICKET</div>
                        @endif
                        <div class="code-text">{{ $ticket->display_code }}</div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

    <div class="footer-note">
        <strong>Ketentuan Masuk Venue:</strong><br>
        1. Tunjukkan E-Ticket PDF ini (cetak atau layar ponsel) kepada petugas Web Scanner di pintu masuk.<br>
        2. Setiap kode QR hanya berlaku untuk <strong>1 (satu) kali check-in</strong> dan otomatis hangus setelah dipindai.<br>
        3. Harap membawa kartu identitas asli (KTP/SIM/Kartu Pelajar) sesuai nama yang tertera pada tiket.
    </div>
</body>
</html>