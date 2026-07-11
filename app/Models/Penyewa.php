<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penyewa extends Model
{
    use HasFactory;

    protected $table = 'penyewa';

    protected $fillable = [
        'user_id',
        'owner_id',
        'tenant_type',
        'phone',
        // Personal data
        'birth_place',
        'birth_date',
        'university',
        'enrollment_year',
        'faculty',
        'major',
        'student_card_number',
        'id_card_number',
        'id_card_photo_path',
        'address',
        'occupation',
        'emergency_contact_name',
        'emergency_contact_phone',
        // Guardian data
        'guardian_name',
        'guardian_birth_place',
        'guardian_birth_date',
        'guardian_occupation',
        'guardian_address',
        'guardian_id_card_number',
        'guardian_home_phone',
        'guardian_phone',
        // Documents (user editable)
        'documents',
        // NOTE: is_verified_by_admin, verified_at, status are intentionally NOT fillable
        // They must only be set explicitly via Admin verify flow.
        // See PenyewaController (Admin)::verify().
    ];

    protected $casts = [
        'documents' => 'array',
        'is_verified_by_admin' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Fields that should be hidden from default JSON / array serialization.
     * Prevents accidental leak via toJson(), API responses, or model events.
     * Add them back explicitly with ->makeVisible([...]) when needed.
     */
    protected $hidden = [
        'id_card_number',
        'id_card_photo_path',
        'guardian_id_card_number',
        'documents',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the profile is complete with all required fields
     */
    public function isComplete(): bool
    {
        // Required personal fields (must match the profile form)
        $requiredFields = [
            'phone',
            'birth_place',
            'birth_date',
            'id_card_number',
            'address',
        ];

        // Required guardian fields (serves as emergency contact)
        $requiredGuardianFields = [
            'guardian_name',
            'guardian_phone',
        ];

        // Check all required personal fields
        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        // Check all required guardian fields
        foreach ($requiredGuardianFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get list of missing required fields
     */
    public function getMissingFields(): array
    {
        $missing = [];
        
        $requiredFields = [
            'phone' => 'No. Telepon',
            'birth_place' => 'Tempat Lahir',
            'birth_date' => 'Tanggal Lahir',
            'id_card_number' => 'No. KTP',
            'address' => 'Alamat',
            'guardian_name' => 'Nama Wali',
            'guardian_phone' => 'No. HP Wali',
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($this->$field)) {
                $missing[] = $label;
            }
        }

        return $missing;
    }
}
