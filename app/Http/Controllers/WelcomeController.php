<?php

namespace App\Http\Controllers;

use App\Models\TipeKamar;
use App\Models\ContentSection;
use App\Models\ContentFacility;
use App\Models\ContentGallery;
use App\Models\ContentContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    /**
     * Show welcome page with content from database
     */
    public function index()
    {
        // Ambil owner_id dari user yang login, atau gunakan user pertama
        // (Asumsi: hanya ada satu owner dalam sistem)
        $ownerId = Auth::check() && Auth::user()->role === 'admin' 
            ? Auth::id() 
            : \App\Models\User::where('role', 'admin')->first()?->id ?? 1;

        // Ambil konten dari database dengan eager loading
        $heroSection = ContentSection::where('section_key', 'hero')
            ->where('owner_id', $ownerId)
            ->with('items')
            ->first();

        $gallerySection = ContentSection::where('section_key', 'gallery')
            ->where('owner_id', $ownerId)
            ->with('galleries')
            ->first();

        $facilitiesSection = ContentSection::where('section_key', 'facilities')
            ->where('owner_id', $ownerId)
            ->with('facilities')
            ->first();

        $contactSection = ContentSection::where('section_key', 'contact')
            ->where('owner_id', $ownerId)
            ->with('contacts')
            ->first();

        // Ambil data detail
        $galleries = $gallerySection ? $gallerySection->galleries()->where('is_active', true)->get() : collect();
        $facilities = $facilitiesSection ? $facilitiesSection->facilities()->where('is_active', true)->get() : collect();
        $contacts = $contactSection ? $contactSection->contacts()->where('is_active', true)->get() : collect();

        // Ambil tipe kamar aktif dengan hitungan kamar tersedia
        $roomTypes = TipeKamar::where('status', 'active')
            ->withCount(['rooms as available_rooms_count' => function ($query) {
                $query->where('status', 'available');
            }])
            ->withCount('rooms as total_rooms_count')
            ->orderBy('price_per_month', 'asc') 
            ->get();

        return view('welcome', [
            'heroSection' => $heroSection,
            'gallerySection' => $gallerySection,
            'galleries' => $galleries,
            'facilitiesSection' => $facilitiesSection,
            'facilities' => $facilities,
            'contactSection' => $contactSection,
            'contacts' => $contacts,
            'tipeKamar' => $roomTypes,
        ]);
    }
}
