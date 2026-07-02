<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminManagementController extends Controller
{
    /**
     * Display admin management page with admin list and audit logs
     */
    public function index(Request $request)
    {
        $owner = auth()->user();

        $admins = User::where('role', 'admin')
            ->with('adminProfile')
            ->orderBy('status', 'desc') // active first
            ->orderBy('last_login_at', 'desc')
            ->get();

        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo   = $request->get('date_to', now()->format('Y-m-d'));

        // Get activity logs filtered by date range (excluding owner's own actions)
        $activityLogs = AdminActivityLog::with(['admin.adminProfile', 'owner'])
            ->where('owner_id', $owner->id)
            ->where('admin_id', '!=', $owner->id)
            ->dateRange($dateFrom, $dateTo . ' 23:59:59')
            ->recent()
            ->get();

        return view('pemilik-kos.manajemen-akses', [
            'admins' => $admins,
            'activityLogs' => $activityLogs,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Store new admin user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'position' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'position.required' => 'Posisi wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        try {
            DB::beginTransaction();

            $admin = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'admin',
                'password' => Hash::make($validated['password']),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Create admin profile (relasi admin → owner)
            $admin->adminProfile()->create([
                'owner_id' => auth()->id(),
                'position' => $validated['position'],
            ]);

            // Log activity using LoggerService
            \App\Services\LoggerService::log(
                'create',
                'Membuat akun admin baru: ' . $admin->name,
                $admin
            );
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin baru berhasil ditambahkan',
                'admin' => $admin
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Admin store failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan admin. Silakan coba lagi atau hubungi support.',
            ], 500);
        }
    }

    /**
     * Update admin profile
     */
    public function update(Request $request, User $user)
    {
        // Ensure user is admin
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'User bukan admin'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email,' . $user->id,
            'position' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            $user->load('adminProfile');
            $oldData = [
                'name' => $user->name,
                'email' => $user->email,
                'position' => $user->adminProfile?->position,
                'status' => $user->status,
            ];
            
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Update position in admin_profiles (normalized)
            $user->adminProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['position' => $validated['position']]
            );

            $user->load('adminProfile');
            $newData = [
                'name' => $user->name,
                'email' => $user->email,
                'position' => $user->adminProfile?->position,
                'status' => $user->status,
            ];

            // Log activity
            \App\Services\LoggerService::log(
                'update',
                'Mengupdate profil admin: ' . $user->name,
                $user,
                $oldData,
                $newData
            );
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil admin berhasil diupdate',
                'admin' => $user->load('adminProfile')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Admin update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal update profil. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Toggle admin status (active/inactive)
     */
    public function toggleStatus(Request $request, User $user)
    {
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'User bukan admin'
            ], 403);
        }

        try {
            DB::beginTransaction();
            
            $oldStatus = $user->status;
            $newStatus = $oldStatus === 'active' ? 'inactive' : 'active';
            
            $user->update(['status' => $newStatus]);

            // Log activity
            \App\Services\LoggerService::log(
                'update_status',
                $newStatus === 'active' 
                    ? 'Mengaktifkan akses admin: ' . $user->name
                    : 'Menonaktifkan akses admin: ' . $user->name,
                $user,
                ['status' => $oldStatus],
                ['status' => $newStatus]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status admin berhasil diubah',
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Admin toggleStatus failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Reset admin password
     */
    public function resetPassword(Request $request, User $user)
    {
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'User bukan admin'
            ], 403);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            // Log activity
            \App\Services\LoggerService::log(
                'reset_password',
                'Reset password untuk admin: ' . $user->name,
                $user
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Admin resetPassword failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal reset password. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Soft delete admin account
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'User bukan admin'
            ], 403);
        }

        try {
            DB::beginTransaction();
            
            $adminName = $user->name;
            
            // Set status inactive first
            $user->update(['status' => 'inactive']);
            
            // Cleanup dependencies that might prevent deletion due to FK constraints
            $user->activityLogs()->delete(); // admin_id is restricted
            $user->passwordHistory()->delete();
            $user->notifications()->delete();
            if ($user->adminProfile) {
                $user->adminProfile()->delete();
            }
            
            // Then delete user
            $user->delete();

            // Log activity
            \App\Services\LoggerService::log(
                'delete',
                'Menghapus akun admin: ' . $adminName,
                null // Model is deleted
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Admin destroy failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus admin. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Export audit log to PDF
     */
    public function exportAuditLog(Request $request)
    {
        $owner = auth()->user();
        
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        
        $logs = AdminActivityLog::with(['admin', 'owner'])
            ->where('owner_id', $owner->id)
            ->where('admin_id', '!=', $owner->id)
            ->dateRange($dateFrom, $dateTo . ' 23:59:59')
            ->recent()
            ->get();
        
        $pdf = Pdf::loadView('pdf.audit-log', [
            'activityLogs' => $logs,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'pemilik' => $owner
        ]);
        
        // Log activity
        \App\Services\LoggerService::log(
            'export',
            'Mengekspor log audit oleh: ' . $owner->name,
            $owner
        );
        
        return $pdf->download('audit-log-' . now()->format('Y-m-d') . '.pdf');
    }
}
