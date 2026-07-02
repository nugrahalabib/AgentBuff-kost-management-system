<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;

class UpdateExistingRoomLeaseDatesSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = Kamar::whereNotNull('current_tenant_id')
            ->whereNull('lease_end_date')
            ->get();
        
        foreach ($rooms as $room) {
            $latestTransaction = Transaksi::where('kamar_id', $room->id)
                ->where('penyewa_id', $room->current_tenant_id)
                ->where('status', 'verified_by_owner')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($latestTransaction) {
                if ($latestTransaction->period_start_date && $latestTransaction->period_end_date) {
                    $room->update([
                        'lease_start_date' => $latestTransaction->period_start_date,
                        'lease_end_date' => $latestTransaction->period_end_date
                    ]);
                    $this->command->info("Room {$room->room_number}: Updated from transaction");
                } else {
                    $room->update([
                        'lease_start_date' => $latestTransaction->created_at,
                        'lease_end_date' => $latestTransaction->created_at->addMonth()
                    ]);
                    $this->command->info("Room {$room->room_number}: Set default 1 month from transaction date");
                }
            } else {
                $room->update([
                    'lease_start_date' => now(),
                    'lease_end_date' => now()->addMonth()
                ]);
                $this->command->info("Room {$room->room_number}: Set default 1 month from now");
            }
        }
        
        $this->command->info("Done! Updated " . count($rooms) . " rooms");
    }
}
