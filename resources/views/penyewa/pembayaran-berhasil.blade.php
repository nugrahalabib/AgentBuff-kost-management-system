@extends('layouts.penyewa')

@section('content')
    <div class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4">
        
        <div class="max-w-md w-full">
            
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">Pembayaran Terkirim!</h1>
                <p class="text-gray-500 mt-2 text-sm">Terima kasih. Bukti pembayaran Anda sedang diverifikasi oleh admin kami (Max 1x24 Jam).</p>
            </div>

            <div class="bg-white rounded-3xl shadow-xl overflow-hidden relative border border-gray-100">
                <div class="h-3 bg-emerald-600"></div>
                
                <div class="p-8">
                    <div class="flex justify-between items-start mb-8 border-b border-dashed border-gray-200 pb-6">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">INVOICE</p>
                            <p class="text-sm font-mono text-gray-800 mt-1">#{{ $transaksiItem->invoice_number }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-800 tracking-tight text-sm">Mutiara<span class="text-emerald-600">27</span></span>
                            <p class="text-[10px] text-gray-400">Official Receipt</p>
                        </div>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Layanan</p>
                            <p class="font-bold text-gray-800 text-sm">
                                {{ $transaksiItem->room?->roomType?->name ?? 'Sewa Kamar' }} 
                                {{ $transaksiItem->room ? '- Kamar ' . $transaksiItem->room->room_number : '' }}
                            </p>
                        </div>
                        <div class="flex justify-between">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">Tanggal</p>
                                <p class="font-bold text-gray-800 text-sm">{{ \Carbon\Carbon::parse($transaksiItem->payment_date)->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400 mb-1">Metode</p>
                                <p class="font-bold text-gray-800 text-sm">Transfer Bank</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex justify-between items-center mb-6">
                        <span class="text-sm font-bold text-gray-500">Total Bayar</span>
                        <span class="text-xl font-extrabold text-emerald-700">Rp {{ number_format($transaksiItem->amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-center">
                        <span class="bg-yellow-50 text-yellow-700 text-xs font-bold px-3 py-1.5 rounded-full border border-yellow-200 flex items-center gap-2">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                            @if(in_array($transaksiItem->status, ['pending_verification', 'verified_by_admin']))
                                Proses Verifikasi Admin
                            @elseif($transaksiItem->status == 'verified_by_owner')
                                <span class="bg-emerald-50 text-emerald-700 border-emerald-200">LUNAS</span>
                            @else
                                {{ strtoupper(str_replace('_', ' ', $transaksiItem->status)) }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex gap-3">
                    <a href="{{ route('tenant.transaction.receipt', $transaksiItem->id) }}" class="flex-1 bg-white border border-gray-200 text-gray-600 hover:text-emerald-600 hover:border-emerald-200 font-bold py-2.5 rounded-xl text-xs transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh PDF
                    </a>
                    <a href="{{ route('tenant.dashboard') }}" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl text-xs transition shadow-md flex items-center justify-center">
                        Selesai
                    </a>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-8">
                Data pembayaran pada dashboard akan otomatis terupdate ketika admin sudah selesai verifikasi pembayaran.
            </p>

        </div>
    </div>
@endsection