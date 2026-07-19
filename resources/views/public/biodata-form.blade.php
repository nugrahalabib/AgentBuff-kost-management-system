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

            @include('partials.biodata-fields', ['p' => $penyewa, 'docTypes' => $docTypes])


            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-md transition">
                Simpan Biodata
            </button>
            <p class="text-center text-xs text-gray-400 pb-6">Data ini hanya digunakan oleh pengelola kos untuk keperluan administrasi.</p>
        </form>
    </div>
</body>
</html>
