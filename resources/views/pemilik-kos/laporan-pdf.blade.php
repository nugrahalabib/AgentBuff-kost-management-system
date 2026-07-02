<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $report->title }}</title>
    <style>
        @page {
            margin: 1cm 1.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2937;
            font-size: 10pt;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #000;
        }

        .header .subtitle {
            margin: 5px 0;
            font-size: 10pt;
            color: #555;
        }

        .summary-box {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 10px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .summary-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 10px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
        }

        .summary-grid {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .summary-item {
            text-align: center;
            padding: 5px;
        }

        .summary-value {
            font-size: 14pt;
            font-weight: bold;
            color: #059669;
        }

        .summary-label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
            margin-top: 4px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        table.data-table th {
            background-color: #374151;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #374151;
        }

        table.data-table td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            vertical-align: middle;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-success {
            color: #059669;
        }

        .text-danger {
            color: #dc2626;
        }

        .text-warning {
            color: #d97706;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN {{ strtoupper(str_replace('_', ' ', $report->report_type)) }}</h1>
        <div class="subtitle">{{ $report->title }}</div>
        <div class="subtitle">Bisnis: {{ $report->owner->name }} | Dibuat: {{ $report->created_at->format('d F Y') }}
        </div>
    </div>

    @if ($report->report_type === 'financial_report')

        <div class="summary-box">
            <div class="summary-title">RINGKASAN KEUANGAN</div>
            <table class="summary-grid">
                <tr>
                    <td class="summary-item">
                        <div class="summary-value">Rp {{ number_format($reportData['gross_revenue'], 0, ',', '.') }}</div>
                        <div class="summary-label">Pendapatan Kotor</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value text-danger">Rp
                            {{ number_format($reportData['opex_total'], 0, ',', '.') }}
                        </div>
                        <div class="summary-label">Operasional (OPEX)</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value text-success">Rp
                            {{ number_format($reportData['noi_total'], 0, ',', '.') }}
                        </div>
                        <div class="summary-label">Pendapatan Bersih (NOI)</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value">{{ $reportData['transaction_count'] }}</div>
                        <div class="summary-label">Total Transaksi</div>
                    </td>
                </tr>
            </table>
        </div>

        <h3 style="margin-top: 20px; font-size: 12pt; border-bottom: 2px solid #ccc; padding-bottom: 5px; color: #374151;">
            DETAIL TRANSAKSI</h3>
        @if (count($reportData['transactions']) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="15%">Tanggal</th>
                        <th width="15%">No. Ref</th>
                        <th width="20%">Penyewa</th>
                        <th width="10%">Kamar</th>
                        <th width="20%">Periode Sewa</th>
                        <th width="20%">Status</th>
                        <!-- <th width="20%" class="text-right">Jumlah (IDR)</th>  -->
                        <!-- Owner PDF had Jumlah before, but Admin PDF didn't show it in table? 
                                                     Wait, Admin PDF DID NOT show Amount? 
                                                     Admin PDF: Tanggal, Ref, Penyewa, Kamar, Periode, Status.
                                                     Owner PDF: Tanggal, Ref, Penyewa, Kamar, Status, Jumlah.
                                                     User asked to add "Periode Sewa".
                                                     Space might be tight.
                                                     Headers:
                                                     Date(15), Ref(15), Tenant(20), Room(10), Period(20), Status(15), Amount(20) = 115% !
                                                     I need to adjust widths or Remove Amount?
                                                     Admin PDF does NOT show Amount in the table rows?
                                                     Let's check Admin PDF again in my memory... 
                                                     Admin PDF (Viewed in Step 10219 and 10268): 
                                                     Headers: Tanggal(15), Ref(15), Penyewa(20), Kamar(10), Periode(20), Status(20). Sum = 100.
                                                     It does NOT show Amount.
                                                     Owner PDF DOES show Amount (Line 166).
                                                     If I add Periode Sewa, I must adjust widths.
                                                     Maybe: Date(12), Ref(12), Tenant(18), Room(8), Period(18), Status(15), Amount(17) = 100.
                                                     I will keep Amount as it is financial report.
                                                -->
                        <th width="20%" class="text-right">Jumlah (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData['transactions'] as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                            <td>{{ $transaction->reference_number ?? '-' }}</td>
                            <td>
                                <div>{{ $transaction->tenant->name ?? 'Deleted User' }}</div>
                            </td>
                            <td class="text-center">{{ $transaction->room->room_number ?? '-' }}</td>

                            <td class="text-center">
                                @php
                                    $trxTenant = $transaction->tenant;
                                    $trxPeriod = '-';
                                    if ($trxTenant) {
                                        $firstRoom = $trxTenant->occupiedRoom->first();
                                        $checkInDate = $firstRoom && $firstRoom->pivot ? $firstRoom->pivot->check_in_date : null;
                                        $startDate = $checkInDate ? \Carbon\Carbon::parse($checkInDate) : $transaction->period_start_date;
                                        $trxPeriod = ($startDate ? $startDate->format('d/m/y') : '-') . ' - ' . ($transaction->period_end_date ? $transaction->period_end_date->format('d/m/y') : '-');
                                    }
                                @endphp
                                {{ $trxPeriod }}
                            </td>

                            <td class="text-center">
                                @php
                                    $statusColor = match ($transaction->status) {
                                        'verified_by_owner' => 'badge-success',
                                        'verified_by_admin' => 'badge-info',
                                        'pending_verification' => 'badge-warning',
                                        default => 'badge-danger',
                                    };
                                    $statusLabel = match ($transaction->status) {
                                        'verified_by_owner' => 'Lunas',
                                        'verified_by_admin' => 'Verif Admin',
                                        'pending_verification' => 'Menunggu',
                                        default => 'Ditolak',
                                    };
                                @endphp
                                <span class="badge {{ $statusColor }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="text-right font-bold">
                                {{ number_format($transaction->final_amount ?? $transaction->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <!-- Total Row -->
                    <tr style="background-color: #e5e7eb; font-weight: bold; border-top: 2px solid #9ca3af;">
                        <td colspan="5" class="text-right">TOTAL PENDAPATAN</td>
                        <td class="text-right">{{ number_format($reportData['gross_revenue'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <p class="text-center" style="color: #666; padding: 20px; background: #fff; border: 1px dashed #ccc;">Tidak ada data
                transaksi untuk periode ini.</p>
        @endif

    @elseif ($report->report_type === 'room_status_report')

        <div class="summary-box">
            <div class="summary-title">RINGKASAN OKUPANSI</div>
            <table class="summary-grid">
                <tr>
                    <td class="summary-item">
                        <div class="summary-value">{{ $reportData['total_rooms'] }}</div>
                        <div class="summary-label">Total Unit</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value text-success">{{ $reportData['occupied'] }}</div>
                        <div class="summary-label">Terisi</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value text-warning">{{ $reportData['available'] }}</div>
                        <div class="summary-label">Kosong</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value">{{ $reportData['occupancy_rate'] }}%</div>
                        <div class="summary-label">Occupancy Rate</div>
                    </td>
                </tr>
            </table>
        </div>

        <h3 style="margin-top: 20px; font-size: 12pt; border-bottom: 2px solid #ccc; padding-bottom: 5px; color: #374151;">
            DAFTAR UNIT KAMAR</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="10%">Kamar</th>
                    <th width="10%">Lantai</th>
                    <th width="20%">Tipe Unit</th>
                    <th width="20%">Harga/Bulan (IDR)</th>
                    <th width="15%">Status</th>
                    <th width="25%">Penghuni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportData['kamar'] as $room)
                    <tr>
                        <td class="text-center font-bold">{{ $room->room_number }}</td>
                        <td class="text-center">{{ $room->floor_number }}</td>
                        <td>{{ $room->roomType->name ?? '-' }}</td>
                        <td class="text-right">
                            {{ number_format($room->price_per_month ?? $room->roomType->price_per_month ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @php
                                $statusMap = [
                                    'occupied' => ['color' => 'badge-success', 'label' => 'Terisi'],
                                    'available' => ['color' => 'badge-info', 'label' => 'Kosong'],
                                    'maintenance' => ['color' => 'badge-danger', 'label' => 'Perbaikan'],
                                ];
                                $st = $statusMap[$room->status] ?? ['color' => 'badge-warning', 'label' => $room->status];
                            @endphp
                            <span class="badge {{ $st['color'] }}">{{ $st['label'] }}</span>
                        </td>
                        <td>
                            @if($room->status === 'occupied' && $room->occupants->count() > 0)
                                @foreach($room->occupants as $index => $occupant)
                                    <div style="{{ $index > 0 ? 'margin-top:4px;' : 'font-weight:bold;' }}">{{ $occupant->name }}</div>
                                @endforeach
                            @elseif($room->status === 'occupied')
                                <span style="color:#d97706; font-style:italic;">(Penghuni dihapus)</span>
                            @else
                                <span style="color:#999">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif ($report->report_type === 'tenant_report')

        <div class="summary-box">
            <div class="summary-title">STATISTIK PENYEWA</div>
            <table class="summary-grid">
                <tr>
                    <td class="summary-item">
                        <div class="summary-value">{{ $reportData['tenant_count'] }}</div>
                        <div class="summary-label">Total Terdaftar</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value text-success">{{ $reportData['active_tenants'] }}</div>
                        <div class="summary-label">Aktif (Ada Kamar)</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value text-danger">{{ $reportData['inactive_tenants'] }}</div>
                        <div class="summary-label">Tidak Aktif</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value text-warning">{{ $reportData['expiring_soon'] }}</div>
                        <div class="summary-label">Habis &lt; 30 Hari</div>
                    </td>
                </tr>
            </table>
        </div>

        <h3 style="margin-top: 20px; font-size: 12pt; border-bottom: 2px solid #ccc; padding-bottom: 5px; color: #374151;">
            DATA LENGKAP PENYEWA</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="20%">Nama Penyewa</th>
                    <th width="15%">No. HP</th>
                    <th width="10%">Kamar</th>
                    <th width="25%" class="text-center">Periode Sewa</th>
                    <th width="15%" class="text-center">Status Bayar</th>
                    <th width="15%" class="text-center">Akun</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportData['tenants'] as $tenant)
                    <tr>
                        <td class="font-bold">{{ $tenant->name }}</td>
                        <td>{{ $tenant->tenantProfile->phone ?? '-' }}</td>
                        <td class="text-center font-bold">
                            {{ $tenant->sort_room_number ?: '-' }}
                        </td>
                        @php
                            $activeTrx = $tenant->tenantTransactions->where('status', 'verified_by_owner')->sortByDesc('period_end_date')->first();
                        @endphp
                        <td class="text-center">
                            @if($activeTrx)
                                @php
                                    $checkInDate = $tenant->activeRoom && $tenant->activeRoom->pivot ? $tenant->activeRoom->pivot->check_in_date : null;
                                    $startDate = $checkInDate ? \Carbon\Carbon::parse($checkInDate) : $activeTrx->period_start_date;
                                @endphp
                                {{ $startDate ? $startDate->format('d/m/y') : '-' }} -
                                {{ $activeTrx->period_end_date ? $activeTrx->period_end_date->format('d/m/y') : '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $payLabel = $tenant->payment_status_label;
                                $payClass = match ($payLabel) {
                                    'Lancar' => 'badge-success',
                                    'Telat Bayar' => 'badge-danger',
                                    'Segera Habis' => 'badge-warning',
                                    default => ''
                                };
                            @endphp
                            @if($payLabel == '-')
                                <span class="badge" style="background: #eee; color: #666;">-</span>
                            @else
                                <span class="badge {{ $payClass }}">{{ $payLabel }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($tenant->account_status_label === 'Aktif')
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif

    <div class="footer">
        Dicetak pada {{ now()->format('d F Y H:i') }} | Sistem Manajemen Kos
    </div>
</body>

</html>