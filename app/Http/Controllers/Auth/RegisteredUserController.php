<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'boarding_house_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Pendaftaran publik kini membuat akun OWNER (pemilik kos) — titik masuk
        // subscription SaaS. Penyewa bukan lagi akun, melainkan data yang dikelola owner.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'owner',
            'status' => 'active',
            // SaaS internal: mailer log-only, jadi owner langsung terverifikasi.
            'email_verified_at' => now(),
        ]);

        // Buat profil/pengaturan kos awal untuk owner (kolom lain sudah ber-default).
        \App\Models\PemilikKos::create([
            'owner_id' => $user->id,
            'boarding_house_name' => $request->boarding_house_name ?: ('Kos ' . $user->name),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('owner.dashboard', absolute: false));
    }
}
