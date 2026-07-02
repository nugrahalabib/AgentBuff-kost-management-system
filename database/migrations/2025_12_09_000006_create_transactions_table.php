<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('restrict');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable(); // bank_transfer, cash, etc
            $table->string('reference_number')->unique(); // #INV/XI/001
            $table->date('payment_date');
            $table->date('due_date');
            $table->string('invoice_number')->nullable();
            
            // Verification workflow
            $table->enum('status', ['pending_verification', 'verified_by_admin', 'verified_by_owner', 'rejected_by_admin', 'rejected_by_owner', 'completed'])->default('pending_verification');
            $table->timestamp('admin_verified_at')->nullable();
            $table->foreignId('admin_verified_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->text('admin_notes')->nullable();
            $table->timestamp('owner_verified_at')->nullable();
            $table->foreignId('owner_verified_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->text('owner_notes')->nullable();
            
            // Amount tracking
            $table->decimal('provisional_amount', 12, 2)->nullable(); // untuk "sementara" sebelum owner verify
            $table->decimal('final_amount', 12, 2)->nullable(); // setelah owner verify
            
            $table->timestamps();

            $table->index('owner_id');
            $table->index('tenant_id');
            $table->index('room_id');
            $table->index('status');
            $table->index('payment_date');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
