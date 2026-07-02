<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemilikKos extends Model
{
    use HasFactory;

    protected $table = 'pemilik_kos';

    protected $fillable = [
        'owner_id',
        'late_payment_fine_per_day',
        'late_payment_tolerance_days',
        'invoice_due_day',
        'invoice_reminder_days_before',
        'invoice_reminder_enabled',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'boarding_house_name',
    ];

    protected $casts = [
        'late_payment_fine_per_day' => 'decimal:2',
        'invoice_reminder_enabled' => 'boolean',
    ];

    /**
     * Get the owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
