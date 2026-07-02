<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Disable tenant account verification: default to true and update existing records.
     */
    public function up(): void
    {
        // Update all existing tenant profiles to be verified
        DB::table('penyewa')->update(['is_verified_by_admin' => true]);

        // Change the default value to true for new records
        Schema::table('penyewa', function (Blueprint $table) {
            $table->boolean('is_verified_by_admin')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyewa', function (Blueprint $table) {
            $table->boolean('is_verified_by_admin')->default(false)->change();
        });
    }
};
