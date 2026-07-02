<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Penyewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * Show the tenant dashboard with profile data
     */
    public function dashboard()
    {
        $user = Auth::user();
        $profile = $user->tenantProfile ?? new Penyewa();

        // Get notifications for this user
        $notifications = Notification::forUser($user->id)
            ->active()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $transactions = \App\Models\Transaksi::where('penyewa_id', $user->id)
            ->with(['room.roomType', 'paymentProofs'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get rented room (source of truth: user->activeRoom which supports multi-tenant pivot)
        $rentedRoom = $user->activeRoom;
        if ($rentedRoom) {
            $rentedRoom->load('roomType');
        }

        // Calculate billing and reminder for lease expiry
        $pendingBill = 0;
        $leaseReminder = null;
        $daysUntilExpiry = null;
        $monthsOverdue = 0;

        // Get configurable reminder days from owner's business settings (default 7)
        $owner = \App\Models\User::where('role', 'owner')->first();
        $businessSettings = $owner ? $owner->businessSettings : null;
        $reminderDaysBefore = ($businessSettings && $businessSettings->invoice_reminder_days_before) 
            ? (int)$businessSettings->invoice_reminder_days_before 
            : 7;

        // Initialize lease dates with null
        $userLeaseStartDate = null;
        $userLeaseEndDate = null;

        if ($rentedRoom) {
            // 1. Determine Start Date (Strictly from Pivot)
            $userLeaseStartDate = $rentedRoom->pivot->check_in_date 
                ? \Carbon\Carbon::parse($rentedRoom->pivot->check_in_date) 
                : null;

            // 2. Determine End Date (From Latest Verified Transaction)
            // Filter transactions already fetched above (collection)
            $latestVerifiedTx = $transactions->where('status', 'verified_by_owner')->sortByDesc('period_end_date')->first();
            
            if ($latestVerifiedTx && $latestVerifiedTx->period_end_date) {
                $userLeaseEndDate = \Carbon\Carbon::parse($latestVerifiedTx->period_end_date);
            } else {
                // Fallback to room's generic end date if no transaction found (legacy/manual)
                $userLeaseEndDate = $rentedRoom->lease_end_date 
                    ? \Carbon\Carbon::parse($rentedRoom->lease_end_date) 
                    : null;
            }

            // Calculate expiry based on User's specific End Date
            if ($userLeaseEndDate) {
                $today = now()->startOfDay();
                $leaseEnd = $userLeaseEndDate->clone()->startOfDay();
                $daysUntilExpiry = (int)$today->diffInDays($leaseEnd, false); // negative if overdue
                
                $pricePerMonth = $rentedRoom->roomType->rent_per_person ?? $rentedRoom->price_per_month ?? 0;

                // If lease is expiring within configured days OR already overdue
                if ($daysUntilExpiry <= $reminderDaysBefore) {
                    if ($daysUntilExpiry < 0) {
                        // Overdue
                        $monthsOverdue = ceil(abs($daysUntilExpiry) / 30);
                        $pendingBill = $pricePerMonth * $monthsOverdue;
                        $leaseReminder = [
                            'type' => 'overdue',
                            'title' => 'Sewa Kamar Telat ' . $monthsOverdue . ' Bulan!',
                            'message' => 'Masa sewa Anda sudah berakhir ' . abs($daysUntilExpiry) . ' hari yang lalu. Segera perpanjang untuk menghindari denda.',
                            'days' => abs($daysUntilExpiry),
                            'months_overdue' => $monthsOverdue,
                        ];
                    } elseif ($daysUntilExpiry == 0) {
                        // Today
                        $pendingBill = $pricePerMonth;
                        $leaseReminder = [
                            'type' => 'today',
                            'title' => 'Sewa Kamar Habis Hari Ini!',
                            'message' => 'Masa sewa Anda berakhir hari ini. Segera perpanjang untuk melanjutkan tinggal.',
                            'days' => 0,
                            'months_overdue' => 0,
                        ];
                    } else {
                        // Warning
                        $pendingBill = $pricePerMonth;
                        $leaseReminder = [
                            'type' => 'warning',
                            'title' => 'Sewa Kamar Hampir Habis!',
                            'message' => 'Masa sewa Anda akan berakhir dalam ' . $daysUntilExpiry . ' hari lagi. Segera perpanjang sebelum tanggal jatuh tempo.',
                            'days' => $daysUntilExpiry,
                            'months_overdue' => 0,
                        ];
                    }
                }
            }
        }

        // Check for rejected payment transactions
        $rejectedTransaction = \App\Models\Transaksi::where('penyewa_id', $user->id)
            ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
            ->first();

        return view('penyewa.dashboard', [
            'profile' => $profile,
            'notifications' => $notifications,
            'transaksi' => $transactions,
            'kamarDisewa' => $rentedRoom,
            'pendingBill' => $pendingBill,
            'leaseReminder' => $leaseReminder,
            'daysUntilExpiry' => $daysUntilExpiry,
            'monthsOverdue' => $monthsOverdue,
            'transaksiDitolak' => $rejectedTransaction,
            'userLeaseStartDate' => $userLeaseStartDate,
            'userLeaseEndDate' => $userLeaseEndDate,
        ]);
    }

    /**
     * Dismiss a notification
     */
    public function dismissNotification($id)
    {
        $user = Auth::user();
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }

        // Check if notification can be dismissed
        if (!$notification->isDismissible()) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tagihan tidak dapat dihapus sampai pembayaran selesai'
            ], 403);
        }

        $notification->update(['status' => 'archived']);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus'
        ]);
    }

    /**
     * Update personal data
     */
    public function updatePersonal(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\-]+$/'],
            'tenant_type' => 'required|in:mahasiswa,non_mahasiswa',
            'birth_place' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\-]+$/'],
            'birth_date' => 'required|date',
            'id_card_number' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            // Mahasiswa fields
            'university' => ['required_if:tenant_type,mahasiswa', 'nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\-]+$/'],
            'enrollment_year' => 'required_if:tenant_type,mahasiswa|nullable|string|max:10',
            'faculty'    => ['required_if:tenant_type,mahasiswa', 'nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\-]+$/'],
            'major'      => ['required_if:tenant_type,mahasiswa', 'nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\-]+$/'],
            'student_card_number' => 'required_if:tenant_type,mahasiswa|nullable|string|max:50',
            // Non-mahasiswa fields
            'occupation' => ['required_if:tenant_type,non_mahasiswa', 'nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\-]+$/'],
        ]);

        $user = Auth::user();

        // Update user name
        $user->update(['name' => $request->name]);

        // Update or create tenant profile
        $profile = $user->tenantProfile ?? new Penyewa(['user_id' => $user->id]);

        $profileData = [
            'tenant_type' => $request->tenant_type,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'id_card_number' => $request->id_card_number,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->tenant_type === 'mahasiswa') {
            $profileData['university'] = $request->university;
            $profileData['enrollment_year'] = $request->enrollment_year;
            $profileData['faculty'] = $request->faculty;
            $profileData['major'] = $request->major;
            $profileData['student_card_number'] = $request->student_card_number;
            // Clear non-mahasiswa fields
            $profileData['occupation'] = null;
        } else {
            $profileData['occupation'] = $request->occupation;
            // Clear mahasiswa fields
            $profileData['university'] = null;
            $profileData['enrollment_year'] = null;
            $profileData['faculty'] = null;
            $profileData['major'] = null;
            $profileData['student_card_number'] = null;
        }

        $profile->fill($profileData);
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Data personal berhasil disimpan!',
            'profile' => $profile
        ]);
    }

    /**
     * Update guardian data
     */
    public function updateGuardian(Request $request)
    {
        $request->validate([
            'guardian_name' => 'required|string|max:255',
            'guardian_birth_place' => 'required|string|max:255',
            'guardian_birth_date' => 'required|date',
            'guardian_occupation' => 'required|string|max:255',
            'guardian_address' => 'required|string',
            'guardian_id_card_number' => 'required|string|max:50',
            'guardian_home_phone' => 'nullable|string|max:20',
            'guardian_phone' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $profile = $user->tenantProfile ?? new Penyewa(['user_id' => $user->id]);

        $profile->fill([
            'guardian_name' => $request->guardian_name,
            'guardian_birth_place' => $request->guardian_birth_place,
            'guardian_birth_date' => $request->guardian_birth_date,
            'guardian_occupation' => $request->guardian_occupation,
            'guardian_address' => $request->guardian_address,
            'guardian_id_card_number' => $request->guardian_id_card_number,
            'guardian_home_phone' => $request->guardian_home_phone,
            'guardian_phone' => $request->guardian_phone,
        ]);
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Data wali berhasil disimpan!',
            'profile' => $profile
        ]);
    }

    /**
     * Stream a tenant identity document to authorized viewers.
     *
     * $userId is the tenant whose document is being viewed; $type is the
     * document key (ktp, ktp_ortu, etc.). Owners and admins can view any
     * tenant's documents; tenants can only view their own.
     */
    public function viewDocument(Request $request, $userId, $type)
    {
        $viewer = Auth::user();
        $isOwner = $viewer->role === 'owner';
        $isAdmin = $viewer->role === 'admin';
        $isSelf = $viewer->role === 'tenant' && (int) $viewer->id === (int) $userId;

        if (!$isOwner && !$isAdmin && !$isSelf) {
            abort(403);
        }

        $allowedTypes = ['ktp', 'kartu_mahasiswa', 'ktp_ortu', 'kartu_keluarga', 'pas_foto', 'surat_pernyataan'];
        if (!in_array($type, $allowedTypes, true)) {
            abort(404);
        }

        $tenantUser = \App\Models\User::with('tenantProfile')->findOrFail($userId);
        $documents = $tenantUser->tenantProfile?->documents ?? [];
        $path = $documents[$type] ?? null;

        if (!$path) {
            abort(404);
        }

        // Prefer private disk; fall back to legacy public storage.
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return response()->file(Storage::disk($disk)->path($path));
            }
        }

        abort(404);
    }

    /**
     * Update documents/attachments
     */
    public function updateDocuments(Request $request)
    {
        $user = Auth::user();
        $profile = $user->tenantProfile ?? new Penyewa(['user_id' => $user->id]);
        $existingDocs = $profile->documents ?? [];
        $tenantType = $profile->tenant_type ?? 'mahasiswa';

        // Build validation rules - required only if not already uploaded
        $rules = [
            'ktp' => (!isset($existingDocs['ktp']) ? 'required|' : 'nullable|') . 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'ktp_ortu' => (!isset($existingDocs['ktp_ortu']) ? 'required|' : 'nullable|') . 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'kartu_keluarga' => (!isset($existingDocs['kartu_keluarga']) ? 'required|' : 'nullable|') . 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pas_foto' => (!isset($existingDocs['pas_foto']) ? 'required|' : 'nullable|') . 'file|mimes:jpg,jpeg,png|max:2048',
            'surat_pernyataan' => (!isset($existingDocs['surat_pernyataan']) ? 'required|' : 'nullable|') . 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        // Kartu mahasiswa only required for mahasiswa type
        if ($tenantType === 'mahasiswa') {
            $rules['kartu_mahasiswa'] = (!isset($existingDocs['kartu_mahasiswa']) ? 'required|' : 'nullable|') . 'file|mimes:jpg,jpeg,png,pdf|max:2048';
        } else {
            $rules['kartu_mahasiswa'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        }

        $request->validate($rules);

        $documents = $existingDocs;

        $fileTypes = ['ktp', 'kartu_mahasiswa', 'ktp_ortu', 'kartu_keluarga', 'pas_foto', 'surat_pernyataan'];

        foreach ($fileTypes as $type) {
            if ($request->hasFile($type)) {
                // Delete old file if exists. Try both disks because earlier uploads
                // may still live on 'public' from before the private-storage migration.
                if (isset($documents[$type])) {
                    foreach (['local', 'public'] as $disk) {
                        if (Storage::disk($disk)->exists($documents[$type])) {
                            Storage::disk($disk)->delete($documents[$type]);
                        }
                    }
                }

                // Store new file on private disk; serve via authenticated route.
                $path = $request->file($type)->store('tenant-documents/' . $user->id, 'local');
                $documents[$type] = $path;
            }
        }

        $profile->documents = $documents;
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diupload!',
            'documents' => $documents
        ]);
    }
}
