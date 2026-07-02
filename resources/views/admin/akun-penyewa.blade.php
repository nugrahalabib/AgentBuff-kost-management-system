@extends('layouts.admin')

@section('title', 'Manajemen Akun')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Data Akun Penyewa') }}
            </h2>
            <p class="text-sm text-gray-500">Kelola semua akun penyewa yang terdaftar di sistem.</p>
        </div>
    </div>
@endsection

@section('content')
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        
        {{-- Hidden verification tabs - keeping code but not displaying --}}
        {{-- 
        <div class="bg-white p-1.5 rounded-xl border border-gray-100 inline-flex shadow-sm">
            <a href="{{ route('admin.akun-penyewa', ['tab' => 'all']) }}" class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-300 {{ $tab === 'all' ? 'text-white bg-emerald-600 shadow-md' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                Semua Akun
            </a>
            <a href="{{ route('admin.akun-penyewa', ['tab' => 'verified']) }}" class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-300 flex items-center gap-2 {{ $tab === 'verified' ? 'text-white bg-emerald-600 shadow-md' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }}">
                Terverifikasi
                <span class="bg-emerald-100 text-emerald-700 text-[10px] px-1.5 py-0.5 rounded-full {{ $tab === 'verified' ? 'bg-emerald-600/30 text-emerald-100' : '' }}">{{ $verifiedCount }}</span>
            </a>
            <a href="{{ route('admin.akun-penyewa', ['tab' => 'pending']) }}" class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-300 flex items-center gap-2 {{ $tab === 'pending' ? 'text-white bg-orange-500 shadow-md' : 'text-gray-500 hover:text-orange-600 hover:bg-orange-50' }}">
                Butuh Verifikasi
                <span class="bg-orange-100 text-orange-600 text-[10px] px-1.5 py-0.5 rounded-full {{ $tab === 'pending' ? 'bg-orange-600/30 text-orange-100 animate-pulse' : 'animate-pulse' }}">{{ $unverifiedCount }}</span>
            </a>
        </div>
        --}}

        <div class="flex gap-3 w-full md:w-auto">
            <form action="{{ route('admin.akun-penyewa') }}" method="GET" class="flex gap-3 w-full md:w-auto">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative w-full md:w-64">
                        <input type="text" name="search" id="searchInput" placeholder="Cari nama atau email..." value="{{ $search }}" class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-white transition shadow-sm">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        @if($search)
                        <button type="button" onclick="resetSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" style="background: none; border: none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        @endif
                </div>
                <button type="submit" class="bg-emerald-600 text-white px-4 py-3 rounded-xl shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                <div class="flex bg-gray-100 p-1 rounded-xl border border-gray-200">
                    <button type="button" onclick="switchView('list')" id="btn-list" class="p-2.5 rounded-lg bg-white shadow-sm text-emerald-600 transition" title="Tampilan Tabel">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </button>
                    <button type="button" onclick="switchView('grid')" id="btn-grid" class="p-2.5 rounded-lg text-gray-400 hover:text-emerald-600 transition" title="Tampilan Kartu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="view-list" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden animate-fade-in">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Nama Akun</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Telepon</th>
                        {{-- Hidden status column --}}
                        {{-- <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Status</th> --}}
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse ($dataPenyewa as $tenant)
                        @php
                            $isVerified = $tenant->tenantProfile->is_verified_by_admin ?? false;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img class="h-9 w-9 rounded-full object-cover border border-gray-200" src="https://ui-avatars.com/api/?name={{ urlencode($tenant->name) }}&background=D1FAE5&color=059669" alt="Foto">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $tenant->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-medium">{{ $tenant->email }}</td>
                            <td class="px-6 py-4 text-gray-600 font-medium">{{ $tenant->tenantProfile?->phone ?? '-' }}</td>
                            {{-- Hidden status column --}}
                            {{--
                            <td class="px-6 py-4">
                                @if($isVerified)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        ✓ Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-50 text-orange-700 border border-orange-100 animate-pulse">
                                        ⚠ Butuh Verifikasi
                                    </span>
                                @endif
                            </td>
                            --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <form action="{{ route('admin.akun-penyewa.destroy', $tenant->id) }}" method="POST" onsubmit="confirmSubmit(event, 'Hapus akun {{ $tenant->name }}? Tindakan ini tidak dapat dibatalkan.');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </form>
                                    {{-- Hidden verify button --}}
                                    {{--
                                    @if(!$isVerified)
                                        <form action="{{ route('admin.penyewa.verify', $tenant->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="text-orange-600 hover:text-orange-700 p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></button>
                                        </form>
                                    @endif
                                    --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Tidak ada akun ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
            @if ($dataPenyewa->count() > 0)
                <span class="text-xs text-gray-500">Menampilkan {{ $dataPenyewa->firstItem() ?? 1 }}-{{ $dataPenyewa->lastItem() ?? 12 }} dari {{ $dataPenyewa->total() }} akun</span>
            @else
                <span class="text-xs text-gray-500">Tidak ada akun</span>
            @endif
            @if ($dataPenyewa->hasPages())
                <div class="space-x-1">
                    {{ $dataPenyewa->appends(request()->query())->links('components.pagination.admin') }}
                </div>
            @endif
        </div>
    </div>

    <div id="view-grid" class="space-y-6 animate-fade-in" style="display: none;">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        @forelse($dataPenyewa as $tenant)
            @php
                $initials = strtoupper(substr($tenant->name, 0, 2));
                $isVerified = $tenant->tenantProfile->is_verified_by_admin ?? false;
            @endphp

            <div class="user-card group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full bg-emerald-50 border-4 border-white shadow-lg mb-4 flex items-center justify-center relative">
                        @if($tenant->avatar)
                            <img src="{{ asset('storage/' . $tenant->avatar) }}" class="w-full h-full rounded-full object-cover opacity-90" alt="Avatar">
                        @else
                            <span class="text-2xl font-bold text-emerald-600">{{ $initials }}</span>
                        @endif
                        <span class="absolute bottom-0 right-0 w-5 h-5 bg-emerald-500 border-2 border-white rounded-full"></span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900">{{ $tenant->name }}</h3>
                    <p class="text-xs text-gray-400 mb-4">Terdaftar {{ $tenant->created_at->diffForHumans() }}</p>
                    
                    <div class="w-full bg-gray-50 rounded-xl p-3 mb-4 space-y-2 text-left">
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="truncate">{{ $tenant->email }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>{{ $tenant->tenantProfile->phone ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full">
                        <button class="flex-1 py-2 rounded-xl border border-gray-200 text-gray-500 text-xs font-bold hover:bg-gray-50 transition">Detail</button>
                        <form action="{{ route('admin.akun-penyewa.destroy', $tenant->id) }}" method="POST" onsubmit="confirmSubmit(event, 'Hapus akun {{ $tenant->name }}? Tindakan ini tidak dapat dibatalkan.');" style="width: 40px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <p class="text-gray-500 text-lg font-medium">Tidak ada akun ditemukan</p>
            </div>
        @endforelse
        </div>

        @if ($dataPenyewa->hasPages())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500">
                    Menampilkan <span class="font-bold text-gray-700">{{ $dataPenyewa->firstItem() ?? 1 }}</span> - <span class="font-bold text-gray-700">{{ $dataPenyewa->lastItem() ?? $dataPenyewa->count() }}</span> dari <span class="font-bold text-emerald-600">{{ $dataPenyewa->total() }}</span> akun
                </div>
                {{ $dataPenyewa->appends(request()->query())->links('components.pagination.admin') }}
            </div>
        @endif
    </div>

    <script>
        function switchView(view) {
            const grid = document.getElementById('view-grid');
            const list = document.getElementById('view-list');
            const btnGrid = document.getElementById('btn-grid');
            const btnList = document.getElementById('btn-list');

            if(view === 'grid') {
                grid.style.display = 'block';
                list.style.display = 'none';
                btnGrid.className = "p-2.5 rounded-lg bg-white shadow-sm text-emerald-600 transition";
                btnList.className = "p-2.5 rounded-lg text-gray-400 hover:text-emerald-600 transition";
            } else {
                grid.style.display = 'none';
                list.style.display = 'block';
                btnGrid.className = "p-2.5 rounded-lg text-gray-400 hover:text-emerald-600 transition";
                btnList.className = "p-2.5 rounded-lg bg-white shadow-sm text-emerald-600 transition";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toastMsg = sessionStorage.getItem('toastMessage');
            if (toastMsg) {
                sessionStorage.removeItem('toastMessage');
                Toast.fire(JSON.parse(toastMsg));
            }
        });

        function resetSearch() {
            document.getElementById('searchInput').value = '';
            // Remove search query and submit form
            const form = document.querySelector('form[action*="akun-penyewa"]');
            if(form) {
                form.querySelector('input[name="search"]').value = '';
                form.submit();
            }
        }
    </script>
@endsection