@extends('layouts.admin')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Kelola Konten Website') }}
            </h2>
            <p class="text-sm text-gray-500">Ubah isi halaman utama (landing page) tanpa perlu menyentuh kode.</p>
        </div>
        <a href="{{ route('welcome') }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2.5 rounded-lg shadow-sm transition whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            Lihat Halaman Website
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-8">

    @if($errors && $errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-800">
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p class="text-gray-600">Pilih bagian yang ingin kamu ubah:</p>

    <!-- Section cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        <a href="{{ route('admin.konten.edit-hero') }}"
           class="flex items-start gap-4 p-6 bg-white rounded-2xl border border-gray-200 hover:border-emerald-400 hover:shadow-md transition group">
            <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:bg-emerald-600 transition">
                <svg class="w-6 h-6 text-emerald-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">Bagian Atas (Hero)</h3>
                <p class="text-sm text-gray-500">Judul besar, deskripsi, gambar utama, dan angka statistik.</p>
            </div>
        </a>

        <a href="{{ route('admin.konten.edit-gallery') }}"
           class="flex items-start gap-4 p-6 bg-white rounded-2xl border border-gray-200 hover:border-blue-400 hover:shadow-md transition group">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-600 transition">
                <svg class="w-6 h-6 text-blue-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">Galeri Foto</h3>
                <p class="text-sm text-gray-500">Kumpulan foto kos per kategori (kamar, dapur, dll).</p>
            </div>
        </a>

        <a href="{{ route('admin.konten.edit-facilities') }}"
           class="flex items-start gap-4 p-6 bg-white rounded-2xl border border-gray-200 hover:border-amber-400 hover:shadow-md transition group">
            <div class="flex-shrink-0 w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center group-hover:bg-amber-500 transition">
                <svg class="w-6 h-6 text-amber-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">Fasilitas &amp; Keunggulan</h3>
                <p class="text-sm text-gray-500">Daftar fasilitas dengan ikon (WiFi, CCTV, dapur, dll).</p>
            </div>
        </a>

        <a href="{{ route('admin.konten.edit-contact') }}"
           class="flex items-start gap-4 p-6 bg-white rounded-2xl border border-gray-200 hover:border-red-400 hover:shadow-md transition group">
            <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-600 transition">
                <svg class="w-6 h-6 text-red-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">Informasi Kontak</h3>
                <p class="text-sm text-gray-500">Alamat, nomor WhatsApp, email, dan peta lokasi.</p>
            </div>
        </a>
    </div>

    <!-- Info Box -->
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex gap-3">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="text-sm text-emerald-900 space-y-1">
            <p>Setiap perubahan yang kamu simpan <strong>langsung tampil</strong> di halaman website.</p>
            <p>Daftar <strong>"Pilihan Tipe Kamar"</strong> diatur terpisah lewat menu <strong>Master Harga &amp; Tipe Kamar</strong>, bukan di sini.</p>
        </div>
    </div>
</div>
@endsection
