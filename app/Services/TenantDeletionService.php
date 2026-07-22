<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\Notification;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Hapus penyewa beserta relasi (transaksi, bukti, occupancy, dokumen).
 * Dipakai panel web (ManagesTenants) dan tool MCP — satu sumber kebenaran.
 */
class TenantDeletionService
{
    /**
     * @param  User  $tenant  User role tenant (sudah di-scope caller)
     * @param  string  $reason  Alasan penghapusan
     * @param  User  $actor  Siapa yang menghapus (owner/admin/MCP token user)
     * @param  int  $ownerId  Owner kos
     */
    public function delete(User $tenant, string $reason, User $actor, int $ownerId): void
    {
        $tenantName = $tenant->name;
        $tenantId = $tenant->id;

        DB::transaction(function () use ($tenant, $tenantId, $reason, $actor, $ownerId, $tenantName) {
            $roomIds = DB::table('riwayat_penghuni_kamar')
                ->where('user_id', $tenantId)
                ->pluck('kamar_id')->unique();

            $proofPaths = DB::table('bukti_bayar')
                ->join('transaksi', 'bukti_bayar.transaksi_id', '=', 'transaksi.id')
                ->where('transaksi.penyewa_id', $tenantId)
                ->pluck('bukti_bayar.file_path');
            foreach ($proofPaths as $path) {
                $this->deleteStoredFile($path);
            }

            $profile = Penyewa::where('user_id', $tenantId)->first();
            if ($profile) {
                foreach ((array) ($profile->documents ?? []) as $docPath) {
                    $this->deleteStoredFile($docPath);
                }
                $this->deleteStoredFile($profile->id_card_photo_path);
            }

            DB::table('late_payment_fines')->where('penyewa_id', $tenantId)->delete();
            DB::table('room_occupancy_histories')->where('penyewa_id', $tenantId)->delete();
            DB::table('transaksi')->where('penyewa_id', $tenantId)->delete();

            Notification::where('related_entity_type', 'tenant')
                ->where('related_entity_id', $tenantId)->delete();

            $tenant->delete();

            foreach ($roomIds as $rid) {
                $room = Kamar::find($rid);
                if (! $room || $room->status === 'maintenance') {
                    continue;
                }
                $room->refresh();
                $room->update(['status' => $room->isFull() ? 'occupied' : 'available']);
            }

            LoggerService::log(
                'delete',
                "Hapus penyewa {$tenantName}. Alasan: {$reason}",
                null,
                null,
                null,
                $actor
            );

            if ($actor->role === 'admin') {
                Notification::create([
                    'user_id' => $ownerId,
                    'type' => 'info',
                    'category' => 'system',
                    'title' => 'Penyewa Dihapus oleh Admin',
                    'message' => "Admin {$actor->name} menghapus penyewa \"{$tenantName}\". Alasan: {$reason}",
                    'related_entity_type' => 'tenant',
                    'related_entity_id' => $tenantId,
                    'priority' => 'high',
                    'action_required' => false,
                ]);
            }
        });
    }

    private function deleteStoredFile(?string $path): void
    {
        if (! $path) {
            return;
        }
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }
}
