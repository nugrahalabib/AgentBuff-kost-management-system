<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'financial_report_id',
        'transaction_date',
        'reference_number',
        'category',
        'category_detail',
        'debit',
        'kredit',
        'balance',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    /**
     * Get the financial report
     */
    public function financialReport(): BelongsTo
    {
        return $this->belongsTo(FinancialReport::class);
    }
}
