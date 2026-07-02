@extends('layouts.admin')

@section('title', 'Pusat Laporan & Arsip')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Pusat Laporan') }}
            </h2>
            <p class="text-sm text-gray-500">Generate dan kelola laporan keuangan, kamar, dan penyewa.</p>
        </div>
    </div>
@endsection

@section('content')

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <p class="text-red-700 text-sm font-bold">❌ {{ $errors->first() }}</p>
        </div>
    @endif


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-emerald-500"></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Laporan Bulan Ini</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $monthlyReports }} <span class="text-sm font-medium text-gray-400">Dokumen</span></p>
            </div>
            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>
        
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-orange-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-orange-500"></div>
            <div>
                <p class="text-xs font-bold text-orange-500 uppercase tracking-wider">Belum Disubmit</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $pendingReports }} <span class="text-sm font-medium text-gray-400">Dokumen</span></p>
                <p class="text-[10px] text-gray-400 mt-1">Status Draft</p>
            </div>
            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center animate-pulse">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-blue-100 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute left-0 top-0 h-full w-1 bg-blue-500"></div>
            <div>
                <p class="text-xs font-bold text-blue-500 uppercase tracking-wider">Total Dikirim</p>
                <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ $totalArchived }} <span class="text-sm font-medium text-gray-400">File</span></p>
            </div>
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 blur-2xl"></div>

        <div class="flex items-center gap-2 mb-6">
            <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Generate Laporan Baru</h3>
        </div>

        <form id="generateReportForm" action="{{ route('admin.laporan.generate') }}" method="POST">
            @csrf
            
            <div class="space-y-5">
                <!-- Section 1: Report Type -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Jenis Laporan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- Financial Report -->
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="report_type" value="financial_report" class="peer sr-only" required>
                            <div class="p-3 rounded-xl border border-gray-200 bg-white hover:border-emerald-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 transition-all flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2a2 2 0 01-1 0m-8 0a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-6"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-gray-700 peer-checked:text-emerald-700 leading-tight">Keuangan & Transaksi</span>
                                <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-emerald-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </label>

                        <!-- Room Status -->
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="report_type" value="room_status_report" class="peer sr-only">
                            <div class="p-3 rounded-xl border border-gray-200 bg-white hover:border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-50/50 transition-all flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-gray-700 peer-checked:text-blue-700 leading-tight">Status Kamar</span>
                                <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-blue-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </label>

                        <!-- Tenant Data -->
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="report_type" value="tenant_report" class="peer sr-only">
                            <div class="p-3 rounded-xl border border-gray-200 bg-white hover:border-violet-200 peer-checked:border-violet-500 peer-checked:bg-violet-50/50 transition-all flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-gray-700 peer-checked:text-violet-700 leading-tight">Data Penyewa</span>
                                <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-violet-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </label>

                        <!-- Comprehensive -->
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="report_type" value="comprehensive_report" class="peer sr-only">
                            <div class="p-3 rounded-xl border border-gray-200 bg-white hover:border-orange-200 peer-checked:border-orange-500 peer-checked:bg-orange-50/50 transition-all flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-gray-700 peer-checked:text-orange-700 leading-tight">Integrasi (Gabungan)</span>
                                <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 text-orange-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Section 2: Period & Action -->
                <div class="bg-gray-50/50 rounded-xl p-4 border border-gray-100">
                    <div class="flex flex-col lg:flex-row gap-4 items-center">
                        <!-- Label & Tabs -->
                        <div class="flex flex-col sm:flex-row items-center gap-3 flex-shrink-0 w-full lg:w-auto">
                           <span class="text-xs font-bold text-gray-400 uppercase hidden lg:block">Periode:</span>
                           <div class="bg-white p-1 rounded-lg border border-gray-200 inline-flex w-full sm:w-auto">
                                <label class="cursor-pointer flex-1 sm:flex-none">
                                    <input type="radio" name="period_type" value="monthly" class="peer sr-only" checked id="periodMonthly">
                                    <span class="block px-4 py-2 rounded-md text-xs font-bold text-center text-gray-500 transition-all peer-checked:bg-gray-900 peer-checked:text-white peer-checked:shadow-sm hover:text-gray-700">Bulanan</span>
                                </label>
                                <label class="cursor-pointer flex-1 sm:flex-none">
                                    <input type="radio" name="period_type" value="range" class="peer sr-only" id="periodRange">
                                    <span class="block px-4 py-2 rounded-md text-xs font-bold text-center text-gray-500 transition-all peer-checked:bg-gray-900 peer-checked:text-white peer-checked:shadow-sm hover:text-gray-700">Rentang</span>
                                </label>
                                <label class="cursor-pointer flex-1 sm:flex-none">
                                    <input type="radio" name="period_type" value="annual" class="peer sr-only" id="periodAnnual">
                                    <span class="block px-4 py-2 rounded-md text-xs font-bold text-center text-gray-500 transition-all peer-checked:bg-gray-900 peer-checked:text-white peer-checked:shadow-sm hover:text-gray-700">Tahunan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="hidden lg:block w-px h-8 bg-gray-200"></div>

                        <!-- Dynamic Inputs -->
                        <div class="flex-1 w-full">
                            <!-- Monthly -->
                            <div id="monthlyInputGroup" class="transition-all duration-300 flex gap-2">
                                <select name="month" class="w-full lg:w-48 py-2 px-3 border border-gray-200 rounded-lg text-sm font-bold text-gray-700 bg-white focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endforeach
                                </select>
                                <select name="year" class="w-32 py-2 px-3 border border-gray-200 rounded-lg text-sm font-bold text-gray-700 bg-white focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach(range(date('Y')-5, date('Y')+1) as $y)
                                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Range -->
                             <div id="rangeInputGroup" class="hidden transition-all duration-300">
                                 <div class="flex flex-col sm:flex-row gap-2 items-center">
                                    <div class="flex gap-2 w-full sm:w-auto">
                                        <select name="month" disabled class="w-full sm:w-32 py-2 px-3 border border-gray-200 rounded-lg text-sm font-bold text-gray-700 bg-white">
                                            @foreach(range(1, 12) as $m)
                                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $m, 1)) }}</option>
                                            @endforeach
                                        </select>
                                        <select name="year" disabled class="w-24 py-2 px-3 border border-gray-200 rounded-lg text-sm font-bold text-gray-700 bg-white">
                                            @foreach(range(date('Y')-5, date('Y')+1) as $y)
                                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span class="text-gray-400 font-bold text-xs">S.D.</span>
                                    <div class="flex gap-2 w-full sm:w-auto">
                                        <select name="end_month" disabled class="w-full sm:w-32 py-2 px-3 border border-gray-200 rounded-lg text-sm font-bold text-gray-700 bg-white">
                                            @foreach(range(1, 12) as $m)
                                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $m, 1)) }}</option>
                                            @endforeach
                                        </select>
                                        <select name="end_year" disabled class="w-24 py-2 px-3 border border-gray-200 rounded-lg text-sm font-bold text-gray-700 bg-white">
                                            @foreach(range(date('Y')-5, date('Y')+1) as $y)
                                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                 </div>
                            </div>

                            <!-- Annual -->
                            <div id="annualInputGroup" class="hidden transition-all duration-300">
                                 <select name="year" disabled class="w-full lg:w-48 py-2 px-3 border border-gray-200 rounded-lg text-sm font-bold text-gray-700 bg-white focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach(range(date('Y')-5, date('Y')+1) as $y)
                                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                         <!-- Generate Button -->
                         <button type="submit" class="w-full lg:w-auto bg-gray-900 hover:bg-black text-white font-bold py-2.5 px-6 rounded-lg shadow-sm hover:shadow-md transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span>Generate</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- JS Logic for Tabs -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.getElementsByName('period_type');
            
            const groups = {
                'monthly': document.getElementById('monthlyInputGroup'),
                'range': document.getElementById('rangeInputGroup'),
                'annual': document.getElementById('annualInputGroup')
            };

            function updateView() {
                let selected = 'monthly';
                for(const r of radios) { if(r.checked) selected = r.value; }

                // Hide all and disable inputs to avoid conflicts
                for (const key in groups) {
                    groups[key].classList.add('hidden');
                    const inputs = groups[key].querySelectorAll('select');
                    inputs.forEach(el => el.disabled = true);
                }

                // Show selected and enable inputs
                if (groups[selected]) {
                    groups[selected].classList.remove('hidden');
                    const inputs = groups[selected].querySelectorAll('select');
                    inputs.forEach(el => el.disabled = false);
                }
            }

            for(const r of radios) {
                r.addEventListener('change', updateView);
            }
            
            // Init
            updateView();
        });
    </script>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <div class="flex flex-col md:flex-row gap-3 justify-between items-start md:items-center">
                <h3 class="font-bold text-gray-800 flex-shrink-0">Daftar Laporan</h3>

                <div class="flex flex-wrap gap-2 items-center w-full md:w-auto">
                    {{-- Status Filter --}}
                    <div class="flex bg-gray-100 p-1 rounded-lg">
                        @php
                            $statusBase = array_filter(['search' => $search, 'report_type' => $reportType]);
                        @endphp
                        <a href="{{ route('admin.laporan', $statusBase) }}"
                            class="px-3 py-1 rounded-md text-xs font-bold transition {{ !$selectedStatus || $selectedStatus === 'semua' ? 'bg-white shadow-sm text-gray-700' : 'text-gray-500 hover:text-emerald-600' }}">Semua</a>
                        <a href="{{ route('admin.laporan', array_merge($statusBase, ['status' => 'sent'])) }}"
                            class="px-3 py-1 rounded-md text-xs font-bold transition {{ $selectedStatus === 'sent' ? 'bg-white shadow-sm text-gray-700' : 'text-gray-500 hover:text-emerald-600' }}">Terkirim</a>
                        <a href="{{ route('admin.laporan', array_merge($statusBase, ['status' => 'draft'])) }}"
                            class="px-3 py-1 rounded-md text-xs font-bold transition {{ $selectedStatus === 'draft' ? 'bg-white shadow-sm text-gray-700' : 'text-gray-500 hover:text-orange-600' }}">Draft</a>
                    </div>

                    {{-- Type & Search Form --}}
                    <form method="GET" action="{{ route('admin.laporan') }}" class="flex gap-2 items-center flex-wrap">
                        @if($selectedStatus)
                            <input type="hidden" name="status" value="{{ $selectedStatus }}">
                        @endif

                        <select name="report_type" onchange="this.form.submit()"
                            class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 bg-gray-50 hover:bg-white focus:ring-emerald-500 focus:border-emerald-500 transition cursor-pointer">
                            <option value="all" {{ !$reportType || $reportType === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                            <option value="financial_report"    {{ $reportType === 'financial_report'    ? 'selected' : '' }}>Keuangan</option>
                            <option value="room_status_report"  {{ $reportType === 'room_status_report'  ? 'selected' : '' }}>Kamar</option>
                            <option value="tenant_report"       {{ $reportType === 'tenant_report'       ? 'selected' : '' }}>Penyewa</option>
                            <option value="comprehensive_report" {{ $reportType === 'comprehensive_report' ? 'selected' : '' }}>Gabungan</option>
                        </select>

                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Cari nama laporan..."
                                class="pl-3 pr-8 py-1.5 border border-gray-200 rounded-lg text-xs bg-gray-50 hover:bg-white focus:ring-emerald-500 w-40 md:w-48 transition">
                            @if($search)
                                <a href="{{ route('admin.laporan', array_filter(['status' => $selectedStatus, 'report_type' => $reportType])) }}"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>

                        <button type="submit"
                            class="bg-emerald-600 text-white p-1.5 rounded-lg hover:bg-emerald-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Laporan</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis</th>

                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($reports as $report)
                        @php
                            $isDraft = $report->status === 'draft';
                            $reportTypeLabel = match($report->report_type) {
                                'financial_report' => 'Keuangan',
                                'room_status_report' => 'Kamar',
                                'tenant_report' => 'Penyewa',
                                'comprehensive_report' => 'Gabungan',
                                default => $report->report_type,
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $report->title }}</div>

                            </td>
                            <td class="px-6 py-4 font-bold text-gray-600">{{ $reportTypeLabel }}</td>

                            <td class="px-6 py-4 text-gray-500">{{ $report->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @if ($isDraft)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Draft
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Terkirim
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.laporan.preview', $report) }}" title="Preview" class="p-2 text-gray-400 hover:text-blue-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('admin.laporan.download-pdf', $report) }}" title="Download PDF" class="p-2 text-gray-400 hover:text-red-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </a>
                                    <a href="{{ route('admin.laporan.download-excel', $report) }}" title="Download Excel" class="p-2 text-gray-400 hover:text-green-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </a>
                                    @if ($isDraft)
                                        <form action="{{ route('admin.laporan.submit', $report) }}" method="POST" class="inline" onsubmit="confirmSubmit(event, 'Kirim laporan ini ke owner?');">
                                            @csrf
                                            <button type="submit" title="Submit" class="p-2 text-gray-400 hover:text-emerald-600 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.laporan.destroy', $report) }}" method="POST" class="inline" onsubmit="confirmSubmit(event, 'Hapus laporan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus" class="p-2 text-gray-400 hover:text-red-600 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Belum ada laporan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <span class="text-xs text-gray-500">{{ $reports->total() }} laporan total</span>
            <div class="flex gap-2">
                {{ $reports->appends(request()->query())->links('components.pagination.admin') }}
            </div>
        </div>
    </div>

    <script>
        // Convert month input to report_month and report_year
        document.getElementById('generateReportForm').addEventListener('submit', function(e) {
            const monthYearInput = document.querySelector('input[name="report_month_year"]');
            const monthYear = monthYearInput.value;
            
            if (!monthYear || !monthYear.includes('-')) {
                e.preventDefault();
                Toast.fire({ icon: 'warning', title: 'Silakan pilih periode laporan terlebih dahulu!' });
                return false;
            }
            
            const [year, month] = monthYear.split('-');
            
            // Create hidden inputs for month and year
            const input1 = document.createElement('input');
            input1.type = 'hidden';
            input1.name = 'report_month';
            input1.value = parseInt(month, 10); // Convert to integer
            
            const input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = 'report_year';
            input2.value = parseInt(year, 10); // Convert to integer
            
            this.appendChild(input1);
            this.appendChild(input2);
            
            // Remove the original input to avoid confusion
            monthYearInput.removeAttribute('name');
        });
    </script>

    <script>
        function submitReport(id) {
            const btn = document.querySelector(`#action-${id} button:last-child`);
            const statusCell = document.getElementById(`status-${id}`);
            const actionCell = document.getElementById(`action-${id}`);

            btn.innerHTML = `<svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mengirim...`;
            
            setTimeout(() => {
                statusCell.innerHTML = `
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200 animate-fade-in">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Terkirim
                    </span>`;
                
                btn.remove();
                actionCell.innerHTML += `<span class="text-xs text-gray-300 italic ml-2 animate-fade-in">Baru saja dikirim</span>`;
                
                Toast.fire({ icon: 'success', title: 'Laporan berhasil dikirim ke Owner!' });
            }, 1500);
        }
    </script>

@endsection