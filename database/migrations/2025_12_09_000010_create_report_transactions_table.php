<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_report_id')->constrained('financial_reports')->onDelete('cascade');
            $table->date('transaction_date');
            $table->string('reference_number'); // #INV/XI/001
            $table->string('category'); // Revenue, Maintenance, CAPEX, dll
            $table->string('category_detail'); // Pembayaran Sewa Kamar 101, etc
            $table->decimal('debit', 12, 2)->nullable(); // uang masuk
            $table->decimal('kredit', 12, 2)->nullable(); // uang keluar
            $table->decimal('balance', 12, 2); // saldo running
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('financial_report_id');
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_transactions');
    }
};
