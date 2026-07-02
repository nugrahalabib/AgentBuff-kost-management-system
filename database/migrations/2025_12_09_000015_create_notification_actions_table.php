<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->enum('action_type', ['approve', 'reject', 'view', 'acknowledge']);
            $table->enum('action_taken', ['approve', 'reject', 'view', 'acknowledge'])->nullable();
            $table->foreignId('taken_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamp('taken_at')->nullable();
            $table->timestamps();

            $table->index('notification_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_actions');
    }
};
