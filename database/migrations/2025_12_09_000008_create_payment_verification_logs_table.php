<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_verification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('verified_by')->constrained('users')->onDelete('restrict');
            $table->enum('verification_type', ['admin', 'owner']); // siapa yang verifikasi
            $table->enum('status', ['approved', 'rejected']); // hasil verifikasi
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('verified_at');
            $table->timestamps();

            $table->index('transaction_id');
            $table->index('verified_by');
            $table->index('verification_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_verification_logs');
    }
};
