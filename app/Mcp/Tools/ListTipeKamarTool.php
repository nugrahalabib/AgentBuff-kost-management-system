<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\TipeKamar;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Daftar tipe kamar (beserta id, harga, kapasitas) milik kos ini. Dipakai saat menambah kamar.')]
class ListTipeKamarTool extends Tool
{
    use InteractsWithOwner;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $types = TipeKamar::where('owner_id', $ownerId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'nama' => $t->name,
                'harga_per_bulan' => (float) $t->price_per_month,
                'kapasitas' => $t->capacity,
            ]);

        return Response::json(['tipe_kamar' => $types]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
