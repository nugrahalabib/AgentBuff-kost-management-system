<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\AuditsMcpActions;
use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\TipeKamar;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Hapus tipe kamar yang tidak dipakai kamar mana pun. Gagal bila masih ada kamar dengan tipe ini. Butuh tipe_kamar_id (dari list-tipe-kamar).')]
class DeleteTipeKamarTool extends Tool
{
    use InteractsWithOwner, AuditsMcpActions;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $validated = $request->validate([
            'tipe_kamar_id' => ['required', 'integer'],
        ]);

        $type = TipeKamar::where('owner_id', $ownerId)->find($validated['tipe_kamar_id']);
        if (! $type) {
            return Response::error('Tipe kamar tidak ditemukan di kos Anda.');
        }

        if ($type->rooms()->exists()) {
            return Response::error(
                "Tipe \"{$type->name}\" masih dipakai kamar. Hapus/ubah kamar terkait dulu."
            );
        }

        $name = $type->name;
        $typeId = $type->id;
        $oldData = [
            'name' => $name,
            'price_per_month' => $type->price_per_month,
            'status' => $type->status,
        ];

        $type->delete();

        $this->logMcp(
            $request,
            'delete_room_type',
            "Hapus tipe kamar: {$name}",
            null,
            $oldData
        );
        $this->notifyOwnerMcp(
            $ownerId,
            'Tipe Kamar Dihapus (AI Agent)',
            "Tipe kamar \"{$name}\" dihapus via AI agent.",
            'room_type',
            $typeId
        );
        $this->notifyAdminsMcp(
            $ownerId,
            'Tipe Kamar Dihapus (AI Agent)',
            "Tipe kamar \"{$name}\" dihapus via AI agent.",
            'room_type',
            $typeId
        );

        return Response::json([
            'success' => true,
            'message' => "Tipe kamar '{$name}' berhasil dihapus.",
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'tipe_kamar_id' => $schema->integer()->description('ID tipe kamar (dari list-tipe-kamar).')->required(),
        ];
    }
}
