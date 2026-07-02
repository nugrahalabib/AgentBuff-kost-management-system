<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->integer('report_month'); // 12
            $table->integer('report_year'); // 2025
            $table->decimal('gross_revenue', 12, 2)->default(0); // Rp 45.200.000
            $table->decimal('opex_total', 12, 2)->default(0); // Rp 8.500.000
            $table->decimal('capex_total', 12, 2)->default(0); // Rp 2.000.000
            $table->decimal('noi_total', 12, 2)->default(0); // Net Operating Income
            $table->decimal('profit_margin', 5, 2)->nullable(); // persentase
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->unique(['owner_id', 'report_month', 'report_year']);
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_reports');
    }
};
