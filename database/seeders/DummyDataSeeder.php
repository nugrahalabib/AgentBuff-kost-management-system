<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TipeKamar;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates accounts with the well-known password "password123".
     * Running it on a production-style environment would publish weak credentials,
     * so refuse to run unless the environment is local/testing or the operator
     * explicitly opts in via SEED_DUMMY_FORCE=true.
     */
    public function run(): void
    {
        $env = app()->environment();
        $force = filter_var(env('SEED_DUMMY_FORCE', false), FILTER_VALIDATE_BOOLEAN);

        if (!in_array($env, ['local', 'testing'], true) && !$force) {
            $this->command?->error("Refusing to run DummyDataSeeder in '{$env}' environment.");
            $this->command?->warn('Set SEED_DUMMY_FORCE=true in the environment to override (and change the passwords first).');
            return;
        }

        // 1. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@kosadmin.local'],
            [
                'name' => 'Admin KosAdmin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Owner Users
        $owner1 = User::firstOrCreate(
            ['email' => 'owner1@kosadmin.local'],
            [
                'name' => 'Budi Hartono',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]
        );

        // 3. Create Room Types (Tipe Kamar)
        $roomTypes = [
            [
                'name' => 'Kamar Standar',
                'description' => 'Kamar dengan fasilitas dasar yang nyaman dan terjangkau',
                'price_per_month' => 2500000,
                'price_per_day' => 100000,
                'facilities' => json_encode(['WiFi', 'AC', 'Tempat Tidur', 'Meja Kerja']),
                'unit_count' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'Kamar Deluxe',
                'description' => 'Kamar dengan fasilitas lengkap dan desain modern',
                'price_per_month' => 3500000,
                'price_per_day' => 140000,
                'facilities' => json_encode(['WiFi', 'AC', 'Tempat Tidur Queen', 'Sofa', 'Meja Kerja', 'Lemari']),
                'unit_count' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'Kamar Premium',
                'description' => 'Kamar premium dengan fasilitas mewah dan pemandangan terbaik',
                'price_per_month' => 5000000,
                'price_per_day' => 200000,
                'facilities' => json_encode(['WiFi Premium', 'AC', 'Tempat Tidur King', 'Sofa Premium', 'Mini Bar', 'Meja Kerja', 'Lemari Besar', 'Kamar Mandi Mewah']),
                'unit_count' => 2,
                'status' => 'active',
            ],
        ];

        foreach ($roomTypes as $type) {
            TipeKamar::firstOrCreate(
                ['name' => $type['name'], 'owner_id' => $owner1->id],
                array_merge($type, [
                    'owner_id' => $owner1->id,
                    'created_by' => $owner1->id,
                    'updated_by' => $owner1->id,
                ])
            );
        }

        // 4. Create Rooms
        $roomData = [
            // Floor 1 - Standar
            ['number' => '101', 'type' => 'Kamar Standar', 'floor' => 1, 'status' => 'occupied'],
            ['number' => '102', 'type' => 'Kamar Standar', 'floor' => 1, 'status' => 'available'],
            ['number' => '103', 'type' => 'Kamar Standar', 'floor' => 1, 'status' => 'occupied'],

            // Floor 2 - Deluxe
            ['number' => '205', 'type' => 'Kamar Deluxe', 'floor' => 2, 'status' => 'occupied'],
            ['number' => '206', 'type' => 'Kamar Deluxe', 'floor' => 2, 'status' => 'occupied'],
            ['number' => '207', 'type' => 'Kamar Deluxe', 'floor' => 2, 'status' => 'available'],

            // Floor 3 - Premium
            ['number' => '301', 'type' => 'Kamar Premium', 'floor' => 3, 'status' => 'available'],
            ['number' => '302', 'type' => 'Kamar Premium', 'floor' => 3, 'status' => 'occupied'],
        ];

        $tenantCounter = 0;
        foreach ($roomData as $room) {
            $roomType = TipeKamar::where('name', $room['type'])->where('owner_id', $owner1->id)->first();

            $createdRoom = Kamar::firstOrCreate(
                ['room_number' => $room['number'], 'owner_id' => $owner1->id],
                [
                    'tipe_kamar_id' => $roomType->id,
                    'owner_id' => $owner1->id,
                    'floor_number' => $room['floor'],
                    'price_per_month' => $roomType->price_per_month,
                    'status' => $room['status'],
                ]
            );

            // If room is occupied, create tenant
            if ($room['status'] === 'occupied' && $tenantCounter < 5) {
                $tenantNames = [
                    ['name' => 'Ahmad Riyanto', 'email' => 'ahmad@example.com', 'phone' => '087812345678'],
                    ['name' => 'Siti Nurhaliza', 'email' => 'siti@example.com', 'phone' => '087856789012'],
                    ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'phone' => '085623456789'],
                    ['name' => 'Rina Wijaya', 'email' => 'rina@example.com', 'phone' => '089123456789'],
                    ['name' => 'Citra Dewi', 'email' => 'citra@example.com', 'phone' => '088765432109'],
                ];

                $tenant = $tenantNames[$tenantCounter];

                $tenantUser = User::firstOrCreate(
                    ['email' => $tenant['email']],
                    [
                        'name' => $tenant['name'],
                        'password' => Hash::make('password123'),
                        'role' => 'tenant',
                        'email_verified_at' => now(),
                    ]
                );

                // Create tenant profile
                $tenantProfile = Penyewa::firstOrCreate(
                    ['user_id' => $tenantUser->id],
                    [
                        'phone' => $tenant['phone'],
                        'id_card_number' => '1234567890' . str_pad($tenantCounter + 1, 6, '0', STR_PAD_LEFT),
                        'address' => 'Jl. Contoh No. ' . (100 + $tenantCounter),
                        'emergency_contact_name' => 'Keluarga ' . $tenant['name'],
                        'emergency_contact_phone' => '08' . substr($tenant['phone'], 2),
                        'is_verified_by_admin' => true,
                        'verified_at' => now(),
                        'status' => 'active',
                    ]
                );

                // Assign tenant to room
                $createdRoom->current_tenant_id = $tenantUser->id;
                $createdRoom->save();

                // Create transaction for this tenant
                Transaksi::firstOrCreate(
                    [
                        'penyewa_id' => $tenantUser->id,
                        'kamar_id' => $createdRoom->id,
                    ],
                    [
                        'owner_id' => $owner1->id,
                        'amount' => $roomType->price_per_month,
                        'final_amount' => $roomType->price_per_month + 510000,
                        'reference_number' => 'TRX' . Carbon::now()->format('YmdHis') . str_pad($tenantCounter + 1, 3, '0', STR_PAD_LEFT),
                        'payment_date' => Carbon::now()->subDays($tenantCounter * 3),
                        'due_date' => Carbon::now()->addDays(5),
                        'status' => $tenantCounter % 2 == 0 ? 'verified_by_owner' : 'pending_verification',
                        'owner_verified_by' => $tenantCounter % 2 == 0 ? $owner1->id : null,
                        'owner_verified_at' => $tenantCounter % 2 == 0 ? now() : null,
                    ]
                );

                $tenantCounter++;
            }
        }

        // Create Admin Staff Users for owner
        $adminSari = User::firstOrCreate(
            ['email' => 'sari@kosadmin.local'],
            [
                'name' => 'Sari Wulandari',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login_at' => now()->subMinutes(10),
            ]
        );
        $adminSari->adminProfile()->firstOrCreate(
            ['user_id' => $adminSari->id],
            ['owner_id' => $owner1->id, 'position' => 'Admin Keuangan']
        );

        $adminAsep = User::firstOrCreate(
            ['email' => 'asep@kosadmin.local'],
            [
                'name' => 'Pak Asep',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login_at' => now()->subDay(),
            ]
        );
        $adminAsep->adminProfile()->firstOrCreate(
            ['user_id' => $adminAsep->id],
            ['owner_id' => $owner1->id, 'position' => 'Penjaga Malam']
        );

        $adminRudi = User::firstOrCreate(
            ['email' => 'rudi@kosadmin.local'],
            [
                'name' => 'Rudi',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'inactive',
                'email_verified_at' => now(),
                'last_login_at' => now()->subMonths(2),
            ]
        );
        $adminRudi->adminProfile()->firstOrCreate(
            ['user_id' => $adminRudi->id],
            ['owner_id' => $owner1->id, 'position' => 'Staff']
        );

        // Create sample activity logs
        \App\Models\AdminActivityLog::create([
            'admin_id' => $adminSari->id,
            'owner_id' => $owner1->id,
            'activity_type' => 'input_transaksi',
            'activity_label' => 'Input Transaksi',
            'notes' => 'Menerima pembayaran tunai Kamar 101',
            'ip_address' => '192.168.1.12',
            'created_at' => now()->subMinutes(15),
        ]);

        \App\Models\AdminActivityLog::create([
            'admin_id' => $owner1->id,
            'owner_id' => $owner1->id,
            'activity_type' => 'login',
            'activity_label' => 'Login',
            'notes' => 'Login sukses ke Dashboard Owner',
            'ip_address' => '36.72.xxx.xxx',
            'created_at' => now()->subHours(2),
        ]);

        \App\Models\AdminActivityLog::create([
            'admin_id' => $adminAsep->id,
            'owner_id' => $owner1->id,
            'activity_type' => 'update_status',
            'activity_label' => 'Update Status',
            'notes' => 'Mengubah status Kamar 104 menjadi "Maintenance"',
            'ip_address' => '192.168.1.15',
            'created_at' => now()->subDays(1)->subHours(1),
        ]);

        \App\Models\AdminActivityLog::create([
            'admin_id' => $adminSari->id,
            'owner_id' => $owner1->id,
            'activity_type' => 'hapus_data',
            'activity_label' => 'Hapus Data',
            'notes' => 'Menghapus data calon penyewa (Batal)',
            'ip_address' => '192.168.1.12',
            'created_at' => now()->subDays(2)->subHours(10),
        ]);

        $this->command->info('✅ Dummy data berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Data yang dibuat:');
        $this->command->info('   Admin: admin@kosadmin.local (password: password123)');
        $this->command->info('   Owner: owner1@kosadmin.local (password: password123)');
        $this->command->info('   Admin Staff: sari@kosadmin.local, asep@kosadmin.local, rudi@kosadmin.local');
        $this->command->info('   5 Penyewa dengan kamar masing-masing');
        $this->command->info('   8 Kamar dalam 3 tipe berbeda');
        $this->command->info('   Sample activity logs untuk audit trail');
        $this->command->info('');
    }
}
