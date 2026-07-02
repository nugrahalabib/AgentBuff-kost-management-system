<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('id_card_photo_path')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->json('documents')->nullable(); // foto KTP, dll
            $table->boolean('is_verified_by_admin')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->enum('status', ['new', 'active', 'inactive', 'checkout'])->default('new');
            $table->timestamps();

            $table->index('user_id');
            $table->index('is_verified_by_admin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_profiles');
    }
};
