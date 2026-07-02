<?php

use App\Models\ContentGallery;
use App\Models\ContentSection;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Mengisi galeri halaman utama dengan foto aesthetic (Unsplash) secara
 * otomatis saat deploy. Aman & idempotent: firstOrCreate per kategori,
 * jadi TIDAK menimpa galeri yang sudah diunggah admin. Dibungkus try/catch
 * agar tidak pernah menggagalkan proses migrate/deploy.
 */
return new class extends Migration
{
    public function up(): void
    {
        try {
            // Galeri di welcome dibaca berdasar owner = user admin (lihat WelcomeController).
            $ownerId = User::where('role', 'admin')->value('id') ?? User::min('id') ?? 1;

            $section = ContentSection::firstOrCreate(
                ['section_key' => 'gallery'],
                ['owner_id' => $ownerId, 'section_name' => 'Gallery Section', 'is_active' => true]
            );

            $img = fn (string $id) => "https://images.unsplash.com/photo-{$id}?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80";

            $collections = [
                ['category' => 'tampak_depan', 'title' => 'Tampak Depan',  'description' => 'Fasad hunian yang bersih dan modern',    'ids' => ['1600585154340-be6161a56a0c', '1564013799919-ab600027ffc6']],
                ['category' => 'bedroom',      'title' => 'Kamar Tidur',   'description' => 'Kamar nyaman dengan pencahayaan natural', 'ids' => ['1598928506311-c55ded91a20c', '1505691938895-1758d7feb511', '1522708323590-d24dbb6b0267']],
                ['category' => 'living_room',  'title' => 'Ruang Bersama', 'description' => 'Area santai untuk berkumpul',            'ids' => ['1555854877-bab0e564b8d5', '1567767292278-a4f21aa2d36e', '1524758631624-e2822e304c36']],
                ['category' => 'kitchen',      'title' => 'Dapur Bersama', 'description' => 'Dapur bersih dan lengkap untuk memasak',  'ids' => ['1556911220-bff31c812dba', '1565538810643-b5bdb714032a']],
                ['category' => 'bathroom',     'title' => 'Kamar Mandi',   'description' => 'Kamar mandi bersih dan terawat',         'ids' => ['1620626011761-996317b8d101', '1584622650111-993a426fbf0a', '1507652313519-d4e9174996dd']],
                ['category' => 'outdoor',      'title' => 'Area Santai',   'description' => 'Ruang terbuka yang asri',                'ids' => ['1416879595882-3373a0480b5b', '1502672260266-1c1ef2d93688']],
            ];

            foreach ($collections as $i => $c) {
                ContentGallery::firstOrCreate(
                    ['content_section_id' => $section->id, 'category' => $c['category']],
                    [
                        'title'       => $c['title'],
                        'description' => $c['description'],
                        'images'      => array_map($img, $c['ids']),
                        'sort_order'  => $i + 1,
                        'is_active'   => true,
                    ]
                );
            }
        } catch (\Throwable $e) {
            // Konten opsional — jangan pernah gagalkan deploy karenanya.
            Log::warning('Seed galeri halaman utama dilewati: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        // Sengaja kosong: ini data konten, bukan skema. Rollback tidak menghapusnya.
    }
};
