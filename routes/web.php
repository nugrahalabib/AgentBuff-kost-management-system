<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\PublicBiodataController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\McpTokenController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KamarController as AdminKamarController;
use App\Http\Controllers\Admin\PenyewaController as AdminPenyewaController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
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

// Form biodata publik untuk penyewa (TANPA login; token pada URL = otorisasi).
Route::get('/biodata/{token}', [PublicBiodataController::class, 'edit'])->name('public.biodata.edit')->middleware('throttle:60,1');
Route::post('/biodata/{token}', [PublicBiodataController::class, 'update'])->name('public.biodata.update')->middleware('throttle:20,1');

// Middleware untuk user yang sudah login
Route::middleware(['auth', 'verified'])->group(function () {

    // Authenticated streaming of payment proof files (private storage).
    // Authorization (own tenant, any owner/admin) is enforced in the controller.
    Route::get('/payment-proof/{proof}/view', [\App\Http\Controllers\DocumentController::class, 'viewPaymentProof'])
        ->name('payment-proof.view');

    // Authenticated streaming of tenant identity documents (KTP, KK, etc.).
    Route::get('/tenant-document/{user}/{type}', [\App\Http\Controllers\DocumentController::class, 'viewDocument'])
        ->name('tenant-document.view');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Hanya owner & admin yang punya panel manajemen internal.
        // Penyewa kini berupa DATA, bukan akun login.
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'owner') {
            return redirect()->route('owner.dashboard');
        }

        // Peran lain (mis. sisa akun tenant lama) tidak punya akses — logout aman.
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('welcome', ['auth' => 'login'])
            ->with('status', 'Akun ini tidak memiliki akses ke panel manajemen.');
    })->middleware(['auth', 'verified'])->name('dashboard');

    // Halaman profil (dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route owner
    Route::prefix('owner')
        ->name('owner.')
        ->middleware(['auth', 'verified', 'role:owner'])
        ->group(function () {

            // 1. Dashboard Owner
            Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

            // 2. Data Kamar (CRUD penuh — setara admin)
            Route::get('/kamar', [OwnerKamarController::class, 'index'])->name('kamar');
            Route::get('/kamar/create', [OwnerKamarController::class, 'create'])->name('kamar.create');
            Route::post('/kamar', [OwnerKamarController::class, 'store'])->name('kamar.store');
            Route::patch('/kamar/{room}/status', [OwnerKamarController::class, 'updateStatus'])->name('kamar.updateStatus');
            Route::delete('/kamar/{room}', [OwnerKamarController::class, 'destroy'])->name('kamar.destroy');

            // 3. Data Penyewa (+ tambah penyewa & tempatkan ke kamar)
            Route::get('/penyewa', [OwnerPenyewaController::class, 'index'])->name('penyewa');
            Route::get('/penyewa/tambah', [OwnerPenyewaController::class, 'create'])->name('penyewa.create');
            Route::post('/penyewa/tambah', [OwnerPenyewaController::class, 'store'])->name('penyewa.store');
            Route::get('/penyewa/{user}', [OwnerPenyewaController::class, 'show'])->name('penyewa.show');
            Route::post('/penyewa/{user}/checkout', [OwnerPenyewaController::class, 'checkout'])->name('penyewa.checkout');
            Route::delete('/penyewa/{user}', [OwnerPenyewaController::class, 'destroy'])->name('penyewa.destroy');
            Route::post('/penyewa/{user}/biodata-link', [OwnerPenyewaController::class, 'generateBiodataLink'])->name('penyewa.biodata-link');
            Route::get('/penyewa/{user}/biodata/edit', [OwnerPenyewaController::class, 'editBiodata'])->name('penyewa.biodata.edit');
            Route::put('/penyewa/{user}/biodata', [OwnerPenyewaController::class, 'updateBiodata'])->name('penyewa.biodata.update');

            // 4. Laporan (cashflow + generate laporan sendiri)
            Route::get('/laporan', [OwnerLaporanController::class, 'index'])->name('laporan');
            Route::get('/laporan/create', [OwnerLaporanController::class, 'create'])->name('laporan.create');
            Route::post('/laporan/generate', [OwnerLaporanController::class, 'generate'])->name('laporan.generate');
            Route::get('/laporan/export-pdf', [OwnerLaporanController::class, 'exportCashflowPdf'])->name('laporan.export-pdf');
            Route::get('/laporan/export-excel', [OwnerLaporanController::class, 'exportCashflowExcel'])->name('laporan.export-excel');
            Route::post('/laporan/expense', [OwnerLaporanController::class, 'storeExpense'])->name('laporan.expense.store');
            Route::delete('/laporan/expense/{expense}', [OwnerLaporanController::class, 'destroyExpense'])->name('laporan.expense.destroy');
            Route::get('/laporan/{report}/preview', [OwnerLaporanController::class, 'preview'])->name('laporan.preview');
            Route::get('/laporan/{report}/download-pdf', [OwnerLaporanController::class, 'downloadPdf'])->name('laporan.download-pdf');
            Route::get('/laporan/{report}/download-excel', [OwnerLaporanController::class, 'downloadExcel'])->name('laporan.download-excel');

            // 4.5 Verifikasi & Input Transaksi
            Route::get('/verifikasi-transaksi', [TransactionController::class, 'index'])->name('verifikasi-transaksi');
            Route::get('/transaksi/tambah', [TransactionController::class, 'createManual'])->name('transaksi.create');
            Route::post('/transaksi/tambah', [TransactionController::class, 'storeManual'])->name('transaksi.store-manual');
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
            // Simpan semua pengaturan lewat updateAll (master save) + updateBankSettings.
            Route::post('/settings/bank', [SettingsController::class, 'updateBankSettings'])->name('settings.bank');
            Route::post('/settings/update-all', [SettingsController::class, 'updateAll'])->name('settings.update-all');

            // 6.5 Manajemen Tipe Kamar
            Route::post('/room-types', [TipeKamarController::class, 'store'])->name('room-types.store');
            Route::patch('/room-types/{roomType}', [TipeKamarController::class, 'update'])->name('room-types.update');
            Route::delete('/room-types/{roomType}', [TipeKamarController::class, 'destroy'])->name('room-types.destroy');

            // 7. Notifikasi
            Route::get('/notifikasi', [OwnerNotificationController::class, 'index'])->name('notifikasi');
            Route::post('/notifikasi/{notification}/archive', [OwnerNotificationController::class, 'archive'])->name('notifikasi.archive');

            // 8. MCP / AI Agent (bearer token)
            Route::get('/mcp', [McpTokenController::class, 'index'])->name('mcp');
            Route::post('/mcp/token', [McpTokenController::class, 'generate'])->name('mcp.generate');
            Route::delete('/mcp/token/{id}', [McpTokenController::class, 'revoke'])->name('mcp.revoke');

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

        // Data Penyewa (+ tambah penyewa & tempatkan ke kamar)
        Route::get('/penyewa', [AdminPenyewaController::class, 'index'])->name('penyewa');
        Route::get('/penyewa/tambah', [AdminPenyewaController::class, 'create'])->name('penyewa.create');
        Route::post('/penyewa/tambah', [AdminPenyewaController::class, 'store'])->name('penyewa.store');
        Route::get('/penyewa/{user}', [AdminPenyewaController::class, 'show'])->name('penyewa.show');
        Route::post('/penyewa/{user}/verify', [AdminPenyewaController::class, 'verify'])->name('penyewa.verify');
        Route::post('/penyewa/{user}/reminder', [AdminPenyewaController::class, 'sendReminder'])->name('penyewa.reminder');
        Route::post('/penyewa/{user}/checkout', [AdminPenyewaController::class, 'checkout'])->name('penyewa.checkout');
        Route::delete('/penyewa/{user}', [AdminPenyewaController::class, 'destroy'])->name('penyewa.destroy');
        Route::post('/penyewa/{user}/biodata-link', [AdminPenyewaController::class, 'generateBiodataLink'])->name('penyewa.biodata-link');
        Route::get('/penyewa/{user}/biodata/edit', [AdminPenyewaController::class, 'editBiodata'])->name('penyewa.biodata.edit');
        Route::put('/penyewa/{user}/biodata', [AdminPenyewaController::class, 'updateBiodata'])->name('penyewa.biodata.update');

        // Detail Penyewa
        Route::get('/detail-penyewa', function () {
            return view('admin.detail-penyewa');
        })->name('detail-penyewa');

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

        // Notifikasi
        Route::get('/notifikasi', [AdminNotificationController::class, 'index'])->name('notifikasi');
        Route::get('/notifikasi/category/{category}', [AdminNotificationController::class, 'byCategory'])->name('notifikasi.category');
        Route::post('/notifikasi/{notification}/read', [AdminNotificationController::class, 'markAsRead'])->name('notifikasi.read');
        Route::post('/notifikasi/{notification}/archive', [AdminNotificationController::class, 'archive'])->name('notifikasi.archive');
    });
});

require __DIR__ . '/auth.php';