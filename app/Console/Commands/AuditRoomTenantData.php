<?php

namespace App\Console\Commands;

use App\Models\Kamar;
use App\Models\User;
use Illuminate\Console\Command;

class AuditRoomTenantData extends Command
{
    protected $signature = 'audit:room-tenant';
    protected $description = 'Audit room and tenant data consistency';

    public function handle()
    {
        $this->info('=== COMPREHENSIVE DATA AUDIT ===');
        $this->newLine();
        
        // 1. Check all rooms with current_tenant_id
        $this->info('1. ROOMS WITH current_tenant_id SET:');
        $roomsWithTenant = Kamar::whereNotNull('current_tenant_id')
            ->with(['currentTenant', 'roomType'])
            ->get();
        
        if ($roomsWithTenant->isEmpty()) {
            $this->warn('   NO ROOMS HAVE current_tenant_id SET!');
        } else {
            foreach ($roomsWithTenant as $room) {
                $this->line("   Room {$room->room_number} | Status: {$room->status} | current_tenant_id: {$room->current_tenant_id} | Tenant: " . ($room->currentTenant?->name ?? 'NULL') . " | Type: " . ($room->roomType?->name ?? 'N/A'));
            }
        }
        
        $this->newLine();
        
        // 2. Check all tenants with currentRoom
        $this->info('2. TENANTS WITH ACTIVE ROOM (from TenantController logic):');
        $tenants = User::where('role', 'tenant')
            ->whereHas('currentRoom')
            ->with('currentRoom')
            ->get();
        
        if ($tenants->isEmpty()) {
            $this->warn('   NO TENANTS HAVE currentRoom!');
        } else {
            foreach ($tenants as $tenant) {
                $this->line("   {$tenant->name} → Room {$tenant->currentRoom->room_number}");
            }
        }
        
        $this->newLine();
        
        // 3. Check specific rooms 106, 408, 409
        $this->info('3. CHECKING SPECIFIC ROOMS (106, 408, 409):');
        $specificRooms = Kamar::whereIn('room_number', ['106', '408', '409'])
            ->with(['currentTenant', 'roomType', 'occupants'])
            ->get();
        
        foreach ($specificRooms as $room) {
            $this->warn("   === ROOM {$room->room_number} ===");
            $this->line("   - ID: {$room->id}");
            $this->line("   - Status: {$room->status}");
            $this->line("   - current_tenant_id: " . ($room->current_tenant_id ?? 'NULL'));
            $this->line("   - current_occupants: {$room->current_occupants}");
            $this->line("   - currentTenant: " . ($room->currentTenant?->name ?? 'NULL'));
            $this->line("   - roomType: " . ($room->roomType?->name ?? 'NULL') . " (capacity: " . ($room->roomType?->capacity ?? 'N/A') . ")");
            $this->line("   - occupants (pivot): " . $room->occupants->pluck('name')->join(', ') ?: 'NONE');
        }
        
        $this->newLine();
        
        // 4. Check isOccupied logic
        $this->info('4. CHECKING isOccupied VARIABLE LOGIC:');
        $this->line('   $isOccupied = $room->status === "occupied"');
        $this->line('   This means rooms with status!=occupied will NOT show tenant info!');
        
        $this->newLine();
        $this->info('=== AUDIT COMPLETE ===');
        
        return 0;
    }
}
