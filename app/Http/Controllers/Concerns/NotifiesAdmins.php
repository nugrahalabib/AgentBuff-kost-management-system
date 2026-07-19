<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Notification;
use App\Models\User;

/**
 * Beri tahu admin saat OWNER mengubah data, supaya admin tidak kaget bila ada
 * data berubah. Hanya jalan bila aktornya OWNER (bukan admin sendiri), dan
 * TIDAK menyertakan nominal uang (admin tak berhak melihat keuangan kos).
 */
trait NotifiesAdmins
{
    protected function notifyAdminsOfChange(string $title, string $message, ?string $entityType = null, ?int $entityId = null): void
    {
        $actor = auth()->user();
        if (! $actor || $actor->role !== 'owner') {
            return;
        }

        $adminIds = User::where('role', 'admin')
            ->whereHas('adminProfile', fn ($q) => $q->where('owner_id', $actor->id))
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
