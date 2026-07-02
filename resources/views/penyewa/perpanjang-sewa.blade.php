<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perpanjang Sewa - Mutiara27</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50">

    @include('partials.navbar-welcome')

    <div class="min-h-screen pb-20 pt-20">

        <!-- Header -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            @if($extendBooking)
                <a href="{{ route('tenant.extend-payment.cancel') }}"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-emerald-600 transition mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Batalkan & Kembali ke Dashboard
                </a>
            @else
                <a href="{{ route('tenant.dashboard') }}"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-emerald-600 transition mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            @endif
            <h1 class="text-2xl font-bold text-gray-900">Perpanjang Sewa Kamar</h1>
            <p class="text-sm text-gray-500 mt-1">Tambah durasi sewa untuk kamar Anda saat ini.</p>
        </div>

        <!-- Alert Messages -->
        @if(session('info'))
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-blue-700">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @php
            $pricePerMonth = $kamar->roomType->rent_per_person ?? 0;
            $currentLeaseEnd = $kamar->lease_end_date ? $kamar->lease_end_date->format('Y-m-d') : now()->format('Y-m-d');
            $currentLeaseEndFormatted = $kamar->lease_end_date ? $kamar->lease_end_date->format('d M Y') : now()->format('d M Y');
        @endphp

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                @if(!$extendBooking)
                    <!-- Left Column - Room Info & Duration Selection (only for duration selection) -->
                    <div class="lg:col-span-3 space-y-6">

                        <!-- Current Room Card -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                                <div class="bg-emerald-100 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                </div>
                                <h2 class="text-lg font-bold text-gray-800">Kamar Anda Saat Ini</h2>
                            </div>

                            <div class="flex gap-4">
                                @if($kamar->roomType && $kamar->roomType->image_path)
                                    <img src="{{ Storage::url($kamar->roomType->image_path) }}" alt="{{ $kamar->roomType->name }}"
                                        class="w-28 h-28 object-cover rounded-xl shadow">
                                @else
                                    <div class="w-28 h-28 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-xl font-bold text-gray-800">{{ $kamar->roomType->name ?? 'Kamar' }}
                                        </h3>
                                        <span
                                            class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-0.5 rounded-full">Aktif</span>
                                    </div>
                                    <p class="text-gray-500 text-sm mb-3">Nomor Kamar: <span
                                            class="font-semibold text-gray-700">{{ $kamar->room_number }}</span></p>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <p class="text-[10px] text-gray-400 uppercase">Lantai</p>
                                            <p class="text-sm font-semibold text-gray-700">{{ $kamar->floor_number ?? '-' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 uppercase">Harga/Bulan</p>
                                            <p class="text-sm font-semibold text-emerald-600">Rp
                                                {{ number_format($pricePerMonth, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 uppercase">Sewa Sampai</p>
                                            <p class="text-sm font-semibold text-gray-700">{{ $currentLeaseEndFormatted }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Duration Selection Form -->
                        <form action="{{ route('tenant.extend-payment.store') }}" method="POST" id="duration-form">
                            @csrf
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                                    <div class="bg-blue-100 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h2 class="text-lg font-bold text-gray-800">Pilih Durasi Perpanjangan</h2>
                                </div>

                                <!-- Quick Select Buttons -->
                                <p class="text-sm text-gray-500 mb-3">Pilih durasi cepat:</p>
                                <div class="grid grid-cols-4 gap-2 mb-4">
                                    @foreach([1, 3, 6, 12] as $months)
                                        <button type="button" onclick="setDuration({{ $months }})"
                                            class="duration-btn p-3 border-2 rounded-xl text-center transition hover:border-emerald-400 {{ $months == 1 ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200' }}">
                                            <p class="text-lg font-bold text-gray-800">
                                                {{ $months == 12 ? '1 Thn' : $months . ' Bln' }}</p>
                                        </button>
                                    @endforeach
                                </div>

                                <!-- Custom Duration Input -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Atau masukkan durasi
                                        manual:</label>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 relative">
                                            <input type="number" name="duration" id="duration-input" min="1" max="24"
                                                value="1"
                                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-lg font-bold text-center focus:border-emerald-500 focus:ring-emerald-500 transition"
                                                onchange="updateCalculation()" oninput="updateCalculation()">
                                        </div>
                                        <span class="text-gray-600 font-semibold">Bulan</span>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2">Minimal 1 bulan, maksimal 24 bulan</p>
                                </div>

                                <button type="submit"
                                    class="w-full mt-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-200 transition flex items-center justify-center gap-2">
                                    Lanjut ke Pembayaran
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- End: Left Column for Duration Selection -->
                @else
                    <!-- Full Width Payment Section (takes all 5 columns) -->
                    <div class="lg:col-span-5 space-y-6">

                        <!-- Rincian Perpanjangan Card -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                                <div class="bg-emerald-100 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                </div>
                                <h2 class="text-lg font-bold text-gray-800">Rincian Perpanjangan Sewa</h2>
                                <span
                                    class="ml-auto font-mono text-xs text-gray-400">{{ $extendBooking['invoice_number'] }}</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Info Kamar -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">Kamar</p>
                                    <p class="text-lg font-bold text-gray-800">{{ $extendBooking['room_type_name'] }}</p>
                                    <p class="text-sm text-gray-500">Nomor {{ $extendBooking['room_number'] }}</p>
                                </div>

                                <!-- Periode Sewa -->
                                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                    <p class="text-xs font-bold text-blue-600 uppercase mb-2">📅 Proyeksi Sewa</p>
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="text-center">
                                            <p class="text-[10px] text-gray-400">SAAT INI</p>
                                            <p class="text-sm font-bold text-gray-700">{{ $currentLeaseEndFormatted }}</p>
                                        </div>
                                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                        <div class="text-center">
                                            <p class="text-[10px] text-gray-400">SETELAH</p>
                                            @php
                                                $newEndDate = \Carbon\Carbon::parse($currentLeaseEnd)->addMonths((int) $extendBooking['duration']);
                                            @endphp
                                            <p class="text-sm font-bold text-blue-600">{{ $newEndDate->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Durasi & Harga -->
                                <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                                    <p class="text-xs font-bold text-emerald-600 uppercase mb-2">Perpanjangan</p>
                                    <p class="text-lg font-bold text-gray-800">{{ $extendBooking['duration'] }} Bulan</p>
                                    <p class="text-sm text-gray-500">@ Rp
                                        {{ number_format($extendBooking['price_per_month'], 0, ',', '.') }}/bln</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total & Bank Transfer -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Total Amount -->
                            <div
                                class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg">
                                <p class="text-sm font-medium opacity-90 uppercase">Total Yang Harus Ditransfer</p>
                                <p class="text-4xl font-extrabold mt-2">Rp
                                    {{ number_format($extendBooking['total_amount'], 0, ',', '.') }}</p>
                                <p class="text-sm opacity-75 mt-2">{{ $extendBooking['duration'] }} bulan × Rp
                                    {{ number_format($extendBooking['price_per_month'], 0, ',', '.') }}/bulan</p>
                            </div>

                            <!-- Bank Info -->
                            @if($businessSettings)
                                <div class="bg-white rounded-2xl p-6 border-2 border-gray-200 shadow-sm">
                                    <p class="text-xs font-bold text-gray-500 uppercase mb-3">Transfer ke Rekening:</p>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-lg font-bold text-gray-800">
                                                {{ $businessSettings->bank_name ?? 'Bank' }}</p>
                                            <p class="text-2xl font-extrabold text-gray-900 tracking-wider" id="bank-number">
                                                {{ $businessSettings->bank_account_number ?? '-' }}</p>
                                            <p class="text-sm text-gray-500">a.n.
                                                {{ $businessSettings->bank_account_name ?? '-' }}</p>
                                        </div>
                                        <button type="button" id="copy-btn"
                                            data-number="{{ $businessSettings->bank_account_number ?? '' }}"
                                            class="flex flex-col items-center gap-1 px-5 py-3 bg-gray-100 border-2 border-gray-200 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 transition cursor-pointer">
                                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span class="text-xs font-bold text-gray-500 uppercase" id="copy-text">Salin</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Upload Area -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                                <div class="bg-blue-100 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h2 class="text-lg font-bold text-gray-800">Upload Bukti Transfer</h2>
                            </div>

                            <form id="payment-form" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Bank / E-Wallet Pengirim</label>
                                        <input type="text" name="sender_bank" id="sender_bank"
                                            placeholder="Contoh: BCA, Dana, Gopay"
                                            class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                            required>
                                        <p id="sender_bank_error" class="text-xs text-red-600 mt-1 hidden">Hanya boleh berisi huruf dan spasi.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Pengirim</label>
                                        <input type="text" name="sender_name" id="sender_name"
                                            placeholder="Nama pemilik rekening"
                                            class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                            required>
                                        <p id="sender_name_error" class="text-xs text-red-600 mt-1 hidden">Hanya boleh berisi huruf dan spasi.</p>
                                    </div>
                                </div>

                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-emerald-500 transition"
                                    id="upload-area">
                                    <input type="file" name="payment_proof" id="payment_proof" class="hidden"
                                        accept="image/*">
                                    <div id="upload-placeholder">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <p class="text-base text-gray-600 font-medium">Klik untuk upload bukti transfer</p>
                                        <p class="text-sm text-gray-400 mt-1">Format: JPG, PNG (Maks. 2MB)</p>
                                    </div>
                                    <img id="preview-image" class="hidden max-h-64 mx-auto rounded-lg">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                    <a href="{{ route('tenant.extend-payment.cancel') }}"
                                        class="flex items-center justify-center py-3.5 border-2 border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Batalkan
                                    </a>
                                    <button type="submit" id="submit-btn"
                                        class="flex items-center justify-center py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                        <span id="btn-text">Konfirmasi Pembayaran</span>
                                        <span id="btn-loading" class="hidden">Mengirim...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End: Full Width Payment Section -->
                @endif

                @if(!$extendBooking)
                    <!-- Right Column - Summary (only show on duration selection) -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24 h-fit">
                            <div class="flex items-center gap-2 mb-5 pb-4 border-b border-gray-100">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                <h3 class="text-lg font-bold text-gray-800">Rincian Perpanjangan</h3>
                            </div>

                            <!-- Dynamic Summary (updated via JS) -->
                            <div class="space-y-3 mb-6" id="summary-content">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Tipe Kamar</span>
                                    <span class="font-semibold text-gray-800">{{ $kamar->roomType->name ?? 'Kamar' }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Nomor Kamar</span>
                                    <span class="font-semibold text-gray-800">{{ $kamar->room_number }}</span>
                                </div>
                                <div class="flex justify-between text-sm border-t border-gray-100 pt-3">
                                    <span class="text-gray-500">Durasi Perpanjangan</span>
                                    <span class="font-semibold text-emerald-600" id="summary-duration">1 Bulan</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Harga/Bulan</span>
                                    <span class="font-semibold text-gray-800">Rp
                                        {{ number_format($pricePerMonth, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Lease Projection -->
                            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 mb-4">
                                <p class="text-xs text-blue-700 font-bold uppercase mb-2">📅 Proyeksi Sewa</p>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Sewa Saat Ini</span>
                                        <span class="font-semibold text-gray-700">s/d {{ $currentLeaseEndFormatted }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Setelah Perpanjang</span>
                                        <span class="font-bold text-blue-600" id="summary-new-end">-</span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-xl p-4 border border-emerald-100">
                                <p class="text-xs text-emerald-700 font-medium mb-1">Total Pembayaran</p>
                                <p class="text-2xl font-extrabold text-emerald-600" id="summary-total">Rp
                                    {{ number_format($pricePerMonth, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    @if(!$extendBooking)
        <script>
            const pricePerMonth = {{ $pricePerMonth }};
            const currentLeaseEnd = new Date('{{ $currentLeaseEnd }}');

            function setDuration(months) {
                document.getElementById('duration-input').value = months;
                updateCalculation();

                // Update button styles
                document.querySelectorAll('.duration-btn').forEach(btn => {
                    btn.classList.remove('border-emerald-500', 'bg-emerald-50');
                    btn.classList.add('border-gray-200');
                });
                event.target.closest('.duration-btn').classList.add('border-emerald-500', 'bg-emerald-50');
                event.target.closest('.duration-btn').classList.remove('border-gray-200');
            }

            function updateCalculation() {
                let duration = parseInt(document.getElementById('duration-input').value) || 1;

                // Clamp between 1 and 24
                if (duration < 1) duration = 1;
                if (duration > 24) duration = 24;

                // Calculate total
                const total = pricePerMonth * duration;

                // Calculate new lease end date
                const newLeaseEnd = new Date(currentLeaseEnd);
                newLeaseEnd.setMonth(newLeaseEnd.getMonth() + duration);

                // Format date
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const formattedDate = `s/d ${newLeaseEnd.getDate()} ${months[newLeaseEnd.getMonth()]} ${newLeaseEnd.getFullYear()}`;

                // Format currency
                const formattedTotal = 'Rp ' + total.toLocaleString('id-ID');

                // Update UI
                document.getElementById('summary-duration').textContent = duration + ' Bulan';
                document.getElementById('summary-new-end').textContent = formattedDate;
                document.getElementById('summary-total').textContent = formattedTotal;

                // Update quick select button styles
                document.querySelectorAll('.duration-btn').forEach(btn => {
                    const btnText = btn.querySelector('p').textContent.trim();
                    let btnMonths;

                    // Check if it's "1 Thn" = 12 months, otherwise parse the number
                    if (btnText.includes('Thn')) {
                        btnMonths = 12;
                    } else {
                        btnMonths = parseInt(btnText);
                    }

                    if (btnMonths === duration) {
                        btn.classList.add('border-emerald-500', 'bg-emerald-50');
                        btn.classList.remove('border-gray-200');
                    } else {
                        btn.classList.remove('border-emerald-500', 'bg-emerald-50');
                        btn.classList.add('border-gray-200');
                    }
                });
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', updateCalculation);
        </script>
    @endif

    @if($extendBooking)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const uploadArea = document.getElementById('upload-area');
                const fileInput = document.getElementById('payment_proof');
                const previewImage = document.getElementById('preview-image');
                const placeholder = document.getElementById('upload-placeholder');
                const submitBtn = document.getElementById('submit-btn');
                const paymentForm = document.getElementById('payment-form');
                const btnText = document.getElementById('btn-text');
                const btnLoading = document.getElementById('btn-loading');

                uploadArea.addEventListener('click', () => fileInput.click());

                fileInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Validate file size (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('Ukuran file terlalu besar! Maksimal 2MB. Silakan kompres foto atau pilih file lain.');
                            this.value = ''; // Clear input
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            previewImage.src = e.target.result;
                            previewImage.classList.remove('hidden');
                            placeholder.classList.add('hidden');
                            submitBtn.disabled = false;
                        };
                        reader.readAsDataURL(file);
                    }
                });

                paymentForm.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    submitBtn.disabled = true;
                    btnText.classList.add('hidden');
                    btnLoading.classList.remove('hidden');

                    const formData = new FormData(paymentForm);

                    try {
                        const response = await fetch('{{ route("tenant.extend-payment.confirm") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                            submitBtn.disabled = false;
                            btnText.classList.remove('hidden');
                            btnLoading.classList.add('hidden');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan: ' + error.message);
                        submitBtn.disabled = false;
                        btnText.classList.remove('hidden');
                        btnLoading.classList.add('hidden');
                    }
                });

                // Copy button functionality
                const copyBtn = document.getElementById('copy-btn');
                const copyText = document.getElementById('copy-text');
                if (copyBtn) {
                    copyBtn.addEventListener('click', function () {
                        const number = this.getAttribute('data-number');
                        navigator.clipboard.writeText(number).then(function () {
                            copyText.textContent = 'Tersalin!';
                            copyBtn.classList.add('border-emerald-500', 'bg-emerald-50');
                            setTimeout(() => {
                                copyText.textContent = 'Salin';
                                copyBtn.classList.remove('border-emerald-500', 'bg-emerald-50');
                            }, 2000);
                        }).catch(function (err) {
                            // Fallback for older browsers
                            const textArea = document.createElement('textarea');
                            textArea.value = number;
                            document.body.appendChild(textArea);
                            textArea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textArea);
                            copyText.textContent = 'Tersalin!';
                            copyBtn.classList.add('border-emerald-500', 'bg-emerald-50');
                            setTimeout(() => {
                                copyText.textContent = 'Salin';
                                copyBtn.classList.remove('border-emerald-500', 'bg-emerald-50');
                            }, 2000);
                        });
                    });
                }
            });
        </script>
    @endif

    <script>
        // Bank & Nama Pengirim: hanya huruf
        document.addEventListener('DOMContentLoaded', () => {
            [
                { id: 'sender_bank', errId: 'sender_bank_error' },
                { id: 'sender_name', errId: 'sender_name_error' },
            ].forEach(({ id, errId }) => {
                const el  = document.getElementById(id);
                const err = document.getElementById(errId);
                if (!el) return;
                el.addEventListener('keydown', e => {
                    if (e.key >= '0' && e.key <= '9') e.preventDefault();
                });
                el.addEventListener('input', () => {
                    const cleaned = el.value.replace(/[0-9]/g, '');
                    const hasDigit = el.value !== cleaned;
                    el.value = cleaned;
                    if (err) err.classList.toggle('hidden', !hasDigit);
                });
                el.addEventListener('blur', () => {
                    if (err) err.classList.add('hidden');
                });
            });
        });
    </script>

</body>

</html>