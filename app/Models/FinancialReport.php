<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'report_month',
        'report_year',
        'gross_revenue',
        'opex_total',
        'capex_total',
        'noi_total',
        'profit_margin',
        'created_by',
    ];

    protected $casts = [
        'gross_revenue' => 'decimal:2',
        'opex_total' => 'decimal:2',
        'capex_total' => 'decimal:2',
        'noi_total' => 'decimal:2',
    ];

    /**
     * Get the owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the user who created
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get report transactions
     */
    public function reportTransactions(): HasMany
    {
        return $this->hasMany(ReportTransaksi::class);
    }
}
