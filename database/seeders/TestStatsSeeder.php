<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Kamar;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Hash;

class TestStatsSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('role', 'owner')->first();
        $admin = User::where('role', 'admin')->first();
        
        if (!$owner || !$admin) {
            $this->command->error('Owner or Admin not found!');
            return;
        }
        
        // Get rooms that can be used
        $rooms = Kamar::limit(5)->get();
        
        if ($rooms->count() < 5) {
            $this->command->error('Not enough rooms in database!');
            return;
        }

        // ========== 3 NUNGGAK ACCOUNTS ==========
        for ($i = 1; $i <= 3; $i++) {
            $user = User::updateOrCreate(
                ['email' => "nunggak$i@test.com"],
                [
                    'name' => "Nunggak $i",
                    'password' => Hash::make('12345678'),
                    'role' => 'tenant',
                    'email_verified_at' => now(),
                    'kamar_id' => $rooms[$i - 1]->id,
                ]
            );

            Penyewa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => "08123456000$i",
                    'birth_place' => 'Kota Test',
                    'birth_date' => '1995-01-15',
                    'address' => "Alamat Nunggak $i",
                    'is_verified_by_admin' => true,
                    'status' => 'active',
                ]
            );

            // Update room
            $rooms[$i - 1]->update(['current_tenant_id' => $user->id]);

            // Create EXPIRED transaction
            Transaksi::updateOrCreate(
                ['invoice_number' => "INV-NUNGGAK-0000$i"],
                [
                    'owner_id' => $owner->id,
                    'penyewa_id' => $user->id,
                    'kamar_id' => $rooms[$i - 1]->id,
                    'amount' => 500000,
                    'duration_months' => 1,
                    'period_start_date' => now()->subMonths(2),
                    'period_end_date' => now()->subMonth(), // EXPIRED
                    'payment_method' => 'bank_transfer',
                    'reference_number' => 'REF-NUNGGAK-' . $i . '-' . uniqid(),
                    'payment_date' => now()->subMonths(2),
                    'due_date' => now()->subMonths(2)->addDays(3),
                    'status' => 'verified_by_owner',
                    'admin_verified_at' => now()->subMonths(2),
                    'admin_verified_by' => $admin->id,
                    'owner_verified_at' => now()->subMonths(2),
                    'owner_verified_by' => $owner->id,
                ]
            );

            $this->command->info("Created: Nunggak $i (nunggak$i@test.com) - EXPIRED 1 month ago");
        }

        // ========== 2 KONTRAK HABIS ACCOUNTS ==========
        for ($i = 1; $i <= 2; $i++) {
            $user = User::updateOrCreate(
                ['email' => "kontrak$i@test.com"],
                [
                    'name' => "Kontrak $i",
                    'password' => Hash::make('12345678'),
                    'role' => 'tenant',
                    'email_verified_at' => now(),
                    'kamar_id' => $rooms[$i + 2]->id,
                ]
            );

            Penyewa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => "08198765000$i",
                    'birth_place' => 'Kota Kontrak',
                    'birth_date' => '1998-06-20',
                    'address' => "Alamat Kontrak $i",
                    'is_verified_by_admin' => true,
                    'status' => 'active',
                ]
            );

            // Update room
            $rooms[$i + 2]->update(['current_tenant_id' => $user->id]);

            // Expires in 3-4 days
            $expiresIn = $i + 2;
            
            Transaksi::updateOrCreate(
                ['invoice_number' => "INV-KONTRAK-0000$i"],
                [
                    'owner_id' => $owner->id,
                    'penyewa_id' => $user->id,
                    'kamar_id' => $rooms[$i + 2]->id,
                    'amount' => 600000,
                    'duration_months' => 1,
                    'period_start_date' => now()->subMonth()->addDays($expiresIn),
                    'period_end_date' => now()->addDays($expiresIn), // EXPIRING
                    'payment_method' => 'bank_transfer',
                    'reference_number' => 'REF-KONTRAK-' . $i . '-' . uniqid(),
                    'payment_date' => now()->subMonth()->addDays($expiresIn),
                    'due_date' => now()->subMonth()->addDays($expiresIn + 3),
                    'status' => 'verified_by_owner',
                    'admin_verified_at' => now()->subMonth(),
                    'admin_verified_by' => $admin->id,
                    'owner_verified_at' => now()->subMonth(),
                    'owner_verified_by' => $owner->id,
                ]
            );

            $this->command->info("Created: Kontrak $i (kontrak$i@test.com) - expires in $expiresIn days");
        }

        $this->command->info("\n=== DONE ===");
        $this->command->info("Password: 12345678");
    }
}
