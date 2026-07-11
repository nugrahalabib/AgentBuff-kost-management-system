@extends('layouts.admin')

@section('title', 'Data Transaksi & Pembayaran')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Data Transaksi') }}
            </h2>
            <p class="text-sm text-gray-500">Kelola pembayaran dan validasi bukti transfer dari penyewa.</p>
        </div>
        <button onclick="openManualModal()"
            class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl font-bold text-sm transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Buat Transaksi
        </button>
    </div>
@endsection

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-emerald-500"></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Masuk</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $totalTransaksi }} <span
                        class="text-sm font-medium text-gray-400">Bukti</span></p>
                <p class="text-[10px] text-emerald-600 mt-1 font-bold">+{{ $transaksiHariIni }} Hari ini</p>
            </div>
            <div
                class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
        </div>

        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-orange-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-orange-500"></div>
            <div>
                <p class="text-xs font-bold text-orange-500 uppercase tracking-wider">Tugas Verifikasi</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $pendingValidation }} <span
                        class="text-sm font-medium text-gray-400">Item</span></p>
                <p class="text-[10px] text-gray-400 mt-1">Cek keaslian bukti transfer</p>
            </div>
            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center animate-pulse">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                </svg>
            </div>
        </div>

        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-yellow-500"></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menunggu Pemilik Kos</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $menungguPemilik }} <span
                        class="text-sm font-medium text-gray-400">Item</span></p>
                <p class="text-[10px] text-gray-400 mt-1">Menunggu cek mutasi bank</p>
            </div>
            <div
                class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm mb-6">
        <div class="flex flex-col xl:flex-row gap-3 justify-between items-center">

            <div class="flex bg-gray-50 p-1 rounded-xl w-full xl:w-auto overflow-x-auto">
                <a href="{{ route('admin.transaksi', array_merge(request()->query(), ['status' => 'semua'])) }}"
                    class="flex-1 whitespace-nowrap px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center {{ !request('status') || request('status') == 'semua' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}">Semua</a>
                <a href="{{ route('admin.transaksi', array_merge(request()->query(), ['status' => 'pending'])) }}"
                    class="flex-1 whitespace-nowrap px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center {{ request('status') == 'pending' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}">Butuh
                    Verifikasi</a>
                <a href="{{ route('admin.transaksi', array_merge(request()->query(), ['status' => 'owner'])) }}"
                    class="flex-1 whitespace-nowrap px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center {{ request('status') == 'owner' ? 'bg-white shadow-sm text-yellow-600' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}">Proses
                    Pemilik Kos</a>
                <a href="{{ route('admin.transaksi', array_merge(request()->query(), ['status' => 'success'])) }}"
                    class="flex-1 whitespace-nowrap px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center {{ request('status') == 'success' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}">Selesai</a>
                <a href="{{ route('admin.transaksi', array_merge(request()->query(), ['status' => 'rejected'])) }}"
                    class="flex-1 whitespace-nowrap px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center {{ request('status') == 'rejected' ? 'bg-white shadow-sm text-red-600' : 'text-gray-600 hover:bg-white hover:shadow-sm' }}">Ditolak</a>
            </div>

            <!-- Right Side: Filters & View Switcher -->
            <div class="flex flex-wrap gap-2 w-full xl:w-auto items-center justify-end">
                <form action="{{ route('admin.transaksi') }}" method="GET" class="contents">
                    <!-- Persist current status -->
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    <select name="floor" onchange="this.form.submit()"
                        class="px-3 py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-500 focus:ring-emerald-500 bg-gray-50 hover:bg-white transition cursor-pointer">
                        <option value="">Semua Lantai</option>
                        @foreach($floors as $fl)
                            <option value="{{ $fl }}" {{ request('floor') == $fl ? 'selected' : '' }}>Lantai {{ $fl }}</option>
                        @endforeach
                    </select>

                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kamar/nama..."
                            class="pl-3 pr-8 py-2 border border-gray-200 rounded-lg text-xs focus:ring-emerald-500 w-32 md:w-40 bg-gray-50 hover:bg-white transition">
                        @if(request('search'))
                            <a href="{{ route('admin.transaksi', request()->except(['search', 'grid_page', 'list_page'])) }}"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition"
                                title="Hapus pencarian">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </div>

                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                        class="px-3 py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-500 focus:ring-emerald-500 bg-gray-50 hover:bg-white transition cursor-pointer">

                    <button type="submit"
                        class="bg-emerald-600 text-white p-2 rounded-lg shadow-md hover:bg-emerald-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    @if(request('search') || request('date') || request('status') || request('floor'))
                        <a href="{{ route('admin.transaksi') }}"
                            class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-200 transition flex items-center gap-1 ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                            Reset
                        </a>
                    @endif
                </form>

                <div class="w-px h-8 bg-gray-200 mx-1"></div>

                <div class="flex bg-gray-100 p-1 rounded-lg border border-gray-200">
                    <button onclick="switchView('grid')" id="btn-grid"
                        class="p-1.5 rounded bg-white shadow-sm text-emerald-600 transition" title="Tampilan Kartu">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                    </button>
                    <button onclick="switchView('list')" id="btn-list"
                        class="p-1.5 rounded text-gray-400 hover:text-emerald-600 transition" title="Tampilan Tabel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="view-grid" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 animate-fade-in">
        @forelse ($transaksiGrid as $transaction)
            @php
                $status = $transaction->status;
                $isPending = $status === 'pending_verification';
                $isOwnerWaiting = $status === 'verified_by_admin';
                $isSuccess = $status === 'verified_by_owner';
                $isRejected = in_array($status, ['rejected_by_admin', 'rejected_by_owner']);
                $isCancelled = $status === 'cancelled_by_tenant';

                $cardClass = $isPending ? 'border-orange-200 shadow-lg shadow-orange-100/50' : ($isRejected ? 'border-red-200 shadow-sm' : ($isCancelled ? 'border-gray-300 shadow-sm opacity-60' : 'border-gray-100 shadow-sm'));
                $typeClass = $isPending ? 'type-pending' : ($isOwnerWaiting ? 'type-owner' : ($isRejected ? 'type-rejected' : ($isCancelled ? 'type-rejected' : 'type-success')));
                $statusBg = $isPending ? 'bg-orange-500' : ($isOwnerWaiting ? 'bg-yellow-500' : ($isRejected ? 'bg-red-500' : ($isCancelled ? 'bg-gray-500' : 'bg-emerald-500')));
                $statusText = $isPending ? 'TUGAS ANDA' : ($isOwnerWaiting ? 'MENUNGGU PEMILIK KOS' : ($isRejected ? 'DITOLAK' : ($isCancelled ? 'DIBATALKAN' : 'LUNAS')));
                $hidden = '';
            @endphp
            <div
                class="trx-card {{ $typeClass }} {{ $cardClass }} bg-white rounded-2xl p-5 relative overflow-hidden group hover:-translate-y-1 transition duration-300{{ $hidden }}">
                @if ($isPending)
                    <div
                        class="absolute top-0 right-0 {{ $statusBg }} text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl shadow-sm z-10">
                        {{ $statusText }}
                    </div>
                @elseif ($isOwnerWaiting)
                    <div
                        class="absolute top-0 right-0 bg-yellow-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl shadow-sm z-10">
                        {{ $statusText }}
                    </div>
                @elseif ($isRejected)
                    <div
                        class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl shadow-sm z-10">
                        {{ $statusText }}
                    </div>
                @else
                    <div
                        class="absolute top-0 right-0 bg-emerald-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl shadow-sm z-10">
                        {{ $statusText }}
                    </div>
                @endif

                <div class="flex gap-4 items-start mb-4">
                    @php
                        // Get the latest payment proof (not first)
                        $proof = $transaction->paymentProofs->sortByDesc('uploaded_at')->first();
                        $proofUrl = $proof?->proof_url;
                        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($transaction->tenant->name);
                    @endphp

                    @if ($proofUrl)
                        <div class="w-16 h-16 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden cursor-pointer relative flex-shrink-0 group"
                            onclick="openModal('{{ $proofUrl }}')">
                            <img src="{{ $proofUrl }}" class="w-full h-full object-cover group-hover:scale-110 transition">
                            <div
                                class="absolute inset-0 bg-black/10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-6 h-6 text-white drop-shadow-md" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    @elseif ($transaction->payment_method == 'cash')
                        <div
                            class="w-16 h-16 rounded-xl bg-emerald-100 border border-emerald-200 flex flex-col items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span class="text-[10px] font-bold text-emerald-700">TUNAI</span>
                        </div>
                    @elseif ($transaction->payment_method == 'edc')
                        <div
                            class="w-16 h-16 rounded-xl bg-blue-100 border border-blue-200 flex flex-col items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg>
                            <span class="text-[10px] font-bold text-blue-700">EDC</span>
                        </div>
                    @else
                        <div class="w-16 h-16 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden cursor-pointer flex-shrink-0 grayscale hover:grayscale-0 transition"
                            onclick="openModal('{{ $avatarUrl }}')">
                            <img src="{{ $avatarUrl }}" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm">{{ $transaction->tenant->name }}</h3>
                        @if($transaction->sender_bank)
                            <div class="text-[10px] text-gray-500 font-medium mt-0.5 mb-1">
                                Dari: <span class="font-bold text-gray-700">{{ $transaction->sender_bank }}</span> a.n
                                {{ $transaction->sender_name }}
                            </div>
                        @endif
                        <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Kamar {{ $transaction->room->room_number }} ({{ $transaction->room->roomType->name }})
                        </div>
                        @if($transaction->duration_months)
                            <div class="flex items-center gap-1 text-xs text-emerald-600 font-bold mt-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                {{ $transaction->duration_months }} Bulan ({{ $transaction->period_start_date?->format('d/m') }} -
                                {{ $transaction->period_end_date?->format('d/m/Y') }})
                            </div>
                        @endif
                        <div
                            class="flex items-center gap-1 text-xs {{ $isPending ? 'text-orange-600' : 'text-gray-500' }} font-bold mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $transaction->updated_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>

                @if ($isPending)
                    <div class="bg-orange-50 p-3 rounded-xl border border-orange-100 mb-4">
                        <p class="text-[10px] text-orange-800 font-medium mb-2">⚠️ Pastikan bukti transfer asli dan nominal sesuai.
                        </p>
                        <div class="flex gap-2">
                            <form action="{{ route('admin.transaksi.verify', $transaction->id) }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg text-xs font-bold transition shadow-sm">
                                    Verifikasi & Teruskan ke Pemilik Kos
                                </button>
                            </form>
                            <button type="button" onclick="showRejectModal({{ $transaction->id }})"
                                class="px-3 py-2 bg-white text-red-600 border border-gray-200 rounded-lg transition hover:bg-red-50 hover:border-red-200 text-xs font-bold"
                                title="Tolak Pembayaran">
                                Tolak
                            </button>
                        </div>
                    </div>
                @elseif($isRejected)
                    <div class="flex items-center gap-2 p-2 bg-red-50 rounded-lg text-red-600 text-[10px] border border-red-100">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="font-medium">
                            Ditolak oleh {{ $status === 'rejected_by_admin' ? 'Admin' : 'Owner' }}
                            @if($status === 'rejected_by_admin' && $transaction->admin_notes)
                                - {{ $transaction->admin_notes }}
                            @elseif($status === 'rejected_by_owner' && $transaction->owner_notes)
                                - {{ $transaction->owner_notes }}
                            @endif
                        </span>
                    </div>
                @else
                    <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg text-gray-500 text-[10px] border border-gray-100">
                        @if($isOwnerWaiting)
                            <svg class="w-4 h-4 flex-shrink-0 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                        <span>{{ $isOwnerWaiting ? 'Menunggu verifikasi owner' : 'Transaksi selesai' }}</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <p class="text-gray-500 text-lg font-medium">Tidak ada transaksi ditemukan</p>
            </div>
        @endforelse
    </div>

    <!-- Grid View Pagination -->
    <div id="grid-pagination"
        class="{{ session('view_mode', 'grid') === 'list' ? 'hidden' : '' }} mt-6 px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center rounded-2xl mb-6">
        @if ($transaksiGrid->count() > 0)
            <span class="text-xs text-gray-500">Menampilkan
                {{ $transaksiGrid->firstItem() ?? 1 }}-{{ $transaksiGrid->lastItem() ?? 10 }} dari
                {{ $transaksiGrid->total() }} data</span>
        @else
            <span class="text-xs text-gray-500">Tidak ada transaksi</span>
        @endif
        @if ($transaksiGrid->hasPages())
            <div class="space-x-1">
                {{ $transaksiGrid->appends(request()->query())->links('components.pagination.admin') }}
            </div>
        @endif
    </div>

    <div id="view-list"
        class="hidden animate-fade-in bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
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
                            $isPending = $status === 'pending_verification';
                            $isOwnerWaiting = $status === 'verified_by_admin';
                            $isSuccess = $status === 'verified_by_owner';
                            $isRejected = in_array($status, ['rejected_by_admin', 'rejected_by_owner']);
                            $isCancelled = $status === 'cancelled_by_tenant';

                            $typeClass = $isPending ? 'type-pending' : ($isOwnerWaiting ? 'type-owner' : ($isRejected ? 'type-rejected' : ($isCancelled ? 'type-rejected' : 'type-success')));
                            $statusBg = $isPending ? 'bg-orange-100 text-orange-700' : ($isOwnerWaiting ? 'bg-yellow-100 text-yellow-700' : ($isRejected ? 'bg-red-100 text-red-700' : ($isCancelled ? 'bg-gray-100 text-gray-700' : 'bg-green-100 text-green-700')));
                            $statusText = $isPending ? 'Butuh Verifikasi' : ($isOwnerWaiting ? 'Proses Pemilik Kos' : ($isRejected ? 'Ditolak' : ($isCancelled ? 'Dibatalkan' : 'Lunas')));
                            $hidden = '';
                        @endphp
                        <tr class="hover:bg-gray-50 transition trx-row {{ $typeClass }}{{ $hidden }}">
                            <td class="px-6 py-4 text-gray-500">{{ $transaction->updated_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $transaction->tenant->name }}</div>
                                @if($transaction->sender_bank)
                                    <div class="text-[10px] text-gray-500 mt-0.5">
                                        Via {{ $transaction->sender_bank }} ({{ $transaction->sender_name }})
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $transaction->room->roomType->abbreviation ?? 'STD' }}-{{ $transaction->room->room_number }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                @php
                                    $tenant = $transaction->tenant;
                                    // Try to get check_in from active room pivot 
                                    $checkInDate = $tenant && $tenant->activeRoom && $tenant->activeRoom->pivot ? $tenant->activeRoom->pivot->check_in_date : null;
                                    // Fallback to transaction period_start if check_in not found
                                    $startDate = $checkInDate ? \Carbon\Carbon::parse($checkInDate) : $transaction->period_start_date;
                                    $endDate = $transaction->period_end_date;
                                @endphp
                                @if($startDate && $endDate)
                                    <span class="text-xs font-medium">{{ $startDate->format('d/m/y') }} -
                                        {{ $endDate->format('d/m/y') }}</span>
                                    <div class="text-[10px] text-gray-400">{{ $transaction->duration_months }} Bulan</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4"><span
                                    class="{{ $statusBg }} px-2 py-1 rounded text-xs font-bold">{{ $statusText }}</span></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @php
                                        // Get the latest payment proof
                                        $proof = $transaction->paymentProofs->sortByDesc('uploaded_at')->first();
                                        $proofUrl = $proof?->proof_url;
                                    @endphp
                                    @if($proofUrl)
                                        <button onclick="openModal('{{ $proofUrl }}')"
                                            class="text-gray-400 hover:text-emerald-600 transition p-1" title="Lihat Bukti">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                    @elseif($transaction->payment_method == 'cash')
                                        <span
                                            class="text-xs font-bold text-emerald-600 border border-emerald-200 bg-emerald-50 px-2 py-1 rounded">Tunai</span>
                                    @elseif($transaction->payment_method == 'edc')
                                        <span
                                            class="text-xs font-bold text-blue-600 border border-blue-200 bg-blue-50 px-2 py-1 rounded">EDC</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                    @if ($isPending)
                                        <form action="{{ route('admin.transaksi.verify', $transaction->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                                                Validasi
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-xs">{{ $isOwnerWaiting ? 'Menunggu' : 'Selesai' }}</span>
                                    @endif
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
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
            @if ($transaksiList->count() > 0)
                <span class="text-xs text-gray-500">Menampilkan
                    {{ $transaksiList->firstItem() ?? 1 }}-{{ $transaksiList->lastItem() ?? 10 }} dari
                    {{ $transaksiList->total() }} data</span>
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

    <div id="proof-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <img id="modal-image" src="" class="w-full rounded-lg">
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                        class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 sm:ml-3 sm:w-auto sm:text-sm"
                        onclick="closeModal()">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchView(view) {
            const grid = document.getElementById('view-grid');
            const gridPagination = document.getElementById('grid-pagination');
            const list = document.getElementById('view-list');
            const btnGrid = document.getElementById('btn-grid');
            const btnList = document.getElementById('btn-list');
            if (view === 'grid') {
                grid.classList.remove('hidden');
                if (gridPagination) gridPagination.classList.remove('hidden');
                list.classList.add('hidden');
                btnGrid.className = "p-1.5 rounded bg-white shadow-sm text-emerald-600 transition";
                btnList.className = "p-1.5 rounded text-gray-400 hover:text-emerald-600 transition";
            } else {
                grid.classList.add('hidden');
                if (gridPagination) gridPagination.classList.add('hidden');
                list.classList.remove('hidden');
                btnGrid.className = "p-1.5 rounded text-gray-400 hover:text-emerald-600 transition";
                btnList.className = "p-1.5 rounded bg-white shadow-sm text-emerald-600 transition";
            }
        }

        function openModal(url) {
            document.getElementById('modal-image').src = url;
            document.getElementById('proof-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('proof-modal').classList.add('hidden');
        }

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
            document.getElementById('reject-transaction-id').value = currentRejectId;
            document.getElementById('reject-notes-hidden').value = notes;
            document.getElementById('reject-form').action = `/admin/transaksi/${currentRejectId}/verify`;
            document.getElementById('reject-form').submit();
        }

        // Manual Transaction Functions
        function openManualModal() {
            document.getElementById('manual-modal').classList.remove('hidden');
        }

        function closeManualModal() {
            document.getElementById('manual-modal').classList.add('hidden');
        }

        function filterRooms() {
            const tenantSelect = document.getElementById('manual-tenant');
            const selectedOption = tenantSelect.options[tenantSelect.selectedIndex];
            const tenantRoomId = selectedOption.getAttribute('data-room-id');
            const roomSelect = document.getElementById('manual-room');
            if (tenantRoomId) {
                roomSelect.value = tenantRoomId;
                updatePrice();
            }
        }

        function updatePrice() {
            const roomSelect = document.getElementById('manual-room');
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                document.getElementById('manual-amount').value = price;
                updateTotal();
            }
        }

        function updateTotal() {
            const duration = document.getElementById('manual-duration').value;
            const roomSelect = document.getElementById('manual-room');
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            const price = selectedOption ? selectedOption.getAttribute('data-price') : 0;
            if (price) {
                document.getElementById('manual-amount').value = price * duration;
            }
        }

        // AJAX Pagination
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', function (e) {
                const link = e.target.closest('a');
                if (!link) return;

                const isGridPagination = link.closest('#grid-pagination');
                const isListPagination = link.closest('#view-list .space-x-1');

                if ((isGridPagination || isListPagination) && link.href) {
                    e.preventDefault();
                    const url = link.href;

                    const gridContainer = document.getElementById('view-grid');
                    const listContainer = document.getElementById('view-list');
                    gridContainer.style.opacity = '0.5';
                    listContainer.style.opacity = '0.5';

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            const newGridContent = doc.getElementById('view-grid').innerHTML;
                            const newGridPagination = doc.getElementById('grid-pagination').innerHTML;
                            document.getElementById('view-grid').innerHTML = newGridContent;
                            document.getElementById('grid-pagination').innerHTML = newGridPagination;

                            const newListContent = doc.getElementById('view-list').innerHTML;
                            document.getElementById('view-list').innerHTML = newListContent;

                            gridContainer.style.opacity = '1';
                            listContainer.style.opacity = '1';

                            window.history.pushState({}, '', url);
                        })
                        .catch(error => {
                            console.error('Error fetching pagination:', error);
                            gridContainer.style.opacity = '1';
                            listContainer.style.opacity = '1';
                            window.location.href = url;
                        });
                }
            });
        });
    </script>

    <!-- Reject Modal -->
    <div id="reject-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-900">Tolak Pembayaran</h3>
                    <p class="text-sm text-gray-500">Berikan alasan penolakan untuk tenant</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Penolakan <span
                        class="text-red-500">*</span></label>
                <textarea id="reject-notes" rows="3"
                    placeholder="Contoh: Bukti transfer tidak jelas, nominal tidak sesuai, dll..."
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-0 text-sm"
                    required></textarea>
                <p class="text-xs text-gray-400 mt-1">Minimal 10 karakter. Tenant akan melihat alasan ini.</p>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 h-12 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-base">Batal</button>
                <form id="reject-form" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <input type="hidden" id="reject-transaction-id" name="transaksi_id" value="">
                    <input type="hidden" id="reject-notes-hidden" name="notes" value="">
                    <button type="button" onclick="submitReject()"
                        class="w-full h-12 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition text-base">Tolak
                        Pembayaran</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Manual Transaction Modal -->
    {{-- Modal Catat Transaksi Manual --}}
    <div id="manual-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true" x-data="manualTransactionForm()" x-init="init()">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeManualModal()"></div>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('admin.transaksi.store-manual') }}" method="POST" enctype="multipart/form-data"
                    class="p-6">
                    @csrf
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-bold text-gray-900">Catat Transaksi Manual</h3>
                        <button type="button" onclick="closeManualModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">

                        {{-- Penyewa --}}
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Penyewa <span
                                    class="text-red-500">*</span></label>
                            <input type="hidden" name="penyewa_id" :value="selectedTenantId">
                            <input type="text" x-model="tenantSearch" @focus="showTenantDropdown = true"
                                @input="showTenantDropdown = true; selectedTenantId = ''"
                                @click.away="showTenantDropdown = false" placeholder="Ketik atau pilih nama penyewa..."
                                autocomplete="off"
                                class="w-full rounded-xl text-sm px-3 py-2.5 {{ $errors->has('penyewa_id') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500' }} border focus:outline-none focus:ring-1">
                            <div x-show="showTenantDropdown && filteredTenants.length > 0"
                                class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl shadow-lg mt-1 max-h-48 overflow-y-auto">
                                <template x-for="tenant in filteredTenants" :key="tenant.id">
                                    <div @click="selectTenant(tenant)"
                                        class="px-4 py-2.5 text-sm cursor-pointer hover:bg-emerald-50 hover:text-emerald-700 transition flex justify-between items-center">
                                        <span x-text="tenant.name"></span>
                                        <span x-show="tenant.room_number" class="text-xs text-gray-400"
                                            x-text="'Kamar ' + tenant.room_number"></span>
                                    </div>
                                </template>
                            </div>
                            <div x-show="showTenantDropdown && tenantSearch.length > 0 && filteredTenants.length === 0"
                                class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl shadow-lg mt-1 px-4 py-3 text-sm text-gray-400">
                                Penyewa tidak ditemukan.
                            </div>
                            @error('penyewa_id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kamar --}}
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kamar <span
                                    class="text-red-500">*</span></label>
                            <input type="hidden" name="kamar_id" :value="selectedRoomId">
                            <input type="text" x-model="roomSearch" @focus="showRoomDropdown = true"
                                @input="showRoomDropdown = true; selectedRoomId = ''; pricePerMonth = 0; updateAmount()"
                                @click.away="showRoomDropdown = false" placeholder="Ketik nomor atau tipe kamar..."
                                autocomplete="off"
                                class="w-full rounded-xl text-sm px-3 py-2.5 {{ $errors->has('kamar_id') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500' }} border focus:outline-none focus:ring-1">
                            <div x-show="showRoomDropdown && filteredRooms.length > 0"
                                class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl shadow-lg mt-1 max-h-48 overflow-y-auto">
                                <template x-for="room in filteredRooms" :key="room.id">
                                    <div @click="selectRoom(room)"
                                        class="px-4 py-2.5 text-sm cursor-pointer transition flex justify-between items-center gap-2"
                                        :class="room.penyewa_id ? 'hover:bg-blue-50' : 'hover:bg-emerald-50 hover:text-emerald-700'">
                                        <div class="flex flex-col min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold" x-text="'Kamar ' + room.number"></span>
                                                <span class="text-gray-400">&mdash;</span>
                                                <span x-text="room.type"></span>
                                            </div>
                                            <span class="text-xs truncate"
                                                :class="room.penyewa_id ? 'text-blue-500' : 'text-emerald-500'"
                                                x-text="room.penyewa_id ? '👤 ' + room.tenant_name : '✓ Tersedia'"></span>
                                        </div>
                                        <span class="text-xs text-gray-400 flex-shrink-0"
                                            x-text="'Rp ' + room.price.toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                            </div>
                            <div x-show="showRoomDropdown && roomSearch.length > 0 && filteredRooms.length === 0"
                                class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl shadow-lg mt-1 px-4 py-3 text-sm text-gray-400">
                                Kamar tidak ditemukan.
                            </div>
                            @error('kamar_id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Metode Pembayaran <span
                                    class="text-red-500">*</span></label>
                            <select name="payment_method"
                                class="w-full rounded-xl text-sm px-3 py-2.5 {{ $errors->has('payment_method') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500' }} border focus:outline-none focus:ring-1"
                                required>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai (Cash)
                                </option>
                                <option value="manual_transfer" {{ old('payment_method') == 'manual_transfer' ? 'selected' : '' }}>Transfer Manual (Admin)</option>
                                <option value="edc" {{ old('payment_method') == 'edc' ? 'selected' : '' }}>EDC / Mesin Kartu
                                </option>
                            </select>
                            @error('payment_method')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Durasi & Nominal --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Durasi (Bulan) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="duration" min="1" x-model.number="duration"
                                    @input="updateAmount()"
                                    class="w-full rounded-xl text-sm px-3 py-2.5 {{ $errors->has('duration') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500' }} border focus:outline-none focus:ring-1"
                                    required>
                                @error('duration')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nominal (Rp) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="amount" min="1" x-model.number="amount"
                                    class="w-full rounded-xl text-sm px-3 py-2.5 {{ $errors->has('amount') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500' }} border focus:outline-none focus:ring-1 bg-emerald-50"
                                    required>
                                <p class="text-xs text-gray-400 mt-1" x-show="pricePerMonth > 0">
                                    <span x-text="'Rp ' + pricePerMonth.toLocaleString('id-ID')"></span>/bln &times; <span
                                        x-text="duration"></span> bln
                                </p>
                                @error('amount')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Bukti --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Bukti / Nota <span
                                    class="text-red-500">*</span></label>
                            <input type="file" name="payment_proof" id="manual-proof" accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition {{ $errors->has('payment_proof') ? 'border border-red-500 rounded-xl p-1' : '' }}">
                            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG. Maksimal 2MB.</p>
                            @error('payment_proof')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                            <textarea name="notes" rows="2"
                                class="w-full rounded-xl text-sm px-3 py-2.5 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 border focus:outline-none focus:ring-1"
                                placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeManualModal()"
                            class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition shadow-sm">Simpan
                            Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function manualTransactionForm() {
            const tenantsData = @json($penyewaUntukJs);
            const roomsData = @json($kamarUntukJs);

            return {
                tenants: tenantsData,
                rooms: roomsData,

                tenantSearch: '',
                selectedTenantId: '',
                showTenantDropdown: false,

                roomSearch: '',
                selectedRoomId: '',
                showRoomDropdown: false,

                duration: 1,
                amount: 0,
                pricePerMonth: 0,

                get filteredTenants() {
                    if (!this.tenantSearch) return this.tenants;
                    const q = this.tenantSearch.toLowerCase();
                    return this.tenants.filter(t => t.name.toLowerCase().includes(q));
                },

                get filteredRooms() {
                    if (!this.roomSearch) return this.rooms;
                    const q = this.roomSearch.toLowerCase();
                    return this.rooms.filter(r =>
                        r.number.toLowerCase().includes(q) ||
                        r.type.toLowerCase().includes(q)
                    );
                },

                selectTenant(tenant) {
                    this.selectedTenantId = tenant.id;
                    this.tenantSearch = tenant.name;
                    this.showTenantDropdown = false;
                    // Auto-pilih kamar milik penyewa ini
                    if (tenant.kamar_id && !this.selectedRoomId) {
                        const room = this.rooms.find(r => r.id == tenant.kamar_id);
                        if (room) this.selectRoom(room);
                    }
                },

                selectRoom(room) {
                    this.selectedRoomId = room.id;
                    this.roomSearch = 'Kamar ' + room.number + ' — ' + room.type;
                    this.showRoomDropdown = false;
                    this.pricePerMonth = room.price;
                    this.updateAmount();
                    // Auto-isi penyewa yang menghuni kamar ini
                    if (room.penyewa_id && !this.selectedTenantId) {
                        const tenant = this.tenants.find(t => t.id == room.penyewa_id);
                        if (tenant) {
                            this.selectedTenantId = tenant.id;
                            this.tenantSearch = tenant.name;
                        }
                    }
                },

                updateAmount() {
                    if (this.pricePerMonth > 0) {
                        this.amount = this.pricePerMonth * this.duration;
                    }
                },

                init() {
                    // Pulihkan nilai lama setelah validasi gagal
                    const oldTenantId = '{{ old("penyewa_id") }}';
                    const oldRoomId = '{{ old("kamar_id") }}';
                    const oldDuration = {{ old('duration', 1) }};
                    const oldAmount = {{ old('amount', 0) }};

                    if (oldTenantId) {
                        const t = this.tenants.find(t => t.id == oldTenantId);
                        if (t) { this.selectedTenantId = t.id; this.tenantSearch = t.name; }
                    }
                    if (oldRoomId) {
                        const r = this.rooms.find(r => r.id == oldRoomId);
                        if (r) {
                            this.selectedRoomId = r.id;
                            this.roomSearch = 'Kamar ' + r.number + ' — ' + r.type;
                            this.pricePerMonth = r.price;
                        }
                    }
                    this.duration = oldDuration;
                    this.amount = oldAmount > 0 ? oldAmount : (this.pricePerMonth * this.duration);

                    // Buka modal otomatis jika ada error validasi
                    @if($errors->hasAny(['penyewa_id', 'kamar_id', 'amount', 'duration', 'payment_method', 'payment_proof', 'notes']))
                        document.getElementById('manual-modal').classList.remove('hidden');
                    @endif
                    }
            };
        }

        // Validasi ukuran file bukti
        document.getElementById('manual-proof').addEventListener('change', function () {
            if (this.files[0] && this.files[0].size > 2 * 1024 * 1024) {
                Toast.fire({ icon: 'warning', title: 'Ukuran file terlalu besar! Maksimal 2MB.' });
                this.value = '';
            }
        });
    </script>


@endsection
@push('tour')
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.__pageTour = [
        { popover: { title: 'Data Transaksi', description: 'Verifikasi bukti pembayaran penyewa, atau catat pembayaran manual (tunai/transfer) lewat tombol input transaksi.' } },
    ];
    window.KostTour && window.KostTour.auto('admin-transaksi-v1', window.__pageTour);
});
</script>
@endpush
