<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'payment_rejected' to enum type
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('approval_request', 'payment_received', 'tenant_overdue', 'contract_expiring', 'account_pending', 'maintenance_request', 'payment_verified', 'report_submitted', 'payment_completed', 'payment_rejected', 'general') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('approval_request', 'payment_received', 'tenant_overdue', 'contract_expiring', 'account_pending', 'maintenance_request', 'payment_verified', 'report_submitted', 'payment_completed', 'general') NOT NULL");
    }
};
