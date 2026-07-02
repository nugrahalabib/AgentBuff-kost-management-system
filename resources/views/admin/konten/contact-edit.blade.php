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
            <h1 class="text-2xl font-bold text-gray-900">Informasi Kontak</h1>
            <p class="text-gray-500 mt-1">Isi data yang ingin ditampilkan di bagian "Lokasi &amp; Kontak". Kosongkan kolom yang tidak ingin ditampilkan.</p>
        </div>
        <a href="{{ route('welcome') }}#kontak" target="_blank" rel="noopener"
           class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 hover:text-emerald-700 border border-emerald-200 hover:border-emerald-300 rounded-lg px-3 py-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            Lihat Halaman
        </a>
    </div>

    @if($errors && $errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-red-800">
            <p class="font-semibold mb-1">Mohon periksa kembali:</p>
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.konten.update-contact-all') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Alamat -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <label for="address" class="flex items-center gap-2 text-sm font-bold text-gray-900 mb-1">
                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </span>
                Alamat
            </label>
            <input type="text" name="address" id="address"
                   value="{{ old('address', $contacts->get('address')?->contact_value) }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                   placeholder="Contoh: BPI Blok S No.29A, Ngaliyan, Semarang">
            <p class="text-xs text-gray-500 mt-1.5">Alamat singkat yang ditampilkan ke pengunjung.</p>
        </div>

        <!-- WhatsApp -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <label for="phone" class="flex items-center gap-2 text-sm font-bold text-gray-900 mb-1">
                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-green-100 text-green-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.481 0 1.461 1.063 2.875 1.211 3.074.149.198 2.095 3.2 5.076 4.487 2.982 1.288 2.982.859 3.526.809.544-.05 1.758-.718 2.006-1.413.248-.695.248-1.29.173-1.414z"></path></svg>
                </span>
                Nomor WhatsApp
            </label>
            <input type="text" name="phone" id="phone"
                   value="{{ old('phone', $contacts->get('phone')?->contact_value) }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                   placeholder="Contoh: 0811 2702 889">
            <p class="text-xs text-gray-500 mt-1.5">Pengunjung bisa langsung klik untuk chat WhatsApp. Cukup tulis nomornya saja.</p>
        </div>

        <!-- Email -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <label for="email" class="flex items-center gap-2 text-sm font-bold text-gray-900 mb-1">
                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-100 text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </span>
                Email <span class="text-xs font-normal text-gray-400">(opsional)</span>
            </label>
            <input type="email" name="email" id="email"
                   value="{{ old('email', $contacts->get('email')?->contact_value) }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                   placeholder="Contoh: info@mutiara27.com">
            <p class="text-xs text-gray-500 mt-1.5">Biarkan kosong kalau tidak ingin menampilkan email.</p>
        </div>

        <!-- Peta -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <label for="maps_embed" class="flex items-center gap-2 text-sm font-bold text-gray-900 mb-1">
                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-100 text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7.882m0 12.236l6-3m-6 3V7.882m6 9.236l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13.118V4m0 0L9 7.882"></path></svg>
                </span>
                Peta Lokasi <span class="text-xs font-normal text-gray-400">(opsional)</span>
            </label>
            <textarea name="maps_embed" id="maps_embed" rows="3"
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-mono text-xs"
                      placeholder="Tempel kode peta di sini...">{{ old('maps_embed', $contacts->get('maps_embed')?->contact_value) }}</textarea>
            <div class="mt-2 bg-amber-50 border border-amber-100 rounded-lg p-3 text-xs text-amber-900 leading-relaxed">
                <p class="font-semibold mb-1">Cara mendapatkan kode peta:</p>
                <ol class="list-decimal list-inside space-y-0.5">
                    <li>Buka <strong>Google Maps</strong>, cari lokasi kos.</li>
                    <li>Klik <strong>Bagikan</strong> → pilih tab <strong>Sematkan peta</strong>.</li>
                    <li>Klik <strong>SALIN HTML</strong>, lalu tempel di kotak di atas.</li>
                </ol>
                <p class="mt-1.5 text-amber-700">Kalau dikosongkan, peta akan otomatis memakai lokasi bawaan Mutiara27.</p>
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
@endsection
