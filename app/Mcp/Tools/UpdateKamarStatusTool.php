<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\AuditsMcpActions;
use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\Kamar;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Ubah status sebuah kamar milik kos ini menjadi available, occupied, atau maintenance.')]
class UpdateKamarStatusTool extends Tool
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
            'status' => ['required', 'in:available,occupied,maintenance'],
        ]);

        $room = Kamar::where('owner_id', $ownerId)->find($validated['kamar_id']);
        if (! $room) {
            return Response::error('Kamar tidak ditemukan di kos Anda.');
        }

        $oldStatus = $room->status;
        $room->update(['status' => $validated['status']]);

        $this->logMcp(
            $request,
            'update_status',
            "Update status kamar {$room->room_number}",
            $room,
            ['status' => $oldStatus],
            ['status' => $validated['status']]
        );
        $this->notifyOwnerMcp(
            $ownerId,
            'Status Kamar Diubah (AI Agent)',
            "Kamar {$room->room_number}: {$oldStatus} → {$validated['status']} via AI agent.",
            'room',
            $room->id
        );
        $this->notifyAdminsMcp(
            $ownerId,
            'Status Kamar Diubah (AI Agent)',
            "Kamar {$room->room_number}: {$oldStatus} → {$validated['status']} via AI agent.",
            'room',
            $room->id
        );

        return Response::json([
            'success' => true,
            'message' => "Status kamar {$room->room_number} diubah menjadi {$validated['status']}.",
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'kamar_id' => $schema->integer()->description('ID kamar yang akan diubah.')->required(),
            'status' => $schema->string()->description('Status baru: available, occupied, atau maintenance.')->enum(['available', 'occupied', 'maintenance'])->required(),
        ];
    }
}
