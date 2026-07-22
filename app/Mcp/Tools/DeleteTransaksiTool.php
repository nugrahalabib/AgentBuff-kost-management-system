<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\AuditsMcpActions;
use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\Notification;
use App\Models\Transaksi;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Hapus catatan transaksi (invoice) beserta bukti bayar. Status hunian/kamar TIDAK berubah — menghapus transaksi tidak mengeluarkan penyewa dari kamar. Butuh transaksi_id (dari list-transaksi).')]
class DeleteTransaksiTool extends Tool
{
    use InteractsWithOwner, AuditsMcpActions;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $validated = $request->validate([
            'transaksi_id' => ['required', 'integer'],
        ]);

        $transaction = Transaksi::with('paymentProofs')
            ->where('owner_id', $ownerId)
            ->find($validated['transaksi_id']);

        if (! $transaction) {
            return Response::error('Transaksi tidak ditemukan di kos Anda.');
        }

        $invoice = $transaction->reference_number
            ?? $transaction->invoice_number
            ?? ('#' . $transaction->id);
        $txId = $transaction->id;

        DB::transaction(function () use ($transaction) {
            foreach ($transaction->paymentProofs as $proof) {
                if ($proof->file_path) {
                    Storage::disk('local')->delete($proof->file_path);
                    Storage::disk('public')->delete($proof->file_path);
                }
            }

            Notification::where('related_entity_type', 'transaction')
                ->where('related_entity_id', $transaction->id)
                ->delete();

            $transaction->delete();
        });

        $this->logMcp($request, 'delete_transaction', "Hapus transaksi {$invoice}");
        $this->notifyOwnerMcp(
            $ownerId,
            'Transaksi Dihapus (AI Agent)',
            "Transaksi {$invoice} dihapus via AI agent. Hunian penyewa tidak diubah.",
            'transaction',
            $txId,
            'info',
            'finance',
            'medium'
        );
        // Admin tidak dinotifikasi soal hapus transaksi (keuangan).

        return Response::json([
            'success' => true,
            'message' => "Transaksi {$invoice} berhasil dihapus.",
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'transaksi_id' => $schema->integer()->description('ID transaksi (dari list-transaksi).')->required(),
        ];
    }
}
