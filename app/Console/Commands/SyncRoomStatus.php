<?php

namespace App\Console\Commands;

use App\Models\Kamar;
use Illuminate\Console\Command;

class SyncRoomStatus extends Command
{
    protected $signature = 'sync:room-status {--dry-run : Tampilkan perubahan tanpa menyimpan}';

    protected $description = 'Selaraskan kolom kamar.status dengan occupancy sebenarnya (pivot riwayat_penghuni_kamar ATAU current_tenant_id). Kolom status hanya cache dan bisa drift.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $rooms = Kamar::withCount('occupants')->orderBy('room_number')->get();

        $changed = 0;
        $legacyOnly = [];

        foreach ($rooms as $room) {
            $hasPivot = $room->occupants_count > 0;
            $hasLegacy = $room->current_tenant_id !== null;
            $isOccupied = $hasPivot || $hasLegacy;

            // Terisi hanya lewat current_tenant_id legacy dan belum ada di pivot.
            // Ini yang bisa di-backfill; pivot-tanpa-legacy justru kondisi target
            // (current_tenant_id sudah deprecated) sehingga tidak perlu ditandai.
            if ($hasLegacy && ! $hasPivot) {
                $legacyOnly[] = $room;
            }

            // Occupied diprioritaskan; maintenance dipertahankan bila kamar kosong.
            $desired = $isOccupied
                ? 'occupied'
                : ($room->status === 'maintenance' ? 'maintenance' : 'available');

            if ($room->status !== $desired) {
                $this->line(sprintf('  Kamar %s: %s -> %s', $room->room_number, $room->status, $desired));

                if (! $dryRun) {
                    $room->status = $desired;
                    $room->save();
                }

                $changed++;
            }
        }

        $this->newLine();
        $this->info(($dryRun ? '[DRY-RUN] ' : '') . "Kamar diperiksa: {$rooms->count()}, status diperbaiki: {$changed}.");

        if (! empty($legacyOnly)) {
            $this->newLine();
            $this->warn('Kamar terisi hanya lewat current_tenant_id (belum ada di pivot riwayat_penghuni_kamar):');
            foreach ($legacyOnly as $room) {
                $this->line(sprintf(
                    '  Kamar %s: current_tenant_id=%s',
                    $room->room_number,
                    $room->current_tenant_id
                ));
            }
            $this->line('  -> Jalankan `php artisan sync:room-occupants` untuk backfill pivot dari current_tenant_id.');
        }

        return self::SUCCESS;
    }
}
