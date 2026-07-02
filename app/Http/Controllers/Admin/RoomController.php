<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\TipeKamar;
use App\Models\User;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display all rooms with filtering
     */
    public function index(Request $request)
    {
        $floor = $request->get('floor', null);
        $status = $request->get('status', null);
        $roomTypeId = $request->get('room_type', null);
        $paymentStatus = $request->get('payment', null);
        $search = $request->get('search', null);

        // Get owner's reminder setting for "mau habis" calculation
        $owner = User::where('role', 'owner')->first();
        $businessSetting = $owner ? \App\Models\PemilikKos::where('owner_id', $owner->id)->first() : null;
        $reminderDays = $businessSetting->invoice_reminder_days_before ?? 7;

        $query = Kamar::with(['roomType', 'occupants.tenantProfile', 'occupants.tenantTransactions']);

        // Search by room number or tenant name
        // Normalize search by replacing spaces with % wildcards to handle multiple spaces
        if ($search) {
            // Replace multiple spaces with single % to match any whitespace variation
            $normalizedSearch = preg_replace('/\s+/', '%', trim($search));
            
            $query->where(function ($q) use ($normalizedSearch, $search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhereHas('occupants', function ($q2) use ($normalizedSearch) {
                      $q2->where('name', 'like', "%{$normalizedSearch}%");
                  });
            });
        }

        // Filter by floor
        if ($floor && $floor !== 'semua') {
            $floorNumber = (int) $floor;
            $query->where('floor_number', $floorNumber);
        }

        // Filter by room status (available/occupied/maintenance)
        // 'occupied' uses current_tenant_id as source of truth (same as tenant page)
        if ($status && $status !== 'semua') {
            if ($status === 'occupied') {
                // Room is occupied if it has at least one occupant
                $query->has('occupants');
            } elseif ($status === 'available') {
                // Room is available if it has no occupants and not in maintenance
                $query->doesntHave('occupants')->where('status', '!=', 'maintenance');
            } else {
                $query->where('status', $status);
            }
        }

        // Filter by room type
        if ($roomTypeId && $roomTypeId !== 'semua') {
            $query->where('tipe_kamar_id', $roomTypeId);
        }

        // Filter by payment status (MUST be occupied rooms with that payment status)
        // Logic copied from TenantController for consistency
        if ($paymentStatus && $paymentStatus !== 'semua') {
            $today = now()->startOfDay();
            $reminderEndDate = now()->addDays($reminderDays)->endOfDay();

            // All payment filters require the room to have a tenant
            $query->has('occupants');

            if ($paymentStatus === 'lancar') {
                // Rooms where ANY occupant has active contract
                $query->whereHas('occupants.tenantTransactions', function ($q) use ($reminderEndDate) {
                    $q->where('status', 'verified_by_owner')
                      ->where('period_end_date', '>', $reminderEndDate);
                });
            } elseif ($paymentStatus === 'mau_habis') {
                // Rooms where ANY occupant's contract expires soon
                $query->whereHas('occupants.tenantTransactions', function ($q) use ($today, $reminderEndDate) {
                    $q->where('status', 'verified_by_owner')
                      ->whereBetween('period_end_date', [$today, $reminderEndDate]);
                });
            } elseif ($paymentStatus === 'nunggak') {
                // Rooms where ANY occupant is delinquent
                $query->whereHas('occupants', function ($q) use ($today) {
                    $q->whereHas('tenantTransactions', function ($q2) use ($today) {
                        $q2->where('status', 'verified_by_owner')
                           ->where('period_end_date', '<', $today);
                    })->whereDoesntHave('tenantTransactions', function ($q2) use ($today) {
                        $q2->where('status', 'verified_by_owner')
                           ->where('period_end_date', '>=', $today);
                    });
                });
            }
        }
        // Check view type (grid/list)
        $viewType = $request->input('view', 'grid');

        if ($viewType === 'list') {
            $rooms = $query->orderBy('floor_number', 'asc')
                           ->orderBy('room_number', 'asc')
                           ->paginate(10);
        } else {
            // Grid view: Show all rooms (no pagination)
            $rooms = $query->orderBy('floor_number', 'asc')
                           ->orderBy('room_number', 'asc')
                           ->get();
        }

        // Stats use occupants pivot as source of truth
        $stats = [
            'total' => Kamar::count(),
            'available' => Kamar::doesntHave('occupants')->where('status', '!=', 'maintenance')->count(),
            'occupied' => Kamar::has('occupants')->count(),
            'maintenance' => Kamar::where('status', 'maintenance')->count(),
        ];

        // Total Penghuni: Count UNIQUE tenants referenced by rooms
        $totalTenants = \Illuminate\Support\Facades\DB::table('riwayat_penghuni_kamar')->distinct('user_id')->count('user_id');

        $floors = [
            '1' => 'Lantai 1',
            '2' => 'Lantai 2',
            '3' => 'Lantai 3',
            '4' => 'Lantai 4',
        ];

        // Get active room types for filters and Add Room modal
        $roomTypes = TipeKamar::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.kamar', [
            'kamar' => $rooms,
            'totalRooms' => $stats['total'],
            'availableRooms' => $stats['available'],
            'occupiedRooms' => $stats['occupied'],
            'totalTenants' => $totalTenants,
            'maintenanceRooms' => $stats['maintenance'],
            'floors' => $floors,
            'selectedFloor' => $floor,
            'selectedStatus' => $status,
            'selectedRoomType' => $roomTypeId,
            'selectedPayment' => $paymentStatus,
            'search' => $search,
            'roomTypes' => $roomTypes,
            'reminderDays' => $reminderDays,
        ]);
    }

    /**
     * Show create room form
     */
    public function create()
    {
        // Admin can see all active room types (created by owner)
        // Since this is a single-owner system, we show all room types
        $roomTypes = TipeKamar::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.kamar-create', [
            'roomTypes' => $roomTypes,
        ]);
    }

    /**
     * Store new room
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|integer|min:1|unique:kamar,room_number',
            'floor_number' => 'required|numeric|min:1|max:4',
            'tipe_kamar_id' => 'required|exists:tipe_kamar,id',
            'price_per_month' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Get room type and use its owner_id
        $roomType = TipeKamar::findOrFail($validated['tipe_kamar_id']);

        // Create room with the room type's owner
        $room = Kamar::create([
            'owner_id' => $roomType->owner_id,
            'tipe_kamar_id' => $validated['tipe_kamar_id'],
            'room_number' => $validated['room_number'],
            'floor_number' => (int) $validated['floor_number'],
            'status' => $validated['status'],
            'price_per_month' => $validated['price_per_month'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Log activity
        \App\Services\LoggerService::log(
            'create',
            "Tambah kamar {$validated['room_number']}",
            $room
        );

        $viewType = $request->input('view', 'grid');
        return redirect()->route('admin.kamar', ['view' => $viewType])
            ->with('success', 'Kamar ' . $validated['room_number'] . ' berhasil ditambahkan!');
    }

    /**
     * Update room status
     */
    public function updateStatus(Request $request, Kamar $room)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $oldStatus = $room->status;
        $room->update(['status' => $validated['status']]);

        // Log activity
        \App\Services\LoggerService::log(
            'update_status',
            'Update Status Kamar',
            $room,
            ['status' => $oldStatus],
            ['status' => $validated['status']]
        );


        // Notify Owner if status changed to Maintenance
        if ($validated['status'] === 'maintenance' && $oldStatus !== 'maintenance') {
            \App\Models\Notification::create([
                'user_id' => $room->owner_id ?? 1, // Fallback to 1 if no owner assigned
                'type' => 'room_maintenance',
                'category' => 'system',
                'title' => 'Kamar Perlu Perbaikan',
                'message' => "Kamar {$room->room_number} telah ditandai Maintenance oleh Admin.",
                'related_entity_type' => 'room',
                'related_entity_id' => $room->id,
                'priority' => 'high',
                'action_required' => false,
            ]);
        }
        // Notify Owner if status changed from Maintenance to Available (Fixed)
        elseif ($validated['status'] === 'available' && $oldStatus === 'maintenance') {
             \App\Models\Notification::create([
                'user_id' => $room->owner_id ?? 1,
                'type' => 'info',
                'category' => 'system',
                'title' => 'Perbaikan Selesai',
                'message' => "Kamar {$room->room_number} telah selesai diperbaiki dan siap dihuni kembali.",
                'related_entity_type' => 'room',
                'related_entity_id' => $room->id,
                'priority' => 'medium',
                'action_required' => false,
            ]);
        }

        $params = $request->only(['view', 'floor', 'status', 'room_type', 'payment', 'search', 'page']);
        return redirect()->route('admin.kamar', $params)
            ->with('success', 'Status kamar berhasil diubah');
    }

    /**
     * Delete room
     */
    public function destroy(Request $request, Kamar $room)
    {
        // Prevent deleting occupied rooms
        if ($room->status === 'occupied' && $room->occupants()->exists()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus kamar yang sedang ditempati. Hubungi penyewa terlebih dahulu.']);
        }

        $roomNumber = $room->room_number;

        // Log activity before deletion
        \App\Services\LoggerService::log(
            'delete',
            "Hapus kamar {$roomNumber}",
            $room
        );

        // Set room_id to null on related transactions to avoid FK constraint
        \App\Models\Transaksi::where('kamar_id', $room->id)->update(['kamar_id' => null]);

        // Delete the room
        $room->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success', 
                'message' => 'Kamar ' . $roomNumber . ' berhasil dihapus!'
            ]);
        }

        $params = $request->only(['view', 'floor', 'status', 'room_type', 'payment', 'search', 'page']);
        return redirect()->route('admin.kamar', $params)
            ->with('success', 'Kamar ' . $roomNumber . ' berhasil dihapus!');
    }
}
