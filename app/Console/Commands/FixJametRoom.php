<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Transaksi;

class FixJametRoom extends Command
{
    protected $signature = 'fix:jamet-room';
    protected $description = 'Add jamet to room 106 pivot table';

    public function handle()
    {
        $jamet = User::find(22);
        $room106 = Kamar::find(25);
        
        if (!$jamet || !$room106) {
            $this->error('User or Room not found!');
            return 1;
        }
        
        // Get jamet's first transaction to get check_in_date
        $firstTx = Transaksi::where('penyewa_id', $jamet->id)
            ->where('kamar_id', $room106->id)
            ->orderBy('period_start_date', 'asc')
            ->first();
        
        $checkInDate = $firstTx ? $firstTx->period_start_date : now()->format('Y-m-d');
        
        // Add jamet to riwayat_penghuni_kamar
        $room106->occupants()->syncWithoutDetaching([
            $jamet->id => ['check_in_date' => $checkInDate]
        ]);
        
        $this->info("Added jamet (ID: {$jamet->id}) to room 106 (ID: {$room106->id}) with check_in_date: {$checkInDate}");
        
        // Verify
        $this->line("Room 106 occupants now: " . $room106->fresh()->occupants->pluck('name')->join(', '));
        
        return 0;
    }
}
