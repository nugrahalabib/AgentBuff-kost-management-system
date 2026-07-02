<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type')->default('image');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('uploaded_at');
            $table->enum('verified_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('verified_notes')->nullable();
            $table->timestamps();

            $table->index('transaction_id');
            $table->index('verified_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
    }
};
