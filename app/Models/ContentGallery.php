<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentGallery extends Model
{
    protected $fillable = [
        'content_section_id',
        'category',
        'images',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'images' => 'json',
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ContentSection::class, 'content_section_id');
    }
}
