@extends('layouts.admin')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Manajemen Kamar') }}
            </h2>
            <p class="text-sm text-gray-500">Kelola ketersediaan dan status kamar.</p>
        </div>
        <div>
            <button onclick="openAddRoomModal()" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:border-emerald-900 focus:ring ring-emerald-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm cursor-pointer">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Kamar
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-8 relative">
            
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-gray-50 text-gray-500 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Unit</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $totalKamar }}</p>
                </div>
            </div>
            <div class="bg-emerald-50 p-5 rounded-2xl border border-emerald-100 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 text-emerald-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-emerald-700 uppercase">Kosong</p>
                    <p class="text-2xl font-extrabold text-emerald-600">{{ $kamarTersedia }} <span class="text-xs font-bold text-emerald-500">Unit</span></p>
                </div>
            </div>
            {{-- Occupied Rooms Card --}}
            <div class="bg-purple-50 p-5 rounded-2xl border border-purple-100 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-purple-100 text-purple-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-purple-700 uppercase">Terisi</p>
                    <p class="text-2xl font-extrabold text-purple-600">{{ $kamarTerisi ?? 0 }} <span class="text-xs font-bold text-purple-500">Unit</span></p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                 <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Penghuni</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $totalPenyewa }} <span class="text-xs font-bold text-gray-400">Org</span></p>
                </div>
            </div>
            <div class="bg-red-50 p-5 rounded-2xl border border-red-100 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-red-100 text-red-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-red-700 uppercase">Perbaikan</p>
                    <p class="text-2xl font-extrabold text-red-600">{{ $kamarMaintenance }} <span class="text-xs font-bold text-red-500">Unit</span></p>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 space-y-4">
            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.kamar') }}" id="filterForm">
                @if(request('view') == 'list')
                    <input type="hidden" name="view" value="list">
                @endif
                <div class="flex flex-col md:flex-row gap-4">
                    {{-- Search Input --}}
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $search ?? '' }}"
                                placeholder="Cari nomor kamar atau nama penyewa..."
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- Filter Dropdowns --}}
                    <div class="flex flex-wrap gap-2">
                        {{-- Status Filter --}}
                        <select name="status" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition min-w-[140px]">
                            <option value="semua" {{ ($selectedStatus ?? '') == 'semua' || !($selectedStatus ?? '') ? 'selected' : '' }}>📊 Semua Status</option>
                            <option value="available" {{ ($selectedStatus ?? '') == 'available' ? 'selected' : '' }}>✅ Tersedia</option>
                            <option value="occupied" {{ ($selectedStatus ?? '') == 'occupied' ? 'selected' : '' }}>🏠 Ditempati</option>
                            <option value="maintenance" {{ ($selectedStatus ?? '') == 'maintenance' ? 'selected' : '' }}>🔧 Maintenance</option>
                        </select>

                        {{-- Room Type Filter --}}
                        <select name="room_type" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition min-w-[130px]">
                            <option value="semua" {{ ($selectedRoomType ?? '') == 'semua' || !($selectedRoomType ?? '') ? 'selected' : '' }}>🛏️ Semua Tipe</option>
                            @foreach($tipeKamar as $type)
                                <option value="{{ $type->id }}" {{ ($selectedRoomType ?? '') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Payment Status Filter --}}
                        <select name="payment" onchange="document.getElementById('filterForm').submit()" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition min-w-[150px]">
                            <option value="semua" {{ ($selectedPayment ?? '') == 'semua' || !($selectedPayment ?? '') ? 'selected' : '' }}>💳 Semua Bayar</option>
                            <option value="lancar" {{ ($selectedPayment ?? '') == 'lancar' ? 'selected' : '' }}>✅ Lancar</option>
                            <option value="mau_habis" {{ ($selectedPayment ?? '') == 'mau_habis' ? 'selected' : '' }}>⏰ Segera Habis</option>
                            <option value="nunggak" {{ ($selectedPayment ?? '') == 'nunggak' ? 'selected' : '' }}>🚨 Telat Bayar</option>
                        </select>

                        {{-- Search Button --}}
                        <button type="submit" class="px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>

                        {{-- Reset Button --}}
                        @if(($search ?? '') || ($selectedStatus ?? '') || ($selectedRoomType ?? '') || ($selectedPayment ?? '') || ($selectedFloor ?? ''))
                            <a href="{{ route('admin.kamar') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-200 transition flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Hidden inputs to preserve other filters --}}
                @if($selectedFloor ?? '')
                    <input type="hidden" name="floor" value="{{ $selectedFloor }}">
                @endif
            </form>

            {{-- Floor Tabs & View Toggle --}}
            <div class="flex flex-wrap items-center justify-between pt-2 border-t border-gray-100 gap-4">
                {{-- Left: Floor Tabs --}}
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.kamar', array_merge(request()->except(['floor', 'page']), ['floor' => null])) }}" class="px-5 py-2 rounded-xl text-sm font-bold transition {{ !$selectedFloor ? 'bg-emerald-800 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                        SEMUA LANTAI
                    </a>
                    @for ($i = 1; $i <= 4; $i++)
                        <a href="{{ route('admin.kamar', array_merge(request()->except(['floor', 'page']), ['floor' => $i])) }}" class="px-5 py-2 rounded-xl text-sm font-medium transition {{ $selectedFloor == $i ? 'bg-emerald-700 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                            Lantai {{ $i }}
                        </a>
                    @endfor
                </div>

                {{-- Right: View Toggle --}}
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500 font-medium">Tampilan:</span>
                    <div class="bg-gray-100 p-1 rounded-lg flex items-center">
                        <a href="{{ route('admin.kamar', array_merge(request()->query(), ['view' => 'grid'])) }}" class="p-2 rounded-md {{ request('view', 'grid') == 'grid' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-400 hover:text-gray-600' }} transition" title="Tampilan Grid">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </a>
                        <a href="{{ route('admin.kamar', array_merge(request()->query(), ['view' => 'list'])) }}" class="p-2 rounded-md {{ request('view') == 'list' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-400 hover:text-gray-600' }} transition" title="Tampilan List">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Active Filters Info --}}
            @if(($search ?? '') || (($selectedStatus ?? '') && $selectedStatus != 'semua') || (($selectedRoomType ?? '') && $selectedRoomType != 'semua') || (($selectedPayment ?? '') && $selectedPayment != 'semua'))
                <div class="flex items-center gap-2 text-xs text-gray-500 pt-2">
                    <span class="font-bold">Filter Aktif:</span>
                    @if($search ?? '')
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full">🔍 "{{ $search }}"</span>
                    @endif
                    @if(($selectedStatus ?? '') && $selectedStatus != 'semua')
                        <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full">Status: {{ ucfirst($selectedStatus) }}</span>
                    @endif
                    @if(($selectedRoomType ?? '') && $selectedRoomType != 'semua')
                        <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded-full">Tipe: {{ $tipeKamar->find($selectedRoomType)->name ?? 'Unknown' }}</span>
                    @endif
                    @if(($selectedPayment ?? '') && $selectedPayment != 'semua')
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">Bayar: {{ ucfirst(str_replace('_', ' ', $selectedPayment)) }}</span>
                    @endif
                    <span class="text-gray-400">({{ $kamar->count() }} hasil)</span>
                </div>
            @endif
        </div>

        {{-- Grid View --}}
        <div id="gridView" class="space-y-6 {{ request('view') == 'list' ? 'hidden' : '' }}">
            @forelse ($kamar as $room)
                @php
                    // Check if room has tenant - source of truth is now occupants pivot
                    $hasTenant = $room->occupants->isNotEmpty();
                    $isOccupied = $hasTenant || $room->status === 'occupied';
                    $isMaintenance = $room->status === 'maintenance';
                    $isAvailable = !$hasTenant && $room->status !== 'maintenance';
                @endphp

                @if ($isMaintenance)
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-red-100 overflow-hidden flex flex-col md:flex-row h-auto group hover:shadow-md transition-all duration-200 relative">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-red-500"></div>
                        <div class="w-full md:w-40 bg-white text-red-700 flex flex-col items-center justify-center p-6 flex-shrink-0 border-r-2 border-red-100 pl-8">
                            <span class="text-xs font-medium opacity-80 uppercase tracking-widest mb-1">KAMAR</span>
                            <span class="text-5xl font-extrabold">{{ $room->room_number }}</span>
                            <span class="text-xs mt-2 bg-red-50 text-red-600 border border-red-200 animate-pulse px-3 py-1 rounded-full font-bold">⚠️ MASALAH</span>
                        </div>
                        <div class="flex-1 flex flex-col divide-y divide-gray-50">
                            <div class="flex-1 flex items-center justify-between px-6 py-4 bg-gray-50/30 opacity-60">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-700 flex items-center justify-center font-bold text-sm shadow-sm">-</div>
                                    <div>
                                        <p class="text-base font-bold text-gray-400 italic">-- Masalah --</p>
                                        <p class="text-xs text-gray-400">Sedang dalam perbaikan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 flex items-center justify-between px-6 py-4 bg-gray-50/30 opacity-60">
                                <div class="flex items-center gap-4">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div>
                                        <p class="text-sm font-bold text-gray-600">Status</p>
                                        <p class="text-xs text-gray-400">Tipe: {{ $room->roomType->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-red-600">Rp {{ number_format($room->price_per_month, 0, ',', '.') }} / bulan</p>
                                </div>
                            </div>
                        </div>
                        {{-- Admin Actions --}}
                        <div class="w-full md:w-32 bg-gray-50 p-4 flex flex-col justify-center items-center border-l gap-2">
                             <form action="{{ route('admin.kamar.updateStatus', $room->id) }}" method="POST" class="w-full" onsubmit="confirmSubmit(event, 'Tandai kamar ini sebagai Selesai Perbaikan (Tersedia)?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="available">
                                <button type="submit" class="w-full bg-white border border-green-200 text-green-600 hover:bg-green-50 text-xs font-bold py-2 rounded-lg transition">
                                    Selesai
                                </button>
                            </form>
                            <form action="{{ route('admin.kamar.destroy', $room->id) }}" method="POST" class="w-full" onsubmit="confirmSubmit(event, 'Hapus kamar ini permanen?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold py-2 rounded-lg transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif ($isOccupied)
                    @php
                        $capacity = $room->roomType->capacity ?? 1;
                        // Get ALL occupants sorted by check_in_date (earliest first = Slot A)
                        $allOccupants = $room->occupants->sortBy(fn($o) => $o->pivot->check_in_date ?? $o->created_at)->values();
                        $occupantCount = $allOccupants->count();
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 overflow-hidden flex flex-col md:flex-row h-auto group hover:shadow-md transition-all duration-200">
                        <div class="w-full md:w-40 bg-gradient-to-br from-emerald-700 to-emerald-900 text-white flex flex-col items-center justify-center p-6 flex-shrink-0">
                            <span class="text-xs font-medium opacity-80 uppercase tracking-widest mb-1">KAMAR</span>
                            <span class="text-5xl font-extrabold">{{ $room->room_number }}</span>
                            <span class="text-xs mt-1 font-bold opacity-75 uppercase tracking-wide">{{ $room->roomType->name }}</span>
                            @if($capacity > 1)
                                <span class="text-xs mt-2 bg-emerald-600/50 text-emerald-100 border border-emerald-500 px-3 py-1 rounded-full font-semibold">
                                    {{ $occupantCount }}/{{ $capacity }} SLOT
                                </span>
                            @else
                                <span class="text-xs mt-2 bg-emerald-600/50 text-emerald-100 border border-emerald-500 px-3 py-1 rounded-full font-semibold">✅ DITEMPATI</span>
                            @endif
                        </div>
                        <div class="flex-1 flex flex-col divide-y divide-gray-50 border-x border-gray-50">
                            {{-- Loop through all slots, showing occupants sorted by check_in_date --}}
                            @for($slot = 0; $slot < $capacity; $slot++)
                                @php
                                    $occupant = $allOccupants->values()->get($slot);
                                    
                                    // Default Styling
                                    $statusColor = 'hover:bg-gray-50';
                                    $borderColor = 'border-l-4 border-l-transparent';
                                    $iconColor = 'bg-purple-100 text-purple-600 border-purple-200';
                                    $statusBadge = 'bg-gray-100 text-gray-500';
                                    $statusText = 'Belum Bayar';

                                    if ($occupant) {
                                        $lastTransaction = $occupant->tenantTransactions()
                                            ->where('status', 'verified_by_owner')
                                            ->latest('period_end_date')
                                            ->first();

                                        if ($lastTransaction) {
                                            $endDate = \Carbon\Carbon::parse($lastTransaction->period_end_date);
                                            $daysRemaining = now()->diffInDays($endDate, false);
                                            
                                            if ($daysRemaining < 0) {
                                                // Nunggak (Overdue) - Red
                                                $statusColor = 'bg-red-50 hover:bg-red-100';
                                                $borderColor = 'border-l-4 border-l-red-500';
                                                $iconColor = 'bg-red-100 text-red-600 border-red-200';
                                                $statusBadge = 'bg-red-100 text-red-700';
                                                $statusText = 'TELAT BAYAR';
                                            } elseif ($daysRemaining <= ($reminderDays ?? 7)) {
                                                // Mau Habis (Expiring Soon) - Yellow
                                                $statusColor = 'bg-yellow-50 hover:bg-yellow-100';
                                                $borderColor = 'border-l-4 border-l-yellow-500';
                                                $iconColor = 'bg-yellow-100 text-yellow-600 border-yellow-200';
                                                $statusBadge = 'bg-yellow-100 text-yellow-700';
                                                $statusText = 'SEGERA HABIS';
                                            } else {
                                                // Lancar (Good)
                                                $statusBadge = 'bg-emerald-100 text-emerald-700';
                                                $statusText = 'LANCAR';
                                            }
                                        }
                                    }
                                @endphp
                                @if($occupant)
                                    <div class="flex-1 flex items-center justify-between px-6 py-4 transition 
                                        {{ isset($statusColor) ? $statusColor : 'hover:bg-gray-50' }} 
                                        {{ isset($borderColor) ? $borderColor : '' }}">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full {{ isset($iconColor) ? $iconColor : 'bg-purple-100 text-purple-600' }} flex items-center justify-center font-bold text-sm border shadow-sm">
                                                {{ chr(65 + $slot) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('admin.penyewa.show', ['user' => $occupant->id, 'from' => 'kamar']) }}" class="text-base font-bold text-gray-800 hover:text-emerald-600 hover:underline">
                                                        {{ $occupant->name }}
                                                    </a>
                                                </div>
                                                <p class="text-xs text-gray-500 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    Masuk: {{ $occupant->pivot->check_in_date ? \Carbon\Carbon::parse($occupant->pivot->check_in_date)->format('d M Y') : $occupant->created_at->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="{{ $statusBadge }} text-xs font-bold px-3 py-1 rounded-full flex items-center justify-end gap-1 ml-auto w-fit">
                                                {{ $statusText }}
                                            </span>
                                            <p class="text-xs text-gray-400 mt-1 font-medium">Rp {{ number_format($room->rent_per_person, 0, ',', '.') }}{{ $capacity > 1 ? '/org' : '' }}</p>
                                        </div>
                                    </div>
                                @else
                                    {{-- Empty slot --}}
                                    <div class="flex-1 flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition border-l-4 border-l-transparent">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm shadow-sm">{{ chr(65 + $slot) }}</div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-base font-bold text-gray-400 italic">-- Kosong --</p>
                                                </div>
                                                <p class="text-xs text-gray-400">Siap ditempati</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-400 mt-1 font-medium">Rp {{ number_format($room->rent_per_person, 0, ',', '.') }}/org</p>
                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </div>
                        <div class="w-full md:w-32 bg-gray-50 p-4 flex flex-col justify-center items-center gap-2 border-l border-gray-100">
                             {{-- Detail buttons for each occupant --}}
                             @foreach($allOccupants as $index => $occupant)
                                 <a href="{{ route('admin.penyewa.show', ['user' => $occupant->id, 'from' => 'kamar']) }}" class="w-full bg-white border border-gray-200 text-gray-600 hover:border-emerald-500 hover:text-emerald-700 text-xs font-bold py-2 rounded-lg transition text-center">
                                    @if($capacity > 1)
                                        Detail {{ chr(65 + $index) }}
                                    @else
                                        Detail
                                    @endif
                                 </a>
                             @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-emerald-100 overflow-hidden flex flex-col md:flex-row h-auto group hover:shadow-md transition-all duration-200 relative">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-emerald-400"></div>
                        <div class="w-full md:w-40 bg-white text-emerald-700 flex flex-col items-center justify-center p-6 flex-shrink-0 border-r-2 border-emerald-100 pl-8">
                            <span class="text-xs font-bold opacity-60 uppercase tracking-widest mb-1">KAMAR</span>
                            <span class="text-5xl font-extrabold">{{ $room->room_number }}</span>
                            <span class="text-xs mt-1 font-bold opacity-75 uppercase tracking-wide text-emerald-600/70">{{ $room->roomType->name }}</span>
                            <span class="text-xs mt-2 bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1 rounded-full font-bold">✅ TERSEDIA</span>
                        </div>
                        <div class="flex-1 flex flex-col divide-y divide-gray-50">
                            <div class="flex-1 flex items-center justify-between px-6 py-4 bg-gray-50/30 opacity-60">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-white border-2 border-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm">A</div>
                                    <p class="text-base font-bold text-gray-400 italic">-- Kosong --</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-emerald-600">Rp {{ number_format($room->price_per_month, 0, ',', '.') }} / bulan</p>
                                </div>
                            </div>
                        </div>
                         {{-- Admin Actions for Available Room --}}
                        <div class="w-full md:w-32 bg-gray-50 p-4 flex flex-col justify-center items-center border-l gap-2">
                             <form action="{{ route('admin.kamar.updateStatus', $room->id) }}" method="POST" class="w-full" onsubmit="confirmSubmit(event, 'Tandai kamar ini sedang Maintenance?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="maintenance">
                                <button type="submit" class="w-full bg-white border border-yellow-200 text-yellow-600 hover:bg-yellow-50 text-xs font-bold py-2 rounded-lg transition">
                                    Maintenance
                                </button>
                            </form>
                            <form action="{{ route('admin.kamar.destroy', $room->id) }}" method="POST" class="w-full" onsubmit="confirmSubmit(event, 'Hapus kamar ini permanen?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold py-2 rounded-lg transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-12 text-center text-gray-400">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <p class="text-lg font-medium">Belum ada data kamar.</p>
                    <p class="text-sm">Silakan tambah kamar baru.</p>
                </div>
            @endforelse
            
            {{-- Pagination for Grid View --}}
            @if($kamar instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $kamar->hasPages())
                <div class="mt-6 px-6 py-4 bg-gray-50/50 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4">
                    <span class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold text-gray-900">{{ $kamar->firstItem() ?? 1 }}</span> - <span class="font-semibold text-gray-900">{{ $kamar->lastItem() ?? $kamar->count() }}</span> dari <span class="font-semibold text-emerald-600">{{ $kamar->total() }}</span> kamar
                    </span>
                    <nav class="flex items-center gap-1">
                        {{ $kamar->appends(request()->query())->links('components.pagination.admin') }}
                    </nav>
                </div>
            @endif
        </div> {{-- End Grid View --}}

        {{-- List View --}}
        <div id="listView" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden {{ request('view') == 'list' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Kamar</th>
                            <th class="px-6 py-4">Lantai</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Penghuni</th>
                            <th class="px-6 py-4">Harga/Bulan</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($kamar as $room)
                            @php
                                $hasTenant = $room->occupants->isNotEmpty();
                                $isOccupied = $hasTenant || $room->status === 'occupied';
                                $capacity = $room->roomType->capacity ?? 1;
                                $allOccupants = $room->occupants->sortBy(fn($o) => $o->pivot->check_in_date ?? $o->created_at)->values();
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition bg-white">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg {{ $isOccupied ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }} flex flex-col items-center justify-center font-bold">
                                            <span>{{ $room->room_number }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800">{{ $room->roomType->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $room->roomType->capacity > 1 ? 'Kapasitas: '.$room->roomType->capacity : 'Single' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Lantai {{ $room->floor_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-2">
                                        @if($room->status === 'maintenance')
                                             <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-bold bg-red-100 text-red-600">
                                                ⚠️ MAINTENANCE
                                            </span>
                                        @elseif(!$isOccupied)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-600">
                                                ✅ TERSEDIA
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-600">
                                                🔒 DITEMPATI
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($isOccupied)
                                        <div class="flex flex-col gap-2">
                                             @foreach($allOccupants as $index => $occupant)
                                                @php
                                                    $lastTransaction = $occupant->tenantTransactions()
                                                        ->where('status', 'verified_by_owner')
                                                        ->latest('period_end_date')
                                                        ->first();
                                                    
                                                    $listStatusBadge = 'bg-gray-100 text-gray-500';
                                                    $listStatusText = 'Belum Bayar';
                                                    
                                                    if ($lastTransaction) {
                                                        $endDate = \Carbon\Carbon::parse($lastTransaction->period_end_date);
                                                        $daysRemaining = now()->diffInDays($endDate, false);
                                                        
                                                        if ($daysRemaining < 0) {
                                                            // Overdue
                                                            $listStatusBadge = 'bg-red-100 text-red-700';
                                                            $listStatusText = 'Telat Bayar';
                                                        } elseif ($daysRemaining <= ($reminderDays ?? 7)) {
                                                            // Expiring Soon
                                                            $listStatusBadge = 'bg-yellow-100 text-yellow-700';
                                                            $listStatusText = 'Segera Habis';
                                                        } else {
                                                            // Good
                                                            $listStatusBadge = 'bg-emerald-100 text-emerald-700';
                                                            $listStatusText = 'Lancar';
                                                        }
                                                    }
                                                @endphp
                                                <div class="flex items-center justify-between gap-4 p-2 rounded-lg bg-gray-50/50 border border-gray-100">
                                                    <div class="flex items-center gap-2">
                                                        @if($capacity > 1)
                                                            <span class="w-5 h-5 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-[10px] border border-purple-200">{{ chr(65 + $index) }}</span>
                                                        @endif
                                                        <a href="{{ route('admin.penyewa.show', ['user' => $occupant->id, 'from' => 'kamar']) }}" class="font-bold text-gray-700 hover:text-emerald-600 hover:underline text-sm">{{ $occupant->name }}</a>
                                                    </div>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $listStatusBadge }}">
                                                        {{ $listStatusText }}
                                                    </span>
                                                </div>
                                             @endforeach
                                             @for($i = $allOccupants->count(); $i < $capacity; $i++)
                                                <div class="flex items-center gap-2 opacity-50 p-2">
                                                    @if($capacity > 1)
                                                        <span class="w-5 h-5 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-[10px] border border-gray-200">{{ chr(65 + $i) }}</span>
                                                    @endif
                                                    <span class="text-xs italic text-gray-400">-- Kosong --</span>
                                                </div>
                                             @endfor
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-emerald-600">
                                        Rp {{ number_format($room->price_per_month, 0, ',', '.') }}
                                        <span class="text-xs text-gray-400 font-normal">/bln</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        @if($isOccupied)
                                            @foreach($allOccupants as $index => $occupant)
                                                <a href="{{ route('admin.penyewa.show', ['user' => $occupant->id, 'from' => 'kamar']) }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-emerald-600 hover:border-emerald-500 transition" title="Detail {{ $occupant->name }}">
                                                    <span class="font-bold text-xs">{{ $capacity > 1 ? chr(65 + $index) : 'Detail' }}</span>
                                                </a>
                                            @endforeach
                                        @elseif($room->status === 'maintenance')
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('admin.kamar.updateStatus', $room->id) }}" method="POST" onsubmit="confirmSubmit(event, 'Tandai kamar ini sebagai Selesai Perbaikan (Tersedia)?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="available">
                                                    <button type="submit" class="text-xs px-3 py-1 bg-emerald-100 text-emerald-600 rounded-lg font-bold hover:bg-emerald-200 transition">
                                                        Selesai
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.kamar.destroy', $room->id) }}" method="POST" onsubmit="confirmSubmit(event, 'Hapus kamar ini permanen?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs px-3 py-1 bg-red-100 text-red-600 rounded-lg font-bold hover:bg-red-200 transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    Tidak ada data kamar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                     {{-- Pagination for List --}}
                    {{-- Pagination for List --}}
                    @if($kamar instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                        @php 
                            $kamar->appends(['view' => 'list']); 
                        @endphp
                        @if ($kamar->hasPages())
                            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                                <div class="text-sm text-gray-500">
                                    Menampilkan <span class="font-semibold text-gray-900">{{ $kamar->firstItem() ?? 1 }}</span> - <span class="font-semibold text-gray-900">{{ $kamar->lastItem() ?? $kamar->count() }}</span> dari <span class="font-semibold text-emerald-600">{{ $kamar->total() }}</span> kamar
                                </div>
                                
                                {{ $kamar->appends(request()->query())->links('components.pagination.admin') }}
                            </div>
                        @else
                             <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                                <div class="text-sm text-gray-500">
                                    Menampilkan <span class="font-semibold text-gray-900">{{ $kamar->count() }}</span> kamar
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            
        </div>

@push('scripts')
{{-- Removed switchView JS as it is now server-side handled --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toastMsg = sessionStorage.getItem('toastMessage');
        if (toastMsg) {
            sessionStorage.removeItem('toastMessage');
            Toast.fire(JSON.parse(toastMsg));
        }
    });
    function openAddRoomModal() {
        const modal = document.getElementById('addRoomModal');
        modal.classList.remove('hidden');
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Focus on first input
         setTimeout(() => {
            document.getElementById('room_number').focus();
        }, 100);
    }

    function closeAddRoomModal() {
        const modal = document.getElementById('addRoomModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function updatePrice() {
        const select = document.getElementById('tipe_kamar_id');
        const selectedOption = select.options[select.selectedIndex];
        const priceInput = document.getElementById('price_per_month');
        if (selectedOption && selectedOption.dataset.price) {
            priceInput.value = selectedOption.dataset.price;
        } else {
            priceInput.value = '';
        }
    }

    // Call updatePrice on page load if an old value is present
    document.addEventListener('DOMContentLoaded', () => {
        // Removed switchView call as it is handled server-side
    });
</script>
@endpush
    </div>

    {{-- Add Room Modal --}}
    <div id="addRoomModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Background Overlay --}}
            <div class="fixed inset-0 bg-black/50 transition-opacity" aria-hidden="true" onclick="closeAddRoomModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel --}}
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                     <div class="flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Tambah Kamar Baru</h3>
                        <button onclick="closeAddRoomModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 max-h-[75vh] overflow-y-auto">
                    <form action="{{ route('admin.kamar.store') }}" method="POST" id="addRoomForm" class="space-y-4">
                        @csrf
                        
                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex gap-3 mb-4">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-xs text-blue-800 leading-relaxed">
                                Nomor kamar harus unik. Lantai & harga akan terisi otomatis (namun bisa disesuaikan).
                            </p>
                        </div>

                         <!-- Nama/Nomor Kamar -->
                        <div>
                            <label for="room_number" class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">
                                Nomor Kamar <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="room_number"
                                name="room_number"
                                value="{{ old('room_number') }}"
                                placeholder="Contoh: 101, 201"
                                min="1"
                                inputmode="numeric"
                                class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('room_number') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                required>
                            @error('room_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lantai -->
                        <div>
                            <label for="floor_number" class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">
                                Lantai <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="floor_number" 
                                name="floor_number"
                                class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('floor_number') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                required>
                                <option value="">-- Pilih Lantai --</option>
                                <option value="1" {{ old('floor_number') == '1' ? 'selected' : '' }}>Lantai 1</option>
                                <option value="2" {{ old('floor_number') == '2' ? 'selected' : '' }}>Lantai 2</option>
                                <option value="3" {{ old('floor_number') == '3' ? 'selected' : '' }}>Lantai 3</option>
                                <option value="4" {{ old('floor_number') == '4' ? 'selected' : '' }}>Lantai 4</option>
                            </select>
                            @error('floor_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipe Kamar & Harga -->
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Tipe Kamar -->
                            <div>
                                <label for="tipe_kamar_id" class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">
                                    Tipe <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="tipe_kamar_id" 
                                    name="tipe_kamar_id"
                                    class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('tipe_kamar_id') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                    required
                                    onchange="updatePrice()">
                                    <option value="">-- Pilih Tipe --</option>
                                    @foreach($tipeKamar as $type)
                                        <option value="{{ $type->id }}" data-price="{{ $type->price_per_month }}" {{ old('tipe_kamar_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipe_kamar_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                             <!-- Harga Per Bulan -->
                            <div>
                                <label for="price_per_month" class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">
                                    Harga <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold text-xs">Rp</span>
                                    <input 
                                        type="number" 
                                        id="price_per_month" 
                                        name="price_per_month" 
                                        value="{{ old('price_per_month') }}"
                                        class="w-full px-3 py-2.5 pl-9 rounded-xl border text-sm bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('price_per_month') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                        required
                                        readonly>
                                </div>
                                @error('price_per_month')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">
                                Status Awal <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="status" 
                                name="status"
                                class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('status') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                required>
                                <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>✅ Tersedia (Siap Huni)</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>🔧 Maintenance (Perbaikan)</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                         <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">
                                Catatan (Opsional)
                            </label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                rows="2"
                                placeholder="Ket.: Dekat tangga, jendela besar..."
                                class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition {{ $errors->has('notes') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300' }}">{{ old('notes') }}</textarea>
                        </div>

                    </form>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" form="addRoomForm" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-bold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Kamar
                    </button>
                    <button type="button" onclick="closeAddRoomModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAddRoomModal() {
            const modal = document.getElementById('addRoomModal');
            modal.classList.remove('hidden');
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
            
            // Focus on first input
             setTimeout(() => {
                document.getElementById('room_number').focus();
            }, 100);
        }

        function closeAddRoomModal() {
            const modal = document.getElementById('addRoomModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function updatePrice() {
            const select = document.getElementById('tipe_kamar_id');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            
            if (price) {
                document.getElementById('price_per_month').value = price;
            }
        }

        // Block non-numeric characters (e, +, -, .)
        document.getElementById('room_number').addEventListener('keydown', function(e) {
            if (['e', 'E', '+', '-', '.'].includes(e.key)) {
                e.preventDefault();
            }
        });

        // Auto floor detection
        document.getElementById('room_number').addEventListener('input', function(e) {
            const roomNumber = this.value;
            if (roomNumber && roomNumber.length >= 1) {
                const floor = roomNumber.charAt(0);
                if (floor >= '1' && floor <= '4') {
                    document.getElementById('floor_number').value = floor;
                }
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeAddRoomModal();
            }
        });

        // Auto-open if errors via Blade (Laravel Validation)
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                openAddRoomModal();
            });
        @endif
    </script>
@endsection
