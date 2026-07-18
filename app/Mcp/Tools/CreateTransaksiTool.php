<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\Kamar;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Catat pembayaran/transaksi manual (langsung terverifikasi) untuk seorang penyewa & tempatkan ke kamar bila belum. Butuh penyewa_id, kamar_id, nominal, durasi (bulan), metode.')]
class CreateTransaksiTool extends Tool
{
    use InteractsWithOwner;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $validated = $request->validate([
            'penyewa_id' => ['required', Rule::exists('penyewa', 'user_id')->where('owner_id', $ownerId)],
            'kamar_id' => ['required', Rule::exists('kamar', 'id')->where('owner_id', $ownerId)],
            'amount' => ['required', 'numeric', 'min:1'],
            'duration' => ['required', 'integer', 'min:1', 'max:24'],
            'payment_method' => ['required', 'in:cash,manual_transfer,edc'],
        ]);

        $tenant = User::findOrFail($validated['penyewa_id']);
        $room = Kamar::with('roomType')->findOrFail($validated['kamar_id']);

        $prefix = 'INV-' . date('ym') . '-';
        $last = Transaksi::where('invoice_number', 'like', $prefix . '%')->orderBy('invoice_number', 'desc')->first();
        $invoice = $prefix . str_pad(($last ? ((int) substr($last->invoice_number, -5)) + 1 : 1), 5, '0', STR_PAD_LEFT);
        $labels = ['cash' => 'TUNAI', 'edc' => 'EDC / MESIN KARTU', 'manual_transfer' => 'TRANSFER MANUAL'];

        try {
            DB::transaction(function () use ($validated, $tenant, $room, $ownerId, $invoice, $labels) {
                // Periode sewa dengan chaining (perpanjangan menyambung dari akhir sewa aktif).
                $period = app(\App\Services\RoomAllocationService::class)
                    ->computeRentalPeriod($tenant->id, (int) $validated['duration']);

                $transaction = Transaksi::create([
                    'owner_id' => $ownerId,
                    'penyewa_id' => $tenant->id,
                    'kamar_id' => $room->id,
                    'amount' => $validated['amount'],
                    'duration_months' => $validated['duration'],
                    'period_start_date' => $period['start'],
                    'period_end_date' => $period['end'],
                    'invoice_number' => $invoice,
                    'reference_number' => $invoice,
                    'payment_date' => now(),
                    'due_date' => now(),
                    'status' => 'verified_by_owner',
                    'payment_method' => $validated['payment_method'],
                    'sender_bank' => $labels[$validated['payment_method']] ?? 'MANUAL',
                    'sender_name' => $tenant->name,
                    'owner_verified_at' => now(),
                    'owner_verified_by' => $ownerId,
                    'provisional_amount' => $validated['amount'],
                    'final_amount' => $validated['amount'],
                ]);

                // Penempatan lewat service bersama (cek kapasitas + reaktivasi pivot).
                // Kamar penuh -> lempar error -> seluruh transaksi ter-rollback.
                $result = app(\App\Services\RoomAllocationService::class)->assignRoomAfterPayment($transaction);
                if (! $result['ok']) {
                    throw new \RuntimeException($result['error']);
                }

                // Notifikasi owner: transaksi MASUK dicatat via AI agent (MCP).
                \App\Models\Notification::create([
                    'user_id' => $ownerId,
                    'type' => 'payment_received',
                    'category' => 'finance',
                    'title' => 'Pemasukan Dicatat (AI Agent)',
                    'message' => 'Pembayaran Rp ' . number_format((float) $validated['amount'], 0, ',', '.') . ' dari ' . $tenant->name . ' dicatat via AI agent.',
                    'related_entity_type' => 'transaction',
                    'related_entity_id' => $transaction->id,
                    'priority' => 'medium',
                ]);
            });
        } catch (\RuntimeException $e) {
            return Response::error($e->getMessage());
        }

        return Response::json([
            'success' => true,
            'invoice' => $invoice,
            'message' => "Transaksi {$invoice} dicatat & penyewa {$tenant->name} ditempatkan di kamar {$room->room_number}.",
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'penyewa_id' => $schema->integer()->description('ID penyewa (user id, dari list-penyewa).')->required(),
            'kamar_id' => $schema->integer()->description('ID kamar (dari list-kamar).')->required(),
            'amount' => $schema->number()->description('Nominal pembayaran (Rupiah).')->required(),
            'duration' => $schema->integer()->description('Durasi sewa dalam bulan (1-24).')->required(),
            'payment_method' => $schema->string()->description('Metode: cash, manual_transfer, atau edc.')->enum(['cash', 'manual_transfer', 'edc'])->required(),
        ];
    }
}
