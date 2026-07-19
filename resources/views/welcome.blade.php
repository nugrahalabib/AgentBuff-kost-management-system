<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Anti-FOUC: pasang tema tersimpan sebelum halaman tampil --}}
    <script>(function(){try{var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}}catch(e){}})();</script>
    <title>AgentBuff KostCloud — Manajemen Kos Cloud + Kontrol AI Agent (MCP)</title>
    <meta name="description" content="Platform manajemen kos internal berbasis cloud untuk pemilik kos: kelola kamar, penyewa, transaksi, dan laporan. Dilengkapi kontrol lewat AI agent via MCP.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏠</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 antialiased">

    {{-- ===================== NAVBAR ===================== --}}
    <header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 20"
        class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
        :class="scrolled ? 'bg-white/90 backdrop-blur shadow-sm' : 'bg-transparent'">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="#top" class="flex items-center gap-2 font-extrabold text-lg text-emerald-700">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-600 text-white shadow-md">🏠</span>
                    AgentBuff <span class="text-gray-800">KostCloud</span>
                </a>

                <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                    <a href="#fitur" class="hover:text-emerald-700 transition">Fitur</a>
                    <a href="#mcp" class="hover:text-emerald-700 transition">AI Agent (MCP)</a>
                </div>

                <div class="hidden md:flex items-center gap-3">
                    <button type="button" onclick="toggleTheme()" title="Ganti tema terang/gelap" aria-label="Ganti tema"
                        class="p-2 rounded-lg text-gray-500 hover:text-emerald-700 hover:bg-emerald-50 transition">
                        <svg class="theme-toggle-icon-sun w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <svg class="theme-toggle-icon-moon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold shadow-md hover:bg-emerald-700 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-bold text-gray-700 hover:text-emerald-700 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold shadow-md hover:bg-emerald-700 transition">Mulai Gratis</a>
                    @endauth
                </div>

                <button @click="open = !open" class="md:hidden p-2 text-gray-700" aria-label="Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>

            {{-- Mobile menu --}}
            <div x-show="open" x-cloak x-transition class="md:hidden pb-4 space-y-1">
                <a href="#fitur" @click="open=false" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-emerald-50">Fitur</a>
                <a href="#mcp" @click="open=false" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-emerald-50">AI Agent (MCP)</a>
                <button type="button" onclick="toggleTheme()" class="w-full px-3 py-2 rounded-lg text-gray-700 hover:bg-emerald-50 flex items-center gap-2">
                    <svg class="theme-toggle-icon-sun w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="theme-toggle-icon-moon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <span>Ganti Tema</span>
                </button>
                <div class="pt-2 flex gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex-1 text-center px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-bold">Masuk</a>
                        <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold">Mulai Gratis</a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    {{-- ===================== HERO ===================== --}}
    <section id="top" class="relative overflow-hidden pt-32 pb-20 sm:pt-40 sm:pb-28">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-emerald-50 via-white to-white"></div>
        <div class="absolute -top-24 -right-24 -z-10 w-96 h-96 bg-emerald-200/40 rounded-full blur-3xl"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold tracking-wide">
                    ⚡ MANAJEMEN KOS × AI AGENT (MCP)
                </span>
                <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-gray-900 leading-tight">
                    Kelola kos-mu <span class="text-emerald-600">rapi & otomatis</span>,
                    <br class="hidden sm:block"> kendalikan lewat AI agent.
                </h1>
                <p class="mt-6 text-lg text-gray-600 leading-relaxed">
                    AgentBuff KostCloud membantu pemilik kos mengelola kamar, penyewa, transaksi, dan laporan
                    dalam satu dashboard cloud — dan bisa dijalankan oleh AI agent (Claude Code, Codex, dll)
                    lewat MCP dengan satu bearer token.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('register') }}" class="px-7 py-3.5 rounded-xl bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition transform hover:scale-105">
                        Mulai Gratis →
                    </a>
                    <a href="#fitur" class="px-7 py-3.5 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold shadow-sm hover:bg-gray-50 transition">
                        Lihat Fitur
                    </a>
                </div>
                <p class="mt-4 text-xs text-gray-400">Tanpa kartu kredit • Langsung pakai • Data kos-mu terpisah aman</p>
            </div>
        </div>
    </section>

    {{-- ===================== FITUR ===================== --}}
    <section id="fitur" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Semua kebutuhan manajemen kos</h2>
                <p class="mt-4 text-gray-600">Dari kamar sampai laporan keuangan — semuanya internal, tanpa reservasi publik.</p>
            </div>

            <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $features = [
                        ['🚪', 'Manajemen Kamar', 'Tambah, ubah status, dan hapus kamar. Pantau kamar kosong, terisi, dan perbaikan secara real-time.'],
                        ['👥', 'Data Penyewa', 'Catat penyewa & tempatkan langsung ke kamar tersedia. Kelola dokumen dan riwayat hunian.'],
                        ['💳', 'Transaksi & Pembayaran', 'Input pembayaran manual atau verifikasi bukti transfer. Otomatis menempatkan penyewa ke kamar.'],
                        ['📊', 'Laporan PDF & Excel', 'Generate laporan keuangan, status kamar, dan data penyewa. Unduh sebagai PDF atau Excel.'],
                        ['🛡️', 'Owner + Admin', 'Buat akun admin opsional. Owner dan admin berbagi data yang sama — perubahan tersinkron.'],
                        ['🏢', 'Multi-Kos (Multi-tenant)', 'Setiap pemilik kos punya workspace terpisah dan aman, berbasis langganan.'],
                    ];
                @endphp
                @foreach($features as [$icon, $title, $desc])
                    <div class="p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-emerald-100 transition bg-white">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-2xl mb-4">{{ $icon }}</div>
                        <h3 class="font-bold text-lg text-gray-900">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== MCP / AI AGENT ===================== --}}
    <section id="mcp" class="py-20 bg-gray-50 relative overflow-hidden">
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">🤖 MODEL CONTEXT PROTOCOL</span>
                    <h2 class="mt-5 text-3xl sm:text-4xl font-extrabold leading-tight text-gray-900">Biarkan AI agent yang mengurus kos-mu</h2>
                    <p class="mt-5 text-gray-600 leading-relaxed">
                        Hasilkan <span class="text-emerald-600 font-semibold">bearer token</span> sekali klik, lalu berikan ke
                        AI agent favoritmu. Mereka bisa menambah kamar, mencatat penyewa, memverifikasi transaksi,
                        hingga membuat laporan — semuanya lewat MCP, aman dalam batas kos-mu sendiri.
                    </p>
                    <ul class="mt-6 space-y-3 text-sm text-gray-700">
                        @foreach(['Claude Code', 'Codex', 'Hermes Agent', 'OpenClaw'] as $agent)
                            <li class="flex items-center gap-3"><span class="text-emerald-600">✔</span> Kompatibel dengan <b>{{ $agent }}</b></li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-2xl bg-black/40 border border-white/10 shadow-2xl overflow-hidden">
                    <div class="flex items-center gap-1.5 px-4 py-3 border-b border-white/10">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                        <span class="w-3 h-3 rounded-full bg-green-400"></span>
                        <span class="ml-3 text-xs text-gray-400">mcp.json</span>
                    </div>
                    <pre class="p-5 text-xs sm:text-sm leading-relaxed overflow-x-auto text-emerald-100"><code>{
  "mcpServers": {
    "kostcloud": {
      "type": "http",
      "url": "https://kostcloud.app/mcp",
      "headers": {
        "Authorization": "Bearer kc_live_••••••••••••"
      }
    }
  }
}</code></pre>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== CTA ===================== --}}
    <section class="py-20 bg-emerald-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white">Siap mengelola kos lebih rapi?</h2>
            <p class="mt-4 text-emerald-100 text-lg">Buat akun pemilik kos sekarang — gratis untuk memulai.</p>
            <a href="{{ route('register') }}" class="mt-8 inline-block px-8 py-4 rounded-xl bg-white text-emerald-700 font-extrabold shadow-lg hover:bg-emerald-50 transition transform hover:scale-105">
                Daftar Sekarang →
            </a>
        </div>
    </section>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="bg-gray-50 text-gray-500 py-10 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 font-extrabold text-gray-800">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-600">🏠</span>
                AgentBuff KostCloud
            </div>
            <p class="text-sm">© {{ date('Y') }} AgentBuff KostCloud — Platform manajemen kos berbasis subscription & MCP.</p>
        </div>
    </footer>

    <style>[x-cloak]{display:none!important}</style>
</body>
</html>
