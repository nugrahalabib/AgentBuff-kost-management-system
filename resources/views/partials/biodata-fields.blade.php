{{-- Field biodata penyewa — dipakai form publik (self-service) & edit owner/admin.
     Butuh: $p (profil Penyewa), $docTypes (array slug dokumen).
     Ancestor form harus punya Alpine x-data="{ type: '...' }" (toggle mahasiswa). --}}
@php
    $inputCls = 'w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm';
    // Padding lebih rapat untuk 3 dropdown tanggal agar muat & tak terpotong di layar HP.
    $dateCls = 'w-full px-2 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 text-sm';
    $docLabels = [
        'ktp' => 'Foto KTP', 'kartu_mahasiswa' => 'Kartu Mahasiswa', 'ktp_ortu' => 'KTP Orang Tua/Wali',
        'kartu_keluarga' => 'Kartu Keluarga', 'pas_foto' => 'Pas Foto', 'surat_pernyataan' => 'Surat Pernyataan',
    ];
    $docs = $p->documents ?? [];
    $bd = $p->birth_date;
    $selEmail = $p->user && ! $p->user->hasPlaceholderEmail() ? $p->user->email : '';
@endphp

{{-- DATA PRIBADI --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 space-y-4">
    <h2 class="font-bold text-gray-800 border-b border-gray-100 pb-2">Data Pribadi</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Email <span class="text-gray-400 font-normal">(opsional)</span></label>
            <input type="email" name="email" value="{{ old('email', $selEmail) }}" class="{{ $inputCls }}" placeholder="nama@email.com">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">No. HP / WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone', $p->phone) }}" class="{{ $inputCls }}" placeholder="08xxxxxxxxxx">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
            <select name="tenant_type" x-model="type" class="{{ $inputCls }}">
                <option value="mahasiswa">Mahasiswa</option>
                <option value="non_mahasiswa">Non-Mahasiswa / Pekerja</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">No. KTP (NIK)</label>
            <input type="text" name="id_card_number" value="{{ old('id_card_number', $p->id_card_number) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Tempat Lahir</label>
            <input type="text" name="birth_place" value="{{ old('birth_place', $p->birth_place) }}" class="{{ $inputCls }}">
        </div>
        <div x-show="type === 'non_mahasiswa'" x-cloak>
            <label class="block text-sm font-bold text-gray-700 mb-1">Pekerjaan</label>
            <input type="text" name="occupation" value="{{ old('occupation', $p->occupation) }}" class="{{ $inputCls }}">
        </div>
    </div>

    {{-- Tanggal lahir: 3 dropdown (tahun mudah dipilih/dicari) --}}
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Lahir</label>
        <div class="grid grid-cols-3 gap-2">
            <select name="birth_day" class="{{ $dateCls }}">
                <option value="">Tgl</option>
                @for($d = 1; $d <= 31; $d++)
                    <option value="{{ $d }}" {{ (int) old('birth_day', $bd?->day) === $d ? 'selected' : '' }}>{{ $d }}</option>
                @endfor
            </select>
            <select name="birth_month" class="{{ $dateCls }}">
                <option value="">Bulan</option>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $mn)
                    <option value="{{ $i + 1 }}" {{ (int) old('birth_month', $bd?->month) === $i + 1 ? 'selected' : '' }}>{{ $mn }}</option>
                @endforeach
            </select>
            <select name="birth_year" class="{{ $dateCls }}">
                <option value="">Tahun</option>
                @for($y = (int) date('Y'); $y >= 1940; $y--)
                    <option value="{{ $y }}" {{ (int) old('birth_year', $bd?->year) === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Asal</label>
        <textarea name="address" rows="2" class="{{ $inputCls }}">{{ old('address', $p->address) }}</textarea>
    </div>

    {{-- Data mahasiswa --}}
    <div x-show="type === 'mahasiswa'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Universitas</label>
            <input type="text" name="university" value="{{ old('university', $p->university) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">NIM</label>
            <input type="text" name="student_card_number" value="{{ old('student_card_number', $p->student_card_number) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Fakultas</label>
            <input type="text" name="faculty" value="{{ old('faculty', $p->faculty) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Jurusan</label>
            <input type="text" name="major" value="{{ old('major', $p->major) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Angkatan</label>
            <input type="text" name="enrollment_year" value="{{ old('enrollment_year', $p->enrollment_year) }}" class="{{ $inputCls }}" placeholder="Contoh: 2023">
        </div>
    </div>
</div>

{{-- DATA WALI --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 space-y-4">
    <h2 class="font-bold text-gray-800 border-b border-gray-100 pb-2">Data Wali / Kontak Darurat</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Wali</label>
            <input type="text" name="guardian_name" value="{{ old('guardian_name', $p->guardian_name) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">No. HP Wali</label>
            <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $p->guardian_phone) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Telp Rumah Wali</label>
            <input type="text" name="guardian_home_phone" value="{{ old('guardian_home_phone', $p->guardian_home_phone) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">NIK Wali</label>
            <input type="text" name="guardian_id_card_number" value="{{ old('guardian_id_card_number', $p->guardian_id_card_number) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Pekerjaan Wali</label>
            <input type="text" name="guardian_occupation" value="{{ old('guardian_occupation', $p->guardian_occupation) }}" class="{{ $inputCls }}">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Wali</label>
            <input type="text" name="guardian_address" value="{{ old('guardian_address', $p->guardian_address) }}" class="{{ $inputCls }}">
        </div>
    </div>
</div>

{{-- DOKUMEN --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 space-y-4">
    <h2 class="font-bold text-gray-800 border-b border-gray-100 pb-2">Dokumen</h2>
    <p class="text-xs text-gray-500">Upload foto/scan (JPG, PNG, atau PDF). Foto besar otomatis dikecilkan. Biarkan kosong jika tak ingin mengganti.</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($docTypes as $type)
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">{{ $docLabels[$type] ?? $type }}</label>
                <input type="file" data-auto-compress name="documents[{{ $type }}]" accept="image/*,.pdf"
                       class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-bold hover:file:bg-emerald-100">
                @if(!empty($docs[$type]))
                    <p class="text-[11px] text-emerald-600 mt-1">✓ Sudah diupload — upload lagi untuk mengganti.</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
