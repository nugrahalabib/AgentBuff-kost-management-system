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
@php
    $initialAuth = old('_auth_form', request('auth'));
    // 'admin' = buka modal login + form admin; selain itu login|register|null
    $initialAdminLogin = false;
    if ($initialAuth === 'admin') {
        $initialAuth = 'login';
        $initialAdminLogin = true;
    } elseif (! in_array($initialAuth, ['login', 'register'], true)) {
        $initialAuth = null;
    }
    // Gagal validasi form admin → buka form admin
    if (old('_auth_form') === 'admin') {
        $initialAuth = 'login';
        $initialAdminLogin = true;
    }
@endphp
<body
    x-data="{ authModal: @js($initialAuth), adminLogin: @js($initialAdminLogin), open: false, scrolled: false }"
    class="bg-white text-gray-800 antialiased"
>

    {{-- ===================== NAVBAR ===================== --}}
    <header @scroll.window="scrolled = window.scrollY > 20"
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
                        <button type="button" @click="authModal = 'login'" class="px-4 py-2.5 text-sm font-bold text-gray-700 hover:text-emerald-700 transition">Masuk</button>
                        <button type="button" @click="authModal = 'register'" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold shadow-md hover:bg-emerald-700 transition">Mulai Gratis</button>
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
                        <button type="button" @click="open=false; authModal='login'" class="flex-1 text-center px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-bold">Masuk</button>
                        <button type="button" @click="open=false; authModal='register'" class="flex-1 text-center px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold">Mulai Gratis</button>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    {{-- ===================== HERO ===================== --}}
    <section id="top" class="hero-gradient-bg relative overflow-hidden pt-32 pb-24 sm:pt-40 sm:pb-28">
        {{-- Lapisan dekor (di belakang konten) --}}
        <div class="grid-pattern absolute inset-0 -z-10 opacity-70" aria-hidden="true"></div>
        <div class="ambient-blob ambient-blob-emerald absolute -top-40 -right-40 w-[34rem] h-[34rem] -z-10" aria-hidden="true"></div>
        <div class="ambient-blob ambient-blob-cyan absolute top-1/2 -left-44 w-[28rem] h-[28rem] -z-10" aria-hidden="true"></div>
        <div class="geo-shape geo-shape-1 -z-10" aria-hidden="true"></div>
        <span class="particle particle-1" aria-hidden="true"></span>
        <span class="particle particle-2" aria-hidden="true"></span>
        <span class="particle particle-3" aria-hidden="true"></span>
        <span class="particle particle-5" aria-hidden="true"></span>
        <span class="particle particle-7" aria-hidden="true"></span>
        <span class="particle particle-8" aria-hidden="true"></span>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-14 lg:gap-8 items-center">

                {{-- Kolom kiri: copy --}}
                <div class="lg:col-span-6 text-center lg:text-left scroll-reveal-left">
                    <span class="hero-badge badge-glass-emerald inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold tracking-wide">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        MANAJEMEN KOS &times; AI AGENT (MCP)
                    </span>

                    <h1 class="mt-6 text-4xl sm:text-5xl lg:text-[3.4rem] font-extrabold tracking-tight text-gray-900 leading-[1.1]">
                        Kelola kos-mu <span class="gradient-text">rapi &amp; otomatis</span>, kendalikan lewat AI agent.
                    </h1>

                    <p class="mt-6 text-lg text-gray-600 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Satu dashboard cloud untuk kamar, penyewa, transaksi, dan laporan &mdash;
                        dan bisa dijalankan AI agent (Claude Code, Codex, dll) lewat MCP hanya dengan satu bearer token.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                        @auth
                            <a href="{{ route('dashboard') }}" class="group px-7 py-3.5 rounded-xl bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-500/25 hover:bg-emerald-700 transition transform hover:scale-105 inline-flex items-center justify-center gap-2">
                                Buka Dashboard
                                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        @else
                            <button type="button" @click="authModal = 'register'" class="group px-7 py-3.5 rounded-xl bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-500/25 hover:bg-emerald-700 transition transform hover:scale-105 inline-flex items-center justify-center gap-2">
                                Mulai Gratis
                                <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </button>
                        @endauth
                        <a href="#fitur" class="px-7 py-3.5 rounded-xl bg-white/80 backdrop-blur border border-gray-200 text-gray-700 font-bold shadow-sm hover:bg-white hover:border-emerald-200 transition inline-flex items-center justify-center gap-2 dark:bg-white/5 dark:hover:bg-white/10">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            Lihat Fitur
                        </a>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-center lg:justify-start gap-x-5 gap-y-2 text-xs font-medium text-gray-500">
                        <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Tanpa kartu kredit</span>
                        <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Langsung pakai</span>
                        <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Data kos terpisah aman</span>
                    </div>
                </div>

                {{-- Kolom kanan: mockup dashboard mini (murni HTML/CSS) --}}
                <div class="lg:col-span-6 scroll-reveal-right delay-200 relative">
                    <div class="hero-image-glow">
                        <div class="relative rounded-2xl overflow-hidden border border-gray-200/70 bg-white shadow-2xl dark:border-white/10">
                            {{-- Title bar --}}
                            <div class="flex items-center gap-1.5 px-4 py-2.5 bg-gray-50 border-b border-gray-200/70 dark:border-white/10">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                                <span class="ml-3 text-[11px] font-medium text-gray-400">kos.agentbuff.id/dashboard</span>
                            </div>
                            {{-- Body: sidebar + konten --}}
                            <div class="flex">
                                {{-- Sidebar emerald (tetap solid di kedua mode) --}}
                                <aside class="hidden sm:flex flex-col gap-0.5 w-40 shrink-0 bg-emerald-600 p-3 text-emerald-50">
                                    <div class="flex items-center gap-2 px-2 py-1.5 mb-2 font-extrabold text-white text-sm">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-white/20">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
                                        </span> KostCloud
                                    </div>
                                    <span class="flex items-center gap-2 px-2 py-1.5 rounded-lg bg-white/20 text-white text-xs font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h6v6H4zM14 5h6v4h-6zM14 13h6v6h-6zM4 15h6v4H4z"/></svg> Dashboard
                                    </span>
                                    <span class="flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs text-emerald-50/90">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21V5a2 2 0 012-2h8a2 2 0 012 2v16M15 21h6V11h-6M8 9h2"/></svg> Kamar
                                    </span>
                                    <span class="flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs text-emerald-50/90">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-1a4 4 0 00-3-3.87M9 20H4v-1a4 4 0 013-3.87m6-1a4 4 0 100-8 4 4 0 000 8z"/></svg> Penyewa
                                    </span>
                                    <span class="flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs text-emerald-50/90">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M6 15h4m-7 4h18a2 2 0 002-2V7a2 2 0 00-2-2H3a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Transaksi
                                    </span>
                                    <span class="flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs text-emerald-50/90">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg> Laporan
                                    </span>
                                </aside>
                                {{-- Konten --}}
                                <div class="flex-1 p-4 space-y-3 bg-white">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Ringkasan</p>
                                            <p class="text-sm font-extrabold text-gray-900">Kos Mutiara &middot; Juli</p>
                                        </div>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live
                                        </span>
                                    </div>
                                    {{-- Kartu statistik --}}
                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="stat-shimmer rounded-lg border border-gray-100 p-2.5 bg-white">
                                            <p class="text-[10px] text-gray-500">Terisi</p>
                                            <p class="text-lg font-extrabold text-gray-900"><span data-count-to="9">9</span><span class="text-gray-400 text-xs font-bold">/10</span></p>
                                        </div>
                                        <div class="stat-shimmer rounded-lg border border-gray-100 p-2.5 bg-white">
                                            <p class="text-[10px] text-gray-500">Penyewa</p>
                                            <p class="text-lg font-extrabold text-gray-900" data-count-to="12">12</p>
                                        </div>
                                        <div class="stat-shimmer rounded-lg border border-gray-100 p-2.5 bg-white">
                                            <p class="text-[10px] text-gray-500">Bulan Ini</p>
                                            <p class="text-lg font-extrabold text-emerald-600">Rp8,4jt</p>
                                        </div>
                                    </div>
                                    {{-- Donut okupansi + grafik bar --}}
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="flex items-center gap-2.5 rounded-lg border border-gray-100 p-2.5 bg-white">
                                            <div class="relative w-14 h-14 shrink-0">
                                                <svg viewBox="0 0 36 36" class="w-14 h-14 -rotate-90 text-gray-200 dark:text-slate-600">
                                                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="currentColor" stroke-width="3.5"></circle>
                                                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#10b981" stroke-width="3.5" stroke-dasharray="90 100" stroke-linecap="round"></circle>
                                                </svg>
                                                <div class="absolute inset-0 flex items-center justify-center text-xs font-extrabold text-gray-900">90%</div>
                                            </div>
                                            <div class="text-[10px] space-y-1 leading-tight">
                                                <p class="font-bold text-gray-900">Okupansi</p>
                                                <p class="flex items-center gap-1 text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Terisi 9</p>
                                                <p class="flex items-center gap-1 text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> Kosong 1</p>
                                            </div>
                                        </div>
                                        <div class="rounded-lg border border-gray-100 p-2.5 bg-white">
                                            <div class="flex items-center justify-between mb-1.5">
                                                <p class="text-[10px] font-bold text-gray-700">Pemasukan</p>
                                                <p class="text-[10px] font-bold text-emerald-600">+12%</p>
                                            </div>
                                            <div class="flex items-end gap-1 h-9">
                                                @foreach([45,60,52,78,68,92] as $h)
                                                    <div class="flex-1 rounded-t-sm bg-gradient-to-t from-emerald-500 to-emerald-300" style="height: {{ $h }}%"></div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Baris aktivitas agent (pembeda MCP) --}}
                                    <div class="rounded-lg bg-gray-900 p-2.5 flex items-start gap-2">
                                        <span class="mt-0.5 inline-flex items-center justify-center w-5 h-5 rounded-md bg-emerald-500/20 text-emerald-300 shrink-0">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-mono text-emerald-300 truncate">agent: verifikasi transfer Kamar A-12</p>
                                            <p class="text-[10px] font-mono text-gray-400 mt-0.5 truncate">&rarr; pembayaran tercatat, penyewa ditempatkan &check;</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Chip mengambang (aman, tak menimpa konten) --}}
                    <div class="hidden lg:flex items-center gap-2 absolute -bottom-4 -left-4 px-3 py-2 rounded-xl map-glass-card text-xs font-semibold text-gray-700 shadow-lg">
                        <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>
                        AI agent menambah 1 kamar
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== STRIP KOMPATIBEL AGENT ===================== --}}
    <section class="relative bg-white border-y border-gray-100 py-8 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center justify-center gap-x-8 gap-y-4 scroll-reveal">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 shrink-0">Kompatibel dengan AI agent</p>
                <div class="flex flex-wrap items-center justify-center gap-2.5">
                    @foreach(['Claude Code', 'Codex', 'Hermes Agent', 'OpenClaw', 'Agentbuff'] as $agent)
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 border border-gray-100 text-sm font-bold text-gray-700 hover:border-emerald-200 hover:text-emerald-700 transition">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ $agent }}
                        </span>
                    @endforeach
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold text-gray-400">&amp; MCP lain</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== STATS / KEUNGGULAN BAND ===================== --}}
    <section class="relative py-20 bg-gray-50 overflow-hidden">
        <div class="ambient-blob ambient-blob-cyan absolute -bottom-32 -left-32 w-96 h-96 opacity-30" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto scroll-reveal mb-12">
                <p class="text-sm font-bold uppercase tracking-widest text-emerald-600 mb-3">Dibuat untuk pemilik kos</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                    <span class="section-title-container">Rapi, aman, dan siap otomatis<span class="section-title-line"></span></span>
                </h2>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="stat-shimmer premium-card-interactive rounded-2xl p-6 text-center scroll-reveal-scale">
                    <div class="mx-auto mb-3 w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4-2a6 6 0 01-7.743 5.743L11 15H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <p class="text-3xl font-extrabold gradient-text leading-none">1 token</p>
                    <p class="mt-2 text-sm text-gray-600">Cukup satu bearer token untuk hubungkan AI agent lewat MCP.</p>
                </div>
                <div class="stat-shimmer premium-card-interactive rounded-2xl p-6 text-center scroll-reveal-scale delay-100">
                    <div class="mx-auto mb-3 w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-1a4 4 0 00-3-3.87M9 20H4v-1a4 4 0 013-3.87m6-1a4 4 0 100-8 4 4 0 000 8z"/></svg>
                    </div>
                    <p class="text-3xl font-extrabold gradient-text leading-none">Owner + Admin</p>
                    <p class="mt-2 text-sm text-gray-600">Bagi tugas dengan admin, data tetap satu dan tersinkron.</p>
                </div>
                <div class="stat-shimmer premium-card-interactive rounded-2xl p-6 text-center scroll-reveal-scale delay-200">
                    <div class="mx-auto mb-3 w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m6-14h.01M9 11h.01M15 7h.01M15 11h.01M9 15h6"/></svg>
                    </div>
                    <p class="text-3xl font-extrabold gradient-text leading-none">Multi-kos</p>
                    <p class="mt-2 text-sm text-gray-600">Tiap properti punya workspace terpisah &amp; aman.</p>
                </div>
                <div class="stat-shimmer premium-card-interactive rounded-2xl p-6 text-center scroll-reveal-scale delay-300">
                    <div class="mx-auto mb-3 w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-3xl font-extrabold gradient-text leading-none">PDF &amp; Excel</p>
                    <p class="mt-2 text-sm text-gray-600">Laporan keuangan &amp; hunian siap unduh kapan saja.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== CARA KERJA 3 LANGKAH ===================== --}}
    <section class="relative py-24 bg-white overflow-hidden">
        <div class="grid-pattern absolute inset-0 opacity-50" aria-hidden="true"></div>
        <div class="ambient-blob ambient-blob-emerald absolute top-10 -right-40 w-96 h-96 opacity-25" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto scroll-reveal mb-16">
                <p class="text-sm font-bold uppercase tracking-widest text-emerald-600 mb-3">Cara kerja</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                    <span class="section-title-container">Siap dalam 3 langkah<span class="section-title-line"></span></span>
                </h2>
                <p class="mt-8 text-gray-600">Dari daftar sampai kos dikendalikan AI agent &mdash; tanpa ribet.</p>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                {{-- garis penghubung dekoratif --}}
                <div class="hidden md:block absolute top-9 left-[16.6%] right-[16.6%] h-px bg-gradient-to-r from-emerald-200 via-emerald-300 to-emerald-200 z-0" aria-hidden="true"></div>

                <div class="relative premium-card-interactive rounded-2xl p-7 text-center scroll-reveal-scale">
                    <div class="mx-auto mb-5 w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl font-extrabold shadow-lg shadow-emerald-500/25">1</div>
                    <h3 class="font-bold text-lg text-gray-900">Buat workspace kos</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Daftar gratis, lalu siapkan workspace untuk kos-mu &mdash; terpisah &amp; aman.</p>
                </div>
                <div class="relative premium-card-interactive rounded-2xl p-7 text-center scroll-reveal-scale delay-100">
                    <div class="mx-auto mb-5 w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl font-extrabold shadow-lg shadow-emerald-500/25">2</div>
                    <h3 class="font-bold text-lg text-gray-900">Isi kamar &amp; penyewa</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Tambah kamar, catat penyewa, dan mulai rekam transaksi pembayaran.</p>
                </div>
                <div class="relative premium-card-interactive rounded-2xl p-7 text-center scroll-reveal-scale delay-200">
                    <div class="mx-auto mb-5 w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl font-extrabold shadow-lg shadow-emerald-500/25">3</div>
                    <h3 class="font-bold text-lg text-gray-900">Hubungkan AI agent</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Buat bearer token MCP, tempel ke AI agent &mdash; biarkan ia bantu kelola kos.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== FITUR (BENTO PREMIUM) ===================== --}}
    <section id="fitur" class="relative py-24 bg-gray-50 overflow-hidden">
        <div class="ambient-blob ambient-blob-emerald absolute top-20 -left-40 w-96 h-96 opacity-30" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto scroll-reveal">
                <p class="text-sm font-bold uppercase tracking-widest text-emerald-600 mb-3">Fitur Lengkap</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                    <span class="section-title-container">Semua kebutuhan manajemen kos<span class="section-title-line"></span></span>
                </h2>
                <p class="mt-8 text-gray-600">Dari kamar sampai laporan keuangan &mdash; semuanya internal, tanpa reservasi publik.</p>
            </div>

            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 items-stretch">
                {{-- Kartu besar (span 2 kolom): Manajemen Kamar --}}
                <div class="premium-card-interactive rounded-2xl p-7 sm:col-span-2 lg:col-span-2 scroll-reveal-scale">
                    <div class="flex items-start gap-5 h-full">
                        <div class="facility-icon-glow shrink-0 w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21V7a2 2 0 012-2h4v16M9 21h12V11a2 2 0 00-2-2h-4m-3 4h.01M12 17h.01"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900">Manajemen Kamar</h3>
                            <p class="mt-2 text-sm text-gray-600 leading-relaxed">Tambah, ubah status, dan hapus kamar. Pantau kamar kosong, terisi, dan perbaikan secara real-time dari satu papan yang jelas.</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold">Kosong</span>
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-xs font-semibold">Terisi</span>
                                <span class="px-2.5 py-1 rounded-lg bg-yellow-50 text-yellow-700 text-xs font-semibold">Perbaikan</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kartu standar: Data Penyewa --}}
                <div class="premium-card-interactive rounded-2xl p-7 scroll-reveal-scale delay-100">
                    <div class="facility-icon-glow w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-1a4 4 0 00-3-3.87M9 20H4v-1a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a3 3 0 10-3-3"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">Data Penyewa</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Catat penyewa &amp; tempatkan langsung ke kamar tersedia. Kelola dokumen dan riwayat hunian.</p>
                </div>

                {{-- Kartu standar: Transaksi & Pembayaran --}}
                <div class="premium-card-interactive rounded-2xl p-7 scroll-reveal-scale delay-100">
                    <div class="facility-icon-glow w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">Transaksi &amp; Pembayaran</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Input pembayaran manual atau verifikasi bukti transfer. Otomatis menempatkan penyewa ke kamar.</p>
                </div>

                {{-- Kartu standar: Laporan PDF & Excel --}}
                <div class="premium-card-interactive rounded-2xl p-7 scroll-reveal-scale delay-200">
                    <div class="facility-icon-glow w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">Laporan PDF &amp; Excel</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Generate laporan keuangan, status kamar, dan data penyewa. Unduh sebagai PDF atau Excel.</p>
                </div>

                {{-- Kartu standar: Owner + Admin --}}
                <div class="premium-card-interactive rounded-2xl p-7 scroll-reveal-scale delay-300">
                    <div class="facility-icon-glow w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.6 1.4A9 9 0 1112 3a9 9 0 018.6 6.4z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">Owner + Admin</h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">Buat akun admin opsional. Owner dan admin berbagi data yang sama &mdash; setiap perubahan langsung tersinkron.</p>
                </div>

                {{-- Kartu banner lebar penutup: Multi-Kos / Multi-tenant --}}
                <div class="premium-card-interactive rounded-2xl p-7 sm:col-span-2 lg:col-span-3 scroll-reveal-scale">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                        <div class="facility-icon-glow shrink-0 w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m6-14h.01M9 11h.01M15 7h.01M15 11h.01M9 15h6"/></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-900">Multi-Kos (Multi-tenant)</h3>
                            <p class="mt-2 text-sm text-gray-600 leading-relaxed max-w-2xl">Punya lebih dari satu kos? Setiap properti berjalan di workspace-nya sendiri &mdash; data, penyewa, dan laporan sepenuhnya terpisah dan aman, berbasis langganan.</p>
                        </div>
                        <div class="flex flex-wrap gap-2 sm:justify-end">
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold">Kos Mutiara</span>
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold">Kos Melati</span>
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold">+ Tambah kos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== MCP / AI AGENT ===================== --}}
    <section id="mcp" class="relative py-24 bg-white overflow-hidden">
        <div class="ambient-blob ambient-blob-cyan absolute -bottom-32 -right-32 w-[30rem] h-[30rem] opacity-25" aria-hidden="true"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                {{-- Kiri: teks --}}
                <div class="scroll-reveal-left">
                    <span class="badge-glass-emerald inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold tracking-wide">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        MODEL CONTEXT PROTOCOL
                    </span>
                    <h2 class="mt-5 text-3xl sm:text-4xl font-extrabold leading-tight text-gray-900">
                        Biarkan <span class="gradient-text">AI agent</span> yang mengurus kos-mu
                    </h2>
                    <p class="mt-5 text-gray-600 leading-relaxed">
                        Hasilkan <span class="text-emerald-600 font-semibold">bearer token</span> sekali klik, lalu berikan ke AI agent favoritmu.
                        Mereka bisa menambah kamar, mencatat penyewa, memverifikasi transaksi, hingga membuat laporan &mdash;
                        semuanya lewat MCP, aman dalam batas kos-mu sendiri.
                    </p>
                    <ul class="mt-7 grid sm:grid-cols-2 gap-3">
                        @foreach(['Claude Code', 'Codex', 'Hermes Agent', 'OpenClaw', 'Agentbuff'] as $agent)
                            <li class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gray-50 border border-gray-100">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span class="text-sm font-semibold text-gray-700">{{ $agent }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-4 text-xs text-gray-500">&hellip; dan MCP client lain yang mendukung HTTP transport &amp; bearer auth.</p>
                </div>

                {{-- Kanan: terminal mockup (tetap gelap di kedua mode) --}}
                <div class="scroll-reveal-right delay-200">
                    <div class="hero-image-glow">
                        <div class="rounded-2xl bg-gray-950 border border-white/10 shadow-2xl overflow-hidden">
                            <div class="flex items-center gap-1.5 px-4 py-3 border-b border-white/10">
                                <span class="w-3 h-3 rounded-full bg-red-400"></span>
                                <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                                <span class="w-3 h-3 rounded-full bg-green-400"></span>
                                <span class="ml-3 text-xs font-mono text-gray-400">mcp.json</span>
                            </div>
                            <pre class="p-5 text-xs sm:text-sm leading-relaxed overflow-x-auto font-mono text-gray-300"><code>{
  <span class="text-emerald-300">"mcpServers"</span>: {
    <span class="text-emerald-300">"kostcloud"</span>: {
      <span class="text-sky-300">"type"</span>: <span class="text-amber-200">"http"</span>,
      <span class="text-sky-300">"url"</span>: <span class="text-amber-200">"https://kos.agentbuff.id/mcp"</span>,
      <span class="text-sky-300">"headers"</span>: {
        <span class="text-sky-300">"Authorization"</span>: <span class="text-amber-200">"Bearer kc_live_&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"</span>
      }
    }
  }
}</code></pre>
                            <div class="px-5 py-3 border-t border-white/10 flex items-center gap-2 text-xs font-mono text-emerald-300">
                                <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>
                                terhubung &middot; 14 tools tersedia
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== CTA ===================== --}}
    <section class="relative py-24 bg-emerald-600 overflow-hidden">
        <div class="cta-pattern absolute inset-0" aria-hidden="true"></div>
        <div class="cta-glow absolute -top-24 -right-24 w-96 h-96 rounded-full blur-3xl" aria-hidden="true"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-900/30 rounded-full blur-3xl" aria-hidden="true"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center scroll-reveal">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white">Siap mengelola kos lebih rapi?</h2>
            <p class="mt-4 text-emerald-50 text-lg">Buat akun pemilik kos sekarang &mdash; gratis untuk memulai, dan siap dikendalikan AI agent.</p>
            <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-on-emerald inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-white text-emerald-700 font-extrabold shadow-lg hover:bg-emerald-50 transition transform hover:scale-105">
                        Buka Dashboard
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <button type="button" @click="authModal = 'register'" class="btn-on-emerald inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-white text-emerald-700 font-extrabold shadow-lg hover:bg-emerald-50 transition transform hover:scale-105">
                        Daftar Sekarang
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                    <button type="button" @click="authModal = 'login'" class="inline-flex items-center justify-center px-8 py-4 rounded-xl bg-emerald-500/30 text-white font-bold border border-white/30 backdrop-blur hover:bg-emerald-500/50 transition">
                        Sudah punya akun? Masuk
                    </button>
                @endauth
            </div>
            <p class="mt-5 text-xs text-emerald-100">Tanpa kartu kredit &middot; Data kos terpisah &amp; aman</p>
        </div>
    </section>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="bg-gray-50 text-gray-500 py-12 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-2 font-extrabold text-gray-800">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-600 text-white shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
                    </span>
                    AgentBuff KostCloud
                </div>
                <nav class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm font-medium text-gray-500">
                    <a href="#fitur" class="hover:text-emerald-700 transition">Fitur</a>
                    <a href="#mcp" class="hover:text-emerald-700 transition">AI Agent (MCP)</a>
                    <button type="button" @click="authModal = 'register'" class="hover:text-emerald-700 transition">Mulai Gratis</button>
                </nav>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-100 text-center sm:text-left">
                <p class="text-sm">&copy; {{ date('Y') }} AgentBuff KostCloud &mdash; Platform manajemen kos berbasis subscription &amp; MCP.</p>
            </div>
        </div>
    </footer>

    <style>[x-cloak]{display:none!important}</style>

    <script>
