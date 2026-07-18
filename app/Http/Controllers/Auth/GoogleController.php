<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Arahkan pengguna ke halaman izin Google.
     */
    public function redirect()
    {
        abort_unless(config('services.google.client_id'), 404);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback dari Google: cocokkan email, login, atau buat akun baru.
     */
    public function callback()
    {
        abort_unless(config('services.google.client_id'), 404);

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            \Log::warning('Google OAuth callback gagal: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal masuk dengan Google. Silakan coba lagi.');
        }

        $email = $googleUser->getEmail();
        if (! $email) {
            return redirect()->route('login')->with('error', 'Akun Google tidak memberikan email. Tidak bisa melanjutkan.');
        }

        // Cari berdasarkan google_id, lalu email (email Google sudah terverifikasi → aman dipakai mencocokkan).
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $email)->first();

        if ($user) {
            // Tautkan google_id bila akun lama belum punya. Peran TIDAK diubah.
            if (! $user->google_id) {
                $user->google_id = $googleUser->getId();
            }
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->save();
        } else {
            // Akun baru via Google → OWNER (pemilik kos), konsisten dengan pendaftaran
            // publik (RegisteredUserController). Penyewa bukan akun; admin dibuat oleh
            // owner. Role 'tenant' sudah tidak dipakai untuk login.
            $user = User::create([
                'name' => $googleUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'role' => 'owner',
                'status' => 'active',
                // Password acak — akun Google tidak login pakai password,
                // tapi kolomnya tetap terisi agar tak perlu ubah skema.
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
            ]);

            // Profil/pengaturan kos awal (sama seperti pendaftaran manual).
            \App\Models\PemilikKos::create([
                'owner_id' => $user->id,
                'boarding_house_name' => 'Kos ' . $user->name,
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
