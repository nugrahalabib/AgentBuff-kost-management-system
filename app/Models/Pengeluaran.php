<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';
    protected $fillable = [
        'owner_id',
        'type',
        'category',
        'amount',
        'date',
        'description',
        'proof_image',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
