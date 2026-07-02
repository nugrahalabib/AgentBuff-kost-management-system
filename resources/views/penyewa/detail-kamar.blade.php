<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kamar {{ $kamar->name }} - Mutiara27</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-white">

    @include('partials.navbar-welcome')

    <div class="bg-white min-h-screen pb-24 md:pb-12 pt-20">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <a href="{{ route('welcome') }}#kamar" class="inline-flex items-center text-sm text-gray-500 hover:text-emerald-600 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Pilihan Kamar
            </a>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $gallery = $kamar->gallery_images ?? [];
                $fallbackImages = [
                    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=600&q=80',
                    'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?w=600&q=80',
                    'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=600&q=80',
                    'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=600&q=80',
                ];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 md:gap-4 h-64 md:h-[450px] rounded-3xl overflow-hidden relative group">
                <div class="md:col-span-2 h-full bg-gray-200 relative overflow-hidden">
                    @if($kamar->image_path)
                        <img src="{{ asset('storage/' . $kamar->image_path) }}" class="w-full h-full object-cover hover:scale-105 transition duration-700" alt="{{ $kamar->name }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1505693314120-0d443867891c?w=600&q=80" class="w-full h-full object-cover hover:scale-105 transition duration-700" alt="Main Room">
                    @endif
                </div>
                <div class="hidden md:flex flex-col gap-4 h-full">
                    <div class="h-1/2 bg-gray-200 overflow-hidden">
                        <img src="{{ isset($gallery[0]) ? asset('storage/' . $gallery[0]) : $fallbackImages[0] }}" class="w-full h-full object-cover hover:scale-105 transition duration-500" alt="Detail 1">
                    </div>
                    <div class="h-1/2 bg-gray-200 overflow-hidden">
                        <img src="{{ isset($gallery[1]) ? asset('storage/' . $gallery[1]) : $fallbackImages[1] }}" class="w-full h-full object-cover hover:scale-105 transition duration-500" alt="Detail 2">
                    </div>
                </div>
                <div class="hidden md:flex flex-col gap-4 h-full">
                    <div class="h-1/2 bg-gray-200 overflow-hidden">
                        <img src="{{ isset($gallery[2]) ? asset('storage/' . $gallery[2]) : $fallbackImages[2] }}" class="w-full h-full object-cover hover:scale-105 transition duration-500" alt="Detail 3">
                    </div>
                    <div class="h-1/2 bg-gray-200 relative overflow-hidden">
                        <img src="{{ isset($gallery[3]) ? asset('storage/' . $gallery[3]) : $fallbackImages[3] }}" class="w-full h-full object-cover hover:scale-105 transition duration-500" alt="Detail 4">
                        @if(count($gallery) > 4)
                            <div class="absolute inset-0 bg-black/30 flex items-center justify-center hover:bg-black/40 transition cursor-pointer">
                                <span class="text-white font-bold border border-white px-4 py-2 rounded-lg backdrop-blur-sm">+{{ count($gallery) - 4 }} Foto Lagi</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="absolute top-4 left-4 md:hidden">
                    <span class="bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">{{ $kamar->name }}</span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <div class="flex flex-col lg:flex-row gap-12">
                
                <div class="flex-1 space-y-8">
                    
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-3xl font-extrabold text-gray-900">Kamar {{ $kamar->name }}</h1>
                                <p class="text-gray-500 mt-1">{{ $kamar->description ?? 'Kamar nyaman dengan fasilitas lengkap' }}</p>
                            </div>
                            <div class="hidden md:block">
                                <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-100">
                                    TERSEDIA
                                </span>
                            </div>
                        </div>
                        
            <div class="flex items-center gap-6 mt-6 border-b border-gray-100 pb-6">
                            <div class="flex items-center gap-2 text-gray-600 text-sm">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Tipe: {{ $kamar->name }}
                            </div>
                            <div class="flex items-center gap-2 text-gray-600 text-sm">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Harga: Rp {{ number_format($kamar->rent_per_person, 0, ',', '.') }}{{ $kamar->capacity > 1 ? '/orang' : '' }}/bulan
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900 mb-3">Deskripsi Kamar</h3>
                        <p class="text-gray-600 leading-relaxed text-sm">
                            {{ $kamar->description ?? 'Kamar ini menawarkan kenyamanan maksimal dengan fasilitas lengkap. Dilengkapi dengan furnitur modern, pencahayaan alami yang baik, dan lokasi strategis. Sangat cocok untuk mahasiswa atau profesional muda.' }}
                        </p>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900 mb-4">Fasilitas Lengkap</h3>
                        @php
                            $facilitiesData = $kamar->facilities;
                            if (is_string($facilitiesData)) {
                                $facilitiesData = json_decode($facilitiesData, true);
                            }
                            $allIcons = config('facilities.icons');
                        @endphp
                        
                        @if($facilitiesData && is_array($facilitiesData) && count($facilitiesData) > 0)
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($facilitiesData as $facility)
                                     @php
                                        $name = is_array($facility) ? ($facility['name'] ?? '') : $facility;
                                        $iconKey = is_array($facility) ? ($facility['icon'] ?? 'check') : 'check';
                                        $svg = $allIcons[$iconKey]['svg'] ?? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                                    @endphp
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <div class="text-emerald-600 w-5 h-5 flex items-center justify-center">
                                            {!! $svg !!}
                                        </div>
                                        <span class="text-sm text-gray-700 font-medium">{{ $name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-xl p-4 text-center">
                                <p class="text-gray-500 text-sm">Fasilitas belum ditambahkan oleh pemilik.</p>
                            </div>
                        @endif
                    </div>

                </div>

                <div class="hidden lg:block w-96 relative">
                    <div class="sticky top-24 bg-white border border-gray-200 rounded-2xl p-6 shadow-xl shadow-gray-200/50">
                        <div class="flex justify-between items-end mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Mulai dari</p>
                                <p class="text-3xl font-extrabold text-emerald-700">Rp {{ number_format($kamar->rent_per_person, 0, ',', '.') }}</p>
                            </div>
                            @if($kamar->capacity > 1)
                                <span class="text-sm font-medium text-emerald-500 mb-1">/ orang (Kapasitas {{ $kamar->capacity }})</span>
                            @else
                                <span class="text-sm font-medium text-gray-400 mb-1">/ bulan</span>
                            @endif
                        </div>

                        <!-- Room Selection -->
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Pilih Kamar</label>
                            @if($kamarTersedia->count() > 0)
                                <div class="relative">
                                    <select id="roomSelect" onchange="selectRoom(this.value)" 
                                        class="w-full appearance-none bg-white border-2 border-gray-200 rounded-xl px-4 py-3.5 pr-12 text-gray-800 font-semibold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all cursor-pointer hover:border-emerald-400">
                                        <option value="">-- Pilih Nomor Kamar --</option>
                                        @foreach($kamarTersedia as $availableRoom)
                                            <option value="{{ $availableRoom->id }}">Kamar {{ $availableRoom->room_number }} (Lantai {{ $availableRoom->floor_number ?? '1' }})</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        {{ $kamarTersedia->count() }} kamar tersedia
                                    </span>
                                </div>
                                <input type="hidden" id="selectedRoomId" value="">
                                <p id="roomNotSelected" class="text-xs text-amber-600 mt-2 hidden">⚠️ Silakan pilih kamar terlebih dahulu</p>
                            @else
                                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                                    <p class="text-red-600 font-semibold">Tidak ada kamar tersedia</p>
                                    <p class="text-xs text-red-500">Semua kamar tipe ini sudah terisi</p>
                                </div>
                            @endif
                        </div>

                        @if($kamarTersedia->count() > 0)
                            <a href="#" id="bookingBtn" onclick="submitBooking(event)" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-200 transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                <span>Ajukan Sewa Sekarang</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        @else
                            <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <span>Tidak Tersedia</span>
                            </button>
                        @endif

                        <p class="text-xs text-center text-gray-400 mt-4">
                            Tidak akan dikenakan biaya sebelum disetujui.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-50">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <p class="text-xs text-gray-500">Harga Sewa</p>
                <p class="text-xl font-bold text-emerald-700">Rp {{ number_format($kamar->rent_per_person, 0, ',', '.') }} <span class="text-xs font-normal text-gray-400">{{ $kamar->capacity > 1 ? '/ org' : '' }}</span></p>
            </div>
            @if($kamarTersedia->count() > 0)
                <a href="#" id="mobileBookingBtn" onclick="submitBooking(event)" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-center font-bold py-3 rounded-xl shadow-md transition flex items-center justify-center">
                    Ajukan Sewa
                </a>
            @else
                <button disabled class="flex-1 bg-gray-300 text-gray-500 text-center font-bold py-3 rounded-xl cursor-not-allowed">
                    Tidak Tersedia
                </button>
            @endif   
        </div>
    </div>

    <script>
        let selectedRoomId = null;
        const bookingBaseUrl = "{{ route('tenant.booking.create', $kamar->id) }}";

        function selectRoom(roomId) {
            selectedRoomId = roomId;
            document.getElementById('selectedRoomId').value = roomId;
            if (roomId) {
                document.getElementById('roomNotSelected').classList.add('hidden');
            }
        }

        function submitBooking(event) {
            event.preventDefault();
            
            if (!selectedRoomId) {
                document.getElementById('roomNotSelected').classList.remove('hidden');
                document.getElementById('roomSelect')?.focus();
                return;
            }
            
            window.location.href = bookingBaseUrl + '?kamar_id=' + selectedRoomId;
        }
    </script>

</body>
</html>