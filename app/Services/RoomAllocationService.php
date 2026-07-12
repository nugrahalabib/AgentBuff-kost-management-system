<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\Notification;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

/**
 * Satu-satunya jalur penempatan penyewa ke kamar. Dipakai semua alur pembayaran
 * (transaksi manual owner, verifikasi reservasi, MCP create-transaksi) supaya
 * logika penempatan tidak terduplikasi dan konsisten (brief §2.5, §3.3).
 *
 * WAJIB dipanggil di dalam DB::transaction milik pemanggil: bila penempatan gagal
 * (mis. kamar penuh) method ini melempar/mengembalikan error sehingga transaksi
 * pemanggil ikut ter-rollback dan tidak ada data setengah jadi.
 */
class RoomAllocationService
{
    /**
     * Tempatkan penyewa dari sebuah transaksi final ke kamarnya.
     *
     * @return array{ok: bool, error?: string}
     */
    public function assignRoomAfterPayment(Transaksi $transaksi): array
    {
        // Transaksi tanpa kamar (mis. denda) → tidak menempatkan siapa pun.
        if (! $transaksi->kamar_id) {
            return ['ok' => true];
        }

        $room = Kamar::with('roomType')->find($transaksi->kamar_id);
        if (! $room) {
            return ['ok' => false, 'error' => 'Kamar tidak ditemukan atau telah dihapus.'];
        }

        $tenantId = $transaksi->penyewa_id;
        $checkInDate = $transaksi->period_start_date ?? now();

        // Penghuni AKTIF kamar ini? (pivot check_out_date NULL). Bila ya → ini
        // perpanjangan sewa: jangan attach lagi, cukup perbarui status & lease.
        $isActiveOccupant = $room->occupants()->where('user.id', $tenantId)->exists();

        if (! $isActiveOccupant) {
            // Tolak bila kamar penuh (belum jadi penghuni & tak ada slot kosong).
            if (! $room->hasAvailableSlot()) {
                $capacity = $room->roomType?->capacity ?? 1;

                return ['ok' => false, 'error' => "Kamar sudah penuh (kapasitas {$capacity} orang)."];
            }

            // Attach / reaktivasi pivot. Ada UNIQUE(kamar_id, user_id): bila baris
            // histori (penyewa pernah checkout) masih ada, REAKTIVASI baris itu —
            // JANGAN insert baru karena akan melanggar constraint (brief §4.1).
            $existingPivot = DB::table('riwayat_penghuni_kamar')
                ->where('kamar_id', $room->id)
                ->where('user_id', $tenantId)
                ->exists();

            if ($existingPivot) {
                DB::table('riwayat_penghuni_kamar')
                    ->where('kamar_id', $room->id)
                    ->where('user_id', $tenantId)
                    ->update([
                        'check_out_date' => null,
                        'check_in_date' => $checkInDate,
                        'updated_at' => now(),
                    ]);
            } else {
                $room->occupants()->attach($tenantId, ['check_in_date' => $checkInDate]);
            }
        }

        // Recompute status dari pivot; 'occupied' HANYA bila penuh (capacity-aware).
        $room->refresh();
        $room->update([
            'status' => $room->isFull() ? 'occupied' : 'available',
            'lease_start_date' => $room->lease_start_date ?? $checkInDate,
            'lease_end_date' => $transaksi->period_end_date ?? $room->lease_end_date,
        ]);

        // Notifikasi penyewa bahwa pembayaran & penempatan berhasil.
        $roomName = $room->roomType->name ?? 'Kamar';
        $periodInfo = $transaksi->period_end_date
            ? ' Masa sewa Anda sampai ' . $transaksi->period_end_date->format('d M Y') . '.'
            : '';

        Notification::create([
            'user_id' => $tenantId,
            'type' => 'payment_completed',
            'category' => 'finance',
            'title' => 'Pembayaran Berhasil!',
            'message' => "Pembayaran untuk {$roomName} telah diverifikasi.{$periodInfo}",
            'related_entity_type' => 'transaction',
            'related_entity_id' => $transaksi->id,
            'priority' => 'high',
        ]);

        return ['ok' => true];
    }
}
