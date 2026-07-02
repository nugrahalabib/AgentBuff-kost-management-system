@extends('layouts.admin')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Tambah Unit Kamar Baru') }}
            </h2>
            <p class="text-sm text-gray-500">Tambahkan kamar/unit baru ke dalam sistem.</p>
        </div>
        <a href="{{ route('admin.kamar') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-bold py-3 px-6 rounded-xl shadow-md transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            KEMBALI
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-lg text-gray-800">Detail Kamar Baru</h3>
                <p class="text-sm text-gray-500 mt-1">Isi informasi lengkap untuk menambahkan kamar baru.</p>
            </div>

            <form action="{{ route('admin.kamar.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Nama/Nomor Kamar -->
                <div>
                    <label for="room_number" class="block text-sm font-bold text-gray-700 mb-2">
                        Nomor Kamar <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="room_number" 
                        name="room_number" 
                        value="{{ old('room_number') }}"
                        placeholder="Contoh: 101, 102, 201, 301, dll"
                        class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('room_number') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                        required>
                    @error('room_number')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2">Format: 101 (lantai 1, nomor 01), 201 (lantai 2, nomor 01), dst.</p>
                </div>

                <!-- Lantai -->
                <div>
                    <label for="floor_number" class="block text-sm font-bold text-gray-700 mb-2">
                        Lantai <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="floor_number" 
                        name="floor_number"
                        class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('floor_number') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                        required>
                        <option value="">-- Pilih Lantai --</option>
                        <option value="1" {{ old('floor_number') == '1' ? 'selected' : '' }}>Lantai 1</option>
                        <option value="2" {{ old('floor_number') == '2' ? 'selected' : '' }}>Lantai 2</option>
                        <option value="3" {{ old('floor_number') == '3' ? 'selected' : '' }}>Lantai 3</option>
                        <option value="4" {{ old('floor_number') == '4' ? 'selected' : '' }}>Lantai 4</option>
                    </select>
                    @error('floor_number')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Kamar -->
                <div>
                    <label for="tipe_kamar_id" class="block text-sm font-bold text-gray-700 mb-2">
                        Tipe Kamar <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="tipe_kamar_id" 
                        name="tipe_kamar_id"
                        class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('tipe_kamar_id') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                        required
                        onchange="updatePrice()">
                        <option value="">-- Pilih Tipe Kamar --</option>
                        @foreach($tipeKamar as $type)
                            <option value="{{ $type->id }}" data-price="{{ $type->price_per_month }}" {{ old('tipe_kamar_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} - Rp {{ number_format($type->price_per_month, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipe_kamar_id')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Per Bulan -->
                <div>
                    <label for="price_per_month" class="block text-sm font-bold text-gray-700 mb-2">
                        Harga Per Bulan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                        <input 
                            type="number" 
                            id="price_per_month" 
                            name="price_per_month" 
                            value="{{ old('price_per_month') }}"
                            class="w-full px-4 py-3 pl-12 rounded-xl border focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('price_per_month') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                            required
                            readonly>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Harga otomatis terisi berdasarkan tipe kamar yang dipilih</p>
                    @error('price_per_month')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-bold text-gray-700 mb-2">
                        Status Awal <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="status" 
                        name="status"
                        class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('status') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                        required>
                        <option value="">-- Pilih Status --</option>
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Tersedia (Kosong)</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Perbaikan/Masalah</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2">Pilih status awal kamar. Kamar tidak bisa langsung "Terisi" saat ditambahkan.</p>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">
                        Catatan Tambahan <span class="text-gray-400">(Opsional)</span>
                    </label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        rows="3"
                        placeholder="Contoh: Kamar dengan jendela besar, dekat tangga, etc."
                        class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('notes') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Aksi -->
                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button 
                        type="submit" 
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        TAMBAH KAMAR
                    </button>
                    <a 
                        href="{{ route('admin.kamar') }}" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-xl shadow-sm transition text-center">
                        BATAL
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-2xl p-6">
            <div class="flex gap-3">
                <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h4 class="font-bold text-blue-900 mb-1">Panduan Penambahan Kamar</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>✓ Nomor kamar harus unik per pemilik kos</li>
                        <li>✓ Lantai otomatis terdeteksi dari nomor kamar (101→Lantai 1, 201→Lantai 2, dst)</li>
                        <li>✓ Harga otomatis mengikuti tipe kamar yang dipilih</li>
                        <li>✓ Kamar baru akan berstatus "Tersedia" atau "Perbaikan" sesuai pilihan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updatePrice() {
            const select = document.getElementById('tipe_kamar_id');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            
            if (price) {
                document.getElementById('price_per_month').value = price;
            }
        }

        // Optionally update floor based on room number
        document.getElementById('room_number').addEventListener('input', function(e) {
            const roomNumber = this.value;
            if (roomNumber && roomNumber.length >= 1) {
                const floor = roomNumber.charAt(0);
                if (floor >= '1' && floor <= '4') {
                    document.getElementById('floor_number').value = floor;
                }
            }
        });
    </script>
@endsection
