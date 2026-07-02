{{-- Welcome Page Navbar - Can be included in any page --}}
<nav class="fixed w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-gray-100 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <a href="{{ route('welcome') }}" class="flex items-center gap-2">
                <div class="bg-emerald-600 p-1.5 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-800 tracking-wide">Mutiara<span class="text-emerald-600">27</span></span>
            </a>
        </div>

        <div class="hidden md:flex items-center gap-8">
            <a href="{{ route('welcome') }}#home" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                Beranda
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
            </a>
            <a href="{{ route('welcome') }}#galeri" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                Galeri
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
            </a>
            <a href="{{ route('welcome') }}#fasilitas" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                Fasilitas
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
            </a>
            <a href="{{ route('welcome') }}#kamar" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                Pilihan Kamar
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
            </a>
            <a href="{{ route('welcome') }}#kontak" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                Lokasi & Kontak
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
            </a>
        </div>

        <div class="flex items-center gap-3">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800">Dashboard Saya</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 hover:text-emerald-600 px-4 py-2">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-5 py-2.5 rounded-full shadow-lg shadow-emerald-200 transition transform hover:scale-105">
                            Daftar Sekarang
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</nav>
