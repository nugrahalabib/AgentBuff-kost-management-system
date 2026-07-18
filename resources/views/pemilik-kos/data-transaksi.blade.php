@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Validasi Transaksi') }}
            </h2>
            <p class="text-sm text-gray-500">Cek mutasi bank dan konfirmasi pembayaran dari penyewa.</p>
        </div>
        <button type="button" onclick="openTxModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-3 px-6 rounded-xl shadow-md transition flex items-center whitespace-nowrap">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            TAMBAH TRANSAKSI
        </button>
    </div>
@endsection

@section('content')
    @include('partials.modal-transaksi', ['tenants' => $tenants, 'rooms' => $rooms, 'penyewaData' => $penyewaData, 'roomData' => $roomData, 'preselectPenyewa' => $preselectPenyewa])

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-orange-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-orange-500"></div>
            <div>
                <p class="text-xs font-bold text-orange-500 uppercase tracking-wider">Perlu Cek Mutasi</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $stats['pending_verification'] ?? 0 }} <span class="text-sm font-medium text-gray-400">Transaksi</span></p>
                <p class="text-[10px] text-gray-400 mt-1">Menunggu konfirmasi Anda</p>
            </div>
            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center animate-pulse">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-emerald-500"></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pemasukan</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">Rp {{ number_format($stats['total_amount'] ?? 0, 0, ',', '.') }} <span class="text-sm font-medium text-gray-400">Perlu Validasi</span></p>
                <p class="text-[10px] text-emerald-600 mt-1 font-bold">{{ now()->format('F Y') }}</p>
            </div>
            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>
        
    </div>

    <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <div class="flex flex-col xl:flex-row gap-3 justify-between items-center">
            
            {{-- Filter Tabs (Fixed 3 Buttons) --}}
            <div class="flex bg-gray-50 p-1 rounded-xl w-full xl:w-auto overflow-x-auto">
                <a href="{{ route('owner.verifikasi-transaksi', array_merge(request()->except(['grid_page', 'list_page']), ['status' => 'semua'])) }}" class="flex-1 whitespace-nowrap px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center {{ !request('status') || request('status') == 'semua' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}" id="tab-all">Semua</a>
                <a href="{{ route('owner.verifikasi-transaksi', array_merge(request()->except(['grid_page', 'list_page']), ['status' => 'pending'])) }}" class="flex-1 whitespace-nowrap px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center {{ request('status') == 'pending' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}" id="tab-pending">Perlu Cek Mutasi</a>
                <a href="{{ route('owner.verifikasi-transaksi', array_merge(request()->except(['grid_page', 'list_page']), ['status' => 'success'])) }}" class="flex-1 whitespace-nowrap px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center {{ request('status') == 'success' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}" id="tab-success">Riwayat Selesai</a>
                <a href="{{ route('owner.verifikasi-transaksi', array_merge(request()->except(['grid_page', 'list_page']), ['status' => 'rejected'])) }}" class="flex-1 whitespace-nowrap px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center {{ request('status') == 'rejected' ? 'bg-white shadow-sm text-red-600' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}" id="tab-rejected">Ditolak</a>
            </div>

            <div class="flex flex-wrap gap-2 w-full xl:w-auto items-center justify-end">
                <form action="{{ route('owner.verifikasi-transaksi') }}" method="GET" class="contents">
                    {{-- Persist Status --}}
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="hidden" name="view" value="{{ request('view', 'grid') }}">

                    {{-- Floor Filter --}}
                    <select name="floor" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-500 focus:ring-emerald-500 bg-gray-50 hover:bg-white transition cursor-pointer">
                        <option value="">Semua Lantai</option>
                        @foreach($floors as $fl)
                            <option value="{{ $fl }}" {{ request('floor') == $fl ? 'selected' : '' }}>Lantai {{ $fl }}</option>
                        @endforeach
                    </select>
                    
                    {{-- Search Input --}}
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari penyewa..." class="pl-3 pr-8 py-2 border border-gray-200 rounded-lg text-xs focus:ring-emerald-500 w-32 md:w-40 bg-gray-50 hover:bg-white transition">
                        @if(request('search'))
                            <a href="{{ route('owner.verifikasi-transaksi', request()->except(['search', 'grid_page', 'list_page'])) }}" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition" title="Hapus pencarian">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>
                    
                    {{-- Date Filter --}}
                    <div class="relative inline-block">
                        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" class="px-3 pr-10 py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-500 focus:ring-emerald-500 bg-white hover:bg-white transition cursor-pointer">
                        @if(request('date'))
                            <a href="{{ route('owner.verifikasi-transaksi', request()->except(['date', 'grid_page', 'list_page'])) }}" class="absolute right-3 top-1/2 -translate-y-1/2 bg-white border border-gray-200 rounded-full p-1 flex items-center justify-center shadow-sm text-gray-400 hover:text-red-500 hover:border-red-200 transition z-10" style="width: 22px; height: 22px;" title="Reset tanggal">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="bg-emerald-600 text-white p-2 rounded-lg shadow-md hover:bg-emerald-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>

                <div class="w-px h-8 bg-gray-200 mx-1"></div>

                {{-- View Switcher --}}
                <div class="flex bg-gray-100 p-1 rounded-lg border border-gray-200">
                    <a href="{{ route('owner.verifikasi-transaksi', array_merge(request()->query(), ['view' => 'grid'])) }}" class="p-1.5 rounded transition {{ request('view', 'grid') === 'grid' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-400 hover:text-emerald-600' }}" title="Tampilan Kartu">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </a>
                    <a href="{{ route('owner.verifikasi-transaksi', array_merge(request()->query(), ['view' => 'list'])) }}" class="p-1.5 rounded transition {{ request('view') === 'list' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-400 hover:text-emerald-600' }}" title="Tampilan Tabel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid View --}}
    <div id="view-grid" class="{{ request('view', 'grid') === 'list' ? 'hidden' : '' }} grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 animate-fade-in">
        
        @forelse ($transaksiGrid as $transaction)
        @php
            $status = $transaction->status;
            // Admin Pending is what we renamed 'pending' in controller for filters
            // But from DB perspective:
            // verified_by_admin = Pending Owner Action
            // verified_by_owner = Success
            // rejected_by_owner = Rejected
            
            $isPending = $status === 'verified_by_admin';
            $isSuccess = $status === 'verified_by_owner';
            $isRejectedByOwner = $status === 'rejected_by_owner';
            $isRejectedByAdmin = $status === 'rejected_by_admin';
            $isRejected = $isRejectedByOwner || $isRejectedByAdmin;

            $typeClass = $isPending ? 'type-pending' : ($isSuccess ? 'type-success' : 'type-rejected');
            $cardBorder = $isPending ? 'border-orange-200 shadow-orange-100/50 shadow-lg' : ($isRejected ? 'border-red-200' : 'border-gray-100');
            $badgeBg = $isPending ? 'bg-orange-500' : ($isRejectedByAdmin ? 'bg-rose-700' : ($isRejectedByOwner ? 'bg-red-500' : 'bg-emerald-500'));
            $badgeText = $isPending ? 'CEK MUTASI' : ($isRejectedByAdmin ? 'DITOLAK ADMIN' : ($isRejectedByOwner ? 'DITOLAK' : 'SELESAI'));
        @endphp
        <!-- Kartu Transaksi -->
        <div class="trx-card {{ $typeClass }} bg-white rounded-2xl p-5 border {{ $cardBorder }} relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute top-0 right-0 {{ $badgeBg }} text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl shadow-sm z-10">{{ $badgeText }}</div>
            
            <div class="flex gap-4 items-start mb-4">
                @php
                    $proof = $transaction->paymentProofs->sortByDesc('uploaded_at')->first();
                    $proofUrl = $proof?->proof_url;
                    $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($transaction->tenant->name);
                @endphp
                @if ($proofUrl)
                    <div class="w-16 h-16 rounded-xl bg-gray-100 border-2 {{ $isPending ? 'border-orange-200' : ($isRejected ? 'border-red-200' : 'border-gray-200') }} overflow-hidden cursor-pointer relative flex-shrink-0 group" onclick="openProofModal('{{ $proofUrl }}')">
                        <img src="{{ $proofUrl }}" class="w-full h-full object-cover group-hover:scale-110 transition">
                        <div class="absolute inset-0 bg-black/10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                             <svg class="w-6 h-6 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <div class="absolute bottom-0 right-0 {{ $isPending ? 'bg-orange-500' : ($isRejected ? 'bg-red-500' : 'bg-emerald-500') }} text-white text-[8px] font-bold px-1.5 py-0.5 rounded-tl">
                            Bukti
                        </div>
                    </div>
                @elseif ($transaction->payment_method == 'cash')
                    <div class="w-16 h-16 rounded-xl bg-emerald-100 border-2 border-emerald-200 flex flex-col items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="text-[10px] font-bold text-emerald-700">TUNAI</span>
                    </div>
                @elseif ($transaction->payment_method == 'edc')
                    <div class="w-16 h-16 rounded-xl bg-blue-100 border-2 border-blue-200 flex flex-col items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span class="text-[10px] font-bold text-blue-700">EDC</span>
                    </div>
                @else
                    <div class="w-16 h-16 rounded-xl bg-gray-100 border-2 border-gray-200 overflow-hidden cursor-pointer relative flex-shrink-0 grayscale hover:grayscale-0 transition" onclick="openProofModal('{{ $avatarUrl }}')">
                        <img src="{{ $avatarUrl }}" class="w-full h-full object-cover">
                    </div>
                @endif
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">{{ $transaction->tenant->name }}</h3>
                    @if($transaction->sender_bank)
                        <div class="text-[10px] text-gray-500 font-medium mt-0.5 mb-1">
                            Dari: <span class="font-bold text-gray-700">{{ $transaction->sender_bank }}</span> a.n {{ $transaction->sender_name }}
                        </div>
                    @endif
                    <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Kamar {{ $transaction->room->room_number ?? 'N/A' }} ({{ $transaction->room->roomType->name ?? 'N/A' }})
                    </div>
                    @if($transaction->duration_months)
                        <div class="flex items-center gap-1 text-xs text-emerald-600 font-bold mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $transaction->duration_months }} Bulan ({{ $transaction->period_start_date?->format('d/m') }} - {{ $transaction->period_end_date?->format('d/m/Y') }})
                        </div>
                    @endif
                    <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $transaction->updated_at->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 mb-4 text-xs">
                <div class="flex justify-between mb-1">
                    <span class="text-gray-500">Nominal Transfer:</span>
                    <span class="font-bold text-gray-800">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Referensi:</span>
                    <span class="font-bold text-gray-800">{{ $transaction->reference_number }}</span>
                </div>
                @if($isPending)
                <div class="mt-2 pt-2 border-t border-gray-200 flex items-center gap-2 text-orange-600 font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Periksa Rekening Anda</span>
                </div>
                @elseif($isRejected)
                @if($isRejectedByOwner)
                <div class="mt-2 pt-2 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-red-600 font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Ditolak oleh Anda</span>
                    </div>
                    @if($transaction->owner_notes)
                    <div class="mt-2 bg-red-50 border border-red-200 rounded-lg p-2">
                        <p class="text-xs text-red-600 font-bold">Alasan:</p>
                        <p class="text-xs text-red-700 italic mt-1">"{{ $transaction->owner_notes }}"</p>
                    </div>
                    @endif
                </div>
                @elseif($isRejectedByAdmin)
                <div class="mt-2 pt-2 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-rose-700 font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Ditolak oleh Admin</span>
                    </div>
                    @if($transaction->admin_notes)
                    <div class="mt-2 bg-rose-50 border border-rose-200 rounded-lg p-2">
                        <p class="text-xs text-rose-700 font-bold">Alasan:</p>
                        <p class="text-xs text-rose-800 italic mt-1">"{{ $transaction->admin_notes }}"</p>
                    </div>
                    @endif
                </div>
                @endif
                @elseif($isSuccess)
                <div class="mt-2 pt-2 border-t border-gray-200 flex items-center gap-2 text-emerald-600 font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Transaksi Selesai</span>
                </div>
                @endif
            </div>

            @if($isPending)
            <div class="flex gap-2">
                <form action="{{ route('owner.verifikasi-transaksi.verify', $transaction->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-xl text-xs font-bold transition shadow-md flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Uang Masuk (Terima)
                    </button>
                </form>
                <button type="button" onclick="showRejectModal({{ $transaction->id }})" class="px-3 bg-white text-red-600 border border-gray-200 rounded-xl transition hover:bg-red-50 hover:border-red-200 font-bold text-xs h-full">
                    <span class="flex items-center justify-center gap-1 font-bold text-xs w-full py-2.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Tolak
                    </span>
                </button>
            </div>
            @endif

            {{-- Hapus transaksi (owner) — berlaku untuk diterima maupun ditolak --}}
            <form action="{{ route('owner.verifikasi-transaksi.destroy', $transaction->id) }}" method="POST"
                  onsubmit="return confirm('Hapus transaksi {{ $transaction->reference_number ?? ('#'.$transaction->id) }} secara permanen? Bukti bayar & riwayat verifikasinya ikut terhapus dan tidak bisa dikembalikan.');"
                  class="mt-2 pt-2 border-t border-gray-100">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-red-600 transition py-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus transaksi
                </button>
            </form>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-12">
            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p class="text-gray-500 font-semibold mb-2">Tidak ada transaksi ditemukan</p>
        </div>
        @endforelse
    </div>

    {{-- Grid Pagination --}}
    <div id="grid-pagination" class="{{ request('view', 'grid') === 'list' ? 'hidden' : '' }} mt-6 px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center rounded-2xl mb-6">
        @if ($transaksiGrid->count() > 0)
            <span class="text-xs text-gray-500">Menampilkan {{ $transaksiGrid->firstItem() ?? 1 }}-{{ $transaksiGrid->lastItem() ?? 10 }} dari {{ $transaksiGrid->total() }} data</span>
        @else
            <span class="text-xs text-gray-500">Tidak ada transaksi</span>
        @endif
        @if ($transaksiGrid->hasPages())
            <div class="space-x-1">
                {{ $transaksiGrid->appends(request()->query())->links('components.pagination.admin') }}
            </div>
        @endif
    </div>

    {{-- List View --}}
    <div id="view-list" class="{{ request('view') === 'list' ? '' : 'hidden' }} animate-fade-in bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Penyewa</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Kamar</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Periode Sewa</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($transaksiList as $transaction)
                        @php
                            $status = $transaction->status;
                            $isPending = $status === 'verified_by_admin';
                            $isSuccess = $status === 'verified_by_owner';
                            $isRejectedByOwner = $status === 'rejected_by_owner';
                            $isRejectedByAdmin = $status === 'rejected_by_admin';
                            $isRejected = $isRejectedByOwner || $isRejectedByAdmin;

                            $statusBg = $isPending ? 'bg-orange-100 text-orange-700' : ($isRejectedByAdmin ? 'bg-rose-100 text-rose-700' : ($isRejected ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'));
                            $statusText = $isPending ? 'Perlu Cek Mutasi' : ($isRejectedByAdmin ? 'Ditolak Admin' : ($isRejectedByOwner ? 'Ditolak' : 'Selesai'));
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-500">{{ $transaction->updated_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $transaction->tenant->name }}</div>
                                @if($transaction->sender_bank)
                                    <div class="text-[10px] text-gray-500 mt-0.5">
                                        Via {{ $transaction->sender_bank }} ({{ $transaction->sender_name }})
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $transaction->room->roomType->abbreviation ?? 'STD' }}-{{ $transaction->room->room_number }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                @php
                                    $tenant = $transaction->tenant;
                                    $checkInDate = $tenant && $tenant->activeRoom && $tenant->activeRoom->pivot ? $tenant->activeRoom->pivot->check_in_date : null;
                                    $startDate = $checkInDate ? \Carbon\Carbon::parse($checkInDate) : $transaction->period_start_date;
                                    $endDate = $transaction->period_end_date;
                                @endphp
                                @if($startDate && $endDate)
                                    <span class="text-xs font-medium">{{ $startDate->format('d/m/y') }} - {{ $endDate->format('d/m/y') }}</span>
                                    <div class="text-[10px] text-gray-400">{{ $transaction->duration_months }} Bulan</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4"><span class="{{ $statusBg }} px-2 py-1 rounded text-xs font-bold">{{ $statusText }}</span></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @php
                                        $proof = $transaction->paymentProofs->sortByDesc('uploaded_at')->first();
                                        $proofUrl = $proof?->proof_url;
                                    @endphp
                                    @if($proofUrl)
                                        <button onclick="openProofModal('{{ $proofUrl }}')" class="text-gray-400 hover:text-emerald-600 transition p-1" title="Lihat Bukti">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    @endif
                                    @if ($isPending)
                                        <form action="{{ route('owner.verifikasi-transaksi.verify', $transaction->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                                                Validasi (Terima Uang)
                                            </button>
                                        </form>
                                    @endif
                                    {{-- Hapus transaksi (diterima maupun ditolak) --}}
                                    <form action="{{ route('owner.verifikasi-transaksi.destroy', $transaction->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Hapus transaksi {{ $transaction->reference_number ?? ('#'.$transaction->id) }} secara permanen? Bukti bayar & riwayat verifikasinya ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1" title="Hapus transaksi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada transaksi ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- List Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
            @if ($transaksiList->count() > 0)
                <span class="text-xs text-gray-500">Menampilkan {{ $transaksiList->firstItem() ?? 1 }}-{{ $transaksiList->lastItem() ?? 10 }} dari {{ $transaksiList->total() }} data</span>
            @else
                <span class="text-xs text-gray-500">Tidak ada transaksi</span>
            @endif
            @if ($transaksiList->hasPages())
                <div class="space-x-1">
                    {{ $transaksiList->appends(request()->query())->links('components.pagination.admin') }}
                </div>
            @endif
        </div>
    </div>

    <div id="proof-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeProofModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <img id="modal-image" src="" class="w-full rounded-lg">
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeProofModal()">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openProofModal(url) { document.getElementById('modal-image').src = url; document.getElementById('proof-modal').classList.remove('hidden'); }
        function closeProofModal() { document.getElementById('proof-modal').classList.add('hidden'); }
        
        // Reject Modal Functions
        let currentRejectId = null;
        function showRejectModal(transactionId) {
            currentRejectId = transactionId;
            document.getElementById('reject-notes').value = '';
            document.getElementById('reject-modal').classList.replace('hidden', 'flex');
        }
        function closeRejectModal() {
            document.getElementById('reject-modal').classList.replace('flex', 'hidden');
            currentRejectId = null;
        }
        function submitReject() {
            const textarea = document.getElementById('reject-notes');
            const notes = textarea.value.trim();
                if (notes.length === 0) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Alasan penolakan wajib diisi!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'rounded-xl shadow-lg border border-red-200',
                            title: 'font-bold text-sm text-red-600',
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        },
                    });
                    textarea.focus();
                    return;
                }
                if (notes.length < 10) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Alasan penolakan minimal 10 karakter!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'rounded-xl shadow-lg border border-red-200',
                            title: 'font-bold text-sm text-red-600',
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        },
                    });
                    textarea.focus();
                    return;
                }
            document.getElementById('reject-notes-hidden').value = notes;
            document.getElementById('reject-form').action = `/owner/verifikasi-transaksi/${currentRejectId}/verify`;
            document.getElementById('reject-form').submit();
        }
    </script>
    
    <!-- Reject Modal -->
    <div id="reject-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-900">Tolak Pembayaran</h3>
                    <p class="text-sm text-gray-500">Berikan alasan penolakan untuk tenant</p>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea id="reject-notes" rows="3" placeholder="Contoh: Uang belum masuk ke rekening, nominal tidak sesuai, dll..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-0 text-sm" required></textarea>
                <p class="text-xs text-gray-400 mt-1">Minimal 10 karakter. Tenant akan melihat alasan ini.</p>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 h-12 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-base">Batal</button>
                <form id="reject-form" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <input type="hidden" id="reject-notes-hidden" name="notes" value="">
                    <button type="button" onclick="submitReject()" class="w-full h-12 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition text-base">Tolak Pembayaran</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('tour')
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.__pageTour = [
        { element: 'a[href$="/owner/transaksi/tambah"]', popover: { title: 'Tambah Transaksi', description: 'Catat pembayaran manual (tunai/transfer) & tempatkan penyewa ke kamar.' } },
        { popover: { title: 'Verifikasi', description: 'Cek bukti transfer penyewa lalu terima/tolak di daftar transaksi ini.' } },
    ];
    window.KostTour && window.KostTour.auto('owner-transaksi-v1', window.__pageTour);
});
</script>
@endpush
