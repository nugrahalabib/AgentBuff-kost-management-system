<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\AuditsMcpActions;
use App\Mcp\Concerns\InteractsWithOwner;
use App\Services\ReportService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Hasilkan data laporan kos untuk periode tertentu. Jenis: financial_report (keuangan), room_status_report (status kamar), tenant_report (penyewa).')]
class GenerateLaporanTool extends Tool
{
    use InteractsWithOwner, AuditsMcpActions;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $validated = $request->validate([
            'report_type' => ['required', 'in:financial_report,room_status_report,tenant_report'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year' => ['nullable', 'integer', 'min:2020', 'max:2100'],
        ]);

        $owner = $request->user()->resolveOwner();
        if (! $owner) {
            return Response::error('Owner tidak ditemukan.');
        }

        $month = $validated['month'] ?? (int) now()->month;
        $year = $validated['year'] ?? (int) now()->year;
        $data = ['report_month' => $month, 'report_year' => $year, 'end_month' => $month, 'end_year' => $year];

        $service = app(ReportService::class);
        $reportData = match ($validated['report_type']) {
            'financial_report' => $service->generateFinancialReport($owner, $data),
            'room_status_report' => $service->generateRoomStatusReport($owner, $data),
            'tenant_report' => $service->generateTenantReport($owner, $data),
        };

        $periode = sprintf('%02d-%d', $month, $year);
        $this->logMcp($request, 'generate_report', "Generate laporan {$validated['report_type']} {$periode}");
        $this->notifyOwnerMcp(
            $ownerId,
            'Laporan Dihasilkan (AI Agent)',
            "Laporan {$validated['report_type']} periode {$periode} dihasilkan via AI agent.",
            null,
            null,
            'info',
            'system',
            'low'
        );
        // Admin: hanya laporan operasional (bukan keuangan).
        if ($validated['report_type'] !== 'financial_report') {
            $this->notifyAdminsMcp(
                $ownerId,
                'Laporan Dihasilkan (AI Agent)',
                "Laporan {$validated['report_type']} periode {$periode} dihasilkan via AI agent."
            );
        }

        return Response::json([
            'jenis' => $validated['report_type'],
            'periode' => $periode,
            'data' => $reportData,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'report_type' => $schema->string()->description('financial_report, room_status_report, atau tenant_report.')->enum(['financial_report', 'room_status_report', 'tenant_report'])->required(),
            'month' => $schema->integer()->description('Bulan (1-12). Default bulan ini.'),
            'year' => $schema->integer()->description('Tahun. Default tahun ini.'),
        ];
    }
}
