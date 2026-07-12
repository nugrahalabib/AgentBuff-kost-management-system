{{-- Form tambah penyewa (dipakai owner & admin). Butuh: $storeRoute, $backRoute, $rooms --}}
<div class="max-w-2xl mx-auto">
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-lg text-gray-800">Data Penyewa</h3>
            <p class="text-sm text-gray-500 mt-1">Penyewa dicatat sebagai data internal (tanpa akun login).</p>
        </div>

        <form action="{{ $storeRoute }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 rounded-xl border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email <span class="text-gray-400">(opsional)</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">No. HP <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="tenant_type" class="block text-sm font-bold text-gray-700 mb-2">Tipe Penyewa</label>
                    <select id="tenant_type" name="tenant_type"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        <option value="mahasiswa" {{ old('tenant_type', 'mahasiswa') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="non_mahasiswa" {{ old('tenant_type') == 'non_mahasiswa' ? 'selected' : '' }}>Non-Mahasiswa</option>
                    </select>
                </div>
                <div>
                    <p class="text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5">
                        Penyewa baru belum bisa langsung ditempatkan ke kamar. Setelah disimpan, catat
                        pembayaran di menu <span class="font-semibold">Transaksi</span> untuk menempatkannya ke kamar.
                    </p>
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-bold text-gray-700 mb-2">Alamat <span class="text-gray-400">(opsional)</span></label>
                <textarea id="address" name="address" rows="2"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">{{ old('address') }}</textarea>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition">
                    TAMBAH PENYEWA
                </button>
                <a href="{{ $backRoute }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-xl text-center transition">BATAL</a>
            </div>
        </form>
    </div>
</div>
