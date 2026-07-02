<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('restrict');
            $table->string('report_type'); // financial_report, room_status_report, etc
            $table->integer('report_month');
            $table->integer('report_year');
            $table->string('title');
            
            // File management
            $table->string('file_path_pdf')->nullable();
            $table->string('file_path_excel')->nullable();
            $table->integer('file_size')->nullable();
            $table->timestamp('generated_at');
            
            // Submission tracking
            $table->enum('status', ['draft', 'sent', 'viewed', 'downloaded'])->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('owner_id');
            $table->index('admin_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
