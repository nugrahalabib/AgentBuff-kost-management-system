<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\BuktiBayar;
use App\Models\Kamar;
use App\Models\TipeKamar;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    /**
     * Disk + base path for payment proof files.
     * Stored on the private 'local' disk and served only via authenticated route.
     */
    private const PROOF_DISK = 'local';
    private const PROOF_BASE_DIR = 'payment-proofs';

    /**
     * Allowed booking duration in months for new bookings.
     */
    private const ALLOWED_NEW_BOOKING_DURATIONS = [1, 3, 6, 12];

    /**
     * Maximum days into the future a check-in date may be set.
     */
    private const MAX_CHECKIN_DAYS_AHEAD = 365;

    /**
     * Store booking data and redirect to payment
     */
    public function store(Request $request, $id)
    {
        $user = auth()->user();

        // Check if tenant already has a room - can only extend, not book new
        $existingRoom = $user->activeRoom;
        if ($existingRoom) {
            return redirect()->route('tenant.extend-payment')
                ->with('error', 'Anda sudah memiliki kamar yang disewa. Silakan perpanjang durasi sewa.');
        }

        // Check if tenant has a rejected payment - must re-upload first
        $rejectedTransaction = Transaksi::where('penyewa_id', $user->id)
            ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
            ->first();
        if ($rejectedTransaction) {
            return redirect()->route('tenant.booking.retry', $rejectedTransaction->id)
                ->with('error', 'Anda memiliki pembayaran yang ditolak. Silakan upload ulang bukti pembayaran terlebih dahulu.');
        }

        // Cegah pengajuan ganda: jika sudah ada transaksi yang sedang diproses.
        if ($this->hasInProgressTransaction($user->id)) {
            return redirect()->route('tenant.dashboard')
                ->with('info', 'Anda sudah memiliki pengajuan sewa yang sedang diproses. Mohon tunggu verifikasi pembayaran sebelumnya selesai.');
        }

        $request->validate([
            'phone' => 'required|digits_between:8,15',
            'check_in_date' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . now()->addDays(self::MAX_CHECKIN_DAYS_AHEAD)->toDateString(),
            ],
            'duration' => ['required', 'integer', 'in:' . implode(',', self::ALLOWED_NEW_BOOKING_DURATIONS)],
            'kamar_id' => 'nullable|integer|exists:kamar,id',
        ]);

        $profile = $user->tenantProfile ?? new \App\Models\Penyewa(['user_id' => $user->id]);
        $profile->phone = $request->phone;
        $profile->save();

        // Get room type for pricing
        $roomType = TipeKamar::findOrFail($id);

        // Get selected room if provided
        $selectedRoom = null;
        $roomNumber = null;
        if ($request->has('kamar_id')) {
            $selectedRoom = Kamar::where('id', $request->kamar_id)
                ->where('tipe_kamar_id', $id)
                ->hasAvailableSlot()
                ->first();
            $roomNumber = $selectedRoom?->room_number;
        }

        // Store booking data in session for payment page.
        // NOTE: We do NOT store the price here. Price is recomputed from the DB at
        // confirmPayment() to prevent client-side tampering.
        session([
            'booking' => [
                'tipe_kamar_id' => $id,
                'room_type_name' => $roomType->name,
                'kamar_id' => $selectedRoom?->id,
                'room_number' => $roomNumber,
                'capacity' => $roomType->capacity,
                'check_in_date' => $request->check_in_date,
                'duration' => (int) $request->duration,
                'phone' => $request->phone,
                // Nomor invoice tampilan (referensi sementara). Transaksi final
                // dibuat di confirmPayment() dengan nomornya sendiri.
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)),
                // Display-only price snapshot for the payment page. NOT trusted by backend.
                'display_price_per_month' => $roomType->rent_per_person,
                'display_total_amount' => $roomType->rent_per_person * (int) $request->duration,
            ]
        ]);

        return redirect()->route('tenant.booking.payment');
    }

    /**
     * Show payment page with booking data
     */
    public function showPayment()
    {
        $booking = session('booking');

        if (!$booking) {
            return redirect()->route('welcome')->with('error', 'Sesi booking tidak ditemukan. Silakan mulai dari awal.');
        }

        // Get owner's business settings for bank info (single-owner system)
        $owner = User::where('role', 'owner')->first();
        $businessSettings = $owner ? $owner->businessSettings : null;

        // Backward-compat: expose computed price under legacy session keys for the view.
        $booking['price_per_month'] = $booking['display_price_per_month'] ?? null;
        $booking['total_amount'] = $booking['display_total_amount'] ?? null;

        return view('penyewa.pembayaran', [
            'booking' => $booking,
            'pemilik' => $owner,
            'businessSettings' => $businessSettings,
        ]);
    }

    /**
     * Process payment confirmation with proof upload
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'sender_bank' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\/\.\-]+$/'],
            'sender_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\-]+$/'],
        ]);

        $user = Auth::user();

        // Check if tenant has a rejected payment - must resolve first
        $rejectedTransaction = Transaksi::where('penyewa_id', $user->id)
            ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
            ->first();
        if ($rejectedTransaction) {
            return response()->json([
                'success' => false,
                'message' => 'Anda memiliki pembayaran yang ditolak. Silakan selesaikan terlebih dahulu.'
            ], 400);
        }

        // Cegah pengajuan/pembayaran ganda: blokir bila sudah ada transaksi diproses.
        if ($this->hasInProgressTransaction($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki pengajuan pembayaran yang sedang diproses. Mohon tunggu verifikasi.',
            ], 409);
        }

        $booking = session('booking');

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi booking tidak ditemukan.'
            ], 400);
        }

        try {
            $proofFile = $request->file('payment_proof');
            $senderBank = $request->sender_bank;
            $senderName = $request->sender_name;

            $transaction = DB::transaction(function () use ($user, $booking, $proofFile, $senderBank, $senderName) {
                $owner = User::where('role', 'owner')->first();
                if (!$owner) {
                    throw new \RuntimeException('Pemilik kos tidak ditemukan dalam sistem.');
                }

                // Re-validate that tenant still has no active room (single-room rule).
                if ($user->fresh()->activeRoom) {
                    throw new \RuntimeException('Anda sudah memiliki kamar aktif. Silakan perpanjang sewa.');
                }

                // Resolve the room with row-level lock to prevent double-booking races.
                $roomId = $booking['kamar_id'] ?? null;
                if ($roomId) {
                    $room = Kamar::where('id', $roomId)->lockForUpdate()->first();
                } else {
                    // Auto-pick: lock candidates in deterministic order to avoid deadlocks.
                    $candidates = Kamar::where('tipe_kamar_id', $booking['tipe_kamar_id'])
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->get();
                    $room = $candidates->first(fn($r) => $r->hasAvailableSlot());
                }

                if (!$room || !$room->hasAvailableSlot()) {
                    throw new \RuntimeException('Kamar tidak tersedia atau sudah penuh.');
                }

                // Recompute the price from DB (do NOT trust session/client).
                $roomType = TipeKamar::findOrFail($booking['tipe_kamar_id']);
                $duration = (int) $booking['duration'];
                if (!in_array($duration, self::ALLOWED_NEW_BOOKING_DURATIONS, true)) {
                    throw new \RuntimeException('Durasi sewa tidak valid.');
                }
                $pricePerMonth = (float) $roomType->rent_per_person;
                $totalAmount = $pricePerMonth * $duration;

                // Generate invoice number inside the transaction so the lock protects uniqueness.
                $invoiceNumber = $this->generateInvoiceNumber();

                $checkInDate = \Carbon\Carbon::parse($booking['check_in_date'])->startOfDay();
                if ($checkInDate->lt(now()->startOfDay())) {
                    throw new \RuntimeException('Tanggal check-in tidak boleh di masa lalu.');
                }
                $periodEndDate = $checkInDate->copy()->addMonths($duration);

                $transaction = Transaksi::create([
                    'owner_id' => $owner->id,
                    'penyewa_id' => $user->id,
                    'kamar_id' => $room->id,
                    'amount' => $totalAmount,
                    'duration_months' => $duration,
                    'period_start_date' => $checkInDate,
                    'period_end_date' => $periodEndDate,
                    'reference_number' => $invoiceNumber,
                    'invoice_number' => $invoiceNumber,
                    'payment_date' => now(),
                    'due_date' => now()->addDays(1),
                    'status' => 'pending_verification',
                    'payment_method' => 'bank_transfer',
                    'sender_bank' => $senderBank,
                    'sender_name' => $senderName,
                ]);

                // Store proof on private disk under a per-user prefix.
                $path = $proofFile->store(self::PROOF_BASE_DIR . '/' . $user->id, self::PROOF_DISK);

                BuktiBayar::create([
                    'transaksi_id' => $transaction->id,
                    'file_path' => $path,
                    'file_type' => $proofFile->getClientMimeType(),
                    'uploaded_by' => $user->id,
                    'uploaded_at' => now(),
                    'verified_status' => 'pending',
                ]);

                // Notify owner.
                \App\Models\Notification::create([
                    'user_id' => $owner->id,
                    'type' => 'payment_received',
                    'category' => 'finance',
                    'title' => 'Pembayaran Baru',
                    'message' => $user->name . ' (Kamar ' . ($room->room_number ?? 'Auto') . ') telah mengupload bukti transfer sebesar Rp ' . number_format($totalAmount, 0, ',', '.'),
                    'related_entity_type' => 'transaction',
                    'related_entity_id' => $transaction->id,
                    'priority' => 'high',
                    'action_required' => true,
                    'status' => 'unread',
                ]);

                return $transaction;
            });

            // Audit trail for tenant financial action.
            \App\Services\LoggerService::log(
                'tenant_payment_created',
                "Penyewa {$user->name} upload bukti pembayaran sebesar Rp " . number_format((float) $transaction->amount, 0, ',', '.') . " (invoice {$transaction->invoice_number})",
                $transaction
            );

            session()->forget('booking');
            session(['completed_transaction_id' => $transaction->id]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi!',
                'redirect' => route('tenant.booking.success'),
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Booking confirmPayment failed', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $this->safeErrorMessage($e, 'Gagal memproses pembayaran. Silakan coba lagi.'),
            ], 500);
        }
    }

    /**
     * Surface user-friendly exception messages without leaking internals
     * (DB errors, stack traces, file paths). Domain errors we throw ourselves
     * are RuntimeException with intentional messages — those are safe to show.
     * Everything else gets the generic fallback.
     */
    private function safeErrorMessage(\Throwable $e, string $fallback): string
    {
        return $e instanceof \RuntimeException ? $e->getMessage() : $fallback;
    }

    /**
     * Generate unique invoice number under a row lock.
     * Format: INV-YYMM-XXXXX
     *
     * Must be called inside a DB transaction so the SELECT ... FOR UPDATE actually holds.
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('ym') . '-';

        $lastTransaction = Transaksi::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->lockForUpdate()
            ->first();

        $newNumber = $lastTransaction
            ? ((int) substr($lastTransaction->invoice_number, -5)) + 1
            : 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Apakah penyewa sudah punya transaksi yang sedang berjalan
     * (menunggu verifikasi admin/owner)? Dipakai untuk mencegah pengajuan ganda.
     */
    private function hasInProgressTransaction(int $userId): bool
    {
        return Transaksi::where('penyewa_id', $userId)
            ->whereIn('status', ['pending_verification', 'verified_by_admin'])
            ->exists();
    }

    /**
     * Show extend payment page for tenants who already have a room
     */
    public function showExtendPayment()
    {
        $user = Auth::user();

        // Check if tenant has a rejected payment - must resolve first
        $rejectedTransaction = Transaksi::where('penyewa_id', $user->id)
            ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
            ->first();
        if ($rejectedTransaction) {
            return redirect()->route('tenant.booking.retry', $rejectedTransaction->id)
                ->with('error', 'Anda memiliki pembayaran yang ditolak. Silakan selesaikan terlebih dahulu sebelum membuat transaksi baru.');
        }

        // Get tenant's current room
        $currentRoom = $user->activeRoom;
        if ($currentRoom) {
            $currentRoom->load('roomType');
        }

        if (!$currentRoom) {
            return redirect()->route('welcome')
                ->with('error', 'Anda belum memiliki kamar yang disewa.');
        }

        // Get owner's business settings for bank info
        $owner = User::where('role', 'owner')->first();
        $businessSettings = $owner ? $owner->businessSettings : null;

        // Get booking session data if exists
        $extendBooking = session('extend_booking');

        return view('penyewa.perpanjang-sewa', [
            'kamar' => $currentRoom,
            'pemilik' => $owner,
            'businessSettings' => $businessSettings,
            'extendBooking' => $extendBooking,
        ]);
    }

    /**
     * Store extend payment data
     */
    public function storeExtendPayment(Request $request)
    {
        $request->validate([
            'duration' => 'required|integer|min:1|max:24',
        ]);

        $user = Auth::user();

        // Check if tenant has a rejected payment - must resolve first
        $rejectedTransaction = Transaksi::where('penyewa_id', $user->id)
            ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
            ->first();
        if ($rejectedTransaction) {
            return redirect()->route('tenant.booking.retry', $rejectedTransaction->id)
                ->with('error', 'Anda memiliki pembayaran yang ditolak. Silakan selesaikan terlebih dahulu.');
        }

        // Get tenant's current room
        $currentRoom = $user->activeRoom;
        if ($currentRoom) {
            $currentRoom->load('roomType');
        }

        if (!$currentRoom) {
            return back()->with('error', 'Anda belum memiliki kamar yang disewa.');
        }

        $duration = (int) $request->duration;
        $pricePerMonth = (float) $currentRoom->roomType->rent_per_person;
        $totalAmount = $pricePerMonth * $duration;

        // Store in session — price is display-only and recomputed at confirm.
        session([
            'extend_booking' => [
                'kamar_id' => $currentRoom->id,
                'room_number' => $currentRoom->room_number,
                'room_type_name' => $currentRoom->roomType->name,
                'duration' => $duration,
                'price_per_month' => $pricePerMonth,
                'total_amount' => $totalAmount,
            ]
        ]);

        return redirect()->route('tenant.extend-payment');
    }

    /**
     * Confirm extend payment with proof upload
     */
    public function confirmExtendPayment(Request $request)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'sender_bank' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\/\.\-]+$/'],
            'sender_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\-]+$/'],
        ]);

        $user = Auth::user();

        $rejectedTransaction = Transaksi::where('penyewa_id', $user->id)
            ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
            ->first();
        if ($rejectedTransaction) {
            return response()->json([
                'success' => false,
                'message' => 'Anda memiliki pembayaran yang ditolak. Silakan selesaikan terlebih dahulu.'
            ], 400);
        }

        $extendBooking = session('extend_booking');

        if (!$extendBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi perpanjangan tidak ditemukan.'
            ], 400);
        }

        try {
            $proofFile = $request->file('payment_proof');
            $senderBank = $request->sender_bank;
            $senderName = $request->sender_name;

            $transaction = DB::transaction(function () use ($user, $extendBooking, $proofFile, $senderBank, $senderName) {
                $owner = User::where('role', 'owner')->first();
                if (!$owner) {
                    throw new \RuntimeException('Pemilik kos tidak ditemukan dalam sistem.');
                }

                // Lock the room to prevent concurrent extend / checkout races.
                $currentRoom = Kamar::where('id', $extendBooking['kamar_id'])->lockForUpdate()->first();
                if (!$currentRoom) {
                    throw new \RuntimeException('Kamar tidak ditemukan atau telah dihapus.');
                }

                // Recompute the price from DB.
                $currentRoom->load('roomType');
                $duration = (int) $extendBooking['duration'];
                if ($duration < 1 || $duration > 24) {
                    throw new \RuntimeException('Durasi perpanjangan tidak valid.');
                }
                $pricePerMonth = (float) $currentRoom->roomType->rent_per_person;
                $totalAmount = $pricePerMonth * $duration;

                $invoiceNumber = $this->generateInvoiceNumber();

                // Extension starts from this user's latest verified period_end_date,
                // not from a shared kamar.lease_end_date (which is wrong for duo rooms).
                $latestVerifiedTx = Transaksi::where('penyewa_id', $user->id)
                    ->where('kamar_id', $currentRoom->id)
                    ->where('status', 'verified_by_owner')
                    ->orderByDesc('period_end_date')
                    ->first();

                $periodStartDate = $latestVerifiedTx && $latestVerifiedTx->period_end_date
                    ? \Carbon\Carbon::parse($latestVerifiedTx->period_end_date)
                    : now();
                $periodEndDate = $periodStartDate->copy()->addMonths($duration);

                $transaction = Transaksi::create([
                    'owner_id' => $owner->id,
                    'penyewa_id' => $user->id,
                    'kamar_id' => $currentRoom->id,
                    'amount' => $totalAmount,
                    'duration_months' => $duration,
                    'period_start_date' => $periodStartDate,
                    'period_end_date' => $periodEndDate,
                    'reference_number' => $invoiceNumber,
                    'invoice_number' => $invoiceNumber,
                    'payment_date' => now(),
                    'due_date' => now()->addDays(1),
                    'status' => 'pending_verification',
                    'payment_method' => 'bank_transfer',
                    'sender_bank' => $senderBank,
                    'sender_name' => $senderName,
                ]);

                $path = $proofFile->store(self::PROOF_BASE_DIR . '/' . $user->id, self::PROOF_DISK);

                BuktiBayar::create([
                    'transaksi_id' => $transaction->id,
                    'file_path' => $path,
                    'file_type' => $proofFile->getClientMimeType(),
                    'uploaded_by' => $user->id,
                    'uploaded_at' => now(),
                    'verified_status' => 'pending',
                ]);

                \App\Models\Notification::create([
                    'user_id' => $owner->id,
                    'type' => 'payment_received',
                    'category' => 'finance',
                    'title' => 'Perpanjangan Sewa',
                    'message' => $user->name . ' (Kamar ' . ($currentRoom->room_number ?? '-') . ') telah mengupload bukti perpanjangan sewa sebesar Rp ' . number_format($totalAmount, 0, ',', '.'),
                    'related_entity_type' => 'transaction',
                    'related_entity_id' => $transaction->id,
                    'priority' => 'high',
                    'action_required' => true,
                    'status' => 'unread',
                ]);

                return $transaction;
            });

            \App\Services\LoggerService::log(
                'tenant_extension_created',
                "Penyewa {$user->name} upload bukti perpanjangan sewa sebesar Rp " . number_format((float) $transaction->amount, 0, ',', '.') . " (invoice {$transaction->invoice_number})",
                $transaction
            );

            session()->forget('extend_booking');
            session(['completed_transaction_id' => $transaction->id]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran perpanjangan berhasil dikonfirmasi!',
                'redirect' => route('tenant.booking.success'),
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Booking confirmExtendPayment failed', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $this->safeErrorMessage($e, 'Gagal memproses perpanjangan. Silakan coba lagi.'),
            ], 500);
        }
    }

    /**
     * Download PDF receipt for a transaction
     */
    public function downloadReceipt($id)
    {
        $user = Auth::user();
        $transaction = Transaksi::with(['room.roomType', 'tenant', 'paymentProofs'])
            ->where('id', $id)
            ->where('penyewa_id', $user->id)
            ->firstOrFail();

        // Get business settings for logo/company info
        $owner = User::where('role', 'owner')->first();
        $businessSettings = $owner ? $owner->businessSettings : null;

        $pdf = app('dompdf.wrapper')->loadView('pdf.receipt', [
            'transaksiItem' => $transaction,
            'businessSettings' => $businessSettings,
        ]);

        $filename = 'Receipt-' . $transaction->invoice_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Stream a payment proof file to authorized viewers.
     * Authorization rules:
     *   - Tenant: only the uploader (own transaction)
     *   - Owner / Admin: anyone with that role (single-owner system)
     */
    public function viewPaymentProof($proofId)
    {
        $user = Auth::user();
        $proof = BuktiBayar::with('transaction')->findOrFail($proofId);
        $transaction = $proof->transaction;

        $isOwner = $user->role === 'owner';
        $isAdmin = $user->role === 'admin';
        $isUploader = $user->role === 'tenant' && $transaction && $transaction->penyewa_id === $user->id;

        if (!$isOwner && !$isAdmin && !$isUploader) {
            abort(403);
        }

        $disk = Storage::disk(self::PROOF_DISK);
        if (!$disk->exists($proof->file_path)) {
            abort(404);
        }

        return response()->file($disk->path($proof->file_path), [
            'Content-Type' => $proof->file_type ?: 'application/octet-stream',
        ]);
    }

    /**
     * Show retry payment page for rejected transactions
     */
    public function showRetryPayment($transactionId)
    {
        $user = Auth::user();

        // Get the rejected transaction
        $transaction = Transaksi::with(['room.roomType', 'paymentProofs'])
            ->where('id', $transactionId)
            ->where('penyewa_id', $user->id)
            ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
            ->firstOrFail();

        // Get owner's business settings for bank info
        $owner = User::where('role', 'owner')->first();
        $businessSettings = $owner ? $owner->businessSettings : null;

        // Calculate price per month from room type
        $pricePerMonth = $transaction->room?->roomType?->rent_per_person ?? ($transaction->amount / max($transaction->duration_months, 1));

        return view('penyewa.retry-payment', [
            'transaksiItem' => $transaction,
            'businessSettings' => $businessSettings,
            'pricePerMonth' => $pricePerMonth,
        ]);
    }

    /**
     * Process retry payment with new proof upload
     */
    public function confirmRetryPayment(Request $request, $transactionId)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'sender_bank' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\/\.\-]+$/'],
            'sender_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\-]+$/'],
        ]);

        $user = Auth::user();

        // Get the rejected transaction (with ownership + status guard)
        $transaction = Transaksi::with(['paymentProofs', 'room.roomType'])
            ->where('id', $transactionId)
            ->where('penyewa_id', $user->id)
            ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
            ->firstOrFail();

        try {
            $proofFile = $request->file('payment_proof');
            $senderBank = $request->sender_bank;
            $senderName = $request->sender_name;

            DB::transaction(function () use ($user, $transaction, $proofFile, $senderBank, $senderName) {
                $path = $proofFile->store(self::PROOF_BASE_DIR . '/' . $user->id, self::PROOF_DISK);

                BuktiBayar::create([
                    'transaksi_id' => $transaction->id,
                    'file_path' => $path,
                    'file_type' => $proofFile->getClientMimeType(),
                    'uploaded_by' => $user->id,
                    'uploaded_at' => now(),
                    'verified_status' => 'pending',
                ]);

                $transaction->update([
                    'sender_bank' => $senderBank,
                    'sender_name' => $senderName,
                    'status' => 'pending_verification',
                    'admin_verified_at' => null,
                    'admin_verified_by' => null,
                    'admin_notes' => null,
                    'owner_verified_at' => null,
                    'owner_verified_by' => null,
                    'owner_notes' => null,
                ]);

                \App\Models\Notification::where('user_id', $user->id)
                    ->where('type', 'payment_rejected')
                    ->where('related_entity_id', $transaction->id)
                    ->update(['status' => 'archived']);

                $owner = User::where('role', 'owner')->first();
                if ($owner) {
                    \App\Models\Notification::create([
                        'user_id' => $owner->id,
                        'type' => 'payment_received',
                        'category' => 'finance',
                        'title' => 'Revisi Bukti Bayar',
                        'message' => $user->name . ' telah mengupload ulang bukti pembayaran invoice ' . $transaction->invoice_number,
                        'related_entity_type' => 'transaction',
                        'related_entity_id' => $transaction->id,
                        'priority' => 'high',
                        'action_required' => true,
                        'status' => 'unread',
                    ]);
                }
            });

            \App\Services\LoggerService::log(
                'tenant_payment_retry',
                "Penyewa {$user->name} upload ulang bukti pembayaran (invoice {$transaction->invoice_number})",
                $transaction
            );

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran baru berhasil diupload! Menunggu verifikasi.',
                'redirect' => route('tenant.dashboard'),
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Booking confirmRetryPayment failed', [
                'user_id' => $user->id ?? null,
                'transaction_id' => $transaction->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $this->safeErrorMessage($e, 'Gagal memproses ulang pembayaran. Silakan coba lagi.'),
            ], 500);
        }
    }

    /**
     * Cancel a rejected transaction.
     * Also cleans up any uploaded proof files so they don't linger in storage.
     */
    public function cancelTransaction($transactionId)
    {
        $user = Auth::user();

        $transaction = Transaksi::with('paymentProofs')
            ->where('id', $transactionId)
            ->where('penyewa_id', $user->id)
            ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
            ->first();

        if (!$transaction) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'Transaksi tidak ditemukan atau tidak dapat dibatalkan.');
        }

        try {
            DB::transaction(function () use ($transaction, $user) {
                // Delete proof files from storage. We try both disks because legacy
                // uploads may still live on the public disk from before the migration
                // to private storage.
                foreach ($transaction->paymentProofs as $proof) {
                    if (!$proof->file_path) {
                        continue;
                    }
                    foreach ([self::PROOF_DISK, 'public'] as $disk) {
                        try {
                            if (Storage::disk($disk)->exists($proof->file_path)) {
                                Storage::disk($disk)->delete($proof->file_path);
                            }
                        } catch (\Throwable $e) {
                            // Swallow individual disk errors so the cancel itself still succeeds.
                        }
                    }
                }

                $transaction->update(['status' => 'cancelled_by_tenant']);

                \App\Models\Notification::where('user_id', $user->id)
                    ->where('type', 'payment_rejected')
                    ->where('related_entity_id', $transaction->id)
                    ->update(['status' => 'archived']);
            });

            \App\Services\LoggerService::log(
                'tenant_payment_cancelled',
                "Penyewa {$user->name} membatalkan transaksi (invoice {$transaction->invoice_number})",
                $transaction
            );

            return redirect()->route('tenant.dashboard')
                ->with('success', 'Transaksi berhasil dibatalkan. Anda sekarang dapat membuat transaksi baru.');

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Booking cancelTransaction failed', [
                'user_id' => $user->id ?? null,
                'transaction_id' => $transaction->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('tenant.dashboard')
                ->with('error', $this->safeErrorMessage($e, 'Gagal membatalkan transaksi. Silakan coba lagi.'));
        }
    }
}
