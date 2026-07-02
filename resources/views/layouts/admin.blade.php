<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - Kos Mutiara 27</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* ================= TRANSISI SIDEBAR (Style Owner) ================= */
        #main-sidebar { 
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }

        /* Teks Menu */
        .sidebar-text { 
            transition: opacity 0.2s; white-space: nowrap; 
        }
        #main-sidebar.collapsed .sidebar-text { 
            opacity: 0; pointer-events: none; display: none; 
        }
        #main-sidebar.collapsed .menu-heading { display: none; }
        
        /* Arrow Dropdown saat Collapsed */
        #main-sidebar.collapsed .dropdown-arrow { display: none; }

        /* LOGO */
        #logo-container { transition: all 0.2s ease-in-out; width: auto; opacity: 1; }
        #main-sidebar.collapsed #logo-container { display: none; width: 0; opacity: 0; }
        #main-sidebar.collapsed #sidebar-header { justify-content: center; padding: 0; }

        /* NAVIGASI */
        #main-sidebar.collapsed nav { padding-left: 12px; padding-right: 12px; }
        #main-sidebar.collapsed nav .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
        #main-sidebar.collapsed nav .nav-item svg { margin-right: 0; }
        
        /* PROFIL */
        #main-sidebar.collapsed #profile-text-container { display: none; }
        #main-sidebar.collapsed #profile-container { justify-content: center; }

        /* ================= DROPDOWN ANIMATION ================= */
        .submenu { 
            max-height: 0; 
            overflow: hidden; 
            transition: max-height 0.3s ease-out; 
        }
        .submenu.open { 
            max-height: 500px; 
            transition: max-height 0.5s ease-in; 
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        
        <aside id="main-sidebar" class="w-64 bg-emerald-900 text-white flex flex-col shadow-2xl z-20 flex-shrink-0 relative">
            
            <div id="sidebar-header" class="h-20 flex items-center justify-between px-6 border-b border-emerald-800 bg-emerald-950 transition-all duration-300">
                
                <a href="{{ url('/') }}" class="flex items-center gap-3 overflow-hidden whitespace-nowrap hover:opacity-80 transition" id="logo-container">
                    <div class="bg-emerald-500 p-1.5 rounded-lg text-white flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold tracking-wide text-white">
                        Admin<span class="text-emerald-400">Panel</span>
                    </h1>
                </a>

                <button id="sidebar-toggle" class="text-emerald-400 hover:text-white p-2 rounded-lg hover:bg-emerald-800 transition flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden custom-scrollbar-dark">
                
                <p class="menu-heading px-2 text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-2">Utama</p>
                
                <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span class="sidebar-text font-medium">Dashboard</span>
                </a>

                <p class="menu-heading px-2 text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-2 mt-4">Operasional</p>

                <div>
                    <button onclick="toggleMenu('menu-penyewa')" class="nav-item w-full flex items-center justify-between px-4 py-3 text-emerald-100 hover:bg-emerald-800/50 hover:text-white rounded-xl transition-all duration-200 group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 flex-shrink-0 mr-3 text-emerald-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="sidebar-text font-medium">Penyewa</span>
                        </div>
                        <svg id="arrow-menu-penyewa" class="w-4 h-4 transition-transform duration-200 dropdown-arrow text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="menu-penyewa" class="submenu pl-12 pr-2 space-y-1 {{ request()->routeIs('admin.penyewa.*') ? 'open' : '' }}">
                        <a href="{{ route('admin.penyewa') }}" class="block px-3 py-2 text-sm text-emerald-200 hover:text-white hover:bg-emerald-800 rounded-lg transition">Data Penyewa</a>
                        <a href="{{ route('admin.akun-penyewa') }}" class="block px-3 py-2 text-sm text-emerald-200 hover:text-white hover:bg-emerald-800 rounded-lg transition">Data Akun Penyewa</a>

                    </div>
                </div>

                <a href="{{ route('admin.kamar') }}" class="nav-item flex items-center px-4 py-3 {{ request()->routeIs('admin.kamar') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('admin.kamar') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="sidebar-text font-medium">Data Kamar</span>
                </a>

                <a href="{{ route('admin.transaksi') }}" class="nav-item flex items-center px-4 py-3 {{ request()->routeIs('admin.transaksi*') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('admin.transaksi*') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span class="sidebar-text font-medium">Data Transaksi</span>
                </a>

                <p class="menu-heading px-2 text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-2 mt-4">Manajemen</p>

                <a href="{{ route('admin.laporan') }}" class="nav-item flex items-center px-4 py-3 {{ request()->routeIs('admin.laporan') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('admin.laporan') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="sidebar-text font-medium">Laporan</span>
                </a>

                <a href="{{ route('admin.konten.index') }}" class="nav-item flex items-center px-4 py-3 {{ request()->routeIs('admin.konten*') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3 {{ request()->routeIs('admin.konten*') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span class="sidebar-text font-medium">Kelola Konten</span>
                </a>

                <p class="menu-heading px-2 text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-2 mt-4">Sistem</p>

                <a href="{{ route('admin.notifikasi') }}" class="nav-item flex items-center px-4 py-3 {{ request()->routeIs('admin.notifikasi') ? 'bg-emerald-800 text-white shadow-lg translate-x-1' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }} rounded-xl transition-all duration-200 group">
                    <div class="relative flex-shrink-0 mr-3">
                        <svg class="w-5 h-5 {{ request()->routeIs('admin.notifikasi') ? 'text-emerald-400' : 'text-emerald-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-1 ring-emerald-900 bg-red-500"></span>
                    </div>
                    <span class="sidebar-text font-medium">Notifikasi</span>
                </a>

            </nav>

            <div class="p-4 bg-emerald-950 border-t border-emerald-900 flex-shrink-0">
                <div class="flex items-center gap-3 transition-all duration-300" id="profile-container">
                    <div class="h-9 w-9 rounded-full bg-emerald-700 flex items-center justify-center text-white font-bold border-2 border-emerald-500 flex-shrink-0">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="sidebar-text overflow-hidden" id="profile-text-container">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'Admin Staff' }}</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-[11px] text-emerald-400 hover:text-white transition font-bold uppercase tracking-wider">Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 flex flex-col">
            <div class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-10 flex-shrink-0">
                <div class="py-4 px-4 sm:px-6 lg:px-8">
                    @yield('header') 
                </div>
            </div>

            <div class="flex-1 p-4 sm:p-8">
                 @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Toast helper
            window.Toast = Swal.mixin({
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
                reverseButtons: true, // Tombol confirm di kanan (umumnya lebih natural)
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // LOGIC SIDEBAR TOGGLE
            const sidebar = document.getElementById('main-sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');
            
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('w-20', 'collapsed');
                sidebar.classList.remove('w-64');
            }

            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('w-64');
                sidebar.classList.toggle('w-20');
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
            });
        });

        // LOGIC DROPDOWN MENU
        function toggleMenu(menuId) {
            const sidebar = document.getElementById('main-sidebar');
            // Auto expand sidebar jika sedang collapsed
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
            }

            const menu = document.getElementById(menuId);
            const arrow = document.getElementById('arrow-' + menuId);
            
            menu.classList.toggle('open');
            if (menu.classList.contains('open')) {
                arrow.style.transform = 'rotate(180deg)';
            } else {
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    </script>
    <style>
        .custom-scrollbar-dark::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar-dark::-webkit-scrollbar-track { background: #064e3b; } 
        .custom-scrollbar-dark::-webkit-scrollbar-thumb { background: #065f46; border-radius: 10px; } 
        .custom-scrollbar-dark::-webkit-scrollbar-thumb:hover { background: #10b981; } 
    </style>
    @stack('scripts')
</body>
</html>