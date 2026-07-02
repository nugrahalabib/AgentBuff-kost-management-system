@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Data Penyewa') }}
            </h2>
            <p class="text-sm text-gray-500">Kelola {{ $totalPenyewa }} penyewa aktif saat ini.</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-8">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
                 <div class="absolute right-0 top-0 h-full w-1 bg-emerald-500"></div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Penghuni Aktif</p>
                    <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $totalPenyewa }} <span class="text-sm font-normal text-gray-500">Orang</span></h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
                 <div class="absolute right-0 top-0 h-full w-1 bg-blue-500"></div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Penghuni Baru</p>
                    <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $penyewaBaru }} <span class="text-sm font-normal text-gray-500">Bulan ini</span></h3>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
                 <div class="absolute right-0 top-0 h-full w-1 bg-yellow-500"></div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kontrak Habis</p>
                    <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $expiringContracts }} <span class="text-sm font-normal text-gray-500">Minggu ini</span></h3>
                </div>
                <div class="p-3 bg-yellow-50 rounded-xl text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
             <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
                 <div class="absolute right-0 top-0 h-full w-1 bg-red-500"></div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Telat Bayar</p>
                    <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $delinquent }} <span class="text-sm font-normal text-gray-500">Orang</span></h3>
                </div>
                <div class="p-3 bg-red-50 rounded-xl text-red-600">
                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col xl:flex-row gap-4 justify-between items-center">
            
            <form method="GET" action="{{ route('owner.penyewa') }}" id="filterForm" class="flex flex-col xl:flex-row gap-4 w-full">
                <div class="flex-1 relative w-full xl:w-auto">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" id="searchInput" value="{{ $search ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 pr-10 p-3 transition" placeholder="Cari Nama, No HP, atau Kamar..." onkeypress="if(event.key === 'Enter') this.form.submit();" oninput="toggleClearBtn()">
                    @if($search)
                    <button type="button" id="clearSearchBtn" onclick="clearSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    @else
                    <button type="button" id="clearSearchBtn" onclick="clearSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition hidden">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3 w-full xl:w-auto items-center justify-end">
                    <select name="floor" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-3 cursor-pointer transition hover:bg-gray-100" onchange="this.form.submit()">
                        <option value="semua" {{ ($selectedFloor ?? '') == 'semua' || !$selectedFloor ? 'selected' : '' }}>Semua Lantai</option>
                        @foreach($availableFloors as $floorNum)
                            <option value="{{ $floorNum }}" {{ ($selectedFloor ?? '') == $floorNum ? 'selected' : '' }}>Lantai {{ $floorNum }}</option>
                        @endforeach
                    </select>

                    <select name="active" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-3 cursor-pointer transition hover:bg-gray-100" onchange="this.form.submit()">
                        <option value="semua" {{ ($selectedActive ?? '') == 'semua' || !$selectedActive ? 'selected' : '' }}>Semua Penghuni</option>
                        <option value="aktif" {{ ($selectedActive ?? '') == 'aktif' ? 'selected' : '' }}>🟢 Aktif</option>
                        <option value="tidak_aktif" {{ ($selectedActive ?? '') == 'tidak_aktif' ? 'selected' : '' }}>🔴 Tidak Aktif</option>
                    </select>

                    <select name="status" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block p-3 cursor-pointer transition hover:bg-gray-100" onchange="this.form.submit()">
                        <option value="semua" {{ ($selectedStatus ?? '') == 'semua' || !$selectedStatus ? 'selected' : '' }}>Semua Status</option>
                        <option value="lancar" {{ ($selectedStatus ?? '') == 'lancar' ? 'selected' : '' }}>✅ Lancar</option>
                        <option value="mau_habis" {{ ($selectedStatus ?? '') == 'mau_habis' ? 'selected' : '' }}>⏰ Segera Habis</option>
                        <option value="nunggak" {{ ($selectedStatus ?? '') == 'nunggak' ? 'selected' : '' }}>⚠️ Telat Bayar</option>
                    </select>

                    <div class="flex bg-gray-100 p-1 rounded-xl border border-gray-200">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="p-2.5 rounded-lg {{ ($viewMode ?? 'list') === 'list' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-400 hover:text-emerald-600' }} transition" title="Tampilan Tabel (Efisien)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="p-2.5 rounded-lg {{ ($viewMode ?? 'list') === 'grid' ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-400 hover:text-emerald-600' }} transition" title="Tampilan Kartu (Visual)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        @if(($viewMode ?? 'list') === 'list')
        <div id="view-list" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden animate-fade-in">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Penghuni</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kamar & Tipe</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Status Bayar</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Jatuh Tempo</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @forelse ($dataPenyewa as $tenant)
                            @php
                                $currentRoom = $tenant->getFirstRoom();
                                $phone = $tenant->tenantProfile?->phone ?? null;
                                
                                // Format Phone for WA (Replace 0 with 62)
                                $waPhone = null;
                                if ($phone) {
                                    $cleaned = preg_replace('/[^0-9]/', '', $phone);
                                    if (str_starts_with($cleaned, '0')) {
                                        $waPhone = '62' . substr($cleaned, 1);
                                    } else {
                                        $waPhone = $cleaned;
                                    }
                                }
                                
                                // Get latest transaction (by period_end_date desc)
                                $latestTx = $tenant->tenantTransactions?->sortByDesc('period_end_date')->first();
                                
                                // Calculate status based on transaction
                                $statusType = 'active'; // default
                                $statusLabel = 'Lancar';
                                $statusBg = 'bg-emerald-50';
                                $statusText = 'text-emerald-700';
                                $statusBorder = 'border-emerald-100';
                                $statusIcon = '✅';
                                $daysInfo = '';
                                $periodEndDate = $latestTx?->period_end_date;
                                
                                if ($latestTx) {
                                    $rejectedTx = $tenant->tenantTransactions?->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])->first();
                                    
                                    if ($rejectedTx) {
                                        $statusType = 'rejected';
                                        $statusLabel = 'Ditolak';
                                        $statusBg = 'bg-orange-100';
                                        $statusText = 'text-orange-700';
                                        $statusBorder = 'border-orange-200';
                                        $statusIcon = '❌';
                                    } elseif ($periodEndDate && $currentRoom && $periodEndDate < now()->startOfDay()) {
                                        // Expired - Nunggak
                                        $daysOverdue = (int)now()->startOfDay()->diffInDays($periodEndDate);
                                        $statusType = 'nunggak';
                                        $statusLabel = 'Telat Bayar';
                                        $statusBg = 'bg-red-100';
                                        $statusText = 'text-red-700';
                                        $statusBorder = 'border-red-200 animate-pulse';
                                        $statusIcon = '⚠️';
                                        $daysInfo = 'Telat ' . $daysOverdue . ' hari';
                                    } elseif ($periodEndDate && $currentRoom && $periodEndDate <= now()->addDays($reminderDays ?? 7)->endOfDay()) {
                                        // Expiring soon - Mau Habis
                                        $daysUntil = (int)now()->startOfDay()->diffInDays($periodEndDate);
                                        $statusType = 'expiring';
                                        $statusLabel = 'Segera Habis';
                                        $statusBg = 'bg-yellow-100';
                                        $statusText = 'text-yellow-700';
                                        $statusBorder = 'border-yellow-200';
                                        $statusIcon = '⏰';
                                        $daysInfo = 'H-' . $daysUntil;
                                    }
                                }
                                
                                $isWarning = in_array($statusType, ['nunggak', 'rejected']);
                                $rowClass = $isWarning 
                                    ? 'hover:bg-red-50/20 border-l-2 border-transparent hover:border-red-500 bg-red-50/5' 
                                    : 'hover:bg-gray-50 border-l-2 border-transparent hover:border-emerald-500';
                            @endphp
                            <tr class="{{ $rowClass }} transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-9 w-9 rounded-full object-cover border {{ $isWarning ? 'border-red-200' : 'border-gray-200' }}" src="https://ui-avatars.com/api/?name={{ urlencode($tenant->name) }}&background={{ $isWarning ? 'FEE2E2' : 'D1FAE5' }}&color={{ $isWarning ? 'B91C1C' : '059669' }}" alt="Foto">
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $tenant->name }}</p>
                                            <p class="text-xs {{ $daysInfo ? ($statusType === 'nunggak' ? 'text-red-500 font-bold' : 'text-yellow-600') : 'text-gray-500' }}">
                                                {{ $daysInfo ?: ($tenant->tenantProfile?->university ?? 'Tenant') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-700">Kamar {{ $currentRoom?->room_number ?? '-' }}</span>
                                        <span class="text-xs text-gray-400">{{ $currentRoom?->roomType?->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($currentRoom)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                            🟢 Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                            🔴 Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-medium">{{ $phone ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($currentRoom)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold {{ $statusBg }} {{ $statusText }} border {{ $statusBorder }}">
                                            <span>{{ $statusIcon }}</span> {{ $statusLabel }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 {{ $currentRoom && $statusType === 'nunggak' ? 'text-red-600 font-bold' : ($currentRoom && $statusType === 'expiring' ? 'text-yellow-600 font-medium' : 'text-gray-600 font-medium') }}">
                                    {{ $currentRoom ? ($periodEndDate?->format('d M Y') ?? 'N/A') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('owner.penyewa.show', $tenant->id) }}" class="text-gray-400 hover:text-emerald-600 p-1" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        @if($phone && $waPhone)
                                        <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($statusType === 'nunggak' ? 'Halo ' . $tenant->name . ', ini pengingat mengenai pembayaran sewa kamar kos Anda yang sudah melewati jatuh tempo. Mohon segera lakukan pembayaran. Terima kasih.' : 'Halo ' . $tenant->name . ', ada yang bisa kami bantu?') }}" target="_blank" class="{{ $isWarning ? 'text-red-500 hover:text-red-700 bg-red-50 rounded' : 'text-gray-400 hover:text-green-600' }} p-1" title="{{ $statusType === 'nunggak' ? 'Tagih via WhatsApp' : 'Chat WhatsApp' }}">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.481 0 1.461 1.063 2.875 1.211 3.074.149.198 2.095 3.2 5.076 4.487 2.982 1.288 2.982.859 3.526.809.544-.05 1.758-.718 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        </a>
                                        @else
                                        <span class="text-gray-300 p-1" title="No phone number">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.481 0 1.461 1.063 2.875 1.211 3.074.149.198 2.095 3.2 5.076 4.487 2.982 1.288 2.982.859 3.526.809.544-.05 1.758-.718 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada penghuni ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                @if ($dataPenyewa->count() > 0)
                    <div class="text-sm text-gray-500">
                        Menampilkan <span class="font-bold text-gray-700">{{ $dataPenyewa->firstItem() ?? 1 }}</span> - <span class="font-bold text-gray-700">{{ $dataPenyewa->lastItem() ?? 10 }}</span> dari <span class="font-bold text-emerald-600">{{ $dataPenyewa->total() }}</span> penghuni
                    </div>
                @else
                    <span class="text-sm text-gray-500">Tidak ada penghuni</span>
                @endif
                
                @if ($dataPenyewa->hasPages())
                    {{ $dataPenyewa->appends(request()->query())->links('components.pagination.admin') }}
                @endif
            </div>
        </div>
        @endif

        @if(($viewMode ?? 'list') === 'grid')
        <div id="view-grid" class="space-y-6 animate-fade-in">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($dataPenyewa as $tenant)
                @php
                    $currentRoom = $tenant->getFirstRoom();
                    $phone = $tenant->tenantProfile?->phone ?? null;
                    
                    // Format Phone for WA (Replace 0 with 62)
                    $waPhone = null;
                    if ($phone) {
                        $cleaned = preg_replace('/[^0-9]/', '', $phone);
                        if (str_starts_with($cleaned, '0')) {
                            $waPhone = '62' . substr($cleaned, 1);
                        } else {
                            $waPhone = $cleaned;
                        }
                    }
                    $latestTx = $tenant->tenantTransactions?->sortByDesc('period_end_date')->first();
                    
                    $statusType = 'active';
                    $statusLabel = '✅ LANCAR';
                    $periodEndDate = $latestTx?->period_end_date;
                    $accentColor = 'emerald';
                    
                    if ($latestTx) {
                        $rejectedTx = $tenant->tenantTransactions?->whereIn('status', ['rejected_by_admin', 'rejected_by_owner'])->first();
                        
                        if ($rejectedTx) {
                            $statusType = 'rejected';
                            $statusLabel = '❌ DITOLAK';
                            $accentColor = 'orange';
                        } elseif ($periodEndDate && $periodEndDate < now()->startOfDay()) {
                            $statusType = 'nunggak';
                            $statusLabel = '⚠️ TELAT BAYAR';
                            $accentColor = 'red';
                        } elseif ($periodEndDate && $periodEndDate <= now()->addDays($reminderDays ?? 7)->endOfDay()) {
                            $statusType = 'expiring';
                            $statusLabel = '⏰ SEGERA HABIS';
                            $accentColor = 'yellow';
                        }
                    }
                    
                    $isWarning = in_array($statusType, ['nunggak', 'rejected']);
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-{{ $accentColor }}-100 overflow-hidden flex flex-col hover:shadow-md transition-all duration-300 group relative">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-{{ $accentColor }}-500"></div>
                    <div class="p-5 flex items-start gap-4 border-b border-{{ $accentColor }}-50 bg-{{ $accentColor }}-50/30">
                        <img class="h-16 w-16 rounded-2xl object-cover border-2 border-white shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode($tenant->name) }}&background={{ $isWarning ? 'FEE2E2' : 'D1FAE5' }}&color={{ $isWarning ? 'B91C1C' : '059669' }}" alt="Foto">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $tenant->name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="bg-{{ $accentColor }}-100 text-gray-800 text-xs font-bold px-2 py-0.5 rounded border border-{{ $accentColor }}-200">KAMAR {{ $currentRoom?->room_number ?? '-' }}</span>
                                <span class="text-xs text-gray-500">{{ $currentRoom?->roomType?->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 space-y-3 flex-1 bg-{{ $accentColor }}-50/10">
                        <div class="space-y-1.5">
                            <p class="text-sm text-gray-700 font-medium">{{ $phone ?? '-' }}</p>
                            <p class="text-sm text-gray-600">{{ $tenant->tenantProfile?->university ?? 'Tenant' }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-{{ $accentColor }}-100 flex justify-between items-center">
                            <span class="bg-{{ $accentColor }}-100 text-{{ $accentColor }}-700 text-xs font-bold px-2 py-1 rounded-full {{ $statusType === 'nunggak' ? 'animate-pulse' : '' }}">{{ $statusLabel }}</span>
                            <span class="text-sm font-bold text-gray-800">{{ $periodEndDate?->format('d M Y') ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="p-4 bg-{{ $accentColor }}-50/50 border-t border-{{ $accentColor }}-100 grid grid-cols-2 gap-3">
                        <a href="{{ route('owner.penyewa.show', $tenant->id) }}" class="bg-white border border-{{ $accentColor }}-200 hover:text-{{ $accentColor }}-600 text-gray-700 text-xs font-bold py-2.5 rounded-xl transition shadow-sm text-center">Biodata</a>

                        @if($phone && $waPhone)
                        <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($statusType === 'nunggak' ? 'Halo ' . $tenant->name . ', ini pengingat mengenai pembayaran sewa kamar kos Anda. Mohon segera lakukan pembayaran.' : 'Halo ' . $tenant->name . ', ada yang bisa kami bantu?') }}" target="_blank" class="bg-{{ $accentColor }}-600 hover:bg-{{ $accentColor }}-700 text-white text-xs font-bold py-2.5 rounded-xl transition shadow-md text-center">{{ $statusType === 'nunggak' ? 'Tagih WA' : 'Chat WA' }}</a>
                        @else
                        <span class="bg-gray-200 text-gray-500 text-xs font-bold py-2.5 rounded-xl text-center cursor-not-allowed">No Phone</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <p class="text-gray-500 text-lg font-medium">Tidak ada penghuni ditemukan</p>
                </div>
            @endforelse
            </div>
        </div>
        @endif

    </div>

    <script>
        function clearSearch() {
            const searchInput = document.getElementById('searchInput');
            searchInput.value = '';
            document.getElementById('filterForm').submit();
        }

        function toggleClearBtn() {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearchBtn');
            if (searchInput.value.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }
    </script>
@endsection