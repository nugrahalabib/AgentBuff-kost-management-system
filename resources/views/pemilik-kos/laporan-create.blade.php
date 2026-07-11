@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">Buat Laporan</h2>
            <p class="text-sm text-gray-500">Hasilkan laporan keuangan, status kamar, atau data penyewa (PDF & Excel).</p>
        </div>
        <a href="{{ route('owner.laporan') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-bold py-3 px-6 rounded-xl shadow-md transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            KEMBALI
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ period: '{{ old('period_type', 'monthly') }}' }">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-lg text-gray-800">Parameter Laporan</h3>
            </div>

            <form action="{{ route('owner.laporan.generate') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="report_type" class="block text-sm font-bold text-gray-700 mb-2">Jenis Laporan <span class="text-red-500">*</span></label>
                    <select id="report_type" name="report_type" required
                        class="w-full px-4 py-3 rounded-xl border {{ $errors->has('report_type') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        @foreach($reportTypes as $val => $label)
                            <option value="{{ $val }}" {{ old('report_type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('report_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="period_type" class="block text-sm font-bold text-gray-700 mb-2">Periode</label>
                    <select id="period_type" name="period_type" x-model="period"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        <option value="monthly">Bulanan</option>
                        <option value="annual">Tahunan (1 tahun penuh)</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div x-show="period === 'monthly'">
                        <label for="month" class="block text-sm font-bold text-gray-700 mb-2">Bulan</label>
                        <select id="month" name="month"
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('month') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                        @error('month')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-bold text-gray-700 mb-2">Tahun <span class="text-red-500">*</span></label>
                        <input type="number" id="year" name="year" value="{{ old('year', now()->year) }}" min="2020" max="2100" required
                            class="w-full px-4 py-3 rounded-xl border {{ $errors->has('year') ? 'border-red-500' : 'border-gray-300' }} focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        @error('year')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition">
                        BUAT LAPORAN
                    </button>
                    <a href="{{ route('owner.laporan') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-xl text-center transition">BATAL</a>
                </div>
            </form>
        </div>
    </div>
@endsection
