<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran - Mutiara27</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Welcome-style Navbar -->
    <nav class="bg-white/95 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="bg-emerald-600 p-1.5 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-800 tracking-wide">Mutiara<span class="text-emerald-600">27</span></span>
            </a>

            <div class="hidden md:flex items-center gap-8">
                <a href="{{ url('/') }}#home" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                    Beranda
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ url('/') }}#galeri" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                    Galeri
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ url('/') }}#fasilitas" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                    Fasilitas
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ url('/') }}#kamar" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                    Pilihan Kamar
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ url('/') }}#kontak" class="relative text-sm font-medium text-gray-600 hover:text-emerald-600 transition group">
                    Lokasi & Kontak
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-600 group-hover:w-full transition-all duration-300"></span>
                </a>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800">Dashboard Saya</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 hover:text-emerald-600 px-4 py-2">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-5 py-2.5 rounded-full shadow-lg shadow-emerald-200 transition transform hover:scale-105">
                        Daftar Sekarang
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="bg-gray-50 min-h-screen py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-center mb-8">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-400">1. Data Diri</span>
                    <div class="w-8 h-px bg-gray-300"></div>
                    <span class="text-xs font-bold text-emerald-600 px-3 py-1 bg-emerald-50 rounded-full border border-emerald-200">2. Pembayaran</span>
                    <div class="w-8 h-px bg-gray-300"></div>
                    <span class="text-xs font-bold text-gray-400">3. Selesai</span>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 relative">
                <div class="h-2 bg-gradient-to-r from-emerald-400 to-emerald-600"></div>

                <div class="p-8 md:p-10">
                    <!-- Booking Summary -->
                    <div class="text-center mb-8">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">Total Tagihan</p>
                        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900">Rp {{ number_format($booking['total_amount'] ?? 0, 0, ',', '.') }}</h1>
                        <p class="text-sm text-emerald-600 font-medium mt-2 bg-emerald-50 inline-block px-3 py-1 rounded-lg">Invoice #{{ $booking['invoice_number'] ?? '-' }}</p>
                    </div>

                    <!-- Booking Details -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-200">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Tipe Kamar</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $booking['room_type_name'] }}</p>
                                @if(isset($booking['room_number']))
                                    <p class="text-xs text-emerald-600 font-bold mt-0.5">Kamar {{ $booking['room_number'] }}</p>
                                @else
                                    <p class="text-xs text-gray-400 ita mt-0.5">Kamar belum dipilih</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Durasi</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $booking['duration'] }} Bulan</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Check-in</p>
                                <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($booking['check_in_date'])->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Harga/Bulan{{ isset($booking['capacity']) && $booking['capacity'] > 1 ? ' (per orang)' : '' }}</p>
                                <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($booking['price_per_month'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 mb-8 relative">
                        <p class="text-xs text-center text-gray-500 mb-4">Silakan transfer ke rekening berikut:</p>
                        
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex items-center gap-4">
                                @php
                                    // Get bank name, default to BCA if not set
                                    $bankName = $businessSettings->bank_name ?? 'BCA';
                                    
                                    // Determine logo URL based on bank name (case insensitive)
                                    $bankNameLower = strtolower($bankName);
                                    $logoUrl = null;
                                    $bgColor = 'bg-blue-600'; // Default color
                                    $bankShort = explode(' ', $bankName)[0];
                                    
                                    if (str_contains($bankNameLower, 'bca')) {
                                        $logoUrl = 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/1200px-Bank_Central_Asia.svg.png';
                                        $bgColor = 'bg-white';
                                    } elseif (str_contains($bankNameLower, 'mandiri')) {
                                        $logoUrl = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/1200px-Bank_Mandiri_logo_2016.svg.png';
                                        $bgColor = 'bg-white';
                                    } elseif (str_contains($bankNameLower, 'bni')) {
                                        $logoUrl = 'https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/1200px-BNI_logo.svg.png';
                                        $bgColor = 'bg-white';
                                    } elseif (str_contains($bankNameLower, 'bri')) {
                                        $logoUrl = 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/BANK_BRI_logo.svg/1200px-BANK_BRI_logo.svg.png';
                                        $bgColor = 'bg-white';
                                    }
                                @endphp
                                
                                @if($logoUrl)
                                    <div class="w-16 h-12 {{ $bgColor }} rounded-lg flex items-center justify-center p-2 border border-gray-100 shadow-sm">
                                        {{-- Jika logo (hotlink eksternal) gagal dimuat, ganti dengan nama bank agar tidak kosong --}}
                                        <img src="{{ $logoUrl }}" alt="{{ $bankName }}" class="w-full h-full object-contain"
                                             onerror="this.onerror=null;this.replaceWith(Object.assign(document.createElement('span'),{className:'text-blue-700 font-bold text-xs italic uppercase',textContent:@js($bankShort)}));">
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs italic uppercase shadow-sm">
                                        {{ $bankShort }}
                                    </div>
                                @endif

                                <div>
                                    <p class="text-xs text-gray-400">{{ $businessSettings->bank_name ?? 'Bank Central Asia' }}</p>
                                    <p class="text-lg font-bold text-gray-800 tracking-wider" id="accountNumber">{{ $businessSettings->bank_account_number ?? '123-456-7890' }}</p>
                                    <p class="text-xs text-gray-500">a.n {{ $businessSettings->bank_account_name ?? 'Pemilik Kost' }}</p>
                                </div>
                            </div>
                            <button type="button" onclick="copyAccountNumber()" class="text-emerald-600 hover:text-emerald-700 font-bold text-xs bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-lg transition flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span id="copyText">Salin</span>
                            </button>
                        </div>
                    </div>

                    <!-- Alert Container -->
                    <div id="alert-container" class="hidden mb-6">
                        <div id="alert-message" class="p-4 rounded-lg"></div>
                    </div>

                    <form id="paymentForm" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-700 text-center">Lengkapi Data Pembayaran</label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 rounded-xl border border-gray-200">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bank / E-Wallet Pengirim</label>
                                    <input type="text" name="sender_bank" id="sender_bank"
                                        value="{{ old('sender_bank') }}"
                                        placeholder="Contoh: BCA, Dana, Gopay"
                                        class="w-full rounded-lg text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('sender_bank') ? 'border-red-500' : 'border-gray-300' }}"
                                        required>
                                    @error('sender_bank')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Pengirim</label>
                                    <input type="text" name="sender_name" id="sender_name"
                                        value="{{ old('sender_name') }}"
                                        placeholder="Nama pemilik rekening"
                                        class="w-full rounded-lg text-sm focus:border-emerald-500 focus:ring-emerald-500 {{ $errors->has('sender_name') ? 'border-red-500' : 'border-gray-300' }}"
                                        required>
                                    @error('sender_name')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <label class="block text-sm font-bold text-gray-700 text-center mt-4">Upload Bukti Transfer</label>
                            
                            <div id="dropZone" class="border-2 border-dashed border-emerald-300 rounded-2xl p-8 text-center hover:bg-emerald-50/50 transition cursor-pointer group relative">
                                <input type="file" name="payment_proof" id="paymentProofInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/jpeg,image/png,image/jpg">
                                <div id="uploadPlaceholder" class="space-y-2">
                                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600">Klik untuk upload atau drag & drop</p>
                                    <p class="text-xs text-gray-400">JPG, PNG (Max 2MB)</p>
                                </div>
                                <div id="uploadPreview" class="hidden relative z-20">
                                    <img id="previewImage" src="" alt="Preview" class="max-h-40 mx-auto rounded-lg shadow-sm mb-2">
                                    <p id="fileName" class="text-sm text-emerald-600 font-medium"></p>
                                    <button type="button" onclick="clearUpload()" class="text-xs text-red-500 hover:text-red-700 mt-2 font-bold px-3 py-1 rounded bg-red-50 hover:bg-red-100 transition">Hapus & Ganti</button>
                                </div>
                            </div>

                            <button type="submit" id="confirmBtn" class="w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-200 transition transform hover:scale-[1.01] text-base disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                <span id="btnText">Konfirmasi Pembayaran</span>
                                <span id="btnLoading" class="hidden">
                                    <svg class="animate-spin h-5 w-5 text-white inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>    
                            
                            <div class="text-center mt-4">
                                <a href="{{ route('tenant.dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600 underline">Batalkan & Kembali</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div class="mt-8 text-center flex justify-center gap-6 opacity-60">
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Pembayaran Aman
                </div>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Verifikasi 1x24 Jam
                </div>
            </div>

        </div>
    </div>

    <script>
        const fileInput = document.getElementById('paymentProofInput');
        const dropZone = document.getElementById('dropZone');
        const placeholder = document.getElementById('uploadPlaceholder');
        const preview = document.getElementById('uploadPreview');
        const previewImage = document.getElementById('previewImage');
        const fileName = document.getElementById('fileName');
        const confirmBtn = document.getElementById('confirmBtn');

        // File input change handler
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                handleFile(this.files[0]);
            }
        });

        // Drag and drop handlers
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-emerald-500', 'bg-emerald-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-emerald-500', 'bg-emerald-50');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-emerald-500', 'bg-emerald-50');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                handleFile(e.dataTransfer.files[0]);
            }
        });

        function handleFile(file) {
            // Validate file type
            if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                showAlert('File harus berformat JPG atau PNG', false);
                return;
            }
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                showAlert('Ukuran file terlalu besar! Maksimal 2MB. Silakan kompres foto atau pilih file lain.', false);
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                fileName.textContent = file.name;
                placeholder.classList.add('hidden');
                preview.classList.remove('hidden');
                confirmBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }

        function clearUpload() {
            fileInput.value = '';
            previewImage.src = '';
            fileName.textContent = '';
            placeholder.classList.remove('hidden');
            preview.classList.add('hidden');
            confirmBtn.disabled = true;
        }

        function copyAccountNumber() {
            const accountNumber = document.getElementById('accountNumber').textContent.replace(/-/g, '');
            navigator.clipboard.writeText(accountNumber).then(() => {
                document.getElementById('copyText').textContent = 'Tersalin!';
                setTimeout(() => {
                    document.getElementById('copyText').textContent = 'Salin';
                }, 2000);
            });
        }

        function showAlert(message, isSuccess) {
            const container = document.getElementById('alert-container');
            const alertDiv = document.getElementById('alert-message');
            
            container.classList.remove('hidden');
            alertDiv.className = 'p-4 rounded-lg ' + (isSuccess 
                ? 'bg-green-100 border border-green-400 text-green-700' 
                : 'bg-red-100 border border-red-400 text-red-700');
            alertDiv.innerHTML = message;
            
            if (!isSuccess) {
                setTimeout(() => container.classList.add('hidden'), 5000);
            }
        }

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            
            confirmBtn.disabled = true;
            btnText.classList.add('hidden');
            btnLoading.classList.remove('hidden');
            
            try {
                const response = await fetch('{{ route("tenant.booking.payment.confirm") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message, true);
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    showAlert(data.message || 'Terjadi kesalahan', false);
                    confirmBtn.disabled = false;
                    btnText.classList.remove('hidden');
                    btnLoading.classList.add('hidden');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan: ' + error.message, false);
                confirmBtn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        });

        // Bank & Nama Pengirim: hanya huruf
        ['sender_bank', 'sender_name'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('keydown', e => {
                if (e.key >= '0' && e.key <= '9') e.preventDefault();
            });
            el.addEventListener('input', () => {
                el.value = el.value.replace(/[0-9]/g, '');
            });
        });
    </script>
</body>
</html>