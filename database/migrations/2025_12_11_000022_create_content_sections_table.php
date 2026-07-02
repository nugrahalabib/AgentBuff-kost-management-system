<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('section_key')->unique(); // hero, gallery, facilities, contact
            $table->string('section_name');
            $table->text('content')->nullable(); // JSON untuk data kompleks
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('owner_id');
            $table->index('section_key');
        });

        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_section_id')->constrained('content_sections')->onDelete('cascade');
            $table->string('item_type'); // heading, description, image, facility_item, contact_info
            $table->string('label')->nullable();
            $table->text('value')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('content_section_id');
        });

        Schema::create('content_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_section_id')->constrained('content_sections')->onDelete('cascade');
            $table->string('category')->default('general'); // living_room, bedroom, kitchen, bathroom, workspace, outdoor
            $table->text('images'); // JSON array of image paths
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('content_section_id');
        });

        Schema::create('content_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_section_id')->constrained('content_sections')->onDelete('cascade');
            $table->string('icon')->nullable(); // icon class atau SVG identifier
            $table->string('facility_name');
            $table->text('description');
            $table->string('icon_color')->default('emerald'); // emerald, blue, amber, red, purple, green
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('content_section_id');
        });

        Schema::create('content_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_section_id')->constrained('content_sections')->onDelete('cascade');
            $table->string('contact_type'); // address, phone, email, maps_embed
            $table->text('contact_value');
            $table->string('label')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('content_section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_contacts');
        Schema::dropIfExists('content_facilities');
        Schema::dropIfExists('content_galleries');
        Schema::dropIfExists('content_items');
        Schema::dropIfExists('content_sections');
    }
};
