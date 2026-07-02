<x-guest-layout>
    <div class="flex min-h-screen bg-white">

        <div class="hidden lg:flex lg:w-1/2 relative bg-emerald-900 items-center justify-center overflow-hidden">
            <img src="https://images.unsplash.com/photo-1598928506311-c55ded91a20c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80"
                alt="Register Image" class="absolute inset-0 w-full h-full object-cover opacity-60">
            <div class="relative z-10 text-white text-center px-10">
                <div
                    class="bg-emerald-600/20 backdrop-blur-md p-4 rounded-2xl border border-white/20 inline-block mb-6">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-4xl font-bold mb-4">Gabung Bersama Kami</h2>
                <p class="text-emerald-100 text-lg">Buat akun untuk melakukan pemesanan kamar dan pengelolaan profil</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16">
            <div class="w-full max-w-md space-y-8">
                <div class="text-center lg:text-left">
                    <h1 class="text-3xl font-bold text-gray-900">Buat Akun Baru</h1>
                    <p class="text-gray-500 mt-2">Sudah punya akun? <a href="{{ route('login') }}"
                            class="text-emerald-600 font-bold hover:underline">Masuk di sini</a></p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input id="name"
                            class="block w-full px-4 py-3 mt-1 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                            type="text" name="name" :value="old('name')" required autofocus placeholder="Nama Anda" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input id="email"
                            class="block w-full px-4 py-3 mt-1 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                            type="email" name="email" :value="old('email')" required placeholder="nama@email.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password"
                            class="block w-full px-4 py-3 mt-1 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                            type="password" name="password" required autocomplete="new-password"
                            placeholder="Minimal 8 karakter" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi
                            Password</label>
                        <input id="password_confirmation"
                            class="block w-full px-4 py-3 mt-1 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"
                            type="password" name="password_confirmation" required placeholder="Ulangi password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition transform hover:scale-[1.02]">
                        Daftar Sekarang
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
                        Daftar dengan Google
                    </a>
                @endif

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-400">Dengan mendaftar, Anda menyetujui Syarat & Ketentuan kami.</p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>