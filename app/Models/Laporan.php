<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';

    protected $fillable = [
        'owner_id',
        'admin_id',
        'report_type',
        'report_month',
        'report_year',
        'end_month', // Added
        'end_year',  // Added
        'title',
        'file_path_pdf',
        'file_path_excel',
        'file_size',
        'generated_at',
        'status',
        'sent_at',
        'sent_by',
        'viewed_at',
        'downloaded_at',
        'notes',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    /**
     * Get the owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the admin who generated
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the user who sent
     */
    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Get report details
     */
    public function reportDetails(): HasMany
    {
        return $this->hasMany(ReportDetail::class, 'laporan_id');
    }
}
