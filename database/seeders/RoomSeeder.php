<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\TipeKamar;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('role', 'owner')->first();
        $singleType = TipeKamar::where('name', 'Single')->first();
        $duoType = TipeKamar::where('name', 'Duo')->first();

        if (!$owner || !$singleType || !$duoType) {
            $this->command->error('Owner or Room Types not found!');
            return;
        }

        // Create 4 floors with 10 rooms each (5 Single + 5 Duo)
        for ($floor = 1; $floor <= 4; $floor++) {
            // Single rooms: x01-x05
            for ($i = 1; $i <= 5; $i++) {
                $roomNumber = $floor . '0' . $i;
                Kamar::create([
                    'owner_id' => $owner->id,
                    'tipe_kamar_id' => $singleType->id,
                    'room_number' => $roomNumber,
                    'floor_number' => $floor,
                    'status' => 'available',
                    'price_per_month' => $singleType->price_per_month,
                ]);
            }
            // Duo rooms: x06-x10
            for ($i = 6; $i <= 10; $i++) {
                $roomNumber = $floor . ($i < 10 ? '0' . $i : $i);
                Kamar::create([
                    'owner_id' => $owner->id,
                    'tipe_kamar_id' => $duoType->id,
                    'room_number' => $roomNumber,
                    'floor_number' => $floor,
                    'status' => 'available',
                    'price_per_month' => $duoType->price_per_month,
                ]);
            }
        }

        $this->command->info('Created ' . Kamar::count() . ' rooms successfully!');
    }
}
