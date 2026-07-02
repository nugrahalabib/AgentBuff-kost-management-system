<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Arus Kas - {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</title>
    <style>
        @page { margin: 1cm 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1f2937; font-size: 10pt; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #333; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; font-weight: bold; }
        .header .subtitle { margin: 5px 0 0; font-size: 10pt; color: #555; }
        .summary-box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 12px; margin-bottom: 20px; }
        .summary-grid { width: 100%; border-collapse: collapse; }
        .summary-grid td { padding: 6px 10px; font-size: 10pt; }
        .summary-grid .label { font-weight: bold; width: 60%; }
        .summary-grid .value { text-align: right; }
        .positive { color: #059669; }
        .negative { color: #dc2626; }
        .section-title { font-size: 12pt; font-weight: bold; margin: 20px 0 8px; padding-bottom: 5px; border-bottom: 1px solid #d1d5db; }
        table.ledger { width: 100%; border-collapse: collapse; font-size: 9pt; margin-bottom: 15px; }
        table.ledger th { background: #f3f4f6; border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; font-weight: bold; }
        table.ledger td { border: 1px solid #e5e7eb; padding: 5px 8px; }
        table.ledger .amount { text-align: right; }
        .total-row td { font-weight: bold; background: #f9fafb; }
        .footer { margin-top: 30px; text-align: center; font-size: 8pt; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Arus Kas</h1>
        <p class="subtitle">Periode: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</p>
        <p class="subtitle">Pemilik: {{ $ownerName }}</p>
    </div>

    {{-- Summary --}}
    <div class="summary-box">
        <table class="summary-grid">
            <tr>
                <td class="label">Total Pemasukan</td>
                <td class="value positive">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Pengeluaran</td>
                <td class="value negative">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 2px solid #333;">
                <td class="label" style="font-size: 11pt;">Laba Bersih</td>
                <td class="value {{ ($totalIncome - $totalExpense) >= 0 ? 'positive' : 'negative' }}" style="font-size: 11pt; font-weight: bold;">
                    Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Income --}}
    <div class="section-title positive">Pemasukan</div>
    <table class="ledger">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th>Keterangan</th>
                <th style="width: 12%;">Kamar</th>
                <th style="width: 20%;">Penyewa</th>
                <th style="width: 18%;" class="amount">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($income as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->payment_date)->format('d/m/Y') }}</td>
                <td>Pemasukan Sewa</td>
                <td>{{ $item->room->room_number ?? '-' }}</td>
                <td>{{ $item->tenant->name ?? '-' }}</td>
                <td class="amount">Rp {{ number_format($item->final_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center; color: #9ca3af;">Tidak ada pemasukan</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Pemasukan</td>
                <td class="amount">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Expenses --}}
    <div class="section-title negative">Pengeluaran</div>
    <table class="ledger">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th>Keterangan</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 10%;">Tipe</th>
                <th style="width: 18%;" class="amount">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenses as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                <td>{{ $item->description ?: $item->category }}</td>
                <td>{{ $item->category }}</td>
                <td>{{ strtoupper($item->type) }}</td>
                <td class="amount">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            @endforelse
            @if ($maintenanceExpenses > 0)
            <tr>
                <td>-</td>
                <td>Biaya Maintenance (Selesai)</td>
                <td>Maintenance</td>
                <td>OPEX</td>
                <td class="amount">Rp {{ number_format($maintenanceExpenses, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if ($expenses->isEmpty() && $maintenanceExpenses <= 0)
            <tr><td colspan="5" style="text-align: center; color: #9ca3af;">Tidak ada pengeluaran</td></tr>
            @endif
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">Total Pengeluaran</td>
                <td class="amount">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->translatedFormat('d F Y H:i') }} &mdash; Laporan ini digenerate otomatis oleh sistem.
    </div>
</body>
</html>
