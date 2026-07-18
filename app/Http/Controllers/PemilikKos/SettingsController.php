<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TipeKamar;
use App\Models\Admin;
use App\Models\PasswordHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index()
    {
        $owner = Auth::user();
        $businessSettings = $owner->businessSettings;
        $adminUsers = Admin::where('owner_id', $owner->id)
            ->with('user')
            ->get();
        $roomTypes = TipeKamar::where('owner_id', $owner->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pemilik-kos.pengaturan', [
            'pemilik' => $owner,
            'businessSettings' => $businessSettings,
            'adminUsers' => $adminUsers,
            'tipeKamar' => $roomTypes,
        ]);
    }

    // updateBusinessSettings(), updateProfile(), dan changePassword() dihapus:
    // seluruh fungsinya (profil, bank, reminder, ganti password + histori) sudah
    // dicakup updateAll() (master save) yang dipakai halaman Pengaturan.

    /**
     * Update bank account settings
     */
    public function updateBankSettings(Request $request)
    {
        // Rekening bank dihapus dari UI; tab "Pengaturan" kini hanya reminder + jumlah lantai.
        $validated = $request->validate([
            'invoice_reminder_days_before' => 'nullable|integer|min:1',
            'floor_count' => 'nullable|integer|min:1|max:20',
        ]);

        $owner = Auth::user();
        $settings = $owner->businessSettings;

        if (!$settings) {
            $settings = new \App\Models\PemilikKos(['owner_id' => $owner->id]);
            $settings->save();
        }

        $updateData = [];
        if (isset($validated['invoice_reminder_days_before'])) {
            $updateData['invoice_reminder_days_before'] = $validated['invoice_reminder_days_before'];
        }
        if (isset($validated['floor_count'])) {
            $updateData['floor_count'] = $validated['floor_count'];
        }

        $settings->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan!',
        ]);
    }

    /**
     * Update ALL settings (Master Save)
     */
    public function updateAll(Request $request)
    {
        $owner = Auth::user();

        // 1. Validation Rules
        $rules = [
            // Business & Bank
            'invoice_reminder_days_before' => 'required|numeric|min:1',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',

            // Profile
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email,' . $owner->id,
            'boarding_house_name' => 'required|string|max:255',
        ];

        // Conditional Password Validation
        if ($request->filled('password')) {
            $rules['current_password'] = 'required|current_password';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // 2. Update Profile & Boarding House Name
        $owner->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // 3. Update Business Settings (Bank + Boarding House + Reminder)
        $settings = $owner->businessSettings;
        if (!$settings) {
            $settings = new \App\Models\PemilikKos(['owner_id' => $owner->id]);
        }

        $settings->fill([
            'boarding_house_name' => $validated['boarding_house_name'],
            'bank_name' => $validated['bank_name'] ?? $settings->bank_name,
            'bank_account_number' => $validated['bank_account_number'] ?? $settings->bank_account_number,
            'bank_account_name' => $validated['bank_account_name'] ?? $settings->bank_account_name,
            'invoice_reminder_days_before' => $validated['invoice_reminder_days_before'],
        ]);
        $settings->save();

        // 4. Update Password (if provided)
        if ($request->filled('password')) {
            // Save history
            PasswordHistory::create([
                'user_id' => $owner->id,
                'old_password_hash' => $owner->password,
                'changed_at' => now(),
                'changed_from_ip' => $request->ip(),
                'changed_from_user_agent' => $request->userAgent(),
            ]);

            $owner->update(['password' => bcrypt($validated['password'])]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua perubahan berhasil disimpan.',
        ]);
    }
}
