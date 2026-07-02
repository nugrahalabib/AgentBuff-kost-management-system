<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Google Search Console site verification --}}
    <meta name="google-site-verification" content="Kiu8GTK0nPywy7XsJo7qm7L6F-26qkzxCGdJLz9YA20" />

    @php
        // ---- SEO data (with sensible fallbacks) ----------------------------
        $heroDesc = $heroSection?->items->firstWhere('item_type', 'description')?->value;
        $heroImg  = $heroSection?->items->firstWhere('item_type', 'hero_image_path')?->value;

        $addressContact = $contacts->firstWhere('contact_type', 'address');
        $phoneContact   = $contacts->firstWhere('contact_type', 'phone');
        $emailContact   = $contacts->firstWhere('contact_type', 'email');

        $seoTitle = 'Kos Putri Mutiara27 Semarang — Hunian Nyaman & Eksklusif';
        $seoDesc  = $heroDesc
            ?: 'Kos Putri Mutiara27 Semarang: hunian eksklusif & nyaman dengan fasilitas lengkap, WiFi cepat, dan keamanan CCTV 24 jam. Lokasi strategis untuk mahasiswa & profesional muda.';
        // Trim to a search-friendly length.
        $seoDesc  = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', $seoDesc)), 160);

        $seoUrl   = url()->current();
        $seoImage = $heroImg
            ? asset('storage/' . $heroImg)
            : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80';

        // ---- Structured data: LodgingBusiness ------------------------------
        $jsonLd = [
            '@context'    => 'https://schema.org',
            '@type'       => 'LodgingBusiness',
            'name'        => 'Kos Putri Mutiara27',
            'description' => $seoDesc,
            'url'         => url('/'),
            'image'       => $seoImage,
            'priceRange'  => 'Rp',
            'address'     => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $addressContact?->contact_value ?: 'BPI Blok S No.29A',
                'addressLocality' => 'Semarang',
                'addressRegion'   => 'Jawa Tengah',
                'addressCountry'  => 'ID',
            ],
            'geo' => [
                '@type'     => 'GeoCoordinates',
                'latitude'  => -6.9941933,
                'longitude' => 110.3510022,
            ],
        ];
        if ($phoneContact?->contact_value) {
            $jsonLd['telephone'] = $phoneContact->contact_value;
        }
        if ($emailContact?->contact_value) {
            $jsonLd['email'] = $emailContact->contact_value;
        }
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    <meta name="keywords" content="kos putri Semarang, kos eksklusif Semarang, kost mutiara27, sewa kamar kos Semarang, kos mahasiswa Semarang, hunian nyaman Semarang">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Kos Putri Mutiara27">
    <link rel="canonical" href="{{ $seoUrl }}">

    {{-- Open Graph (preview saat dibagikan di WhatsApp, Facebook, dll) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Kos Putri Mutiara27">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDesc }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    {{-- Data terstruktur untuk Google (rich result lokasi/bisnis) --}}
    <script type="application/ld+json">
        {{-- JSON_HEX_TAG escapes < and > so an admin-supplied "</script>" can't break out. --}}
        {!! json_encode($jsonLd, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) !!}
    </script>

    {{-- Branded favicon (M27 emerald) --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-white">

    <nav class="fixed w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-emerald-600 p-1.5 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-800 tracking-wide">Mutiara<span class="text-emerald-600">27</span></span>
            </div>

            <div class="hidden md:flex items-center gap-8">
                <a href="#home" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                    Beranda
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#galeri" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                    Galeri
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#fasilitas" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                    Fasilitas
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#kamar" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                    Pilihan Kamar
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#kontak" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
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

    <div id="home" class="relative pt-24 pb-14 sm:pt-32 sm:pb-24 lg:pt-48 lg:pb-40 overflow-hidden hero-gradient-bg grid-pattern wave-divider wave-divider-white">
        {{-- Animated Background Blobs --}}
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-[450px] h-[450px] bg-emerald-300/35 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-96 h-96 bg-cyan-300/30 rounded-full blur-[90px]"></div>
        <div class="absolute top-1/2 left-1/3 -translate-y-1/2 w-[650px] h-[650px] bg-emerald-100/25 rounded-full blur-[120px]"></div>

        {{-- Floating Particles --}}
        <div class="particle particle-1"></div>
        <div class="particle particle-2"></div>
        <div class="particle particle-3"></div>
        <div class="particle particle-4"></div>
        <div class="particle particle-5"></div>
        <div class="particle particle-6"></div>
        <div class="particle particle-7"></div>
        <div class="particle particle-8"></div>

        {{-- Morphing Geometric Shapes --}}
        <div class="geo-shape geo-shape-1"></div>
        <div class="geo-shape geo-shape-2"></div>
        <div class="geo-shape geo-shape-3"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <div class="space-y-6 scroll-reveal-left" style="transition-delay: 0ms;">
                    @if($heroSection)
                        @php
                            $badge = $heroSection->items->firstWhere('item_type', 'badge')?->value;
                            $title = $heroSection->items->firstWhere('item_type', 'title')?->value;
                            $subtitle = $heroSection->items->firstWhere('item_type', 'subtitle')?->value;
                            $description = $heroSection->items->firstWhere('item_type', 'description')?->value;
                        @endphp
                    @endif
                    
                    @if(!empty($badge))
                        <span class="inline-block py-1.5 px-4 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold uppercase tracking-wider border border-emerald-100 hero-badge">
                            {{ $badge }}
                        </span>
                    @else
                        <span class="inline-block py-1.5 px-4 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold uppercase tracking-wider border border-emerald-100 hero-badge">
                            ✨ Hunian Eksklusif & Nyaman
                        </span>
                    @endif

                    @if(!empty($title))
                        <h1 class="text-3xl sm:text-4xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                            {{-- Escape both first, then inject the highlight span around the subtitle.
                                 This prevents admin-supplied HTML/JS from being executed. --}}
                            @if(!empty($subtitle) && str_contains($title, $subtitle))
                                {!! str_replace(e($subtitle), '<span class="gradient-text">' . e($subtitle) . '</span>', e($title)) !!}
                            @else
                                {{ $title }}
                            @endif
                        </h1>
                    @else
                        <h1 class="text-3xl sm:text-4xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                            Temukan Kenyamanan <br>
                            Seperti di <span class="gradient-text">Rumah Sendiri.</span>
                        </h1>
                    @endif

                    @if(!empty($description))
                        <p class="text-base sm:text-lg text-gray-600 leading-relaxed max-w-lg">
                            {{-- Preserve admin's line breaks but escape everything else. --}}
                            {!! nl2br(e($description)) !!}
                        </p>
                    @else
                        <p class="text-base sm:text-lg text-gray-600 leading-relaxed max-w-lg">
                            Fasilitas lengkap, lokasi strategis, dan keamanan 24 jam. Pilihan tepat untuk mahasiswa dan profesional muda.
                        </p>
                    @endif

                    {{-- Tombol bawaan (tidak diatur dari admin): CTA ke daftar kamar, sekunder ke kontak. --}}
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="#kamar" class="bg-emerald-600 hover:bg-emerald-700 text-white text-center font-bold py-3.5 px-8 rounded-xl shadow-xl shadow-emerald-200 transition transform hover:-translate-y-1 hover:shadow-2xl hover:shadow-emerald-300">
                            Lihat Ketersediaan
                        </a>
                        <a href="#kontak" class="bg-white border border-gray-200 text-gray-700 hover:text-emerald-600 hover:border-emerald-200 text-center font-bold py-3.5 px-8 rounded-xl transition flex items-center justify-center gap-2 hover:shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            Hubungi Kami
                        </a>
                    </div>
                    
                    <div class="pt-8 flex gap-6 sm:gap-8 border-t border-gray-200/60 mt-8">
                        <div class="stat-shimmer rounded-lg px-3 py-2 -mx-3">
                            <p class="text-2xl font-bold text-gray-800 counter-value">{{ $heroSection?->items->firstWhere('item_type', 'stat_1_value')?->value ?? '200+' }}</p>
                            <p class="text-sm text-gray-500">{{ $heroSection?->items->firstWhere('item_type', 'stat_1_label')?->value ?? 'Unit Kamar' }}</p>
                        </div>
                        <div class="stat-shimmer rounded-lg px-3 py-2">
                            <p class="text-2xl font-bold text-gray-800 counter-value">{{ $heroSection?->items->firstWhere('item_type', 'stat_2_value')?->value ?? '100%' }}</p>
                            <p class="text-sm text-gray-500">{{ $heroSection?->items->firstWhere('item_type', 'stat_2_label')?->value ?? 'Aman & CCTV' }}</p>
                        </div>
                        <div class="stat-shimmer rounded-lg px-3 py-2">
                            <p class="text-2xl font-bold text-gray-800 counter-value">{{ $heroSection?->items->firstWhere('item_type', 'stat_3_value')?->value ?? '4.9' }}</p>
                            <p class="text-sm text-gray-500">{{ $heroSection?->items->firstWhere('item_type', 'stat_3_label')?->value ?? 'Rating Penghuni' }}</p>
                        </div>
                    </div>
                </div>

                <div class="relative scroll-reveal-right" style="transition-delay: 200ms;">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white hero-image-glow">
                        @php
                            $heroImage = $heroSection?->items->firstWhere('item_type', 'hero_image_path')?->value;
                        @endphp
                        @if($heroImage)
                            <img src="{{ asset('storage/' . $heroImage) }}" alt="Hero Image" class="w-full h-full object-cover transform hover:scale-105 transition duration-700">
                        @else
                            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80" alt="Kamar Kos Mewah" class="w-full h-full object-cover transform hover:scale-105 transition duration-700">
                        @endif
                        
                        <div class="absolute bottom-6 left-6 bg-white/90 backdrop-blur-md p-4 rounded-xl shadow-lg border border-white max-w-xs hidden sm:block">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-100 p-2 rounded-full text-green-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $heroSection?->items->firstWhere('item_type', 'verify_badge_title')?->value ?? 'Terverifikasi' }}</p>
                                    <p class="text-xs text-gray-500">{{ $heroSection?->items->firstWhere('item_type', 'verify_badge_desc')?->value ?? 'Kebersihan & Fasilitas Terjamin' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -z-10 -bottom-6 -right-6">
                        <svg width="120" height="120" fill="none" viewBox="0 0 120 120">
                            <pattern id="dots" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                <rect x="0" y="0" width="4" height="4" class="text-emerald-200" fill="currentColor" />
                            </pattern>
                            <rect width="120" height="120" fill="url(#dots)" />
                        </svg>
                    </div>
                    {{-- Additional decorative corner element --}}
                    <div class="absolute -z-10 -top-4 -left-4">
                        <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                            <circle cx="40" cy="40" r="38" stroke="rgba(16,185,129,0.15)" stroke-width="2" stroke-dasharray="8 6"/>
                        </svg>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Galeri Section -->
    <div id="galeri" class="relative scroll-mt-16 py-16 md:py-24 bg-gradient-to-b from-white via-emerald-50/20 to-emerald-50/40 overflow-hidden grid-pattern wave-divider wave-divider-gray">
        {{-- Floating ambient blobs --}}
        <div class="absolute top-1/4 left-10 w-96 h-96 ambient-blob ambient-blob-cyan"></div>
        <div class="absolute bottom-10 right-10 w-80 h-80 ambient-blob ambient-blob-emerald"></div>
        {{-- Aksen dekoratif (elemen berat hanya tampil di layar >= md agar HP tetap ringan) --}}
        <div class="particle particle-3"></div>
        <div class="particle particle-6"></div>
        <div class="hidden md:block particle particle-1"></div>
        <div class="hidden md:block particle particle-8"></div>
        <div class="hidden md:block absolute top-2/3 right-1/4 w-72 h-72 ambient-blob ambient-blob-blue"></div>
        <div class="hidden md:block geo-shape geo-shape-2"></div>
        <div class="hidden md:block geo-shape geo-shape-3"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-8 sm:mb-16 scroll-reveal">
                <div class="section-title-container">
                    <h2 class="text-2xl sm:text-3xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">Galeri Hunian Mutiara27</h2>
                    <span class="section-title-line"></span>
                </div>
                <p class="text-gray-500 mt-3 sm:mt-6 text-sm sm:text-lg max-w-2xl mx-auto">Lihat kenyamanan, suasana modern, dan fasilitas lengkap di area hunian kami</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($galleries as $index => $gallery)
                    <!-- Gallery Carousel -->
                    <div class="relative h-72 rounded-2xl overflow-hidden group premium-card-interactive shadow-md scroll-reveal" style="transition-delay: {{ $index * 100 }}ms;">
                        <div class="gallery-carousel h-full flex transition-transform duration-500 ease-out" style="transform: translateX(0%)">
                            @forelse($gallery->images as $image)
                                @php $imgSrc = \Illuminate\Support\Str::startsWith($image, ['http://', 'https://']) ? $image : asset('storage/' . $image); @endphp
                                <img src="{{ $imgSrc }}" alt="{{ $gallery->title }}" class="gallery-photo w-full h-full object-cover flex-shrink-0 cursor-zoom-in group-hover:brightness-110 transition duration-300">
                            @empty
                                <div class="w-full h-full bg-gray-300 flex items-center justify-center flex-shrink-0">
                                    <span class="text-gray-500">No images</span>
                                </div>
                            @endforelse
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex items-end pointer-events-none">
                            <div class="w-full p-6 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                <h3 class="text-white font-bold text-lg tracking-wide">{{ $gallery->title }}</h3>
                                @if($gallery->description)
                                    <p class="text-white/80 text-sm mt-1 line-clamp-2">{{ $gallery->description }}</p>
                                @endif
                            </div>
                        </div>
                        @if($gallery->images && count($gallery->images) > 1)
                            <button class="carousel-prev absolute left-3 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white p-2 rounded-full text-gray-800 opacity-0 group-hover:opacity-100 transition shadow-lg backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <button class="carousel-next absolute right-3 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white p-2 rounded-full text-gray-800 opacity-0 group-hover:opacity-100 transition shadow-lg backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 premium-card-interactive rounded-2xl p-8 max-w-lg mx-auto">
                        <p class="text-gray-500 text-lg">Belum ada galeri. Hubungi administrator untuk menambahkan galeri.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Fasilitas Section -->
    <div id="fasilitas" class="relative scroll-mt-16 py-16 md:py-24 bg-gradient-to-b from-emerald-50/40 via-white to-white overflow-hidden grid-pattern wave-divider wave-divider-white">
        {{-- Ambient Blobs --}}
        <div class="absolute top-1/3 right-10 w-96 h-96 ambient-blob ambient-blob-emerald"></div>
        <div class="absolute bottom-10 left-5 w-80 h-80 ambient-blob ambient-blob-blue"></div>
        {{-- Aksen dekoratif (elemen berat hanya tampil di layar >= md) --}}
        <div class="particle particle-2"></div>
        <div class="particle particle-7"></div>
        <div class="hidden md:block particle particle-4"></div>
        <div class="hidden md:block particle particle-5"></div>
        <div class="hidden md:block absolute bottom-1/3 right-1/4 w-72 h-72 ambient-blob ambient-blob-cyan"></div>
        <div class="hidden md:block geo-shape geo-shape-1"></div>
        <div class="hidden md:block geo-shape geo-shape-3"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-8 sm:mb-16 scroll-reveal">
                <div class="section-title-container">
                    <h2 class="text-2xl sm:text-3xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">Fasilitas Lengkap</h2>
                    <span class="section-title-line"></span>
                </div>
                <p class="text-gray-500 mt-3 sm:mt-6 text-sm sm:text-lg max-w-2xl mx-auto">Nikmati berbagai fasilitas untuk kenyamanan maksimal hunian Anda</p>
            </div>

            @php
                $iconPaths = [
                    'bolt' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                    'wifi' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>',
                    'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                    'shield' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                    'camera' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>',
                    'car' => '<path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>',
                    'clock' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'key' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>',
                    'lock' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
                    'water' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
                    'fire' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>',
                    'wind' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.59 4.59A2 2 0 1111 8H2m10.59 11.41A2 2 0 1014 16H2m15.73-8.27A2.5 2.5 0 1119.5 12H2"/>',
                    'sun' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>',
                    'moon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>',
                    'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
                    'phone' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>',
                    'tv' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                    'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
                    'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                    'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
                    'cube' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                    'gift' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>',
                    'cake' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/>',
                ];
            @endphp

            @php
                // Palet warna ikon bergiliran agar fasilitas tampak berwarna.
                // Kelas ditulis literal (bukan interpolasi) supaya ter-generate Tailwind.
                $facilityPalette = [
                    ['icon' => 'bg-emerald-100 text-emerald-600', 'ring' => 'hover:border-emerald-300'],
                    ['icon' => 'bg-sky-100 text-sky-600',         'ring' => 'hover:border-sky-300'],
                    ['icon' => 'bg-amber-100 text-amber-600',     'ring' => 'hover:border-amber-300'],
                    ['icon' => 'bg-rose-100 text-rose-600',       'ring' => 'hover:border-rose-300'],
                    ['icon' => 'bg-violet-100 text-violet-600',   'ring' => 'hover:border-violet-300'],
                    ['icon' => 'bg-teal-100 text-teal-600',       'ring' => 'hover:border-teal-300'],
                ];
            @endphp
            {{-- Flex-wrap + justify-center: kartu ter-tengah saat belum penuh satu baris (seperti Pilihan Kamar). --}}
            <div class="flex flex-wrap justify-center gap-4">
                @forelse($facilities as $index => $facility)
                    @php $p = $facilityPalette[$index % count($facilityPalette)]; @endphp
                    <!-- Fasilitas -->
                    <div class="bg-white border border-gray-200 {{ $p['ring'] }} rounded-2xl p-5 text-center shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 scroll-reveal w-[calc(50%-0.5rem)] md:w-[calc(33.333%-0.667rem)] lg:w-[calc(25%-0.75rem)]" style="transition-delay: {{ $index * 80 }}ms;">
                        <div class="w-12 h-12 {{ $p['icon'] }} rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $iconPaths[$facility->icon ?? 'bolt'] ?? $iconPaths['bolt'] !!}
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 mb-1">{{ $facility->facility_name }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $facility->description }}</p>
                    </div>
                @empty
                    <div class="w-full text-center py-12 premium-card-interactive rounded-3xl p-8 max-w-lg mx-auto">
                        <p class="text-gray-500 text-lg">Belum ada fasilitas. Hubungi administrator untuk menambahkan fasilitas.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pilihan Tipe Kamar Section -->
    <div id="kamar" class="relative scroll-mt-16 py-16 md:py-24 bg-gradient-to-b from-white via-emerald-50/20 to-emerald-50/40 overflow-hidden grid-pattern wave-divider wave-divider-gray">
        {{-- Floating ambient blobs --}}
        <div class="absolute top-1/4 left-5 w-96 h-96 ambient-blob ambient-blob-cyan"></div>
        <div class="absolute bottom-1/4 right-5 w-80 h-80 ambient-blob ambient-blob-emerald"></div>
        {{-- Aksen dekoratif (elemen berat hanya tampil di layar >= md) --}}
        <div class="particle particle-4"></div>
        <div class="particle particle-8"></div>
        <div class="hidden md:block particle particle-2"></div>
        <div class="hidden md:block particle particle-6"></div>
        <div class="hidden md:block absolute top-1/3 right-1/3 w-72 h-72 ambient-blob ambient-blob-blue"></div>
        <div class="hidden md:block geo-shape geo-shape-1"></div>
        <div class="hidden md:block geo-shape geo-shape-2"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-8 sm:mb-16 scroll-reveal">
                <div class="section-title-container">
                    <h2 class="text-2xl sm:text-3xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">Pilihan Tipe Kamar</h2>
                    <span class="section-title-line"></span>
                </div>
                <p class="text-gray-500 mt-3 sm:mt-6 text-sm sm:text-lg max-w-2xl mx-auto">Pilih kamar terbaik yang sesuai dengan gaya hidup dan kebutuhan Anda</p>
            </div>

            {{-- Flex-wrap (bukan grid) supaya saat kartu belum penuh satu baris, ia ter-tengah.
                 Lebar kartu tetap meniru 3-kolom: penuh di mobile, 2 di md, 3 di lg. --}}
            <div class="flex flex-wrap justify-center gap-8">

                @if($tipeKamar->count() > 0)
                    @forelse($tipeKamar as $index => $room)
                        <!-- Room Card -->
                        <div class="premium-card-interactive rounded-3xl overflow-hidden shadow-md flex flex-col scroll-reveal w-full md:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.333rem)]" style="transition-delay: {{ $index * 100 }}ms;">
                            <div class="relative h-64 overflow-hidden">
                                @if($room->image_path)
                                    {{-- Tampilkan gambar: URL penuh (http) langsung, selain itu dari storage --}}
                                    @php $roomImg = \Illuminate\Support\Str::startsWith($room->image_path, ['http://', 'https://']) ? $room->image_path : asset('storage/' . $room->image_path); @endphp
                                    <img src="{{ $roomImg }}" alt="{{ $room->name }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                                @else
                                    {{-- Fallback to placeholder images --}}
                                    @php
                                        // Foto cadangan (semua sudah diverifikasi dapat diakses)
                                        $fallbackImages = [
                                            'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                            'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                            'https://images.unsplash.com/photo-1567767292278-a4f21aa2d36e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                            'https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                        ];
                                        // Pakai id kamar (stabil) agar foto cadangan konsisten di semua halaman.
                                        $imageIndex = $room->id % count($fallbackImages);
                                    @endphp
                                    <img src="{{ $fallbackImages[$imageIndex] }}" alt="{{ $room->name }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                                @endif
                                
                                <!-- Availability Glass Badge -->
                                @if($room->available_rooms_count > 0)
                                    <div class="absolute top-4 left-4 badge-glass-emerald text-xs font-bold px-3 py-1.5 rounded-full shadow-md z-10">
                                        {{ $room->available_rooms_count }} Tersedia
                                    </div>
                                @else
                                    <div class="absolute top-4 left-4 badge-glass-red text-xs font-bold px-3 py-1.5 rounded-full shadow-md z-10">
                                        Sold Out
                                    </div>
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center z-10">
                                        <span class="text-white text-2xl font-bold bg-red-600/90 backdrop-blur-sm px-6 py-2 rounded-xl shadow-lg border border-red-500">SOLD OUT</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6 flex flex-col flex-grow relative z-10">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-xl font-bold text-gray-800 tracking-tight group-hover:text-emerald-700 transition-colors">{{ $room->name }}</h3>
                                    <div class="text-right">
                                        <p class="text-emerald-600 font-extrabold text-lg">
                                            Rp {{ number_format($room->rent_per_person, 0, ',', '.') }}
                                        </p>
                                        @if($room->capacity > 1)
                                            <p class="text-xs text-emerald-500 font-medium">/ orang (Kapasitas {{ $room->capacity }})</p>
                                        @else
                                            <p class="text-xs text-gray-400 font-medium">/ bulan</p>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 mb-4 line-clamp-2 min-h-10">
                                    @if($room->description)
                                        {{ $room->description }}
                                    @else
                                        Kamar dengan fasilitas lengkap untuk kenyamanan maksimal Anda.
                                    @endif
                                </p>
                                
                                <div class="flex flex-wrap gap-2 mb-6 min-h-8">
                                    @php
                                        $facilitiesData = $room->facilities;
                                        if (is_string($facilitiesData)) {
                                            $facilitiesData = json_decode($facilitiesData, true);
                                        }
                                        $allIcons = config('facilities.icons');
                                    @endphp
                                    @if($facilitiesData && is_array($facilitiesData) && count($facilitiesData) > 0)
                                        @foreach($facilitiesData as $facility)
                                            @php
                                                $name = is_array($facility) ? ($facility['name'] ?? '') : $facility;
                                                $iconKey = is_array($facility) ? ($facility['icon'] ?? 'check') : 'check';
                                                $svg = $allIcons[$iconKey]['svg'] ?? '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                                                
                                                // If legacy string, try to map known names to icons
                                                if (!is_array($facility)) {
                                                     foreach($allIcons as $key => $val) {
                                                         if (stripos($facility, $val['name']) !== false) {
                                                             $svg = $val['svg'];
                                                             break;
                                                         }
                                                     }
                                                }
                                            @endphp
                                            <span class="text-xs bg-emerald-50/50 text-emerald-800 border border-emerald-100/50 px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                                                <span class="w-3 h-3 text-emerald-600 flex items-center justify-center">
                                                    {!! str_replace('w-5 h-5', 'w-3 h-3', $svg) !!}
                                                </span>
                                                {{ $name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-xs bg-emerald-50/50 text-emerald-800 border border-emerald-100/50 px-2.5 py-1 rounded-lg">Fasilitas Lengkap</span>
                                    @endif
                                </div>

                                @if($room->available_rooms_count > 0)
                                    <a href="{{ Auth::check() ? route('tenant.room.detail', $room->id) : route('login') }}" class="block w-full bg-white border border-emerald-600 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white text-center font-bold py-3.5 rounded-xl transition duration-300 mt-auto shadow-md group-hover:shadow-emerald-200">
                                        Pesan Sekarang
                                    </a>
                                @else
                                    <button disabled class="block w-full bg-gray-100 text-gray-400 text-center font-bold py-3.5 rounded-xl cursor-not-allowed mt-auto border border-gray-200">
                                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Tidak Tersedia
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="w-full text-center py-12 premium-card-interactive rounded-3xl p-8 max-w-lg mx-auto">
                            <p class="text-gray-500 text-lg">Belum ada tipe kamar yang tersedia. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                        </div>
                    @endforelse
                @else
                    <div class="w-full text-center py-12 premium-card-interactive rounded-3xl p-8 max-w-lg mx-auto">
                        <p class="text-gray-500 text-lg">Belum ada tipe kamar yang tersedia. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                    </div>
                @endif

            </div>

            <div class="relative z-10 bg-gradient-to-r from-emerald-600 to-emerald-800 border border-emerald-500 rounded-3xl p-8 lg:p-12 mt-20 text-center text-white shadow-xl shadow-emerald-900/10 overflow-hidden group scroll-reveal">
                {{-- Mini geometric design inside the promo --}}
                <div class="absolute -right-16 -bottom-16 w-48 h-48 bg-emerald-500/20 rounded-full blur-xl group-hover:scale-125 transition duration-500"></div>
                <div class="absolute -left-16 -top-16 w-48 h-48 bg-teal-500/20 rounded-full blur-xl group-hover:scale-125 transition duration-500"></div>
                
                <p class="text-white/90 font-medium text-lg lg:text-xl mb-8 relative z-10 max-w-2xl mx-auto">Daftar akun sekarang untuk melakukan reservasi online, cek tagihan bulanan, dan nikmati kemudahan hidup di hunian Mutiara27.</p>
                <a href="{{ route('register') }}" class="inline-block bg-white hover:bg-emerald-50 text-emerald-800 font-extrabold py-4 px-10 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 relative z-10">
                    Buat Akun Penyewa
                </a>
            </div>
        </div>
    </div>

    <!-- Maps & Kontak Section -->
    <div id="kontak" class="relative scroll-mt-16 py-16 md:py-24 bg-gradient-to-b from-emerald-50/40 via-white to-white overflow-hidden grid-pattern">
        {{-- Floating ambient blobs --}}
        <div class="absolute top-1/4 left-1/4 w-96 h-96 ambient-blob ambient-blob-cyan"></div>
        <div class="absolute bottom-10 right-5 w-80 h-80 ambient-blob ambient-blob-emerald"></div>
        {{-- Aksen dekoratif (elemen berat hanya tampil di layar >= md) --}}
        <div class="particle particle-1"></div>
        <div class="particle particle-5"></div>
        <div class="hidden md:block particle particle-3"></div>
        <div class="hidden md:block particle particle-7"></div>
        <div class="hidden md:block absolute top-1/2 left-1/3 w-72 h-72 ambient-blob ambient-blob-cyan"></div>
        <div class="hidden md:block geo-shape geo-shape-1"></div>
        <div class="hidden md:block geo-shape geo-shape-3"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-8 sm:mb-16 scroll-reveal">
                <div class="section-title-container">
                    <h2 class="text-2xl sm:text-3xl lg:text-5xl font-extrabold text-gray-900 tracking-tight">Lokasi & Kontak</h2>
                    <span class="section-title-line"></span>
                </div>
                <p class="text-gray-500 mt-3 sm:mt-6 text-sm sm:text-lg max-w-2xl mx-auto">Temukan kami dengan mudah dan hubungi untuk informasi lebih lanjut</p>
            </div>

            @php
                $mapsEmbed = $contacts->firstWhere('contact_type', 'maps_embed');
                $addressContact = $contacts->firstWhere('contact_type', 'address');
                $phoneContact = $contacts->firstWhere('contact_type', 'phone');
                $emailContact = $contacts->firstWhere('contact_type', 'email');
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                <!-- Maps Column -->
                <div class="lg:col-span-2 flex flex-col justify-between scroll-reveal-left">
                    <div class="rounded-3xl overflow-hidden map-glass-card h-96 border-4 border-white p-2 flex-grow shadow-lg">
                        <div class="rounded-2xl overflow-hidden h-full">
                            @if($mapsEmbed && $mapsEmbed->contact_value)
                                @php
                                    $mapsValue = trim($mapsEmbed->contact_value);
                                    $extractedSrc = null;

                                    if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $mapsValue, $m)) {
                                        $extractedSrc = $m[1];
                                    } elseif (filter_var($mapsValue, FILTER_VALIDATE_URL)) {
                                        $extractedSrc = $mapsValue;
                                    }

                                    $host = $extractedSrc ? parse_url($extractedSrc, PHP_URL_HOST) : null;
                                    $isSafeMapsHost = $host && (
                                        $host === 'www.google.com'
                                        || $host === 'maps.google.com'
                                        || str_ends_with($host, '.google.com')
                                    );
                                    $safeMapsSrc = $isSafeMapsHost ? $extractedSrc : null;
                                @endphp
                                @if($safeMapsSrc)
                                    <iframe src="{{ $safeMapsSrc }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                                @else
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.1280774714837!2d110.35100217523436!3d-6.9941932930069335!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708b834b8a199f%3A0x526c7bef91205552!2sKost%20Putri%20Mutiara27!5e0!3m2!1sid!2sid!4v1770413208018!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                                @endif
                            @else
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.1280774714837!2d110.35100217523436!3d-6.9941932930069335!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708b834b8a199f%3A0x526c7bef91205552!2sKost%20Putri%20Mutiara27!5e0!3m2!1sid!2sid!4v1770413208018!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between px-2">
                        <a href="https://maps.app.goo.gl/CvtvJTgDTTEQraHP6" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-bold transition transform hover:translate-x-1 duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Buka di Google Maps
                        </a>
                    </div>
                </div>

                <!-- Kontak Column -->
                <div class="scroll-reveal-right flex flex-col">
                    <div class="map-glass-card rounded-3xl p-8 border border-white flex-grow flex flex-col justify-between shadow-lg relative overflow-hidden group">
                        {{-- Decorative gradient background subtle light --}}
                        <div class="absolute -right-16 -top-16 w-32 h-32 bg-emerald-500/10 rounded-full blur-xl group-hover:scale-110 transition duration-500"></div>

                        <div class="space-y-8 relative z-10">
                            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-8">Hubungi Kami!</h2>

                            <!-- Lokasi -->
                            @if($addressContact)
                            <div class="flex gap-4 group/item">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm group-hover/item:bg-emerald-600 group-hover/item:text-white transition duration-300">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $addressContact->label ?? 'Lokasi' }}</p>
                                    <p class="text-base text-gray-800 font-semibold mt-0.5 leading-relaxed">{{ $addressContact->contact_value }}</p>
                                </div>
                            </div>
                            @else
                            <div class="flex gap-4 group/item">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm group-hover/item:bg-emerald-600 group-hover/item:text-white transition duration-300">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Lokasi</p>
                                    <p class="text-base text-gray-800 font-semibold mt-0.5 leading-relaxed">BPI Blok S No.29A</p>
                                </div>
                            </div>
                            @endif

                            <!-- Telepon/WhatsApp -->
                            @if($phoneContact)
                            @php
                                $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneContact->contact_value);
                                $waNumber = preg_replace('/^0/', '62', $phoneNumber);
                                $waNumber = preg_replace('/^\+/', '', $waNumber);
                            @endphp
                            <div class="flex gap-4 group/item">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm group-hover/item:bg-emerald-600 group-hover/item:text-white transition duration-300">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.481 0 1.461 1.063 2.875 1.211 3.074.149.198 2.095 3.2 5.076 4.487 2.982 1.288 2.982.859 3.526.809.544-.05 1.758-.718 2.006-1.413.248-.695.248-1.29.173-1.414z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $phoneContact->label ?? 'WhatsApp' }}</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="text-base sm:text-lg text-emerald-600 hover:text-emerald-700 font-bold transition">
                                            {{ $phoneContact->contact_value }}
                                        </a>
                                        {{-- Popup kecil "Klik untuk chat" di samping nomor --}}
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer"
                                           class="relative inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold px-3 py-1 rounded-full shadow-md animate-chat-nudge whitespace-nowrap">
                                            <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-emerald-600 rotate-45"></span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.84L3 20l1.05-3.15A7.6 7.6 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            Klik untuk chat
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="flex gap-4 group/item">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm group-hover/item:bg-emerald-600 group-hover/item:text-white transition duration-300">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.481 0 1.461 1.063 2.875 1.211 3.074.149.198 2.095 3.2 5.076 4.487 2.982 1.288 2.982.859 3.526.809.544-.05 1.758-.718 2.006-1.413.248-.695.248-1.29.173-1.414z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">WhatsApp</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                        <a href="https://wa.me/628112702889" target="_blank" rel="noopener noreferrer" class="text-base sm:text-lg text-emerald-600 hover:text-emerald-700 font-bold transition">
                                            0811 2702 889
                                        </a>
                                        {{-- Popup kecil "Klik untuk chat" di samping nomor --}}
                                        <a href="https://wa.me/628112702889" target="_blank" rel="noopener noreferrer"
                                           class="relative inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold px-3 py-1 rounded-full shadow-md animate-chat-nudge whitespace-nowrap">
                                            <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-emerald-600 rotate-45"></span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.84L3 20l1.05-3.15A7.6 7.6 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            Klik untuk chat
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Email -->
                            @if($emailContact)
                            <div class="flex gap-4 group/item">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm group-hover/item:bg-emerald-600 group-hover/item:text-white transition duration-300">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $emailContact->label ?? 'Email' }}</p>
                                    <a href="mailto:{{ $emailContact->contact_value }}" class="text-base text-emerald-600 hover:text-emerald-700 font-bold transition mt-0.5 block hover:translate-x-1 duration-300">
                                        {{ $emailContact->contact_value }}
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Disclaimer -->
                        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-2xl mt-8 relative z-10">
                            <p class="text-sm text-amber-900 leading-relaxed">
                                <strong>⚠️ Penting:</strong> Jika ingin survey langsung ke lokasi, harap membuat janji temu terlebih dahulu melalui nomor WhatsApp kami.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lightbox galeri: foto besar + navigasi (tombol, keyboard, geser/swipe) --}}
    <div id="galleryLightbox" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/90 backdrop-blur-sm select-none">
        <button type="button" data-close class="absolute top-4 right-4 z-20 text-white/80 hover:text-white transition" aria-label="Tutup">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <button type="button" data-prev class="lightbox-nav absolute left-2 sm:left-5 top-1/2 -translate-y-1/2 z-20 bg-white/15 hover:bg-white/30 text-white rounded-full p-2 sm:p-3 backdrop-blur-sm transition" aria-label="Sebelumnya">
            <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button type="button" data-next class="lightbox-nav absolute right-2 sm:right-5 top-1/2 -translate-y-1/2 z-20 bg-white/15 hover:bg-white/30 text-white rounded-full p-2 sm:p-3 backdrop-blur-sm transition" aria-label="Berikutnya">
            <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>

        <img id="galleryLightboxImg" src="" alt="Foto galeri" class="max-w-[92vw] max-h-[82vh] rounded-2xl shadow-2xl object-contain">

        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 text-white/85 text-xs sm:text-sm bg-black/40 rounded-full px-3 py-1">
            <span id="galleryLightboxCounter">1 / 1</span>
        </div>
    </div>

    <footer class="bg-emerald-950 py-8 border-t border-emerald-900">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm text-emerald-400">© {{ date('Y') }} Mutiara27. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Gallery Lightbox — klik foto untuk lihat besar, lalu bisa geser ke foto lain
        (function () {
            const lightbox = document.getElementById('galleryLightbox');
            const lightboxImg = document.getElementById('galleryLightboxImg');
            const counter = document.getElementById('galleryLightboxCounter');
            if (!lightbox || !lightboxImg) return;

            const photos = Array.from(document.querySelectorAll('.gallery-photo'));
            const sources = photos.map((img) => img.currentSrc || img.src);
            if (sources.length === 0) return;

            let current = 0;
            const single = sources.length <= 1;
            if (single) {
                lightbox.querySelectorAll('.lightbox-nav').forEach((b) => b.classList.add('hidden'));
            }

            const render = () => {
                lightboxImg.src = sources[current];
                if (counter) counter.textContent = (current + 1) + ' / ' + sources.length;
            };
            const open = (i) => {
                current = i;
                render();
                lightbox.classList.remove('hidden');
                lightbox.classList.add('flex');
                document.body.style.overflow = 'hidden';
            };
            const close = () => {
                lightbox.classList.add('hidden');
                lightbox.classList.remove('flex');
                document.body.style.overflow = '';
                lightboxImg.src = '';
            };
            const next = () => { current = (current + 1) % sources.length; render(); };
            const prev = () => { current = (current - 1 + sources.length) % sources.length; render(); };

            photos.forEach((img, i) => {
                img.addEventListener('click', (e) => { e.stopPropagation(); open(i); });
            });

            lightbox.addEventListener('click', (e) => {
                if (e.target.closest('[data-prev]')) { prev(); return; }
                if (e.target.closest('[data-next]')) { next(); return; }
                // tutup saat klik area gelap atau tombol close (bukan saat klik gambarnya)
                if (e.target === lightbox || e.target.closest('[data-close]')) close();
            });

            document.addEventListener('keydown', (e) => {
                if (lightbox.classList.contains('hidden')) return;
                if (e.key === 'Escape') close();
                else if (e.key === 'ArrowRight') next();
                else if (e.key === 'ArrowLeft') prev();
            });

            // Geser (swipe) di layar sentuh
            let startX = null;
            lightbox.addEventListener('touchstart', (e) => { startX = e.changedTouches[0].clientX; }, { passive: true });
            lightbox.addEventListener('touchend', (e) => {
                if (startX === null || single) { startX = null; return; }
                const dx = e.changedTouches[0].clientX - startX;
                if (Math.abs(dx) > 40) { dx < 0 ? next() : prev(); }
                startX = null;
            }, { passive: true });
        })();

        // Gallery Carousel Functionality
        document.querySelectorAll('.group').forEach(group => {
            const carousel = group.querySelector('.gallery-carousel');
            const nextBtn = group.querySelector('.carousel-next');
            const prevBtn = group.querySelector('.carousel-prev');
            
            if (carousel && nextBtn && prevBtn) {
                let currentIndex = 0;
                const images = carousel.querySelectorAll('img');
                const totalImages = images.length;
                
                const updateCarousel = () => {
                    const translateValue = -currentIndex * 100;
                    carousel.style.transform = `translateX(${translateValue}%)`;
                };
                
                nextBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    currentIndex = (currentIndex + 1) % totalImages;
                    updateCarousel();
                });
                
                prevBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    currentIndex = (currentIndex - 1 + totalImages) % totalImages;
                    updateCarousel();
                });
            }
        });

        // Scroll Reveal — IntersectionObserver
        (function() {
            const revealSelectors = '.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right, .scroll-reveal-scale';
            const revealEls = document.querySelectorAll(revealSelectors);

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('revealed');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

                revealEls.forEach(el => observer.observe(el));
            } else {
                // Fallback: just show everything
                revealEls.forEach(el => el.classList.add('revealed'));
            }
        })();

        // Navbar shadow on scroll
        (function() {
            const nav = document.querySelector('nav');
            if (!nav) return;
            let ticking = false;
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        if (window.scrollY > 20) {
                            nav.classList.add('nav-scrolled');
                        } else {
                            nav.classList.remove('nav-scrolled');
                        }
                        ticking = false;
                    });
                    ticking = true;
                }
            });
        })();
    </script>

</body>
</html>