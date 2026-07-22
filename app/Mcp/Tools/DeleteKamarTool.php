<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\AuditsMcpActions;
use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\Kamar;
use App\Models\Transaksi;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Hapus kamar kosong milik kos ini. Gagal bila kamar masih ditempati penghuni aktif. Butuh kamar_id (lihat list-kamar).')]
class DeleteKamarTool extends Tool
{
    use InteractsWithOwner, AuditsMcpActions;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $validated = $request->validate([
            'kamar_id' => ['required', 'integer'],
        ]);

        $room = Kamar::where('owner_id', $ownerId)->find($validated['kamar_id']);
        if (! $room) {
            return Response::error('Kamar tidak ditemukan di kos Anda.');
        }

        if ($room->status === 'occupied' && $room->occupants()->exists()) {
            return Response::error(
                "Kamar {$room->room_number} masih ditempati. Kosongkan penghuni (checkout) dulu sebelum menghapus."
            );
        }

        $roomNumber = $room->room_number;
        $roomId = $room->id;

        Transaksi::where('kamar_id', $room->id)->update(['kamar_id' => null]);
        $room->delete();

        $this->logMcp($request, 'delete', "Hapus kamar {$roomNumber}", null);
        $this->notifyOwnerMcp(
            $ownerId,
            'Kamar Dihapus (AI Agent)',
            "Kamar {$roomNumber} dihapus via AI agent.",
            'room',
            $roomId
        );
        $this->notifyAdminsMcp(
            $ownerId,
            'Kamar Dihapus (AI Agent)',
            "Kamar {$roomNumber} dihapus via AI agent.",
            'room',
            $roomId
        );

        return Response::json([
            'success' => true,
            'message' => "Kamar {$roomNumber} berhasil dihapus.",
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'kamar_id' => $schema->integer()->description('ID kamar yang akan dihapus (dari list-kamar).')->required(),
        ];
    }
}
