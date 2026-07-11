{{-- Modal Tambah Penyewa. Butuh: $rooms --}}
<div id="penyewaModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 invisible opacity-0 transition-opacity duration-200" onclick="if(event.target===this) closeModal('penyewaModal')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
            <h3 class="font-bold text-lg text-gray-800">Tambah Penyewa</h3>
            <button type="button" onclick="closeModal('penyewaModal')" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('owner.penyewa.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <p class="text-xs text-gray-500 -mt-1">Penyewa dicatat sebagai data internal (tanpa akun login).</p>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Email <span class="text-gray-400">(opsional)</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">No. HP <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tipe Penyewa</label>
                    <select name="tenant_type" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                        <option value="mahasiswa" {{ old('tenant_type', 'mahasiswa') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="non_mahasiswa" {{ old('tenant_type') == 'non_mahasiswa' ? 'selected' : '' }}>Non-Mahasiswa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tempatkan ke Kamar <span class="text-gray-400">(opsional)</span></label>
                    <select name="kamar_id" class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('kamar_id') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Belum ditempatkan --</option>
                        @foreach($rooms as $r)
                            @php
                                $cap = $r->roomType->capacity ?? 1;
                                $occ = $r->occupants_count ?? 0;
                                $lbl = 'Kamar ' . $r->room_number . ' — ' . ($r->roomType->name ?? '-') . ($cap > 1 ? " ({$occ}/{$cap} slot terisi)" : '');
                            @endphp
                            <option value="{{ $r->id }}" {{ old('kamar_id') == $r->id ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @if($rooms->isEmpty())<p class="text-amber-600 text-xs mt-1">Tak ada kamar kosong.</p>@endif
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Alamat <span class="text-gray-400">(opsional)</span></label>
                <textarea name="address" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">{{ old('address') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl">TAMBAH PENYEWA</button>
                <button type="button" onclick="closeModal('penyewaModal')" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-xl">BATAL</button>
            </div>
        </form>
    </div>
</div>
<script>
    if (new URLSearchParams(window.location.search).get('add') === '1') { document.addEventListener('DOMContentLoaded', function () { window.openModal && openModal('penyewaModal'); }); }
</script>
@if($errors->hasAny(['name', 'email', 'kamar_id']))
<script>document.addEventListener('DOMContentLoaded', function () { window.openModal && openModal('penyewaModal'); });</script>
@endif
