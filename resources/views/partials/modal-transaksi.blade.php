{{-- Modal Tambah Transaksi Manual. Butuh: $tenants, $rooms --}}
<div id="transaksiModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 invisible opacity-0 transition-opacity duration-200" onclick="if(event.target===this) closeModal('transaksiModal')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
            <h3 class="font-bold text-lg text-gray-800">Input Transaksi Manual</h3>
            <button type="button" onclick="closeModal('transaksiModal')" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('owner.transaksi.store-manual') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Penyewa <span class="text-red-500">*</span></label>
                <select name="penyewa_id" required class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('penyewa_id') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Penyewa --</option>
                    @foreach($tenants as $t)<option value="{{ $t->id }}" {{ old('penyewa_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach
                </select>
                @error('penyewa_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                @if($tenants->isEmpty())<p class="text-amber-600 text-xs mt-1">Belum ada penyewa — tambah dulu di menu Data Penyewa.</p>@endif
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Kamar <span class="text-red-500">*</span></label>
                <select name="kamar_id" id="tx_room" required onchange="txCalc()" class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('kamar_id') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Kamar --</option>
                    @foreach($rooms as $r)
                        @php
                            $cap = $r->roomType->capacity ?? 1;
                            $occ = $r->occupants_count ?? 0;
                            $lbl = 'Kamar ' . $r->room_number . ' — ' . ($r->roomType->name ?? '-')
                                . ($cap > 1 ? " ({$occ}/{$cap} terisi)" : '')
                                . ' · Rp ' . number_format($r->rent_per_person, 0, ',', '.') . ($cap > 1 ? '/org' : '');
                        @endphp
                        <option value="{{ $r->id }}" data-price="{{ $r->rent_per_person }}" {{ old('kamar_id') == $r->id ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                @error('kamar_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="tx_amount" value="{{ old('amount') }}" min="1" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Durasi (bulan) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration" id="tx_dur" value="{{ old('duration', 1) }}" min="1" max="24" required oninput="txCalc()" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
                <select name="payment_method" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="manual_transfer" {{ old('payment_method') == 'manual_transfer' ? 'selected' : '' }}>Transfer Manual</option>
                    <option value="edc" {{ old('payment_method') == 'edc' ? 'selected' : '' }}>EDC / Mesin Kartu</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Bukti Pembayaran <span class="text-gray-400">(opsional)</span></label>
                <input type="file" name="payment_proof" accept="image/*" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-bold">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl">SIMPAN</button>
                <button type="button" onclick="closeModal('transaksiModal')" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-xl">BATAL</button>
            </div>
        </form>
    </div>
</div>
<script>
    function txCalc() { const s = document.getElementById('tx_room'); const p = parseInt(s.options[s.selectedIndex] && s.options[s.selectedIndex].getAttribute('data-price') || '0', 10); const d = parseInt(document.getElementById('tx_dur').value || '1', 10); if (p > 0) document.getElementById('tx_amount').value = p * d; }
    if (new URLSearchParams(window.location.search).get('add') === '1') { document.addEventListener('DOMContentLoaded', function () { window.openModal && openModal('transaksiModal'); }); }
    @if($errors->hasAny(['penyewa_id', 'kamar_id', 'amount', 'duration', 'payment_method']))
    document.addEventListener('DOMContentLoaded', function () { window.openModal && openModal('transaksiModal'); });
    @endif
</script>
