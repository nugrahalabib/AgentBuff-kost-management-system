<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BuktiBayar extends Model
{
    use HasFactory;

    protected $table = 'bukti_bayar';

    protected $fillable = [
        'transaksi_id',
        'file_path',
        'file_type',
        'uploaded_by',
        'uploaded_at',
        'verified_status',
        'verified_notes',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Get the transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    /**
     * Get the user who uploaded
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * URL to view this payment proof.
     *
     * New uploads live on the private 'local' disk and are served via an
     * authenticated controller. Legacy uploads (pre-fix) may still live on
     * the 'public' disk; we serve those via the public storage URL so old
     * data keeps rendering, but new uploads are never publicly accessible.
     */
    public function getProofUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        // New private storage path: serve via authenticated route.
        if (Storage::disk('local')->exists($this->file_path)) {
            return route('payment-proof.view', ['proof' => $this->id]);
        }

        // Legacy public storage path: serve directly (backward compat only).
        if (Storage::disk('public')->exists($this->file_path)) {
            return asset('storage/' . $this->file_path);
        }

        return null;
    }
}
