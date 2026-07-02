<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class CleanupJametData extends Command
{
    protected $signature = 'cleanup:jamet';
    protected $description = 'Remove jamet from room 106 and delete jamet transactions';

    public function handle()
    {
        $this->info('=== CLEANING UP JAMET DATA ===');
        
        $jamet = User::find(22);
        $room106 = Kamar::find(25);
        
        if (!$jamet) {
            $this->error('Jamet not found!');
            return 1;
        }
        
        // 1. Remove jamet from riwayat_penghuni_kamar pivot
        if ($room106) {
            $this->line('Removing jamet from room 106 pivot...');
            $room106->occupants()->detach($jamet->id);
            $this->info('✓ Removed jamet from riwayat_penghuni_kamar');
        }
        
        // 2. Delete jamet's transactions
        $txCount = Transaksi::where('penyewa_id', $jamet->id)->count();
        $this->line("Deleting {$txCount} transactions for jamet...");
        Transaksi::where('penyewa_id', $jamet->id)->delete();
        $this->info("✓ Deleted {$txCount} transactions");
        
        // 3. Verify room 106 now only has asuew and pookie u
        $room106Fresh = Kamar::find(25);
        $occupants = $room106Fresh->occupants->pluck('name')->join(', ');
        $this->newLine();
        $this->info('=== VERIFICATION ===');
        $this->line("Room 106 occupants now: {$occupants}");
        $this->line("Occupant count: " . $room106Fresh->occupants->count());
        
        // 4. Check jamet status
        $jametFresh = User::find(22);
        $jametRoom = $jametFresh->activeRoom;
        $jametTxCount = $jametFresh->tenantTransactions()->count();
        $this->line("Jamet room: " . ($jametRoom ? $jametRoom->room_number : 'NONE'));
        $this->line("Jamet transactions: {$jametTxCount}");
        
        $this->newLine();
        $this->info('=== CLEANUP COMPLETE ===');
        
        return 0;
    }
}
