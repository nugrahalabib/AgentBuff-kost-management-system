@extends('layouts.admin')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Pusat Notifikasi') }}
            </h2>
            <p class="text-sm text-gray-500">Pemberitahuan penting dan persetujuan yang membutuhkan tindakan Anda.</p>
        </div>
        <button class="text-emerald-700 font-bold text-sm hover:text-emerald-900 hover:bg-emerald-50 px-4 py-2 rounded-lg transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Tandai Semua Dibaca
        </button>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
            
        <div class="flex flex-wrap gap-2 pb-2">
            <button class="bg-emerald-900 text-white px-5 py-2 rounded-full text-sm font-bold shadow-md transition">
                Semua
            </button>
            <button class="bg-white text-gray-600 hover:bg-red-50 hover:text-red-600 border border-gray-200 px-5 py-2 rounded-full text-sm font-medium transition flex items-center gap-2">
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                Urgent & Approval
            </button>
            <button class="bg-white text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 border border-gray-200 px-5 py-2 rounded-full text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Keuangan
            </button>
            <button class="bg-white text-gray-600 hover:bg-blue-50 hover:text-blue-600 border border-gray-200 px-5 py-2 rounded-full text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Info Sistem
            </button>
        </div>

        <div class="space-y-8">

            <div class="space-y-3">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-2">Notifikasi Terbaru</h3>

                @forelse ($notifications as $notification)
                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 {{ $notification->category == 'urgent' ? 'border-l-red-500' : ($notification->category == 'finance' ? 'border-l-emerald-500' : 'border-l-blue-500') }} border-y border-r border-gray-100 relative group hover:shadow-md transition">
                    @if ($notification->status == 'unread')
                    <div class="absolute top-5 right-5 w-2.5 h-2.5 {{ $notification->category == 'urgent' ? 'bg-red-500' : 'bg-emerald-500' }} rounded-full animate-ping"></div>
                    <div class="absolute top-5 right-5 w-2.5 h-2.5 {{ $notification->category == 'urgent' ? 'bg-red-500' : 'bg-emerald-500' }} rounded-full"></div>
                    @endif

                    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
                        <div class="w-12 h-12 rounded-full {{ $notification->category == 'urgent' ? 'bg-red-50 text-red-600' : ($notification->category == 'finance' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600') }} flex items-center justify-center flex-shrink-0">
                            @if ($notification->category == 'urgent')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <h4 class="text-base font-bold text-gray-800">{{ $notification->type }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>

                        @if ($notification->action_required)
                        <div class="flex gap-2 w-full md:w-auto">
                            <button class="flex-1 md:flex-none bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-bold px-4 py-2 rounded-lg transition">
                                Tolak
                            </button>
                            <button class="flex-1 md:flex-none bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-2 rounded-lg shadow-md transition">
                                Setujui
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center">
                    <p class="text-gray-500 text-sm">Tidak ada notifikasi</p>
                </div>
                @endforelse
            </div>
                    </div>

                    <button class="bg-white border border-gray-300 text-gray-600 hover:text-emerald-600 hover:border-emerald-500 px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.481 0 1.461 1.063 2.875 1.211 3.074.149.198 2.095 3.2 5.076 4.487 2.982 1.288 2.982.859 3.526.809.544-.05 1.758-.718 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        Ingatkan via WA
                    </button>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 items-start md:items-center hover:bg-gray-50 transition opacity-80 hover:opacity-100">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    
                    <div class="flex-1">
                        <h4 class="text-base font-bold text-gray-700">Kontrak Segera Habis</h4>
                        <p class="text-sm text-gray-600 mt-1">Sewa Kamar 103 (Siti Aminah) akan berakhir dalam 7 hari lagi.</p>
                        <p class="text-xs text-gray-400 mt-2">Kemarin, 08:00 AM</p>
                    </div>

                    <button class="text-blue-600 text-sm font-bold hover:underline">
                        Lihat Data Penyewa
                    </button>
                </div>
            </div>

        </div>
    </div>
@endsection