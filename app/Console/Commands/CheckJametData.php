<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Kamar;

class CheckJametData extends Command
{
    protected $signature = 'check:jamet';
    protected $description = 'Check jamet tenant data consistency';

    public function handle()
    {
        $this->info('=== CHECKING JAMET DATA ===');
        
        $jamet = User::where('name', 'like', '%jamet%')->first();
        
        if (!$jamet) {
            $this->error('User jamet not found!');
            return 1;
        }
        
        $this->line("User ID: {$jamet->id}");
        $this->line("Name: {$jamet->name}");
        $currentRoom = $jamet->currentRoom;
        $this->line("currentRoom: " . ($currentRoom ? $currentRoom->room_number : 'NULL'));
        $occupiedRoom = $jamet->occupiedRoom()->first();
        $this->line("occupiedRoom (pivot): " . ($occupiedRoom ? $occupiedRoom->room_number : 'NULL'));
        $activeRoom = $jamet->activeRoom;
        $this->line("activeRoom: " . ($activeRoom ? $activeRoom->room_number : 'NULL'));
        
        $this->newLine();
        $this->info('=== TRANSACTIONS ===');
        
        $transactions = $jamet->tenantTransactions()->with('room')->get();
        $this->line("Total transactions: " . $transactions->count());
        
        foreach ($transactions as $tx) {
            $this->warn("  Invoice: {$tx->invoice_number}");
            $roomNum = $tx->room ? $tx->room->room_number : 'NULL';
            $this->line("    Room ID: {$tx->kamar_id} ({$roomNum})");
            $this->line("    Status: {$tx->status}");
            $this->line("    Period: {$tx->period_start_date} to {$tx->period_end_date}");
        }
        
        $this->newLine();
        $this->info('=== ROOM 106 CHECK ===');
        
        $room106 = Kamar::where('room_number', '106')->first();
        if ($room106) {
            $this->line("Room 106 ID: {$room106->id}");
            $this->line("current_tenant_id: " . ($room106->current_tenant_id ?? 'NULL'));
            $this->line("Occupants in pivot: " . $room106->occupants->pluck('name')->join(', ') ?: 'NONE');
            
            // Check if jamet is in pivot
            $jametInPivot = $room106->occupants->contains('id', $jamet->id);
            $this->line("Jamet in pivot for room 106: " . ($jametInPivot ? 'YES' : 'NO'));
        }
        
        return 0;
    }
}
