{{-- Edit biodata penyewa oleh owner/admin. Layout ditentukan controller ($layout).
     Memakai partial field yang sama dengan form publik self-service. --}}
@extends($layout)

@section('content')
    <div class="max-w-2xl mx-auto py-2">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Biodata Penyewa</h1>
                <p class="text-sm text-gray-500">{{ $penyewa->name }}</p>
            </div>
            <a href="{{ $backRoute }}" class="text-sm font-semibold text-gray-500 hover:text-emerald-600">← Kembali</a>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">Ada isian yang belum benar (mis. email sudah dipakai / ukuran file). Periksa kembali.</div>
        @endif

        <form action="{{ $updateRoute }}" method="POST" enctype="multipart/form-data"
              x-data="{ type: '{{ old('tenant_type', $profile->tenant_type ?? 'mahasiswa') }}' }" class="space-y-6">
            @csrf
            @method('PUT')

            @include('partials.biodata-fields', ['p' => $profile, 'docTypes' => $docTypes])

            <div class="flex gap-3 pb-8">
                <a href="{{ $backRoute }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition">Batal</a>
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-md transition">Simpan Biodata</button>
            </div>
        </form>
    </div>
@endsection
