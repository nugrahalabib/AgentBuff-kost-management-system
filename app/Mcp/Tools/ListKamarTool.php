<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\Kamar;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Daftar kamar milik kos ini. Opsional filter status: available, occupied, maintenance.')]
class ListKamarTool extends Tool
{
    use InteractsWithOwner;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $query = Kamar::where('owner_id', $ownerId)->with('roomType')->orderBy('room_number');

        $status = $request->get('status');
        if (in_array($status, ['available', 'occupied', 'maintenance'], true)) {
            if ($status === 'occupied') {
                $query->has('occupants');
            } elseif ($status === 'available') {
                $query->doesntHave('occupants')->where('status', '!=', 'maintenance');
            } else {
                $query->where('status', 'maintenance');
            }
        }

        $rooms = $query->get()->map(fn ($k) => [
            'id' => $k->id,
            'nomor_kamar' => $k->room_number,
            'lantai' => $k->floor_number,
            'tipe' => $k->roomType->name ?? null,
            'harga_per_bulan' => (float) $k->price_per_month,
            'status' => $k->occupants()->exists() ? 'occupied' : $k->status,
        ]);

        return Response::json(['jumlah' => $rooms->count(), 'kamar' => $rooms]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()
                ->description('Filter status kamar: available, occupied, atau maintenance.')
                ->enum(['available', 'occupied', 'maintenance']),
        ];
    }
}
