<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Kamar;

class CheckRoomOccupants extends Command
{
    protected $signature = 'check:occupants';
    protected $description = 'Check riwayat_penghuni_kamar pivot table data';

    public function handle()
    {
        $this->info('=== ROOM OCCUPANTS PIVOT TABLE ===');
        
        $occupants = DB::table('riwayat_penghuni_kamar')
            ->join('user', 'user.id', '=', 'riwayat_penghuni_kamar.user_id')
            ->join('kamar', 'kamar.id', '=', 'riwayat_penghuni_kamar.kamar_id')
            ->select('kamar.room_number', 'user.name', 'riwayat_penghuni_kamar.*')
            ->get();
        
        if ($occupants->isEmpty()) {
            $this->warn('NO DATA in riwayat_penghuni_kamar table!');
        } else {
            foreach ($occupants as $o) {
                $this->line("Room {$o->room_number} <- {$o->name} (user_id: {$o->user_id})");
            }
        }
        
        $this->newLine();
        $this->info('=== CHECKING ASUEW ===');
        
        $asuew = User::where('name', 'like', '%asuew%')->first();
        if ($asuew) {
            $this->line("User ID: {$asuew->id}");
            $this->line("currentRoom: " . ($asuew->currentRoom?->room_number ?? 'NULL'));
            $this->line("occupiedRoom: " . ($asuew->occupiedRoom()->first()?->room_number ?? 'NULL'));
            $this->line("activeRoom: " . ($asuew->activeRoom?->room_number ?? 'NULL'));
        } else {
            $this->warn('User asuew not found!');
        }
        
        $this->newLine();
        $this->info('=== ROOM 106 DETAILS ===');
        
        $room106 = Kamar::where('room_number', '106')->with(['currentTenant', 'occupants'])->first();
        if ($room106) {
            $this->line("Room ID: {$room106->id}");
            $this->line("current_tenant_id: " . ($room106->current_tenant_id ?? 'NULL'));
            $this->line("currentTenant: " . ($room106->currentTenant?->name ?? 'NULL'));
            $this->line("occupants (from pivot): " . $room106->occupants->pluck('name')->join(', ') ?: 'NONE');
        }
        
        return 0;
    }
}
