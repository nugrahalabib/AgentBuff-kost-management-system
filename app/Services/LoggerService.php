<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class LoggerService
{
    /**
     * Field names that should never end up in audit log payloads.
     * Stripped from old_data / new_data before persisting.
     */
    private const PII_FIELDS = [
        'password',
        'password_confirmation',
        'remember_token',
        'id_card_number',
        'id_card_photo_path',
        'guardian_id_card_number',
        'documents',
        'bank_account_number',
        'bank_account_name',
    ];

    /**
     * Strip PII fields from a data array before persisting it to audit logs.
     */
    private static function stripPii(?array $data): ?array
    {
        if (!$data) {
            return $data;
        }
        return array_diff_key($data, array_flip(self::PII_FIELDS));
    }

    /**
     * Log an admin activity
     *
     * @param string $activityType Type of activity (e.g., 'create', 'update', 'delete', 'verify')
     * @param string $description Human readable description
     * @param Model|null $model The model being affected
     * @param array|null $oldData Original data before change
     * @param array|null $newData New data after change
     * @param User|null $actor Aktor eksplisit (MCP Sanctum / failed-login); default Auth::user()
     * @return AdminActivityLog|null
     */
    public static function log(
        string $activityType,
        string $description,
        ?Model $model = null,
        ?array $oldData = null,
        ?array $newData = null,
        ?User $actor = null
    ) {
        // Strip sensitive fields before they ever touch the audit log table.
        $oldData = self::stripPii($oldData);
        $newData = self::stripPii($newData);

        $user = $actor ?? Auth::user();
        if (! $user) {
            return null;
        }
        
        // Try to find the owner_id
        // If current user is owner, use their ID
        // If current user is admin, we need to find who owns the system/resource
        // For simplicity in this project context where Admin manages Owner's property:
        // We might need to adjust this logic if it's a multi-tenant system with relation
        
        $ownerId = null;
        
        if ($user->role === 'owner') {
            $ownerId = $user->id;
        } else {
            // Admin, tenant, atau peran lain: tentukan owner dari model / profil /
            // owner tunggal sistem. Jangan pernah insert owner_id null (kolom NOT NULL).
            // Check if user has an owner relation, or if the model has an owner
            // For now, let's assume specific business logic or null
            // In the AdminManagementController, it used auth()->id() as owner when owner logged in.
            // When Admin logs in, we need to capture who they are working for.
            
            // Attempt to get owner from the model if possible
            if ($model && method_exists($model, 'owner')) {
                $ownerId = $model->owner_id;
            } elseif ($model && isset($model->owner_id)) {
                $ownerId = $model->owner_id;
            }
             
            // Fallback: owner dari profil admin (admin bekerja untuk satu kos).
            if (!$ownerId) {
                $ownerId = $user->adminProfile?->owner_id ?? null;
            }

            // Multi-tenant: TIDAK ada fallback "owner satu-satunya" — menebak owner
            // berisiko salah-atribusi antar kos. Bila owner tak ketemu, lewati log.
            // CRITICAL: kalau owner_id tetap tidak ketemu, JANGAN insert (kolom NOT NULL).
            // Lewati audit log secara diam-diam agar tidak menggagalkan operasi utama
            // (mis. konfirmasi pembayaran penyewa) — cukup catat ke log sistem.
            if (!$ownerId) {
                \Illuminate\Support\Facades\Log::warning("Skipping audit log: owner_id tak ditemukan untuk user {$user->id} (peran {$user->role}), aktivitas {$activityType}.");
                return null;
            }
        }

        // Calculate changes if not provided but data is

        // Calculate changes if not provided but data is
        $changes = null;
        if ($oldData && $newData) {
            $changes = array_diff_assoc($newData, $oldData);
        }

        return AdminActivityLog::create([
            'admin_id' => $user->id,
            'owner_id' => $ownerId, // Can be null if generic system action
            'activity_type' => $activityType,
            'activity_label' => ucfirst(str_replace('_', ' ', $activityType)),
            'model_name' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'old_data' => $oldData,
            'new_data' => $newData,
            'changes' => $changes,
            'notes' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
