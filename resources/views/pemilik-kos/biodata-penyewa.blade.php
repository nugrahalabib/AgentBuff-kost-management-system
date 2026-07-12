@extends('layouts.pemilik-kos')

@section('title', 'Detail Biodata Penyewa')

@section('content')
    <div class="flex flex-col gap-6">
        @php
            $documents = $penyewa->tenantProfile?->documents ?? [];

            // Format Phone for WA (Replace 0 with 62)
            $phone = $penyewa->tenantProfile?->phone ?? '';
            $waPhone = null;
            if ($phone) {
                $cleaned = preg_replace('/[^0-9]/', '', $phone);
                if (str_starts_with($cleaned, '0')) {
                    $waPhone = '62' . substr($cleaned, 1);
                } else {
                    $waPhone = $cleaned;
                }
            }
        @endphp
        <!-- Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex flex-wrap gap-2 items-center">
                    @if(request('from') == 'kamar')
                        <a href="{{ route('owner.kamar') }}" class="text-gray-500 hover:text-emerald-600 transition">Data
                            Kamar</a>
                    @else
                        <a href="{{ route('owner.penyewa') }}" class="text-gray-500 hover:text-emerald-600 transition">Data
                            Penyewa</a>
                    @endif
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900 font-semibold">Detail Biodata</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $penyewa->name }}</h1>
            </div>

            <div class="flex gap-3">
                @if($waPhone)
                    <a href="https://wa.me/{{ $waPhone }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.481 0 1.461 1.063 2.875 1.211 3.074.149.198 2.095 3.2 5.076 4.487 2.982 1.288 2.982.859 3.526.809.544-.05 1.758-.718 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                        </svg>
                        Hubungi Penyewa
                    </a>
                @endif

                @if($penyewa->activeRoom)
                    <form action="{{ route('owner.penyewa.checkout', $penyewa->id) }}" method="POST"
                        onsubmit="return confirm('Checkout {{ $penyewa->name }} dari kamarnya?');">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 transition shadow-sm">
                            Checkout
                        </button>
                    </form>
                @else
                    <a href="{{ route('owner.verifikasi-transaksi', ['penyewa' => $penyewa->id, 'add' => 1]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">
                        Tempatkan ke Kamar
                    </a>
                @endif
            </div>
        </div>

        <main class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Primary Profile -->
            <div class="space-y-6">
                <!-- Main Profile Card -->
                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col items-center text-center">
                    <div class="relative mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($penyewa->name) }}&background=059669&color=fff&size=128"
                            alt="Profile" class="w-32 h-32 rounded-full object-cover border-4 border-emerald-50">
                        {{-- Verification badge disabled --}}
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $penyewa->name }}</h2>
                    <p class="text-sm text-gray-500 mb-4">{{ $penyewa->email }}</p>

                    <div class="flex flex-col w-full gap-2 border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-start text-sm">
                            <span class="text-gray-500 mt-1">Status Akun</span>
                            <div class="flex flex-col items-end gap-1">
                                @php
                                    $isNew = $penyewa->created_at->diffInDays(now()) <= 30;
                                    $isActive = $penyewa->activeRoom !== null;
                                @endphp

                                @if($isNew)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                        New
                                    </span>
                                @endif

                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-bold {{ $isActive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $isActive ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Bergabung</span>
                            <span class="font-medium text-gray-900">{{ $penyewa->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Personal Info -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Data Pribadi
                    </h3>

                    <div class="space-y-4">
                        {{-- Basic Contact --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Kontak</span>
                                <div class="text-gray-900 font-medium mt-1">{{ $penyewa->tenantProfile?->phone ?? '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- Identity Numbers --}}
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-50">
                            <div class="col-span-2">
                                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">NIK (KTP)</span>
                                <div class="text-gray-900 font-medium mt-1 font-mono tracking-wide">
                                    {{ $penyewa->tenantProfile?->id_card_number ?? '-' }}</div>
                            </div>
                            @if($penyewa->tenantProfile?->student_card_number)
                                <div class="col-span-2">
                                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">NIM
                                        (Mahasiswa)</span>
                                    <div class="text-gray-900 font-medium mt-1 font-mono tracking-wide">
                                        {{ $penyewa->tenantProfile->student_card_number }}</div>
                                </div>
                            @endif
                        </div>

                        {{-- Birth & Personal Details --}}
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-50">
                            <div class="col-span-2">
                                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">TTL</span>
                                <div class="text-gray-900 mt-1 text-sm">
                                    {{ $penyewa->tenantProfile?->birth_place ?? '-' }}, <br>
                                    {{ $penyewa->tenantProfile?->birth_date ? \Carbon\Carbon::parse($penyewa->tenantProfile->birth_date)->format('d M Y') : '-' }}
                                </div>
                            </div>

                            {{-- Student vs Non-Student Logic --}}
                            @if($penyewa->tenantProfile?->university)
                                {{-- Show Campus Data for Students --}}
                                <div class="col-span-2">
                                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Data Kampus</span>
                                    <div class="text-gray-900 mt-1 text-sm">
                                        <div class="font-bold text-emerald-600">{{ $penyewa->tenantProfile?->university }}</div>
                                        <div class="mt-0.5">
                                            {{ $penyewa->tenantProfile?->faculty ? $penyewa->tenantProfile?->faculty . ' - ' : '' }}
                                            {{ $penyewa->tenantProfile?->major }}</div>
                                        <div class="text-gray-500 text-xs mt-0.5">Angkatan
                                            {{ $penyewa->tenantProfile?->enrollment_year }}</div>
                                    </div>
                                </div>
                            @else
                                {{-- Show Occupation for Non-Students --}}
                                <div class="col-span-2">
                                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Pekerjaan</span>
                                    <div class="text-gray-900 font-medium mt-1 text-sm">
                                        {{ $penyewa->tenantProfile?->occupation ?? '-' }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-gray-50">
                            <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Alamat Asal</span>
                            <p class="text-gray-600 mt-1 text-sm leading-relaxed">
                                {{ $penyewa->tenantProfile?->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle & Right: Details + Activity -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Room Status & Emergency Contact -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Room Info -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col h-full">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            Kamar Saat Ini
                        </h3>

                        @if($penyewa->activeRoom)
                            @php
                                $room = $penyewa->activeRoom;

                                // FIX: Get dates specific to THIS tenant, not the shared room data
                                // 1. End Date: Latest verified transaction (Contract)
                                $latestTx = $penyewa->tenantTransactions->sortByDesc('period_end_date')->first();
                                $endDate = $latestTx ? $latestTx->period_end_date : null;

                                // 2. Start Date: Pivot Check-in ONLY (Physical Check-in)
                                $pivot = \DB::table('riwayat_penghuni_kamar')
                                    ->where('kamar_id', $room->id)
                                    ->where('user_id', $penyewa->id)
                                    ->first();

                                $startDate = $pivot?->check_in_date;

                                $daysRemaining = $endDate ? (int) now()->startOfDay()->diffInDays($endDate, false) : null;

                                // Recalculate status flags based on Tenant specific dates
                                $isExpiringSoon = $daysRemaining !== null && $daysRemaining <= 14 && $daysRemaining > 0;
                                $isExpired = $daysRemaining !== null && $daysRemaining <= 0;
                            @endphp

                            <div
                                class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-4 border border-emerald-100 flex-1">
                                {{-- Top Part: Image & Main Info --}}
                                <div class="flex flex-col sm:flex-row gap-4 items-start">
                                    {{-- Room Image --}}
                                    @if($room->roomType && $room->roomType->image_path)
                                        <img src="{{ Storage::url($room->roomType->image_path) }}" alt="Room"
                                            class="w-full sm:w-20 h-20 object-cover rounded-lg shadow-sm">
                                    @else
                                        <div
                                            class="w-full sm:w-20 h-20 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="flex-1 w-full">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-bold text-gray-900 leading-tight">
                                                    {{ $room->roomType->name ?? 'Tipe Standar' }}</h4>
                                                <p class="text-sm text-gray-500 mt-0.5">Kamar <span
                                                        class="font-bold text-emerald-600 font-mono">{{ $room->room_number }}</span>
                                                </p>
                                            </div>
                                            <span
                                                class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Aktif</span>
                                        </div>

                                        {{-- Mini Grid --}}
                                        <div class="grid grid-cols-2 gap-2 mt-3">
                                            <div class="bg-white/60 rounded p-1.5 border border-emerald-50/50">
                                                <p class="text-[10px] text-gray-400 uppercase">Lantai</p>
                                                <p class="text-xs font-semibold text-gray-700">{{ $room->floor_number }}</p>
                                            </div>
                                            <div class="bg-white/60 rounded p-1.5 border border-emerald-50/50">
                                                <p class="text-[10px] text-gray-400 uppercase">Harga</p>
                                                <p class="text-xs font-semibold text-gray-700">Rp
                                                    {{ number_format($room->roomType->rent_per_person ?? 0, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Bottom Part: Lease Info --}}
                                @if($startDate || $endDate)
                                    <div
                                        class="mt-4 {{ $isExpired ? 'bg-red-50/80 border-red-100' : ($isExpiringSoon ? 'bg-yellow-50/80 border-yellow-100' : 'bg-white/50 border-emerald-100') }} rounded-lg p-3 border">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 {{ $isExpired ? 'text-red-500' : ($isExpiringSoon ? 'text-yellow-500' : 'text-emerald-500') }}"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                <span
                                                    class="text-[10px] font-bold uppercase {{ $isExpired ? 'text-red-700' : 'text-gray-500' }}">Periode
                                                    Sewa</span>
                                            </div>
                                            @if($daysRemaining !== null)
                                                <span
                                                    class="text-[10px] font-bold {{ $isExpired ? 'text-red-600' : ($isExpiringSoon ? 'text-yellow-600' : 'text-emerald-600') }}">
                                                    {{ $isExpired ? 'Berakhir' : ($isExpiringSoon ? $daysRemaining . ' hari lagi' : 'Aktif') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex justify-between text-xs">
                                            <div>
                                                <span class="text-[9px] text-gray-400 block uppercase">Mulai</span>
                                                <span
                                                    class="font-medium text-gray-700">{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : '-' }}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-[9px] text-gray-400 block uppercase">Selesai</span>
                                                <span
                                                    class="font-medium text-gray-700">{{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-4 bg-white/50 border border-dashed border-gray-200 rounded-lg p-3 text-center">
                                        <p class="text-xs text-gray-400 italic">Tanggal sewa belum diatur</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div
                                class="flex flex-col items-center justify-center py-8 text-center h-full border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                                <div
                                    class="w-12 h-12 bg-white rounded-full flex items-center justify-center mb-2 shadow-sm border border-gray-100">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-gray-400 text-sm font-medium">Belum menempati kamar</p>
                            </div>
                        @endif
                    </div>

                    <!-- Emergency Contact -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Wali / Kontak Darurat
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Nama Wali</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $penyewa->tenantProfile?->guardian_name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">No. HP</p>
                                <p class="text-sm text-gray-900">{{ $penyewa->tenantProfile?->guardian_phone ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Telp Rumah</p>
                                <p class="text-sm text-gray-900">{{ $penyewa->tenantProfile?->guardian_home_phone ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Pekerjaan</p>
                                <p class="text-sm text-gray-900 truncate">
                                    {{ $penyewa->tenantProfile?->guardian_occupation ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">NIK Wali</p>
                                <p class="text-sm text-gray-900 truncate">
                                    {{ $penyewa->tenantProfile?->guardian_id_card_number ?? '-' }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Alamat Wali</p>
                                <p class="text-sm text-gray-600 leading-snug">
                                    {{ $penyewa->tenantProfile?->guardian_address ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Dokumen & Identitas (File)
                    </h3>

                    @php
                        // $documents is already defined at the top
                        $docTypes = [
                            'ktp' => ['label' => 'KTP', 'icon' => '🪪'],
                            'kartu_mahasiswa' => ['label' => 'Kartu Mahasiswa', 'icon' => '🎓'],
                            'ktp_ortu' => ['label' => 'KTP Orang Tua', 'icon' => '👨‍👩‍👧'],
                            'kartu_keluarga' => ['label' => 'Kartu Keluarga', 'icon' => '👨‍👩‍👧‍👦'],
                            'pas_foto' => ['label' => 'Pas Foto 3x4', 'icon' => '📷'],
                            'surat_pernyataan' => ['label' => 'Surat Pernyataan', 'icon' => '📄']
                        ];

                        // Get KTP/KTM paths specifically if stored in main columns, merge into documents array for display loop if needed or handle separately
                        if ($penyewa->tenantProfile?->id_card_photo_path)
                            $documents['ktp'] = $penyewa->tenantProfile->id_card_photo_path;
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($docTypes as $key => $doc)
                            {{-- Hide Kartu Mahasiswa if not student (no university data) --}}
                            @if($key === 'kartu_mahasiswa' && !$penyewa->tenantProfile?->university)
                                @continue
                            @endif

                            @if(isset($documents[$key]) || ($key === 'ktp' && $penyewa->tenantProfile?->id_card_photo_path))
                                @php
                                    $filePath = ($key === 'ktp' && $penyewa->tenantProfile?->id_card_photo_path) ? $penyewa->tenantProfile->id_card_photo_path : $documents[$key];
                                    $docUrl = route('tenant-document.view', ['user' => $penyewa->id, 'type' => $key]);
                                @endphp
                                <div
                                    class="p-3 rounded-xl border border-emerald-100 bg-emerald-50/30 hover:shadow-sm transition flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl group-hover:scale-110 transition">{{ $doc['icon'] }}</span>
                                        <div>
                                            <p class="text-xs font-bold text-gray-700">{{ $doc['label'] }}</p>
                                            <span
                                                class="text-[10px] text-emerald-600 font-medium bg-emerald-100 px-1.5 py-0.5 rounded">Ada
                                                File</span>
                                        </div>
                                    </div>
                                    <a href="{{ $docUrl }}" target="_blank"
                                        class="p-2 text-gray-400 hover:text-emerald-600 transition" title="Lihat">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            @else
                                <div class="p-3 rounded-xl border border-gray-100 bg-gray-50 flex items-center gap-3 opacity-60">
                                    <span class="text-xl grayscale">{{ $doc['icon'] }}</span>
                                    <div>
                                        <p class="text-xs font-bold text-gray-500">{{ $doc['label'] }}</p>
                                        <span class="text-[10px] text-gray-400">Belum upload</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Histori Pembayaran
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="py-3 px-2 font-medium text-gray-500 uppercase text-xs">Tanggal</th>
                                    <th class="py-3 px-2 font-medium text-gray-500 uppercase text-xs">Periode</th>
                                    <th class="py-3 px-2 font-medium text-gray-500 uppercase text-xs">Jumlah</th>
                                    <th class="py-3 px-2 font-medium text-gray-500 uppercase text-xs">Status</th>
                                    <th class="py-3 px-2 font-medium text-gray-500 uppercase text-xs">Bukti</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($penyewa->tenantTransactions->sortByDesc('created_at') as $tx)
                                    <tr>
                                        <td class="py-3 px-2 text-gray-900">{{ $tx->created_at->format('d M Y') }}</td>
                                        <td class="py-3 px-2 text-gray-600">
                                            @if($tx->period_start_date)
                                                {{ \Carbon\Carbon::parse($tx->period_start_date)->translatedFormat('M Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 font-medium text-gray-900">Rp
                                            {{ number_format($tx->amount, 0, ',', '.') }}</td>
                                        <td class="py-3 px-2">
                                            @if($tx->status == 'verified_by_owner' || $tx->status == 'verified_by_admin')
                                                <span
                                                    class="inline-flex px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700">Lunas</span>
                                            @elseif($tx->status == 'pending_verification')
                                                <span
                                                    class="inline-flex px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-700">Verifikasi</span>
                                            @elseif($tx->status == 'rejected_by_owner' || $tx->status == 'rejected_by_admin')
                                                <span
                                                    class="inline-flex px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700">Ditolak</span>
                                            @else
                                                <span
                                                    class="inline-flex px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-600">{{ $tx->status }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2">
                                            @php($proof = $tx->paymentProofs->first())
                                            @if($proof)
                                                <a href="{{ route('payment-proof.view', $proof->id) }}" target="_blank"
                                                    class="text-emerald-600 hover:text-emerald-700 text-xs font-semibold">Lihat Bukti</a>
                                            @else
                                                <span class="text-gray-300 text-xs">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-gray-400 text-sm">Belum ada riwayat
                                            transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection