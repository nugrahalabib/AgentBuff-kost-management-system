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
        // Modify the status enum to include cancelled_by_tenant
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending_verification', 'verified_by_admin', 'verified_by_owner', 'rejected_by_admin', 'rejected_by_owner', 'completed', 'cancelled_by_tenant') DEFAULT 'pending_verification'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending_verification', 'verified_by_admin', 'verified_by_owner', 'rejected_by_admin', 'rejected_by_owner', 'completed') DEFAULT 'pending_verification'");
    }
};
