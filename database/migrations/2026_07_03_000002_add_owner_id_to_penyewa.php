<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Multi-tenant: sekat data penyewa (tenant_profiles) ke pemilik kos.
 * Sebelumnya penyewa hanya tertaut ke user; owner-nya disimpulkan dari
 * kamar/transaksi. Kolom owner_id eksplisit membuat scoping owner/admin robust.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('penyewa', 'owner_id')) {
            Schema::table('penyewa', function (Blueprint $table) {
                $table->foreignId('owner_id')->nullable()->after('user_id')
                    ->constrained('user')->nullOnDelete();
                $table->index('owner_id');
            });
        }

        // Backfill dari transaksi (penyewa_id -> user.id; owner_id ada di transaksi).
        DB::statement("
            UPDATE penyewa p
            JOIN (
                SELECT penyewa_id, MAX(owner_id) AS owner_id
                FROM transaksi
                WHERE owner_id IS NOT NULL
                GROUP BY penyewa_id
            ) t ON t.penyewa_id = p.user_id
            SET p.owner_id = t.owner_id
            WHERE p.owner_id IS NULL
        ");

        // Sisa penyewa tanpa transaksi -> tautkan ke owner pertama (saat ini single-owner).
        $defaultOwnerId = DB::table('user')->where('role', 'owner')->orderBy('id')->value('id');
        if ($defaultOwnerId) {
            DB::table('penyewa')->whereNull('owner_id')->update(['owner_id' => $defaultOwnerId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('penyewa', 'owner_id')) {
            Schema::table('penyewa', function (Blueprint $table) {
                $table->dropConstrainedForeignId('owner_id');
            });
        }
    }
};
