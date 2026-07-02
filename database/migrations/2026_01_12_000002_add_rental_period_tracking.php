<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add lease period tracking to rooms table
        Schema::table('rooms', function (Blueprint $table) {
            $table->date('lease_start_date')->nullable()->after('current_tenant_id');
            $table->date('lease_end_date')->nullable()->after('lease_start_date');
        });

        // Add duration and period tracking to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('duration_months')->nullable()->after('amount');
            $table->date('period_start_date')->nullable()->after('duration_months');
            $table->date('period_end_date')->nullable()->after('period_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['lease_start_date', 'lease_end_date']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['duration_months', 'period_start_date', 'period_end_date']);
        });
    }
};
