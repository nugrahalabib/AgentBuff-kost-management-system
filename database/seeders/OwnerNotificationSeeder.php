<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class OwnerNotificationSeeder extends Seeder
{
    public function run()
    {
        // Get the owner (assuming id 1 or role owner)
        $owner = User::where('role', 'owner')->first();

        if (!$owner) {
            $this->command->info('No owner found. Skipping notification seeding.');
            return;
        }

        // Clear existing notifications for cleanliness during dev
        Notification::where('user_id', $owner->id)->delete();

        // 1. Transaction Verification (Today) -> Use 'payment_received'
        Notification::create([
            'user_id' => $owner->id,
            'type' => 'payment_received',
            'category' => 'finance',
            'title' => 'Pembayaran Baru',
            'message' => 'Budi Santoso (Kamar 101) telah mengupload bukti transfer sebesar Rp 1.500.000.',
            'created_at' => Carbon::now()->subHours(1),
            'action_required' => true,
            'related_entity_type' => 'transaction',
            'related_entity_id' => 101, // Dummy ID
            'priority' => 'high',
            'status' => 'unread',
        ]);

        // 2. Report Available (Today) -> Use 'report_submitted'
        Notification::create([
            'user_id' => $owner->id,
            'type' => 'report_submitted',
            'category' => 'system',
            'title' => 'Laporan Bulanan Siap',
            'message' => 'Laporan Keuangan & Okupansi periode Januari 2026 telah dikirim oleh Admin.',
            'created_at' => Carbon::now()->subHours(4),
            'action_required' => false,
            'related_entity_type' => 'report',
            'related_entity_id' => 16, // Matches the visible report ID
            'priority' => 'normal',
            'status' => 'unread',
        ]);

        // 3. Tenant Expiring (Yesterday) -> Use 'contract_expiring'
        Notification::create([
            'user_id' => $owner->id,
            'type' => 'contract_expiring',
            'category' => 'info',
            'title' => 'Masa Sewa Akan Habis',
            'message' => 'Sewa untuk Siti Aminah (Kamar 103) akan berakhir dalam 7 hari.',
            'created_at' => Carbon::now()->subDay()->hour(9),
            'action_required' => false,
            'related_entity_type' => 'tenant',
            'related_entity_id' => 5, // Dummy ID
            'priority' => 'normal',
            'status' => 'read',
        ]);

        // 4. Maintenance Request (2 Days Ago)
        Notification::create([
            'user_id' => $owner->id,
            'type' => 'maintenance_request',
            'category' => 'urgent',
            'title' => 'Laporan Kerusakan',
            'message' => 'AC di Kamar 205 (Ahmad) dilaporkan tidak dingin.',
            'created_at' => Carbon::now()->subDays(2)->hour(14),
            'action_required' => true,
            'related_entity_type' => 'maintenance',
            'related_entity_id' => 1,
            'priority' => 'high',
            'status' => 'read',
        ]);

        $this->command->info('Owner notifications seeded successfully.');
    }
}
