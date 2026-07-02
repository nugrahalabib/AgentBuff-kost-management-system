<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenyewaController extends Controller
{
    /**
     * Display tenants (view-only for owner)
     */
    public function index(Request $request)
    {
        $search = $request->get('search', null);
        $floor = $request->get('floor', null);
        $paymentStatus = $request->get('status', null);
        $activeStatus = $request->get('active', null);

        $ownerId = Auth::id();

        // Query Tenant - Global / Unscoped as requested (100% Admin Mirror)
        $query = User::where('role', 'tenant')
            ->with(['tenantProfile', 'tenantTransactions', 'currentRoom.roomType', 'occupiedRoom.roomType']);

        // Filter by active status (has room or not)
        if ($activeStatus && $activeStatus !== 'semua') {
            if ($activeStatus === 'aktif') {
                $query->where(function($q) {
                    $q->whereHas('currentRoom')
                      ->orWhereHas('occupiedRoom');
                });
            } elseif ($activeStatus === 'tidak_aktif') {
                $query->whereDoesntHave('currentRoom')
                      ->whereDoesntHave('occupiedRoom');
            }
        }

        // Search logic from Admin
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('tenantProfile', function ($q2) use ($search) {
                      $q2->where('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('currentRoom', function ($q2) use ($search) {
                      $q2->where('room_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('occupiedRoom', function ($q2) use ($search) {
                      $q2->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by floor
        // Filter by floor
        if ($floor && $floor !== 'semua') {
            $floorNumber = (int) $floor;
            $query->where(function ($q) use ($floorNumber) {
                 // MATCH: Active Tenants currently in this floor
                 $q->whereHas('currentRoom', function ($sq) use ($floorNumber) {
                     $sq->where('floor_number', $floorNumber);
                 })->orWhereHas('occupiedRoom', function ($sq) use ($floorNumber) {
                     $sq->where('floor_number', $floorNumber);
                 });
            });
        }

        // Filter by payment status (Admin Logic)
        // Get owner's reminder setting
        $businessSetting = \App\Models\PemilikKos::where('owner_id', Auth::id())->first();
        $reminderDays = $businessSetting->invoice_reminder_days_before ?? 7;
        if ($paymentStatus && $paymentStatus !== 'semua') {
            $today = now()->startOfDay();
            $reminderEndDate = now()->addDays($reminderDays)->endOfDay();
            
            if ($paymentStatus === 'nunggak') {
                $query->where(function ($q2) {
                    $q2->whereHas('currentRoom')->orWhereHas('occupiedRoom');
                })->whereHas('tenantTransactions', function ($q) use ($today) {
                    $q->where('status', 'verified_by_owner')
                      ->where('period_end_date', '<', $today);
                })->whereDoesntHave('tenantTransactions', function ($q) use ($today) {
                    $q->where('status', 'verified_by_owner')
                      ->where('period_end_date', '>=', $today);
                });
            } elseif ($paymentStatus === 'mau_habis') {
                $query->whereHas('tenantTransactions', function ($q) use ($today, $reminderEndDate) {
                    $q->where('status', 'verified_by_owner')
                      ->whereBetween('period_end_date', [$today, $reminderEndDate]);
                });
            } elseif ($paymentStatus === 'lancar') {
                $query->whereHas('tenantTransactions', function ($q) use ($reminderEndDate) {
                    $q->where('status', 'verified_by_owner')
                      ->where('period_end_date', '>', $reminderEndDate);
                })->whereDoesntHave('tenantTransactions', function ($q) {
                    $q->whereIn('status', ['rejected_by_admin', 'rejected_by_owner', 'cancelled_by_tenant']);
                });
            }
        }

        // Order by room number (smallest first), active tenants (with rooms) first
        // Checks BOTH riwayat_penghuni_kamar pivot AND kamar.current_tenant_id
        $query->orderByRaw("
            COALESCE(
                (SELECT MIN(kamar.room_number) FROM kamar 
                 INNER JOIN riwayat_penghuni_kamar ON kamar.id = riwayat_penghuni_kamar.kamar_id 
                 WHERE riwayat_penghuni_kamar.user_id = user.id AND riwayat_penghuni_kamar.check_out_date IS NULL),
                (SELECT MIN(kamar.room_number) FROM kamar WHERE kamar.current_tenant_id = user.id)
            ) IS NULL ASC,
            COALESCE(
                (SELECT MIN(kamar.room_number) FROM kamar 
                 INNER JOIN riwayat_penghuni_kamar ON kamar.id = riwayat_penghuni_kamar.kamar_id 
                 WHERE riwayat_penghuni_kamar.user_id = user.id AND riwayat_penghuni_kamar.check_out_date IS NULL),
                (SELECT MIN(kamar.room_number) FROM kamar WHERE kamar.current_tenant_id = user.id)
            ) + 0 ASC
        ");
        
        $viewMode = $request->get('view', 'list');
        $perPage = $viewMode === 'grid' ? 100 : 10;

        $tenants = $query->paginate($perPage)->appends($request->query());

        // Calculate Dashboard Stats (Global / Unscoped as Admin)
        
        // Total Penghuni (Active only)
        $totalTenants = User::where('role', 'tenant')
            ->where(function($q) {
                $q->whereHas('currentRoom')->orWhereHas('occupiedRoom');
            })
            ->count();

        // Penghuni Baru (Last 30 days)
        $newTenants = User::where('role', 'tenant')
            ->where('created_at', '>=', now()->subDays(30))
            ->where(function($q) {
                $q->whereHas('currentRoom')->orWhereHas('occupiedRoom');
            })
            ->count();
            
        // Kontrak yang akan habis dalam $reminderDays hari ke depan
        $expiringContracts = Transaksi::where('status', 'verified_by_owner')
            ->whereBetween('period_end_date', [
                now()->startOfDay(),
                now()->addDays($reminderDays)->endOfDay()
            ])
            ->distinct('penyewa_id')
            ->count('penyewa_id');
            
        // Nunggak (Expired) - only tenants with active rooms
        $delinquent = Transaksi::where('status', 'verified_by_owner')
            ->where('period_end_date', '<', now()->startOfDay())
            ->whereNotExists(function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('transaksi as t2')
                    ->whereColumn('t2.penyewa_id', 'transaksi.penyewa_id')
                    ->where('t2.status', 'verified_by_owner')
                    ->where('t2.period_end_date', '>=', now()->startOfDay());
            })
            ->whereExists(function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('kamar')
                    ->where(function ($q) {
                        $q->whereColumn('kamar.current_tenant_id', 'transaksi.penyewa_id')
                          ->orWhereExists(function ($q2) {
                              $q2->select(\Illuminate\Support\Facades\DB::raw(1))
                                ->from('riwayat_penghuni_kamar')
                                ->whereColumn('riwayat_penghuni_kamar.kamar_id', 'kamar.id')
                                ->whereColumn('riwayat_penghuni_kamar.user_id', 'transaksi.penyewa_id')
                                ->whereNull('riwayat_penghuni_kamar.check_out_date');
                          });
                    });
            })
            ->distinct('penyewa_id')
            ->count('penyewa_id');

        // Available floors for filter
        // Admin logic: `App\Models\Kamar::distinct()->pluck('floor_number')`
        $availableFloors = \App\Models\Kamar::distinct()->pluck('floor_number')->sort()->values();

        return view('pemilik-kos.penyewa', [
            'dataPenyewa' => $tenants,
            'totalPenyewa' => $totalTenants,
            'penyewaBaru' => $newTenants,
            'expiringContracts' => $expiringContracts,
            'delinquent' => $delinquent,
            'reminderDays' => $reminderDays,
            'availableFloors' => $availableFloors,
            'selectedFloor' => $floor,
            'selectedStatus' => $paymentStatus,
            'selectedActive' => $activeStatus,
            'search' => $search,
            'viewMode' => $viewMode,
        ]);
    }

    /**
     * Get tenant details.
     *
     * In the current single-owner system, owner has visibility over the whole
     * tenant pool (mirrors Admin behaviour). We still defensively reject any
     * tenant whose active or historical room is owned by a different owner —
     * so this controller stays safe if multi-owner support is ever turned on.
     */
    public function show(User $user)
    {
        if ($user->role !== 'tenant') {
            abort(404);
        }

        $user->load(['tenantProfile', 'tenantTransactions', 'occupiedRoom.roomType', 'currentRoom.roomType']);

        $ownerId = Auth::id();

        // Defensive ownership check: if tenant is linked to a room/transaction at all,
        // at least one such link must belong to this owner.
        $hasAnyRoomOrTransaction = $user->occupiedRoom->isNotEmpty()
            || $user->currentRoom
            || $user->tenantTransactions->isNotEmpty();

        if ($hasAnyRoomOrTransaction) {
            $linkedToThisOwner = $user->occupiedRoom->contains(fn($r) => $r->owner_id === $ownerId)
                || ($user->currentRoom && $user->currentRoom->owner_id === $ownerId)
                || $user->tenantTransactions->contains(fn($t) => $t->owner_id === $ownerId);

            if (!$linkedToThisOwner) {
                abort(403);
            }
        }

        return view('pemilik-kos.biodata-penyewa', [
            'penyewa' => $user,
        ]);
    }
}
