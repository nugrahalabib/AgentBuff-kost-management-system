<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordHistory extends Model
{
    use HasFactory;

    protected $table = 'password_history';

    protected $fillable = [
        'user_id',
        'old_password_hash',
        'changed_at',
        'changed_from_ip',
        'changed_from_user_agent',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
