<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\BuktiBayar;
use App\Models\PaymentVerificationLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display transactions with filtering
     */
    public function index(Request $request)
    {
        $status = $request->get('status', null);
        $search = $request->get('search', null);
        $floor = $request->get('floor', null);
        $date = $request->get('date', null);

        $query = Transaksi::with(['tenant', 'room', 'paymentProofs']);

        // Filter by status
        if ($status && $status !== 'semua') {
            if ($status === 'pending') {
                $query->where('status', 'pending_verification');
            } elseif ($status === 'owner') {
                $query->where('status', 'verified_by_admin');
            } elseif ($status === 'success') {
                $query->where('status', 'verified_by_owner');
            } elseif ($status === 'rejected') {
                $query->whereIn('status', ['rejected_by_admin', 'rejected_by_owner', 'cancelled_by_tenant']);
            } else {
                $query->where('status', $status);
            }
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

        // Order by updated_at so retried/resubmitted transactions appear at top
        $gridTransactions = $gridQuery->orderBy('updated_at', 'desc')->paginate(9, ['*'], 'grid_page');
        $listTransactions = $listQuery->orderBy('updated_at', 'desc')->paginate(10, ['*'], 'list_page');

        $stats = [
            'total' => Transaksi::count(),
            'pending' => Transaksi::where('status', 'pending_verification')->count(),
            'verified_admin' => Transaksi::where('status', 'verified_by_admin')->count(),
            'verified_owner' => Transaksi::where('status', 'verified_by_owner')->count(),
        ];

        $todayTransactions = Transaksi::whereDate('created_at', today())->count();

        // Get distinct floors for filter
        $floors = \App\Models\Kamar::distinct()->orderBy('floor_number')->pluck('floor_number');

        return view('admin.transaksi', [
            'transaksiGrid' => $gridTransactions,
            'transaksiList' => $listTransactions,
            'totalTransaksi' => $stats['total'],
            'pendingValidation' => $stats['pending'],
            'menungguPemilik' => $stats['verified_admin'],
            'transaksiHariIni' => $todayTransactions,
            'selectedStatus' => $status,
            'search' => $search,
            'floors' => $floors,
            'penyewaUntukJs' => \App\Models\User::where('role', 'tenant')->with('currentRoom')->orderBy('name')->get()
                ->map(fn($t) => [
                    'id'          => $t->id,
                    'name'        => $t->name,
                    'kamar_id'     => $t->currentRoom?->id,
                    'room_number' => $t->currentRoom?->room_number,
                ])->values(),
            'kamarUntukJs' => \App\Models\Kamar::with(['roomType', 'occupants'])->orderBy('room_number')->get()
                ->map(fn($r) => [
                    'id'          => $r->id,
                    'number'      => $r->room_number,
                    'type'        => $r->roomType?->name ?? '',
                    'price'       => $r->roomType?->rent_per_person ?? 0,
                    'penyewa_id'   => $r->occupants->first()?->id,
                    'tenant_name' => $r->occupants->first()?->name,
                ])->values(),
        ]);
    }

    /**
     * Verify payment proof (Step 1 - Admin Verification)
     */
    public function verifyPayment(Request $request, Transaksi $transaction)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'required_if:status,rejected|nullable|string|min:10',
        ], [
            'notes.required_if' => 'Catatan wajib diisi saat menolak pembayaran. Jelaskan alasan penolakan agar penyewa dapat memperbaikinya.',
            'notes.min' => 'Catatan penolakan minimal 10 karakter.',
        ]);

        // Lock the row and re-check state so two concurrent admin clicks can't
        // both reach the update below.
        $transaction = DB::transaction(function () use ($transaction) {
            return Transaksi::where('id', $transaction->id)->lockForUpdate()->first();
        });

        if (!$transaction) {
            return back()->with('error', 'Transaksi tidak ditemukan.');
        }

        // Only pending transactions are eligible for admin verification.
        if ($transaction->status !== 'pending_verification') {
            return back()->with('info', 'Transaksi ini sudah diproses sebelumnya.');
        }

        $transaction->update([
            'status' => $validated['status'] === 'approved' ? 'verified_by_admin' : 'rejected_by_admin',
            'admin_verified_at' => now(),
            'admin_verified_by' => Auth::id(),
            'admin_notes' => $validated['notes'] ?? null,
            'provisional_amount' => $validated['status'] === 'approved' ? $transaction->amount : 0,
        ]);

        // Log verification
        PaymentVerificationLog::create([
            'transaksi_id' => $transaction->id,
            'verified_by' => Auth::id(),
            'verification_type' => 'admin',
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'verified_at' => now(),
        ]);

        // Log activity
        \App\Services\LoggerService::log(
            'verify_payment',
            "Verifikasi pembayaran {$transaction->reference_number}",
            $transaction
        );

        // Create notification for owner if approved
        if ($validated['status'] === 'approved') {
            Notification::create([
                'user_id' => $transaction->owner_id,
                'type' => 'payment_verified',
                'category' => 'finance',
                'title' => 'Pembayaran Diverifikasi Admin',
                'message' => "Pembayaran {$transaction->reference_number} telah diverifikasi admin, menunggu verifikasi owner.",
                'related_entity_type' => 'transaction',
                'related_entity_id' => $transaction->id,
                'priority' => 'high',
                'action_required' => true,
            ]);
        } else {
            // Payment rejected - notify tenant
            $roomName = $transaction->room?->roomType?->name ?? 'kamar';
            $notes = $validated['notes'] ?? null;
            $rejectionNotes = $notes ? " Alasan: {$notes}" : '';

            Notification::create([
                'user_id' => $transaction->penyewa_id,
                'type' => 'payment_rejected',
                'category' => 'finance',
                'title' => 'Pembayaran Ditolak',
                'message' => "Pembayaran untuk {$roomName} ({$transaction->reference_number}) telah ditolak.{$rejectionNotes} Silakan upload ulang bukti pembayaran atau hubungi admin untuk informasi lebih lanjut.",
                'related_entity_type' => 'transaction',
                'related_entity_id' => $transaction->id,
                'priority' => 'high',
                'action_required' => true,
            ]);
        }

        if ($validated['status'] === 'rejected') {
            return back()->with('success', 'Pembayaran ditolak');
        }
        return back()->with('success', 'Pembayaran berhasil diterima');
    }

    /**
     * Update payment proof status
     */
    public function updateProofStatus(Request $request, BuktiBayar $proof)
    {
        $validated = $request->validate([
            'verified_status' => 'required|in:approved,rejected',
            'verified_notes' => 'nullable|string',
        ]);

        $proof->update([
            'verified_status' => $validated['verified_status'],
            'verified_notes' => $validated['verified_notes'],
        ]);

        // Log activity
        \App\Services\LoggerService::log(
            'update_status',
            'Update status bukti pembayaran: ' . $validated['verified_status'],
            $proof
        );

        return back()->with('success', 'Status bukti pembayaran berhasil diubah');
    }
    /**
     * Store manual transaction (Cash/EDC/Manual Transfer)
     */
    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'penyewa_id' => 'required|exists:user,id',
            'kamar_id'   => 'required|exists:kamar,id',
            'amount'    => 'required|numeric|min:1',
            'duration'  => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,manual_transfer,edc',
            'payment_proof'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'notes'     => 'nullable|string',
        ], [
            'penyewa_id.required' => 'Penyewa harus dipilih.',
            'penyewa_id.exists'   => 'Penyewa tidak ditemukan.',
            'kamar_id.required'   => 'Kamar harus dipilih.',
            'kamar_id.exists'     => 'Kamar tidak ditemukan.',
            'amount.required'    => 'Nominal pembayaran wajib diisi.',
            'amount.numeric'     => 'Nominal harus berupa angka.',
            'amount.min'         => 'Nominal harus lebih dari 0.',
            'duration.required'  => 'Durasi sewa wajib diisi.',
            'duration.integer'   => 'Durasi harus berupa angka bulat.',
            'duration.min'       => 'Durasi minimal 1 bulan.',
            'payment_method.required' => 'Metode pembayaran harus dipilih.',
            'payment_proof.required'  => 'Bukti pembayaran wajib diunggah.',
            'payment_proof.image'     => 'Bukti pembayaran harus berupa gambar (JPG/PNG).',
            'payment_proof.max'       => 'Ukuran file maksimal 2MB.',
        ]);

        $tenant = \App\Models\User::findOrFail($validated['penyewa_id']);
        $room = \App\Models\Kamar::findOrFail($validated['kamar_id']);
        $owner = \App\Models\User::where('role', 'owner')->first();

        // Generate Invoice Number
        $prefix = 'INV-' . date('ym') . '-';
        $lastTransaction = Transaksi::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->invoice_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $invoiceNumber = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        // Determine Sender Bank Label
        $methodLabels = [
            'cash' => 'TUNAI',
            'edc' => 'EDC / MESIN KARTU',
            'manual_transfer' => 'TRANSFER MANUAL',
        ];
        $senderBank = $methodLabels[$validated['payment_method']] ?? 'MANUAL';

        // Create Transaction
        $transaction = Transaksi::create([
            'owner_id' => $owner ? $owner->id : 1, // Fallback if no owner
            'penyewa_id' => $tenant->id,
            'kamar_id' => $room->id,
            'amount' => $validated['amount'],
            'duration_months' => $validated['duration'],
            'period_start_date' => now(),
            'period_end_date' => now()->addMonths((int) $validated['duration']),
            'invoice_number' => $invoiceNumber,
            'reference_number' => $invoiceNumber, // Use invoice as ref
            'payment_date' => now(),
            'due_date' => now(),
            'status' => 'verified_by_admin', // Skip pending
            'payment_method' => $validated['payment_method'],
            'sender_bank' => $senderBank,
            'sender_name' => $tenant->name,
            'admin_verified_at' => now(),
            'admin_verified_by' => Auth::id(),
            'admin_notes' => $validated['notes'],
            'provisional_amount' => $validated['amount'],
        ]);

        // Handle Proof Upload — store on private disk; access through authenticated view route.
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment-proofs/' . $tenant->id, 'local');
            BuktiBayar::create([
                'transaksi_id' => $transaction->id,
                'file_path' => $path,
                'file_type' => $request->file('payment_proof')->getClientMimeType(),
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
                'verified_status' => 'approved',
                'verified_notes' => 'Uploaded by Admin',
            ]);
        }

        // Log Activity
        \App\Services\LoggerService::log(
            'create_transaction',
            "Membuat transaksi manual ({$validated['payment_method']}) untuk {$tenant->name}",
            $transaction
        );

        // Notify Owner
        if ($owner) {
            Notification::create([
                'user_id' => $owner->id,
                'type' => 'payment_verified',
                'category' => 'finance',
                'title' => 'Pembayaran Manual Baru',
                'message' => "Admin mencatat pembayaran {$senderBank} dari {$tenant->name} sebesar Rp " . number_format($validated['amount'], 0, ',', '.'),
                'related_entity_type' => 'transaction',
                'related_entity_id' => $transaction->id,
                'priority' => 'high',
                'action_required' => true,
            ]);
        }

        return back()->with('success', 'Transaksi manual berhasil dicatat!');
    }
}
