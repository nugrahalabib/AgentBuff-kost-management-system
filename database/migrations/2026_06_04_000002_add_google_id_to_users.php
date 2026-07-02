<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dukungan login Google (Socialite): tambah kolom google_id (unik, nullable).
 * Password TIDAK diubah — akun Google diberi password acak di controller,
 * jadi tidak perlu mengutak-atik skema kolom password (lebih aman saat deploy).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            if (! Schema::hasColumn('user', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            if (Schema::hasColumn('user', 'google_id')) {
                $table->dropColumn('google_id');
            }
        });
    }
};
