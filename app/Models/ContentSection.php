<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class ContentSection extends Model
{
    protected $fillable = [
        'owner_id',
        'section_key',
        'section_name',
        'content',
        'is_active',
    ];

    protected $casts = [
        'content' => 'json',
        'is_active' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ContentItem::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(ContentGallery::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(ContentFacility::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ContentContact::class);
    }

    public static function getOrCreateSection(string $key, string $name)
    {
        return self::firstOrCreate(
            ['section_key' => $key],
            [
                'owner_id' => Auth::id(),
                'section_name' => $name,
                'is_active' => true,
            ]
        );
    }
}
