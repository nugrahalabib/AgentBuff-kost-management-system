<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\AuditsMcpActions;
use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Tambah kamar baru ke kos ini. Butuh nomor kamar (unik), lantai, tipe_kamar_id (lihat list-tipe-kamar), dan status awal (available/maintenance).')]
class CreateKamarTool extends Tool
{
    use InteractsWithOwner, AuditsMcpActions;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $validated = $request->validate([
            'room_number' => ['required', 'integer', 'min:1', Rule::unique('kamar', 'room_number')->where('owner_id', $ownerId)],
            'floor_number' => ['required', 'integer', 'min:1', 'max:4'],
            'tipe_kamar_id' => ['required', Rule::exists('tipe_kamar', 'id')->where('owner_id', $ownerId)],
            'status' => ['nullable', 'in:available,maintenance'],
        ]);

        $type = TipeKamar::where('owner_id', $ownerId)->findOrFail($validated['tipe_kamar_id']);

        $room = Kamar::create([
            'owner_id' => $ownerId,
            'tipe_kamar_id' => $type->id,
            'room_number' => $validated['room_number'],
            'floor_number' => $validated['floor_number'],
            'status' => $validated['status'] ?? 'available',
            'price_per_month' => $type->price_per_month,
        ]);

        $this->logMcp($request, 'create', "Tambah kamar {$room->room_number}", $room);
        $this->notifyOwnerMcp(
            $ownerId,
            'Kamar Baru (AI Agent)',
            "Kamar {$room->room_number} ditambahkan via AI agent.",
            'room',
            $room->id
        );
        $this->notifyAdminsMcp(
            $ownerId,
            'Kamar Baru (AI Agent)',
            "Kamar {$room->room_number} ditambahkan via AI agent.",
            'room',
            $room->id
        );

        return Response::json([
            'success' => true,
            'message' => "Kamar {$room->room_number} berhasil ditambahkan.",
            'kamar' => ['id' => $room->id, 'nomor_kamar' => $room->room_number, 'status' => $room->status],
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'room_number' => $schema->integer()->description('Nomor kamar (mis. 101). Harus unik di kos ini.')->required(),
            'floor_number' => $schema->integer()->description('Lantai kamar (1-4).')->required(),
            'tipe_kamar_id' => $schema->integer()->description('ID tipe kamar (dari tool list-tipe-kamar).')->required(),
            'status' => $schema->string()->description('Status awal: available atau maintenance. Default available.')->enum(['available', 'maintenance']),
        ];
    }
}
