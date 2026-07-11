<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Logika bersama untuk menambahkan PENYEWA sebagai data internal (tanpa akun login)
 * dan menempatkannya ke kamar yang tersedia. Dipakai owner (PemilikKos) & admin —
 * owner_id ditentukan lewat User::ownerId() (owner → dirinya, admin → owner-nya),
 * sehingga data yang dibuat admin & owner selalu menyasar tenant/kos yang sama.
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
            'kamar_id'    => ['nullable', Rule::exists('kamar', 'id')->where('owner_id', $ownerId)],
        ], [
            'name.required'  => 'Nama penyewa wajib diisi.',
            'email.unique'   => 'Email tersebut sudah dipakai.',
            'kamar_id.exists' => 'Kamar tidak ditemukan di kos Anda.',
        ]);

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

            // Penempatan ke kamar (opsional).
            if (! empty($validated['kamar_id'])) {
                $room = Kamar::where('owner_id', $ownerId)->with('roomType')->find($validated['kamar_id']);
                if ($room && $room->hasAvailableSlot()) {
                    $room->occupants()->attach($user->id, ['check_in_date' => now()]);
                    $room->refresh();
                    $room->update([
                        'status'            => $room->isFull() ? 'occupied' : 'available',
                        'lease_start_date'  => $room->lease_start_date ?? now(),
                    ]);
                }
            }

            \App\Services\LoggerService::log('create', "Tambah penyewa {$user->name}", $user);

            return $user;
        });
    }
}
