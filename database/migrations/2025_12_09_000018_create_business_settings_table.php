<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->unique()->constrained('users')->onDelete('cascade');
            
            // Denda Keterlambatan
            $table->decimal('late_payment_fine_per_day', 12, 2)->default(0);
            $table->integer('late_payment_tolerance_days')->default(3);
            
            // Siklus Penagihan
            $table->integer('invoice_due_day')->default(5); // tanggal ke-5 setiap bulan
            $table->integer('invoice_reminder_days_before')->default(7); // kirim WA H-7
            $table->boolean('invoice_reminder_enabled')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
