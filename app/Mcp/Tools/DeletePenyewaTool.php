<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\AuditsMcpActions;
use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\User;
use App\Services\TenantDeletionService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Hapus permanen data penyewa beserta transaksi & dokumennya. Wajib alasan (min 5 karakter). Gagal bila penyewa masih menempati kamar aktif — checkout dulu. Butuh penyewa_id (user id dari list-penyewa) + reason.')]
class DeletePenyewaTool extends Tool
{
    use InteractsWithOwner, AuditsMcpActions;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $actor = $request->user();
        if (! $actor) {
            return Response::error('Token tidak valid.');
        }

        $validated = $request->validate([
            'penyewa_id' => ['required', 'integer'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $tenant = User::where('role', 'tenant')
            ->whereHas('tenantProfile', fn ($q) => $q->where('owner_id', $ownerId))
            ->find($validated['penyewa_id']);

        if (! $tenant) {
            return Response::error('Penyewa tidak ditemukan di kos Anda.');
        }

        if ($tenant->occupiedRoom()->exists()) {
            return Response::error(
                "Penyewa {$tenant->name} masih menempati kamar aktif. Checkout dulu sebelum menghapus."
            );
        }

        $name = $tenant->name;
        $tenantId = $tenant->id;

        app(TenantDeletionService::class)->delete(
            $tenant,
            $validated['reason'],
            $actor,
            $ownerId
        );

        // Audit log sudah ditulis TenantDeletionService.
        // Owner: notifikasi AI Agent (skip bila aktor admin — service sudah notifikasi owner).
        if ($actor->role !== 'admin') {
            $this->notifyOwnerMcp(
                $ownerId,
                'Penyewa Dihapus (AI Agent)',
                "Penyewa \"{$name}\" dihapus via AI agent. Alasan: {$validated['reason']}",
                'tenant',
                $tenantId,
                'info',
                'system',
                'high'
            );
        }
        $this->notifyAdminsMcp(
            $ownerId,
            'Penyewa Dihapus (AI Agent)',
            "Penyewa \"{$name}\" dihapus via AI agent. Alasan: {$validated['reason']}",
            'tenant',
            $tenantId
        );

        return Response::json([
            'success' => true,
            'message' => "Penyewa {$name} berhasil dihapus permanen.",
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'penyewa_id' => $schema->integer()->description('ID penyewa (user id, dari list-penyewa).')->required(),
            'reason' => $schema->string()->description('Alasan penghapusan (wajib, min 5 karakter).')->required(),
        ];
    }
}
