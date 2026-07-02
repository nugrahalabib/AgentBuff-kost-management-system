<x-guest-layout>
    <div class="flex min-h-screen bg-white">

        <div class="hidden lg:flex lg:w-1/2 relative bg-emerald-900 items-center justify-center overflow-hidden">
            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80"
                alt="Login Image" class="absolute inset-0 w-full h-full object-cover opacity-60">
            <div class="relative z-10 text-white text-center px-10">
                <div
                    class="bg-emerald-600/20 backdrop-blur-md p-4 rounded-2xl border border-white/20 inline-block mb-6">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold mb-4">Selamat Datang Kembali</h2>
                <p class="text-emerald-100 text-lg">Masuk untuk mengecek tagihan, status sewa, dan layanan lainnya.</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16">
            <div class="w-full max-w-md space-y-8">
                <div class="text-center lg:text-left">
                    <h1 class="text-3xl font-bold text-gray-900">Masuk Akun</h1>
                    <p class="text-gray-500 mt-2">Belum punya akun? <a href="{{ route('register') }}"
                            class="text-emerald-600 font-bold hover:underline">Daftar di sini</a></p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input id="email"
                                class="block w-full px-4 py-3 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                                type="email" name="email" :value="old('email')" required autofocus
                                placeholder="nama@email.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-sm text-emerald-600 hover:text-emerald-500 font-bold"
                                    href="{{ route('password.request') }}">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input id="password"
                                class="block w-full px-4 py-3 pr-12 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                                type="password" name="password" required autocomplete="current-password"
                                placeholder="••••••••" />
                            <button type="button" id="togglePassword" aria-label="Tampilkan password"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-emerald-600 focus:outline-none">
                                {{-- Ikon mata terbuka: tampil saat password tersembunyi (klik untuk lihat) --}}
                                <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{-- Ikon mata tercoret: tampil saat password terlihat (klik untuk sembunyikan) --}}
                                <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>


                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition transform hover:scale-[1.02]">
                        Masuk Sekarang
                    </button>
                </form>

                @if(config('services.google.client_id'))
                    <div class="flex items-center gap-3 my-6">
                        <div class="h-px bg-gray-200 flex-1"></div>
                        <span class="text-xs text-gray-400 font-medium">atau</span>
                        <div class="h-px bg-gray-200 flex-1"></div>
                    </div>
                    <a href="{{ route('auth.google.redirect') }}"
                       class="w-full flex items-center justify-center gap-3 py-3 px-4 border border-gray-300 rounded-xl text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.76h3.56c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.56-2.76c-.98.66-2.23 1.06-3.72 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23z"/><path fill="#FBBC05" d="M5.84 14.09a6.6 6.6 0 0 1 0-4.18V7.07H2.18a11 11 0 0 0 0 9.86l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        Masuk dengan Google
                    </a>
                @endif

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-400">© {{ date('Y') }} Mutiara27. Secure Login.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const btn = document.getElementById('togglePassword');
            const pw = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            if (!btn || !pw) return;
            btn.addEventListener('click', function () {
                const show = pw.type === 'password';
                pw.type = show ? 'text' : 'password';
                eyeOpen.classList.toggle('hidden', show);
                eyeClosed.classList.toggle('hidden', !show);
                btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
                pw.focus();
            });
        })();
    </script>
</x-guest-layout>