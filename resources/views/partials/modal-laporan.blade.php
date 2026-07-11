{{-- Modal Buat Laporan (owner). Tanpa data ekstra. --}}
<div id="laporanModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 invisible opacity-0 transition-opacity duration-200" onclick="if(event.target===this) closeModal('laporanModal')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto" x-data="{ period: '{{ old('period_type', 'monthly') }}' }">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800">Buat Laporan</h3>
            <button type="button" onclick="closeModal('laporanModal')" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('owner.laporan.generate') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Laporan <span class="text-red-500">*</span></label>
                <select name="report_type" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                    <option value="financial_report" {{ old('report_type') == 'financial_report' ? 'selected' : '' }}>Laporan Keuangan & Transaksi</option>
                    <option value="room_status_report" {{ old('report_type') == 'room_status_report' ? 'selected' : '' }}>Laporan Status Kamar</option>
                    <option value="tenant_report" {{ old('report_type') == 'tenant_report' ? 'selected' : '' }}>Laporan Data Penyewa</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Periode</label>
                <select name="period_type" x-model="period" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                    <option value="monthly">Bulanan</option>
                    <option value="annual">Tahunan (1 tahun penuh)</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div x-show="period === 'monthly'">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Bulan</label>
                    <select name="month" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                        @for($m = 1; $m <= 12; $m++)<option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>@endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="year" value="{{ old('year', now()->year) }}" min="2020" max="2100" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl">BUAT LAPORAN</button>
                <button type="button" onclick="closeModal('laporanModal')" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-xl">BATAL</button>
            </div>
        </form>
    </div>
</div>
