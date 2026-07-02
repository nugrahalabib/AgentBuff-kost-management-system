<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccountController extends Controller
{
    /**
     * Display tenant account management page
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $search = $request->get('search', '');

        // Verification disabled - always show ALL tenants
        $query = User::where('role', 'tenant')
            ->with('tenantProfile');

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $tenants = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.akun-penyewa', [
            'dataPenyewa' => $tenants,
            'tab' => $tab,
            'search' => $search,
            'verifiedCount' => 0,
            'unverifiedCount' => 0,
        ]);
    }

    /**
     * Delete tenant account
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'tenant') {
            abort(404);
        }

        // Menghapus akun penyewa otomatis melepas huniannya: baris hunian
        // (riwayat_penghuni_kamar) ikut terhapus via cascade FK, lalu status kamar
        // di-reset di bawah. Tidak lagi diblokir meski penyewa sedang menempati kamar.
        $userName = $user->name;
        $userId = $user->id;

        try {
            DB::beginTransaction();

            // Kamar yang sedang dihuni penyewa ini — disimpan agar statusnya bisa
            // di-reset setelah akun (dan baris pivot hunian) terhapus.
            $occupiedKamarIds = DB::table('riwayat_penghuni_kamar')
                ->where('user_id', $userId)
                ->whereNull('check_out_date')
                ->pluck('kamar_id')
                ->all();

            // Delete all related records in correct order to avoid FK constraints
            // 1. Delete session if using database
            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $userId)->delete();
            }

            // 2. Delete records that reference the user
            if (Schema::hasTable('notification_actions')) {
                DB::table('notification_actions')->where('taken_by', $userId)->delete();
            }
            if (Schema::hasTable('payment_verification_logs')) {
                DB::table('payment_verification_logs')->where('verified_by', $userId)->delete();
            }
            if (Schema::hasTable('bukti_bayar')) {
                DB::table('bukti_bayar')->where('uploaded_by', $userId)->delete();
            }
            if (Schema::hasTable('late_payment_fines')) {
                DB::table('late_payment_fines')->where('penyewa_id', $userId)->delete();
            }
            if (Schema::hasTable('room_occupancy_histories')) {
                DB::table('room_occupancy_histories')->where('penyewa_id', $userId)->delete();
            }
            if (Schema::hasTable('transaksi')) {
                DB::table('transaksi')->where('penyewa_id', $userId)->delete();
            }
            if (Schema::hasTable('admin_activity_logs')) {
                DB::table('admin_activity_logs')->where('admin_id', $userId)->delete();
            }
            if (Schema::hasTable('notifications')) {
                DB::table('notifications')->where('user_id', $userId)->delete();
            }
            if (Schema::hasTable('password_history')) {
                DB::table('password_history')->where('user_id', $userId)->delete();
            }

            // 3. Update rooms to set current_tenant_id to null
            DB::table('kamar')->where('current_tenant_id', $userId)->update(['current_tenant_id' => null]);

            // 3. Delete tenant profile
            DB::table('penyewa')->where('user_id', $userId)->delete();

            // 4. Finally delete the user
            DB::table('user')->where('id', $userId)->delete();

            // 5. Lepas hunian: baris riwayat_penghuni_kamar penyewa ini sudah terhapus
            //    via cascade FK. Reset status kamar yang tadi dihuni agar kembali
            //    'available' bila tak ada penghuni aktif lagi (mendukung kamar multi-orang).
            foreach ($occupiedKamarIds as $kamarId) {
                $room = \App\Models\Kamar::find($kamarId);
                if (!$room) {
                    continue;
                }
                $activeOccupants = $room->occupants()->count();
                $room->status = $activeOccupants > 0 ? ($room->isFull() ? 'occupied' : 'available') : 'available';
                if ($activeOccupants === 0) {
                    $room->lease_start_date = null;
                    $room->lease_end_date = null;
                }
                $room->save();
            }

            // Log activity
            \App\Services\LoggerService::log(
                'delete',
                'Menghapus akun penyewa: ' . $userName,
                null // Model deleted
            );

            DB::commit();

            return redirect()->route('admin.akun-penyewa')
                ->with('success', 'Akun ' . $userName . ' berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Account destroy failed', ['error' => $e->getMessage()]);
            return redirect()->route('admin.akun-penyewa')
                ->with('error', 'Gagal menghapus akun. Silakan coba lagi atau hubungi support.');
        }
    }
}
