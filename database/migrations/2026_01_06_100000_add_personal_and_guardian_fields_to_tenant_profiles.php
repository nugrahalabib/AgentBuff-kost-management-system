<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenant_profiles', function (Blueprint $table) {
            // Data Personal
            $table->string('birth_place')->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('university')->nullable()->after('birth_date');
            $table->string('enrollment_year')->nullable()->after('university');
            $table->string('faculty')->nullable()->after('enrollment_year');
            $table->string('major')->nullable()->after('faculty');
            $table->string('student_card_number')->nullable()->after('major');

            // Data Wali
            $table->string('guardian_name')->nullable()->after('emergency_contact_phone');
            $table->string('guardian_birth_place')->nullable()->after('guardian_name');
            $table->date('guardian_birth_date')->nullable()->after('guardian_birth_place');
            $table->string('guardian_occupation')->nullable()->after('guardian_birth_date');
            $table->text('guardian_address')->nullable()->after('guardian_occupation');
            $table->string('guardian_id_card_number')->nullable()->after('guardian_address');
            $table->string('guardian_home_phone')->nullable()->after('guardian_id_card_number');
            $table->string('guardian_phone')->nullable()->after('guardian_home_phone');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_profiles', function (Blueprint $table) {
            // Data Personal
            $table->dropColumn([
                'birth_place',
                'birth_date',
                'university',
                'enrollment_year',
                'faculty',
                'major',
                'student_card_number',
            ]);

            // Data Wali
            $table->dropColumn([
                'guardian_name',
                'guardian_birth_place',
                'guardian_birth_date',
                'guardian_occupation',
                'guardian_address',
                'guardian_id_card_number',
                'guardian_home_phone',
                'guardian_phone',
            ]);
        });
    }
};
