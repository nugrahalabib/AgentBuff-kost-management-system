<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generated_report_id')->constrained('generated_reports')->onDelete('cascade');
            $table->string('section_name'); // Pendapatan, Pengeluaran, Okupansi, dll
            $table->json('section_data'); // data detail per section
            $table->text('summary_text')->nullable();
            $table->timestamps();

            $table->index('generated_report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_details');
    }
};
