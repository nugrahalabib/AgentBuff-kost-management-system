@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ $report->title }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">Dibuat pada {{ $report->created_at->format('d F Y H:i') }} • Oleh
                {{ $report->admin->name ?? 'Admin' }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('owner.laporan') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition font-semibold">
                ← Kembali
            </a>
            <div class="flex rounded-lg shadow-sm gap-2">
                <a href="{{ route('owner.laporan.download-pdf', $report) }}"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('owner.laporan.download-excel', $report) }}"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Excel
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        @if ($report->report_type === 'financial_report')

            <!-- Transaction Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Verified -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-emerald-100 rounded-lg text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Verified (Selesai)</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $reportData['verified_owner_count'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">Transaksi</p>
                        </div>
                    </div>
                </div>

                <!-- Rejected / Cancelled -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-red-100 rounded-lg text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Ditolak / Batal</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $reportData['rejected_count'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">Transaksi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-800">Detail Status Transaksi</h3>
                    <span
                        class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">{{ $reportData['transaction_count'] }}
                        Transaksi</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase">
                                <th class="p-4 font-semibold">Tanggal & Ref</th>
                                <th class="p-4 font-semibold">Penyewa</th>
                                <th class="p-4 font-semibold">Kamar</th>
                                <th class="p-4 font-semibold">Tipe</th>
                                <th class="p-4 font-semibold text-center">Periode Sewa</th>
                                <th class="p-4 font-semibold">Bank Pengirim</th>
                                <th class="p-4 font-semibold text-center">Metode</th>
                                <th class="p-4 font-semibold text-center">Status</th>
                                <th class="p-4 font-semibold text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse ($reportData['transactions'] as $transaction)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-medium text-gray-900">{{ $transaction->created_at->format('d M Y') }}</span>
                                            <span class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i') }}
                                                WIB</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ $transaction->reference_number ?? '-' }}</p>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                                {{ substr($transaction->tenant->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">
                                                    {{ $transaction->tenant->name ?? 'Deleted User' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-gray-600">{{ $transaction->room->room_number ?? '-' }}</td>
                                    <td class="p-4 text-gray-600">{{ $transaction->room->roomType->name ?? '-' }}</td>
                                    <td class="p-4 text-center text-xs text-gray-600">
                                        @php
                                            $tenant = $transaction->tenant;
                                            $trxPeriod = '-';
                                            if ($tenant) {
                                                $roomId = $transaction->kamar_id;
                                                $firstRoom = $tenant->occupiedRoom->where('id', $roomId)->first();
                                                $checkInDate = $firstRoom && $firstRoom->pivot ? $firstRoom->pivot->check_in_date : null;

                                                $startDate = $checkInDate ? \Carbon\Carbon::parse($checkInDate) : $transaction->period_start_date;
                                                $endDate = $transaction->period_end_date;
                                                $trxPeriod = ($startDate ? $startDate->format('d/m/y') : '-') . ' - ' . ($endDate ? $endDate->format('d/m/y') : '-');
                                            }
                                        @endphp
                                        {{ $trxPeriod }}
                                    </td>
                                    <td class="p-4">
                                        @if($transaction->sender_bank)
                                            <p class="font-medium text-gray-900 text-sm">{{ $transaction->sender_bank }}</p>
                                            <p class="text-xs text-gray-500">{{ $transaction->sender_name }}</p>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($transaction->payment_method == 'cash')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                                Tunai
                                            </span>
                                        @elseif($transaction->payment_method == 'edc')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                EDC
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                Transfer
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $statusClasses = match ($transaction->status) {
                                                'verified_by_owner' => 'bg-emerald-100 text-emerald-700',
                                                'verified_by_admin' => 'bg-blue-100 text-blue-700',
                                                'pending_verification' => 'bg-yellow-100 text-yellow-700',
                                                default => 'bg-red-100 text-red-700',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                            {{ ucfirst(str_replace('_', ' ', $transaction->status)) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-bold text-gray-900">
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-500">Tidak ada transaksi ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif ($report->report_type === 'room_status_report')

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Room Stats Cards -->
                <!-- Total Unit -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-indigo-100 rounded-lg text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Unit</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $reportData['total_rooms'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">Kamar</p>
                        </div>
                    </div>
                </div>

                <!-- Terisi -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-emerald-100 rounded-lg text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Terisi</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $reportData['occupied'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">Kamar</p>
                        </div>
                    </div>
                </div>

                <!-- Kosong -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 rounded-lg text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Kosong</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $reportData['available'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">Ready</p>
                        </div>
                    </div>
                </div>

                <!-- Occupancy Rate -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-100 rounded-lg text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Occupancy</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $reportData['occupancy_rate'] }}%</p>
                            <div class="w-20 bg-gray-100 h-1 mt-1 rounded-full overflow-hidden">
                                <div class="bg-purple-500 h-full" style="width: {{ $reportData['occupancy_rate'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room List Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800">Detail Status Kamar</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase">
                                <th class="p-4 font-semibold text-center">No. Kamar</th>
                                <th class="p-4 font-semibold text-center">Lantai</th>
                                <th class="p-4 font-semibold">Tipe</th>
                                <th class="p-4 font-semibold">Penghuni Saat Ini</th>
                                <th class="p-4 font-semibold text-center">Periode Sewa</th>
                                <th class="p-4 font-semibold text-center">Status Bayar</th>
                                <th class="p-4 font-semibold text-center">Status</th>
                                <th class="p-4 font-semibold text-center">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach ($reportData['kamar'] as $room)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 text-center font-bold text-gray-900">{{ $room->room_number }}</td>
                                    <td class="p-4 text-center text-gray-600">{{ $room->floor_number }}</td>
                                    <td class="p-4 text-gray-900">{{ $room->roomType->name ?? '-' }}</td>
                                    <td class="p-4">
                                        @if($room->occupants->count() > 0)
                                            <div class="space-y-1">
                                                @foreach($room->occupants as $occupant)
                                                    <div class="flex items-center gap-2">
                                                        <div
                                                            class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                                                            {{ substr($occupant->name, 0, 1) }}
                                                        </div>
                                                        <span class="text-gray-900 font-medium text-sm">{{ $occupant->name }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center text-gray-600 text-xs">
                                        @if($room->occupants->count() > 0)
                                            <div class="space-y-1">
                                                @foreach($room->occupants as $occupant)
                                                    @php
                                                        $period = '-';
                                                        $pivot = $occupant->pivot;
                                                        $checkInDate = $pivot ? $pivot->check_in_date : null;
                                                        $lastTrx = $occupant->tenantTransactions->where('status', 'verified_by_owner')->sortByDesc('period_end_date')->first();
                                                        $endDate = $lastTrx ? $lastTrx->period_end_date : null;

                                                        if ($checkInDate && $endDate) {
                                                            $period = \Carbon\Carbon::parse($checkInDate)->format('d/m/y') . '-' . $endDate->format('d/m/y');
                                                        }
                                                    @endphp
                                                    <div class="h-6 flex items-center justify-center">{{ $period }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($room->occupants->count() > 0)
                                            <div class="flex flex-col gap-1 items-center">
                                                @foreach($room->occupants as $occupant)
                                                    @php
                                                        $payLabel = $occupant->payment_status_label;
                                                        $payClass = match ($payLabel) {
                                                            'Lancar' => 'bg-emerald-100 text-emerald-700',
                                                            'Telat Bayar' => 'bg-red-100 text-red-700',
                                                            'Segera Habis' => 'bg-orange-100 text-orange-700',
                                                            default => 'bg-gray-100 text-gray-600'
                                                        };
                                                    @endphp
                                                    <div class="flex items-center h-6"> <!-- Fixed height to match tenant name row -->
                                                        <div
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $payClass }}">
                                                            {{ $payLabel }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-center">
                                        @php
                                            $stClass = match ($room->status) {
                                                'occupied' => 'bg-emerald-100 text-emerald-700',
                                                'available' => 'bg-blue-100 text-blue-700',
                                                'maintenance' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-700'
                                            };
                                            $stLabel = match ($room->status) {
                                                'occupied' => 'Terisi',
                                                'available' => 'Kosong',
                                                'maintenance' => 'Perbaikan',
                                                default => $room->status
                                            };
                                        @endphp
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stClass }}">{{ $stLabel }}</span>
                                    </td>
                                    <td class="p-4 text-center font-medium text-gray-900">
                                        Rp
                                        {{ number_format($room->roomType->capacity > 1 ? ($room->rent_per_person ?? ($room->price_per_month / 2)) : ($room->price_per_month ?? 0), 0, ',', '.') }}
                                        @if($room->roomType->capacity > 1)
                                            <span class="text-[10px] text-gray-500 block">/ org / bln</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif ($report->report_type === 'tenant_report')

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Tenant Stats -->
                <!-- Total Penyewa -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 rounded-lg text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Penyewa</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $reportData['tenant_count'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">Terdaftar</p>
                        </div>
                    </div>
                </div>

                <!-- Penyewa Aktif -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-emerald-100 rounded-lg text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Penyewa Aktif</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $reportData['active_tenants'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">Ada Kamar</p>
                        </div>
                    </div>
                </div>

                <!-- Habis 30 Hari -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-orange-100 rounded-lg text-orange-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Habis < 30 Hari</p>
                                    <p class="text-3xl font-bold text-gray-900">{{ $reportData['expiring_soon'] }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Perlu Follow-up</p>
                        </div>
                    </div>
                </div>

                <!-- Non-Aktif -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-gray-100 rounded-lg text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Non-Aktif</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $reportData['inactive_tenants'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">Akun</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tenant List Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800">Data Penyewa</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase">
                                <th class="p-4 font-semibold">Nama Penyewa</th>
                                <th class="p-4 font-semibold">Kontak</th>
                                <th class="p-4 font-semibold">Kamar</th>
                                <th class="p-4 font-semibold text-center">Periode Sewa</th>
                                <th class="p-4 font-semibold text-center">Status Bayar</th>
                                <th class="p-4 font-semibold text-center">Status Akun</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach ($reportData['tenants'] as $tenant)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 font-medium text-gray-900">{{ $tenant->name }}</td>
                                    <td class="p-4 text-gray-600">{{ $tenant->tenantProfile->phone ?? '-' }}</td>
                                    <td class="p-4 font-bold text-gray-800">
                                        {{ $tenant->sort_room_number ?: '-' }}
                                    </td>
                                    @php
                                        $activeTrx = $tenant->tenantTransactions->where('status', 'verified_by_owner')->sortByDesc('period_end_date')->first();
                                    @endphp
                                    <td class="p-4 text-center text-gray-600">
                                        @if($activeTrx)
                                            @php
                                                $checkInDate = $tenant->activeRoom && $tenant->activeRoom->pivot ? $tenant->activeRoom->pivot->check_in_date : null;
                                                $startDate = $checkInDate ? \Carbon\Carbon::parse($checkInDate) : $activeTrx->period_start_date;
                                             @endphp
                                            {{ $startDate ? $startDate->format('d/m/y') : '-' }} -
                                            {{ $activeTrx->period_end_date ? $activeTrx->period_end_date->format('d/m/y') : '-' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $payLabel = $tenant->payment_status_label;
                                            $payClass = match ($payLabel) {
                                                'Lancar' => 'bg-emerald-100 text-emerald-700',
                                                'Telat Bayar' => 'bg-red-100 text-red-700',
                                                'Segera Habis' => 'bg-orange-100 text-orange-700',
                                                default => 'bg-gray-100 text-gray-600'
                                            };
                                        @endphp
                                        @if($payLabel == '-')
                                            <span class="text-gray-400">-</span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-bold {{ $payClass }}">{{ $payLabel }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($tenant->account_status_label === 'Aktif')
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Aktif</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Tidak
                                                Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif ($report->report_type === 'comprehensive_report')

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Stats Cards -->
                <!-- Active Tenants -->
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Penyewa Aktif</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['total_active'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Kamar Terisi -->
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Kamar Terisi</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['room_stats']['occupied'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Kamar Tersedia -->
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Kamar Tersedia</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['room_stats']['available'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Lancar -->
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-teal-100 rounded-lg text-teal-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Bayar Lancar</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['status_counts']['paid'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Nunggak -->
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-100 rounded-lg text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Telat Bayar</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $reportData['status_counts']['late'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comprehensive Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800">Detail Integrasi Data</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <th class="p-4">Penyewa</th>
                                <th class="p-4">Kamar & Tipe</th>
                                <th class="p-4 text-center">Harga Sewa</th>
                                <th class="p-4 text-center">Status Bayar</th>
                                <th class="p-4 text-center">Periode Sewa</th>
                                <th class="p-4 text-right">Pembayaran Terakhir</th>
                                <th class="p-4 text-center">Bank Pengirim</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($reportData['tenants'] as $tenant)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4">
                                        <div class="font-medium text-gray-900">{{ $tenant->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $tenant->tenantProfile->phone ?? '-' }}</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Status: <span
                                                class="{{ $tenant->activeRoom ? 'text-green-600' : 'text-red-500' }}">{{ $tenant->activeRoom ? 'Aktif' : 'Tidak Aktif' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        @if($tenant->activeRoom)
                                            <div class="font-medium text-gray-900">{{ $tenant->activeRoom->room_number ?? '-' }}</div>
                                            <div class="text-sm text-gray-500">{{ $tenant->activeRoom->roomType->name ?? '-' }}</div>
                                        @else
                                            <span class="text-gray-400 text-sm italic">Tidak Ada Kamar</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $room = $tenant->activeRoom;
                                            $price = $room ? ($room->roomType->capacity > 1
                                                ? ($room->rent_per_person ?? ($room->price_per_month / 2))
                                                : ($room->price_per_month ?? 0)) : 0;
                                        @endphp
                                        @if($price > 0)
                                            <div class="font-medium">Rp {{ number_format($price, 0, ',', '.') }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $payLabel = $tenant->payment_status_label;
                                            $payClass = match ($payLabel) {
                                                'Lancar' => 'bg-green-100 text-green-700 border-green-200',
                                                'Telat Bayar' => 'bg-red-100 text-red-700 border-red-200',
                                                'Segera Habis' => 'bg-orange-100 text-orange-700 border-orange-200',
                                                default => 'bg-gray-100 text-gray-700 border-gray-200'
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $payClass }}">
                                            {{ $payLabel }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center text-sm text-gray-600">
                                        @php
                                            $lastTrx = $tenant->tenantTransactions->where('status', 'verified_by_owner')->sortByDesc('period_end_date')->first();
                                        @endphp
                                        @if($lastTrx)
                                            @php
                                                $checkInDate = $tenant->activeRoom && $tenant->activeRoom->pivot ? $tenant->activeRoom->pivot->check_in_date : null;
                                                $startDate = $checkInDate ? \Carbon\Carbon::parse($checkInDate) : $lastTrx->period_start_date;
                                            @endphp
                                            {{ $startDate ? $startDate->format('d/m/y') : '-' }} -
                                            {{ optional($lastTrx->period_end_date)->format('d/m/y') ?? '-' }}
                                        @else
                                            <span class="text-gray-400 italic">Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        @if($lastTrx)
                                            <div class="font-medium text-gray-900">Rp {{ number_format($lastTrx->amount, 0, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $lastTrx->created_at->format('d/m/Y H:i') }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center text-sm text-gray-600">
                                        @if($lastTrx)
                                            @if($lastTrx->sender_bank)
                                                <div>{{ $lastTrx->sender_bank }}</div>
                                                @if($lastTrx->sender_name)
                                                    <div class="text-xs text-gray-400">a.n {{ $lastTrx->sender_name }}</div>
                                                @endif
                                            @else
                                                {{ $lastTrx->payment_method ?? '-' }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
@endsection