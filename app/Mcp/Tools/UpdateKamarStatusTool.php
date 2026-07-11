<?php

namespace App\Mcp\Tools;

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
    use InteractsWithOwner;

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

        $room->update(['status' => $validated['status']]);

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
