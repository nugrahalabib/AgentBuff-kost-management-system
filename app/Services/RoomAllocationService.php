<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\Notification;
use App\Models\Transaksi;
use Illuminate\Support\Carbon;
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
     * Hitung periode sewa dengan CHAINING agar perpanjangan berakumulasi.
     *
     * Perpanjangan menyambung dari AKHIR masa sewa aktif penyewa, bukan dari
     * hari ini. Contoh: penyewa perpanjang 2 bln lalu (hari yang sama) 3 bln lagi
     * → total 5 bln (bukan 3). Untuk penyewa tanpa sewa aktif (baru / sudah lewat)
     * → mulai dari sekarang.
     *
     * Basis = period_end_date TERBESAR dari transaksi penyewa yang sudah
     * terverifikasi (owner/admin). Denda, transaksi ditolak, & pending diabaikan.
     * Di-scope by penyewa_id saja karena penyewa_id (= user.id) unik global.
     *
     * @return array{start: \Illuminate\Support\Carbon, end: \Illuminate\Support\Carbon}
     */
    public function computeRentalPeriod(int $tenantId, int $months): array
    {
        $currentEnd = Transaksi::where('penyewa_id', $tenantId)
            ->whereIn('status', ['verified_by_owner', 'verified_by_admin'])
            ->whereNotNull('period_end_date')
            ->max('period_end_date');

        $base = ($currentEnd && Carbon::parse($currentEnd)->isFuture())
            ? Carbon::parse($currentEnd)
            : now();

        return [
            'start' => $base->copy(),
            'end' => $base->copy()->addMonths($months),
        ];
    }

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
