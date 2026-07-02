@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Selamat Datang, Admin! 👋
            </h2>
            <p class="text-sm text-gray-500">Ringkasan operasional kos hari ini, <strong>{{ date('d F Y') }}</strong>.</p>
        </div>
    </div>
@endsection

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">

        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-blue-500"></div>
            <div>
                <p class="text-xs font-bold text-blue-500 uppercase tracking-wider">Total Penghuni</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $totalPenyewa }} <span
                        class="text-sm font-medium text-gray-400">Orang</span></h3>
                <p class="text-[10px] text-gray-400 mt-1">Aktif saat ini</p>
            </div>
            <div
                class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
        </div>

        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-orange-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-orange-500"></div>
            <div>
                <p class="text-xs font-bold text-orange-500 uppercase tracking-wider">Tugas Verifikasi</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $pendingVerifications }} <span
                        class="text-sm font-medium text-gray-400">Item</span></h3>
                <p class="text-[10px] text-gray-400 mt-1">Bukti transfer baru</p>
            </div>
            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center animate-pulse">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-indigo-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-indigo-500"></div>
            <div>
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Kamar Terisi</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $kamarTerisi }} <span
                        class="text-sm font-medium text-gray-400">Unit</span></h3>
                <p class="text-[10px] text-gray-400 mt-1">Tidak tersedia</p>
            </div>
            <div
                class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
            </div>
        </div>

        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-emerald-500"></div>
            <div>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Kamar Tersedia</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $kamarTersedia }} <span
                        class="text-sm font-medium text-gray-400">Unit</span></h3>
                <p class="text-[10px] text-gray-400 mt-1">Siap huni</p>
            </div>
            <div
                class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
            </div>
        </div>

        <div
            class="bg-white p-5 rounded-2xl shadow-sm border border-red-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-red-500"></div>
            <div>
                <p class="text-xs font-bold text-red-500 uppercase tracking-wider">Jatuh Tempo (H-3)</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $dueSoon }} <span
                        class="text-sm font-medium text-gray-400">Orang</span></h3>
                <p class="text-[10px] text-gray-400 mt-1">Segera ingatkan</p>
            </div>
            <div
                class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-8">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        Notifikasi Terbaru
                    </h3>
                    <a href="{{ route('admin.notifikasi') }}"
                        class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline">Lihat Semua</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($latestNotifications as $notification)
                        <div class="p-4 hover:bg-gray-50 transition flex gap-4 items-start relative group">
                            <div
                                class="absolute left-0 top-0 bottom-0 w-1 {{ $notification->category == 'urgent' ? 'bg-red-500' : ($notification->category == 'finance' ? 'bg-emerald-500' : 'bg-blue-500') }} rounded-l">
                            </div>
                            <div
                                class="w-10 h-10 rounded-full {{ $notification->category == 'urgent' ? 'bg-red-50 text-red-500' : ($notification->category == 'finance' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600') }} flex items-center justify-center flex-shrink-0 mt-1">
                                @if ($notification->category == 'urgent')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                        </path>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <h4 class="text-sm font-bold text-gray-800">{{ $notification->type }}</h4>
                                    <span
                                        class="text-[10px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $notification->message }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500 text-sm">
                            Tidak ada notifikasi
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Tugas Verifikasi
                    </h3>
                    <a href="{{ route('admin.transaksi') }}"
                        class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @forelse ($paymentVerifications as $transaction)
                                <tr class="hover:bg-orange-50/30 transition group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs">
                                                {{ strtoupper(substr($transaction->tenant->name ?? 'N/A', 0, 1)) . strtoupper(substr(explode(' ', $transaction->tenant->name ?? 'N/A')[1] ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800">{{ $transaction->tenant->name ?? 'N/A' }}</p>
                                                <p class="text-[10px] text-gray-500">Kamar
                                                    {{ $transaction->room?->room_number ?? '-' }} •
                                                    {{ $transaction->room?->roomType?->name ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-emerald-600">Rp
                                        {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-400 text-xs">{{ $transaction->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.transaksi') }}?highlight={{ $transaction->id }}"
                                            class="bg-orange-100 text-orange-700 hover:bg-orange-200 px-3 py-1 rounded-lg text-xs font-bold transition">
                                            Cek Bukti
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 text-sm">
                                        Tidak ada pembayaran yang menunggu verifikasi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="space-y-8">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800">Jatuh Tempo</h3>
                    <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded-full">Minggu Ini</span>
                </div>

                <div class="space-y-4">
                    @forelse ($detailPenyewaJatuhTempo as $detail)
                        @php
                            $bgClass = match ($detail['status_color']) {
                                'red' => 'bg-red-50 border-red-100',
                                'yellow' => 'bg-yellow-50 border-yellow-100',
                                'gray' => 'bg-gray-50 border-gray-100',
                                default => 'bg-gray-50 border-gray-100'
                            };

                            $iconBgClass = match ($detail['status_color']) {
                                'red' => 'bg-white text-red-500 border-red-100',
                                'yellow' => 'bg-white text-yellow-500 border-yellow-100',
                                'gray' => 'bg-white text-gray-500 border-gray-100',
                                default => 'bg-white text-gray-500 border-gray-100'
                            };

                            $labelColorClass = match ($detail['status_color']) {
                                'red' => 'text-red-500 font-bold',
                                'yellow' => 'text-yellow-600 font-bold',
                                'gray' => 'text-gray-500',
                                default => 'text-gray-500'
                            };

                            $buttonColorClass = match ($detail['status_color']) {
                                'red' => 'text-red-500 hover:text-red-700',
                                'yellow' => 'text-emerald-600 hover:text-emerald-700',
                                'gray' => 'text-gray-400 hover:text-gray-600',
                                default => 'text-gray-400 hover:text-gray-600'
                            };
                        @endphp

                        <div class="flex items-center gap-3 p-3 {{ $bgClass }} rounded-xl border">
                            <div
                                class="w-10 h-10 rounded-full {{ $iconBgClass }} flex items-center justify-center font-bold border flex-shrink-0">
                                @if ($detail['status_color'] === 'red')
                                    !
                                @else
                                    {{ $detail['status_icon_text'] }}
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800">{{ $detail['tenant_name'] }}</p>
                                <p class="text-[10px] {{ $labelColorClass }}">{{ $detail['status_label'] }}
                                    ({{ $detail['due_date']->format('d M Y') }})</p>
                            </div>
                            <a href="{{ route('admin.penyewa') }}"
                                class="{{ $buttonColorClass }} p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition"
                                title="Lihat Data Penyewa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268 2.943-9.542 7z" />
                                </svg>
                            </a>
                        </div>
                    @empty
                        <div class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div
                                class="w-10 h-10 rounded-full bg-white text-emerald-500 flex items-center justify-center font-bold border border-emerald-100 flex-shrink-0">
                                ✓
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800">Semua Lancar</p>
                                <p class="text-[10px] text-emerald-600 font-bold">Tidak ada pembayaran jatuh tempo minggu ini
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Okupansi Hunian section disembunyikan --}}
            {{--
            <div class="bg-emerald-900 text-white p-6 rounded-2xl shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-500 rounded-full opacity-20 blur-2xl">
                </div>

                <h3 class="font-bold text-lg mb-1">Okupansi Hunian</h3>
                <p class="text-xs text-emerald-200 mb-6">Total {{ $kamarTerisi }} dari {{ $totalKamar }} Kamar terisi</p>

                <div class="flex items-center justify-between text-xs font-bold mb-2">
                    <span>{{ $occupancyRate }}% Terisi</span>
                    <span>Target: 95%</span>
                </div>
                <div class="w-full bg-emerald-800 rounded-full h-2.5 mb-6">
                    <div class="bg-emerald-400 h-2.5 rounded-full shadow-[0_0_10px_rgba(52,211,153,0.5)]"
                        style="width: {{ $occupancyRate }}%"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 p-3 rounded-xl backdrop-blur-sm">
                        <p class="text-[10px] text-emerald-200 uppercase tracking-wide">Penyewa Baru</p>
                        <p class="text-xl font-bold mt-1">+{{ $penyewaBaru }}</p>
                    </div>
                    <div class="bg-white/10 p-3 rounded-xl backdrop-blur-sm">
                        <p class="text-[10px] text-emerald-200 uppercase tracking-wide">Keluar</p>
                        <p class="text-xl font-bold mt-1 text-red-300">{{ $penyewaCheckout }}</p>
                    </div>
                </div>
            </div>
            --}}

        </div>

    </div>

@endsection