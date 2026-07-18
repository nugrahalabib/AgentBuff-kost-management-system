<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\PaymentVerificationLog;
use App\Models\Notification;
use App\Models\BuktiBayar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * Display transactions pending owner verification
     */
    /**
     * Display transactions pending owner verification
     */
    public function index(Request $request)
    {
        $owner = Auth::user();
        $status = $request->get('status', 'semua'); // Default to 'semua' (Show All)
        $search = $request->get('search', null);
        $floor = $request->get('floor', null);
        $date = $request->get('date', null);

        // Base Query - Scoped to Owner
        // Only show statuses relevant to Owner Verification process + History
        $query = Transaksi::where('owner_id', $owner->id)
            ->with(['tenant', 'room.roomType', 'paymentProofs']);

        // Filter by status
        // Mapping:
        // 'pending' (Button: Perlu Cek Mutasi) -> verified_by_admin (Ready for Owner)
        // 'success' (Button: Riwayat Selesai) -> verified_by_owner
        // 'rejected' (Button: Ditolak) -> rejected_by_owner (Only Owner Rejections)
        if ($status && $status !== 'semua') {
            if ($status === 'pending') {
                $query->where('status', 'verified_by_admin');
            } elseif ($status === 'success') {
                $query->where('status', 'verified_by_owner');
            } elseif ($status === 'rejected') {
                $query->whereIn('status', ['rejected_by_owner', 'rejected_by_admin']);
            }
        } else {
            // 'semua' -> Show all relevant statuses for Owner
            $query->whereIn('status', ['verified_by_admin', 'verified_by_owner', 'rejected_by_owner', 'rejected_by_admin']);
        }

        // Filter by floor
        if ($floor) {
            $query->whereHas('room', function ($q) use ($floor) {
                $q->where('floor_number', $floor);
            });
        }

        // Filter by date
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        // Search by tenant name, room number, or reference number
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('tenant', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('room', function ($q3) use ($search) {
                        $q3->where('room_number', 'like', "%{$search}%");
                    })
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        // Clone query for separate pagination
        $gridQuery = clone $query;
        $listQuery = clone $query;

        // Pass filter values to pagination to persist them in links
        $gridTransactions = $gridQuery->orderBy('updated_at', 'desc')->paginate(9, ['*'], 'grid_page');
        $listTransactions = $listQuery->orderBy('updated_at', 'desc')->paginate(10, ['*'], 'list_page');

        // Stats for Dashboard/Cards
        $stats = [
            'pending_verification' => Transaksi::where('owner_id', $owner->id)->where('status', 'verified_by_admin')->count(),
            'total_amount' => Transaksi::where('owner_id', $owner->id)->where('status', 'verified_by_admin')->sum('provisional_amount'),
        ];

        // Get distinct floors owned by this owner
        $floors = \App\Models\Kamar::where('owner_id', $owner->id)->distinct()->orderBy('floor_number')->pluck('floor_number');

        $tenantsForModal = \App\Models\User::where('role', 'tenant')
            ->whereHas('tenantProfile', fn ($q) => $q->where('owner_id', $owner->id))
            ->with(['occupiedRoom', 'currentRoom'])
            ->orderBy('name')->get();
        $roomsForModal = \App\Models\Kamar::where('owner_id', $owner->id)->with('roomType')->withCount('occupants')->orderBy('room_number')->get();

        // Data ringkas untuk dropdown dinamis (Alpine) di modal tambah transaksi:
        // tiap penyewa membawa id kamar aktifnya (bila ada -> perpanjangan).
        $penyewaData = $tenantsForModal->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'kamar_id' => $t->activeRoom->id ?? null,
        ])->values();
        $roomData = $roomsForModal->map(fn ($r) => [
            'id' => $r->id,
            'label' => 'Kamar ' . $r->room_number . ' — ' . ($r->roomType->name ?? '-'),
            'capacity' => $r->roomType->capacity ?? 1,
            'occupants' => $r->occupants_count ?? 0,
            'price' => (float) $r->rent_per_person,
        ])->values();

        return view('pemilik-kos.data-transaksi', [
            'transaksiGrid' => $gridTransactions,
            'transaksiList' => $listTransactions,
            'stats' => $stats,
            'selectedStatus' => $status,
            'search' => $search,
            'floors' => $floors,
            'tenants' => $tenantsForModal,
            'rooms' => $roomsForModal,
            'penyewaData' => $penyewaData,
            'roomData' => $roomData,
            'preselectPenyewa' => $request->query('penyewa'),
        ]);
    }

    /**
     * Verify payment (Step 2 - Owner Verification)
     */
    public function verify(Request $request, Transaksi $transaction)
    {
        // Check ownership
        if ($transaction->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'required_if:status,rejected|nullable|string|min:10',
        ], [
            'notes.required_if' => 'Catatan wajib diisi saat menolak pembayaran. Jelaskan alasan penolakan agar penyewa dapat memperbaikinya.',
            'notes.min' => 'Catatan penolakan minimal 10 karakter.',
        ]);

        // Re-fetch the row under a transaction lock so two concurrent verify
        // clicks can't both succeed (no duplicate pivot inserts, no double notifs).
        $transaction = \Illuminate\Support\Facades\DB::transaction(function () use ($transaction) {
            return Transaksi::where('id', $transaction->id)->lockForUpdate()->first();
        });

        if (!$transaction) {
            return back()->with('error', 'Transaksi tidak ditemukan.');
        }

        // Reject if the owner already acted on this transaction.
        if (in_array($transaction->status, ['verified_by_owner', 'rejected_by_owner'], true)) {
            return back()->with('info', 'Transaksi ini sudah diproses sebelumnya.');
        }

        // Use the amount that admin verified (provisional_amount) when available so
        // the financial reports reflect what was actually checked, not the tenant's
        // original (untrusted) submitted amount.
        $approvedAmount = $transaction->provisional_amount ?? $transaction->amount;

        $logData = [
            'transaksi_id' => $transaction->id,
            'verified_by' => Auth::id(),
            'verification_type' => 'owner',
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'verified_at' => now(),
        ];

        if ($validated['status'] === 'approved') {
            // Update status, log verifikasi, dan PENEMPATAN dijalankan atomik: bila
            // kamar penuh, assignRoomAfterPayment() melempar error sehingga update
            // status & log ikut ter-rollback (tidak ada data setengah jadi).
            try {
                DB::transaction(function () use ($transaction, $approvedAmount, $validated, $logData) {
                    $transaction->update([
                        'status' => 'verified_by_owner',
                        'owner_verified_at' => now(),
                        'owner_verified_by' => Auth::id(),
                        'owner_notes' => $validated['notes'] ?? null,
                        'final_amount' => $approvedAmount,
                    ]);

                    PaymentVerificationLog::create($logData);

                    // Satu fungsi penempatan (attach/reaktivasi + status kamar +
                    // notifikasi "pembayaran berhasil" ke penyewa di dalamnya).
                    $result = app(\App\Services\RoomAllocationService::class)->assignRoomAfterPayment($transaction);
                    if (! $result['ok']) {
                        throw new \RuntimeException($result['error']);
                    }
                });
            } catch (\RuntimeException $e) {
                return back()->with('error', $e->getMessage());
            }
        } else {
            // Ditolak: update status + log + notifikasi penyewa (tanpa penempatan).
            $transaction->update([
                'status' => 'rejected_by_owner',
                'owner_verified_at' => now(),
                'owner_verified_by' => Auth::id(),
                'owner_notes' => $validated['notes'] ?? null,
                'final_amount' => 0,
            ]);

            PaymentVerificationLog::create($logData);

            $roomName = $transaction->room?->roomType?->name ?? 'kamar';
            $notes = $validated['notes'] ?? null;
            $rejectionNotes = $notes ? " Alasan: {$notes}" : '';

            Notification::create([
                'user_id' => $transaction->penyewa_id,
                'type' => 'payment_rejected',
                'category' => 'finance',
                'title' => 'Pembayaran Ditolak',
                'message' => "Pembayaran untuk {$roomName} ({$transaction->reference_number}) telah ditolak oleh pemilik.{$rejectionNotes} Silakan upload ulang bukti pembayaran atau hubungi admin untuk informasi lebih lanjut.",
                'related_entity_type' => 'transaction',
                'related_entity_id' => $transaction->id,
                'priority' => 'high',
                'action_required' => true,
            ]);
        }

        // Notify the admin who originally verified, if any.
        // Skip if no admin verifier was recorded (avoids inserting a NULL user_id).
        $adminVerifierId = $transaction->adminVerifiedBy?->id ?? $transaction->admin_verified_by;
        if ($adminVerifierId) {
            Notification::create([
                'user_id' => $adminVerifierId,
                'type' => 'payment_completed',
                'category' => 'finance',
                'title' => 'Verifikasi Owner Selesai',
                'message' => "Pembayaran {$transaction->reference_number} telah diverifikasi owner.",
                'related_entity_type' => 'transaction',
                'related_entity_id' => $transaction->id,
                'priority' => 'medium',
            ]);
        }

        // Cleanup: Archive Owner's own "Action Required" notification for this transaction
        Notification::where('user_id', Auth::id())
            ->where('related_entity_type', 'transaction')
            ->where('related_entity_id', $transaction->id)
            ->where('action_required', true)
            ->update([
                'action_required' => false,
                'status' => 'archived', // Move to archive so it disappears from 'Unread/Action' lists
                'read_at' => now(),
            ]);

        if ($validated['status'] === 'rejected') {
            return back()->with('success', 'Pembayaran ditolak');
        }
        return back()->with('success', 'Pembayaran berhasil diterima');
    }

    /**
     * Hapus permanen data transaksi (baik yang diterima maupun ditolak).
     * Catatan: ini hanya menghapus catatan transaksi + bukti bayar + log
     * verifikasi (cascade). Status hunian/kamar TIDAK diubah — menghapus
     * transaksi tidak mengeluarkan penyewa dari kamar.
     */
    public function destroy(Request $request, Transaksi $transaction)
    {
        // Hanya boleh menghapus transaksi milik owner ini.
        if ($transaction->owner_id !== Auth::id()) {
            abort(403);
        }

        $invoice = $transaction->reference_number ?? $transaction->invoice_number ?? ('#' . $transaction->id);

        DB::transaction(function () use ($transaction) {
            // Hapus file bukti bayar dari disk (baris DB-nya ikut terhapus via cascade FK).
            foreach ($transaction->paymentProofs as $proof) {
                if ($proof->file_path) {
                    Storage::disk('local')->delete($proof->file_path);
                    Storage::disk('public')->delete($proof->file_path);
                }
            }

            // Bersihkan notifikasi terkait transaksi ini (tidak terhubung FK).
            Notification::where('related_entity_type', 'transaction')
                ->where('related_entity_id', $transaction->id)
                ->delete();

            // Hapus transaksi. payment_proofs, payment_verification_logs, dan
            // late_payment_fines ikut terhapus otomatis (cascadeOnDelete).
            $transaction->delete();
        });

        \App\Services\LoggerService::log(
            'delete_transaction',
            "Owner menghapus transaksi {$invoice}",
            null
        );

        return back()->with('success', "Transaksi {$invoice} berhasil dihapus.");
    }

    /**
     * Form input transaksi/pembayaran manual (owner).
     */
    public function createManual(Request $request)
    {
        $ownerId = Auth::id();

        $tenants = \App\Models\User::where('role', 'tenant')
            ->whereHas('tenantProfile', fn ($q) => $q->where('owner_id', $ownerId))
            ->with(['occupiedRoom', 'currentRoom'])
            ->orderBy('name')
            ->get();

        $rooms = \App\Models\Kamar::where('owner_id', $ownerId)
            ->with('roomType')
            ->withCount('occupants')
            ->orderBy('room_number')
            ->get();

        // Data ringkas untuk dropdown dinamis (Alpine): tiap penyewa membawa id
        // kamar aktifnya (bila ada -> perpanjangan); tiap kamar membawa kapasitas,
        // okupansi, dan harga per orang.
        $penyewaData = $tenants->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'kamar_id' => $t->activeRoom->id ?? null,
        ])->values();

        $roomData = $rooms->map(fn ($r) => [
            'id' => $r->id,
            'label' => 'Kamar ' . $r->room_number . ' — ' . ($r->roomType->name ?? '-'),
            'capacity' => $r->roomType->capacity ?? 1,
            'occupants' => $r->occupants_count ?? 0,
            'price' => (float) $r->rent_per_person,
        ])->values();

        return view('pemilik-kos.transaksi-create', [
            'tenants' => $tenants,
            'rooms' => $rooms,
            'penyewaData' => $penyewaData,
            'roomData' => $roomData,
            'preselectPenyewa' => $request->query('penyewa'),
        ]);
    }

    /**
     * Simpan transaksi manual (owner). Owner = verifikator final, jadi transaksi
     * langsung verified_by_owner dan penyewa ditempatkan ke kamar bila belum.
     */
    public function storeManual(Request $request)
    {
        $ownerId = Auth::id();

        $validated = $request->validate([
            'penyewa_id' => ['required', Rule::exists('penyewa', 'user_id')->where('owner_id', $ownerId)],
            'kamar_id'   => ['required', Rule::exists('kamar', 'id')->where('owner_id', $ownerId)],
            'amount'     => 'required|numeric|min:1',
            'duration'   => 'required|integer|min:1|max:24',
            'payment_method' => 'required|in:cash,manual_transfer,edc',
            'payment_proof'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'notes'      => 'nullable|string|max:1000',
        ], [
            'penyewa_id.required' => 'Penyewa harus dipilih.',
            'penyewa_id.exists'   => 'Penyewa tidak ditemukan di kos Anda.',
            'kamar_id.required'   => 'Kamar harus dipilih.',
            'kamar_id.exists'     => 'Kamar tidak ditemukan di kos Anda.',
            'amount.required'     => 'Nominal pembayaran wajib diisi.',
            'duration.required'   => 'Durasi sewa wajib diisi.',
            'payment_method.required' => 'Metode pembayaran harus dipilih.',
        ]);

        $tenant = \App\Models\User::findOrFail($validated['penyewa_id']);
        $room = \App\Models\Kamar::with('roomType')->findOrFail($validated['kamar_id']);

        // Nomor invoice
        $prefix = 'INV-' . date('ym') . '-';
        $last = Transaksi::where('invoice_number', 'like', $prefix . '%')->orderBy('invoice_number', 'desc')->first();
        $newNumber = $last ? ((int) substr($last->invoice_number, -5)) + 1 : 1;
        $invoiceNumber = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        $methodLabels = ['cash' => 'TUNAI', 'edc' => 'EDC / MESIN KARTU', 'manual_transfer' => 'TRANSFER MANUAL'];
        $senderBank = $methodLabels[$validated['payment_method']] ?? 'MANUAL';

        try {
            DB::transaction(function () use ($request, $validated, $tenant, $room, $ownerId, $invoiceNumber, $senderBank) {
                // Periode sewa dengan chaining (perpanjangan menyambung dari akhir sewa aktif).
                $period = app(\App\Services\RoomAllocationService::class)
                    ->computeRentalPeriod($tenant->id, (int) $validated['duration']);

                $transaction = Transaksi::create([
                    'owner_id' => $ownerId,
                    'penyewa_id' => $tenant->id,
                    'kamar_id' => $room->id,
                    'amount' => $validated['amount'],
                    'duration_months' => $validated['duration'],
                    'period_start_date' => $period['start'],
                    'period_end_date' => $period['end'],
                    'invoice_number' => $invoiceNumber,
                    'reference_number' => $invoiceNumber,
                    'payment_date' => now(),
                    'due_date' => now(),
                    'status' => 'verified_by_owner',
                    'payment_method' => $validated['payment_method'],
                    'sender_bank' => $senderBank,
                    'sender_name' => $tenant->name,
                    'owner_verified_at' => now(),
                    'owner_verified_by' => $ownerId,
                    'owner_notes' => $validated['notes'] ?? null,
                    'provisional_amount' => $validated['amount'],
                    'final_amount' => $validated['amount'],
                ]);

                // Penempatan lewat service bersama (cek kapasitas + reaktivasi pivot).
                // Dijalankan SEBELUM menyimpan file bukti: bila kamar penuh, error dilempar
                // dan transaksi di-rollback tanpa menyisakan file bukti yang terlanjur tersimpan.
                $result = app(\App\Services\RoomAllocationService::class)->assignRoomAfterPayment($transaction);
                if (! $result['ok']) {
                    throw new \RuntimeException($result['error']);
                }

                if ($request->hasFile('payment_proof')) {
                    $path = $request->file('payment_proof')->store('payment-proofs/' . $tenant->id, 'local');
                    BuktiBayar::create([
                        'transaksi_id' => $transaction->id,
                        'file_path' => $path,
                        'file_type' => $request->file('payment_proof')->getClientMimeType(),
                        'uploaded_by' => $ownerId,
                        'uploaded_at' => now(),
                        'verified_status' => 'approved',
                        'verified_notes' => 'Dicatat oleh pemilik kos',
                    ]);
                }

                \App\Services\LoggerService::log(
                    'create_transaction',
                    "Transaksi manual ({$validated['payment_method']}) untuk {$tenant->name}",
                    $transaction
                );

                // Notifikasi agar transaksi MASUK (pemasukan) muncul di halaman notifikasi owner.
                \App\Models\Notification::create([
                    'user_id' => $ownerId,
                    'type' => 'payment_received',
                    'category' => 'finance',
                    'title' => 'Pemasukan Dicatat',
                    'message' => 'Pembayaran Rp ' . number_format((float) $validated['amount'], 0, ',', '.') . ' dari ' . $tenant->name . ' telah dicatat.',
                    'related_entity_type' => 'transaction',
                    'related_entity_id' => $transaction->id,
                    'priority' => 'medium',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('owner.verifikasi-transaksi')
            ->with('success', 'Transaksi manual berhasil dicatat & penyewa ditempatkan.');
    }
}
