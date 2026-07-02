<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('room_type_id')->constrained('room_types')->onDelete('restrict');
            $table->string('room_number'); // 101, 102, 201, 202, etc
            $table->integer('floor_number'); // derived dari room_number (101->1, 201->2)
            $table->foreignId('current_tenant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->decimal('price_per_month', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['owner_id', 'room_number']);
            $table->index('owner_id');
            $table->index('floor_number');
            $table->index('status');
            $table->index('current_tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
