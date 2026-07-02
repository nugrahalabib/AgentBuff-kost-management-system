<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomOccupancyHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'kamar_id',
        'penyewa_id',
        'check_in_date',
        'check_out_date',
        'contract_start_date',
        'contract_end_date',
        'is_active',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the room
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    /**
     * Get the tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penyewa_id');
    }
}
