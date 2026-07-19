<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Penyewa;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Logika bersama pengisian biodata penyewa. Dipakai form publik (self-service,
 * token) & edit oleh owner/admin — supaya validasi + penyimpanan konsisten:
 * tanggal lahir dari 3 dropdown, email menyasar User, dan upload dokumen.
 */
trait HandlesBiodataForm
{
    /** 6 jenis dokumen identitas (selaras DocumentController & halaman biodata). */
    private array $biodataDocTypes = ['ktp', 'kartu_mahasiswa', 'ktp_ortu', 'kartu_keluarga', 'pas_foto', 'surat_pernyataan'];

    /** Field skalar profil penyewa yang boleh diisi. */
    private array $biodataScalarFields = [
        'phone', 'tenant_type', 'birth_place', 'id_card_number', 'address', 'occupation',
        'university', 'faculty', 'major', 'enrollment_year', 'student_card_number',
        'guardian_name', 'guardian_phone', 'guardian_home_phone', 'guardian_id_card_number',
        'guardian_occupation', 'guardian_address',
    ];

    protected function applyBiodata(Penyewa $penyewa, Request $request): void
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email', 'max:255', Rule::unique('user', 'email')->ignore($penyewa->user_id)],
            'phone' => 'nullable|string|max:30',
            'tenant_type' => 'nullable|in:mahasiswa,non_mahasiswa',
            'birth_place' => 'nullable|string|max:100',
            'birth_day' => 'nullable|integer|min:1|max:31',
            'birth_month' => 'nullable|integer|min:1|max:12',
            'birth_year' => 'nullable|integer|min:1940|max:' . date('Y'),
            'id_card_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'occupation' => 'nullable|string|max:100',
            'university' => 'nullable|string|max:150',
            'faculty' => 'nullable|string|max:150',
            'major' => 'nullable|string|max:150',
            'enrollment_year' => 'nullable|string|max:10',
            'student_card_number' => 'nullable|string|max:50',
            'guardian_name' => 'nullable|string|max:150',
            'guardian_phone' => 'nullable|string|max:30',
            'guardian_home_phone' => 'nullable|string|max:30',
            'guardian_id_card_number' => 'nullable|string|max:30',
            'guardian_occupation' => 'nullable|string|max:100',
            'guardian_address' => 'nullable|string|max:500',
            'documents.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ], [], [
            'birth_day' => 'tanggal lahir',
            'birth_month' => 'bulan lahir',
            'birth_year' => 'tahun lahir',
        ]);

        // Field skalar (guarded: is_verified_by_admin/verified_at/status tak tersentuh).
        $penyewa->fill(Arr::only($validated, $this->biodataScalarFields));

        // Tanggal lahir dari 3 dropdown → date valid.
        if (! empty($validated['birth_day']) && ! empty($validated['birth_month']) && ! empty($validated['birth_year'])) {
            try {
                $penyewa->birth_date = Carbon::create(
                    (int) $validated['birth_year'],
                    (int) $validated['birth_month'],
                    (int) $validated['birth_day']
                )->toDateString();
            } catch (\Throwable $e) {
                // tanggal tak valid (mis. 31 Feb) → abaikan
            }
        }

        // Email menyasar akun User penyewa (bila diisi).
        if (! empty($validated['email']) && $penyewa->user) {
            $penyewa->user->update(['email' => $validated['email']]);
        }

        // Upload dokumen → disk privat 'local', gabung ke map JSON documents.
        $documents = $penyewa->documents ?? [];
        foreach ($this->biodataDocTypes as $type) {
            if ($request->hasFile("documents.$type")) {
                $path = $request->file("documents.$type")->store("tenant-documents/{$penyewa->id}", 'local');
                $documents[$type] = $path;
                if ($type === 'ktp') {
                    $penyewa->id_card_photo_path = $path;
                }
            }
        }
        $penyewa->documents = $documents;
        $penyewa->save();
    }
}
