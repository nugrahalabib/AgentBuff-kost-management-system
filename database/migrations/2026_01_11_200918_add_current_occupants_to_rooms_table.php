<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('current_occupants')->default(0)->after('current_tenant_id');
        });

        // Set current_occupants = 1 for rooms that already have a tenant
        \DB::statement('UPDATE rooms SET current_occupants = 1 WHERE current_tenant_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('current_occupants');
        });
    }
};
