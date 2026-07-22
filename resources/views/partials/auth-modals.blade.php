{{-- Auth di landing: Google untuk owner (daftar+masuk); email/password khusus admin. --}}
@guest
@php
    $googleReady = (bool) config('services.google.client_id');
@endphp
<div
    x-show="authModal"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    @keydown.escape.window="authModal = null; adminLogin = false"
>
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="authModal = null; adminLogin = false"></div>

    <div
        x-show="authModal"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden"
        @click.stop
    >
        <button type="button" @click="authModal = null; adminLogin = false"
            class="absolute top-4 right-4 p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
            aria-label="Tutup">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- ===== OWNER: Google only ===== --}}
        <div x-show="authModal === 'register' || (authModal === 'login' && !adminLogin)" class="p-6 sm:p-8">
            <h2 class="text-2xl font-extrabold text-gray-900" x-text="authModal === 'register' ? 'Daftar Pemilik Kos' : 'Masuk'"></h2>
            <p class="mt-1 text-sm text-gray-500"
               x-text="authModal === 'register'
                 ? 'Daftar atau masuk dengan akun Google. Workspace kos dibuat otomatis.'
                 : 'Pemilik kos masuk dengan Google. Admin pakai email & password.'">
            </p>

            @if(session('error'))
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
            @endif
            @if(session('status'))
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif

            <div class="mt-6 space-y-4">
                @if($googleReady)
                    <a href="{{ route('auth.google.redirect') }}"
                       class="w-full flex items-center justify-center gap-3 py-3.5 px-4 rounded-xl bg-emerald-600 text-white text-sm font-bold shadow-md hover:bg-emerald-700 transition">
                        <svg class="w-5 h-5 bg-white rounded-full p-0.5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.76h3.56c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.56-2.76c-.98.66-2.23 1.06-3.72 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23z"/><path fill="#FBBC05" d="M5.84 14.09a6.6 6.6 0 0 1 0-4.18V7.07H2.18a11 11 0 0 0 0 9.86l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        <span x-text="authModal === 'register' ? 'Daftar dengan Google' : 'Masuk dengan Google'"></span>
                    </a>
                @else
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Login Google belum dikonfigurasi. Setel <code class="text-xs">GOOGLE_CLIENT_ID</code> di environment.
                    </div>
                @endif

                <p class="text-center text-sm text-gray-500" x-show="authModal === 'register'">
                    Sudah punya akun?
                    <button type="button" @click="authModal = 'login'; adminLogin = false" class="font-bold text-emerald-600 hover:underline">Masuk</button>
                </p>
                <p class="text-center text-sm text-gray-500" x-show="authModal === 'login'">
                    Belum punya akun?
                    <button type="button" @click="authModal = 'register'; adminLogin = false" class="font-bold text-emerald-600 hover:underline">Daftar dengan Google</button>
                </p>

                <div x-show="authModal === 'login'" class="pt-2 border-t border-gray-100">
                    <button type="button" @click="adminLogin = true"
                        class="w-full text-center text-sm font-semibold text-gray-600 hover:text-emerald-700 transition py-2">
                        Masuk sebagai admin →
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== ADMIN: email + password only ===== --}}
        <div x-show="authModal === 'login' && adminLogin" class="p-6 sm:p-8" x-cloak>
            <button type="button" @click="adminLogin = false"
                class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-gray-500 hover:text-emerald-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </button>

            <h2 class="text-2xl font-extrabold text-gray-900">Login Admin</h2>
            <p class="mt-1 text-sm text-gray-500">Akun admin dibuat oleh pemilik kos. Pakai email &amp; password yang diberikan.</p>

            <x-auth-session-status class="mt-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="_auth_form" value="admin">

                <div>
                    <label for="admin-email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="admin-email" type="email" name="email" value="{{ old('_auth_form') === 'admin' ? old('email') : '' }}" required autofocus
                        class="mt-1 block w-full px-4 py-3 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                        placeholder="admin@email.com">
                    @if(old('_auth_form') === 'admin' || request('auth') === 'admin')
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    @endif
                </div>

                <div>
                    <div class="flex justify-between items-center">
                        <label for="admin-password" class="block text-sm font-medium text-gray-700">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-bold text-emerald-600 hover:underline">Lupa password?</a>
                        @endif
                    </div>
                    <input id="admin-password" type="password" name="password" required autocomplete="current-password"
                        class="mt-1 block w-full px-4 py-3 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                        placeholder="••••••••">
                    @if(old('_auth_form') === 'admin' || request('auth') === 'admin')
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    @endif
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    Ingat saya
                </label>

                <button type="submit"
                    class="w-full py-3 rounded-xl bg-emerald-600 text-white text-sm font-bold shadow-md hover:bg-emerald-700 transition">
                    Masuk sebagai Admin
                </button>
            </form>
        </div>
    </div>
</div>
@endguest
