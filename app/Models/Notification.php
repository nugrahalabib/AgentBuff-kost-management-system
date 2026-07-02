<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'category',
        'title',
        'message',
        'related_entity_type',
        'related_entity_id',
        'priority',
        'action_required',
        'status',
        'read_at',
    ];

    protected $casts = [
        'action_required' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related entity (polymorphic)
     */
    public function relatedEntity()
    {
        return $this->morphTo('related', 'related_entity_type', 'related_entity_id');
    }

    /**
     * Get notification actions
     */
    public function notificationActions(): HasMany
    {
        return $this->hasMany(NotificationAction::class);
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    /**
     * Mark as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'status' => 'unread',
            'read_at' => null,
        ]);
    }

    /**
     * Check if notification can be dismissed by tenant
     * Overdue payment and payment_rejected notifications cannot be dismissed
     */
    public function isDismissible(): bool
    {
        return !in_array($this->type, ['tenant_overdue', 'payment_rejected']);
    }

    /**
     * Scope for user notifications
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for active (non-archived) notifications
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['unread', 'read']);
    }
}
