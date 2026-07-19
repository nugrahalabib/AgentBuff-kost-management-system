<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemilik_kos', function (Blueprint $table) {
            if (! Schema::hasColumn('pemilik_kos', 'brand_color')) {
                // Kunci palet warna dasar (lihat config/brand_colors.php). Default emerald.
                $table->string('brand_color', 20)->default('emerald')->after('boarding_house_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pemilik_kos', function (Blueprint $table) {
            if (Schema::hasColumn('pemilik_kos', 'brand_color')) {
                $table->dropColumn('brand_color');
            }
        });
    }
};
