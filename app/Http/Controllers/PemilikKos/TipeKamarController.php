<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use App\Models\TipeKamar;
use App\Models\AdminActivityLog;
use App\Http\Controllers\Concerns\NotifiesAdmins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TipeKamarController extends Controller
{
    use NotifiesAdmins;

    /**
     * Display list of room types (via settings page)
     */
    public function index()
    {
        $owner = Auth::user();
        $roomTypes = TipeKamar::where('owner_id', $owner->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pemilik-kos.pengaturan', [
            'pemilik' => $owner,
            'tipeKamar' => $roomTypes,
            'businessSettings' => $owner->businessSettings,
            'tab' => 'pricing',
        ]);
    }

    /**
     * Store new room type
     */
    public function store(Request $request)
    {
        $owner = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tipe_kamar,name,NULL,id,owner_id,' . $owner->id,
            'description' => 'nullable|string|max:1000',
            'facilities' => 'nullable|json',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'price_per_month' => 'required|numeric|min:0',
            'price_per_day' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'capacity' => 'required|in:1,2',
        ]);

        // Facilities coming from the form is a JSON string.
        // Since the model casts 'facilities' => 'array', we must pass a PHP array.
        $facilities = $request->facilities ? json_decode($request->facilities, true) : [];

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('room-types', 'public');
        }

        // Handle gallery images upload
        $galleryPaths = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $galleryImage) {
                $galleryPaths[] = $galleryImage->store('room-types/gallery', 'public');
            }
        }

        $roomType = TipeKamar::create([
            'owner_id' => $owner->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'facilities' => $facilities,
            'image_path' => $imagePath,
            'gallery_images' => $galleryPaths,
            'price_per_month' => $validated['price_per_month'],
            'price_per_day' => $validated['price_per_day'] ?? null,
            'status' => $validated['status'],
            'capacity' => $validated['capacity'],
            'created_by' => Auth::id(),
        ]);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => Auth::id(),
            'owner_id' => $owner->id,
            'activity_type' => 'create_room_type',
            'activity_label' => 'Tambah Tipe Kamar Baru',
            'model_name' => 'RoomType',
            'model_id' => $roomType->id,
            'new_data' => [
                'name' => $roomType->name,
                'price_per_month' => $roomType->price_per_month,
                'status' => $roomType->status,
                'capacity' => $roomType->capacity,
            ],
            'notes' => "Tambah tipe kamar: {$validated['name']}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Redirect ke URL bersih (bukan back()) agar modal tidak terbuka ulang oleh
        // logika ?add=1, sehingga pop-up tertutup otomatis setelah sukses menambah.
        $this->notifyAdminsOfChange('Tipe Kamar Baru', "Owner menambah tipe kamar \"{$validated['name']}\".");

        return redirect()->route('owner.settings')->with('success', "Tipe kamar '{$validated['name']}' berhasil ditambahkan!");
    }

    /**
     * Update room type
     */
    public function update(Request $request, TipeKamar $roomType)
    {
        $owner = Auth::user();

        // Verify ownership
        if ($roomType->owner_id !== $owner->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tipe_kamar,name,' . $roomType->id . ',id,owner_id,' . $owner->id,
            'description' => 'nullable|string|max:1000',
            'facilities' => 'nullable|json',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'price_per_month' => 'required|numeric|min:0',
            'price_per_day' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'capacity' => 'required|in:1,2',
        ]);

        // Log only field names being updated — never dump the raw request
        // (it contains uploaded files and potentially other PII).
        \Illuminate\Support\Facades\Log::info('RoomType update started', [
            'room_type_id' => $roomType->id,
            'fields' => array_keys($request->except(['_token', 'image', 'gallery_images'])),
        ]);

        // Facilities coming from the form is a JSON string.
        // Since the model casts 'facilities' => 'array', we must pass a PHP array (not a JSON string),
        // otherwise Laravel will double-encode it.
        $facilities = $request->facilities ? json_decode($request->facilities, true) : [];

        $oldData = [
            'name' => $roomType->name,
            'price_per_month' => $roomType->price_per_month,
            'status' => $roomType->status,
            'capacity' => $roomType->capacity,
        ];

        // Handle image upload
        $imagePath = $roomType->image_path;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($roomType->image_path) {
                Storage::disk('public')->delete($roomType->image_path);
            }
            $imagePath = $request->file('image')->store('room-types', 'public');
        }

        // Handle gallery images upload (append to existing)
        $galleryPaths = $roomType->gallery_images ?? [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $galleryImage) {
                $galleryPaths[] = $galleryImage->store('room-types/gallery', 'public');
            }
        }

        $roomType->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'facilities' => $facilities,
            'image_path' => $imagePath,
            'gallery_images' => $galleryPaths,
            'price_per_month' => $validated['price_per_month'],
            'price_per_day' => $validated['price_per_day'] ?? null,
            'status' => $validated['status'],
            'capacity' => $validated['capacity'],
            'updated_by' => Auth::id(),
        ]);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => Auth::id(),
            'owner_id' => $owner->id,
            'activity_type' => 'update_room_type',
            'activity_label' => 'Update Tipe Kamar',
            'model_name' => 'RoomType',
            'model_id' => $roomType->id,
            'old_data' => $oldData,
            'new_data' => [
                'name' => $roomType->name,
                'price_per_month' => $roomType->price_per_month,
                'status' => $roomType->status,
                'capacity' => $roomType->capacity,
            ],
            'changes' => array_keys($validated),
            'notes' => "Update tipe kamar: {$validated['name']}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->notifyAdminsOfChange('Tipe Kamar Diperbarui', "Owner memperbarui tipe kamar \"{$validated['name']}\".");

        return redirect()->route('owner.settings')->with('success', "Tipe kamar '{$validated['name']}' berhasil diperbarui!");
    }

    /**
     * Delete room type
     */
    public function destroy(Request $request, TipeKamar $roomType)
    {
        $owner = Auth::user();

        // Verify ownership
        if ($roomType->owner_id !== $owner->id) {
            abort(403);
        }

        // Check if room type is used by any rooms
        if ($roomType->rooms()->exists()) {
            return back()->withErrors(['error' => 'Tipe kamar ini sedang digunakan oleh kamar. Hapus kamar terlebih dahulu.']);
        }

        $roomTypeName = $roomType->name;

        $roomType->delete();

        // Log activity
        AdminActivityLog::create([
            'admin_id' => Auth::id(),
            'owner_id' => $owner->id,
            'activity_type' => 'delete_room_type',
            'activity_label' => 'Hapus Tipe Kamar',
            'model_name' => 'RoomType',
            'model_id' => $roomType->id,
            'old_data' => [
                'name' => $roomTypeName,
                'price_per_month' => $roomType->price_per_month,
                'status' => $roomType->status,
            ],
            'notes' => "Hapus tipe kamar: {$roomTypeName}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->notifyAdminsOfChange('Tipe Kamar Dihapus', "Owner menghapus tipe kamar \"{$roomTypeName}\".");

        return back()->with('success', "Tipe kamar '{$roomTypeName}' berhasil dihapus!");
    }
}
