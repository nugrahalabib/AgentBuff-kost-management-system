<?php

namespace App\Mcp\Tools;

use App\Mcp\Concerns\InteractsWithOwner;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Tambah penyewa baru (data internal, tanpa akun login) dan opsional tempatkan langsung ke kamar tersedia.')]
class CreatePenyewaTool extends Tool
{
    use InteractsWithOwner;

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
            'kamar_id' => ['nullable', Rule::exists('kamar', 'id')->where('owner_id', $ownerId)],
        ]);

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

            if (! empty($validated['kamar_id'])) {
                $room = Kamar::where('owner_id', $ownerId)->with('roomType')->find($validated['kamar_id']);
                if ($room && $room->hasAvailableSlot()) {
                    $room->occupants()->attach($user->id, ['check_in_date' => now()]);
                    $room->refresh();
                    $room->update([
                        'status' => $room->isFull() ? 'occupied' : 'available',
                        'lease_start_date' => $room->lease_start_date ?? now(),
                    ]);
                }
            }

            return $user;
        });

        return Response::json([
            'success' => true,
            'message' => "Penyewa {$user->name} berhasil ditambahkan.",
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
            'kamar_id' => $schema->integer()->description('ID kamar untuk menempatkan penyewa (opsional).'),
        ];
    }
}
