{{-- Modal Tambah Transaksi Manual. Butuh: $tenants, $rooms, $penyewaData, $roomData, $preselectPenyewa --}}
@php
    $txHasErrors = $errors->hasAny(['penyewa_id', 'kamar_id', 'amount', 'duration', 'payment_method', 'payment_proof']) ? 'true' : 'false';
@endphp
<div id="transaksiModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 invisible opacity-0 transition-opacity duration-200" onclick="if(event.target===this) closeTxModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
            <h3 class="font-bold text-lg text-gray-800">Input Transaksi Manual</h3>
            <button type="button" onclick="closeTxModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('owner.transaksi.store-manual') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4"
              x-data="txModal({
                tenants: {{ Illuminate\Support\Js::from($penyewaData) }},
                rooms: {{ Illuminate\Support\Js::from($roomData) }},
                initPenyewa: '{{ old('penyewa_id', $preselectPenyewa) }}',
                initKamar: '{{ old('kamar_id') }}',
                initDuration: '{{ old('duration', 1) }}',
                initAmount: '{{ old('amount') }}',
                hasErrors: {{ $txHasErrors }}
              })">
            @csrf

            {{-- Penyewa: searchable combobox --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Penyewa <span class="text-red-500">*</span></label>
                <input type="hidden" name="penyewa_id" :value="penyewaId">
                <div class="relative" @click.outside="openTenant = false">
                    <input type="text" x-model="tenantQuery" @focus="openTenant = true" @input="openTenant = true"
                        placeholder="Ketik untuk mencari penyewa…" autocomplete="off"
                        class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('penyewa_id') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500">
                    <div x-show="openTenant" x-cloak class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-52 overflow-auto">
                        <template x-for="t in filteredTenants" :key="t.id">
                            <button type="button" @click="selectTenant(t)" class="w-full text-left px-4 py-2 text-sm hover:bg-emerald-50"
                                x-text="t.name + (t.kamar_id ? ' · sudah punya kamar (perpanjangan)' : '')"></button>
                        </template>
                        <div x-show="filteredTenants.length === 0" class="px-4 py-2 text-sm text-gray-400">Tidak ada penyewa cocok.</div>
                    </div>
                </div>
                @error('penyewa_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                @if($tenants->isEmpty())<p class="text-amber-600 text-xs mt-1">Belum ada penyewa — tambah dulu di menu Data Penyewa.</p>@endif
                <p x-show="isExtension" x-cloak class="text-emerald-600 text-xs mt-1">Penyewa sudah menempati kamar ini — pembayaran dihitung sebagai perpanjangan sewa.</p>
            </div>

            {{-- Kamar: dinamis mengikuti penyewa terpilih. Hidden input membawa nilai
                 walau select di-disable saat perpanjangan (disabled select tak ter-POST). --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Kamar <span class="text-red-500">*</span></label>
                <input type="hidden" name="kamar_id" :value="kamarId">
                <select x-model="kamarId" @change="recalc()" :disabled="isExtension"
                    class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('kamar_id') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 disabled:bg-gray-100">
                    <option value="">-- Pilih Kamar --</option>
                    <template x-for="r in filteredRooms" :key="r.id">
                        <option :value="r.id" x-text="r.label + (r.capacity > 1 ? ' (' + r.occupants + '/' + r.capacity + ' terisi)' : '') + ' · Rp ' + r.price.toLocaleString('id-ID') + (r.capacity > 1 ? '/org' : '')"></option>
                    </template>
                </select>
                <p x-show="!penyewaId" class="text-gray-400 text-xs mt-1">Pilih penyewa dulu untuk melihat kamar tersedia.</p>
                <p x-show="penyewaId && filteredRooms.length === 0" x-cloak class="text-amber-600 text-xs mt-1">Tak ada kamar dengan slot kosong.</p>
                @error('kamar_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" x-model="amount" min="1" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Durasi (bulan) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration" x-model="duration" @input="recalc()" min="1" max="24" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
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
                <label class="block text-sm font-bold text-gray-700 mb-1">Bukti Pembayaran <span class="text-red-500">*</span></label>
                <input type="file" name="payment_proof" accept="image/*" required class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-bold">
                @error('payment_proof')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl">SIMPAN</button>
                <button type="button" onclick="closeTxModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-xl">BATAL</button>
            </div>
        </form>
    </div>
</div>
<script>
    // Buka/tutup modal transaksi sendiri (menghindari tabrakan dengan openModal
    // gambar-bukti di halaman data-transaksi). Modal disembunyikan via invisible+opacity-0.
    window.openTxModal = function () { const m = document.getElementById('transaksiModal'); if (m) m.classList.remove('invisible', 'opacity-0'); };
    window.closeTxModal = function () { const m = document.getElementById('transaksiModal'); if (m) m.classList.add('invisible', 'opacity-0'); };

    function txModal(cfg) {
        return {
            tenants: cfg.tenants,
            rooms: cfg.rooms,
            penyewaId: cfg.initPenyewa || '',
            tenantQuery: '',
            openTenant: false,
            kamarId: cfg.initKamar || '',
            duration: cfg.initDuration || 1,
            amount: cfg.initAmount || '',

            init() {
                if (this.penyewaId) {
                    const t = this.tenants.find(x => String(x.id) === String(this.penyewaId));
                    if (t) { this.tenantQuery = t.name; this.applyTenant(t, !!this.kamarId); }
                }
                // Buka modal otomatis bila preselect (?penyewa=), ?add=1, atau ada error validasi.
                const params = new URLSearchParams(window.location.search);
                if (this.penyewaId || params.get('add') === '1' || cfg.hasErrors) {
                    window.openTxModal && window.openTxModal();
                }
            },

            get selectedTenant() { return this.tenants.find(t => String(t.id) === String(this.penyewaId)) || null; },
            get isExtension() { return !!(this.selectedTenant && this.selectedTenant.kamar_id); },
            get filteredTenants() {
                const q = this.tenantQuery.toLowerCase().trim();
                return this.tenants.filter(t => !q || t.name.toLowerCase().includes(q)).slice(0, 50);
            },
            get filteredRooms() {
                const t = this.selectedTenant;
                if (!t) return [];
                if (t.kamar_id) return this.rooms.filter(r => String(r.id) === String(t.kamar_id));
                return this.rooms.filter(r => r.occupants < r.capacity);
            },

            selectTenant(t) {
                this.tenantQuery = t.name;
                this.openTenant = false;
                this.penyewaId = String(t.id);
                this.applyTenant(t, false);
            },
            applyTenant(t, keepKamar) {
                if (t.kamar_id) { this.kamarId = String(t.kamar_id); }
                else if (!keepKamar) { this.kamarId = ''; }
                this.recalc();
            },
            recalc() {
                const r = this.rooms.find(x => String(x.id) === String(this.kamarId));
                const dur = parseInt(this.duration || '1', 10);
                if (r && r.price > 0) this.amount = r.price * dur;
            },
        };
    }
</script>
