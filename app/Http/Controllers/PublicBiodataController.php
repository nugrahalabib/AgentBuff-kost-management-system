<?php

namespace App\Http\Controllers;

use App\Models\Penyewa;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * Form publik "isi biodata sendiri" untuk penyewa — TANPA login.
 * Token pada URL adalah otorisasinya (dibuat owner/admin dari halaman biodata).
 * Penyewa mengisi data pribadi + wali + upload dokumen; tersimpan langsung ke
 * profil penyewa dan otomatis tampil di halaman biodata (owner/admin).
 */
class PublicBiodataController extends Controller
{
    /** 6 jenis dokumen identitas (selaras DocumentController & halaman biodata). */
    private const DOC_TYPES = ['ktp', 'kartu_mahasiswa', 'ktp_ortu', 'kartu_keluarga', 'pas_foto', 'surat_pernyataan'];

    /** Field skalar yang boleh diisi penyewa lewat form publik. */
    private const SCALAR_FIELDS = [
        'phone', 'tenant_type', 'birth_place', 'birth_date', 'id_card_number', 'address', 'occupation',
        'university', 'faculty', 'major', 'enrollment_year', 'student_card_number',
        'guardian_name', 'guardian_phone', 'guardian_occupation', 'guardian_address',
    ];

    public function edit(string $token)
    {
        $penyewa = Penyewa::with('user')->where('form_token', $token)->firstOrFail();

        return view('public.biodata-form', [
            'penyewa' => $penyewa,
            'token' => $token,
            'docTypes' => self::DOC_TYPES,
        ]);
    }

    public function update(Request $request, string $token)
    {
        $penyewa = Penyewa::where('form_token', $token)->firstOrFail();

        $validated = $request->validate([
            'phone' => 'nullable|string|max:30',
            'tenant_type' => 'nullable|in:mahasiswa,non_mahasiswa',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
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
            'guardian_occupation' => 'nullable|string|max:100',
            'guardian_address' => 'nullable|string|max:500',
            'documents.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // Simpan field skalar (guarded: is_verified_by_admin/verified_at/status tak tersentuh).
        $penyewa->fill(Arr::only($validated, self::SCALAR_FIELDS));

        // Upload dokumen → disk privat 'local', gabung ke map JSON documents.
        $documents = $penyewa->documents ?? [];
        foreach (self::DOC_TYPES as $type) {
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

        return redirect()->route('public.biodata.edit', ['token' => $token])
            ->with('success', 'Biodata berhasil disimpan. Terima kasih! Kamu boleh menutup halaman ini.');
    }
}
