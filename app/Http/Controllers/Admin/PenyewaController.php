<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Concerns\ManagesTenants;
use App\Http\Controllers\Concerns\HandlesBiodataForm;

class PenyewaController extends Controller
{
    use ManagesTenants, HandlesBiodataForm;

    /** Form tambah penyewa baru (admin). */
    public function create()
    {
        return view('admin.penyewa-create', [
            'rooms' => $this->availableRoomsForOwner(),
        ]);
    }

    /** Simpan penyewa baru + opsional tempatkan ke kamar (admin). */
    public function store(Request $request)
    {
        $this->persistNewTenant($request);

        return redirect()->route('admin.penyewa')->with('success', 'Penyewa baru berhasil ditambahkan.');
    }

    /**
     * Display list of tenants
     */
    public function index(Request $request)
    {
        $ownerId = auth()->user()->ownerId();

        $search = $request->get('search', null);
        $floor = $request->get('floor', null);
        $paymentStatus = $request->get('status', null);
        $activeStatus = $request->get('active', null); // aktif, tidak_aktif

        // Get owner's reminder setting for "mau habis" calculation
        $owner = auth()->user()->resolveOwner();
        $businessSetting = $owner ? \App\Models\PemilikKos::where('owner_id', $owner->id)->first() : null;
        $reminderDays = $businessSetting->invoice_reminder_days_before ?? 7;

        // Get all available floors for filter dropdown
        $availableFloors = \App\Models\Kamar::where('owner_id', $ownerId)->distinct()->pluck('floor_number')->sort()->values();

        // Show ALL tenants - use pure Eloquent to avoid GROUP BY/DISTINCT issues
        $query = User::where('role', 'tenant')
            ->whereHas('tenantProfile', fn($q) => $q->where('owner_id', $ownerId))
            ->with(['tenantProfile', 'tenantTransactions', 'currentRoom.roomType', 'occupiedRoom.roomType']);

        // Filter by active status (has room or not) - check BOTH relationships
        if ($activeStatus && $activeStatus !== 'semua') {
            if ($activeStatus === 'aktif') {
                $query->where(function ($q) {
                    $q->whereHas('currentRoom')
                        ->orWhereHas('occupiedRoom');
                });
            } elseif ($activeStatus === 'tidak_aktif') {
                $query->whereDoesntHave('currentRoom')
                    ->whereDoesntHave('occupiedRoom');
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

        // Search by name, phone, or room number
        // Room search checks BOTH currentRoom and occupiedRoom (pivot) for multi-tenant support
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

        // Filter by payment status
        if ($paymentStatus && $paymentStatus !== 'semua') {
            // We need to filter based on the latest verified transaction's period_end_date
            $today = now()->startOfDay();
            $reminderEndDate = now()->addDays($reminderDays)->endOfDay();

            if ($paymentStatus === 'nunggak') {
                // Only tenants with active rooms who are overdue
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
                // Tenants whose contract expires within reminder days
                $query->whereHas('tenantTransactions', function ($q) use ($today, $reminderEndDate) {
                    $q->where('status', 'verified_by_owner')
                        ->whereBetween('period_end_date', [$today, $reminderEndDate]);
                });
            } elseif ($paymentStatus === 'lancar') {
                // Tenants with truly active contract (period_end_date > reminderEndDate)
                // And NO rejected or cancelled transactions
                $query->whereHas('tenantTransactions', function ($q) use ($reminderEndDate) {
                    $q->where('status', 'verified_by_owner')
                        ->where('period_end_date', '>', $reminderEndDate);
                })->whereDoesntHave('tenantTransactions', function ($q) {
                    $q->whereIn('status', ['rejected_by_admin', 'rejected_by_owner', 'cancelled_by_tenant']);
                });
            }
        }

        $viewMode = $request->get('view', 'list');
        $perPage = $viewMode === 'grid' ? 100 : 10;

        $tenants = $query->paginate($perPage)->appends($request->query());

        // Total Penghuni (active tenants with rooms assigned)
        $totalTenants = User::where('role', 'tenant')
            ->whereHas('tenantProfile', fn($q) => $q->where('owner_id', $ownerId))
            ->where(function ($q) {
                $q->whereHas('currentRoom')->orWhereHas('occupiedRoom');
            })
            ->count();

        // Penghuni Baru (joined in the last 30 days)
        $newTenants = User::where('role', 'tenant')
            ->whereHas('tenantProfile', fn($q) => $q->where('owner_id', $ownerId))
            ->where(function ($q) {
                $q->whereHas('currentRoom')->orWhereHas('occupiedRoom');
            })
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Get owner's reminder setting (H-? before contract ends)
        $owner = auth()->user()->resolveOwner();
        $businessSetting = $owner ? \App\Models\PemilikKos::where('owner_id', $owner->id)->first() : null;
        $reminderDays = $businessSetting->invoice_reminder_days_before ?? 7; // Default H-7 if not set

        // Kontrak Habis (tenants whose lease expires within H-? days based on owner setting)
        // Shows tenants who need to renew soon (within reminder period)
        $expiringContracts = \App\Models\Transaksi::where('owner_id', $ownerId)
            ->where('status', 'verified_by_owner')
            ->whereBetween('period_end_date', [
                now()->startOfDay(),
                now()->addDays($reminderDays)->endOfDay()
            ])
            ->distinct('penyewa_id')
            ->count('penyewa_id');

        // Nunggak (tenants whose lease already EXPIRED - period_end_date < today)
        // Only count tenants who still have an active room
        $delinquent = \App\Models\Transaksi::where('owner_id', $ownerId)
            ->where('status', 'verified_by_owner')
            ->where('period_end_date', '<', now()->startOfDay())
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('transaksi as t2')
                    ->whereColumn('t2.penyewa_id', 'transaksi.penyewa_id')
                    ->where('t2.status', 'verified_by_owner')
                    ->where('t2.period_end_date', '>=', now()->startOfDay());
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('kamar')
                    ->where(function ($q) {
                        $q->whereColumn('kamar.current_tenant_id', 'transaksi.penyewa_id')
                            ->orWhereExists(function ($q2) {
                                $q2->select(DB::raw(1))
                                    ->from('riwayat_penghuni_kamar')
                                    ->whereColumn('riwayat_penghuni_kamar.kamar_id', 'kamar.id')
                                    ->whereColumn('riwayat_penghuni_kamar.user_id', 'transaksi.penyewa_id')
                                    ->whereNull('riwayat_penghuni_kamar.check_out_date');
                            });
                    });
            })
            ->distinct('penyewa_id')
            ->count('penyewa_id');

        return view('admin.penyewa', [
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
     * Show tenant profile details
     */
    public function show(User $user)
    {
        if ($user->role !== 'tenant') {
            abort(404);
        }

        abort_unless($user->tenantProfile && $user->tenantProfile->owner_id === auth()->user()->ownerId(), 404);

        $user->load(['tenantProfile', 'currentRoom.roomType', 'occupiedRoom.roomType', 'tenantTransactions.paymentProofs']);

        return view('admin.biodata-penyewa', [
            'penyewa' => $user,
        ]);
    }

    /**
     * Verify tenant account
     */
    public function verify(Request $request, User $user)
    {
        if ($user->role !== 'tenant') {
            abort(404);
        }

        $user->tenantProfile()->update([
            'is_verified_by_admin' => true,
            'verified_at' => now(),
            'status' => 'active',
        ]);

        // Log activity
        \App\Services\LoggerService::log(
            'verify_tenant',
            "Verifikasi akun penyewa {$user->name}",
            $user
        );

        // Create notification for tenant
        Notification::create([
            'user_id' => $user->id,
            'type' => 'account_verified',
            'category' => 'info',
            'title' => 'Akun Berhasil Diverifikasi',
            'message' => 'Akun Anda telah diverifikasi oleh admin dan sekarang aktif.',
            'priority' => 'medium',
            'action_required' => false,
        ]);

        return back()->with('success', 'Akun penyewa berhasil diverifikasi');
    }

    /**
     * Send payment reminder to tenant
     */
    public function sendReminder(Request $request, User $user)
    {
        if ($user->role !== 'tenant') {
            abort(404);
        }

        // Log aktivitas pengiriman pengingat pembayaran

        \App\Services\LoggerService::log(
            'send_reminder',
            "Kirim pengingat pembayaran ke {$user->name}",
            $user
        );

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'payment_reminder',
            'category' => 'finance',
            'title' => 'Pengingat Pembayaran',
            'message' => 'Mohon segera lakukan pembayaran untuk kamar Anda.',
            'priority' => 'high',
            'action_required' => true,
        ]);

        return back()->with('success', 'Pengingat pembayaran telah dikirim');
    }

    /**
     * Checkout tenant from their room
     * Sets check_out_date on pivot, preserving history
     * Only allowed when lease has expired (period_end_date < today)
     */
    public function checkout(User $user)
    {
        // Ensure tenant has an active room
        $occupiedRoom = $user->occupiedRoom()->first();

        if (!$occupiedRoom) {
            return back()->with('error', 'Penyewa tidak memiliki kamar aktif.');
        }

        // Check if lease has expired - look at the latest verified transaction
        $latestTransaction = $user->tenantTransactions()
            ->where('status', 'verified_by_owner')
            ->orderBy('period_end_date', 'desc')
            ->first();

        if ($latestTransaction && $latestTransaction->period_end_date > now()) {
            $endDate = \Carbon\Carbon::parse($latestTransaction->period_end_date)->format('d M Y');
            return back()->with('error', "Masa sewa penyewa masih aktif sampai {$endDate}. Checkout hanya bisa dilakukan setelah masa sewa berakhir.");
        }

        // Set check_out_date on pivot table (soft removal - preserves history)
        // Using direct DB update because occupiedRoom() relation filters out records with check_out_date
        DB::table('riwayat_penghuni_kamar')
            ->where('user_id', $user->id)
            ->where('kamar_id', $occupiedRoom->id)
            ->whereNull('check_out_date')
            ->update(['check_out_date' => now()]);

        // Update room status using the pivot table as the source of truth.
        // We no longer write to the deprecated current_occupants / current_tenant_id columns.
        $room = \App\Models\Kamar::find($occupiedRoom->id);
        if ($room) {
            // Promote another active occupant into the legacy current_tenant_id slot
            // only if there's already someone there to keep backward-compat reads accurate.
            if ($room->current_tenant_id == $user->id) {
                $otherOccupant = $room->occupants()
                    ->where('user.id', '!=', $user->id)
                    ->whereNull('riwayat_penghuni_kamar.check_out_date')
                    ->first();

                $room->current_tenant_id = $otherOccupant?->id;
            }

            // Status is occupied only if pivot still has any active occupants.
            $room->status = $room->occupants()->count() > 0 ? ($room->isFull() ? 'occupied' : 'available') : 'available';

            $room->save();
        }

        // Log activity
        $owner = auth()->user()->resolveOwner();
        AdminActivityLog::create([
            'admin_id' => Auth::id(),
            'owner_id' => $owner?->id,
            'activity_type' => 'checkout_tenant',
            'activity_label' => "Checkout penyewa {$user->name} dari Kamar {$occupiedRoom->room_number}",
            'notes' => json_encode([
                'penyewa_id' => $user->id,
                'kamar_id' => $occupiedRoom->id,
                'room_number' => $occupiedRoom->room_number,
            ]),
        ]);

        return back()->with('success', "Penyewa {$user->name} berhasil di-checkout dari Kamar {$occupiedRoom->room_number}");
    }

    /**
     * Hapus penyewa (admin) — wajib menyertakan alasan. Owner kos otomatis diberi
     * notifikasi (lihat trait ManagesTenants::deleteTenant).
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->role !== 'tenant') {
            abort(404);
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:5|max:500',
        ], [
            'reason.required' => 'Alasan penghapusan penyewa wajib diisi.',
            'reason.min'      => 'Alasan minimal 5 karakter.',
        ]);

        // Scope: penyewa harus milik kos yang dikelola admin ini.
        if ((int) ($user->tenantProfile?->owner_id) !== $this->tenantOwnerId()) {
            abort(403);
        }

        // Blok hapus bila penyewa masih menempati kamar aktif — checkout dulu.
        if ($user->occupiedRoom()->exists()) {
            return back()->with('error', "Penyewa {$user->name} masih menempati kamar aktif. Lakukan checkout terlebih dahulu sebelum menghapus.");
        }

        $name = $user->name;
        $this->deleteTenant($user, $validated['reason']);

        return redirect()->route('admin.penyewa')
            ->with('success', "Penyewa {$name} telah dihapus. Pemilik kos telah diberi tahu.");
    }

    /** Generate/rotate link publik agar penyewa mengisi biodatanya sendiri (admin). */
    public function generateBiodataLink(User $user)
    {
        if ($user->role !== 'tenant') {
            abort(404);
        }
        if ((int) ($user->tenantProfile?->owner_id) !== $this->tenantOwnerId()) {
            abort(403);
        }
        $profile = $user->tenantProfile;
        if (! $profile) {
            return back()->with('error', 'Profil penyewa tidak ditemukan.');
        }
        $profile->form_token = \Illuminate\Support\Str::random(40);
        $profile->save();

        return back()
            ->with('biodata_link', route('public.biodata.edit', ['token' => $profile->form_token]))
            ->with('success', 'Link biodata dibuat. Salin & kirim ke penyewa untuk mengisi datanya.');
    }

    /** Halaman edit biodata penyewa oleh admin (isi/ubah data & upload dokumen). */
    public function editBiodata(User $user)
    {
        if ($user->role !== 'tenant') {
            abort(404);
        }
        if ((int) ($user->tenantProfile?->owner_id) !== $this->tenantOwnerId()) {
            abort(403);
        }
        $profile = $user->tenantProfile;
        if (! $profile) {
            abort(404);
        }
        $profile->load('user');

        return view('biodata-edit', [
            'layout' => 'layouts.admin',
            'penyewa' => $user,
            'profile' => $profile,
            'docTypes' => ['ktp', 'kartu_mahasiswa', 'ktp_ortu', 'kartu_keluarga', 'pas_foto', 'surat_pernyataan'],
            'updateRoute' => route('admin.penyewa.biodata.update', $user->id),
            'backRoute' => route('admin.penyewa.show', $user->id),
        ]);
    }

    /** Simpan biodata penyewa (admin). */
    public function updateBiodata(Request $request, User $user)
    {
        if ($user->role !== 'tenant') {
            abort(404);
        }
        if ((int) ($user->tenantProfile?->owner_id) !== $this->tenantOwnerId()) {
            abort(403);
        }
        $profile = $user->tenantProfile;
        if (! $profile) {
            abort(404);
        }
        $profile->load('user');
        $this->applyBiodata($profile, $request);

        return redirect()->route('admin.penyewa.show', $user->id)->with('success', 'Biodata penyewa berhasil diperbarui.');
    }
}
