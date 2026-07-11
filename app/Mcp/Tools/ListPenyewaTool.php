<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Daftar penyewa (penghuni) kos ini beserta kamar yang ditempati saat ini.')]
class ListPenyewaTool extends Tool
{
    use InteractsWithOwner;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $tenants = User::where('role', 'tenant')
            ->whereHas('tenantProfile', fn ($q) => $q->where('owner_id', $ownerId))
            ->with(['tenantProfile', 'occupiedRoom', 'currentRoom'])
            ->orderBy('name')
            ->get()
            ->map(function ($u) {
                $room = $u->activeRoom;
                return [
                    'id' => $u->id,
                    'nama' => $u->name,
                    'telepon' => $u->tenantProfile->phone ?? null,
                    'tipe_penyewa' => $u->tenantProfile->tenant_type ?? null,
                    'kamar' => $room->room_number ?? null,
                ];
            });

        return Response::json(['jumlah' => $tenants->count(), 'penyewa' => $tenants]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
