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
        Schema::table('transactions', function (Blueprint $table) {
            // Explicitly change the column definition to be nullable
            $table->unsignedBigInteger('room_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Revert back to not null (be careful if there are null values now)
            // Ideally we would ensure no nulls before this, but for dev env it's fine
            $table->unsignedBigInteger('room_id')->nullable(false)->change();
        });
    }
};
