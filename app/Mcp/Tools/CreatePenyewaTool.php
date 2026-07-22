<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\AuditsMcpActions;
use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Tambah penyewa baru (data internal, tanpa akun login). Penyewa baru BELUM bisa langsung ditempatkan ke kamar karena belum ada pembayaran — jika kamar_id diisi, permintaan ditolak. Untuk menempatkan, gunakan create-transaksi yang mewajibkan pembayaran.')]
class CreatePenyewaTool extends Tool
{
    use InteractsWithOwner, AuditsMcpActions;

    public function handle(Request $request): Response
    {
        $ownerId = $this->ownerId($request);
        if (! $ownerId) {
            return Response::error('Token tidak terkait pengelola kos.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:user,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'tenant_type' => ['nullable', 'in:mahasiswa,non_mahasiswa'],
            'kamar_id' => ['nullable', 'integer'],
        ]);

        // Guard: penyewa yang baru dibuat belum punya pembayaran terverifikasi,
        // sehingga tidak boleh langsung ditempatkan ke kamar. Occupancy harus
        // selalu didahului pembayaran — lihat create-transaksi.
        if (! empty($validated['kamar_id'])) {
            return Response::error(
                'Penyewa baru belum memiliki pembayaran terverifikasi, jadi belum bisa '
                . 'ditempatkan ke kamar. Buat penyewa tanpa kamar_id, lalu gunakan '
                . 'create-transaksi untuk mencatat pembayaran sekaligus menempatkannya.'
            );
        }

        // Penyewa hanya didata di sini; penempatan ke kamar dilakukan lewat
        // create-transaksi agar occupancy selalu disertai catatan pembayaran.
        $user = DB::transaction(function () use ($validated, $ownerId) {
            $email = $validated['email'] ?? ('penyewa_' . Str::lower(Str::random(10)) . '@internal.local');

            $user = User::create([
                'name' => $validated['name'],
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'role' => 'tenant',
                'status' => 'active',
            ]);

            Penyewa::create([
                'user_id' => $user->id,
                'owner_id' => $ownerId,
                'tenant_type' => $validated['tenant_type'] ?? 'mahasiswa',
                'phone' => $validated['phone'] ?? null,
            ]);

            return $user;
        });

        $this->logMcp($request, 'create', "Tambah penyewa {$user->name}", $user);
        $this->notifyOwnerMcp(
            $ownerId,
            'Penyewa Baru (AI Agent)',
            "Penyewa \"{$user->name}\" ditambahkan via AI agent.",
            'tenant',
            $user->id
        );
        $this->notifyAdminsMcp(
            $ownerId,
            'Penyewa Baru (AI Agent)',
            "Penyewa \"{$user->name}\" ditambahkan via AI agent.",
            'tenant',
            $user->id
        );

        return Response::json([
            'success' => true,
            'message' => "Penyewa {$user->name} berhasil ditambahkan (belum ditempatkan ke kamar). "
                . 'Untuk menempatkan ke kamar, gunakan create-transaksi (butuh pembayaran).',
            'penyewa' => ['id' => $user->id, 'nama' => $user->name],
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('Nama lengkap penyewa.')->required(),
            'email' => $schema->string()->description('Email (opsional). Kosongkan bila tak ada.'),
            'phone' => $schema->string()->description('Nomor HP (opsional).'),
            'tenant_type' => $schema->string()->description('Jenis: mahasiswa atau non_mahasiswa.')->enum(['mahasiswa', 'non_mahasiswa']),
            'kamar_id' => $schema->integer()->description('ID kamar untuk menempatkan penyewa. Catatan: penyewa baru belum bayar, jadi penempatan lewat sini akan DITOLAK — pakai create-transaksi untuk menempatkan dengan pembayaran.'),
        ];
    }
}
