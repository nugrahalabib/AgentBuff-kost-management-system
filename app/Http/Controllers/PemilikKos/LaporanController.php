<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ReportExportTrait;
use App\Models\Laporan;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\Kamar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class LaporanController extends Controller
{
    use ReportExportTrait;

    protected $reportService;

    public function __construct(\App\Services\ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display reports sent by admin
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $owner = Auth::user();

        // Date Filter
        $currentMonth = $request->input('month', now()->month);
        $currentYear = $request->input('year', now()->year);

        // Admin Reports (all for this period, filtered client-side)
        $reportsQuery = Laporan::where('owner_id', $owner->id)
            ->where('status', '!=', 'draft')
            ->whereMonth('sent_at', $currentMonth)
            ->whereYear('sent_at', $currentYear);

        $reports = $reportsQuery->orderBy('sent_at', 'desc')->get();

        // Stats for existing Report Cards
        $monthlyReports = Laporan::where('owner_id', $owner->id)
            ->whereMonth('sent_at', $currentMonth)
            ->whereYear('sent_at', $currentYear)
            ->where('status', '!=', 'draft')
            ->count();

        $pendingReports = Laporan::where('owner_id', $owner->id)
            ->where('status', 'sent')
            ->count();

        $totalDownloaded = Laporan::where('owner_id', $owner->id)
            ->whereNotNull('downloaded_at')
            ->count();

        // Expense Management & Dashboard Metrics
        $manualExpenses = Pengeluaran::where('owner_id', $owner->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();

        $totalOpex = $manualExpenses->where('type', 'opex')->sum('amount');
        $totalCapex = $manualExpenses->where('type', 'capex')->sum('amount');

        // Total Revenue - Selected Month
        $totalRevenue = Transaksi::where('owner_id', $owner->id)
            ->whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->where('status', 'verified_by_owner')
            ->sum('final_amount');

        // Pending Revenue
        $pendingRevenue = Transaksi::where('owner_id', $owner->id)
            ->where('status', 'verified_by_admin')
            ->sum('amount');

        // Total Expenses
        $maintenanceExpenses = \App\Models\MaintenanceRequest::whereHas('room', function ($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'completed')
            ->sum('estimated_cost');

        $totalExpenses = $maintenanceExpenses + $totalOpex + $totalCapex;

        // Net Profit
        $netProfit = $totalRevenue - $totalExpenses;

        // Projected Next Month
        $rooms = Kamar::with('roomType')->where('owner_id', $owner->id)->get();
        $grossPotential = $rooms->reduce(function ($carry, $room) {
            $price = $room->price_per_month ?? $room->roomType->price_per_month ?? 0;
            return $carry + $price;
        }, 0);

        $projectedNext = max(0, $grossPotential - $totalExpenses);

        // Unified Ledger
        $ledger = $this->buildUnifiedLedger($request, $owner, $currentMonth, $currentYear);

        // Legacy calculations
        $grossRevenue = $totalRevenue;
        $opexTotal = $maintenanceExpenses + $totalOpex;
        $noiTotal = $netProfit;
        $noiPercentage = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        $roomRevenue = Transaksi::where('owner_id', $owner->id)
            ->whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->where('status', 'verified_by_owner')
            ->sum('final_amount');

        $otherRevenue = $grossRevenue - $roomRevenue;

        $financialSummary = $this->buildFinancialSummary(
            $grossRevenue,
            $roomRevenue,
            $otherRevenue,
            $opexTotal,
            $noiTotal,
            $noiPercentage,
            $maintenanceExpenses,
            $manualExpenses,
            $totalOpex,
            $totalCapex
        );

        return view('pemilik-kos.laporan', [
            'reports' => $reports,
            'stats' => [
                'monthly_reports' => $monthlyReports,
                'pending_reports' => $pendingReports,
                'total_downloaded' => $totalDownloaded,
            ],
            'dashboardMetrics' => [
                'totalRevenue' => $totalRevenue,
                'pendingRevenue' => $pendingRevenue,
                'totalExpenses' => $totalExpenses,
                'netProfit' => $netProfit,
                'projectedNext' => $projectedNext,
            ],
            'financialSummary' => $financialSummary,
            'transaksi' => $ledger,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Build unified ledger (income + expenses)
     */
    private function buildUnifiedLedger($request, $owner, $currentMonth, $currentYear)
    {
        // Income
        $incomeQuery = Transaksi::with(['tenant', 'room', 'paymentProofs'])
            ->where('owner_id', $owner->id)
            ->whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->where('status', 'verified_by_owner');

        $incomeTransactions = $incomeQuery->get()->map(function ($item) {
            $item->ledger_type = 'income';
            $item->ledger_date = $item->payment_date;
            $item->ledger_amount = $item->final_amount;
            $item->ledger_desc = "Pemasukan Sewa - " . ($item->room->room_number ?? 'Dihapus') . " (" . ($item->tenant->name ?? 'Mantan Penyewa') . ")";
            return $item;
        });

        // Expenses
        $expenseQuery = Pengeluaran::where('owner_id', $owner->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear);

        $expenseTransactions = $expenseQuery->get()->map(function ($item) {
            $item->ledger_type = 'expense';
            $item->ledger_date = $item->date;
            $item->ledger_amount = $item->amount;
            $item->ledger_desc = "Pengeluaran (" . strtoupper($item->type) . ") - " . $item->category . ($item->description ? " (" . $item->description . ")" : "");
            return $item;
        });

        // Merge and sort by date descending
        return $incomeTransactions->concat($expenseTransactions)->sortByDesc(function ($item) {
            return $item->ledger_date . $item->created_at;
        })->values();
    }

    /**
     * Build financial summary array
     */
    private function buildFinancialSummary($grossRevenue, $roomRevenue, $otherRevenue, $opexTotal, $noiTotal, $noiPercentage, $maintenanceExpenses, $manualExpenses, $totalOpex, $totalCapex)
    {
        return [
            'gross_revenue' => $grossRevenue,
            'room_revenue' => $roomRevenue,
            'other_revenue' => $otherRevenue,
            'opex_total' => $opexTotal,
            'noi_total' => $noiTotal,
            'noi_percentage' => $noiPercentage,
            'breakdown' => [
                'maintenance' => $maintenanceExpenses + $manualExpenses->where('category', 'Maintenance')->sum('amount'),
                'utilities' => $manualExpenses->where('category', 'Utilities')->sum('amount'),
                'salary' => $manualExpenses->where('category', 'Salary')->sum('amount'),
                'cleaning' => $manualExpenses->where('category', 'Cleaning')->sum('amount'),
                'marketing' => $manualExpenses->where('category', 'Marketing')->sum('amount'),
                'admin' => $manualExpenses->where('category', 'Admin')->sum('amount'),
                'opex_other' => $manualExpenses->where('category', 'Other')->sum('amount'),
                'capex_renovation' => $manualExpenses->where('category', 'Renovation')->sum('amount'),
                'capex_furniture' => $manualExpenses->where('category', 'Furniture')->sum('amount'),
                'capex_electronic' => $manualExpenses->where('category', 'Electronics')->sum('amount'),
                'capex_other' => $manualExpenses->where('category', 'CapexOther')->sum('amount'),
                'manual_opex' => $totalOpex,
                'manual_capex' => $totalCapex
            ]
        ];
    }

    /**
     * Store new expense
     */
    public function storeExpense(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:opex,capex',
            'category' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'proof_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('payment_proofs', 'public');
        }

        Pengeluaran::create([
            'owner_id' => Auth::id(),
            'type' => $request->type,
            'category' => $request->category,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
            'proof_image' => $proofPath,
        ]);

        return redirect()->back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    /**
     * Delete expense
     */
    public function destroyExpense(Pengeluaran $expense)
    {
        if ($expense->owner_id !== Auth::id()) {
            abort(403);
        }

        $expense->delete();

        return redirect()->back()->with('success', 'Pengeluaran berhasil dihapus.');
    }

    /**
     * Preview report
     */
    public function preview(Laporan $report)
    {
        if ($report->owner_id !== Auth::id() || $report->status === 'draft') {
            abort(403);
        }

        if ($report->status === 'sent') {
            $report->update(['status' => 'viewed', 'viewed_at' => now()]);
        }

        $reportData = $this->getReportData($report);

        return view('pemilik-kos.laporan-preview', [
            'report' => $report,
            'reportData' => $reportData,
        ]);
    }

    /**
     * Download report as PDF
     */
    public function downloadPdf(Laporan $report)
    {
        if ($report->owner_id !== Auth::id() || $report->status === 'draft') {
            abort(403);
        }

        $reportData = $this->getReportData($report);

        $pdf = Pdf::loadView('pemilik-kos.laporan-pdf', [
            'report' => $report,
            'reportData' => $reportData,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        $report->update(['downloaded_at' => now()]);

        return $pdf->download('Laporan_' . Str::slug($report->title) . '_' . date('YmdHis') . '.pdf');
    }

    /**
     * Download report as Excel
     */
    public function downloadExcel(Laporan $report)
    {
        if ($report->owner_id !== Auth::id() || $report->status === 'draft') {
            abort(403);
        }

        $reportData = $this->getReportData($report);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', $report->title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A2', 'Periode: ' . Carbon::createFromDate($report->report_year, $report->report_month)->format('F Y'));

        $row = 4;
        match ($report->report_type) {
            'financial_report' => $this->exportFinancialToExcel($sheet, $reportData, $row),
            'room_status_report' => $this->exportRoomStatusToExcel($sheet, $reportData, $row),
            'tenant_report' => $this->exportTenantToExcel($sheet, $reportData, $row),
            'comprehensive_report' => $this->exportComprehensiveToExcel($sheet, $reportData, $row),
        };

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $report->update(['downloaded_at' => now()]);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_' . Str::slug($report->title) . '_' . date('YmdHis') . '.xlsx';

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                "Content-Disposition" => "attachment; filename=\"$fileName\"",
            ]
        );
    }

    /**
     * Get report data by type
     */
    private function getReportData(Laporan $report)
    {
        $data = [
            'report_month' => $report->report_month,
            'report_year' => $report->report_year,
            'end_month' => $report->end_month,
            'end_year' => $report->end_year,
        ];

        return match ($report->report_type) {
            'financial_report' => $this->reportService->generateFinancialReport($report->owner, $data),
            'room_status_report' => $this->reportService->generateRoomStatusReport($report->owner, $data),
            'tenant_report' => $this->reportService->generateTenantReport($report->owner, $data),
            'comprehensive_report' => $this->reportService->generateComprehensiveReport($report->owner, $data),
            default => [],
        };
    }

    /**
     * Get cashflow data for export (income + expenses for a given month)
     */
    private function getCashflowData($owner, $month, $year)
    {
        $income = Transaksi::with(['tenant', 'room'])
            ->where('owner_id', $owner->id)
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->where('status', 'verified_by_owner')
            ->orderBy('payment_date')
            ->get();

        $expenses = Pengeluaran::where('owner_id', $owner->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $maintenanceExpenses = \App\Models\MaintenanceRequest::whereHas('room', function ($q) use ($owner) {
                $q->where('owner_id', $owner->id);
            })
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->sum('estimated_cost');

        $totalIncome = $income->sum('final_amount');
        $totalExpense = $expenses->sum('amount') + $maintenanceExpenses;

        return compact('income', 'expenses', 'maintenanceExpenses', 'totalIncome', 'totalExpense');
    }

    /**
     * Export cashflow as PDF
     */
    public function exportCashflowPdf(\Illuminate\Http\Request $request)
    {
        $owner = Auth::user();
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $data = $this->getCashflowData($owner, $month, $year);

        $pdf = Pdf::loadView('pemilik-kos.cashflow-pdf', array_merge($data, [
                'month' => $month,
                'year' => $year,
                'ownerName' => $owner->name,
            ]))
            ->setPaper('a4', 'portrait');

        $periodLabel = Carbon::createFromDate($year, $month, 1)->format('F_Y');
        return $pdf->download("Laporan_Arus_Kas_{$periodLabel}.pdf");
    }

    /**
     * Export cashflow as Excel
     */
    public function exportCashflowExcel(\Illuminate\Http\Request $request)
    {
        $owner = Auth::user();
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $data = $this->getCashflowData($owner, $month, $year);
        $periodLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Arus Kas');

        // Header
        $sheet->setCellValue('A1', 'LAPORAN ARUS KAS');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', "Periode: {$periodLabel}");
        $sheet->getStyle('A2')->getFont()->setSize(10);

        // --- INCOME ---
        $row = 4;
        $sheet->setCellValue('A' . $row, 'PEMASUKAN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;

        $headers = ['Tanggal', 'Keterangan', 'Kamar', 'Penyewa', 'Jumlah'];
        $cols = ['A', 'B', 'C', 'D', 'E'];
        foreach ($cols as $i => $col) {
            $sheet->setCellValue($col . $row, $headers[$i]);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
        }
        $row++;

        foreach ($data['income'] as $item) {
            $sheet->setCellValue('A' . $row, Carbon::parse($item->payment_date)->format('d/m/Y'));
            $sheet->setCellValue('B' . $row, 'Pemasukan Sewa');
            $sheet->setCellValue('C' . $row, $item->room->room_number ?? '-');
            $sheet->setCellValue('D' . $row, $item->tenant->name ?? '-');
            $sheet->setCellValue('E' . $row, $item->final_amount);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        $sheet->setCellValue('D' . $row, 'Total Pemasukan');
        $sheet->setCellValue('E' . $row, $data['totalIncome']);
        $sheet->getStyle('D' . $row . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // --- EXPENSES ---
        $row += 2;
        $sheet->setCellValue('A' . $row, 'PENGELUARAN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;

        $expHeaders = ['Tanggal', 'Keterangan', 'Kategori', 'Tipe', 'Jumlah'];
        foreach ($cols as $i => $col) {
            $sheet->setCellValue($col . $row, $expHeaders[$i]);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
        }
        $row++;

        foreach ($data['expenses'] as $item) {
            $sheet->setCellValue('A' . $row, Carbon::parse($item->date)->format('d/m/Y'));
            $sheet->setCellValue('B' . $row, $item->description ?: $item->category);
            $sheet->setCellValue('C' . $row, $item->category);
            $sheet->setCellValue('D' . $row, strtoupper($item->type));
            $sheet->setCellValue('E' . $row, $item->amount);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        if ($data['maintenanceExpenses'] > 0) {
            $sheet->setCellValue('A' . $row, '-');
            $sheet->setCellValue('B' . $row, 'Biaya Maintenance (Selesai)');
            $sheet->setCellValue('C' . $row, 'Maintenance');
            $sheet->setCellValue('D' . $row, 'OPEX');
            $sheet->setCellValue('E' . $row, $data['maintenanceExpenses']);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        $sheet->setCellValue('D' . $row, 'Total Pengeluaran');
        $sheet->setCellValue('E' . $row, $data['totalExpense']);
        $sheet->getStyle('D' . $row . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // --- NET ---
        $row += 2;
        $sheet->setCellValue('D' . $row, 'LABA BERSIH');
        $sheet->setCellValue('E' . $row, $data['totalIncome'] - $data['totalExpense']);
        $sheet->getStyle('D' . $row . ':E' . $row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = "Laporan_Arus_Kas_{$periodLabel}.xlsx";

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ]
        );
    }
}
