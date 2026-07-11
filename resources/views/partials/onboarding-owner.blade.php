{{-- Panduan Awal (onboarding checklist) owner. Butuh: $setup, $setupComplete. Tampil hanya saat setup inti belum selesai. --}}
@if(!($setupComplete ?? true))
    @php
        $steps = [
            ['done' => $setup['tipe_kamar'], 'title' => 'Buat Tipe Kamar', 'desc' => 'Tentukan jenis & harga kamar (mis. Standar, Deluxe).', 'cta' => 'Buat Tipe', 'url' => route('owner.settings') . '?add=1'],
            ['done' => $setup['kamar'],      'title' => 'Tambah Kamar',   'desc' => 'Daftarkan unit kamar sesuai tipe yang dibuat.',     'cta' => 'Tambah Kamar', 'url' => route('owner.kamar') . '?add=1'],
            ['done' => $setup['penyewa'],    'title' => 'Tambah Penyewa', 'desc' => 'Catat penghuni & tempatkan ke kamar tersedia.',    'cta' => 'Tambah Penyewa', 'url' => route('owner.penyewa') . '?add=1'],
            ['done' => $setup['transaksi'],  'title' => 'Catat Transaksi', 'desc' => 'Rekam pembayaran sewa penyewa (opsional).',        'cta' => 'Catat', 'url' => route('owner.verifikasi-transaksi') . '?add=1'],
        ];
        $doneCount = collect($steps)->where('done', true)->count();
        $nextIndex = collect($steps)->search(fn ($s) => ! $s['done']);
    @endphp

    <div id="onboarding-card" class="bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-xl font-extrabold">👋 Selamat datang! Yuk siapkan kos Anda</h3>
                    <p class="text-emerald-100 text-sm mt-1">Ikuti langkah berurutan ini agar kos siap dikelola — tanpa bolak-balik antar halaman.</p>
                </div>
                <span class="bg-white/20 px-3 py-1.5 rounded-full text-sm font-bold whitespace-nowrap self-start">{{ $doneCount }}/{{ count($steps) }} selesai</span>
            </div>

            <div class="w-full bg-white/20 rounded-full h-2 mb-5">
                <div class="bg-white h-2 rounded-full transition-all duration-500" style="width: {{ round($doneCount / count($steps) * 100) }}%"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($steps as $i => $s)
                    <div class="flex items-center gap-3 rounded-xl p-3 {{ $i === $nextIndex ? 'bg-white/25 ring-2 ring-white/70' : 'bg-white/10' }}">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 font-bold text-sm {{ $s['done'] ? 'bg-white text-emerald-700' : 'bg-emerald-900/40 text-white' }}">
                            @if($s['done'])
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm {{ $s['done'] ? 'line-through text-emerald-200' : '' }}">{{ $s['title'] }}</p>
                            <p class="text-emerald-100 text-xs leading-snug">{{ $s['desc'] }}</p>
                        </div>
                        @if($s['done'])
                            <span class="flex-shrink-0 text-emerald-200 text-xs font-bold">Selesai</span>
                        @else
                            <a href="{{ $s['url'] }}" class="flex-shrink-0 bg-white text-emerald-700 text-xs font-bold px-3 py-2 rounded-lg hover:bg-emerald-50 transition whitespace-nowrap {{ $i === $nextIndex ? '' : 'opacity-80' }}">{{ $s['cta'] }} →</a>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($nextIndex !== false)
                <p class="text-emerald-100/90 text-xs mt-4">Langkah berikutnya: <b class="text-white">{{ $steps[$nextIndex]['title'] }}</b> — klik tombolnya untuk langsung mulai.</p>
            @endif
        </div>
    </div>
@endif
