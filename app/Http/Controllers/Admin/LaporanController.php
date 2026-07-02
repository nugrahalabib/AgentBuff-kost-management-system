<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ReportExportTrait;
use App\Models\Laporan;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
     * Display reports page
     */
    public function index(Request $request)
    {
        $status     = $request->get('status', null);
        $search     = $request->get('search', null);
        $reportType = $request->get('report_type', null);

        $query = Laporan::with(['owner', 'admin']);

        if ($status && $status !== 'semua') {
            if ($status === 'sent') {
                $query->whereIn('status', ['sent', 'viewed']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($reportType && $reportType !== 'all') {
            $query->where('report_type', $reportType);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);

        $monthlyReports = Laporan::whereMonth('created_at', today()->month)
            ->whereYear('created_at', today()->year)
            ->count();
        $pendingReports = Laporan::where('status', 'draft')->count();
        $totalArchived = Laporan::whereIn('status', ['sent', 'viewed'])->count();

        return view('admin.laporan', [
            'reports'   => $reports,
            'monthlyReports'  => $monthlyReports,
            'pendingReports' => $pendingReports,
            'totalArchived'      => $totalArchived,
            'selectedStatus'  => $status,
            'search'          => $search,
            'reportType'    => $reportType,
        ]);
    }

    /**
     * Show create report form
     */
    public function create()
    {
        $reportTypes = [
            'financial_report' => 'Laporan Keuangan & Transaksi',
            'room_status_report' => 'Laporan Status Kamar',
            'tenant_report' => 'Laporan Data Penyewa',
        ];

        return view('admin.laporan-create', [
            'reportTypes' => $reportTypes,
        ]);
    }

    /**
     * Generate new report
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:financial_report,room_status_report,tenant_report,comprehensive_report',
            'period_type' => 'nullable|in:monthly,range,annual',
            'month' => 'required_if:period_type,monthly,range|nullable|numeric|min:1|max:12',
            'year' => 'required|numeric|min:2020',
            'end_month' => 'required_if:period_type,range|nullable|numeric|min:1|max:12',
            'end_year' => 'required_if:period_type,range|nullable|numeric|min:2020',
        ]);

        $periodType = $validated['period_type'] ?? 'monthly';
        $startMonth = $validated['month'] ?? 1;
        $startYear = $validated['year'];
        $endMonth = $startMonth;
        $endYear = $startYear;

        if ($periodType === 'annual') {
            $startMonth = 1;
            $endMonth = 12;
            $endYear = $startYear;
        } elseif ($periodType === 'range') {
            $endMonth = $validated['end_month'];
            $endYear = $validated['end_year'];
        }

        $data = [
            'report_month' => $startMonth,
            'report_year' => $startYear,
            'end_month' => $endMonth,
            'end_year' => $endYear
        ];

        $admin = Auth::user();
        $owners = \App\Models\User::where('role', 'owner')->get();

        foreach ($owners as $owner) {
            $reportData = match ($validated['report_type']) {
                'financial_report' => $this->reportService->generateFinancialReport($owner, $data),
                'room_status_report' => $this->reportService->generateRoomStatusReport($owner, $data),
                'tenant_report' => $this->reportService->generateTenantReport($owner, $data),
                'comprehensive_report' => $this->reportService->generateComprehensiveReport($owner, $data),
                default => [],
            };

            $report = Laporan::create([
                'owner_id' => $owner->id,
                'admin_id' => $admin->id,
                'report_type' => $validated['report_type'],
                'report_month' => $startMonth,
                'report_year' => $startYear,
                'end_month' => $endMonth,
                'end_year' => $endYear,
                'title' => $this->getReportTitle($validated['report_type'], $startMonth, $startYear, $endMonth, $endYear),
                'status' => 'draft',
                'generated_at' => now(),
            ]);

            try {
                $this->generateReportFiles($report, $reportData, $validated['report_type']);
            } catch (\Exception $e) {
                Log::error('Report file generation failed: ' . $e->getMessage());
            }

            \App\Services\LoggerService::log(
                'create',
                'Membuat laporan ' . $validated['report_type'],
                $report
            );
        }

        return back()->with('success', 'Laporan berhasil dibuat! Silakan cek daftar di bawah.');
    }

    /**
     * Submit report to owner
     */
    public function submit(Request $request, Laporan $report)
    {
        if ($report->status !== 'draft') {
            return back()->with('error', 'Hanya laporan dalam status draft yang bisa dikirim');
        }

        $report->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_by' => Auth::id(),
        ]);

        Notification::create([
            'user_id' => $report->owner_id,
            'type' => 'report_submitted',
            'category' => 'info',
            'title' => 'Laporan Baru Diterima',
            'message' => "Laporan {$report->title} telah dikirim oleh admin.",
            'related_entity_type' => 'generated_report',
            'related_entity_id' => $report->id,
            'priority' => 'medium',
            'action_required' => false,
        ]);

        \App\Services\LoggerService::log(
            'update_status',
            'Mengirim laporan ke owner: ' . $report->title,
            $report
        );

        return back()->with('success', 'Laporan berhasil dikirim ke owner');
    }

    /**
     * Preview report as PDF
     */
    public function preview(Laporan $report)
    {
        if ($report->status === 'draft' && $report->admin_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $reportData = $this->getReportData($report);

        return view('admin.laporan-preview', [
            'report' => $report,
            'reportData' => $reportData,
        ]);
    }

    /**
     * Download report as PDF
     */
    public function downloadPdf(Laporan $report)
    {
        if ($report->status === 'draft' && $report->admin_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $reportData = $this->getReportData($report);

        $pdf = Pdf::loadView('admin.laporan-pdf', [
            'report' => $report,
            'reportData' => $reportData,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        \App\Services\LoggerService::log(
            'download',
            'Download PDF laporan: ' . $report->title,
            $report
        );

        return $pdf->download('Laporan_' . Str::slug($report->title) . '_' . date('YmdHis') . '.pdf');
    }

    /**
     * Download report as Excel
     */
    public function downloadExcel(Laporan $report)
    {
        if ($report->status === 'draft' && $report->admin_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $reportData = $this->getReportData($report);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', $report->title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $periodString = Carbon::createFromDate($report->report_year, $report->report_month)->format('F Y');
        if ($report->end_month && ($report->end_month != $report->report_month || $report->end_year != $report->report_year)) {
            $periodString .= ' - ' . Carbon::createFromDate($report->end_year, $report->end_month)->format('F Y');
        }
        $sheet->setCellValue('A2', 'Periode: ' . $periodString);

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

        \App\Services\LoggerService::log(
            'download',
            'Download Excel laporan: ' . $report->title,
            $report
        );

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
     * Delete report
     */
    public function destroy(Laporan $report)
    {
        if ($report->status !== 'draft' || Auth::user()->role !== 'admin') {
            abort(403);
        }

        \App\Services\LoggerService::log(
            'delete',
            'Menghapus draft laporan: ' . $report->title,
            $report
        );

        $report->delete();

        return back()->with('success', 'Laporan berhasil dihapus');
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
     * Generate actual PDF and Excel files
     */
    private function generateReportFiles(Laporan $report, $reportData, $reportType)
    {
        // Generate PDF
        try {
            $pdf = Pdf::loadView('admin.laporan-pdf', [
                'report' => $report,
                'reportData' => $reportData,
            ])
                ->setPaper('a4', 'portrait');

            $pdfPath = 'reports/' . date('Y/m') . '/';
            Storage::makeDirectory($pdfPath);

            $pdfFileName = 'laporan_' . $report->report_type . '_' . $report->report_year . '_' . $report->report_month . '.pdf';
            Storage::put($pdfPath . $pdfFileName, $pdf->output());

            $report->update(['file_path_pdf' => $pdfPath . $pdfFileName]);
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
        }

        // Generate Excel
        try {
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

            $xlsxPath = 'reports/' . date('Y/m') . '/';
            Storage::makeDirectory($xlsxPath);

            $xlsxFileName = 'laporan_' . $report->report_type . '_' . $report->report_year . '_' . $report->report_month . '.xlsx';

            $writer = new Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $excelContent = ob_get_clean();

            Storage::put($xlsxPath . $xlsxFileName, $excelContent);

            $report->update(['file_path_excel' => $xlsxPath . $xlsxFileName]);
        } catch (\Exception $e) {
            Log::error('Excel generation failed: ' . $e->getMessage());
        }
    }
}
