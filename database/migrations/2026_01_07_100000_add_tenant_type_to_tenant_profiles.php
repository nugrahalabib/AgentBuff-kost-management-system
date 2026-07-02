<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenant_profiles', function (Blueprint $table) {
            $table->enum('tenant_type', ['mahasiswa', 'non_mahasiswa'])->default('mahasiswa')->after('user_id');
            $table->string('occupation')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_profiles', function (Blueprint $table) {
            $table->dropColumn(['tenant_type', 'occupation']);
        });
    }
};
