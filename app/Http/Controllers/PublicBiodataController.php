<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesBiodataForm;
use App\Models\Penyewa;
use Illuminate\Http\Request;

/**
 * Form publik "isi biodata sendiri" untuk penyewa — TANPA login.
 * Token pada URL adalah otorisasinya (dibuat owner/admin dari halaman biodata).
 */
class PublicBiodataController extends Controller
{
    use HandlesBiodataForm;

    public function edit(string $token)
    {
        $penyewa = Penyewa::with('user')->where('form_token', $token)->firstOrFail();

        return view('public.biodata-form', [
            'penyewa' => $penyewa,
            'token' => $token,
            'docTypes' => $this->biodataDocTypes,
        ]);
    }

    public function update(Request $request, string $token)
    {
        $penyewa = Penyewa::with('user')->where('form_token', $token)->firstOrFail();

        $this->applyBiodata($penyewa, $request);

        return redirect()->route('public.biodata.edit', ['token' => $token])
            ->with('success', 'Biodata berhasil disimpan. Terima kasih! Kamu boleh menutup halaman ini.');
    }
}
