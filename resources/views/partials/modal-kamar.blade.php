{{-- Modal Tambah Kamar. Butuh: $tipeKamar --}}
<div id="kamarModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 invisible opacity-0 transition-opacity duration-200" onclick="if(event.target===this) closeModal('kamarModal')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
            <h3 class="font-bold text-lg text-gray-800">Tambah Kamar</h3>
            <button type="button" onclick="closeModal('kamarModal')" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('owner.kamar.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Kamar <span class="text-red-500">*</span></label>
                <input type="text" name="room_number" value="{{ old('room_number') }}" required oninput="kmSyncFloor(this.value)" placeholder="mis. 101"
                    class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('room_number') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                @error('room_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Lantai <span class="text-red-500">*</span></label>
                    <select name="floor_number" id="km_floor" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                        @for($i = 1; $i <= 4; $i++)<option value="{{ $i }}" {{ old('floor_number') == $i ? 'selected' : '' }}>Lantai {{ $i }}</option>@endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tipe Kamar <span class="text-red-500">*</span></label>
                <select name="tipe_kamar_id" id="km_type" required onchange="kmSetPrice()"
                    class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('tipe_kamar_id') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Tipe --</option>
                    @foreach($tipeKamar as $t)
                        <option value="{{ $t->id }}" data-price="{{ $t->price_per_month }}" {{ old('tipe_kamar_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} — Rp {{ number_format($t->price_per_month, 0, ',', '.') }}</option>
                    @endforeach
                </select>
                @error('tipe_kamar_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                @if($tipeKamar->isEmpty())<p class="text-amber-600 text-xs mt-1">Belum ada tipe kamar — tambah dulu di Pengaturan.</p>@endif
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Harga / Bulan</label>
                <input type="number" name="price_per_month" id="km_price" value="{{ old('price_per_month') }}" readonly required
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 bg-gray-50">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Catatan <span class="text-gray-400">(opsional)</span></label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl">TAMBAH KAMAR</button>
                <button type="button" onclick="closeModal('kamarModal')" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-xl">BATAL</button>
            </div>
        </form>
    </div>
</div>
<script>
    function kmSetPrice() { const s = document.getElementById('km_type'); const p = s.options[s.selectedIndex] && s.options[s.selectedIndex].getAttribute('data-price'); if (p) document.getElementById('km_price').value = p; }
    function kmSyncFloor(v) { if (v && v[0] >= '1' && v[0] <= '4') document.getElementById('km_floor').value = v[0]; }
    if (new URLSearchParams(window.location.search).get('add') === '1') { document.addEventListener('DOMContentLoaded', function () { window.openModal && openModal('kamarModal'); }); }
    @if($errors->has('room_number') || $errors->has('tipe_kamar_id') || $errors->has('floor_number') || $errors->has('price_per_month'))
    document.addEventListener('DOMContentLoaded', function () { window.openModal && openModal('kamarModal'); });
    @endif
</script>
