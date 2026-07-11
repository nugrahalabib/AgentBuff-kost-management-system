<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\Kamar;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Ringkasan kos: jumlah kamar (total/terisi/kosong/perbaikan), jumlah penyewa, dan pemasukan terverifikasi bulan ini.')]
class DashboardSummaryTool extends Tool
{
    use InteractsWithOwner;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        // Occupancy uses Kamar::scopeOccupied()/scopeVacant() (pivot OR legacy
        // current_tenant_id) so these counts agree with list-kamar and the rest of
        // the app. Occupied takes precedence over maintenance, so the three counts
        // form a clean partition that sums to the total.
        $totalRooms = Kamar::where('owner_id', $ownerId)->count();
        $occupied = Kamar::where('owner_id', $ownerId)->occupied()->count();
        $maintenance = Kamar::where('owner_id', $ownerId)->where('status', 'maintenance')->vacant()->count();
        $available = Kamar::where('owner_id', $ownerId)->where('status', '!=', 'maintenance')->vacant()->count();

        $tenants = User::where('role', 'tenant')
            ->whereHas('tenantProfile', fn ($q) => $q->where('owner_id', $ownerId))
            ->count();

        $incomeThisMonth = Transaksi::where('owner_id', $ownerId)
            ->where('status', 'verified_by_owner')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('final_amount');

        return Response::json([
            'kamar' => [
                'total' => $totalRooms,
                'terisi' => $occupied,
                'kosong' => $available,
                'perbaikan' => $maintenance,
            ],
            'jumlah_penyewa' => $tenants,
            'pemasukan_bulan_ini' => (float) $incomeThisMonth,
            'bulan' => now()->translatedFormat('F Y'),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
