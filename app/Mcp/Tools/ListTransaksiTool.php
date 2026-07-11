<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\Transaksi;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Daftar transaksi/pembayaran kos ini (maks 50 terbaru). Opsional filter status.')]
class ListTransaksiTool extends Tool
{
    use InteractsWithOwner;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $query = Transaksi::where('owner_id', $ownerId)->with(['tenant', 'room'])->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $trx = $query->limit(50)->get()->map(fn ($t) => [
            'id' => $t->id,
            'invoice' => $t->invoice_number,
            'penyewa' => $t->tenant->name ?? null,
            'kamar' => $t->room->room_number ?? null,
            'nominal' => (float) ($t->final_amount ?: $t->amount),
            'status' => $t->status,
            'tanggal' => optional($t->payment_date)->format('Y-m-d'),
        ]);

        return Response::json(['jumlah' => $trx->count(), 'transaksi' => $trx]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()->description('Filter status, mis. verified_by_owner, verified_by_admin, rejected_by_owner.'),
        ];
    }
}
