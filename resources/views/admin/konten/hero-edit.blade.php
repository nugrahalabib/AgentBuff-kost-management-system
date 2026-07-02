@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('admin.konten.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-emerald-600 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Kelola Konten
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Bagian Atas Halaman (Hero)</h1>
            <p class="text-gray-500 mt-1">Bagian pertama yang dilihat pengunjung.</p>
        </div>
        <a href="{{ route('welcome') }}" target="_blank" rel="noopener"
           class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 hover:text-emerald-700 border border-emerald-200 hover:border-emerald-300 rounded-lg px-3 py-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            Lihat Halaman
        </a>
    </div>

    @if($errors && $errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-red-800">
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.konten.update-hero') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <!-- Teks Utama -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
            <h2 class="font-bold text-gray-900">Teks Utama</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Badge kecil <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="text" name="badge" value="{{ $items->firstWhere('item_type', 'badge')?->value ?? '✨ Hunian Eksklusif & Nyaman' }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                       placeholder="✨ Hunian Eksklusif & Nyaman">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Utama <span class="text-red-500">*</span></label>
                <textarea name="title" rows="2" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                       placeholder="Temukan Kenyamanan Seperti di Rumah Sendiri.">{{ $items->firstWhere('item_type', 'title')?->value ?? 'Temukan Kenyamanan Seperti di Rumah Sendiri.' }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kata yang diwarnai hijau <span class="text-red-500">*</span></label>
                <input type="text" name="subtitle" required value="{{ $items->firstWhere('item_type', 'subtitle')?->value ?? 'Rumah Sendiri.' }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                       placeholder="Rumah Sendiri.">
                <p class="text-xs text-gray-500 mt-1">Salin sebagian kata dari Judul Utama — kata itu yang akan berwarna hijau.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="description" rows="3" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                       placeholder="Fasilitas lengkap, lokasi strategis, dan keamanan 24 jam.">{{ $items->firstWhere('item_type', 'description')?->value ?? 'Fasilitas lengkap, lokasi strategis, dan keamanan 24 jam. Pilihan tepat untuk mahasiswa dan profesional muda.' }}</textarea>
            </div>
        </div>

        <!-- Statistik -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-bold text-gray-900">Statistik</h2>
            <p class="text-xs text-gray-500 mt-0.5 mb-4">Tiga angka ringkas di bawah tombol. Boleh dikosongkan.</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach([1 => ['200+', 'Unit Kamar'], 2 => ['100%', 'Aman & CCTV'], 3 => ['4.9', 'Rating Penghuni']] as $i => $def)
                    <div class="space-y-2">
                        <input type="text" name="stat_{{ $i }}_value"
                               value="{{ $items->firstWhere('item_type', 'stat_'.$i.'_value')?->value ?? $def[0] }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-semibold"
                               placeholder="{{ $def[0] }}">
                        <input type="text" name="stat_{{ $i }}_label"
                               value="{{ $items->firstWhere('item_type', 'stat_'.$i.'_label')?->value ?? $def[1] }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm"
                               placeholder="{{ $def[1] }}">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Gambar -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h2 class="font-bold text-gray-900 mb-3">Gambar Utama</h2>

            @php $currentHeroImage = $items->firstWhere('item_type', 'hero_image_path')?->value; @endphp
            <div class="flex flex-col sm:flex-row gap-4 items-start">
                @if($currentHeroImage)
                    <img src="{{ asset('storage/' . $currentHeroImage) }}" alt="Gambar hero" class="w-40 h-28 object-cover rounded-lg border border-gray-200 flex-shrink-0">
                @endif
                <div class="flex-1 w-full">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-emerald-500 hover:bg-emerald-50 transition" id="imageDropZone">
                        <input type="file" name="hero_image" accept="image/*" class="hidden" id="heroImageInput">
                        <p class="text-gray-600 text-sm font-medium" id="dropZoneText">Klik atau seret gambar ke sini untuk {{ $currentHeroImage ? 'mengganti' : 'mengunggah' }}</p>
                        <p class="text-xs text-gray-400 mt-1">PNG / JPG, maks 5MB</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-100">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label di atas gambar</label>
                    <input type="text" name="verify_badge_title"
                           value="{{ $items->firstWhere('item_type', 'verify_badge_title')?->value ?? 'Terverifikasi' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           placeholder="Terverifikasi">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan label</label>
                    <input type="text" name="verify_badge_desc"
                           value="{{ $items->firstWhere('item_type', 'verify_badge_desc')?->value ?? 'Kebersihan & Fasilitas Terjamin' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                           placeholder="Kebersihan & Fasilitas Terjamin">
                </div>
            </div>
        </div>

        <!-- Save -->
        <div class="flex items-center gap-3 pt-1">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-lg shadow-sm transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.konten.index') }}" class="text-gray-600 hover:text-gray-900 font-semibold py-3 px-4">Batal</a>
        </div>
    </form>
</div>

<script>
    const dropZone = document.getElementById('imageDropZone');
    const imageInput = document.getElementById('heroImageInput');
    const dropZoneText = document.getElementById('dropZoneText');

    dropZone.addEventListener('click', () => imageInput.click());
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-emerald-500', 'bg-emerald-50');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-emerald-500', 'bg-emerald-50');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        imageInput.files = e.dataTransfer.files;
        dropZone.classList.remove('border-emerald-500', 'bg-emerald-50');
        if (imageInput.files.length) dropZoneText.textContent = imageInput.files[0].name;
    });
    imageInput.addEventListener('change', () => {
        if (imageInput.files.length) dropZoneText.textContent = imageInput.files[0].name;
    });
</script>
@endsection
