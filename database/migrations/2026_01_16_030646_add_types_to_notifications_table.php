<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'room_maintenance' and 'info' to allowed types
        // Preserving existing types: 
        // approval_request, payment_received, tenant_overdue, contract_expiring, account_pending, 
        // maintenance_request, payment_verified, report_submitted, payment_completed, payment_rejected
        
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM(
            'approval_request',
            'payment_received',
            'tenant_overdue',
            'contract_expiring',
            'account_pending',
            'maintenance_request',
            'payment_verified',
            'report_submitted',
            'payment_completed',
            'payment_rejected',
            'room_maintenance',
            'info'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Reverting enum changes is risky if data exists with new types.
        // We generally don't revert enum additions in production environments to avoid data truncation.
        // If needed, we would list only the original types here.
    }
};
