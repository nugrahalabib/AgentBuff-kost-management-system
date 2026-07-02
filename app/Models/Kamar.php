<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamar';

    protected $fillable = [
        'owner_id',
        'tipe_kamar_id',
        'room_number',
        'floor_number',
        'current_tenant_id',
        'current_occupants',
        'lease_start_date',
        'lease_end_date',
        'status',
        'price_per_month',
        'notes',
    ];

    protected $casts = [
        'lease_start_date' => 'date',
        'lease_end_date' => 'date',
    ];

    /**
     * Get the owner of this room
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the room type
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(TipeKamar::class, 'tipe_kamar_id');
    }

    /**
     * Get the current tenant (DEPRECATED: Use occupants() instead)
     * Kept for backward compatibility but data should be in riwayat_penghuni_kamar
     */
    public function currentTenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_tenant_id');
    }

    /**
     * Get all occupants of this room (Primary Source of Truth)
     * For single rooms, this will just return a collection of 1 user.
     */
    public function occupants()
    {
        return $this->belongsToMany(User::class, 'riwayat_penghuni_kamar', 'kamar_id', 'user_id')
            ->withPivot(['check_in_date', 'check_out_date'])
            ->whereNull('riwayat_penghuni_kamar.check_out_date') // Only active occupants
            ->withTimestamps();
    }

    /**
     * Check if a specific user is occupying this room
     */
    public function isOccupiedBy($userId)
    {
        return $this->occupants()->where('user.id', $userId)->exists();
    }

    /**
     * Get rent price per person based on capacity
     */
    public function getRentPerPersonAttribute()
    {
        $capacity = $this->roomType->capacity ?? 1;
        if ($capacity > 1) {
            return $this->price_per_month / 2; // Fixed logic as per user request: "dibagi dua"
        }
        return $this->price_per_month;
    }

    /**
     * Get room status histories
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(RoomStatusHistory::class, 'kamar_id');
    }

    /**
     * Get occupancy histories
     */
    public function occupancyHistories(): HasMany
    {
        return $this->hasMany(RoomOccupancyHistory::class, 'kamar_id');
    }

    /**
     * Get transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'kamar_id');
    }

    /**
     * Get maintenance requests
     */
    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'kamar_id');
    }

    /**
     * Scope to get rooms with available slots
     * Uses subquery to count actual active occupants from pivot table for accuracy
     */
    public function scopeHasAvailableSlot($query)
    {
        return $query->where('status', '!=', 'maintenance')
            ->whereRaw('(
                SELECT COUNT(*) FROM riwayat_penghuni_kamar 
                WHERE riwayat_penghuni_kamar.kamar_id = kamar.id 
                AND riwayat_penghuni_kamar.check_out_date IS NULL
            ) < (
                SELECT capacity FROM tipe_kamar 
                WHERE tipe_kamar.id = kamar.tipe_kamar_id
            )');
    }

    /**
     * Check if room has available slots
     */
    /**
     * Check if room has available slots
     * Uses strict count of active occupants for accuracy
     */
    public function hasAvailableSlot(): bool
    {
        $capacity = $this->roomType->capacity ?? 1;
        // Count actual active occupants from relationship (source of truth)
        return $this->occupants()->count() < $capacity;
    }

    /**
     * Check if room is full
     */
    public function isFull(): bool
    {
        $capacity = $this->roomType->capacity ?? 1;
        // Count actual active occupants from relationship
        return $this->occupants()->count() >= $capacity;
    }
}
