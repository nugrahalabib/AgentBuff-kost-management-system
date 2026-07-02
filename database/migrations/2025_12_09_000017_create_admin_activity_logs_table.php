<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('activity_type'); // input_transaksi, update_status, hapus_data, approve_payment
            $table->string('activity_label'); // tag di UI
            $table->string('model_name')->nullable(); // Transaction, Room, Tenant
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_data')->nullable(); // snapshot sebelum diubah
            $table->json('new_data')->nullable(); // snapshot sesudah diubah
            $table->json('changes')->nullable(); // field apa yang diubah
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('admin_id');
            $table->index('owner_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
