@extends('layouts.pemilik-kos')

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
            
        {{-- Filter Categories --}}
        <div class="flex flex-wrap gap-2 pb-2">
            <a href="{{ route('owner.notifikasi') }}" class="{{ !request('category') || request('category') == 'semua' ? 'bg-emerald-900 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }} px-5 py-2 rounded-full text-sm font-bold transition">
                Semua
            </a>
            <a href="{{ route('owner.notifikasi', ['category' => 'urgent']) }}" class="{{ request('category') == 'urgent' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-white text-gray-600 hover:bg-red-50 hover:text-red-600 border border-gray-200' }} px-5 py-2 rounded-full text-sm font-medium transition flex items-center gap-2">
                <span class="w-2 h-2 bg-red-500 rounded-full {{ request('category') == 'urgent' ? 'animate-pulse' : '' }}"></span>
                Urgent & Approval
            </a>
            <a href="{{ route('owner.notifikasi', ['category' => 'finance']) }}" class="{{ request('category') == 'finance' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-white text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 border border-gray-200' }} px-5 py-2 rounded-full text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Keuangan
            </a>
            <a href="{{ route('owner.notifikasi', ['category' => 'system']) }}" class="{{ request('category') == 'system' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-white text-gray-600 hover:bg-blue-50 hover:text-blue-600 border border-gray-200' }} px-5 py-2 rounded-full text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Info Sistem
            </a>
        </div>

        <div class="space-y-8">
            @php $currentGroup = null; @endphp
            
            @forelse ($notifications as $notification)
                @php
                    if ($notification->created_at->isToday()) {
                        $group = 'Hari Ini';
                    } elseif ($notification->created_at->isYesterday()) {
                        $group = 'Kemarin';
                    } else {
                        $group = 'Lebih Lama';
                    }
                @endphp

                @if ($currentGroup !== $group)
                    @if ($currentGroup !== null) </div> @endif {{-- Close previous group div --}}
                    
                    <div class="space-y-3 animate-fade-in-up">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-2">{{ $group }}</h3>
                    @php $currentGroup = $group; @endphp
                @endif

                <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 {{ $notification->category == 'urgent' ? 'border-l-red-500' : ($notification->category == 'finance' ? 'border-l-emerald-500' : ($notification->category == 'system' ? 'border-l-blue-500' : 'border-l-gray-400')) }} border-y border-r border-gray-100 relative group hover:shadow-md transition">
                    @if ($notification->status == 'unread')
                    <div class="absolute top-5 right-5 w-2.5 h-2.5 {{ $notification->category == 'urgent' ? 'bg-red-500' : 'bg-emerald-500' }} rounded-full animate-ping"></div>
                    <div class="absolute top-5 right-5 w-2.5 h-2.5 {{ $notification->category == 'urgent' ? 'bg-red-500' : 'bg-emerald-500' }} rounded-full"></div>
                    @endif

                    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center">
                        <div class="w-12 h-12 rounded-full {{ $notification->category == 'urgent' ? 'bg-red-50 text-red-600' : ($notification->category == 'finance' ? 'bg-emerald-50 text-emerald-600' : ($notification->category == 'system' ? 'bg-blue-50 text-blue-600' : 'bg-gray-50 text-gray-600')) }} flex items-center justify-center flex-shrink-0">
                            @if ($notification->category == 'urgent')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            @elseif ($notification->type == 'payment_received' || $notification->category == 'finance')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @elseif ($notification->type == 'contract_expiring')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @elseif ($notification->type == 'maintenance_request')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <h4 class="text-base font-bold text-gray-800">{{ $notification->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 w-full md:w-auto items-center">
                            @if ($notification->type == 'payment_received')
                                <a href="{{ route('owner.verifikasi-transaksi') }}" class="text-emerald-700 text-sm font-bold hover:underline">
                                    Lihat Bukti Transfer →
                                </a>
                            @elseif ($notification->type == 'report_submitted')
                                <a href="{{ route('owner.laporan') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-md transition">
                                    Buka Laporan
                                </a>
                            @elseif ($notification->type == 'contract_expiring')
                                <a href="{{ route('owner.penyewa.show', $notification->related_entity_id) }}" class="text-blue-600 text-sm font-bold hover:underline">
                                    Lihat Data Penyewa
                                </a>
                            @elseif ($notification->action_required && $notification->related_entity_type == 'transaction')
                                @php
                                    $transaction = $notification->relatedEntity;
                                    $ref = $transaction ? $transaction->reference_number : '';
                                @endphp
                                <a href="{{ route('owner.verifikasi-transaksi', ['search' => $ref, 'status' => 'pending']) }}" class="flex-1 md:flex-none bg-white border border-gray-200 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-sm font-bold px-4 py-2 rounded-lg transition text-center flex items-center justify-center">
                                    Tolak
                                </a>
                                <form action="{{ route('owner.verifikasi-transaksi.verify', $notification->related_entity_id) }}" method="POST" class="flex-1 md:flex-none">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-2 rounded-lg shadow-md transition">
                                        Setujui
                                    </button>
                                </form>
                            @endif
                            
                            {{-- Archive Button --}}
                            <form action="{{ route('owner.notifikasi.archive', $notification->id) }}" method="POST" class="ml-2">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition p-1" title="Arsipkan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-2xl shadow-sm border border-gray-100 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-emerald-100 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <h3 class="text-lg font-bold text-gray-900">Tidak ada notifikasi</h3>
                        <p class="text-gray-500 text-sm mt-1">Anda sudah membaca semua pemberitahuan.</p>
                    </div>
                </div>
            @endforelse

            @if ($notifications->count() > 0) </div> @endif {{-- Close last group div --}}
            
            {{-- Pagination --}}
            <div class="pt-4">
                {{ $notifications->links('components.pagination.admin') }}
            </div>
        </div>
    </div>
@endsection