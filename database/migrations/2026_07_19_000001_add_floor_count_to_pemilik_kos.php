<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jumlah lantai per kos — tiap kos beda-beda, jadi jangan hardcode 4.
 * Dipakai untuk mengisi dropdown/tab lantai secara dinamis (owner & admin).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemilik_kos', function (Blueprint $table) {
            if (! Schema::hasColumn('pemilik_kos', 'floor_count')) {
                $table->unsignedTinyInteger('floor_count')->default(4)->after('boarding_house_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pemilik_kos', function (Blueprint $table) {
            if (Schema::hasColumn('pemilik_kos', 'floor_count')) {
                $table->dropColumn('floor_count');
            }
        });
    }
};
