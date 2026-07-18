<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'user';

    /**
     * Resolusi owner (pemilik kos) yang menaungi user ini — inti model multi-tenant:
     * - role 'owner'  -> dirinya sendiri
     * - role 'admin'  -> owner yang membuat akun admin ini (admin.owner_id)
     * - lainnya       -> null
     *
     * Dinamai resolveOwner() (bukan owner()) agar tidak bentrok dengan magic
     * relationship Eloquent saat diakses sebagai properti.
     */
    public function resolveOwner(): ?User
    {
        if ($this->role === 'owner') {
            return $this;
        }
        if ($this->role === 'admin') {
            return $this->adminProfile?->owner;
        }
        return null;
    }

    /** Id owner yang menaungi user ini (lihat resolveOwner()). */
    public function ownerId(): ?int
    {
        if ($this->role === 'owner') {
            return $this->id;
        }
        if ($this->role === 'admin') {
            return $this->adminProfile?->owner_id;
        }
        return null;
    }

    /** Apakah user ini seorang pengelola kos (owner atau admin). */
    public function isManager(): bool
    {
        return in_array($this->role, ['owner', 'admin'], true);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'role',
        'status',
        // room_id removed - Source of Truth is now kamar.current_tenant_id
        'password',
        'last_login_at',
    ];

    /**
     * Get position from admin_profiles table (normalized).
     * Only admin users have a position.
     */
    public function getPositionAttribute()
    {
        return $this->adminProfile?->position;
    }

    /**
     * Append computed attributes to JSON/array serialization.
     */
    protected $appends = ['position'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    // ===== Relationships =====

    /**
     * Get tenant profile
     */
    public function tenantProfile(): HasOne
    {
        return $this->hasOne(Penyewa::class);
    }

    /**
     * Get admin profile
     */
    public function adminProfile(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Get rooms owned by this user (for owner)
     */
    public function ownedRooms(): HasMany
    {
        return $this->hasMany(Kamar::class, 'owner_id');
    }

    // room() method removed - use currentRoom() instead
    // Source of Truth is now kamar.current_tenant_id

    /**
     * Get room currently occupied by this user (via current_tenant_id - backward compat)
     */
    public function currentRoom(): HasOne
    {
        return $this->hasOne(Kamar::class, 'current_tenant_id');
    }

    /**
     * Get room occupied by this user via riwayat_penghuni_kamar pivot table
     * This is the PRIMARY way to check if tenant has a room for multi-tenant support
     */
    /** Email placeholder internal (penyewa tanpa email asli) — jangan ditampilkan. */
    public function hasPlaceholderEmail(): bool
    {
        return str_ends_with((string) $this->email, '@internal.local');
    }

    public function occupiedRoom()
    {
        return $this->belongsToMany(Kamar::class, 'riwayat_penghuni_kamar', 'user_id', 'kamar_id')
            ->withPivot(['check_in_date', 'check_out_date'])
            ->whereNull('riwayat_penghuni_kamar.check_out_date') // Only active occupancies
            ->withTimestamps();
    }

    /**
     * Get ALL rooms ever occupied by this user (History)
     * Used for Reports to find inactive tenants
     */
    public function historyRooms()
    {
        return $this->belongsToMany(Kamar::class, 'riwayat_penghuni_kamar', 'user_id', 'kamar_id')
            ->withPivot(['check_in_date', 'check_out_date'])
            ->withTimestamps();
    }

    /**
     * Get the active room for this tenant
     * Checks riwayat_penghuni_kamar first, then falls back to currentRoom
     */
    public function getActiveRoomAttribute()
    {
        // Check riwayat_penghuni_kamar pivot first (primary source for multi-tenant)
        // Check riwayat_penghuni_kamar pivot first (primary source for multi-tenant)
        // Use loaded relation if available to avoid extra query
        if ($this->relationLoaded('occupiedRoom')) {
            $roomFromPivot = $this->occupiedRoom->first();
        } else {
            $roomFromPivot = $this->occupiedRoom()->first();
        }

        if ($roomFromPivot) {
            return $roomFromPivot;
        }
        
        // Fallback to currentRoom (backward compat)
        return $this->currentRoom;
    }

    /**
     * Get transactions as owner
     */
    public function ownerTransactions(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'owner_id');
    }

    /**
     * Get transactions as tenant
     */
    public function tenantTransactions(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'penyewa_id');
    }

    /**
     * Get notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get activity logs (as admin)
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(AdminActivityLog::class, 'admin_id');
    }

    /**
     * Get business settings (as owner)
     */
    public function businessSettings(): HasOne
    {
        return $this->hasOne(PemilikKos::class, 'owner_id');
    }

    /**
     * Get password history
     */
    public function passwordHistory(): HasMany
    {
        return $this->hasMany(PasswordHistory::class);
    }

    /**
     * Get the first room where this user is an occupant
     * Uses activeRoom accessor which checks both currentRoom and occupiedRoom (pivot)
     */
    public function getFirstRoom()
    {
        return $this->activeRoom;
    }

    /**
     * Get payment status label accessor
     * Returns: Lancar, Telat Bayar, Segera Habis, or -
     */
    public function getPaymentStatusLabelAttribute()
    {
        // Only tenants with active rooms have a payment status
        if (!$this->activeRoom) {
            return '-';
        }

        $today = now()->startOfDay();
        $reminderDays = 7; // Could be fetched from settings if needed
        $reminderEndDate = now()->addDays($reminderDays)->endOfDay();

        $lastVerifiedTrx = $this->tenantTransactions()
            ->where('status', 'verified_by_owner')
            ->orderByDesc('period_end_date')
            ->first();

        if (!$lastVerifiedTrx) {
            return '-';
        }

        $periodEnd = \Carbon\Carbon::parse($lastVerifiedTrx->period_end_date);

        if ($periodEnd < $today) {
            return 'Telat Bayar';
        } elseif ($periodEnd <= $reminderEndDate) {
            return 'Segera Habis'; 
        }

        return 'Lancar';
    }
}
