<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Optimize VARCHAR(255) columns to realistic lengths.
     *
     * KEPT at 255: file paths, password hashes, framework tables (cache, jobs, migrations)
     */
    public function up(): void
    {
        // ── users ──
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->string('email', 100)->change();
            $table->string('role', 20)->default('tenant')->change();
            $table->string('position', 50)->nullable()->change();
            // password: keep 255 (hash length varies)
        });

        // ── admin_profiles ──
        Schema::table('admin_profiles', function (Blueprint $table) {
            $table->string('position', 50)->change();
            $table->string('phone', 20)->nullable()->change();
        });

        // ── admin_activity_logs ──
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->string('activity_type', 50)->change();
            $table->string('activity_label', 100)->change();
            $table->string('model_name', 100)->nullable()->change();
            $table->string('ip_address', 45)->nullable()->change();
        });

        // ── ai_chat_sessions ──
        Schema::table('ai_chat_sessions', function (Blueprint $table) {
            $table->string('title', 150)->nullable()->change();
        });

        // ── business_settings ──
        Schema::table('business_settings', function (Blueprint $table) {
            $table->string('boarding_house_name', 100)->change();
            $table->string('bank_name', 50)->nullable()->change();
            $table->string('bank_account_number', 30)->nullable()->change();
            $table->string('bank_account_name', 100)->nullable()->change();
        });

        // ── content_contacts ──
        Schema::table('content_contacts', function (Blueprint $table) {
            $table->string('contact_type', 30)->change();
            $table->string('label', 100)->nullable()->change();
        });

        // ── content_facilities ──
        Schema::table('content_facilities', function (Blueprint $table) {
            $table->string('icon', 100)->nullable()->change();
            $table->string('facility_name', 100)->change();
            $table->string('icon_color', 30)->change();
        });

        // ── content_galleries ──
        Schema::table('content_galleries', function (Blueprint $table) {
            $table->string('category', 50)->change();
            $table->string('title', 150)->nullable()->change();
        });

        // ── content_items ──
        Schema::table('content_items', function (Blueprint $table) {
            $table->string('item_type', 50)->change();
            $table->string('label', 100)->nullable()->change();
            // image_path: keep 255 (file path)
        });

        // ── content_sections ──
        Schema::table('content_sections', function (Blueprint $table) {
            $table->string('section_key', 50)->change();
            $table->string('section_name', 100)->change();
        });

        // ── expenses ──
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('category', 50)->change();
            // proof_image: keep 255 (file path)
        });

        // ── generated_reports ──
        Schema::table('generated_reports', function (Blueprint $table) {
            $table->string('report_type', 50)->change();
            $table->string('title', 150)->change();
            // file_path_pdf & file_path_excel: keep 255 (file path)
        });

        // ── maintenance_requests ──
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->string('title', 150)->change();
        });

        // ── notifications ──
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('title', 150)->change();
            $table->string('related_entity_type', 100)->nullable()->change();
        });

        // ── password_history ──
        Schema::table('password_history', function (Blueprint $table) {
            // old_password_hash: keep 255 (hash)
            $table->string('changed_from_ip', 45)->nullable()->change();
        });

        // ── payment_proofs ──
        Schema::table('payment_proofs', function (Blueprint $table) {
            // file_path: keep 255 (file path)
            $table->string('file_type', 20)->nullable()->change();
        });

        // ── payment_verification_logs ──
        Schema::table('payment_verification_logs', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->change();
        });

        // ── report_details ──
        Schema::table('report_details', function (Blueprint $table) {
            $table->string('section_name', 100)->change();
        });

        // ── report_transactions ──
        Schema::table('report_transactions', function (Blueprint $table) {
            $table->string('reference_number', 50)->change();
            $table->string('category', 50)->change();
            $table->string('category_detail', 100)->change();
        });

        // ── rooms ──
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('room_number', 10)->change();
        });

        // ── room_types ──
        Schema::table('room_types', function (Blueprint $table) {
            $table->string('name', 100)->change();
            // image_path: keep 255 (file path)
        });

        // ── tenant_profiles ──
        Schema::table('tenant_profiles', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->change();
            $table->string('birth_place', 100)->nullable()->change();
            $table->string('university', 100)->nullable()->change();
            $table->string('enrollment_year', 4)->nullable()->change();
            $table->string('faculty', 100)->nullable()->change();
            $table->string('major', 100)->nullable()->change();
            $table->string('student_card_number', 30)->nullable()->change();
            $table->string('id_card_number', 20)->nullable()->change();
            // id_card_photo_path: keep 255 (file path)
            $table->string('occupation', 100)->nullable()->change();
            $table->string('emergency_contact_name', 100)->nullable()->change();
            $table->string('emergency_contact_phone', 20)->nullable()->change();
            $table->string('guardian_name', 100)->nullable()->change();
            $table->string('guardian_birth_place', 100)->nullable()->change();
            $table->string('guardian_occupation', 100)->nullable()->change();
            $table->string('guardian_id_card_number', 20)->nullable()->change();
            $table->string('guardian_home_phone', 20)->nullable()->change();
            $table->string('guardian_phone', 20)->nullable()->change();
        });

        // ── transactions ──
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method', 30)->nullable()->change();
            $table->string('sender_bank', 50)->nullable()->change();
            $table->string('sender_name', 100)->nullable()->change();
            $table->string('reference_number', 50)->change();
            $table->string('invoice_number', 50)->nullable()->change();
        });
    }

    /**
     * Revert all columns back to VARCHAR(255).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('email', 255)->change();
            $table->string('role', 255)->change();
            $table->string('position', 255)->nullable()->change();
        });

        Schema::table('admin_profiles', function (Blueprint $table) {
            $table->string('position', 255)->change();
            $table->string('phone', 255)->nullable()->change();
        });

        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->string('activity_type', 255)->change();
            $table->string('activity_label', 255)->change();
            $table->string('model_name', 255)->nullable()->change();
            $table->string('ip_address', 255)->nullable()->change();
        });

        Schema::table('ai_chat_sessions', function (Blueprint $table) {
            $table->string('title', 255)->nullable()->change();
        });

        Schema::table('business_settings', function (Blueprint $table) {
            $table->string('boarding_house_name', 255)->change();
            $table->string('bank_name', 255)->nullable()->change();
            $table->string('bank_account_number', 255)->nullable()->change();
            $table->string('bank_account_name', 255)->nullable()->change();
        });

        Schema::table('content_contacts', function (Blueprint $table) {
            $table->string('contact_type', 255)->change();
            $table->string('label', 255)->nullable()->change();
        });

        Schema::table('content_facilities', function (Blueprint $table) {
            $table->string('icon', 255)->nullable()->change();
            $table->string('facility_name', 255)->change();
            $table->string('icon_color', 255)->change();
        });

        Schema::table('content_galleries', function (Blueprint $table) {
            $table->string('category', 255)->change();
            $table->string('title', 255)->nullable()->change();
        });

        Schema::table('content_items', function (Blueprint $table) {
            $table->string('item_type', 255)->change();
            $table->string('label', 255)->nullable()->change();
        });

        Schema::table('content_sections', function (Blueprint $table) {
            $table->string('section_key', 255)->change();
            $table->string('section_name', 255)->change();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('category', 255)->change();
        });

        Schema::table('generated_reports', function (Blueprint $table) {
            $table->string('report_type', 255)->change();
            $table->string('title', 255)->change();
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->string('title', 255)->change();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('title', 255)->change();
            $table->string('related_entity_type', 255)->nullable()->change();
        });

        Schema::table('password_history', function (Blueprint $table) {
            $table->string('changed_from_ip', 255)->nullable()->change();
        });

        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->string('file_type', 255)->nullable()->change();
        });

        Schema::table('payment_verification_logs', function (Blueprint $table) {
            $table->string('ip_address', 255)->nullable()->change();
        });

        Schema::table('report_details', function (Blueprint $table) {
            $table->string('section_name', 255)->change();
        });

        Schema::table('report_transactions', function (Blueprint $table) {
            $table->string('reference_number', 255)->change();
            $table->string('category', 255)->change();
            $table->string('category_detail', 255)->change();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->string('room_number', 255)->change();
        });

        Schema::table('room_types', function (Blueprint $table) {
            $table->string('name', 255)->change();
        });

        Schema::table('tenant_profiles', function (Blueprint $table) {
            $table->string('phone', 255)->nullable()->change();
            $table->string('birth_place', 255)->nullable()->change();
            $table->string('university', 255)->nullable()->change();
            $table->string('enrollment_year', 255)->nullable()->change();
            $table->string('faculty', 255)->nullable()->change();
            $table->string('major', 255)->nullable()->change();
            $table->string('student_card_number', 255)->nullable()->change();
            $table->string('id_card_number', 255)->nullable()->change();
            $table->string('occupation', 255)->nullable()->change();
            $table->string('emergency_contact_name', 255)->nullable()->change();
            $table->string('emergency_contact_phone', 255)->nullable()->change();
            $table->string('guardian_name', 255)->nullable()->change();
            $table->string('guardian_birth_place', 255)->nullable()->change();
            $table->string('guardian_occupation', 255)->nullable()->change();
            $table->string('guardian_id_card_number', 255)->nullable()->change();
            $table->string('guardian_home_phone', 255)->nullable()->change();
            $table->string('guardian_phone', 255)->nullable()->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method', 255)->nullable()->change();
            $table->string('sender_bank', 255)->nullable()->change();
            $table->string('sender_name', 255)->nullable()->change();
            $table->string('reference_number', 255)->change();
            $table->string('invoice_number', 255)->nullable()->change();
        });
    }
};
