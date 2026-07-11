@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">Input Transaksi Manual</h2>
            <p class="text-sm text-gray-500">Catat pembayaran tunai/transfer manual & tempatkan penyewa ke kamar.</p>
        </div>
        <a href="{{ route('owner.verifikasi-transaksi') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-bold py-3 px-6 rounded-xl shadow-md transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            KEMBALI
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-lg text-gray-800">Detail Pembayaran</h3>
            </div>

            <form action="{{ route('owner.transaksi.store-manual') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="penyewa_id" class="block text-sm font-bold text-gray-700 mb-2">Penyewa <span class="text-red-500">*</span></label>
                    <select id="penyewa_id" name="penyewa_id" required
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('penyewa_id') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        <option value="">-- Pilih Penyewa --</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}" {{ old('penyewa_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                    @error('penyewa_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @if($tenants->isEmpty())
                        <p class="text-amber-600 text-xs mt-1">Belum ada penyewa. Tambah penyewa dulu lewat menu Data Penyewa.</p>
                    @endif
                </div>

                <div>
                    <label for="kamar_id" class="block text-sm font-bold text-gray-700 mb-2">Kamar <span class="text-red-500">*</span></label>
                    <select id="kamar_id" name="kamar_id" required
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('kamar_id') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="amount" class="block text-sm font-bold text-gray-700 mb-2">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" id="amount" name="amount" value="{{ old('amount') }}" min="1" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('amount') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="duration" class="block text-sm font-bold text-gray-700 mb-2">Durasi (bulan) <span class="text-red-500">*</span></label>
                        <input type="number" id="duration" name="duration" value="{{ old('duration', 1) }}" min="1" max="24" required
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
                    <label for="payment_proof" class="block text-sm font-bold text-gray-700 mb-2">Bukti Pembayaran <span class="text-gray-400">(opsional)</span></label>
                    <input type="file" id="payment_proof" name="payment_proof" accept="image/*"
                        class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-bold hover:file:bg-emerald-100">
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
        // Auto-isi nominal dari harga kamar × durasi.
        function recalc() {
            const room = document.getElementById('kamar_id');
            const price = parseInt(room.options[room.selectedIndex]?.getAttribute('data-price') || '0', 10);
            const dur = parseInt(document.getElementById('duration').value || '1', 10);
            if (price > 0) document.getElementById('amount').value = price * dur;
        }
        document.getElementById('kamar_id').addEventListener('change', recalc);
        document.getElementById('duration').addEventListener('input', recalc);
    </script>
@endsection
