<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Token untuk link publik "isi biodata sendiri" oleh penyewa (tanpa login).
 * Owner/admin generate token → beri link ke penyewa → penyewa isi data & upload
 * dokumen → tersimpan otomatis ke profil penyewa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyewa', function (Blueprint $table) {
            if (! Schema::hasColumn('penyewa', 'form_token')) {
                $table->string('form_token', 64)->nullable()->unique()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penyewa', function (Blueprint $table) {
            if (Schema::hasColumn('penyewa', 'form_token')) {
                $table->dropColumn('form_token');
            }
        });
    }
};
