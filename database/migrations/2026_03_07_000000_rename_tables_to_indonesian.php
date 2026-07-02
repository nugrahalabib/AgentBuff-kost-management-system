<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename tables and FK columns to indonesian names.
     */
    public function up(): void
    {
        // ============================================================
        // PHASE 1: Drop FK constraints on columns that will be renamed
        // ============================================================

        // rooms.room_type_id → tipe_kamar_id
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['room_type_id']);
        });

        // transactions.room_id → kamar_id
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
        });

        // room_occupants.room_id → kamar_id
        Schema::table('room_occupants', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
        });

        // maintenance_requests.room_id → kamar_id
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
        });

        // room_status_histories.room_id → kamar_id
        Schema::table('room_status_histories', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
        });

        // room_occupancy_histories.room_id → kamar_id
        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
        });

        // payment_proofs.transaction_id → transaksi_id
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
        });

        // payment_verification_logs.transaction_id → transaksi_id
        Schema::table('payment_verification_logs', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
        });

        // late_payment_fines.transaction_id → transaksi_id
        // late_payment_fines.tenant_id → penyewa_id
        Schema::table('late_payment_fines', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['tenant_id']);
        });

        // report_details.generated_report_id → laporan_id
        Schema::table('report_details', function (Blueprint $table) {
            $table->dropForeign(['generated_report_id']);
        });

        // transactions.tenant_id → penyewa_id
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });

        // rooms.current_tenant_id (FK references users, which will be renamed)
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['current_tenant_id']);
        });

        // room_occupancy_histories.tenant_id → penyewa_id
        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });

        // ============================================================
        // PHASE 2: Rename FK columns (tables still have old names)
        // ============================================================

        Schema::table('rooms', function (Blueprint $table) {
            $table->renameColumn('room_type_id', 'tipe_kamar_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('room_id', 'kamar_id');
        });

        Schema::table('room_occupants', function (Blueprint $table) {
            $table->renameColumn('room_id', 'kamar_id');
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->renameColumn('room_id', 'kamar_id');
        });

        Schema::table('room_status_histories', function (Blueprint $table) {
            $table->renameColumn('room_id', 'kamar_id');
        });

        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->renameColumn('room_id', 'kamar_id');
        });

        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->renameColumn('transaction_id', 'transaksi_id');
        });

        Schema::table('payment_verification_logs', function (Blueprint $table) {
            $table->renameColumn('transaction_id', 'transaksi_id');
        });

        Schema::table('late_payment_fines', function (Blueprint $table) {
            $table->renameColumn('transaction_id', 'transaksi_id');
        });

        Schema::table('report_details', function (Blueprint $table) {
            $table->renameColumn('generated_report_id', 'laporan_id');
        });

        // tenant_id → penyewa_id renames
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('tenant_id', 'penyewa_id');
        });

        Schema::table('late_payment_fines', function (Blueprint $table) {
            $table->renameColumn('tenant_id', 'penyewa_id');
        });

        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->renameColumn('tenant_id', 'penyewa_id');
        });

        // ============================================================
        // PHASE 3: Rename tables
        // ============================================================

        Schema::rename('users', 'user');
        Schema::rename('admin_profiles', 'admin');
        Schema::rename('tenant_profiles', 'penyewa');
        Schema::rename('transactions', 'transaksi');
        Schema::rename('room_types', 'tipe_kamar');
        Schema::rename('rooms', 'kamar');
        Schema::rename('room_occupants', 'riwayat_penghuni_kamar');
        Schema::rename('payment_proofs', 'bukti_bayar');
        Schema::rename('expenses', 'pengeluaran');
        Schema::rename('generated_reports', 'laporan');
        Schema::rename('ai_chat_messages', 'ai_assistant');
        Schema::rename('business_settings', 'pemilik_kos');

        // ============================================================
        // PHASE 4: Re-add FK constraints with new table/column names
        // ============================================================

        Schema::table('kamar', function (Blueprint $table) {
            $table->foreign('tipe_kamar_id')->references('id')->on('tipe_kamar')->onDelete('cascade');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreign('kamar_id')->references('id')->on('kamar')->nullOnDelete();
        });

        Schema::table('riwayat_penghuni_kamar', function (Blueprint $table) {
            $table->foreign('kamar_id')->references('id')->on('kamar')->cascadeOnDelete();
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreign('kamar_id')->references('id')->on('kamar')->cascadeOnDelete();
        });

        Schema::table('room_status_histories', function (Blueprint $table) {
            $table->foreign('kamar_id')->references('id')->on('kamar')->cascadeOnDelete();
        });

        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->foreign('kamar_id')->references('id')->on('kamar')->cascadeOnDelete();
        });

        Schema::table('bukti_bayar', function (Blueprint $table) {
            $table->foreign('transaksi_id')->references('id')->on('transaksi')->cascadeOnDelete();
        });

        Schema::table('payment_verification_logs', function (Blueprint $table) {
            $table->foreign('transaksi_id')->references('id')->on('transaksi')->cascadeOnDelete();
        });

        Schema::table('late_payment_fines', function (Blueprint $table) {
            $table->foreign('transaksi_id')->references('id')->on('transaksi')->cascadeOnDelete();
            $table->foreign('penyewa_id')->references('id')->on('user')->onDelete('restrict');
        });

        Schema::table('report_details', function (Blueprint $table) {
            $table->foreign('laporan_id')->references('id')->on('laporan')->cascadeOnDelete();
        });

        // Re-add tenant_id (now penyewa_id) FKs
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreign('penyewa_id')->references('id')->on('user')->onDelete('restrict');
        });

        Schema::table('kamar', function (Blueprint $table) {
            $table->foreign('current_tenant_id')->references('id')->on('user')->nullOnDelete();
        });

        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->foreign('penyewa_id')->references('id')->on('user')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new FKs
        Schema::table('kamar', function (Blueprint $table) {
            $table->dropForeign(['tipe_kamar_id']);
        });
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['kamar_id']);
        });
        Schema::table('riwayat_penghuni_kamar', function (Blueprint $table) {
            $table->dropForeign(['kamar_id']);
        });
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['kamar_id']);
        });
        Schema::table('room_status_histories', function (Blueprint $table) {
            $table->dropForeign(['kamar_id']);
        });
        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->dropForeign(['kamar_id']);
        });
        Schema::table('bukti_bayar', function (Blueprint $table) {
            $table->dropForeign(['transaksi_id']);
        });
        Schema::table('payment_verification_logs', function (Blueprint $table) {
            $table->dropForeign(['transaksi_id']);
        });
        Schema::table('late_payment_fines', function (Blueprint $table) {
            $table->dropForeign(['transaksi_id']);
            $table->dropForeign(['penyewa_id']);
        });
        Schema::table('report_details', function (Blueprint $table) {
            $table->dropForeign(['laporan_id']);
        });
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['penyewa_id']);
        });
        Schema::table('kamar', function (Blueprint $table) {
            $table->dropForeign(['current_tenant_id']);
        });
        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->dropForeign(['penyewa_id']);
        });

        // Rename tables back
        Schema::rename('user', 'users');
        Schema::rename('admin', 'admin_profiles');
        Schema::rename('penyewa', 'tenant_profiles');
        Schema::rename('transaksi', 'transactions');
        Schema::rename('tipe_kamar', 'room_types');
        Schema::rename('kamar', 'rooms');
        Schema::rename('riwayat_penghuni_kamar', 'room_occupants');
        Schema::rename('bukti_bayar', 'payment_proofs');
        Schema::rename('pengeluaran', 'expenses');
        Schema::rename('laporan', 'generated_reports');
        Schema::rename('ai_assistant', 'ai_chat_messages');
        Schema::rename('pemilik_kos', 'business_settings');

        // Rename columns back
        Schema::table('rooms', function (Blueprint $table) {
            $table->renameColumn('tipe_kamar_id', 'room_type_id');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('kamar_id', 'room_id');
        });
        Schema::table('room_occupants', function (Blueprint $table) {
            $table->renameColumn('kamar_id', 'room_id');
        });
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->renameColumn('kamar_id', 'room_id');
        });
        Schema::table('room_status_histories', function (Blueprint $table) {
            $table->renameColumn('kamar_id', 'room_id');
        });
        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->renameColumn('kamar_id', 'room_id');
        });
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->renameColumn('transaksi_id', 'transaction_id');
        });
        Schema::table('payment_verification_logs', function (Blueprint $table) {
            $table->renameColumn('transaksi_id', 'transaction_id');
        });
        Schema::table('late_payment_fines', function (Blueprint $table) {
            $table->renameColumn('transaksi_id', 'transaction_id');
        });
        Schema::table('report_details', function (Blueprint $table) {
            $table->renameColumn('laporan_id', 'generated_report_id');
        });

        // Rename penyewa_id back to tenant_id
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('penyewa_id', 'tenant_id');
        });
        Schema::table('late_payment_fines', function (Blueprint $table) {
            $table->renameColumn('penyewa_id', 'tenant_id');
        });
        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->renameColumn('penyewa_id', 'tenant_id');
        });
        // Re-add old FKs
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('cascade');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('rooms')->nullOnDelete();
        });
        Schema::table('room_occupants', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('rooms')->cascadeOnDelete();
        });
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('rooms')->cascadeOnDelete();
        });
        Schema::table('room_status_histories', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('rooms')->cascadeOnDelete();
        });
        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('rooms')->cascadeOnDelete();
        });
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->cascadeOnDelete();
        });
        Schema::table('payment_verification_logs', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->cascadeOnDelete();
        });
        Schema::table('late_payment_fines', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->cascadeOnDelete();
            $table->foreign('tenant_id')->references('id')->on('users')->onDelete('restrict');
        });
        Schema::table('report_details', function (Blueprint $table) {
            $table->foreign('generated_report_id')->references('id')->on('generated_reports')->cascadeOnDelete();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('users')->onDelete('restrict');
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreign('current_tenant_id')->references('id')->on('users')->nullOnDelete();
        });
        Schema::table('room_occupancy_histories', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('users')->onDelete('restrict');
        });
    }
};
