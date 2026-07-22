<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Logika bersama untuk menambahkan PENYEWA sebagai data internal (tanpa akun login).
 * Dipakai owner (PemilikKos) & admin — owner_id ditentukan lewat User::ownerId()
 * (owner → dirinya, admin → owner-nya), sehingga data yang dibuat admin & owner
 * selalu menyasar tenant/kos yang sama.
 *
 * Penyewa TIDAK ditempatkan ke kamar di sini: penyewa baru belum punya pembayaran,
 * dan occupancy harus selalu didahului pembayaran. Penempatan dilakukan lewat
 * pencatatan transaksi (TransactionController / create-transaksi MCP).
 */
trait ManagesTenants
{
    /** Owner (pemilik kos) yang menaungi user yang sedang login. */
    protected function tenantOwnerId(): int
    {
        return (int) auth()->user()->ownerId();
    }

    /** Kamar milik owner ini yang masih punya slot kosong (untuk dropdown penempatan). */
    protected function availableRoomsForOwner()
    {
        return Kamar::where('owner_id', $this->tenantOwnerId())
            ->hasAvailableSlot()
            ->with('roomType')
            ->withCount('occupants')
            ->orderBy('floor_number')
            ->orderBy('room_number')
            ->get();
    }

    /**
     * Buat penyewa baru (user data-only + profil) dan opsional tempatkan ke kamar.
     * Mengembalikan User penyewa yang dibuat.
     */
    protected function persistNewTenant(Request $request): User
    {
        $ownerId = $this->tenantOwnerId();

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email|max:255|unique:user,email',
            'phone'       => 'nullable|string|max:30',
            'tenant_type' => 'nullable|in:mahasiswa,non_mahasiswa',
            'address'     => 'nullable|string|max:500',
            'kamar_id'    => ['nullable', 'integer'],
        ], [
            'name.required'  => 'Nama penyewa wajib diisi.',
            'email.unique'   => 'Email tersebut sudah dipakai.',
        ]);

        // Guard: penyewa baru belum punya pembayaran terverifikasi, jadi tidak boleh
        // langsung ditempatkan ke kamar. Occupancy harus selalu didahului pembayaran —
        // penempatan dilakukan lewat pencatatan transaksi.
        if (! empty($validated['kamar_id'])) {
            throw ValidationException::withMessages([
                'kamar_id' => 'Penyewa baru belum memiliki pembayaran, jadi belum bisa langsung '
                    . 'ditempatkan ke kamar. Simpan penyewa dulu, lalu catat pembayaran di menu '
                    . 'transaksi untuk menempatkannya.',
            ]);
        }

        return DB::transaction(function () use ($validated, $ownerId) {
            // Email placeholder non-login bila tidak diisi (kolom UNIQUE NOT NULL).
            $email = $validated['email'] ?? ('penyewa_' . Str::lower(Str::random(10)) . '@internal.local');

            // Akun data-only: password acak yang tak diketahui siapa pun (tak bisa login).
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $email,
                'password' => Hash::make(Str::random(40)),
                'role'     => 'tenant',
                'status'   => 'active',
            ]);

            Penyewa::create([
                'user_id'     => $user->id,
                'owner_id'    => $ownerId,
                'tenant_type' => $validated['tenant_type'] ?? 'mahasiswa',
                'phone'       => $validated['phone'] ?? null,
                'address'     => $validated['address'] ?? null,
            ]);

            \App\Services\LoggerService::log('create', "Tambah penyewa {$user->name}", $user);

            return $user;
        });
    }

    /**
     * Hapus penyewa (user data-only + profil) beserta SELURUH relasinya dengan aman.
     * WAJIB menyertakan alasan. Bila yang menghapus ADMIN, owner kos diberi notifikasi.
     *
     * Menangani FK RESTRICT (transaksi, denda, histori occupancy), membersihkan file
     * bukti bayar & dokumen identitas dari disk, lalu me-recompute status kamar.
     * Pemanggil WAJIB memastikan $tenant memang milik kos-nya (scope owner_id).
     */
    protected function deleteTenant(User $tenant, string $reason): void
    {
        app(\App\Services\TenantDeletionService::class)->delete(
            $tenant,
            $reason,
            auth()->user(),
            $this->tenantOwnerId()
        );
    }
}
