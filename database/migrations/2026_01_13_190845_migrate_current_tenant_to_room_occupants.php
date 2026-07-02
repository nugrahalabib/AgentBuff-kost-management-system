<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This is a one-time data migration. On a fresh install there is nothing
        // to move, so it must tolerate the tables not yet existing. On an upgrade
        // path it runs after the rename to Indonesian, so both naming schemes can
        // be present depending on when the migration runs.
        $roomsTable = Schema::hasTable('rooms')
            ? 'rooms'
            : (Schema::hasTable('kamar') ? 'kamar' : null);

        $pivotTable = Schema::hasTable('room_occupants')
            ? 'room_occupants'
            : (Schema::hasTable('riwayat_penghuni_kamar') ? 'riwayat_penghuni_kamar' : null);

        if (!$roomsTable || !$pivotTable) {
            return;
        }

        // Pivot column may be `room_id` (pre-rename) or `kamar_id` (post-rename).
        $pivotRoomColumn = Schema::hasColumn($pivotTable, 'room_id') ? 'room_id' : 'kamar_id';

        $rooms = DB::table($roomsTable)
            ->whereNotNull('current_tenant_id')
            ->get();

        foreach ($rooms as $room) {
            $exists = DB::table($pivotTable)
                ->where($pivotRoomColumn, $room->id)
                ->where('user_id', $room->current_tenant_id)
                ->exists();

            if (!$exists) {
                DB::table($pivotTable)->insert([
                    $pivotRoomColumn => $room->id,
                    'user_id' => $room->current_tenant_id,
                    'check_in_date' => $room->lease_start_date ?? now(),
                    'check_out_date' => $room->lease_end_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Theoretically we could delete occupants that match current_tenant_id
        // but it's safer to keep them as data preservation.
        // This is mostly a one-way data repair migration.
    }
};
