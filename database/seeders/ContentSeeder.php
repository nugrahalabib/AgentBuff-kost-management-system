<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ContentSection;
use App\Models\ContentGallery;
use App\Models\ContentFacility;
use App\Models\ContentContact;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerId = User::where('email', 'admin@kosadmin.local')->first()->id ?? 1; // Admin user ID

        // Create Gallery Section with sample images
        $gallerySection = ContentSection::firstOrCreate(
            ['section_key' => 'gallery', 'owner_id' => $ownerId],
            [
                'section_name' => 'Gallery Section',
                'is_active' => true,
            ]
        );

        // Gallery 0: Tampak Depan (New)
        ContentGallery::updateOrCreate(
            ['content_section_id' => $gallerySection->id, 'title' => 'Tampak Depan'],
            [
                'category' => 'facade',
                'title' => 'Tampak Depan',
                'description' => 'Tampilan depan Kos Mutiara 27 yang asri dan modern',
                'images' => [
                    'https://images.unsplash.com/photo-1600596542815-6ad4c727dddf?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                ],
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        // Gallery 1: Ruang Tamu
        ContentGallery::updateOrCreate(
            ['content_section_id' => $gallerySection->id, 'title' => 'Ruang Tamu'],
            [
                'category' => 'living_room',
                'title' => 'Ruang Tamu',
                'description' => 'Area ruang tamu yang nyaman untuk berkumpul dengan teman-teman',
                'images' => [
                    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1578500494198-246f612d03b3?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                ],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        // Gallery 2: Kamar Tidur
        ContentGallery::updateOrCreate(
            ['content_section_id' => $gallerySection->id, 'title' => 'Kamar Tidur'],
            [
                'category' => 'bedroom',
                'title' => 'Kamar Tidur',
                'description' => 'Kamar yang luas dengan pencahayaan alami dan ventilasi udara yang bagus',
                'images' => [
                    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1540932760986-b8f14838ef60?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1551632786-de41ec16a RTCIceCandidate?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                ],
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        // Gallery 3: Dapur
        ContentGallery::updateOrCreate(
            ['content_section_id' => $gallerySection->id, 'title' => 'Dapur'],
            [
                'category' => 'kitchen',
                'title' => 'Dapur',
                'description' => 'Dapur yang dilengkapi dengan peralatan modern dan tempat makan bersama',
                'images' => [
                    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                ],
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        // Gallery 4: Kamar Mandi
        ContentGallery::updateOrCreate(
            ['content_section_id' => $gallerySection->id, 'title' => 'Kamar Mandi'],
            [
                'category' => 'bathroom',
                'title' => 'Kamar Mandi',
                'description' => 'Kamar mandi yang bersih dengan fasilitas air panas dan perlengkapan modern',
                'images' => [
                    'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                ],
                'sort_order' => 4,
                'is_active' => true,
            ]
        );

        // Gallery 5: Area Outdoor
        ContentGallery::updateOrCreate(
            ['content_section_id' => $gallerySection->id, 'title' => 'Area Outdoor'],
            [
                'category' => 'outdoor',
                'title' => 'Area Outdoor',
                'description' => 'Taman dan area terbuka untuk santai dan olahraga',
                'images' => [
                    'https://images.unsplash.com/photo-1560448204-e02f11cb3cc0?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1578500494198-246f612d03b3?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
                ],
                'sort_order' => 5,
                'is_active' => true,
            ]
        );

        // Create Facilities Section
        $facilitiesSection = ContentSection::firstOrCreate(
            ['section_key' => 'facilities', 'owner_id' => $ownerId],
            [
                'section_name' => 'Facilities Section',
                'is_active' => true,
            ]
        );

        // Add facilities with different colors
        $facilities = [
            ['facility_name' => 'WiFi Ultra Cepat', 'description' => 'Koneksi internet 50 Mbps tersedia 24 jam', 'icon_color' => 'blue', 'sort_order' => 1],
            ['facility_name' => 'Keamanan 24 Jam', 'description' => 'Sistem keamanan dengan CCTV dan satuan keamanan profesional', 'icon_color' => 'emerald', 'sort_order' => 2],
            ['facility_name' => 'Ruang Belajar', 'description' => 'Ruang belajar nyaman dengan AC dan pencahayaan optimal', 'icon_color' => 'amber', 'sort_order' => 3],
            ['facility_name' => 'Mesin Cuci', 'description' => 'Fasilitas laundry dengan mesin cuci otomatis di setiap lantai', 'icon_color' => 'purple', 'sort_order' => 4],
            ['facility_name' => 'Parkir Luas', 'description' => 'Area parkir berlangganan untuk motor dan mobil dengan sistem keamanan', 'icon_color' => 'red', 'sort_order' => 5],
            ['facility_name' => 'Tempat Ibadah', 'description' => 'Mushola yang bersih dan nyaman untuk ibadah', 'icon_color' => 'green', 'sort_order' => 6],
        ];

        foreach ($facilities as $facility) {
            ContentFacility::updateOrCreate(
                ['content_section_id' => $facilitiesSection->id, 'facility_name' => $facility['facility_name']],
                $facility + ['is_active' => true]
            );
        }

        // Create Contact Section
        $contactSection = ContentSection::firstOrCreate(
            ['section_key' => 'contact', 'owner_id' => $ownerId],
            [
                'section_name' => 'Contact Section',
                'is_active' => true,
            ]
        );

        // Add contact information
        $contacts = [
            [
                'contact_type' => 'address',
                'contact_value' => 'BPI Blok S No.29A, Tambakaji, Semarang, Indonesia',
                'label' => 'Alamat Lengkap',
                'sort_order' => 1,
            ],
            [
                'contact_type' => 'phone',
                'contact_value' => '0811 2702 889',
                'label' => 'Telepon / WhatsApp',
                'sort_order' => 2,
            ],
            [
                'contact_type' => 'email',
                'contact_value' => 'info@mutiara27.com',
                'label' => 'Email',
                'sort_order' => 3,
            ],
            [
                'contact_type' => 'maps_embed',
                'contact_value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.1280774714837!2d110.35100217523436!3d-6.9941932930069335!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708b834b8a199f%3A0x526c7bef91205552!2sKost%20Putri%20Mutiara27!5e0!3m2!1sid!2sid!4v1770413208018!5m2!1sid!2sid',
                'label' => 'Google Maps',
                'sort_order' => 4,
            ],
        ];

        foreach ($contacts as $contact) {
            ContentContact::updateOrCreate(
                ['content_section_id' => $contactSection->id, 'contact_type' => $contact['contact_type']],
                $contact + ['is_active' => true]
            );
        }

        $this->command->info('Content seeded successfully!');
    }
}
