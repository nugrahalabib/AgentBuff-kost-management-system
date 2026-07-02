<?php

namespace App\Console\Commands;

use App\Models\Kamar;
use App\Models\User;
use Illuminate\Console\Command;

class SyncTenantRoomData extends Command
{
    protected $signature = 'sync:tenant-room {--dry-run : Show what would be changed without making changes}';
    protected $description = 'Synchronize tenant room data - ensure users.room_id matches kamar.current_tenant_id';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('=== Tenant-Room Data Sync ===');
        $this->newLine();

        // Step 1: Get all rooms with current_tenant_id set
        $roomsWithTenants = Kamar::whereNotNull('current_tenant_id')->get();
        
        $this->info("Found {$roomsWithTenants->count()} rooms with tenants assigned.");
        $this->newLine();

        $synced = 0;
        $alreadyCorrect = 0;
        $errors = [];

        foreach ($roomsWithTenants as $room) {
            $tenant = User::find($room->current_tenant_id);
            
            if (!$tenant) {
                $errors[] = "Room {$room->room_number} (ID:{$room->id}) points to non-existent tenant ID {$room->current_tenant_id}";
                continue;
            }

            if ($tenant->kamar_id === $room->id) {
                $alreadyCorrect++;
                $this->line("  ✓ Room {$room->room_number} <-> {$tenant->name} (already synced)");
            } else {
                $oldRoomId = $tenant->kamar_id ?? 'NULL';
                
                if (!$isDryRun) {
                    $tenant->kamar_id = $room->id;
                    $tenant->save();
                }
                
                $synced++;
                $action = $isDryRun ? '[DRY-RUN] Would update' : 'Updated';
                $this->warn("  → {$action}: {$tenant->name} room_id: {$oldRoomId} -> {$room->id}");
            }
        }

        // Step 2: Clear room_id for tenants not in any room
        $tenantsWithOrphanRoomId = User::where('role', 'tenant')
            ->whereNotNull('kamar_id')
            ->whereNotIn('id', $roomsWithTenants->pluck('current_tenant_id')->filter())
            ->get();

        $orphansCleared = 0;
        if ($tenantsWithOrphanRoomId->count() > 0) {
            $this->newLine();
            $this->info("Found {$tenantsWithOrphanRoomId->count()} tenants with orphan room_id:");
            
            foreach ($tenantsWithOrphanRoomId as $tenant) {
                if (!$isDryRun) {
                    $tenant->kamar_id = null;
                    $tenant->save();
                }
                
                $orphansCleared++;
                $action = $isDryRun ? '[DRY-RUN] Would clear' : 'Cleared';
                $this->warn("  → {$action}: {$tenant->name} (ID:{$tenant->id}) room_id was {$tenant->kamar_id}");
            }
        }

        // Summary
        $this->newLine();
        $this->info('=== Summary ===');
        $this->line("  Rooms with tenants: {$roomsWithTenants->count()}");
        $this->line("  Already synced: {$alreadyCorrect}");
        $this->line("  Synced now: {$synced}");
        $this->line("  Orphan room_id cleared: {$orphansCleared}");
        
        if (count($errors) > 0) {
            $this->newLine();
            $this->error('Errors found:');
            foreach ($errors as $error) {
                $this->line("  ✗ {$error}");
            }
        }

        if ($isDryRun) {
            $this->newLine();
            $this->comment('This was a dry run. No changes were made.');
            $this->comment('Run without --dry-run to apply changes.');
        }

        return 0;
    }
}
