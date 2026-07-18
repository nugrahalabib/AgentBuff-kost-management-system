<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Isi Biodata Penyewa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">
    @php
        $p = $penyewa;
        $docLabels = [
            'ktp' => 'Foto KTP', 'kartu_mahasiswa' => 'Kartu Mahasiswa', 'ktp_ortu' => 'KTP Orang Tua/Wali',
            'kartu_keluarga' => 'Kartu Keluarga', 'pas_foto' => 'Pas Foto', 'surat_pernyataan' => 'Surat Pernyataan',
        ];
        $docs = $p->documents ?? [];
        $inputCls = 'w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm';
    @endphp

    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-6">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-600 text-white text-2xl shadow-md mb-3">🏠</span>
            <h1 class="text-2xl font-bold text-gray-900">Formulir Biodata Penyewa</h1>
            <p class="text-gray-500 text-sm mt-1">Halo <span class="font-semibold text-emerald-700">{{ $p->user->name ?? 'Penyewa' }}</span>, lengkapi data & dokumenmu di bawah ini. Data langsung tersimpan untuk pengelola kos.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">Ada isian yang belum benar (mis. format tanggal atau ukuran file). Periksa kembali.</div>
        @endif

        <form action="{{ route('public.biodata.update', ['token' => $token]) }}" method="POST" enctype="multipart/form-data"
              x-data="{ type: '{{ old('tenant_type', $p->tenant_type ?? 'mahasiswa') }}' }" class="space-y-6">
            @csrf

            {{-- DATA PRIBADI --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="font-bold text-gray-800 border-b border-gray-100 pb-2">Data Pribadi</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', $p->birth_place) }}" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', optional($p->birth_date)->format('Y-m-d')) }}" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">No. KTP (NIK)</label>
                        <input type="text" name="id_card_number" value="{{ old('id_card_number', $p->id_card_number) }}" class="{{ $inputCls }}">
                    </div>
                    <div x-show="type === 'non_mahasiswa'" x-cloak>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Pekerjaan</label>
                        <input type="text" name="occupation" value="{{ old('occupation', $p->occupation) }}" class="{{ $inputCls }}">
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="font-bold text-gray-800 border-b border-gray-100 pb-2">Dokumen</h2>
                <p class="text-xs text-gray-500">Upload foto/scan (JPG, PNG, atau PDF). Foto besar otomatis dikecilkan. Biarkan kosong jika sudah pernah diupload & tak ingin mengganti.</p>
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

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-md transition">
                Simpan Biodata
            </button>
            <p class="text-center text-xs text-gray-400 pb-6">Data ini hanya digunakan oleh pengelola kos untuk keperluan administrasi.</p>
        </form>
    </div>
</body>
</html>
