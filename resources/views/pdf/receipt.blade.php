<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $transaksiItem->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            background: #fff;
        }
        .receipt {
            max-width: 400px;
            margin: 20px auto;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .logo span {
            color: #10b981;
        }
        .tagline {
            font-size: 10px;
            color: #888;
            margin-top: 5px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #ddd;
        }
        .invoice-number {
            font-size: 11px;
        }
        .invoice-number strong {
            display: block;
            font-size: 14px;
            color: #333;
        }
        .status {
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-verified {
            background: #d1fae5;
            color: #065f46;
        }
        .details {
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f3f3;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #666;
            font-size: 11px;
        }
        .detail-value {
            font-weight: bold;
            text-align: right;
        }
        .total-section {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .total-label {
            font-size: 12px;
            color: #666;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #10b981;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #ddd;
            font-size: 10px;
            color: #888;
        }
        .footer p {
            margin: 3px 0;
        }
        .period-section {
            background: #eff6ff;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .period-title {
            font-size: 10px;
            color: #1d4ed8;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .period-dates {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 8px 0;
            vertical-align: top;
        }
        table td:last-child {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="logo">Mutiara<span>27</span></div>
            <div class="tagline">Official Payment Receipt</div>
        </div>

        <table style="margin-bottom: 20px; border-bottom: 1px dashed #ddd; padding-bottom: 15px;">
            <tr>
                <td>
                    <div style="font-size: 10px; color: #888; text-transform: uppercase;">Invoice</div>
                    <div style="font-weight: bold; font-size: 13px;">#{{ $transaksiItem->invoice_number }}</div>
                </td>
                <td>
                    <div style="font-size: 10px; color: #888;">Tanggal</div>
                    <div style="font-weight: bold;">{{ \Carbon\Carbon::parse($transaksiItem->payment_date)->format('d M Y') }}</div>
                </td>
            </tr>
        </table>

        <div class="details">
            <table>
                <tr>
                    <td style="color: #666; font-size: 11px;">Nama Penyewa</td>
                    <td style="font-weight: bold;">{{ $transaksiItem->tenant->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color: #666; font-size: 11px;">Layanan</td>
                    <td style="font-weight: bold;">
                        {{ $transaksiItem->room?->roomType?->name ?? 'Sewa Kamar' }}
                        {{ $transaksiItem->room ? '- No. ' . $transaksiItem->room->room_number : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="color: #666; font-size: 11px;">Durasi</td>
                    <td style="font-weight: bold;">{{ $transaksiItem->duration_months }} Bulan</td>
                </tr>
                <tr>
                    <td style="color: #666; font-size: 11px;">Metode Pembayaran</td>
                    <td style="font-weight: bold;">Transfer Bank</td>
                </tr>
            </table>
        </div>

        <div class="period-section">
            <div class="period-title">📅 PERIODE SEWA</div>
            <table>
                <tr>
                    <td style="font-size: 11px; color: #666;">Mulai</td>
                    <td style="font-weight: bold; color: #1d4ed8;">
                        {{ \Carbon\Carbon::parse($transaksiItem->period_start_date)->format('d M Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 11px; color: #666;">Sampai</td>
                    <td style="font-weight: bold; color: #1d4ed8;">
                        {{ \Carbon\Carbon::parse($transaksiItem->period_end_date)->format('d M Y') }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="total-section">
            <table>
                <tr>
                    <td style="color: #666;">Total Pembayaran</td>
                    <td style="font-size: 20px; font-weight: bold; color: #10b981;">
                        Rp {{ number_format($transaksiItem->amount, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin: 15px 0;">
            @php
                $status = $transaksiItem->status;
                $statusConfig = [
                    'pending_verification' => ['label' => '⏳ MENUNGGU VERIFIKASI ADMIN', 'bg' => '#fef3c7', 'color' => '#92400e'],
                    'verified_by_admin' => ['label' => '📋 DIVERIFIKASI ADMIN - MENUNGGU OWNER', 'bg' => '#dbeafe', 'color' => '#1e40af'],
                    'verified_by_owner' => ['label' => '✓ LUNAS - TERVERIFIKASI', 'bg' => '#d1fae5', 'color' => '#065f46'],
                    'rejected_by_admin' => ['label' => '✕ DITOLAK OLEH ADMIN', 'bg' => '#fee2e2', 'color' => '#991b1b'],
                    'rejected_by_owner' => ['label' => '✕ DITOLAK OLEH PEMILIK', 'bg' => '#fee2e2', 'color' => '#991b1b'],
                ];
                $cfg = $statusConfig[$status] ?? ['label' => strtoupper(str_replace('_', ' ', $status)), 'bg' => '#e5e7eb', 'color' => '#374151'];
            @endphp
            <span style="background: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }}; padding: 6px 16px; border-radius: 20px; font-size: 10px; font-weight: bold; display: inline-block;">
                {{ $cfg['label'] }}
            </span>
        </div>

        @if(in_array($status, ['rejected_by_admin', 'rejected_by_owner']))
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
            <div style="font-size: 10px; font-weight: bold; color: #991b1b; margin-bottom: 5px;">⚠️ CATATAN PENOLAKAN:</div>
            <p style="font-size: 11px; color: #7f1d1d; margin: 0;">
                {{ $status === 'rejected_by_admin' ? ($transaksiItem->admin_notes ?? 'Tidak ada catatan') : ($transaksiItem->owner_notes ?? 'Tidak ada catatan') }}
            </p>
            <p style="font-size: 9px; color: #9b1c1c; margin-top: 8px; font-style: italic;">
                Silakan upload ulang bukti pembayaran yang benar melalui dashboard.
            </p>
        </div>
        @endif

        @if($status === 'verified_by_admin')
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px; margin-bottom: 15px;">
            <p style="font-size: 10px; color: #1e40af; margin: 0;">
                ℹ️ Pembayaran sudah diverifikasi admin. Menunggu konfirmasi dari pemilik kos.
            </p>
        </div>
        @endif

        <div class="footer">
            <p><strong>Mutiara27</strong></p>
            @if($businessSettings)
                <p>{{ $businessSettings->address ?? '' }}</p>
                <p>{{ $businessSettings->phone ?? '' }}</p>
            @endif
            <p style="margin-top: 10px; font-style: italic;">Terima kasih telah memilih kami</p>
        </div>
    </div>
</body>
</html>
