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

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Tangani callback dari Google: cocokkan email, login, atau buat akun owner baru.
     * Admin tidak boleh masuk lewat Google — wajib email & password.
     */
    public function callback()
    {
        abort_unless(config('services.google.client_id'), 404);

        try {
            // Coba dengan state session dulu; bila gagal (host localhost vs 127.0.0.1,
            // session hilang, dll.) fallback stateless agar login tetap jalan.
            try {
                $googleUser = Socialite::driver('google')->user();
            } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
                $googleUser = Socialite::driver('google')->stateless()->user();
            }
        } catch (\Throwable $e) {
            \Log::warning('Google OAuth callback gagal: '.$e->getMessage(), [
                'exception' => $e::class,
            ]);

            return redirect()->route('welcome', ['auth' => 'login'])
                ->with('error', 'Gagal masuk dengan Google. Silakan coba lagi.');
        }

        $email = $googleUser->getEmail();
        if (! $email) {
            return redirect()->route('welcome', ['auth' => 'login'])
                ->with('error', 'Akun Google tidak memberikan email. Tidak bisa melanjutkan.');
        }

        // Gerbang marketplace AgentBuff: owner hanya boleh masuk/mendaftar bila akun
        // AgentBuff-nya LIVE (langganan/trial aktif) DAN sudah membeli KostCloud.
        // Admin masuk lewat email/password (bukan Google), jadi gate ini menutup
        // jalur owner. Inert bila gate dimatikan (dev standalone).
        $abGate = app(\App\Services\AgentBuffGate::class);
        $ent = $abGate->checkEntitlementStrict($email);
        if (! ($ent['entitled'] ?? false)) {
            return redirect()->route('welcome', ['auth' => 'login'])
                ->with('error', $abGate->message($ent['reason'] ?? 'access_lapsed'));
        }

        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $email)->first();

        if ($user) {
            if ($user->role === 'admin') {
                return redirect()->route('welcome', ['auth' => 'admin'])
                    ->with('error', 'Akun admin harus masuk dengan email & password.');
            }

            if ($user->role !== 'owner') {
                return redirect()->route('welcome', ['auth' => 'login'])
                    ->with('error', 'Akun ini tidak memiliki akses ke panel manajemen.');
            }

            if ($user->status === 'inactive') {
                return redirect()->route('welcome', ['auth' => 'login'])
                    ->with('error', 'Akun Anda dinonaktifkan. Hubungi dukungan.');
            }

            if (! $user->google_id) {
                $user->google_id = $googleUser->getId();
            }
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            if ($googleUser->getName() && $user->name !== $googleUser->getName()) {
                // Jangan overwrite nama yang sudah diedit user; hanya isi bila kosong.
            }
            $user->save();
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'role' => 'owner',
                'status' => 'active',
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
            ]);

            \App\Models\PemilikKos::create([
                'owner_id' => $user->id,
                'boarding_house_name' => 'Kos '.$user->name,
            ]);
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        // Jangan pakai intended() — sering tersimpan /login dari middleware guest.
        return redirect()->route('owner.dashboard');
    }
}
