<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('name'); // VIP AC, Standard A, Ekonomi LL.4
            $table->text('description')->nullable();
            $table->json('facilities')->nullable(); // AC, TV, KM Dalam, Water Heater
            $table->decimal('price_per_month', 12, 2);
            $table->decimal('price_per_day', 12, 2)->nullable();
            $table->integer('unit_count')->default(0); // total unit type ini
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index('owner_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
