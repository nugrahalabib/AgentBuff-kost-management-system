@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Laporan Arus Kas') }}
            </h2>
            <p class="text-sm text-gray-500">Laporan Laba Rugi (P&L) & Neraca Keuangan Periode {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->translatedFormat('F Y') }}.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button type="button" onclick="openModal('laporanModal')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-2.5 px-5 rounded-lg shadow-md transition flex items-center whitespace-nowrap">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Laporan
            </button>
            <div class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg flex items-center shadow-sm overflow-hidden">
                <a href="{{ route('owner.laporan', ['month' => \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->subMonth()->month, 'year' => \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->subMonth()->year]) }}" class="px-3 py-2 hover:bg-gray-50 border-r border-gray-300 transition block">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <span class="px-4 py-2 font-bold text-emerald-800 min-w-[150px] text-center block">
                    {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->translatedFormat('F Y') }}
                </span>
                <a href="{{ route('owner.laporan', ['month' => \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->addMonth()->month, 'year' => \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->addMonth()->year]) }}" class="px-3 py-2 hover:bg-gray-50 border-l border-gray-300 transition block">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>


            
            <div class="flex rounded-lg shadow-sm ml-2" role="group">
                <a href="{{ route('owner.laporan.export-pdf', ['month' => $currentMonth, 'year' => $currentYear]) }}" class="px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50">
                    PDF
                </a>
                <a href="{{ route('owner.laporan.export-excel', ['month' => $currentMonth, 'year' => $currentYear]) }}" class="px-3 py-2 text-xs font-medium text-gray-700 bg-white border-t border-b border-r border-gray-300 rounded-r-lg hover:bg-gray-50">
                    Excel
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @include('partials.modal-laporan')
    <div class="space-y-8">
            
        <!-- Financial Summary Cards (Replaced per User Request) -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
            {{-- 1. Pemasukan Sementara (High Priority Action) --}}
            <div class="bg-amber-50 rounded-2xl p-5 shadow-md border-2 border-amber-400 relative overflow-hidden group transform hover:-translate-y-1 transition duration-300">
                <div class="absolute right-0 top-0 h-full w-1 bg-amber-500"></div>
                
                {{-- Action Badge --}}
                <div class="absolute top-2 right-2">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                </div>

                <div class="flex items-center justify-between mb-4 mt-2">
                    <p class="text-xs font-extrabold text-amber-700 uppercase tracking-wider">Pemasukan Sementara</p>
                    <div class="bg-white p-1.5 rounded-lg text-amber-600 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h3 class="text-xl font-black text-gray-800">Rp {{ number_format($dashboardMetrics['pendingRevenue'] ?? 0, 0, ',', '.') }}</h3>
                <div class="mt-3">
                    <a href="{{ route('owner.verifikasi-transaksi') }}" class="text-xs bg-amber-200 hover:bg-amber-300 text-amber-800 font-bold px-2 py-1 rounded inline-flex items-center transition">
                        Verifikasi Sekarang
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
                <p class="text-[10px] text-amber-600/80 mt-2 font-medium">
                    *Dana menunggu konfirmasi Anda
                </p>
            </div>

            {{-- 2. Total Pemasukan --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-1 bg-emerald-500"></div>
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pemasukan</p>
                    <div class="bg-emerald-50 p-1.5 rounded-lg text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h3 class="text-xl font-extrabold text-gray-800">Rp {{ number_format($dashboardMetrics['totalRevenue'] ?? 0, 0, ',', '.') }}</h3>
                <p class="text-xs text-emerald-600 mt-2 flex items-center font-medium">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    Bulan Ini
                </p>
            </div>

            {{-- 3. Total Pengeluaran --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-1 bg-red-500"></div>
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pengeluaran</p>
                    <div class="bg-red-50 p-1.5 rounded-lg text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    </div>
                </div>
                <h3 class="text-xl font-extrabold text-gray-800">Rp {{ number_format($dashboardMetrics['totalExpenses'] ?? 0, 0, ',', '.') }}</h3>
                <p class="text-xs text-red-500 mt-2 flex items-center font-medium">
                    Maintenance & Ops
                </p>
            </div>

            {{-- 4. Laba Bersih --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 h-full w-1 bg-blue-500"></div>
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Laba Bersih</p>
                    <div class="bg-blue-50 p-1.5 rounded-lg text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
                <h3 class="text-xl font-extrabold text-gray-800">Rp {{ number_format($dashboardMetrics['netProfit'] ?? 0, 0, ',', '.') }}</h3>
                <p class="text-xs text-blue-600 mt-2 flex items-center font-medium">
                    Net Operating Income
                </p>
            </div>

            {{-- 5. Proyeksi Bulan Depan --}}
            <div class="bg-emerald-900 rounded-2xl p-5 shadow-lg border border-emerald-800 text-white relative overflow-hidden">
                <div class="absolute -right-6 -top-6 bg-white opacity-10 rounded-full w-32 h-32"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-xs font-bold text-emerald-200 uppercase tracking-wider">Proyeksi Bulan Depan</p>
                        <div class="bg-emerald-800 p-1.5 rounded-lg text-emerald-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-extrabold mt-1">Rp {{ number_format($dashboardMetrics['projectedNext'] ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-emerald-300 mt-2 opacity-80">
                        Potensi Pendapatan Maksimal
                    </p>
                </div>
            </div>

        </div>

        {{-- Detailed Expense Analysis (OPEX & CAPEX) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 border-b border-gray-100 pb-6">
                <div>
                    <h3 class="text-lg font-extrabold text-gray-800 uppercase tracking-tight">Analisa Pengeluaran (Expense Analysis)</h3>
                    <p class="text-sm text-gray-500 mt-1">Breakdown detail beban operasional dan belanja modal periode ini.</p>
                </div>
                <button type="button" onclick="document.getElementById('expenseModal').classList.remove('hidden')" class="px-5 py-2.5 text-sm font-bold text-white bg-amber-500 rounded-xl hover:bg-amber-600 flex items-center gap-2 transition shadow-md shadow-amber-200 hover:shadow-lg hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Input Pengeluaran Baru
                </button>
            </div>

                @php
                    $bd = $financialSummary['breakdown'] ?? [];
                    $totalOp = $financialSummary['opex_total'] > 0 ? $financialSummary['opex_total'] : 1;
                    $totalCap = ($bd['capex_renovation'] + $bd['capex_furniture'] + ($bd['capex_electronic'] ?? 0) + ($bd['capex_other'] ?? 0)) > 0 ? ($bd['capex_renovation'] + $bd['capex_furniture'] + ($bd['capex_electronic'] ?? 0) + ($bd['capex_other'] ?? 0)) : 1;
                    
                    // OPEX Values
                    $utilVal = $bd['utilities'] ?? 0;
                    $payVal = $bd['salary'] ?? 0;
                    $mainVal = $bd['maintenance'] ?? 0;
                    $cleanVal = $bd['cleaning'] ?? 0;
                    $mktVal = $bd['marketing'] ?? 0;
                    $adminVal = $bd['admin'] ?? 0;
                    $otherVal = $bd['opex_other'] ?? 0;
    
                    // CAPEX Values
                    $renoVal = $bd['capex_renovation'] ?? 0;
                    $furnVal = $bd['capex_furniture'] ?? 0;
                    $elecVal = $bd['capex_electronic'] ?? 0;
                    $otherCapVal = $bd['capex_other'] ?? 0;
                @endphp
    
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    {{-- OPEX SECTION --}}
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Analisa Beban Operasional (OPEX)
                        </h4>
                        
                        <div class="space-y-6">
                            {{-- Utilities --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        <span class="text-sm font-semibold text-gray-700">Utilities (Listrik, Air, Wifi)</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($utilVal / $totalOp) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: {{ ($utilVal / $totalOp) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($utilVal, 0, ',', '.') }}</p>
                            </div>
    
                            {{-- Payroll --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        <span class="text-sm font-semibold text-gray-700">Payroll (Gaji Staff)</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($payVal / $totalOp) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($payVal / $totalOp) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($payVal, 0, ',', '.') }}</p>
                            </div>
    
                            {{-- Maintenance --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                                        <span class="text-sm font-semibold text-gray-700">Maintenance</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($mainVal / $totalOp) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-orange-400 h-2 rounded-full" style="width: {{ ($mainVal / $totalOp) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($mainVal, 0, ',', '.') }}</p>
                            </div>

                            {{-- Cleaning --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                                        <span class="text-sm font-semibold text-gray-700">Kebersihan & Lingkungan</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($cleanVal / $totalOp) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-teal-400 h-2 rounded-full" style="width: {{ ($cleanVal / $totalOp) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($cleanVal, 0, ',', '.') }}</p>
                            </div>

                            {{-- Marketing --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-pink-500"></span>
                                        <span class="text-sm font-semibold text-gray-700">Marketing & Iklan</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($mktVal / $totalOp) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-pink-500 h-2 rounded-full" style="width: {{ ($mktVal / $totalOp) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($mktVal, 0, ',', '.') }}</p>
                            </div>

                            {{-- Admin --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                        <span class="text-sm font-semibold text-gray-700">Admin & Pajak</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($adminVal / $totalOp) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-indigo-400 h-2 rounded-full" style="width: {{ ($adminVal / $totalOp) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($adminVal, 0, ',', '.') }}</p>
                            </div>
    
                            {{-- Others --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                        <span class="text-sm font-semibold text-gray-700">Lain-lain (ATK, dll)</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($otherVal / $totalOp) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-gray-400 h-2 rounded-full" style="width: {{ ($otherVal / $totalOp) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($otherVal, 0, ',', '.') }}</p>
                            </div>
    
                            {{-- Summary Box for OPEX --}}
                            <div class="mt-8 p-4 bg-red-50 rounded-xl border border-red-100">
                                 <p class="text-xs text-red-600 font-bold uppercase mb-1">Total OPEX Bulan Ini</p>
                                 <h3 class="text-2xl font-black text-red-800">Rp {{ number_format($financialSummary['opex_total'] ?? 0, 0, ',', '.') }}</h3>
                                 <p class="text-[10px] text-red-500/80 mt-1">Total beban operasional rutin.</p>
                            </div>
                        </div>
                    </div>
    
                    {{-- CAPEX SECTION --}}
                    <div class="border-t lg:border-t-0 lg:border-l border-gray-100 pt-8 lg:pt-0 lg:pl-12">
                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            Belanja Modal (CAPEX)
                        </h4>
                        
                        <div class="space-y-6">
                            {{-- Renovation --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                        <span class="text-sm font-semibold text-gray-700">Renovasi Bangunan</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($renoVal / $totalCap) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: {{ ($renoVal / $totalCap) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($renoVal, 0, ',', '.') }}</p>
                            </div>
    
                            {{-- Furniture --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                        <span class="text-sm font-semibold text-gray-700">Furniture Perabot</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($furnVal / $totalCap) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ ($furnVal / $totalCap) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($furnVal, 0, ',', '.') }}</p>
                            </div>

                            {{-- Electronics --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        <span class="text-sm font-semibold text-gray-700">Elektronik (AC/TV)</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($elecVal / $totalCap) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($elecVal / $totalCap) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($elecVal, 0, ',', '.') }}</p>
                            </div>

                            {{-- Other Assets --}}
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                                        <span class="text-sm font-semibold text-gray-700">Aset Lainnya</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800">{{ round(($otherCapVal / $totalCap) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                    <div class="bg-gray-500 h-2 rounded-full" style="width: {{ ($otherCapVal / $totalCap) * 100 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 font-mono">Rp {{ number_format($otherCapVal, 0, ',', '.') }}</p>
                            </div>
                            
                            {{-- Summary Box for CAPEX --}}
                            <div class="mt-8 p-4 bg-purple-50 rounded-xl border border-purple-100">
                                 <p class="text-xs text-purple-600 font-bold uppercase mb-1">Total CAPEX Bulan Ini</p>
                                 <h3 class="text-2xl font-black text-purple-800">Rp {{ number_format($totalCap > 1 ? $totalCap : 0, 0, ',', '.') }}</h3>
                                 <p class="text-[10px] text-purple-500/80 mt-1">Investasi aset jangka panjang.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Buku Besar Transaksi</h3>
                    <p class="text-sm text-gray-500">Rincian detail pergerakan arus kas.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <select id="ledgerTypeFilter" class="border border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 py-2 pl-3 pr-8">
                        <option value="all">Semua Tipe</option>
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran (Semua)</option>
                        <option value="opex">-- OPEX</option>
                        <option value="capex">-- CAPEX</option>
                    </select>

                    <div class="relative">
                        <input type="text" id="ledgerSearchInput" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500 w-64" placeholder="Cari No. Ref atau Keterangan...">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-100 text-gray-500 font-bold uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">No. Ref</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4 w-1/3">Keterangan Detail</th>
                            <th class="px-6 py-4 text-right text-emerald-600">Debit (Masuk)</th>
                            <th class="px-6 py-4 text-right text-red-600">Kredit (Keluar)</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="ledgerTableBody" class="divide-y divide-gray-100">
                        @forelse ($transaksi as $trx)
                        <tr class="hover:bg-gray-50 transition ledger-row" data-ledger-type="{{ $trx->ledger_type }}" data-expense-type="{{ $trx->type ?? '' }}" data-ledger-ref="{{ strtolower($trx->reference_number ?? 'manual') }}" data-ledger-desc="{{ strtolower($trx->ledger_desc) }}">
                            <td class="px-6 py-4 font-medium text-gray-700">{{ $trx->ledger_date ? \Carbon\Carbon::parse($trx->ledger_date)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $trx->reference_number ?? 'MANUAL' }}</td>
                            <td class="px-6 py-4">
                                @if($trx->ledger_type == 'income')
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-xs font-bold border border-emerald-200">Revenue</span>
                                @elseif($trx->ledger_type == 'expense' && isset($trx->type) && $trx->type == 'opex')
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold border border-red-200">OPEX</span>
                                @elseif($trx->ledger_type == 'expense' && isset($trx->type) && $trx->type == 'capex')
                                    <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-bold border border-orange-200">CAPEX</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-bold border border-gray-200">Expense</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs text-wrap max-w-xs">
                                {{ $trx->ledger_desc }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-700">
                                @if($trx->ledger_type == 'income')
                                    + {{ number_format($trx->ledger_amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-red-700">
                                @if($trx->ledger_type == 'expense')
                                    - {{ number_format($trx->ledger_amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($trx->ledger_type == 'income')
                                    <button onclick="openTrxModal(this)" 
                                        class="text-blue-500 hover:text-blue-700 transition p-1 rounded hover:bg-blue-50" 
                                        title="Lihat Detail Transaksi"
                                        data-ref="{{ $trx->reference_number }}"
                                        data-date="{{ $trx->payment_date ? $trx->payment_date->format('d M Y') : '-' }}"
                                        data-amount="{{ number_format($trx->final_amount, 0, ',', '.') }}"
                                        data-tenant="{{ $trx->tenant->name ?? 'Mantan Penyewa' }}"
                                        data-room="{{ $trx->room->room_number ?? '-' }}"
                                        data-admin-date="{{ $trx->admin_verified_at ? $trx->admin_verified_at->format('d M Y H:i') : '-' }}"
                                        data-owner-date="{{ $trx->owner_verified_at ? $trx->owner_verified_at->format('d M Y H:i') : '-' }}"
                                        data-proof="{{ $trx->paymentProofs->first()?->proof_url ?? '' }}"
                                        data-notes="{{ $trx->owner_notes ?? '-' }}"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                @elseif($trx->ledger_type == 'expense' && isset($trx->id))
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick="openTrxModal(this)" 
                                            class="text-blue-500 hover:text-blue-700 transition p-1 rounded hover:bg-blue-50" 
                                            title="Lihat Detail Transaksi"
                                            data-type="expense"
                                            data-ref="MANUAL"
                                            data-date="{{ $trx->date ? $trx->date->format('d M Y') : '-' }}"
                                            data-amount="{{ number_format($trx->amount, 0, ',', '.') }}"
                                            data-category="{{ $trx->category }}"
                                            data-desc="{{ $trx->description ?? '-' }}"
                                            data-proof="{{ $trx->proof_image ? Storage::url($trx->proof_image) : '' }}"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        <form action="{{ route('owner.laporan.expense.destroy', $trx->id) }}" method="POST" onsubmit="confirmSubmit(event, 'Apakah Anda yakin ingin menghapus pengeluaran ini?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-700 transition p-1 rounded hover:bg-red-50" title="Hapus Pengeluaran">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr id="ledgerEmptyRow">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Belum ada transaksi bulan ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500" id="ledgerCountText">
                    @if ($transaksi->count() > 0)
                        Menampilkan <span class="font-bold text-emerald-600">{{ $transaksi->count() }}</span> transaksi
                    @else
                        Tidak ada transaksi
                    @endif
                </div>
                <div class="flex gap-1" id="ledgerPagination"></div>
            </div>
        </div>


    <!-- Bagian Baru: Arsip Laporan Resmi -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-8">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50">
            <div>
                <h3 class="font-bold text-gray-800">Arsip Laporan Resmi dari Admin</h3>
                <p class="text-xs text-gray-500">Dokumen laporan yang telah dikirim dan divalidasi oleh Admin.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-2">
                <select id="reportTypeFilter" class="border border-gray-300 rounded-lg text-xs focus:ring-emerald-500 focus:border-emerald-500 py-2 pl-3 pr-8">
                    <option value="all">Semua Jenis</option>
                    <option value="financial_report">Keuangan</option>
                    <option value="tenant_report">Penyewa</option>
                    <option value="room_status_report">Kamar</option>
                    <option value="comprehensive_report">Gabungan (Integrasi)</option>
                </select>

                <div class="relative">
                    <input type="text" id="reportSearchInput" class="pl-8 pr-4 py-2 border border-gray-300 rounded-lg text-xs focus:ring-emerald-500 focus:border-emerald-500 w-48" placeholder="Cari Judul Laporan...">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Laporan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Diterima</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>

                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="reportTableBody" class="divide-y divide-gray-100 text-sm">
                    
                    @forelse ($reports as $report)
                    @php
                        $reportTypeLabel = match($report->report_type) {
                            'financial_report' => 'Keuangan',
                            'room_status_report' => 'Kamar',
                            'tenant_report' => 'Penyewa',
                            default => $report->report_type,
                        };
                        $reportTypeColor = match($report->report_type) {
                            'financial_report' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-500', 'icon' => '📊'],
                            'room_status_report' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-500', 'icon' => '🏠'],
                            'tenant_report' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-500', 'icon' => '👥'],
                            default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-500', 'icon' => '📄'],
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition group report-row" data-report-type="{{ $report->report_type }}" data-report-title="{{ strtolower($report->title) }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg {{ $reportTypeColor['bg'] }} {{ $reportTypeColor['text'] }} flex items-center justify-center border">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $report->title }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-600">{{ $reportTypeLabel }}</td>
                        <td class="px-6 py-4 font-bold text-gray-600">{{ \Carbon\Carbon::createFromDate($report->report_year, $report->report_month)->format('F Y') }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $report->sent_at?->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if ($report->viewed_at || $report->downloaded_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Sudah Dibaca
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                    Laporan Baru Masuk
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('owner.laporan.preview', $report) }}" title="Preview" class="p-2 text-gray-400 hover:text-blue-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('owner.laporan.download-pdf', $report) }}" title="Download PDF" class="p-2 text-gray-400 hover:text-red-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </a>
                                <a href="{{ route('owner.laporan.download-excel', $report) }}" title="Download Excel" class="p-2 text-gray-400 hover:text-green-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="reportEmptyRow">
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-gray-500 font-semibold mb-2">Belum ada laporan</p>
                                <p class="text-gray-400 text-sm">Admin belum mengirimkan laporan apapun.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-500" id="reportCountText">
                @if ($reports->count() > 0)
                    Menampilkan <span class="font-bold text-emerald-600">{{ $reports->count() }}</span> laporan
                @else
                    Tidak ada laporan
                @endif
            </div>
            <div class="flex gap-1" id="reportPagination"></div>
        </div>
    </div>
</div>

    {{-- Expense Input Modal --}}
    <div id="expenseModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/50 transition-opacity" aria-hidden="true" onclick="document.getElementById('expenseModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('owner.laporan.expense.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Input Pengeluaran
                                </h3>
                                <div class="mt-4 space-y-4">
                                    {{-- Amount --}}
                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah Pengeluaran (Rp)</label>
                                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}"
                                            class="mt-1 block w-full shadow-sm sm:text-sm rounded-md
                                                {{ $errors->has('amount') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-red-500 focus:border-red-500' }}"
                                            placeholder="Contoh: 500000">
                                        @error('amount')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    {{-- Date --}}
                                    <div>
                                        <label for="date" class="block text-sm font-medium text-gray-700">Tanggal</label>
                                        <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}"
                                            class="mt-1 block w-full shadow-sm sm:text-sm rounded-md
                                                {{ $errors->has('date') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-red-500 focus:border-red-500' }}">
                                        @error('date')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    {{-- Type (OPEX/CAPEX) --}}
                                    <div>
                                        <span class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengeluaran</span>
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center">
                                                <input id="opex" name="type" type="radio" value="opex"
                                                    {{ old('type', 'opex') == 'opex' ? 'checked' : '' }}
                                                    class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300">
                                                <label for="opex" class="ml-2 block text-sm text-gray-700">
                                                    OPEX (Operasional)
                                                </label>
                                            </div>
                                            <div class="flex items-center">
                                                <input id="capex" name="type" type="radio" value="capex"
                                                    {{ old('type') == 'capex' ? 'checked' : '' }}
                                                    class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300">
                                                <label for="capex" class="ml-2 block text-sm text-gray-700">
                                                    CAPEX (Modal/Aset)
                                                </label>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <b>OPEX:</b> Listrik, Air, Gaji, Maintenance, Kebersihan.<br>
                                            <b>CAPEX:</b> Renovasi besar, Pembelian AC/Furniture baru.
                                        </p>
                                    </div>
                                    
                                    {{-- Category --}}
                                    <div>
                                        <label for="category" class="block text-sm font-bold text-gray-700 mb-2">Kategori Pengeluaran</label>
                                        <div class="relative">
                                            <select name="category" id="category" class="appearance-none block w-full py-3 px-4 pl-4 pr-10 bg-white rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 sm:text-sm transition ease-in-out duration-200
                                                {{ $errors->has('category') ? 'border-red-500' : 'border-gray-300' }}">
                                                <option value="" disabled selected>Pilih Kategori...</option>
                                                
                                                <optgroup id="optgroup-opex" label="OPEX (Operasional Rutin)">
                                                    <option value="Utilities">💡 Utilities (Listrik, Air, WiFi)</option>
                                                    <option value="Salary">👥 Gaji Karyawan (Payroll)</option>
                                                    <option value="Maintenance">🔧 Maintenance & Perbaikan</option>
                                                    <option value="Cleaning">🧹 Kebersihan & Lingkungan</option>
                                                    <option value="Marketing">📢 Marketing & Iklan</option>
                                                    <option value="Admin">📝 Admin & Pajak</option>
                                                    <option value="Other">📦 Lain-lain (ATK/Perlengkapan)</option>
                                                </optgroup>

                                                <optgroup id="optgroup-capex" label="CAPEX (Belanja Modal/Aset)">
                                                    <option value="Renovation">🏗️ Renovasi Bangunan</option>
                                                    <option value="Furniture">🛏️ Furniture (Kasur/Lemari)</option>
                                                    <option value="Electronics">📺 Elektronik (AC/TV/CCTV)</option>
                                                    <option value="CapexOther">💎 Aset Lainnya</option>
                                                </optgroup>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                        </div>
                                        @error('category')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    {{-- Description --}}
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                        <textarea name="description" id="description" rows="3"
                                            class="mt-1 block w-full shadow-sm sm:text-sm rounded-md
                                                {{ $errors->has('description') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-red-500 focus:border-red-500' }}"
                                            placeholder="Detail pengeluaran...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Proof Upload --}}
                                    <div>
                                        <label for="proof_image" class="block text-sm font-medium text-gray-700">Bukti Nota / Foto (Opsional)</label>
                                        <input type="file" name="proof_image" id="proof_image" accept="image/*,.pdf" class="mt-1 block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-red-50 file:text-red-700
                                            hover:file:bg-red-100
                                        ">
                                        <p class="mt-1 text-xs text-gray-500">Maksimal 2MB (JPG, PNG, PDF)</p>
                                        @error('proof_image')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Pengeluaran
                        </button>
                        <button type="button" onclick="document.getElementById('expenseModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Existing Expense Filter Script
            const radioType = document.querySelectorAll('input[name="type"]');
            const groupOpex = document.getElementById('optgroup-opex');
            const groupCapex = document.getElementById('optgroup-capex');
            const selectCat = document.getElementById('category');
    
            function updateCategoryOptions() {
                const checkedRadio = document.querySelector('input[name="type"]:checked');
                if (!checkedRadio || !groupOpex || !groupCapex) return;
    
                const selectedType = checkedRadio.value;
                
                if (selectedType === 'opex') {
                    groupOpex.style.display = '';
                    groupOpex.disabled = false;
                    groupCapex.style.display = 'none';
                    groupCapex.disabled = true;
                } else {
                    groupOpex.style.display = 'none';
                    groupOpex.disabled = true;
                    groupCapex.style.display = '';
                    groupCapex.disabled = false;
                }
                selectCat.value = ""; 
            }
    
            radioType.forEach(radio => {
                radio.addEventListener('change', updateCategoryOptions);
            });
            updateCategoryOptions();

            // Restore old category value after updateCategoryOptions runs
            @if(old('category'))
                document.getElementById('category').value = '{{ old('category') }}';
            @endif

            // Auto-reopen expense modal if there are validation errors
            @if($errors->hasAny(['amount', 'type', 'category', 'date', 'description', 'proof_image']))
                document.getElementById('expenseModal').classList.remove('hidden');
            @endif

            // Client-side ledger filtering + pagination
            const ledgerTypeFilter = document.getElementById('ledgerTypeFilter');
            const ledgerSearchInput = document.getElementById('ledgerSearchInput');
            const ledgerRows = Array.from(document.querySelectorAll('.ledger-row'));
            const ledgerEmptyRow = document.getElementById('ledgerEmptyRow');
            const ledgerCountText = document.getElementById('ledgerCountText');
            const ledgerPagination = document.getElementById('ledgerPagination');
            const LEDGER_PER_PAGE = 10;
            let ledgerCurrentPage = 1;

            function getFilteredLedgerRows() {
                const selectedType = ledgerTypeFilter.value;
                const searchText = ledgerSearchInput.value.toLowerCase().trim();
                return ledgerRows.filter(row => {
                    const type = row.dataset.ledgerType;
                    const expenseType = row.dataset.expenseType;
                    const ref = row.dataset.ledgerRef;
                    const desc = row.dataset.ledgerDesc;
                    let matchType = false;
                    if (selectedType === 'all') matchType = true;
                    else if (selectedType === 'income') matchType = type === 'income';
                    else if (selectedType === 'expense') matchType = type === 'expense';
                    else if (selectedType === 'opex') matchType = type === 'expense' && expenseType === 'opex';
                    else if (selectedType === 'capex') matchType = type === 'expense' && expenseType === 'capex';
                    const matchSearch = !searchText || ref.includes(searchText) || desc.includes(searchText);
                    return matchType && matchSearch;
                });
            }

            function renderLedgerPage() {
                const filtered = getFilteredLedgerRows();
                const totalPages = Math.max(1, Math.ceil(filtered.length / LEDGER_PER_PAGE));
                if (ledgerCurrentPage > totalPages) ledgerCurrentPage = totalPages;
                const start = (ledgerCurrentPage - 1) * LEDGER_PER_PAGE;
                const end = start + LEDGER_PER_PAGE;

                ledgerRows.forEach(row => row.style.display = 'none');
                filtered.slice(start, end).forEach(row => row.style.display = '');

                if (ledgerEmptyRow) ledgerEmptyRow.style.display = filtered.length > 0 ? 'none' : '';

                if (ledgerCountText) {
                    if (filtered.length > 0) {
                        const showStart = start + 1;
                        const showEnd = Math.min(end, filtered.length);
                        ledgerCountText.innerHTML = 'Menampilkan <span class="font-bold text-gray-700">' + showStart + '</span> - <span class="font-bold text-gray-700">' + showEnd + '</span> dari <span class="font-bold text-emerald-600">' + filtered.length + '</span> transaksi';
                    } else {
                        ledgerCountText.innerHTML = 'Tidak ada transaksi yang cocok';
                    }
                }

                renderPagination(ledgerPagination, ledgerCurrentPage, totalPages, function(page) {
                    ledgerCurrentPage = page;
                    renderLedgerPage();
                });
            }

            function filterLedger() {
                ledgerCurrentPage = 1;
                renderLedgerPage();
            }

            if (ledgerTypeFilter) ledgerTypeFilter.addEventListener('change', filterLedger);
            if (ledgerSearchInput) ledgerSearchInput.addEventListener('input', filterLedger);

            // Client-side report filtering + pagination
            const reportTypeFilter = document.getElementById('reportTypeFilter');
            const reportSearchInput = document.getElementById('reportSearchInput');
            const reportRows = Array.from(document.querySelectorAll('.report-row'));
            const reportEmptyRow = document.getElementById('reportEmptyRow');
            const reportCountText = document.getElementById('reportCountText');
            const reportPagination = document.getElementById('reportPagination');
            const REPORT_PER_PAGE = 5;
            let reportCurrentPage = 1;

            function getFilteredReportRows() {
                const selectedType = reportTypeFilter.value;
                const searchText = reportSearchInput.value.toLowerCase().trim();
                return reportRows.filter(row => {
                    const type = row.dataset.reportType;
                    const title = row.dataset.reportTitle;
                    const matchType = selectedType === 'all' || type === selectedType;
                    const matchSearch = !searchText || title.includes(searchText);
                    return matchType && matchSearch;
                });
            }

            function renderReportPage() {
                const filtered = getFilteredReportRows();
                const totalPages = Math.max(1, Math.ceil(filtered.length / REPORT_PER_PAGE));
                if (reportCurrentPage > totalPages) reportCurrentPage = totalPages;
                const start = (reportCurrentPage - 1) * REPORT_PER_PAGE;
                const end = start + REPORT_PER_PAGE;

                reportRows.forEach(row => row.style.display = 'none');
                filtered.slice(start, end).forEach(row => row.style.display = '');

                if (reportEmptyRow) reportEmptyRow.style.display = filtered.length > 0 ? 'none' : '';

                if (reportCountText) {
                    if (filtered.length > 0) {
                        const showStart = start + 1;
                        const showEnd = Math.min(end, filtered.length);
                        reportCountText.innerHTML = 'Menampilkan <span class="font-bold text-gray-700">' + showStart + '</span> - <span class="font-bold text-gray-700">' + showEnd + '</span> dari <span class="font-bold text-emerald-600">' + filtered.length + '</span> laporan';
                    } else {
                        reportCountText.innerHTML = 'Tidak ada laporan yang cocok';
                    }
                }

                renderPagination(reportPagination, reportCurrentPage, totalPages, function(page) {
                    reportCurrentPage = page;
                    renderReportPage();
                });
            }

            function filterReports() {
                reportCurrentPage = 1;
                renderReportPage();
            }

            if (reportTypeFilter) reportTypeFilter.addEventListener('change', filterReports);
            if (reportSearchInput) reportSearchInput.addEventListener('input', filterReports);

            // Shared pagination renderer
            function renderPagination(container, currentPage, totalPages, onPageClick) {
                if (!container) return;
                container.innerHTML = '';
                if (totalPages <= 1) return;

                const btnBase = 'relative inline-flex items-center px-4 py-2 border text-sm font-medium';
                const btnActive = 'z-10 bg-emerald-50 border-emerald-500 text-emerald-600 font-bold';
                const btnNormal = 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 cursor-pointer';
                const btnDisabled = 'bg-white border-gray-300 text-gray-300 cursor-not-allowed';

                // Prev
                const prev = document.createElement('button');
                prev.type = 'button';
                prev.className = 'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium ' + (currentPage === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-500 hover:bg-gray-50 cursor-pointer');
                prev.innerHTML = '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
                if (currentPage > 1) prev.addEventListener('click', function() { onPageClick(currentPage - 1); });
                container.appendChild(prev);

                // Page numbers
                const pages = getPageNumbers(currentPage, totalPages);
                pages.forEach(function(p) {
                    if (p === '...') {
                        const dots = document.createElement('span');
                        dots.className = btnBase + ' bg-white border-gray-300 text-gray-700';
                        dots.textContent = '...';
                        container.appendChild(dots);
                    } else {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = btnBase + ' ' + (p === currentPage ? btnActive : btnNormal);
                        btn.textContent = p;
                        if (p !== currentPage) btn.addEventListener('click', function() { onPageClick(p); });
                        container.appendChild(btn);
                    }
                });

                // Next
                const next = document.createElement('button');
                next.type = 'button';
                next.className = 'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium ' + (currentPage === totalPages ? 'text-gray-300 cursor-not-allowed' : 'text-gray-500 hover:bg-gray-50 cursor-pointer');
                next.innerHTML = '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>';
                if (currentPage < totalPages) next.addEventListener('click', function() { onPageClick(currentPage + 1); });
                container.appendChild(next);
            }

            function getPageNumbers(current, total) {
                if (total <= 7) return Array.from({length: total}, function(_, i) { return i + 1; });
                const pages = [];
                pages.push(1);
                if (current > 3) pages.push('...');
                for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) pages.push(i);
                if (current < total - 2) pages.push('...');
                pages.push(total);
                return pages;
            }

            // Initial render
            renderLedgerPage();
            renderReportPage();
        });

        // Transaction Modal Script
        function openTrxModal(btn) {
            const modal = document.getElementById('trxModal');
            document.getElementById('m-ref').textContent = btn.dataset.ref;
            document.getElementById('m-date').textContent = btn.dataset.date;
            document.getElementById('m-amount').textContent = 'Rp ' + btn.dataset.amount;

            // Elements to Toggle
            const rowTenant = document.getElementById('m-row-tenant');
            const rowRoom = document.getElementById('m-row-room');
            const rowTimestamps = document.getElementById('m-row-timestamps');
            const rowCategory = document.getElementById('m-row-category');
            const rowDesc = document.getElementById('m-row-desc');

            if (btn.dataset.type === 'expense') {
                // EXPENSE VIEW
                rowTenant.classList.add('hidden');
                rowRoom.classList.add('hidden');
                rowTimestamps.classList.add('hidden'); // No admin/owner verify timestamps for expense
                
                rowCategory.classList.remove('hidden');
                document.getElementById('m-category').textContent = btn.dataset.category;
                
                rowDesc.classList.remove('hidden');
                document.getElementById('m-desc').textContent = btn.dataset.desc;
            } else {
                // INCOME VIEW (Default)
                rowCategory.classList.add('hidden');
                rowDesc.classList.add('hidden');

                rowTenant.classList.remove('hidden');
                document.getElementById('m-tenant').textContent = btn.dataset.tenant;

                rowRoom.classList.remove('hidden');
                document.getElementById('m-room').textContent = btn.dataset.room;
                
                rowTimestamps.classList.remove('hidden');
                document.getElementById('m-admin-date').textContent = btn.dataset.adminDate;
                document.getElementById('m-owner-date').textContent = btn.dataset.ownerDate;
            }

            const proofImg = document.getElementById('m-proof');
            const proofContainer = document.getElementById('m-proof-container');
            
            if (btn.dataset.proof) {
                proofImg.src = btn.dataset.proof;
                proofContainer.classList.remove('hidden');
            } else {
                proofContainer.classList.add('hidden');
            }

            modal.classList.remove('hidden');
        }

        function closeTrxModal() {
            document.getElementById('trxModal').classList.add('hidden');
        }
    </script>
    
    <!-- Transaction Detail Modal -->
    <div id="trxModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeTrxModal()" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Detail Transaksi
                            </h3>
                            <div class="mt-4 space-y-3 text-sm">
                                <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                                    <span class="text-gray-500">No. Referensi:</span>
                                    <span class="font-bold text-gray-800 text-right" id="m-ref">-</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                                    <span class="text-gray-500">Tanggal Bayar:</span>
                                    <span class="font-bold text-gray-800 text-right" id="m-date">-</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                                    <span class="text-gray-500">Jumlah:</span>
                                    <span class="font-bold text-emerald-600 text-right" id="m-amount">-</span>
                                </div>
                                
                                {{-- Income Fields --}}
                                <div id="m-row-tenant" class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                                    <span class="text-gray-500">Penyewa:</span>
                                    <span class="font-bold text-gray-800 text-right" id="m-tenant">-</span>
                                </div>
                                <div id="m-row-room" class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                                    <span class="text-gray-500">Kamar:</span>
                                    <span class="font-bold text-gray-800 text-right" id="m-room">-</span>
                                </div>

                                {{-- Expense Fields --}}
                                <div id="m-row-category" class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2 hidden">
                                    <span class="text-gray-500">Kategori:</span>
                                    <span class="font-bold text-gray-800 text-right" id="m-category">-</span>
                                </div>
                                <div id="m-row-desc" class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2 hidden">
                                    <span class="text-gray-500">Deskripsi:</span>
                                    <span class="font-bold text-gray-800 text-right" id="m-desc">-</span>
                                </div>
                                
                                <div id="m-row-timestamps" class="bg-gray-50 p-3 rounded-lg space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-xs text-gray-500">Diverifikasi Admin:</span>
                                        <span class="text-xs font-mono font-bold text-blue-600" id="m-admin-date">-</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-xs text-gray-500">Diterima Owner:</span>
                                        <span class="text-xs font-mono font-bold text-green-600" id="m-owner-date">-</span>
                                    </div>
                                </div>

                                <div id="m-proof-container" class="mt-3 hidden">
                                    <p class="text-gray-500 mb-1">Bukti Transfer/Nota:</p>
                                    <img src="" id="m-proof" class="w-full rounded-lg border border-gray-200 shadow-sm" alt="Bukti">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeTrxModal()">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('tour')
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.__pageTour = [
        { element: 'a[href$="/owner/laporan/create"]', popover: { title: 'Buat Laporan', description: 'Hasilkan laporan keuangan, status kamar, atau data penyewa (PDF & Excel).' } },
    ];
    window.KostTour && window.KostTour.auto('owner-laporan-v1', window.__pageTour);
});
</script>
@endpush
