<?php

namespace App\Http\Controllers\Traits;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

/**
 * Trait for Excel export functionality in Report Controllers
 * Shared between Admin and Owner ReportController
 */
trait ReportExportTrait
{
    /**
     * Export financial report to Excel
     */
    protected function exportFinancialToExcel($sheet, $reportData, $row)
    {
        // Summary Section (Status Counts only)
        $sheet->setCellValue('A' . $row, 'Verified (Selesai)');
        $sheet->setCellValue('B' . $row, $reportData['verified_owner_count']);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A' . $row, 'Ditolak / Batal');
        $sheet->setCellValue('B' . $row, $reportData['rejected_count']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Menunggu Verifikasi');
        $sheet->setCellValue('B' . $row, $reportData['pending_count'] + $reportData['verified_admin_count']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Total Transaksi');
        $sheet->setCellValue('B' . $row, $reportData['transaction_count']);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        // Transaction Table
        $row += 2;
        $sheet->setCellValue('A' . $row, 'DAFTAR STATUS TRANSAKSI');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;

        $headerRow = $row;
        $headers = ['Tanggal', 'Ref', 'Penyewa', 'Kamar', 'Periode Sewa', 'Metode', 'Status'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach ($cols as $idx => $col) {
            $sheet->setCellValue($col . $row, $headers[$idx]);
        }
        $row++;

        foreach ($reportData['transactions'] as $transaction) {
            $sheet->setCellValue('A' . $row, $transaction->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('B' . $row, $transaction->reference_number ?? '-');
            $sheet->setCellValue('C' . $row, $transaction->tenant->name ?? 'Deleted User');
            $sheet->setCellValue('D' . $row, $transaction->room->room_number ?? '-');

            $tenant = $transaction->tenant;
            if ($tenant) {
                $roomId = $transaction->kamar_id;
                $firstRoom = $tenant->occupiedRoom->where('id', $roomId)->first();
                $checkInDate = $firstRoom ? $firstRoom->pivot->check_in_date : null;

                $startDate = $checkInDate ? Carbon::parse($checkInDate) : $transaction->period_start_date;
                $endDate = $transaction->period_end_date;

                if ($startDate && $endDate) {
                    $period = $startDate->format('d/m/y') . ' - ' . $endDate->format('d/m/y');
                    $sheet->setCellValue('E' . $row, $period);
                } else {
                    $sheet->setCellValue('E' . $row, '-');
                }
            } else {
                $sheet->setCellValue('E' . $row, '-');
            }

            $sheet->setCellValue('F' . $row, $transaction->payment_method ?? 'Manual');

            $status = ucfirst(str_replace('_', ' ', $transaction->status));
            $sheet->setCellValue('G' . $row, $status);

            $row++;
        }
        $lastRow = $row - 1;

        $this->applyTableStyles($sheet, $headerRow, $lastRow, $cols);

        // Center alignment for specific columns
        $sheet->getStyle('A' . $headerRow . ':A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $headerRow . ':F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    /**
     * Export room status report to Excel
     */
    protected function exportRoomStatusToExcel($sheet, $reportData, $row)
    {
        $sheet->setCellValue('A' . $row, 'Total Kamar');
        $sheet->setCellValue('B' . $row, $reportData['total_rooms']);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A' . $row, 'Terisi');
        $sheet->setCellValue('B' . $row, $reportData['occupied']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Tersedia');
        $sheet->setCellValue('B' . $row, $reportData['available']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Maintenance');
        $sheet->setCellValue('B' . $row, $reportData['maintenance']);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'DAFTAR UNIT KAMAR');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;

        $headerRow = $row;
        $headers = ['No. Kamar', 'Lantai', 'Tipe', 'Penghuni', 'Status Bayar', 'Status', 'Harga'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach ($cols as $idx => $col) {
            $sheet->setCellValue($col . $row, $headers[$idx]);
        }
        $row++;

        foreach ($reportData['kamar'] as $room) {
            $sheet->setCellValue('A' . $row, $room->room_number);
            $sheet->setCellValue('B' . $row, $room->floor_number);
            $sheet->setCellValue('C' . $row, $room->roomType->name ?? '-');

            // Penghuni (Tenant)
            $tenantNames = [];
            if ($room->occupants->isNotEmpty()) {
                foreach ($room->occupants as $occupant) {
                    $tenantNames[] = $occupant->name;
                }
            } else {
                $tenantNames[] = '-';
            }
            $sheet->setCellValue('D' . $row, implode("\n", $tenantNames));
            $sheet->getStyle('D' . $row)->getAlignment()->setWrapText(true);

            // Payment Status
            $payStatus = [];
            if ($room->occupants->isNotEmpty()) {
                foreach ($room->occupants as $occupant) {
                    $payStatus[] = $occupant->payment_status_label;
                }
            } else {
                $payStatus[] = '-';
            }
            $sheet->setCellValue('E' . $row, implode("\n", $payStatus));
            $sheet->getStyle('E' . $row)->getAlignment()->setWrapText(true);

            $sheet->setCellValue('F' . $row, ucfirst($room->status));

            // Harga (Price)
            $price = $room->roomType->capacity > 1
                ? ($room->rent_per_person ?? ($room->price_per_month / 2))
                : ($room->price_per_month ?? 0);

            $sheet->setCellValue('G' . $row, $price);
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }
        $lastRow = $row - 1;

        $this->applyTableStyles($sheet, $headerRow, $lastRow, $cols);

        // Center alignment for specific columns
        $sheet->getStyle('A' . $headerRow . ':B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $headerRow . ':G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    /**
     * Export tenant report to Excel
     */
    protected function exportTenantToExcel($sheet, $reportData, $row)
    {
        $sheet->setCellValue('A' . $row, 'Total Penyewa');
        $sheet->setCellValue('B' . $row, $reportData['tenant_count']);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A' . $row, 'Penyewa Aktif');
        $sheet->setCellValue('B' . $row, $reportData['active_tenants']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Tidak Aktif');
        $sheet->setCellValue('B' . $row, $reportData['inactive_tenants']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Akan Berakhir (30 hari)');
        $sheet->setCellValue('B' . $row, $reportData['expiring_soon']);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'DAFTAR PENYEWA');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;

        $headerRow = $row;
        $headers = ['Nama', 'Telepon', 'Kamar', 'Status Bayar', 'Periode Sewa', 'Status Akun'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($cols as $idx => $col) {
            $sheet->setCellValue($col . $row, $headers[$idx]);
        }
        $row++;

        foreach ($reportData['tenants'] as $tenant) {
            $sheet->setCellValue('A' . $row, $tenant->name);
            $sheet->setCellValue('B' . $row, $tenant->tenantProfile->phone ?? '-');
            $sheet->setCellValue('C' . $row, $tenant->sort_room_number ?: '-');
            $sheet->setCellValue('D' . $row, $tenant->payment_status_label);

            // Periode Sewa Logic
            $lastTrx = $tenant->tenantTransactions->where('status', 'verified_by_owner')->sortByDesc('period_end_date')->first();

            if ($lastTrx) {
                $checkInDate = $tenant->activeRoom ? $tenant->activeRoom->pivot->check_in_date : null;
                $startDate = $checkInDate ? Carbon::parse($checkInDate) : $lastTrx->period_start_date;
                $endDate = $lastTrx->period_end_date;
                if ($startDate && $endDate) {
                    $period = $startDate->format('d/m/y') . ' - ' . $endDate->format('d/m/y');
                } else {
                    $period = '-';
                }
            } else {
                $period = '-';
            }
            $sheet->setCellValue('E' . $row, $period);

            $sheet->setCellValue('F' . $row, $tenant->account_status_label);
            $row++;
        }
        $lastRow = $row - 1;

        $this->applyTableStyles($sheet, $headerRow, $lastRow, $cols);

        // Center specific columns
        $sheet->getStyle('C' . $headerRow . ':E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    /**
     * Export comprehensive report to Excel
     */
    protected function exportComprehensiveToExcel($sheet, $reportData, $row)
    {
        $sheet->setCellValue('A' . $row, 'Total Penyewa Aktif');
        $sheet->setCellValue('B' . $row, $reportData['total_active']);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A' . $row, 'Kamar Terisi');
        $sheet->setCellValue('B' . $row, $reportData['room_stats']['occupied']);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A' . $row, 'Kamar Tersedia');
        $sheet->setCellValue('B' . $row, $reportData['room_stats']['available']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Pembayaran Lancar');
        $sheet->setCellValue('B' . $row, $reportData['status_counts']['paid']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Telat Bayar');
        $sheet->setCellValue('B' . $row, $reportData['status_counts']['late']);
        $row++;

        $row++; // Spacer

        $headerRow = $row;
        $headers = ['Nama Penyewa', 'Kontak', 'Kamar', 'Tipe', 'Harga', 'Status Bayar', 'Periode Sewa', 'Terakhir Bayar', 'Bank Pengirim'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        foreach ($cols as $idx => $col) {
            $sheet->setCellValue($col . $row, $headers[$idx]);
        }
        $row++;

        foreach ($reportData['tenants'] as $tenant) {
            $sheet->setCellValue('A' . $row, $tenant->name);
            $sheet->setCellValue('B' . $row, $tenant->tenantProfile->phone ?? '-');

            $room = $tenant->activeRoom;
            $sheet->setCellValue('C' . $row, $room ? ($room->room_number ?? '-') : 'Tidak Ada');
            $sheet->setCellValue('D' . $row, $room ? ($room->roomType->name ?? '-') : '-');

            $price = $room ? ($room->roomType->capacity > 1
                ? ($room->rent_per_person ?? ($room->price_per_month / 2))
                : ($room->price_per_month ?? 0)) : 0;

            $sheet->setCellValue('E' . $row, $price);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->setCellValue('F' . $row, $tenant->payment_status_label);

            $lastTrx = $tenant->tenantTransactions->where('status', 'verified_by_owner')->sortByDesc('period_end_date')->first();

            if ($lastTrx) {
                $checkInDate = $tenant->activeRoom ? $tenant->activeRoom->pivot->check_in_date : null;
                $startDate = $checkInDate ? Carbon::parse($checkInDate) : $lastTrx->period_start_date;
                $endDate = $lastTrx->period_end_date;

                if ($startDate && $endDate) {
                    $period = $startDate->format('d/m/y') . ' - ' . $endDate->format('d/m/y');
                } else {
                    $period = '-';
                }
                $sheet->setCellValue('G' . $row, $period);

                $lastPay = 'Rp ' . number_format($lastTrx->amount, 0, ',', '.') . ' (' . $lastTrx->created_at->format('d/m/y H:i') . ')';
                $sheet->setCellValue('H' . $row, $lastPay);

                $bank = $lastTrx->sender_bank ?? $lastTrx->payment_method ?? '-';
                if ($lastTrx->sender_name) {
                    $bank .= ' (a.n ' . $lastTrx->sender_name . ')';
                }
                $sheet->setCellValue('I' . $row, $bank);
            } else {
                $sheet->setCellValue('G' . $row, '-');
                $sheet->setCellValue('H' . $row, '-');
                $sheet->setCellValue('I' . $row, '-');
            }

            $row++;
        }
        $lastRow = $row - 1;

        $this->applyTableStyles($sheet, $headerRow, $lastRow, $cols);

        // Center columns: C, E, F, G, H, I
        foreach (['C', 'E', 'F', 'G', 'H', 'I'] as $c) {
            $sheet->getStyle($c . $headerRow . ':' . $c . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    /**
     * Apply common table styles
     */
    private function applyTableStyles($sheet, $headerRow, $lastRow, $cols)
    {
        $lastCol = end($cols);
        $tableRange = 'A' . $headerRow . ':' . $lastCol . $lastRow;
        $headerRange = 'A' . $headerRow . ':' . $lastCol . $headerRow;

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF10B981'], // Emerald Green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    /**
     * Get report title based on type and period
     */
    protected function getReportTitle($type, $month, $year, $endMonth = null, $endYear = null)
    {
        $monthName = date('F', mktime(0, 0, 0, $month, 1));
        $period = "$monthName $year";

        if ($endMonth && ($endMonth != $month || $endYear != $year)) {
            $endMonthName = date('F', mktime(0, 0, 0, $endMonth, 1));
            $period = "$monthName $year - $endMonthName $endYear";
            if ($year == $endYear) {
                $period = "$monthName - $endMonthName $year";
            }
        }

        $titles = [
            'financial_report' => "Laporan Keuangan & Transaksi $period",
            'room_status_report' => "Laporan Status Kamar $period",
            'tenant_report' => "Laporan Data Penyewa $period",
            'comprehensive_report' => "Laporan Gabungan (Integrasi) $period",
        ];

        return $titles[$type] ?? 'Laporan';
    }
}
