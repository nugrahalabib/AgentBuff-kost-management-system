<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Audit Log - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #065f46;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #065f46;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-gray { background-color: #f3f4f6; color: #4b5563; }
        .badge-yellow { background-color: #fef3c7; color: #92400e; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Audit Trail Log</h1>
        <p><strong>KosAdmin - {{ $pemilik->name }}</strong></p>
        <p>Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
        <p>Generated: {{ now()->format('d M Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Waktu</th>
                <th style="width: 20%;">Aktor</th>
                <th style="width: 15%;">Aktivitas</th>
                <th style="width: 35%;">Detail</th>
                <th style="width: 15%;">IP Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activityLogs as $log)
            <tr>
                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                <td>{{ $log->admin->name }}</td>
                <td>
                    @php
                        $badgeClass = match($log->activity_type) {
                            'input_transaksi', 'input' => 'badge-blue',
                            'login' => 'badge-gray',
                            'update_status', 'update' => 'badge-yellow',
                            'hapus_data', 'delete' => 'badge-red',
                            default => 'badge-gray'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $log->activity_label }}</span>
                </td>
                <td>{{ $log->notes }}</td>
                <td>{{ $log->ip_address }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; color: #999;">
                    Tidak ada aktivitas dalam periode ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Total Aktivitas:</strong> {{ $activityLogs->count() }} | <strong>Document ID:</strong> AUDIT-{{ now()->format('YmdHis') }}</p>
        <p>Dokumen ini digenerate secara otomatis oleh sistem KosAdmin</p>
    </div>
</body>
</html>
