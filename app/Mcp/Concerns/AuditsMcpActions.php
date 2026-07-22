<?php

namespace App\Mcp\Concerns;

use App\Models\Notification;
use App\Models\User;
use App\Services\LoggerService;
use Illuminate\Database\Eloquent\Model;
use Laravel\Mcp\Request;

/**
 * Audit & notifikasi untuk mutasi MCP.
 * - Log masuk AdminActivityLog (muncul di dashboard owner).
 * - Notifikasi owner untuk aksi penting.
 * - Notifikasi admin untuk perubahan operasional (tanpa nominal uang).
 */
trait AuditsMcpActions
{
    protected function logMcp(
        Request $request,
        string $activityType,
        string $description,
        ?Model $model = null,
        ?array $oldData = null,
        ?array $newData = null
    ): void {
        $actor = $request->user();
        if (! $actor) {
            return;
        }

        LoggerService::log(
            $activityType,
            rtrim($description) . ' (via AI Agent)',
            $model,
            $oldData,
            $newData,
            $actor
        );
    }

    protected function notifyOwnerMcp(
        int $ownerId,
        string $title,
        string $message,
        ?string $entityType = null,
        ?int $entityId = null,
        string $type = 'info',
        string $category = 'system',
        string $priority = 'medium'
    ): void {
        Notification::create([
            'user_id' => $ownerId,
            'type' => $type,
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'related_entity_type' => $entityType,
            'related_entity_id' => $entityId,
            'priority' => $priority,
        ]);
    }

    /**
     * Beri tahu admin kos (operasional saja — jangan sertakan nominal).
     */
    protected function notifyAdminsMcp(
        int $ownerId,
        string $title,
        string $message,
        ?string $entityType = null,
        ?int $entityId = null
    ): void {
        $adminIds = User::where('role', 'admin')
            ->whereHas('adminProfile', fn ($q) => $q->where('owner_id', $ownerId))
            ->pluck('id');

        foreach ($adminIds as $adminId) {
            Notification::create([
                'user_id' => $adminId,
                'type' => 'info',
                'category' => 'system',
                'title' => $title,
                'message' => $message,
                'related_entity_type' => $entityType,
                'related_entity_id' => $entityId,
                'priority' => 'low',
            ]);
        }
    }
}
