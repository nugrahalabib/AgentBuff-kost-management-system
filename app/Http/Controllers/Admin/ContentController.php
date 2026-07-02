<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentSection;
use App\Models\ContentItem;
use App\Models\ContentGallery;
use App\Models\ContentFacility;
use App\Models\ContentContact;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{
    public function index(): View
    {
        $sections = ContentSection::where('owner_id', Auth::id())->get();
        return view('admin.konten', ['sections' => $sections]);
    }

    // Hero Section
    public function editHero(): View
    {
        $section = ContentSection::getOrCreateSection('hero', 'Hero Section');
        $items = $section->items()->orderBy('sort_order')->get();
        return view('admin.konten.hero-edit', ['section' => $section, 'items' => $items]);
    }

    public function updateHero(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'badge' => 'nullable|string|max:255',
            'title' => 'required|string|max:500',
            'subtitle' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'stat_1_value' => 'nullable|string|max:50',
            'stat_1_label' => 'nullable|string|max:100',
            'stat_2_value' => 'nullable|string|max:50',
            'stat_2_label' => 'nullable|string|max:100',
            'stat_3_value' => 'nullable|string|max:50',
            'stat_3_label' => 'nullable|string|max:100',
            'verify_badge_title' => 'nullable|string|max:100',
            'verify_badge_desc' => 'nullable|string|max:255',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $section = ContentSection::getOrCreateSection('hero', 'Hero Section');
        
        // Handle image upload
        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('hero', 'public');
            ContentItem::updateOrCreate(
                ['content_section_id' => $section->id, 'item_type' => 'hero_image_path'],
                ['value' => $path, 'sort_order' => 99]
            );
        }

        // Update or create items - including new statistics and badge fields
        $itemTypes = [
            'badge', 'title', 'subtitle', 'description',
            'stat_1_value', 'stat_1_label',
            'stat_2_value', 'stat_2_label',
            'stat_3_value', 'stat_3_label',
            'verify_badge_title', 'verify_badge_desc'
        ];
        
        foreach ($itemTypes as $type) {
            if (isset($validated[$type]) || $request->has($type)) {
                ContentItem::updateOrCreate(
                    ['content_section_id' => $section->id, 'item_type' => $type],
                    ['value' => $validated[$type] ?? $request->input($type, ''), 'sort_order' => array_search($type, $itemTypes)]
                );
            }
        }

        // Log activity
        \App\Services\LoggerService::log(
            'update_content',
            'Update Hero Section',
            $section,
            null,
            $validated
        );

        return redirect()->route('admin.konten.edit-hero')->with('success', 'Hero section updated successfully');
    }

    // Gallery Section
    public function editGallery(): View
    {
        $section = ContentSection::getOrCreateSection('gallery', 'Gallery Section');
        $galleries = $section->galleries()->orderBy('sort_order')->get();
        $categories = ['living_room', 'bedroom', 'kitchen', 'bathroom', 'workspace', 'outdoor'];
        return view('admin.konten.gallery-edit', ['section' => $section, 'galleries' => $galleries, 'categories' => $categories]);
    }

    public function storeGallery(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|in:tampak_depan,living_room,bedroom,kitchen,bathroom,outdoor',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $section = ContentSection::getOrCreateSection('gallery', 'Gallery Section');
        
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('galleries', 'public');
                $images[] = $path;
            }
        }

        $gallery = ContentGallery::create([
            'content_section_id' => $section->id,
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'images' => $images,
            'sort_order' => ContentGallery::where('content_section_id', $section->id)->max('sort_order') + 1,
            'is_active' => true,
        ]);

        // Log activity
        \App\Services\LoggerService::log(
            'create_content',
            'Tambah Gallery Baru: ' . $validated['title'],
            $gallery
        );

        return redirect()->route('admin.konten.edit-gallery')->with('success', 'Gallery added successfully');
    }

    public function updateGallery(Request $request, ContentGallery $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Append new images to existing ones
        if ($request->hasFile('images')) {
            $existingImages = $gallery->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('galleries', 'public');
                $existingImages[] = $path;
            }
            $validated['images'] = $existingImages;
        }

        $gallery->update($validated);

        return redirect()->route('admin.konten.edit-gallery')->with('success', 'Galeri berhasil diperbarui');
    }

    public function deleteGallery(ContentGallery $gallery): RedirectResponse
    {
        $title = $gallery->title;
        
        \App\Services\LoggerService::log(
            'delete_content',
            'Hapus Gallery: ' . $title,
            null
        );

        $gallery->delete();
        return redirect()->route('admin.konten.edit-gallery')->with('success', 'Gallery deleted successfully');
    }

    // Facilities Section
    public function editFacilities(): View
    {
        $section = ContentSection::getOrCreateSection('facilities', 'Facilities Section');
        $facilities = $section->facilities()->orderBy('sort_order')->get();
        $colors = ['emerald', 'blue', 'amber', 'red', 'purple', 'green'];
        return view('admin.konten.facilities-edit', ['section' => $section, 'facilities' => $facilities, 'colors' => $colors]);
    }

    public function storeFacility(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'facility_name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'icon' => 'required|string|max:50',
        ]);

        $section = ContentSection::getOrCreateSection('facilities', 'Facilities Section');

        ContentFacility::create([
            'content_section_id' => $section->id,
            'facility_name' => $validated['facility_name'],
            'description' => $validated['description'],
            'icon' => $validated['icon'],
            // Warna ikon dibuat bawaan (emerald, warna brand) agar tampilan konsisten
            // & admin tidak perlu memilih warna.
            'icon_color' => 'emerald',
            'sort_order' => ContentFacility::where('content_section_id', $section->id)->max('sort_order') + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.konten.edit-facilities')->with('success', 'Fasilitas berhasil ditambahkan');
    }

    public function updateFacility(Request $request, ContentFacility $facility): RedirectResponse
    {
        $validated = $request->validate([
            'facility_name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'icon' => 'nullable|string|max:50',
        ]);

        $facility->update($validated);

        return redirect()->route('admin.konten.edit-facilities')->with('success', 'Fasilitas berhasil diperbarui');
    }

    public function deleteFacility(ContentFacility $facility): RedirectResponse
    {
        $facility->delete();
        return redirect()->route('admin.konten.edit-facilities')->with('success', 'Facility deleted successfully');
    }

    // Contact Section
    //
    // Disederhanakan untuk admin non-IT: satu form dengan field khusus
    // (Alamat, WhatsApp, Email, Peta) — bukan lagi dropdown "jenis kontak"
    // + textarea generik. Setiap field tetap disimpan sebagai satu baris
    // ContentContact per contact_type, jadi welcome.blade.php tidak berubah.
    public function editContact(): View
    {
        $section = ContentSection::getOrCreateSection('contact', 'Contact Section');
        $contacts = $section->contacts()->get()->keyBy('contact_type');
        return view('admin.konten.contact-edit', ['section' => $section, 'contacts' => $contacts]);
    }

    public function updateContactAll(Request $request): RedirectResponse
    {
        $request->validate([
            'address'    => 'nullable|string|max:500',
            'phone'      => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:255',
            'maps_embed' => 'nullable|string|max:5000',
        ], [
            'email.email' => 'Format email belum benar. Contoh: info@mutiara27.com',
        ]);

        $section = ContentSection::getOrCreateSection('contact', 'Contact Section');

        // Label & urutan tampil bawaan untuk tiap jenis kontak.
        $defaults = [
            'address'    => ['label' => 'Lokasi',    'sort' => 1],
            'phone'      => ['label' => 'WhatsApp',  'sort' => 2],
            'email'      => ['label' => 'Email',     'sort' => 3],
            'maps_embed' => ['label' => 'Peta',      'sort' => 4],
        ];

        foreach ($defaults as $type => $meta) {
            $value = trim((string) $request->input($type, ''));

            if ($value === '') {
                // Field dikosongkan → hapus baris kontak yang lama (jika ada).
                ContentContact::where('content_section_id', $section->id)
                    ->where('contact_type', $type)
                    ->delete();
                continue;
            }

            ContentContact::updateOrCreate(
                ['content_section_id' => $section->id, 'contact_type' => $type],
                [
                    'contact_value' => $value,
                    'label'         => $meta['label'],
                    'sort_order'    => $meta['sort'],
                    'is_active'     => true,
                ]
            );
        }

        return redirect()->route('admin.konten.edit-contact')->with('success', 'Informasi kontak berhasil disimpan');
    }
}
