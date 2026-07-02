<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'owner_id',
        'penyewa_id',
        'kamar_id',
        'amount',
        'duration_months',
        'period_start_date',
        'period_end_date',
        'payment_method',
        'reference_number',
        'payment_date',
        'due_date',
        'invoice_number',
        'status',
        'admin_verified_at',
        'admin_verified_by',
        'admin_notes',
        'owner_verified_at',
        'owner_verified_by',
        'owner_notes',
        'provisional_amount',
        'final_amount',
        'sender_bank',
        'sender_name',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'due_date' => 'date',
        'period_start_date' => 'date',
        'period_end_date' => 'date',
        'admin_verified_at' => 'datetime',
        'owner_verified_at' => 'datetime',
    ];

    /**
     * Get the owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penyewa_id');
    }

    /**
     * Get the room
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    /**
     * Get the admin who verified
     */
    public function adminVerifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_verified_by');
    }

    /**
     * Get the owner who verified
     */
    public function ownerVerifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_verified_by');
    }

    /**
     * Get payment proofs
     */
    public function paymentProofs(): HasMany
    {
        return $this->hasMany(BuktiBayar::class, 'transaksi_id');
    }

    /**
     * Get verification logs
     */
    public function verificationLogs(): HasMany
    {
        return $this->hasMany(PaymentVerificationLog::class, 'transaksi_id');
    }

    /**
     * Get late payment fines
     */
    public function latePaymentFines(): HasMany
    {
        return $this->hasMany(LatePaymentFine::class, 'transaksi_id');
    }
}
