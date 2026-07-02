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
        <div class="subtitle">Owner: {{ $report->owner->name }} | Dibuat: {{ $report->created_at->format('d F Y') }}
        </div>
    </div>

    @if ($report->report_type === 'financial_report')

        <div class="summary-box">
            <div class="summary-title">RINGKASAN STATUS TRANSAKSI</div>
            <table class="summary-grid">
                <tr>
                    <td class="summary-item">
                        <div class="summary-value text-success">{{ $reportData['verified_owner_count'] }}</div>
                        <div class="summary-label">Verified (Selesai)</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value text-danger">{{ $reportData['rejected_count'] }}</div>
                        <div class="summary-label">Ditolak / Batal</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value text-warning">
                            {{ $reportData['pending_count'] + $reportData['verified_admin_count'] }}
                        </div>
                        <div class="summary-label">Menunggu Verifikasi</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value">{{ $reportData['transaction_count'] }}</div>
                        <div class="summary-label">Total Transaksi</div>
                    </td>
                </tr>
            </table>
        </div>

        <h3 style="margin-top: 20px; font-size: 12pt; border-bottom: 2px solid #ccc; padding-bottom: 5px; color: #374151;">
            DETAIL STATUS TRANSAKSI</h3>
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData['transactions'] as $transaction)
                        <tr>
                            <td>
                                <div>{{ $transaction->created_at->format('d/m/Y') }}</div>
                                <div style="font-size: 8pt; color: #666;">{{ $transaction->created_at->format('H:i') }} WIB</div>
                            </td>
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
                                        'verified_by_owner' => 'Lunas', // Or 'Verified'
                                        'verified_by_admin' => 'Verif Admin',
                                        'pending_verification' => 'Menunggu',
                                        default => 'Ditolak/Batal',
                                    };
                                @endphp
                                <span class="badge {{ $statusColor }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                    @endforeach
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
                    <th width="8%" class="text-center">Kamar</th>
                    <th width="8%" class="text-center">Lantai</th>
                    <th width="12%">Tipe Unit</th>
                    <th width="20%">Penghuni</th>
                    <th width="15%" class="text-center">Status Bayar</th>
                    <th width="12%" class="text-center">Status</th>
                    <th width="25%" class="text-center">Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportData['kamar'] as $room)
                    <tr>
                        <td class="text-center font-bold">{{ $room->room_number }}</td>
                        <td class="text-center">{{ $room->floor_number }}</td>
                        <td>{{ $room->roomType->name ?? '-' }}</td>
                        <td>
                            @if($room->occupants->count() > 0)
                                @foreach($room->occupants as $occupant)
                                    <div style="margin-bottom: 4px;">{{ $occupant->name }}</div>
                                @endforeach
                            @else
                                <span style="color:#999">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($room->occupants->count() > 0)
                                @foreach($room->occupants as $occupant)
                                    @php
                                        $payLabel = $occupant->payment_status_label;
                                        $payColor = match ($payLabel) {
                                            'Lancar' => 'badge-success',
                                            'Telat Bayar' => 'badge-danger',
                                            'Segera Habis' => 'badge-warning',
                                            default => 'badge-info' // fallback color like gray
                                        };
                                        if ($payLabel == '-')
                                            $payColor = 'badge-info'; // or generic style
                                    @endphp
                                    <div style="margin-bottom: 4px;">
                                        <span class="badge {{ $payColor }}">{{ $payLabel }}</span>
                                    </div>
                                @endforeach
                            @else
                                <span style="color:#999">-</span>
                            @endif
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
                        <td class="text-center">
                            Rp
                            {{ number_format($room->roomType->capacity > 1 ? ($room->rent_per_person ?? ($room->price_per_month / 2)) : ($room->price_per_month ?? 0), 0, ',', '.') }}
                            @if($room->roomType->capacity > 1)
                                <div style="font-size: 8pt; color: #666; margin-top: 2px;">/ org / bln</div>
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

    @elseif ($report->report_type === 'comprehensive_report')

        <div class="summary-box">
            <div class="summary-title">RINGKASAN INTEGRASI DATA</div>
            <table class="summary-grid">
                <tr>
                    <td class="summary-item">
                        <div class="summary-value" style="color: #6366f1;">{{ $reportData['total_active'] }}</div>
                        <div class="summary-label">Penyewa Aktif</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value" style="color: #2563eb;">{{ $reportData['room_stats']['occupied'] }}</div>
                        <div class="summary-label">Kamar Terisi</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value" style="color: #10b981;">{{ $reportData['room_stats']['available'] }}
                        </div>
                        <div class="summary-label">Kamar Tersedia</div>
                    </td>
                </tr>
            </table>
            <table class="summary-grid" style="margin-top: 10px;">
                <tr>
                    <td class="summary-item">
                        <div class="summary-value text-success">{{ $reportData['status_counts']['paid'] }}</div>
                        <div class="summary-label">Bayar Lancar</div>
                    </td>
                    <td class="summary-item">
                        <div class="summary-value text-danger">{{ $reportData['status_counts']['late'] }}</div>
                        <div class="summary-label">Telat Bayar</div>
                    </td>
                </tr>
            </table>
        </div>

        <h3 style="margin-top: 20px; font-size: 12pt; border-bottom: 2px solid #ccc; padding-bottom: 5px; color: #374151;">
            DETAIL INTEGRASI DATA</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="14%">Penyewa</th>
                    <th width="11%">Kontak</th>
                    <th width="12%">Kamar</th>
                    <th width="12%" class="text-center">Harga</th>
                    <th width="12%" class="text-center">Status Bayar</th>
                    <th width="14%" class="text-center">Periode Sewa</th>
                    <th width="13%" class="text-right">Bayar Terakhir</th>
                    <th width="12%" class="text-center">Bank</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportData['tenants'] as $tenant)
                    <tr>
                        <td>
                            <div style="font-weight: bold;">{{ $tenant->name }}</div>
                            <div style="font-size: 8pt; color: #666;">
                                <span
                                    class="{{ $tenant->activeRoom ? 'text-success' : 'text-danger' }}">{{ $tenant->activeRoom ? 'Aktif' : 'Tidak Aktif' }}</span>
                            </div>
                        </td>
                        <td>{{ $tenant->tenantProfile->phone ?? '-' }}</td>
                        <td>
                            @if($tenant->activeRoom)
                                <div>{{ $tenant->activeRoom->room_number ?? '-' }}</div>
                                <div style="font-size: 8pt; color: #666;">{{ $tenant->activeRoom->roomType->name ?? '-' }}</div>
                            @else
                                <span style="font-style:italic; color:#999">Tidak Ada</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $room = $tenant->activeRoom;
                                $price = $room ? ($room->roomType->capacity > 1
                                    ? ($room->rent_per_person ?? ($room->price_per_month / 2))
                                    : ($room->price_per_month ?? 0)) : 0;
                            @endphp
                            @if($price > 0)
                                Rp {{ number_format($price, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $payLabel = $tenant->payment_status_label;
                                $payColor = match ($payLabel) {
                                    'Lancar' => 'badge-success',
                                    'Telat Bayar' => 'badge-danger',
                                    'Segera Habis' => 'badge-warning',
                                    default => 'badge-info'
                                };
                            @endphp
                            <span class="badge {{ $payColor }}">{{ $payLabel }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $lastTrx = $tenant->tenantTransactions->where('status', 'verified_by_owner')->sortByDesc('period_end_date')->first();
                            @endphp
                            @if($lastTrx)
                                @php
                                    $firstRoom = $tenant->occupiedRoom->first();
                                    $checkInDate = $firstRoom && $firstRoom->pivot ? $firstRoom->pivot->check_in_date : null;
                                    $startDate = $checkInDate ? \Carbon\Carbon::parse($checkInDate) : $lastTrx->period_start_date;
                                @endphp
                                {{ $startDate ? $startDate->format('d/m/y') : '-' }} -
                                {{ $lastTrx->period_end_date ? $lastTrx->period_end_date->format('d/m/y') : '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($lastTrx)
                                <div>Rp {{ number_format($lastTrx->amount, 0, ',', '.') }}</div>
                                <div style="font-size: 8pt; color: #666;">{{ $lastTrx->created_at->format('d/m/Y H:i') }}</div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($lastTrx)
                                @if($lastTrx->sender_bank)
                                    <div>{{ $lastTrx->sender_bank }}</div>
                                    @if($lastTrx->sender_name)
                                        <div style="font-size: 8pt; color: #666;">a.n {{ $lastTrx->sender_name }}</div>
                                    @endif
                                @else
                                    {{ $lastTrx->payment_method ?? '-' }}
                                @endif
                            @else
                                -
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