<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kamar;

class CheckRoomCapacity extends Command
{
    protected $signature = 'check:capacity';
    protected $description = 'Check for rooms exceeding capacity';

    public function handle()
    {
        $this->info('=== CHECKING ROOM CAPACITY ===');
        
        $rooms = Kamar::with(['occupants', 'roomType'])->get();
        $issues = 0;
        
        foreach ($rooms as $room) {
            $capacity = $room->roomType->capacity ?? 1;
            $occupantCount = $room->occupants->count();
            
            if ($occupantCount > $capacity) {
                $this->error("Room {$room->room_number} OVER CAPACITY!");
                $this->line("  Type: {$room->roomType->name} (Max: {$capacity})");
                $this->line("  Occupants: {$occupantCount}");
                $this->line("  Names: " . $room->occupants->pluck('name')->join(', '));
                $issues++;
            } elseif ($occupantCount > 0) {
                $this->info("Room {$room->room_number}: {$occupantCount}/{$capacity} (OK)");
            }
        }
        
        if ($issues === 0) {
            $this->info("ALL ROOMS OK! No capacity violations found.");
        } else {
            $this->warn("Found {$issues} rooms exceeding capacity.");
        }
        
        return 0;
    }
}
