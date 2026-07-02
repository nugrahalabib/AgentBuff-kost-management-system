<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', [
                'approval_request',
                'payment_received',
                'tenant_overdue',
                'contract_expiring',
                'account_pending',
                'maintenance_request',
                'payment_verified',
                'report_submitted'
            ]);
            $table->enum('category', ['urgent', 'finance', 'system', 'info'])->default('info');
            $table->string('title');
            $table->text('message');
            $table->string('related_entity_type')->nullable(); // maintenance_request, transaction, tenant, room, etc
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('action_required')->default(false);
            $table->enum('status', ['unread', 'read', 'archived'])->default('unread');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
            $table->index('category');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
