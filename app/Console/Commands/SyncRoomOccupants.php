<?php

namespace App\Console\Commands;

use App\Models\Kamar;
use Illuminate\Console\Command;

class SyncRoomOccupants extends Command
{
    protected $signature = 'sync:room-occupants';
    protected $description = 'Sync existing tenants from current_tenant_id to riwayat_penghuni_kamar pivot table';

    public function handle()
    {
        $this->info('=== Syncing Room Occupants ===');
        
        $rooms = Kamar::whereNotNull('current_tenant_id')->with('currentTenant')->get();
        
        $this->info("Found {$rooms->count()} rooms with tenants.");
        
        $synced = 0;
        $alreadyExists = 0;
        
        foreach ($rooms as $room) {
            // Check if tenant already exists in pivot table
            if ($room->occupants()->where('user_id', $room->current_tenant_id)->exists()) {
                $alreadyExists++;
                $this->line("  ✓ Room {$room->room_number} already has tenant in occupants table");
                continue;
            }
            
            // Add tenant to pivot table
            $checkInDate = $room->lease_start_date ?? now();
            $room->occupants()->attach($room->current_tenant_id, [
                'check_in_date' => $checkInDate,
            ]);
            
            // Set current_occupants to 1 if not already set
            if ($room->current_occupants < 1) {
                $room->update(['current_occupants' => 1]);
            }
            
            $tenantName = $room->currentTenant?->name ?? 'Unknown';
            $this->warn("  → Synced: Room {$room->room_number} ← {$tenantName}");
            $synced++;
        }
        
        $this->newLine();
        $this->info('=== Summary ===');
        $this->line("  Already synced: {$alreadyExists}");
        $this->line("  Newly synced: {$synced}");
        $this->info('✅ Done!');
        
        return 0;
    }
}
