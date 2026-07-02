<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipeKamar extends Model
{
    use HasFactory;

    protected $table = 'tipe_kamar';

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'facilities',
        'image_path',
        'gallery_images',
        'capacity',
        'price_per_month',
        'price_per_day',
        'unit_count',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'facilities' => 'array',
        'gallery_images' => 'array',
    ];

    /**
     * Get the owner of this room type
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get rooms of this type
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Kamar::class, 'tipe_kamar_id');
    }

    /**
     * Get rent price per person based on capacity
     */
    public function getRentPerPersonAttribute()
    {
        $capacity = $this->capacity ?? 1;
        if ($capacity > 1) {
            return $this->price_per_month / 2;
        }
        return $this->price_per_month;
    }

    /**
     * Get the user who created this
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
