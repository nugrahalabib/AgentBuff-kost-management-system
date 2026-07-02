@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Executive Dashboard') }}
            </h2>
            <p class="text-sm text-gray-500 hidden sm:block">Ringkasan bisnis, cashflow, dan operasional properti Anda.</p>
        </div>
        <div class="flex items-center gap-3">
            <div
                class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg flex items-center shadow-sm overflow-hidden">
                <a href="{{ route('owner.dashboard', ['month' => \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->subMonth()->month, 'year' => \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->subMonth()->year]) }}"
                    class="px-3 py-2 hover:bg-gray-50 border-r border-gray-300 transition block">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <span class="px-4 py-2 font-bold text-emerald-800 min-w-[150px] text-center block">
                    {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->translatedFormat('F Y') }}
                </span>
                <a href="{{ route('owner.dashboard', ['month' => \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->addMonth()->month, 'year' => \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->addMonth()->year]) }}"
                    class="px-3 py-2 hover:bg-gray-50 border-l border-gray-300 transition block">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-8">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            <!-- Total Pemasukan -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                    <svg class="w-16 h-16 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Pemasukan</p>
                <h3 class="text-xl font-extrabold text-gray-800">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
                <div class="mt-2 flex items-center text-xs">
                    <span
                        class="text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded-md">{{ $incomeGrowthPercent }}%</span>
                    <span class="text-gray-400 ml-2">dari bulan lalu</span>
                </div>
            </div>

            <!-- Pemasukan Sementara -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                    <svg class="w-16 h-16 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pemasukan Sementara</p>
                <h3 class="text-xl font-extrabold text-gray-800">Rp {{ number_format($pendingIncome, 0, ',', '.') }}</h3>
                <div class="mt-2 flex items-center text-xs">
                    <span class="text-amber-600 font-bold bg-amber-50 px-1.5 py-0.5 rounded-md">Pending</span>
                    <span class="text-gray-400 ml-2">menunggu verifikasi</span>
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                    <svg class="w-16 h-16 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a1 1 0 100-2 1 1 0 000 2z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Pengeluaran</p>
                <h3 class="text-xl font-extrabold text-gray-800">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h3>
                <div class="mt-2 flex items-center text-xs">
                    <span class="text-red-600 font-bold bg-red-50 px-1.5 py-0.5 rounded-md">{{ $expensePercent }}%</span>
                    <span class="text-gray-400 ml-2">dari pemasukan</span>
                </div>
            </div>

            <!-- Laba Bersih -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                    <svg class="w-16 h-16 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Laba Bersih</p>
                <h3 class="text-xl font-extrabold text-gray-800">Rp {{ number_format($netProfit, 0, ',', '.') }}</h3>
                <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 100%"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Cashflow positif bulan ini</p>
            </div>

            <!-- Proyeksi Bulan Depan -->
            <div class="bg-emerald-900 p-5 rounded-2xl shadow-lg relative overflow-hidden group text-white">
                <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <p class="text-xs font-bold text-emerald-300 uppercase tracking-wider mb-1">Proyeksi Bulan Depan</p>
                <h3 class="text-xl font-extrabold">Rp {{ number_format($projectedNext, 0, ',', '.') }}</h3>
                <div class="mt-2 flex items-center text-xs">
                    <span
                        class="text-emerald-100 bg-emerald-800 px-1.5 py-0.5 rounded-md border border-emerald-700">Estimasi</span>
                    <span class="text-emerald-200 ml-2 text-[10px]">Berdasarkan potensi max - pengeluaran</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Analisa Cashflow -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h4 class="text-sm font-bold text-gray-800">Analisa Cashflow</h4>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="flex items-center gap-1 text-gray-500"><span
                                class="w-2 h-2 rounded-full bg-emerald-500"></span> Masuk</span>
                        <span class="flex items-center gap-1 text-gray-500"><span
                                class="w-2 h-2 rounded-full bg-gray-300"></span> Keluar</span>
                    </div>
                </div>
                <!-- Chart Container -->
                <div id="cashflowChart" class="w-full" style="height: 420px;"></div>
            </div>

            <div class="space-y-6">
                <!-- Status Kamar -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-sm font-bold text-gray-800 mb-4">Status Kamar</h4>
                    <div class="flex items-center justify-between">
                        <!-- Donut Chart -->
                        <div class="relative" style="width: 128px; height: 128px;">
                            <div id="roomStatusChart" style="width: 128px; height: 128px;"></div>
                            <div class="absolute inset-0 flex items-center justify-center flex-col pointer-events-none">
                                <span class="text-xl font-bold text-gray-800">{{ $occupancyRate }}%</span>
                                <span class="text-[10px] text-gray-400 uppercase">Terisi</span>
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="space-y-3 text-sm flex-1 ml-6">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-gray-600 font-medium">Terisi</span>
                                </div>
                                <span class="font-bold text-gray-800">{{ $kamarTerisi }} Unit</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-gray-200"></span>
                                    <span class="text-gray-600 font-medium">Kosong</span>
                                </div>
                                <span class="font-bold text-gray-800">{{ $kamarTersedia }} Unit</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span class="text-gray-600 font-medium">Maintenance</span>
                                </div>
                                <span class="font-bold text-gray-800">{{ $kamarMaintenance }} Unit</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas Admin -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-sm font-bold text-gray-800">Aktivitas Admin</h4>
                        <a href="{{ route('owner.admin') }}"
                            class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Kelola Admin</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($logs as $log)
                            <div class="flex gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-600 flex-shrink-0">
                                    {{ substr($log->admin->name ?? 'Admin', 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-0.5">
                                        <span class="font-bold text-gray-700">{{ $log->admin->name ?? 'System' }}</span>
                                        {{ $log->activity_label }}
                                    </p>
                                    <p class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-center text-gray-400 italic py-4">Belum ada aktivitas admin.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Penyewa Terbaru -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h4 class="text-sm font-bold text-gray-800">Penyewa Terbaru & Status Pembayaran</h4>
                <a href="{{ route('owner.penyewa') }}"
                    class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Lihat Semua Data</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Penyewa</th>
                            <th class="px-6 py-3">Kamar</th>
                            <th class="px-6 py-3">Tgl Masuk</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($penyewaTerbaru as $tenant)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                            {{ substr($tenant->name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-gray-800">{{ $tenant->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $tenant->activeRoom->room_number ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    {{ $tenant->activeRoom && $tenant->activeRoom->pivot ? \Carbon\Carbon::parse($tenant->activeRoom->pivot->check_in_date)->format('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $status = $tenant->payment_status_label;
                                        $color = match ($status) {
                                            'Lancar' => 'bg-emerald-100 text-emerald-800',
                                            'Telat Bayar' => 'bg-red-100 text-red-800',
                                            'Segera Habis' => 'bg-yellow-100 text-yellow-800',
                                            default => 'bg-gray-100 text-gray-600'
                                        };
                                    @endphp
                                    <span class="{{ $color }} px-2.5 py-1 rounded-full text-xs font-bold">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="viewTenant('{{ $tenant->id }}')"
                                        class="text-gray-400 hover:text-emerald-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                    Belum ada data penyewa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts for Charts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cashflow Chart
            var cashflowOptions = {
                series: [{
                    name: 'Pemasukan',
                    data: @json($incomeData)
                }, {
                    name: 'Pengeluaran',
                    data: @json($expenseData)
                }],
                chart: {
                    type: 'bar',
                    height: 400,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#10B981', '#E5E7EB'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4
                    },
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#9CA3AF', fontSize: '10px' } }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#9CA3AF', fontSize: '10px' },
                        formatter: (value) => { return new Intl.NumberFormat('id-ID', { notation: "compact", compactDisplay: "short" }).format(value) }
                    }
                },
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                        }
                    }
                },
                legend: { show: false },
                grid: { show: true, borderColor: '#F3F4F6', strokeDashArray: 4 }
            };

            var cashflowChart = new ApexCharts(document.querySelector("#cashflowChart"), cashflowOptions);
            cashflowChart.render();

            // Room Status Chart (Donut)
            var roomStatusOptions = {
                series: [{{ $kamarTerisi }}, {{ $kamarTersedia }}, {{ $kamarMaintenance }}],
                chart: {
                    type: 'donut',
                    height: 128,
                    width: 128,
                },
                labels: ['Terisi', 'Kosong', 'Maintenance'],
                colors: ['#10B981', '#E5E7EB', '#FBBF24'],
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: { show: false }
                        }
                    }
                },
                legend: { show: false },
                stroke: { show: false },
                tooltip: {
                    enabled: true,
                    y: {
                        formatter: function (val) {
                            return val + " Unit"
                        }
                    }
                }
            };

            var roomStatusChart = new ApexCharts(document.querySelector("#roomStatusChart"), roomStatusOptions);
            roomStatusChart.render();
        });

        function viewTenant(id) {
            // Redirect to tenant detail page logic if available, or just alert for now
            // Assuming route exists or fallback
            window.location.href = "{{ url('/owner/penyewa') }}"; // Simplest for now
        }
    </script>
@endsection