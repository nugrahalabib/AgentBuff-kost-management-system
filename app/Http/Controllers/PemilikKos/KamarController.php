<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KamarController extends Controller
{
    /**
     * Display rooms (view-only for owner)
     */
    /**
     * Display rooms (view-only for owner)
     */
    public function index(Request $request)
    {
        // Owner checks their own rooms
        $ownerId = Auth::id();

        $floor = $request->get('floor', null);
        $status = $request->get('status', null);
        $roomTypeId = $request->get('room_type', null);
        $paymentStatus = $request->get('payment', null);
        $search = $request->get('search', null);

        // Get owner's reminder setting for "mau habis" calculation
        $businessSetting = \App\Models\PemilikKos::where('owner_id', $ownerId)->first();
        $reminderDays = $businessSetting->invoice_reminder_days_before ?? 7;

        // Base Query - Scoped to Owner
        $query = Kamar::where('owner_id', $ownerId)
            ->with(['roomType', 'occupants.tenantProfile', 'occupants.tenantTransactions']);

        // Search by room number or tenant name
        if ($search) {
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
        if ($status && $status !== 'semua') {
            if ($status === 'occupied') {
                $query->has('occupants');
            } elseif ($status === 'available') {
                $query->doesntHave('occupants')->where('status', '!=', 'maintenance');
            } else {
                $query->where('status', $status);
            }
        }

        // Filter by room type
        if ($roomTypeId && $roomTypeId !== 'semua') {
            $query->where('tipe_kamar_id', $roomTypeId);
        }

        // Filter by payment status
        if ($paymentStatus && $paymentStatus !== 'semua') {
            $today = now()->startOfDay();
            $reminderEndDate = now()->addDays($reminderDays)->endOfDay();

            $query->has('occupants');

            if ($paymentStatus === 'lancar') {
                $query->whereHas('occupants.tenantTransactions', function ($q) use ($reminderEndDate) {
                    $q->where('status', 'verified_by_owner')
                      ->where('period_end_date', '>', $reminderEndDate);
                });
            } elseif ($paymentStatus === 'mau_habis') {
                $query->whereHas('occupants.tenantTransactions', function ($q) use ($today, $reminderEndDate) {
                    $q->where('status', 'verified_by_owner')
                      ->whereBetween('period_end_date', [$today, $reminderEndDate]);
                });
            } elseif ($paymentStatus === 'nunggak') {
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

        // Stats - Scoped to Owner
        $stats = [
            'total' => Kamar::where('owner_id', $ownerId)->count(),
            'available' => Kamar::where('owner_id', $ownerId)->doesntHave('occupants')->where('status', '!=', 'maintenance')->count(),
            'occupied' => Kamar::where('owner_id', $ownerId)->has('occupants')->count(),
            'maintenance' => Kamar::where('owner_id', $ownerId)->where('status', 'maintenance')->count(),
        ];

        // Total Penghuni: Count UNIQUE tenants referenced by rooms owned by this owner
        $totalTenants = \Illuminate\Support\Facades\DB::table('riwayat_penghuni_kamar')
            ->join('kamar', 'riwayat_penghuni_kamar.kamar_id', '=', 'kamar.id')
            ->where('kamar.owner_id', $ownerId)
            ->distinct('riwayat_penghuni_kamar.user_id')
            ->count('riwayat_penghuni_kamar.user_id');

        $floors = [
            '1' => 'Lantai 1',
            '2' => 'Lantai 2',
            '3' => 'Lantai 3',
            '4' => 'Lantai 4',
        ];

        // Get room types for filters
        $roomTypes = TipeKamar::where('owner_id', $ownerId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('pemilik-kos.kamar', [
            'kamar' => $rooms,
            'totalKamar' => $stats['total'],
            'kamarTersedia' => $stats['available'],
            'kamarTerisi' => $stats['occupied'],
            'totalPenyewa' => $totalTenants,
            'kamarMaintenance' => $stats['maintenance'],
            'floors' => $floors,
            'selectedFloor' => $floor,
            'selectedStatus' => $status,
            'selectedRoomType' => $roomTypeId,
            'selectedPayment' => $paymentStatus,
            'search' => $search,
            'tipeKamar' => $roomTypes,
            'reminderDays' => $reminderDays,
        ]);
    }
}
