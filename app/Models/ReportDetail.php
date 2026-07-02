<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_id',
        'section_name',
        'section_data',
        'summary_text',
    ];

    protected $casts = [
        'section_data' => 'array',
    ];

    /**
     * Get the generated report
     */
    public function generatedReport(): BelongsTo
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }
}
