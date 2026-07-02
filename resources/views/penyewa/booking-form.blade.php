<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengajuan Sewa {{ $tipeKamarItem->name }} - Mutiara27</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50">

    @include('partials.navbar-welcome')

    <div class="min-h-screen pb-20 pt-20">
        
        <!-- Header -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            <a href="{{ route('tenant.room.detail', $tipeKamarItem->id) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-emerald-600 transition mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Detail Kamar
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Formulir Pengajuan Sewa</h1>
            <p class="text-sm text-gray-500 mt-1">Lengkapi data diri Anda untuk melanjutkan proses booking kamar impian Anda.</p>
        </div>

        <!-- Step Indicator -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="flex justify-center mb-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-emerald-600 px-3 py-1 bg-emerald-50 rounded-full border border-emerald-200">1. Data Diri</span>
                    <div class="w-8 h-px bg-gray-300"></div>
                    <span class="text-xs font-bold text-gray-400">2. Pembayaran</span>
                    <div class="w-8 h-px bg-gray-300"></div>
                    <span class="text-xs font-bold text-gray-400">3. Selesai</span>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <form action="{{ route('tenant.booking.store', $tipeKamarItem->id) }}" method="POST">
                @csrf
                @if($kamarDipilih)
                    <input type="hidden" name="kamar_id" value="{{ $kamarDipilih->id }}">
                @endif
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column (2/3 width) -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Room Preview Card -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                            <div class="flex gap-5">
                                @php
                                    // Foto cadangan SAMA & konsisten dengan kartu "Pilihan Tipe Kamar" di halaman utama
                                    // (di-key dengan id kamar). Kalau owner sudah mengunggah foto, foto itu yang dipakai.
                                    $fallbackImages = [
                                        'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                                        'https://images.unsplash.com/photo-1505691938895-1758d7feb511?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                                        'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                                        'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                                        'https://images.unsplash.com/photo-1567767292278-a4f21aa2d36e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                                        'https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                                    ];
                                    $roomImg = $tipeKamarItem->image_path
                                        ? (\Illuminate\Support\Str::startsWith($tipeKamarItem->image_path, ['http://', 'https://'])
                                            ? $tipeKamarItem->image_path
                                            : asset('storage/' . $tipeKamarItem->image_path))
                                        : $fallbackImages[$tipeKamarItem->id % count($fallbackImages)];
                                @endphp
                                <img src="{{ $roomImg }}" class="w-36 h-28 rounded-xl object-cover flex-shrink-0 shadow-sm" alt="{{ $tipeKamarItem->name }}">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h2 class="text-xl font-bold text-gray-900">{{ $tipeKamarItem->name }}</h2>
                                            <p class="text-sm text-gray-500 mt-0.5">Kapasitas {{ $tipeKamarItem->capacity }} Orang</p>
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            @if($kamarDipilih)
                                                <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-full whitespace-nowrap">
                                                    Kamar {{ $kamarDipilih->room_number }}
                                                </span>
                                            @endif
                                            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded-full whitespace-nowrap">Tersedia</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Facility Tags -->
                                    @if($tipeKamarItem->facilities && is_array($tipeKamarItem->facilities))
                                        <div class="flex flex-wrap gap-2 mt-4">
                                            @foreach($tipeKamarItem->facilities as $facility)
                                                <span class="inline-flex items-center gap-1.5 bg-gray-50 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-200">
                                                    @php
                                                        $iconData = config('facilities.icons.' . ($facility['icon'] ?? 'star'));
                                                    @endphp
                                                    @if($iconData && isset($iconData['svg']))
                                                        <span class="w-3.5 h-3.5 text-gray-500 [&>svg]:w-full [&>svg]:h-full">{!! $iconData['svg'] !!}</span>
                                                    @endif
                                                    {{ $facility['name'] ?? $facility }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Identity Form Card -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                                <div class="bg-emerald-100 p-2.5 rounded-xl text-emerald-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Identitas Penyewa</h3>
                                    <p class="text-xs text-gray-500">Pastikan data sesuai dengan identitas Anda</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap (Sesuai KTP)</label>
                                    <input type="text" value="{{ Auth::user()->name }}" class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 text-gray-600 py-3" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" value="{{ Auth::user()->email }}" class="w-full border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50 text-gray-600 py-3" readonly>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp Aktif <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone"
                                        value="{{ old('phone', $profile->phone ?? '') }}"
                                        placeholder="Contoh: 08123456789"
                                        inputmode="numeric"
                                        maxlength="15"
                                        id="input-phone"
                                        class="w-full rounded-xl focus:ring-emerald-500 focus:border-emerald-500 transition py-3 {{ $errors->has('phone') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                        required>
                                    @error('phone')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">Hanya angka, 8–15 digit. Data ini akan otomatis tersimpan ke profil Anda.</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column (1/3 width) -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24">
                            <div class="flex items-center gap-2 mb-5 pb-4 border-b border-gray-100">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <h3 class="text-lg font-bold text-gray-800">Rincian Sewa</h3>
                            </div>
                            
                            <div class="space-y-4 mb-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Masuk</label>
                                    <input type="date" name="check_in_date" id="checkInDate" class="w-full border-gray-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 py-2.5" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Durasi Sewa</label>
                                    <select name="duration" id="durationSelect" class="w-full border-gray-300 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 py-2.5">
                                        <option value="1">1 Bulan</option>
                                        <option value="3">3 Bulan</option>
                                        <option value="6">6 Bulan</option>
                                        <option value="12">1 Tahun</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-4 mb-6">
                                <div class="flex justify-between text-sm mb-3">
                                    <span class="text-gray-500">Harga / Bulan{{ $tipeKamarItem->capacity > 1 ? ' (per orang)' : '' }}</span>
                                    <span class="font-semibold text-gray-800">Rp {{ number_format($tipeKamarItem->rent_per_person, 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-xl p-4 border border-emerald-100">
                                    <p class="text-xs text-emerald-700 font-medium mb-1">Total Pembayaran</p>
                                    <p class="text-2xl font-extrabold text-emerald-600" id="totalPrice">Rp {{ number_format($tipeKamarItem->rent_per_person, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500 mt-1.5" id="durationLabel">*Untuk 1 bulan{{ $tipeKamarItem->capacity > 1 ? ' (per orang)' : '' }}</p>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-200 transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                Lanjut ke Pembayaran
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </button>
                            
                            <p class="flex items-center justify-center gap-1.5 text-xs text-gray-400 mt-4">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Data Anda aman dan terenkripsi
                            </p>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        // Set today's date as default for check-in date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            document.getElementById('checkInDate').value = `${yyyy}-${mm}-${dd}`;
        });

        // Dynamic price calculation based on duration
        const pricePerMonth = {{ $tipeKamarItem->rent_per_person }};
        const isSharedRoom = {{ $tipeKamarItem->capacity > 1 ? 'true' : 'false' }};
        const durationSelect = document.getElementById('durationSelect');
        const totalPriceEl = document.getElementById('totalPrice');
        const durationLabelEl = document.getElementById('durationLabel');

        function formatPrice(price) {
            return 'Rp ' + price.toLocaleString('id-ID');
        }

        function updatePrice() {
            const months = parseInt(durationSelect.value);
            const total = pricePerMonth * months;
            totalPriceEl.textContent = formatPrice(total);
            
            const perPersonText = isSharedRoom ? ' (per orang)' : '';
            if (months === 1) {
                durationLabelEl.textContent = '*Untuk 1 bulan' + perPersonText;
            } else if (months === 12) {
                durationLabelEl.textContent = '*Untuk 1 tahun (12 bulan)' + perPersonText;
            } else {
                durationLabelEl.textContent = `*Untuk ${months} bulan` + perPersonText;
            }
        }

        durationSelect.addEventListener('change', updatePrice);

        // Nomor WhatsApp: hanya angka
        const phoneInput = document.getElementById('input-phone');
        if (phoneInput) {
            phoneInput.addEventListener('keydown', function(e) {
                const blocked = ['e', 'E', '+', '-', '.', ',', ' '];
                if (blocked.includes(e.key)) e.preventDefault();
            });
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    </script>

</body>
</html>
