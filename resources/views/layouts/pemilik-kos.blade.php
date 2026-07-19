<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Anti-FOUC: pasang tema tersimpan sebelum halaman tampil --}}
    <script>(function(){try{var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}}catch(e){}})();</script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>Owner Dashboard - AgentBuff KostCloud</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Palet warna owner: SETELAH app.css agar override :root menang --}}
    @include('partials.brand-theme')

    <style>
        /* ================= TRANSISI SIDEBAR ================= */
        #main-sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        [x-cloak] { display: none !important; }

        /* 1. Teks Menu Menghilang */
        .sidebar-text {
            transition: opacity 0.2s;
            white-space: nowrap;
        }

        #main-sidebar.collapsed .sidebar-text {
            opacity: 0;
            pointer-events: none;
            display: none;
        }

        #main-sidebar.collapsed .menu-heading {
            display: none;
        }

        /* 2. LOGIK LOGO & TOGGLE */
        /* Transisi Container Logo */
        #logo-container {
            transition: all 0.2s ease-in-out;
            width: auto;
            opacity: 1;
        }

        /* SAAT COLLAPSED: Logo Hilang Total */
        #main-sidebar.collapsed #logo-container {
            display: none;
            width: 0;
            opacity: 0;
        }

        /* SAAT COLLAPSED: Header jadi Center (Biar tombol garis 3 di tengah) */
        #main-sidebar.collapsed #sidebar-header {
            justify-content: center;
            padding: 0;
        }

        /* Penyesuaian Padding Navigasi */
        #main-sidebar.collapsed nav {
            padding-left: 12px;
            padding-right: 12px;
        }

        #main-sidebar.collapsed nav a {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        #main-sidebar.collapsed nav a svg,
        #main-sidebar.collapsed nav a .relative {
            margin-right: 0;
        }

        /* Sembunyikan profil text saat collapsed */
        #main-sidebar.collapsed #profile-text-container {
            display: none;
        }

        #main-sidebar.collapsed #profile-container {
            justify-content: center;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="flex h-screen overflow-hidden" x-data="{ mobileOpen: false }">

        {{-- Backdrop off-canvas (mobile) --}}
        <div x-show="mobileOpen" x-cloak x-transition.opacity @click="mobileOpen = false"
            class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

        <aside id="main-sidebar"
            class="w-64 bg-emerald-900 text-white flex flex-col shadow-2xl flex-shrink-0 fixed inset-y-0 left-0 z-40 transform transition-transform duration-300 lg:relative lg:inset-auto lg:translate-x-0"
            :class="mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            <div id="sidebar-header"
                class="h-20 flex items-center justify-between px-6 border-b border-emerald-800 bg-emerald-950 transition-all duration-300">

                <a href="{{ url('/') }}"
                    class="flex items-center gap-3 overflow-hidden whitespace-nowrap hover:opacity-80 transition"
                    id="logo-container">
                    <div class="bg-emerald-500 p-1.5 rounded-lg text-white flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold tracking-wide text-white">
                        Kost<span class="text-emerald-400">Cloud</span>
                    </h1>
                </a>

                <button id="sidebar-toggle"
                    class="hidden lg:flex text-emerald-400 hover:text-white p-2 rounded-lg hover:bg-emerald-800 transition flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden custom-scrollbar-dark">

                <p class="menu-heading px-2 text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-2 mt-4">
                    Utama</p>
                <a href="{{ route('owner.dashboard') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('owner.dashboard') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group"
                    title="Dashboard">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('owner.dashboard') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <span class="sidebar-text font-medium">Dashboard</span>
                </a>

                <p class="menu-heading px-2 text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-2 mt-4">
                    Operasional</p>

                <a href="{{ route('owner.kamar') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('owner.kamar') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group"
                    title="Kamar">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('owner.kamar') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <span class="sidebar-text font-medium">Data Kamar</span>
                </a>

                <a href="{{ route('owner.penyewa') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('owner.penyewa') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group"
                    title="Penyewa">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('owner.penyewa') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span class="sidebar-text font-medium">Data Penyewa</span>
                </a>

                <a href="{{ route('owner.verifikasi-transaksi') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('owner.verifikasi-transaksi') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group"
                    title="Data Transaksi">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('owner.verifikasi-transaksi') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="sidebar-text font-medium">Data Transaksi</span>
                </a>

                <a href="{{ route('owner.laporan') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('owner.laporan') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group"
                    title="Keuangan">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('owner.laporan') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="sidebar-text font-medium">Laporan</span>
                </a>

                <p class="menu-heading px-2 text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-2 mt-4">
                    Sistem</p>

                <a href="{{ route('owner.notifikasi') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('owner.notifikasi') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group"
                    title="Notifikasi">
                    <div class="relative flex-shrink-0 mr-3">
                        <svg class="w-5 h-5 {{ request()->routeIs('owner.notifikasi') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        @if(auth()->user()->notifications()->where('status', 'unread')->exists())
                            <span
                                class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-1 ring-emerald-900 bg-red-500"></span>
                        @endif
                    </div>
                    <span class="sidebar-text font-medium">Notifikasi</span>
                </a>

                <a href="{{ route('owner.admin') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('owner.admin') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group"
                    title="Admin">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('owner.admin') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span class="sidebar-text font-medium">Tim & Akses</span>
                </a>

                <a href="{{ route('owner.settings') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('owner.settings') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group"
                    title="Pengaturan">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('owner.settings') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="sidebar-text font-medium">Pengaturan</span>
                </a>

                <a href="{{ route('owner.mcp') }}"
                    class="flex items-center px-4 py-3 {{ request()->routeIs('owner.mcp') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group"
                    title="MCP / AI Agent">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('owner.mcp') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="sidebar-text font-medium">MCP / AI Agent</span>
                </a>
            </nav>

            <div class="p-4 bg-emerald-950 border-t border-emerald-900 flex-shrink-0">
                <div class="flex items-center gap-3 transition-all duration-300" id="profile-container">
                    <div
                        class="h-9 w-9 rounded-full bg-emerald-700 flex items-center justify-center text-white font-bold border-2 border-emerald-500 flex-shrink-0">
                        {{ substr(Auth::user()->name ?? 'O', 0, 1) }}
                    </div>
                    <div class="sidebar-text overflow-hidden" id="profile-text-container">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'Owner' }}</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="text-[11px] text-emerald-400 hover:text-white transition font-bold uppercase tracking-wider">Log
                                Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 flex flex-col">
            <div class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-10 flex-shrink-0">
                <div class="py-4 px-4 sm:px-6 lg:px-8 flex items-center gap-3">
                    <button @click="mobileOpen = true" class="lg:hidden p-2 -ml-1 text-gray-600 hover:text-emerald-700 flex-shrink-0" aria-label="Buka menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div class="flex-1 min-w-0">
                        @yield('header')
                    </div>
                    <button type="button" onclick="toggleTheme()" title="Ganti tema terang/gelap" aria-label="Ganti tema"
                        class="flex-shrink-0 p-2 rounded-lg text-gray-500 hover:text-emerald-700 hover:bg-gray-100 transition">
                        <svg class="theme-toggle-icon-sun w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <svg class="theme-toggle-icon-moon w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 p-4 sm:p-8">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Tombol Panduan (tur interaktif) + slot tur per-halaman --}}
    <button type="button"
        onclick="window.KostTour && window.__pageTour && window.KostTour.start(window.__pageTour)"
        class="fixed bottom-5 right-5 z-40 flex items-center gap-2 px-4 py-3 rounded-full bg-emerald-600 text-white shadow-lg hover:bg-emerald-700 transition"
        title="Putar panduan halaman ini">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="hidden sm:inline text-sm font-bold">Panduan</span>
    </button>
    @stack('tour')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fallback: load Alpine.js from CDN if Vite didn't load it
        if (typeof Alpine === 'undefined') {
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js';
            s.defer = true;
            document.head.appendChild(s);
        }
    </script>
    <script>
        // GLOBAL CONFIRMATION HELPER
        function confirmSubmit(event, message) {
            event.preventDefault();
            const form = event.target;

            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981', // Emerald-500
                cancelButtonColor: '#ef4444', // Red-500
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true, // Tombol confirm di kanan
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        // FLASH MESSAGE HANDLER
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('main-sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');

            // Cek LocalStorage
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('w-20', 'collapsed');
                sidebar.classList.remove('w-64');
            }

            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('w-64');
                sidebar.classList.toggle('w-20');
                sidebar.classList.toggle('collapsed');

                // Simpan status
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
            });
        });
    </script>
    <style>
        /* Scrollbar Gelap untuk Sidebar */
        .custom-scrollbar-dark::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar-dark::-webkit-scrollbar-track {
            background: #064e3b;
        }

        /* emerald-900 */
        .custom-scrollbar-dark::-webkit-scrollbar-thumb {
            background: #065f46;
            border-radius: 10px;
        }

        /* emerald-800 */
        .custom-scrollbar-dark::-webkit-scrollbar-thumb:hover {
            background: #10b981;
        }

        /* emerald-500 */
    </style>
</body>

</html>