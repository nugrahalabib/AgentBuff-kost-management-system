@extends('layouts.admin')

@section('content')
@php
    $categoryLabels = [
        'tampak_depan' => 'Tampak Depan',
        'living_room'  => 'Ruang Tamu',
        'bedroom'      => 'Kamar Tidur',
        'kitchen'      => 'Dapur',
        'bathroom'     => 'Kamar Mandi',
        'outdoor'      => 'Area Outdoor',
    ];
@endphp

<div class="max-w-3xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('admin.konten.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-emerald-600 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Kelola Konten
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Galeri Foto</h1>
            <p class="text-gray-500 mt-1">Tiap kumpulan foto tampil sebagai carousel di halaman utama.</p>
        </div>
        <a href="{{ route('welcome') }}#galeri" target="_blank" rel="noopener"
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

    <!-- Tambah Galeri -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <h2 class="font-bold text-gray-900 mb-4">Tambah Kumpulan Foto</h2>
        <form action="{{ route('admin.konten.store-gallery') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">Pilih kategori</option>
                        @foreach($categoryLabels as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Tampak Depan"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="text" name="description" placeholder="Deskripsi singkat"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-emerald-500 hover:bg-emerald-50 transition" id="galleryDropZone">
                    <input type="file" name="images[]" accept="image/*" multiple required class="hidden" id="galleryImageInput">
                    <p class="text-gray-600 text-sm font-medium">Klik atau seret foto ke sini</p>
                    <p class="text-xs text-gray-400 mt-1" id="imageCount">Bisa pilih beberapa foto sekaligus</p>
                </div>
            </div>

            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-lg transition">
                Simpan
            </button>
        </form>
    </div>

    <!-- Daftar Galeri -->
    <h2 class="font-bold text-gray-900 mb-3">Galeri Saat Ini ({{ $galleries->count() }})</h2>
    <div class="space-y-4">
        @forelse($galleries as $gallery)
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div class="min-w-0">
                        <h3 class="font-bold text-gray-900">{{ $gallery->title }}</h3>
                        <span class="inline-block mt-1 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-100 rounded px-2 py-0.5">
                            {{ $categoryLabels[$gallery->category] ?? $gallery->category }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <button onclick="editGallery({{ $gallery->id }}, '{{ addslashes($gallery->title) }}', '{{ addslashes($gallery->description ?? '') }}')"
                                class="text-sm text-emerald-600 hover:text-emerald-800 font-semibold">Ubah</button>
                        <form action="{{ route('admin.konten.delete-gallery', $gallery) }}" method="POST" onsubmit="return confirm('Hapus galeri ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                        </form>
                    </div>
                </div>

                @if($gallery->images && count($gallery->images) > 0)
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach($gallery->images as $image)
                            <div class="rounded-lg overflow-hidden h-24 bg-gray-100">
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Belum ada foto</p>
                @endif
            </div>
        @empty
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center">
                <p class="text-gray-500">Belum ada galeri. Tambahkan di atas.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 p-6">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-gray-900">Ubah Galeri</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                <input type="text" name="title" id="editTitle" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <input type="text" name="description" id="editDescription"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tambah foto baru <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2">
                <p class="text-xs text-gray-400 mt-1">Foto baru ditambahkan ke yang sudah ada.</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-lg transition">Simpan Perubahan</button>
                <button type="button" onclick="closeEditModal()" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-lg transition">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    const dropZone = document.getElementById('galleryDropZone');
    const imageInput = document.getElementById('galleryImageInput');
    const imageCount = document.getElementById('imageCount');

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
        updateImageCount();
    });
    imageInput.addEventListener('change', updateImageCount);

    function updateImageCount() {
        imageCount.textContent = imageInput.files.length
            ? imageInput.files.length + ' foto dipilih'
            : 'Bisa pilih beberapa foto sekaligus';
    }

    function editGallery(id, title, description) {
        document.getElementById('editTitle').value = title;
        document.getElementById('editDescription').value = description;
        document.getElementById('editForm').action = `/admin/konten/gallery/${id}`;
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
@endsection
