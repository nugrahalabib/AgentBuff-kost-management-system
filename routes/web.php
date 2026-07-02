<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KamarController as AdminKamarController;
use App\Http\Controllers\Admin\PenyewaController as AdminPenyewaController;
use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\PemilikKos\DashboardController as OwnerDashboardController;
use App\Http\Controllers\PemilikKos\KamarController as OwnerKamarController;
use App\Http\Controllers\PemilikKos\TipeKamarController;
use App\Http\Controllers\PemilikKos\PenyewaController as OwnerPenyewaController;
use App\Http\Controllers\PemilikKos\TransactionController;
use App\Http\Controllers\PemilikKos\LaporanController as OwnerLaporanController;
use App\Http\Controllers\PemilikKos\NotificationController as OwnerNotificationController;
use App\Http\Controllers\PemilikKos\SettingsController;
use App\Http\Controllers\PemilikKos\AdminManagementController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Sitemap XML untuk mesin pencari (Google, Bing)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Middleware untuk user yang sudah login
Route::middleware(['auth', 'verified'])->group(function () {

    // Authenticated streaming of payment proof files (private storage).
    // Authorization (own tenant, any owner/admin) is enforced in the controller.
    Route::get('/payment-proof/{proof}/view', [\App\Http\Controllers\Penyewa\BookingController::class, 'viewPaymentProof'])
        ->name('payment-proof.view');

    // Authenticated streaming of tenant identity documents (KTP, KK, etc.).
    Route::get('/tenant-document/{user}/{type}', [\App\Http\Controllers\Penyewa\ProfilController::class, 'viewDocument'])
        ->name('tenant-document.view');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Redirect based on user role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'owner') {
            return redirect()->route('owner.dashboard');
        } else {
            // Default to tenant dashboard
            return redirect()->route('tenant.dashboard');
        }
    })->middleware(['auth', 'verified'])->name('dashboard');

    // Halaman profil (dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route tenant
    Route::prefix('tenant')->name('tenant.')->middleware(['auth', 'verified', 'role:tenant'])->group(function () {
        // 1. Dashboard Tenant
        Route::get('/dashboard', [\App\Http\Controllers\Penyewa\ProfilController::class, 'dashboard'])->name('dashboard');

        // 2. Profile Update Routes
        Route::post('/profile/personal', [\App\Http\Controllers\Penyewa\ProfilController::class, 'updatePersonal'])->name('profile.personal');
        Route::post('/profile/guardian', [\App\Http\Controllers\Penyewa\ProfilController::class, 'updateGuardian'])->name('profile.guardian');
        Route::post('/profile/documents', [\App\Http\Controllers\Penyewa\ProfilController::class, 'updateDocuments'])
            ->middleware('throttle:10,1')
            ->name('profile.documents');

        // 2.5 Notification routes
        Route::delete('/notification/{id}', [\App\Http\Controllers\Penyewa\ProfilController::class, 'dismissNotification'])->name('notification.dismiss');

        // 3. Detail Kamar  
        Route::get('/kamar/{id}', function ($id) {
            $user = auth()->user();
            
            $user = auth()->user();

            // Check if tenant already has a room - redirect to extension page
            if ($user->activeRoom) {
                return redirect()->route('tenant.extend-payment')
                    ->with('info', 'Maaf, Anda sudah memiliki kamar. Anda tidak bisa memesan kamar lain kecuali periode sewa kamar Anda sudah habis dan Anda tidak melakukan perpanjang sewa.');
            }
            
            // Check if tenant has a rejected payment - must re-upload first
            $rejectedTransaction = \App\Models\Transaksi::where('penyewa_id', $user->id)
                ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
                ->first();
            if ($rejectedTransaction) {
                return redirect()->route('tenant.booking.retry', $rejectedTransaction->id)
                    ->with('error', 'Anda memiliki pembayaran yang ditolak. Silakan upload ulang bukti pembayaran terlebih dahulu.');
            }
            
            $room = \App\Models\TipeKamar::findOrFail($id);
            // Get rooms with available slots (for Duo rooms, shows even if 1 person already there)
            $availableRooms = \App\Models\Kamar::where('tipe_kamar_id', $id)
                ->hasAvailableSlot()
                ->orderBy('room_number', 'asc')
                ->get();
            return view('penyewa.detail-kamar', ['kamar' => $room, 'kamarTersedia' => $availableRooms]);
        })->name('room.detail');

        // 4. Formulir Reservasi    
        Route::get('/booking/{id}/create', function (\Illuminate\Http\Request $request, $id) {
            $user = auth()->user();
            
            // Check if tenant already has a room
            if ($user->activeRoom) {
                return redirect()->route('tenant.extend-payment')
                    ->with('info', 'Maaf, Anda sudah memiliki kamar. Anda tidak bisa memesan kamar lain kecuali periode sewa kamar Anda sudah habis dan Anda tidak melakukan perpanjang sewa.');
            }
            
            // Check if tenant has a rejected payment - must re-upload first
            $rejectedTransaction = \App\Models\Transaksi::where('penyewa_id', $user->id)
                ->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])
                ->first();
            if ($rejectedTransaction) {
                return redirect()->route('tenant.booking.retry', $rejectedTransaction->id)
                    ->with('error', 'Anda memiliki pembayaran yang ditolak. Silakan upload ulang bukti pembayaran terlebih dahulu.');
            }
            
            $roomType = \App\Models\TipeKamar::findOrFail($id);
            $profile = $user->tenantProfile;
            
            // Get selected room from query string
            $selectedRoom = null;
            if ($request->has('kamar_id')) {
                $selectedRoom = \App\Models\Kamar::where('id', $request->kamar_id)
                    ->where('tipe_kamar_id', $id)
                    ->hasAvailableSlot()
                    ->first();
            }
            
            return view('penyewa.booking-form', [
                'tipeKamarItem' => $roomType,
                'profile' => $profile,
                'kamarDipilih' => $selectedRoom,
            ]);
        })->name('booking.create');
        
        // 4.1 Submit Booking (saves phone to profile and calculates price)
        Route::post('/booking/{id}/store', [\App\Http\Controllers\Penyewa\BookingController::class, 'store'])->name('booking.store');

        // 5. Formulir Pembayaran   
        Route::get('/booking/payment', [\App\Http\Controllers\Penyewa\BookingController::class, 'showPayment'])->name('booking.payment');

        // 5.1 Konfirmasi Pembayaran (upload bukti) — throttle to cap proof-upload abuse.
        Route::post('/booking/payment/confirm', [\App\Http\Controllers\Penyewa\BookingController::class, 'confirmPayment'])
            ->middleware('throttle:10,1')
            ->name('booking.payment.confirm');

        // 6. Sukses Bayar  
        Route::get('/booking/success', function () {
            $transactionId = session('completed_transaction_id');
            if (!$transactionId) {
                return redirect()->route('tenant.dashboard');
            }
            $transaction = \App\Models\Transaksi::with('room.roomType')->find($transactionId);
            return view('penyewa.pembayaran-berhasil', ['transaksiItem' => $transaction]);
        })->name('booking.success');

        // 6.1 Download Receipt PDF
        Route::get('/transaction/{id}/receipt', [\App\Http\Controllers\Penyewa\BookingController::class, 'downloadReceipt'])->name('transaction.receipt');

        // 7. Perpanjangan Sewa (for tenants who already have a room)
        Route::get('/extend-payment', [\App\Http\Controllers\Penyewa\BookingController::class, 'showExtendPayment'])->name('extend-payment');
        Route::post('/extend-payment/store', [\App\Http\Controllers\Penyewa\BookingController::class, 'storeExtendPayment'])->name('extend-payment.store');
        Route::post('/extend-payment/confirm', [\App\Http\Controllers\Penyewa\BookingController::class, 'confirmExtendPayment'])
            ->middleware('throttle:10,1')
            ->name('extend-payment.confirm');
        Route::get('/extend-payment/cancel', function () {
            session()->forget('extend_booking');
            return redirect()->route('tenant.dashboard')->with('info', 'Perpanjangan sewa dibatalkan.');
        })->name('extend-payment.cancel');

        // 8. Retry Payment (for rejected transactions)
        Route::get('/booking/retry/{transaction}', [\App\Http\Controllers\Penyewa\BookingController::class, 'showRetryPayment'])->name('booking.retry');
        Route::post('/booking/retry/{transaction}/confirm', [\App\Http\Controllers\Penyewa\BookingController::class, 'confirmRetryPayment'])
            ->middleware('throttle:10,1')
            ->name('booking.retry.confirm');
        Route::post('/booking/{transaction}/cancel', [\App\Http\Controllers\Penyewa\BookingController::class, 'cancelTransaction'])->name('booking.cancel');
    });

    // Route owner
    Route::prefix('owner')
        ->name('owner.')
        ->middleware(['auth', 'verified', 'role:owner'])
        ->group(function () {

            // 1. Dashboard Owner
            Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

            // 2. Data Kamar
            Route::get('/kamar', [OwnerKamarController::class, 'index'])->name('kamar');

            // 3. Data Penyewa
            Route::get('/penyewa', [OwnerPenyewaController::class, 'index'])->name('penyewa');
            Route::get('/penyewa/{user}', [OwnerPenyewaController::class, 'show'])->name('penyewa.show');

            // 4. Laporan Cashflow
            Route::get('/laporan', [OwnerLaporanController::class, 'index'])->name('laporan');
            Route::get('/laporan/export-pdf', [OwnerLaporanController::class, 'exportCashflowPdf'])->name('laporan.export-pdf');
            Route::get('/laporan/export-excel', [OwnerLaporanController::class, 'exportCashflowExcel'])->name('laporan.export-excel');
            Route::post('/laporan/expense', [OwnerLaporanController::class, 'storeExpense'])->name('laporan.expense.store');
            Route::delete('/laporan/expense/{expense}', [OwnerLaporanController::class, 'destroyExpense'])->name('laporan.expense.destroy');
            Route::get('/laporan/{report}/preview', [OwnerLaporanController::class, 'preview'])->name('laporan.preview');
            Route::get('/laporan/{report}/download-pdf', [OwnerLaporanController::class, 'downloadPdf'])->name('laporan.download-pdf');
            Route::get('/laporan/{report}/download-excel', [OwnerLaporanController::class, 'downloadExcel'])->name('laporan.download-excel');

            // 4.5 Verifikasi Transaksi
            Route::get('/verifikasi-transaksi', [TransactionController::class, 'index'])->name('verifikasi-transaksi');
            Route::post('/verifikasi-transaksi/{transaction}/verify', [TransactionController::class, 'verify'])->name('verifikasi-transaksi.verify');
            Route::delete('/verifikasi-transaksi/{transaction}', [TransactionController::class, 'destroy'])->name('verifikasi-transaksi.destroy');

            // 5. Manajemen Admin
            Route::get('/admin-control', [AdminManagementController::class, 'index'])->name('admin');
            Route::post('/admin-control/store', [AdminManagementController::class, 'store'])->name('admin.store');
            Route::patch('/admin-control/{user}', [AdminManagementController::class, 'update'])->name('admin.update');
            Route::post('/admin-control/{user}/toggle', [AdminManagementController::class, 'toggleStatus'])->name('admin.toggle');
            Route::post('/admin-control/{user}/reset-password', [AdminManagementController::class, 'resetPassword'])->name('admin.reset-password');
            Route::delete('/admin-control/{user}', [AdminManagementController::class, 'destroy'])->name('admin.destroy');
            Route::get('/admin-control/audit-log/export', [AdminManagementController::class, 'exportAuditLog'])->name('admin.export-audit');

            // 6. Pengaturan
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
            Route::post('/settings/business', [SettingsController::class, 'updateBusinessSettings'])->name('settings.business');
            Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
            Route::post('/settings/password', [SettingsController::class, 'changePassword'])->name('settings.password');
            Route::post('/settings/bank', [SettingsController::class, 'updateBankSettings'])->name('settings.bank');
            Route::post('/settings/update-all', [SettingsController::class, 'updateAll'])->name('settings.update-all');

            // 6.5 Manajemen Tipe Kamar
            Route::post('/room-types', [TipeKamarController::class, 'store'])->name('room-types.store');
            Route::patch('/room-types/{roomType}', [TipeKamarController::class, 'update'])->name('room-types.update');
            Route::delete('/room-types/{roomType}', [TipeKamarController::class, 'destroy'])->name('room-types.destroy');

            // 7. Notifikasi
            Route::get('/notifikasi', [OwnerNotificationController::class, 'index'])->name('notifikasi');
            Route::post('/notifikasi/{notification}/archive', [OwnerNotificationController::class, 'archive'])->name('notifikasi.archive');

            // 8. AI Assistant
            Route::get('/ai-assistant', [\App\Http\Controllers\PemilikKos\AiAssistantController::class, 'index'])->name('ai');
            // Throttle the chat endpoint so an abusive caller can't run up the Gemini bill.
            Route::post('/ai-assistant/chat', [\App\Http\Controllers\PemilikKos\AiAssistantController::class, 'chat'])
                ->middleware('throttle:20,1')
                ->name('ai.chat');
            Route::get('/ai-assistant/sessions', [\App\Http\Controllers\PemilikKos\AiAssistantController::class, 'getSessions'])->name('ai.sessions');
            Route::get('/ai-assistant/sessions/{id}', [\App\Http\Controllers\PemilikKos\AiAssistantController::class, 'getSessionMessages'])->name('ai.sessions.messages');
            Route::patch('/ai-assistant/sessions/{id}', [\App\Http\Controllers\PemilikKos\AiAssistantController::class, 'renameSession'])->name('ai.sessions.rename');
            Route::delete('/ai-assistant/sessions/{id}', [\App\Http\Controllers\PemilikKos\AiAssistantController::class, 'deleteSession'])->name('ai.sessions.delete');
            Route::get('/ai-assistant/health', [\App\Http\Controllers\PemilikKos\AiAssistantController::class, 'healthCheck'])->name('ai.health');

        });




    // Route admin
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin', 'log.admin.activity'])->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Data Kamar
        Route::get('/kamar', [AdminKamarController::class, 'index'])->name('kamar');
        Route::get('/kamar/create', [AdminKamarController::class, 'create'])->name('kamar.create');
        Route::post('/kamar', [AdminKamarController::class, 'store'])->name('kamar.store');
        Route::patch('/kamar/{room}/status', [AdminKamarController::class, 'updateStatus'])->name('kamar.updateStatus');
        Route::delete('/kamar/{room}', [AdminKamarController::class, 'destroy'])->name('kamar.destroy');

        // Data Penyewa
        Route::get('/penyewa', [AdminPenyewaController::class, 'index'])->name('penyewa');
        Route::get('/penyewa/unverified', [AdminPenyewaController::class, 'unverified'])->name('penyewa.unverified');
        Route::get('/penyewa/{user}', [AdminPenyewaController::class, 'show'])->name('penyewa.show');
        Route::post('/penyewa/{user}/verify', [AdminPenyewaController::class, 'verify'])->name('penyewa.verify');
        Route::post('/penyewa/{user}/reminder', [AdminPenyewaController::class, 'sendReminder'])->name('penyewa.reminder');
        Route::post('/penyewa/{user}/checkout', [AdminPenyewaController::class, 'checkout'])->name('penyewa.checkout');

        // Detail Penyewa
        Route::get('/detail-penyewa', function () {
            return view('admin.detail-penyewa');
        })->name('detail-penyewa');

        // Data Akun Penyewa
        Route::get('/akun-penyewa', [AdminAccountController::class, 'index'])->name('akun-penyewa');
        Route::delete('/akun-penyewa/{user}', [AdminAccountController::class, 'destroy'])->name('akun-penyewa.destroy');

        // Formulir Pendataan
        Route::get('/formulir-pendataan', function () {
            return view('admin.formulir-pendataan');
        })->name('formulir-pendataan');

        // Transaksi
        Route::get('/transaksi', [AdminTransactionController::class, 'index'])->name('transaksi');
        Route::post('/transaksi/{transaction}/verify', [AdminTransactionController::class, 'verifyPayment'])->name('transaksi.verify');
        Route::post('/transaksi/store-manual', [AdminTransactionController::class, 'storeManual'])->name('transaksi.store-manual');
        Route::patch('/payment-proofs/{proof}/status', [AdminTransactionController::class, 'updateProofStatus'])->name('payment-proof.status');

        // Formulir Pembayaran
        Route::get('/formulir-pembayaran', function () {
            return view('admin.formulir-pembayaran');
        })->name('formulir-pembayaran');

        // Laporan
        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/create', [AdminLaporanController::class, 'create'])->name('laporan.create');
        Route::post('/laporan', [AdminLaporanController::class, 'generate'])->name('laporan.generate');
        Route::post('/laporan/{report}/submit', [AdminLaporanController::class, 'submit'])->name('laporan.submit');
        Route::get('/laporan/{report}/preview', [AdminLaporanController::class, 'preview'])->name('laporan.preview');
        Route::get('/laporan/{report}/download-pdf', [AdminLaporanController::class, 'downloadPdf'])->name('laporan.download-pdf');
        Route::get('/laporan/{report}/download-excel', [AdminLaporanController::class, 'downloadExcel'])->name('laporan.download-excel');
        Route::delete('/laporan/{report}', [AdminLaporanController::class, 'destroy'])->name('laporan.destroy');

        // Kelola Konten Website
        Route::get('/konten', [AdminContentController::class, 'index'])->name('konten.index');

        // Hero Section
        Route::get('/konten/hero/edit', [AdminContentController::class, 'editHero'])->name('konten.edit-hero');
        Route::post('/konten/hero/update', [AdminContentController::class, 'updateHero'])->name('konten.update-hero');

        // Gallery Section (judul section bawaan, tidak diatur admin)
        Route::get('/konten/gallery/edit', [AdminContentController::class, 'editGallery'])->name('konten.edit-gallery');
        Route::post('/konten/gallery/store', [AdminContentController::class, 'storeGallery'])->name('konten.store-gallery');
        Route::patch('/konten/gallery/{gallery}', [AdminContentController::class, 'updateGallery'])->name('konten.update-gallery');
        Route::delete('/konten/gallery/{gallery}', [AdminContentController::class, 'deleteGallery'])->name('konten.delete-gallery');

        // Facilities Section (judul section bawaan, tidak diatur admin)
        Route::get('/konten/facilities/edit', [AdminContentController::class, 'editFacilities'])->name('konten.edit-facilities');
        Route::post('/konten/facilities/store', [AdminContentController::class, 'storeFacility'])->name('konten.store-facility');
        Route::patch('/konten/facilities/{facility}', [AdminContentController::class, 'updateFacility'])->name('konten.update-facility');
        Route::delete('/konten/facilities/{facility}', [AdminContentController::class, 'deleteFacility'])->name('konten.delete-facility');

        // Contact Section (form tunggal dengan field khusus)
        Route::get('/konten/contact/edit', [AdminContentController::class, 'editContact'])->name('konten.edit-contact');
        Route::post('/konten/contact/update', [AdminContentController::class, 'updateContactAll'])->name('konten.update-contact-all');

        // Notifikasi
        Route::get('/notifikasi', [AdminNotificationController::class, 'index'])->name('notifikasi');
        Route::get('/notifikasi/category/{category}', [AdminNotificationController::class, 'byCategory'])->name('notifikasi.category');
        Route::post('/notifikasi/{notification}/read', [AdminNotificationController::class, 'markAsRead'])->name('notifikasi.read');
        Route::post('/notifikasi/{notification}/archive', [AdminNotificationController::class, 'archive'])->name('notifikasi.archive');
    });
});

require __DIR__ . '/auth.php';