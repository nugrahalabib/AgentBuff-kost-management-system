<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LatePaymentFine extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id',
        'penyewa_id',
        'amount',
        'days_overdue',
        'calculated_at',
        'is_paid',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'calculated_at' => 'datetime',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    /**
     * Get the tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penyewa_id');
    }
}