(function () {
    'use strict';

    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var noIO = !('IntersectionObserver' in window);

    // --- Scroll reveal (observer + fallback aman) ---
    var els = document.querySelectorAll(
        '.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right, .scroll-reveal-scale'
    );
    if (els.length) {
        if (reduce || noIO) {
            // Tampilkan semua tanpa animasi — cegah konten tak terlihat permanen.
            els.forEach(function (el) { el.classList.add('revealed'); });
        } else {
            var revObserver = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
            els.forEach(function (el) { revObserver.observe(el); });
        }
    }

    // --- Count-up untuk angka di mockup ([data-count-to]) ---
    var counters = document.querySelectorAll('[data-count-to]');
    if (!counters.length) return;

    if (reduce || noIO) {
        counters.forEach(function (el) {
            var t = el.getAttribute('data-count-to');
            if (t !== null) el.textContent = t;
        });
        return;
    }

    function animateCount(el) {
        var target = parseInt(el.getAttribute('data-count-to'), 10);
        if (isNaN(target)) return;
        var start = null, dur = 900;
        el.textContent = '0';
        function step(ts) {
            if (start === null) start = ts;
            var p = Math.min((ts - start) / dur, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            el.textContent = String(Math.round(target * eased));
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    var cObserver = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                animateCount(entry.target);
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.6 });
    counters.forEach(function (el) { cObserver.observe(el); });
})();
</script>

    @include('partials.auth-modals')
</body>
</html>
