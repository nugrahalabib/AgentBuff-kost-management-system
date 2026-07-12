<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambahkan tipe notifikasi yang sudah dipakai di kode tapi belum ada di enum
     * kolom notifications.type: 'payment_reminder' (Admin\PenyewaController::sendReminder)
     * dan 'account_verified' (Admin\PenyewaController::verify). Tanpa ini, endpoint
     * tersebut error "Data truncated for column 'type'" di MySQL strict mode.
     * Semua nilai lama dipertahankan.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'approval_request',
            'payment_received',
            'tenant_overdue',
            'contract_expiring',
            'account_pending',
            'account_verified',
            'maintenance_request',
            'payment_verified',
            'payment_reminder',
            'report_submitted',
            'payment_completed',
            'payment_rejected',
            'room_maintenance',
            'info'
        ) NOT NULL");
    }

    public function down(): void
    {
        // Enum reduction dilewati agar tidak memicu data truncation bila sudah ada
        // baris dengan tipe baru. Konsisten dengan migration enum sebelumnya.
    }
};
