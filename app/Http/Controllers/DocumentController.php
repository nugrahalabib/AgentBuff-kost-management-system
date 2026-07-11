<?php

namespace App\Http\Controllers;

use App\Models\BuktiBayar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Streaming file privat (bukti bayar & dokumen identitas penyewa) untuk
 * pengelola kos (owner/admin). Direlokasi dari controller Penyewa yang dihapus
 * saat reservasi online ditiadakan.
 *
 * TODO(multi-tenant): setelah kolom owner_id ditambahkan ke tabel penyewa,
 * batasi owner agar hanya bisa melihat berkas milik tenant/transaksi kos-nya.
 */
class DocumentController extends Controller
{
    private const PROOF_DISK = 'local';

    /** Stream bukti bayar dari private storage. */
    public function viewPaymentProof($proofId)
    {
        $user = Auth::user();
        if (! in_array($user->role, ['owner', 'admin'], true)) {
            abort(403);
        }

        $proof = BuktiBayar::with('transaction')->findOrFail($proofId);

        // Owner hanya boleh melihat bukti bayar pada tenant/kos miliknya.
        if ($user->role === 'owner' && $proof->transaction && $proof->transaction->owner_id !== $user->id) {
            abort(403);
        }

        $disk = Storage::disk(self::PROOF_DISK);
        if (! $disk->exists($proof->file_path)) {
            abort(404);
        }

        return response()->file($disk->path($proof->file_path), [
            'Content-Type' => $proof->file_type ?: 'application/octet-stream',
        ]);
    }

    /** Stream dokumen identitas penyewa (KTP, KK, dll). */
    public function viewDocument(Request $request, $userId, $type)
    {
        $viewer = Auth::user();
        if (! in_array($viewer->role, ['owner', 'admin'], true)) {
            abort(403);
        }

        $allowedTypes = ['ktp', 'kartu_mahasiswa', 'ktp_ortu', 'kartu_keluarga', 'pas_foto', 'surat_pernyataan'];
        if (! in_array($type, $allowedTypes, true)) {
            abort(404);
        }

        $tenantUser = User::with('tenantProfile')->findOrFail($userId);
        $documents = $tenantUser->tenantProfile?->documents ?? [];
        $path = $documents[$type] ?? null;
        if (! $path) {
            abort(404);
        }

        // Utamakan private disk; fallback ke public storage lama.
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return response()->file(Storage::disk($disk)->path($path));
            }
        }

        abort(404);
    }
}
