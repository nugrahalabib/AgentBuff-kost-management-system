@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">Input Transaksi Manual</h2>
            <p class="text-sm text-gray-500">Catat pembayaran & tempatkan penyewa ke kamar (penempatan wajib disertai pembayaran).</p>
        </div>
        <a href="{{ route('owner.verifikasi-transaksi') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-bold py-3 px-6 rounded-xl shadow-md transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            KEMBALI
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto"
         x-data="txForm({
            tenants: {{ Illuminate\Support\Js::from($penyewaData) }},
            rooms: {{ Illuminate\Support\Js::from($roomData) }},
            initPenyewa: '{{ old('penyewa_id', $preselectPenyewa) }}',
            initKamar: '{{ old('kamar_id') }}',
            initDuration: '{{ old('duration', 1) }}',
            initAmount: '{{ old('amount') }}'
         })">
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-lg text-gray-800">Detail Pembayaran</h3>
            </div>

            <form action="{{ route('owner.transaksi.store-manual') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                {{-- Penyewa: searchable combobox --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Penyewa <span class="text-red-500">*</span></label>
                    <input type="hidden" name="penyewa_id" :value="penyewaId">
                    <div class="relative" @click.outside="openTenant = false">
                        <input type="text" x-model="tenantQuery" @focus="openTenant = true" @input="openTenant = true"
                            placeholder="Ketik untuk mencari penyewa…"
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('penyewa_id') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        <div x-show="openTenant" x-cloak
                            class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-56 overflow-auto">
                            <template x-for="t in filteredTenants" :key="t.id">
                                <button type="button" @click="selectTenant(t)"
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-emerald-50"
                                    x-text="t.name + (t.kamar_id ? ' · sudah punya kamar (perpanjangan)' : '')"></button>
                            </template>
                            <div x-show="filteredTenants.length === 0" class="px-4 py-2 text-sm text-gray-400">Tidak ada penyewa cocok.</div>
                        </div>
                    </div>
                    @error('penyewa_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @if($tenants->isEmpty())
                        <p class="text-amber-600 text-xs mt-1">Belum ada penyewa. Tambah penyewa dulu lewat menu Data Penyewa.</p>
                    @endif
                    <p x-show="isExtension" x-cloak class="text-emerald-600 text-xs mt-1">Penyewa sudah menempati kamar ini — pembayaran dihitung sebagai perpanjangan sewa.</p>
                </div>

                {{-- Kamar: dinamis mengikuti penyewa terpilih --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kamar <span class="text-red-500">*</span></label>
                    {{-- Hidden input membawa nilai walau select di-disable (extension): select yang
                         disabled tidak ikut ter-POST, jadi kamar_id harus lewat hidden input ini. --}}
                    <input type="hidden" name="kamar_id" :value="kamarId">
                    <select x-model="kamarId" @change="recalc()" :disabled="isExtension"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('kamar_id') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition disabled:bg-gray-100">
                        <option value="">-- Pilih Kamar --</option>
                        <template x-for="r in filteredRooms" :key="r.id">
                            <option :value="r.id" x-text="r.label + (r.capacity > 1 ? ' (' + r.occupants + '/' + r.capacity + ' terisi)' : '') + ' · Rp ' + r.price.toLocaleString('id-ID') + (r.capacity > 1 ? '/org' : '')"></option>
                        </template>
                    </select>
                    <p x-show="!penyewaId" class="text-gray-400 text-xs mt-1">Pilih penyewa dulu untuk melihat kamar yang tersedia.</p>
                    <p x-show="penyewaId && filteredRooms.length === 0" x-cloak class="text-amber-600 text-xs mt-1">Tak ada kamar dengan slot kosong.</p>
                    @error('kamar_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="amount" class="block text-sm font-bold text-gray-700 mb-2">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" id="amount" name="amount" x-model="amount" min="1" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('amount') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        <p class="text-gray-400 text-xs mt-1">Otomatis = harga/orang × durasi (boleh diubah).</p>
                        @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="duration" class="block text-sm font-bold text-gray-700 mb-2">Durasi (bulan) <span class="text-red-500">*</span></label>
                        <input type="number" id="duration" name="duration" x-model="duration" @input="recalc()" min="1" max="24" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('duration') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        @error('duration')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-bold text-gray-700 mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <select id="payment_method" name="payment_method" required
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('payment_method') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="manual_transfer" {{ old('payment_method') == 'manual_transfer' ? 'selected' : '' }}>Transfer Manual</option>
                        <option value="edc" {{ old('payment_method') == 'edc' ? 'selected' : '' }}>EDC / Mesin Kartu</option>
                    </select>
                    @error('payment_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="payment_proof" class="block text-sm font-bold text-gray-700 mb-2">Bukti Pembayaran <span class="text-red-500">*</span></label>
                    <input type="file" id="payment_proof" name="payment_proof" accept="image/*" required
                        class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-bold hover:file:bg-emerald-100">
                    <p class="text-gray-400 text-xs mt-1">Gambar (JPG/PNG), maks 2MB.</p>
                    @error('payment_proof')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">Catatan <span class="text-gray-400">(opsional)</span></label>
                    <textarea id="notes" name="notes" rows="2"
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('notes') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">{{ old('notes') }}</textarea>
                    @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition">
                        SIMPAN TRANSAKSI
                    </button>
                    <a href="{{ route('owner.verifikasi-transaksi') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-xl text-center transition">BATAL</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function txForm(cfg) {
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
                    // Pre-select penyewa (dari ?penyewa= atau old()).
                    if (this.penyewaId) {
                        const t = this.tenants.find(x => String(x.id) === String(this.penyewaId));
                        if (t) { this.tenantQuery = t.name; this.applyTenant(t, !!this.kamarId); }
                    }
                },

                get selectedTenant() {
                    return this.tenants.find(t => String(t.id) === String(this.penyewaId)) || null;
                },
                // Penyewa sudah punya kamar aktif -> skenario perpanjangan.
                get isExtension() {
                    return !!(this.selectedTenant && this.selectedTenant.kamar_id);
                },
                get filteredTenants() {
                    const q = this.tenantQuery.toLowerCase().trim();
                    return this.tenants.filter(t => !q || t.name.toLowerCase().includes(q)).slice(0, 50);
                },
                // Belum punya kamar -> hanya kamar yang punya slot kosong.
                // Sudah punya kamar -> hanya kamarnya (perpanjangan), auto-terpilih.
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
                    if (t.kamar_id) {
                        this.kamarId = String(t.kamar_id); // perpanjangan: kunci ke kamarnya
                    } else if (!keepKamar) {
                        this.kamarId = '';
                    }
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
@endsection
