@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Pengaturan Sistem') }}
            </h2>
            <p class="text-sm text-gray-500">Pusat kendali strategi harga, kebijakan bisnis, dan keamanan akun.</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-1.5 flex flex-wrap gap-2" id="settings-tabs">
            <button onclick="switchTab('pricing')" id="btn-pricing" class="flex-1 py-3 px-6 rounded-xl text-sm font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 shadow-sm transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Harga & Tipe
            </button>
            <button onclick="switchTab('rules')" id="btn-rules" class="flex-1 py-3 px-6 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 border border-transparent transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Pengaturan
            </button>
            <button onclick="switchTab('account')" id="btn-account" class="flex-1 py-3 px-6 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 border border-transparent transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Profil Pemilik
            </button>
        </div>

        <div id="content-pricing" class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Master Tipe & Harga Kamar</h3>
                        <p class="text-xs text-gray-500">Perubahan harga di sini akan otomatis mengupdate tagihan bulan depan.</p>
                    </div>
                    <button onclick="openAddRoomTypeModal()" class="text-emerald-700 text-sm font-bold flex items-center hover:underline transition">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Tipe Baru
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-500 font-bold uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Foto</th>
                                <th class="px-6 py-4">Nama Tipe</th>
                                <th class="px-6 py-4">Kapasitas</th>
                                <th class="px-6 py-4">Fasilitas</th>
                                <th class="px-6 py-4">Harga/Bulan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tipeKamar as $type)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        @if($type->image_path)
                                            <img src="{{ asset('storage/' . $type->image_path) }}" alt="{{ $type->name }}" class="w-16 h-12 object-cover rounded-lg">
                                        @else
                                            <div class="w-16 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $type->name }}</td>
                                    <td class="px-6 py-4 text-gray-600 text-sm">
                                        {{ $type->capacity }} Orang
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">
                                        @if($type->facilities)
                                            @php
                                                $facilitiesRaw = $type->facilities;
                                                // If it's a string (legacy/bug), decode it. If it's already array (cast), use it.
                                                $facilities = is_string($facilitiesRaw) ? json_decode($facilitiesRaw, true) : $facilitiesRaw;
                                                
                                                $names = [];
                                                if(is_array($facilities)) {
                                                    foreach($facilities as $fac) {
                                                        if(is_array($fac) && isset($fac['name'])) {
                                                            $names[] = $fac['name'];
                                                        } elseif(is_string($fac)) {
                                                            $names[] = $fac;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            {{ implode(', ', $names) }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">
                                        Rp {{ number_format($type->price_per_month, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($type->status === 'active')
                                            <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-xs font-bold">Aktif</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-bold">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button 
                                            onclick="openEditRoomTypeModal(this)"
                                            data-id="{{ $type->id }}"
                                            data-name="{{ $type->name }}"
                                            data-price="{{ $type->price_per_month }}"
                                            data-status="{{ $type->status }}"
                                            data-description="{{ $type->description ?? '' }}"
                                            data-facilities="{{ $type->facilities ? json_encode($type->facilities) : '[]' }}"
                                            data-image="{{ $type->image_path ? asset('storage/' . $type->image_path) : '' }}"
                                            data-capacity="{{ $type->capacity }}"
                                            class="text-emerald-600 hover:text-emerald-800 font-bold text-xs transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('owner.room-types.destroy', $type->id) }}" method="POST" class="inline" onsubmit="confirmSubmit(event, 'Yakin ingin menghapus tipe kamar ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        Belum ada tipe kamar. <button type="button" onclick="openAddRoomTypeModal()" class="text-emerald-600 font-bold hover:underline">Tambah sekarang</button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                    <h4 class="font-bold text-red-800 mb-2">Error:</h4>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>

        <div id="content-rules" class="space-y-6 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4">Siklus Penagihan</h4>
                    <p class="text-sm text-gray-500 mb-4">Pengaturan notifikasi WA dan reminder sewa untuk tenant.</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-bold text-gray-700">Reminder Sewa Tenant (H-?)</label>
                            <p class="text-xs text-gray-500 mb-2">Tenant akan mendapat reminder di dashboard saat sewa mendekati jatuh tempo.</p>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600 font-bold">H -</span>
                                <input type="number" name="invoice_reminder_days_before" id="reminderDaysBefore" 
                                       value="{{ $businessSettings->invoice_reminder_days_before ?? 7 }}" 
                                       min="1" 
                                       class="w-24 border-gray-300 rounded-lg text-sm font-bold focus:ring-emerald-500 text-center">
                                <span class="text-gray-600">Hari sebelum jatuh tempo</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Rekening Pembayaran
                    </h4>
                    <p class="text-sm text-gray-500 mb-4">Informasi rekening yang akan ditampilkan di halaman pembayaran tenant.</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Bank</label>
                            <input type="text" id="bankName" value="{{ $businessSettings->bank_name ?? '' }}" placeholder="Contoh: BCA, Mandiri, BNI" class="w-full border-gray-300 rounded-lg text-sm font-medium focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nomor Rekening</label>
                            <input type="text" id="bankAccountNumber" value="{{ $businessSettings->bank_account_number ?? '' }}" placeholder="Contoh: 1234567890" class="w-full border-gray-300 rounded-lg text-sm font-medium focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Pemilik Rekening</label>
                            <input type="text" id="bankAccountName" value="{{ $businessSettings->bank_account_name ?? '' }}" placeholder="Contoh: PT. Kos Melati" class="w-full border-gray-300 rounded-lg text-sm font-medium focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex justify-end">
                <button onclick="saveRulesSettings()" id="rulesSaveBtn" class="bg-emerald-800 hover:bg-emerald-900 text-white text-sm font-bold py-3 px-8 rounded-xl shadow-lg transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Pengaturan
                </button>
            </div>
        </div>

        <div id="content-account" class="space-y-6 hidden">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-2">Profil Pemilik & Keamanan</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Profile Update Form -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Gedung Kos</label>
                            <input type="text" id="boardingHouseName" value="{{ $businessSettings->boarding_house_name ?? 'Kos Executive Melati' }}" class="w-full border-gray-300 rounded-lg text-sm font-medium focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Pemilik</label>
                            <input type="text" id="ownerName" value="{{ $pemilik->name }}" class="w-full border-gray-300 rounded-lg text-sm font-medium focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Login</label>
                            <input type="email" id="ownerEmail" value="{{ $pemilik->email }}" class="w-full border-gray-300 rounded-lg text-sm font-medium focus:ring-emerald-500">
                        </div>
                    </div>

                    <!-- Password Update Form -->
                    <div class="bg-red-50 p-6 rounded-xl border border-red-100 h-fit">
                        <h4 class="text-sm font-bold text-red-800 mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Ganti Password
                        </h4>
                        <div class="space-y-3">
                            <input type="password" id="currentPassword" placeholder="Password Lama" class="w-full border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                            <input type="password" id="newPassword" placeholder="Password Baru" class="w-full border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                            <input type="password" id="confirmPassword" placeholder="Konfirmasi Password Baru" class="w-full border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                            <p class="text-xs text-red-600 italic">*Kosongkan jika tidak ingin mengganti password</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button onclick="saveAccountSettings()" id="accountSaveBtn" class="bg-emerald-800 hover:bg-emerald-900 text-white text-sm font-bold py-3 px-8 rounded-xl shadow-lg transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Profil
                </button>
            </div>
        </div>

    </div>

    <!-- Icon Picker Modal -->
    <!-- ... (previous modal code) ... -->



    <!-- Modal Tambah/Edit Tipe Kamar -->
    <div id="roomTypeModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 invisible opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
                <h3 id="roomTypeModalTitle" class="font-bold text-lg text-gray-800">Tambah Tipe Kamar Baru</h3>
                <button onclick="closeRoomTypeModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="roomTypeForm" method="POST" action="{{ route('owner.room-types.store') }}" class="p-6 space-y-4" enctype="multipart/form-data">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Tipe Kamar <span class="text-red-500">*</span></label>
                    <input type="text" name="name" placeholder="Contoh: VIP AC, Standard, Ekonomi" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi <span class="text-gray-400">(Opsional)</span></label>
                    <textarea name="description" placeholder="Contoh: Kamar dengan AC dan TV" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kapasitas <span class="text-red-500">*</span></label>
                    <select name="capacity" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" required>
                        <option value="1">Single (1 Orang)</option>
                        <option value="2">Duo (2 Orang)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Untuk tipe "Duo", harga akan otomatis dibagi dua untuk setiap penghuni.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Fasilitas</label>
                    <input type="hidden" name="facilities" id="facilitiesInput">
                    
                    <!-- Dynamic List Container -->
                    <div id="facilitiesContainer" class="space-y-3 mb-3">
                        <!-- Facilities will be added here via JS -->
                    </div>

                    <button type="button" onclick="addFacility()" class="inline-flex items-center gap-2 text-sm text-emerald-600 font-bold hover:text-emerald-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Fasilitas
                    </button>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga Per Bulan <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                        <input type="number" name="price_per_month" placeholder="0" class="w-full pl-10 px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" required min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Utama <span class="text-gray-400">(Opsional, max 2MB)</span></label>
                    <div id="imagePreviewContainer" class="mb-2 hidden">
                        <img id="imagePreview" src="" alt="Preview" class="w-full h-32 object-cover rounded-lg border">
                    </div>
                    <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" onchange="previewImage(this)">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, atau WebP</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Galeri Foto <span class="text-gray-400">(Max 4 foto, masing-masing 2MB)</span></label>
                    <div id="galleryPreviewContainer" class="grid grid-cols-4 gap-2 mb-2 hidden">
                    </div>
                    <input type="file" name="gallery_images[]" id="galleryInput" accept="image/jpeg,image/png,image/webp" multiple class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" onchange="previewGallery(this)">
                    <p class="text-xs text-gray-500 mt-1">Foto tambahan untuk halaman detail kamar</p>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-lg text-sm transition">
                        Simpan
                    </button>
                    <button type="button" onclick="closeRoomTypeModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 rounded-lg text-sm transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Icon Picker Modal -->
    <div id="iconPickerModal" class="fixed inset-0 bg-black/50 z-[60] hidden transition-opacity opacity-0" onclick="closeIconPicker(event)">
        <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 transition-all scale-95 opacity-0" id="iconPickerContent">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Pilih Ikon Fasilitas</h3>
                <button onclick="closeIconPicker()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="grid grid-cols-4 gap-4 max-h-96 overflow-y-auto p-2" id="iconGrid">
                @foreach(config('facilities.icons') as $key => $icon)
                    <div onclick="selectIcon('{{ $key }}')" class="cursor-pointer flex flex-col items-center justify-center p-3 rounded-xl border border-gray-100 hover:border-emerald-500 hover:bg-emerald-50 transition group">
                        <div class="w-8 h-8 text-gray-500 group-hover:text-emerald-600 mb-2 transition">
                            {!! $icon['svg'] !!}
                        </div>
                        <span class="text-xs text-center text-gray-600 group-hover:text-emerald-700 font-medium truncate w-full">{{ $icon['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        window.facilityIcons = @json(config('facilities.icons'));
    </script>

    <script>
        let currentEditId = null;
        let currentRowId = null; // Track which row is opening the picker

        function openAddRoomTypeModal() {
            // ... (rest of function)
            currentEditId = null;
            document.getElementById('roomTypeModalTitle').textContent = 'Tambah Tipe Kamar Baru';
            document.getElementById('roomTypeForm').reset();
            document.getElementById('roomTypeForm').action = "{{ route('owner.room-types.store') }}";
            document.getElementById('roomTypeForm').method = "POST";
            
            // Remove method override for POST
            const methodInput = document.getElementById('roomTypeMethod');
            if (methodInput) methodInput.remove();
            
            // Reset image preview
            document.getElementById('imagePreviewContainer').classList.add('hidden');
            document.getElementById('imagePreview').src = '';
            
            // Clear facilities list and add one empty row by default
            document.getElementById('facilitiesContainer').innerHTML = '';
            addFacility(); 

            const modal = document.getElementById('roomTypeModal');
            const content = document.getElementById('roomTypeModalContent');
            
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.remove('invisible', 'opacity-0');
                content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeRoomTypeModal(e) {
            if (e && e.target !== e.currentTarget && e.target.closest('#roomTypeModalContent')) return;
            
            const modal = document.getElementById('roomTypeModal');
            const content = document.getElementById('roomTypeModalContent');
            
            modal.classList.add('opacity-0');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('invisible');
                modal.style.display = 'none';
                currentEditId = null;
            }, 300);
        }

        function openEditRoomTypeModal(btn) {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const price = btn.dataset.price;
            const status = btn.dataset.status;
            const description = btn.dataset.description;
            const facilities = JSON.parse(btn.dataset.facilities);
            const imagePath = btn.dataset.image;
            const capacity = btn.dataset.capacity;

            currentEditId = id;
            document.getElementById('roomTypeModalTitle').textContent = 'Edit Tipe Kamar';
            document.getElementById('roomTypeForm').name.value = name;
            document.getElementById('roomTypeForm').price_per_month.value = price;
            document.getElementById('roomTypeForm').status.value = status;
            document.getElementById('roomTypeForm').capacity.value = capacity;
            document.getElementById('roomTypeForm').description.value = description;
            
            // Populate facilities list
            const container = document.getElementById('facilitiesContainer');
            container.innerHTML = '';
            
            try {
                let facilitiesData = facilities;
                // If it's a string, try to parse it. If simple string (comma separated), convert to objects
                if (typeof facilities === 'string') {
                    // Check if it looks like JSON
                    if (facilities.trim().startsWith('[')) {
                         facilitiesData = JSON.parse(facilities);
                    } else if (facilities.trim() !== '') { // Only process if not empty string
                        // Legacy format: converting "A, B" to objects
                        facilitiesData = facilities.split(',').map(f => ({ name: f.trim(), icon: 'star' }));
                    } else {
                        facilitiesData = []; // Empty string means no facilities
                    }
                }
                
                if (Array.isArray(facilitiesData) && facilitiesData.length > 0) {
                    facilitiesData.forEach(fac => {
                        // Handle legacy simple string arrays ["WiFi", "AC"]
                        if (typeof fac === 'string') {
                             addFacility(fac, 'star'); 
                        } else {
                             addFacility(fac.name, fac.icon);
                        }
                    });
                } else {
                    addFacility(); // Add one empty row if no facilities or parsing failed
                }
            } catch (e) {
                console.error("Error parsing facilities:", e);
                addFacility(); // Add one empty row if parsing failed
            }

            document.getElementById('roomTypeForm').action = "{{ route('owner.room-types.update', ':id') }}".replace(':id', id);
            
            // Add method override for PATCH
            let methodInput = document.getElementById('roomTypeMethod');
            if (!methodInput) {
                const form = document.getElementById('roomTypeForm');
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                methodInput.id = 'roomTypeMethod';
                form.appendChild(methodInput);
            }
            
             if (imagePath) {
                const preview = document.getElementById('imagePreview');
                preview.src = imagePath;
                document.getElementById('imagePreviewContainer').classList.remove('hidden');
            } else {
                document.getElementById('imagePreviewContainer').classList.add('hidden');
            }

            const modal = document.getElementById('roomTypeModal');
            const content = document.getElementById('roomTypeModalContent');
            
            modal.style.display = 'flex'; // Ensure flex display
            // Small timeout to allow display to apply before opacity transition
            setTimeout(() => {
                modal.classList.remove('invisible', 'opacity-0');
                content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        // Facilities List Logic
        function addFacility(name = '', iconKey = 'star') {
            const container = document.getElementById('facilitiesContainer');
            const rowId = 'fac_row_' + Date.now() + Math.random().toString(36).substr(2, 9);
            
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-center group';
            div.id = rowId;

            // Get the SVG for the iconKey from the global config
            let iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>'; // Default star icon
            if (window.facilityIcons && window.facilityIcons[iconKey]) {
                iconSvg = window.facilityIcons[iconKey].svg;
            }

            div.innerHTML = `
                <button type="button" onclick="openIconPicker('${rowId}')" class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-500 hover:border-emerald-500 hover:text-emerald-600 transition" id="btn_${rowId}">
                    ${iconSvg}
                </button>
                <input type="hidden" class="facility-icon-input" value="${iconKey}">
                <input type="text" class="facility-name-input w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm" placeholder="Nama Fasilitas" value="${name}" required>
                <button type="button" onclick="removeFacility('${rowId}')" class="text-gray-400 hover:text-red-500 transition p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            `;
            container.appendChild(div);
        }

        function removeFacility(rowId) {
            document.getElementById(rowId).remove();
        }

        // Icon Picker Logic
        function openIconPicker(rowId) {
            currentRowId = rowId;
            const modal = document.getElementById('iconPickerModal');
            const content = document.getElementById('iconPickerContent');
            modal.classList.remove('hidden', 'opacity-0'); // Remove hidden and opacity-0
            content.classList.remove('scale-95', 'opacity-0'); // Remove scale-95 and opacity-0
            modal.style.pointerEvents = 'auto';
        }

        function closeIconPicker(e) {
            if (e && e.target !== e.currentTarget && e.target.closest('#iconPickerContent')) return;
            const modal = document.getElementById('iconPickerModal');
            const content = document.getElementById('iconPickerContent');
            modal.classList.add('opacity-0');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.pointerEvents = 'none';
            }, 300);
        }

        function selectIcon(key) {
            if (currentRowId && window.facilityIcons[key]) {
                const row = document.getElementById(currentRowId);
                row.querySelector('.facility-icon-input').value = key;
                
                const svg = window.facilityIcons[key].svg;
                const button = row.querySelector('button');
                button.innerHTML = svg;
                
                // Ensure consistent sizing for the new SVG
                const svgEl = button.querySelector('svg');
                if(svgEl) {
                    svgEl.classList.add('w-5', 'h-5');
                }
            }
            closeIconPicker();
        }

        // Form Submit Handler
        document.getElementById('roomTypeForm').addEventListener('submit', function(e) {
            const container = document.getElementById('facilitiesContainer');
            const rows = container.querySelectorAll('.group');
            const facilities = [];
            
            rows.forEach(row => {
                const nameInput = row.querySelector('.facility-name-input');
                const iconInput = row.querySelector('.facility-icon-input');
                
                if (nameInput && iconInput && nameInput.value.trim()) {
                    facilities.push({ name: nameInput.value.trim(), icon: iconInput.value });
                }
            });
            
            document.getElementById('facilitiesInput').value = JSON.stringify(facilities);
        });

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                container.classList.add('hidden');
                preview.src = '';
            }
        }

        function previewGallery(input) {
            const container = document.getElementById('galleryPreviewContainer');
            container.innerHTML = '';
            
            if (input.files && input.files.length > 0) {
                container.classList.remove('hidden');
                const files = Array.from(input.files).slice(0, 4); // Max 4 images
                
                files.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-16 object-cover rounded-lg border';
                        container.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                container.classList.add('hidden');
            }
        }

        function closeRoomTypeModal() {
            const modal = document.getElementById('roomTypeModal');
            modal.classList.add('invisible', 'opacity-0');
            modal.style.display = 'none';
            currentEditId = null;
        }

        function switchTab(tabName) {
            // 1. Hide All Content
            document.getElementById('content-pricing').classList.add('hidden');
            document.getElementById('content-rules').classList.add('hidden');
            document.getElementById('content-account').classList.add('hidden');

            // 2. Reset All Buttons Styles
            const buttons = ['btn-pricing', 'btn-rules', 'btn-account'];
            buttons.forEach(id => {
                const btn = document.getElementById(id);
                btn.className = "flex-1 py-3 px-6 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 border border-transparent transition-all duration-200 flex items-center justify-center gap-2";
            });

            // 3. Show Selected Content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // 4. Highlight Selected Button
            const activeBtn = document.getElementById('btn-' + tabName);
            activeBtn.className = "flex-1 py-3 px-6 rounded-xl text-sm font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 shadow-sm transition-all duration-200 flex items-center justify-center gap-2";
        }

        // Save Rules/Settings tab (siklus penagihan + bank)
        async function saveRulesSettings() {
            const btn = document.getElementById('rulesSaveBtn');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;
            
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('invoice_reminder_days_before', document.getElementById('reminderDaysBefore')?.value || 7);
            formData.append('bank_name', document.getElementById('bankName')?.value || '');
            formData.append('bank_account_number', document.getElementById('bankAccountNumber')?.value || '');
            formData.append('bank_account_name', document.getElementById('bankAccountName')?.value || '');
            
            try {
                const response = await fetch("{{ route('owner.settings.bank') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Toast.fire({ icon: 'success', title: data.message || 'Pengaturan berhasil disimpan!' });
                } else {
                    Toast.fire({ icon: 'error', title: 'Gagal menyimpan pengaturan' });
                }
            } catch (error) {
                console.error('Save error:', error);
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan saat menyimpan' });
            }
            
            btn.disabled = false;
            btn.innerHTML = originalText;
        }

        // Save Account/Profile tab (profile + password)
        async function saveAccountSettings() {
            const btn = document.getElementById('accountSaveBtn');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;
            
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('boarding_house_name', document.getElementById('boardingHouseName')?.value || '');
            formData.append('name', document.getElementById('ownerName')?.value || '');
            formData.append('email', document.getElementById('ownerEmail')?.value || '');
            formData.append('invoice_reminder_days_before', document.getElementById('reminderDaysBefore')?.value || 7);
            
            const currentPass = document.getElementById('currentPassword')?.value;
            const newPass = document.getElementById('newPassword')?.value;
            const confirmPass = document.getElementById('confirmPassword')?.value;
            
            if (newPass) {
                formData.append('current_password', currentPass || '');
                formData.append('password', newPass);
                formData.append('password_confirmation', confirmPass || '');
            }
            
            try {
                const response = await fetch("{{ route('owner.settings.update-all') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success || response.ok) {
                    Toast.fire({ icon: 'success', title: data.message || 'Profil berhasil disimpan!' });
                    if (newPass) {
                        try {
                            document.getElementById('currentPassword').value = '';
                            document.getElementById('newPassword').value = '';
                            document.getElementById('confirmPassword').value = '';
                        } catch(e) {}
                    }
                } else {
                    Toast.fire({ icon: 'error', title: data.message || 'Gagal menyimpan profil' });
                }
            } catch (error) {
                console.error('Save error:', error);
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan saat menyimpan' });
            }
            
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    </script>
    
    <style>
        .toggle-checkbox:checked { right: 0; border-color: #10B981; }
        .toggle-checkbox:checked + .toggle-label { background-color: #10B981; }
        .toggle-checkbox { right: 0; transition: all 0.3s; }
        .toggle-label { width: 100%; height: 100%; background-color: #ccc; }
    </style>
@endsection