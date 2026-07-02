@extends('layouts.penyewa')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Tab Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Success/Error Message from Session -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- JS Alert Container -->
            <div id="alert-container" class="hidden mb-6">
                <div id="alert-message"></div>
            </div>

            <!-- TRANSAKSI TAB -->
            <div id="transaksi-tab" class="tab-content active">

                <!-- Dashboard Header Card -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-500 rounded-2xl p-6 mb-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold">Selamat Datang, {{ Auth::user()->name }}!</h2>
                                <p class="text-emerald-100 text-sm">Kelola penyewaan kamar kos Anda di sini</p>
                            </div>
                        </div>
                        <div class="hidden md:block text-right">
                            <p class="text-emerald-100 text-xs">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Incomplete Banner - HIGHEST PRIORITY, CANNOT BE DISMISSED -->
                @php
                    $isProfileComplete = isset($profile) && $profile->id && $profile->isComplete();
                    $hasRoom = isset($kamarDisewa) && $kamarDisewa;
                @endphp
                
                @if(!$isProfileComplete)
                    @if($hasRoom)
                    {{-- User with room but incomplete profile - Yellow/Orange (more urgent reminder) --}}
                    <div id="profile-incomplete-banner" class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-400 rounded-xl p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0 animate-pulse">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-yellow-800">⚠️ Profil Anda Belum Lengkap</p>
                                <p class="text-sm text-yellow-700 mt-1">Segera lengkapi data profil Anda untuk kelancaran administrasi dan komunikasi.</p>
                                @if(isset($profile) && $profile->id)
                                    @php $missingFields = $profile->getMissingFields(); @endphp
                                    @if(count($missingFields) > 0)
                                        <p class="text-xs text-yellow-600 mt-2">Data yang belum lengkap: <span class="font-semibold">{{ implode(', ', array_slice($missingFields, 0, 3)) }}{{ count($missingFields) > 3 ? ', ...' : '' }}</span></p>
                                    @endif
                                @endif
                                <a href="?tab=profil" class="inline-flex items-center mt-3 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold rounded-lg transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Lengkapi Profil Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- User without room - Blue (info/reminder) --}}
                    <div id="profile-incomplete-banner" class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-blue-800">📋 Lengkapi Profil Anda</p>
                                <p class="text-sm text-blue-700 mt-1">Lengkapi data profil untuk mempermudah proses pemesanan dan administrasi.</p>
                                @if(isset($profile) && $profile->id)
                                    @php $missingFields = $profile->getMissingFields(); @endphp
                                    @if(count($missingFields) > 0)
                                        <p class="text-xs text-blue-600 mt-2">Data yang belum lengkap: <span class="font-semibold">{{ implode(', ', array_slice($missingFields, 0, 3)) }}{{ count($missingFields) > 3 ? ', ...' : '' }}</span></p>
                                    @endif
                                @endif
                                <a href="?tab=profil" class="inline-flex items-center mt-3 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-lg transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Lengkapi Profil
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif

                <!-- Rejected Payment Banner - HIGHEST PRIORITY AFTER PROFILE -->
                @if(isset($transaksiDitolak) && $transaksiDitolak)
                    @php
                        $rejectionNote = $transaksiDitolak->status === 'rejected_by_admin' 
                            ? $transaksiDitolak->admin_notes 
                            : $transaksiDitolak->owner_notes;
                        $rejectedBy = $transaksiDitolak->status === 'rejected_by_admin' ? 'Admin' : 'Pemilik Kos';
                    @endphp
                    <div id="rejection-banner" class="bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-300 rounded-xl p-4 mb-6 animate-pulse">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-base font-bold text-red-800">⚠️ Pembayaran Anda Ditolak</p>
                                <p class="text-sm text-red-700 mt-1">
                                    Transaksi <span class="font-mono font-bold">{{ $transaksiDitolak->invoice_number }}</span> untuk kamar 
                                    <span class="font-bold">{{ $transaksiDitolak->room?->room_number ?? '-' }}</span> ditolak oleh {{ $rejectedBy }}.
                                </p>
                                @if($rejectionNote)
                                    <div class="mt-3 bg-red-100 border border-red-200 rounded-lg p-3">
                                        <p class="text-xs font-bold text-red-700 uppercase">Alasan Penolakan:</p>
                                        <p class="text-sm text-red-800 mt-1 italic">"{{ $rejectionNote }}"</p>
                                    </div>
                                @endif
                                <div class="flex flex-wrap gap-3 mt-4">
                                    <a href="{{ route('tenant.booking.retry', $transaksiDitolak->id) }}" class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition shadow-lg shadow-red-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        Upload Ulang Bukti
                                    </a>
                                    <form action="{{ route('tenant.booking.cancel', $transaksiDitolak->id) }}" method="POST" onsubmit="confirmSubmit(event, 'Apakah Anda yakin ingin membatalkan transaksi ini? Setelah dibatalkan, Anda dapat membuat transaksi baru.');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-lg transition">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Batalkan Transaksi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Notification Alert Banner (only show if profile is complete and no rejection) -->
                @if($isProfileComplete && !(isset($transaksiDitolak) && $transaksiDitolak))
                @php
                    $urgentNotif = isset($notifications) ? $notifications->where('category', 'urgent')->first() : null;
                    $rejectedNotif = isset($notifications) ? $notifications->where('type', 'payment_rejected')->first() : null;
                    $importantNotif = isset($notifications) ? $notifications->where('priority', 'high')->first() : null;
                    $showBanner = $rejectedNotif ?? $urgentNotif ?? $importantNotif;
                    $isRedBanner = $showBanner && ($showBanner->category === 'urgent' || $showBanner->type === 'payment_rejected');
                @endphp
                
                @if($showBanner)
                    <div id="notification-banner" class="bg-gradient-to-r {{ $isRedBanner ? 'from-red-50 to-orange-50 border-red-200' : 'from-blue-50 to-indigo-50 border-blue-200' }} border rounded-xl p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 {{ $isRedBanner ? 'bg-red-500' : 'bg-blue-500' }} rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold {{ $isRedBanner ? 'text-red-800' : 'text-blue-800' }}">{{ $isRedBanner ? '⚠️' : '📌' }} {{ $showBanner->title }}</p>
                                    @if($showBanner->isDismissible())
                                        <button onclick="dismissNotification({{ $showBanner->id }}); document.getElementById('notification-banner').style.display='none'" class="{{ $isRedBanner ? 'text-red-400 hover:text-red-600' : 'text-blue-400 hover:text-blue-600' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    @endif
                                </div>
                                <p class="text-sm {{ $isRedBanner ? 'text-red-700' : 'text-blue-700' }} mt-1">{{ $showBanner->message }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                @endif

                <!-- Lease Expiry Reminder Banner -->
                @if(isset($leaseReminder) && $leaseReminder)
                    <div class="mb-6 rounded-xl p-4 border-2 {{ $leaseReminder['type'] === 'overdue' ? 'bg-red-50 border-red-300' : ($leaseReminder['type'] === 'today' ? 'bg-orange-50 border-orange-300' : 'bg-yellow-50 border-yellow-300') }}">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 {{ $leaseReminder['type'] === 'overdue' ? 'bg-red-500' : ($leaseReminder['type'] === 'today' ? 'bg-orange-500' : 'bg-yellow-500') }} rounded-full flex items-center justify-center flex-shrink-0 animate-pulse">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-bold {{ $leaseReminder['type'] === 'overdue' ? 'text-red-800' : ($leaseReminder['type'] === 'today' ? 'text-orange-800' : 'text-yellow-800') }}">
                                    {{ $leaseReminder['title'] }}
                                </h3>
                                <p class="text-sm {{ $leaseReminder['type'] === 'overdue' ? 'text-red-700' : ($leaseReminder['type'] === 'today' ? 'text-orange-700' : 'text-yellow-700') }} mt-1">
                                    {{ $leaseReminder['message'] }}
                                </p>
                                <div class="flex items-center gap-4 mt-3">
                                    <div class="{{ $leaseReminder['type'] === 'overdue' ? 'bg-red-100' : ($leaseReminder['type'] === 'today' ? 'bg-orange-100' : 'bg-yellow-100') }} px-3 py-1.5 rounded-lg">
                                        <p class="text-xs {{ $leaseReminder['type'] === 'overdue' ? 'text-red-600' : ($leaseReminder['type'] === 'today' ? 'text-orange-600' : 'text-yellow-600') }} font-bold">
                                            Tagihan: Rp {{ number_format($pendingBill ?? 0, 0, ',', '.') }}
                                            @if(isset($monthsOverdue) && $monthsOverdue > 0)
                                                <span class="text-xs">({{ $monthsOverdue }} bulan)</span>
                                            @endif
                                        </p>
                                    </div>
                                    <a href="{{ route('tenant.extend-payment') }}" class="{{ $leaseReminder['type'] === 'overdue' ? 'bg-red-600 hover:bg-red-700' : ($leaseReminder['type'] === 'today' ? 'bg-orange-600 hover:bg-orange-700' : 'bg-yellow-600 hover:bg-yellow-700') }} text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                        Perpanjang Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-semibold">Kamar</p>
                                <p class="text-lg font-bold text-gray-800">{{ isset($kamarDisewa) && $kamarDisewa ? 1 : 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-semibold">Transaksi</p>
                                <p class="text-lg font-bold text-gray-800">{{ isset($transaksi) ? $transaksi->count() : 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-semibold">Notifikasi</p>
                                <p class="text-lg font-bold text-gray-800">{{ isset($notifications) ? $notifications->count() : 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm {{ isset($pendingBill) && $pendingBill > 0 ? 'ring-2 ring-red-400' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 {{ isset($pendingBill) && $pendingBill > 0 ? 'bg-red-100' : 'bg-purple-100' }} rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 {{ isset($pendingBill) && $pendingBill > 0 ? 'text-red-600' : 'text-purple-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-semibold">Tagihan</p>
                                <p class="text-lg font-bold {{ isset($pendingBill) && $pendingBill > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                    Rp {{ number_format($pendingBill ?? 0, 0, ',', '.') }}
                                </p>
                                @if(isset($monthsOverdue) && $monthsOverdue > 0)
                                    <p class="text-[10px] text-red-500 font-medium">{{ $monthsOverdue }} bulan telat bayar</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accordion Sections -->
                <div class="space-y-3">
                    
                    <!-- Kamar yang Disewa Accordion -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <button type="button" onclick="toggleAccordion('kamar')" class="w-full flex justify-between items-center p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                </div>
                                <span class="font-semibold text-gray-800">Kamar yang Disewa</span>
                            </div>
                            <svg id="kamar-arrow" class="w-5 h-5 text-gray-400 transform transition-transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div id="kamar-content" class="border-t border-gray-100">
                            <div class="p-6">
                                @if(isset($kamarDisewa) && $kamarDisewa)
                                    <!-- Kamar yang Disewa -->
                                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-4 border border-emerald-200">
                                        <div class="flex items-start gap-4">
                                            @if($kamarDisewa->roomType && $kamarDisewa->roomType->image_path)
                                                <img src="{{ Storage::url($kamarDisewa->roomType->image_path) }}" alt="{{ $kamarDisewa->roomType->name ?? 'Kamar' }}" class="w-24 h-24 object-cover rounded-lg shadow">
                                            @else
                                                <div class="w-24 h-24 bg-emerald-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h3 class="font-bold text-lg text-gray-800">{{ $kamarDisewa->roomType->name ?? 'Kamar' }}</h3>
                                                    <span class="bg-emerald-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">Aktif</span>
                                                </div>
                                                <p class="text-sm text-gray-500 mb-3">Nomor Kamar: <span class="font-semibold text-gray-700">{{ $kamarDisewa->room_number }}</span></p>
                                                
                                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                                                    <div class="bg-white rounded-lg px-3 py-2">
                                                        <p class="text-[10px] text-gray-400 uppercase">Lantai</p>
                                                        <p class="font-semibold text-gray-700">{{ $kamarDisewa->floor_number ?? '-' }}</p>
                                                    </div>
                                                    <div class="bg-white rounded-lg px-3 py-2">
                                                        <p class="text-[10px] text-gray-400 uppercase">Harga/Bulan</p>
                                                        <p class="font-semibold text-emerald-600">Rp {{ number_format($kamarDisewa->roomType->rent_per_person ?? 0, 0, ',', '.') }}</p>
                                                    </div>
                                                    <div class="bg-white rounded-lg px-3 py-2">
                                                        <p class="text-[10px] text-gray-400 uppercase">Kapasitas</p>
                                                        <p class="font-semibold text-gray-700">{{ $kamarDisewa->roomType->capacity ?? 1 }} Orang</p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Lease Period Info -->
                                                @if(isset($userLeaseStartDate) || isset($userLeaseEndDate))
                                                    @php
                                                        // Use strict user-specific dates from controller
                                                        $startDate = $userLeaseStartDate ?? null;
                                                        $endDate = $userLeaseEndDate ?? null;
                                                        
                                                        // Recalculate days remaining for display
                                                        $daysRemaining = $endDate ? (int) now()->diffInDays($endDate, false) : null;
                                                        $isExpiringSoon = $daysRemaining !== null && $daysRemaining <= 14 && $daysRemaining > 0;
                                                        $isExpired = $daysRemaining !== null && $daysRemaining <= 0;
                                                    @endphp
                                                    <div class="mt-3 {{ $isExpired ? 'bg-red-50 border-red-200' : ($isExpiringSoon ? 'bg-yellow-50 border-yellow-200' : 'bg-blue-50 border-blue-200') }} rounded-lg p-3 border">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <svg class="w-4 h-4 {{ $isExpired ? 'text-red-500' : ($isExpiringSoon ? 'text-yellow-600' : 'text-blue-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            <span class="text-xs font-bold {{ $isExpired ? 'text-red-700' : ($isExpiringSoon ? 'text-yellow-700' : 'text-blue-700') }} uppercase">Periode Sewa</span>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                                            <div>
                                                                <p class="text-[10px] text-gray-500 uppercase">Mulai</p>
                                                                <p class="font-semibold text-gray-700">{{ $startDate ? $startDate->format('d M Y') : '-' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-[10px] text-gray-500 uppercase">Sampai</p>
                                                                <p class="font-semibold {{ $isExpired ? 'text-red-600' : ($isExpiringSoon ? 'text-yellow-600' : 'text-gray-700') }}">{{ $endDate ? $endDate->format('d M Y') : '-' }}</p>
                                                            </div>
                                                        </div>
                                                        @if($daysRemaining !== null)
                                                            <div class="mt-2 text-xs">
                                                                @if($isExpired)
                                                                    <span class="text-red-600 font-semibold">⚠️ Sewa sudah berakhir. Segera perpanjang!</span>
                                                                @elseif($isExpiringSoon)
                                                                    <span class="text-yellow-700 font-semibold">⏰ Sisa {{ $daysRemaining }} hari lagi. Jangan lupa perpanjang!</span>
                                                                @else
                                                                    <span class="text-blue-600">✓ Sisa {{ $daysRemaining }} hari</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                <!-- Extend Payment Button -->
                                                <div class="mt-4 pt-3 border-t border-emerald-200">
                                                    <a href="{{ route('tenant.extend-payment') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition shadow-sm">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Perpanjang Sewa
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Empty State -->
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                        </div>
                                        @if(isset($transaksiDitolak) && $transaksiDitolak)
                                            <p class="text-red-600 text-sm mb-2 font-semibold">⚠️ Pembayaran Anda ditolak</p>
                                            <p class="text-gray-500 text-xs mb-4">Silakan upload ulang bukti pembayaran untuk kamar yang sudah dipesan.</p>
                                            <a href="{{ route('tenant.booking.retry', $transaksiDitolak->id) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                Upload Ulang Bukti Bayar
                                            </a>
                                        @else
                                            <p class="text-gray-500 text-sm mb-4">Anda belum memiliki kamar yang disewa</p>
                                            <a href="{{ url('/#kamar') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                Lihat Katalog
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Histori Transaksi Accordion -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <button type="button" onclick="toggleAccordion('histori')" class="w-full flex justify-between items-center p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                </div>
                                <span class="font-semibold text-gray-800">Histori Transaksi</span>
                            </div>
                            <svg id="histori-arrow" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div id="histori-content" class="hidden border-t border-gray-100">
                            <div class="p-6">
                                @if(isset($transaksi) && $transaksi->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left text-sm whitespace-nowrap">
                                            <thead class="bg-gray-50 text-gray-500">
                                                <tr>
                                                    <th class="px-4 py-3 font-medium uppercase text-xs tracking-wider">Invoice</th>
                                                    <th class="px-4 py-3 font-medium uppercase text-xs tracking-wider">Kamar</th>
                                                    <th class="px-4 py-3 font-medium uppercase text-xs tracking-wider">Periode</th>
                                                    <th class="px-4 py-3 font-medium uppercase text-xs tracking-wider">Total</th>
                                                    <th class="px-4 py-3 font-medium uppercase text-xs tracking-wider">Status</th>
                                                    <th class="px-4 py-3 font-medium uppercase text-xs tracking-wider text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($transaksi as $trx)
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-4 py-3">
                                                        <p class="font-mono text-gray-600 text-sm">#{{ $trx->invoice_number }}</p>
                                                        <p class="text-[10px] text-gray-400">{{ $trx->created_at->format('d M Y') }}</p>
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-800">
                                                        {{ $trx->room?->roomType?->name ?? 'Kamar' }} 
                                                        <span class="text-xs text-gray-400">({{ $trx->room?->room_number ?? '-' }})</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if($trx->period_start_date && $trx->period_end_date)
                                                            <p class="text-sm text-gray-700">{{ $trx->period_start_date->format('d M Y') }}</p>
                                                            <p class="text-xs text-gray-500">s/d {{ $trx->period_end_date->format('d M Y') }}</p>
                                                            <p class="text-[10px] text-emerald-600 font-semibold">{{ $trx->duration_months }} Bulan</p>
                                                        @else
                                                            <span class="text-xs text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 font-bold text-gray-800">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                                    <td class="px-4 py-3">
                                                        @php
                                                            // Simplified status for tenant - hide verification stages
                                                            $statusLabel = match($trx->status) {
                                                                'verified_by_owner' => 'Lunas',
                                                                'verified_by_admin', 'pending_verification', 'pending' => 'Diproses',
                                                                'rejected_by_admin', 'rejected_by_owner' => 'Ditolak',
                                                                default => $trx->status
                                                            };
                                                            $statusClass = match($trx->status) {
                                                                'verified_by_owner' => 'bg-emerald-100 text-emerald-700',
                                                                'verified_by_admin', 'pending_verification', 'pending' => 'bg-yellow-100 text-yellow-700',
                                                                'rejected_by_admin', 'rejected_by_owner' => 'bg-red-100 text-red-700',
                                                                default => 'bg-gray-100 text-gray-700'
                                                            };
                                                        @endphp
                                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusClass }}">
                                                            {{ $statusLabel }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <a href="{{ route('tenant.transaction.receipt', $trx->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Download Nota">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                            </a>
                                                            @if(in_array($trx->status, ['rejected_by_admin', 'rejected_by_owner']))
                                                                <a href="{{ route('tenant.booking.retry', $trx->id) }}" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Upload Ulang">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <!-- Empty State -->
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </div>
                                        <p class="text-gray-500 text-sm">Belum ada riwayat transaksi</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Notifikasi Accordion -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <button type="button" onclick="toggleAccordion('notif')" class="w-full flex justify-between items-center p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                </div>
                                <span class="font-semibold text-gray-800">Semua Notifikasi</span>
                                @if(isset($notifications) && $notifications->count() > 0)
                                    <span class="bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $notifications->count() }}</span>
                                @endif
                            </div>
                            <svg id="notif-arrow" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div id="notif-content" class="hidden border-t border-gray-100">
                            <div class="p-4 space-y-3">
                                @if(isset($notifications) && $notifications->count() > 0)
                                    @foreach($notifications as $notif)
                                        <div id="notif-{{ $notif->id }}" class="flex items-start gap-3 p-3 rounded-lg border-l-4 
                                            @if($notif->type === 'payment_rejected') bg-red-50 border-red-500
                                            @elseif($notif->category === 'urgent') bg-red-50 border-red-500
                                            @elseif($notif->category === 'finance') bg-yellow-50 border-yellow-500
                                            @else bg-blue-50 border-blue-500 @endif">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                                @if($notif->type === 'payment_rejected') bg-red-100
                                                @elseif($notif->category === 'urgent') bg-red-100
                                                @elseif($notif->category === 'finance') bg-yellow-100
                                                @else bg-blue-100 @endif">
                                                @if($notif->type === 'tenant_overdue' || $notif->type === 'payment_rejected')
                                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @else
                                                    <svg class="w-4 h-4 @if($notif->category === 'finance') text-yellow-600 @else text-blue-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium @if($notif->type === 'payment_rejected') text-red-800 @elseif($notif->category === 'urgent') text-red-800 @elseif($notif->category === 'finance') text-yellow-800 @else text-blue-800 @endif">{{ $notif->title }}</p>
                                                <p class="text-xs mt-0.5 @if($notif->type === 'payment_rejected') text-red-600 @elseif($notif->category === 'urgent') text-red-600 @elseif($notif->category === 'finance') text-yellow-600 @else text-blue-600 @endif">{{ $notif->message }}</p>
                                                <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                                
                                                {{-- Action button for rejected payment --}}
                                                @if($notif->type === 'payment_rejected' && $notif->related_entity_id)
                                                    <a href="{{ route('tenant.booking.retry', $notif->related_entity_id) }}" class="inline-flex items-center gap-1 mt-2 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                        Upload Ulang Bukti Bayar
                                                    </a>
                                                @endif
                                            </div>
                                            @if($notif->isDismissible())
                                                <button onclick="dismissNotification({{ $notif->id }})" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            @else
                                                <span class="text-[10px] text-red-500 font-medium flex-shrink-0">🔒</span>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-6">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        </div>
                                        <p class="text-gray-500 text-sm">Tidak ada notifikasi</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- PROFIL TAB -->
            <div id="profil-tab" class="tab-content hidden">
                
                <!-- Profile Header Card -->
                <div class="bg-gradient-to-r from-emerald-600 to-teal-500 rounded-2xl p-6 mb-6 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold backdrop-blur-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">{{ Auth::user()->name }}</h2>
                            @if(($profile->tenant_type ?? 'mahasiswa') === 'mahasiswa')
                                <p class="text-emerald-100 text-sm">🎓 {{ $profile->university ?? 'Mahasiswa' }} • {{ $profile->major ?? '-' }}</p>
                            @else
                                <p class="text-emerald-100 text-sm">💼 {{ $profile->occupation ?? 'Pekerja' }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Accordion Sections -->
                <div class="space-y-3">
                    
                    <!-- Data Personal Accordion -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <button type="button" onclick="toggleAccordion('personal')" class="w-full flex justify-between items-center p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <span class="font-semibold text-gray-800">Data Personal</span>
                            </div>
                            <svg id="personal-arrow" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div id="personal-content" class="hidden border-t border-gray-100">
                            <div class="p-4">
                                <div class="flex justify-end mb-3">
                                    <button type="button" onclick="toggleEdit('personal')" id="personal-edit-btn" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition">Edit</button>
                                </div>
                                
                                <div id="personal-view">
                                    <!-- Tenant Type Badge -->
                                    <div class="mb-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ ($profile->tenant_type ?? 'mahasiswa') === 'mahasiswa' ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-100 text-purple-700' }}">
                                            {{ ($profile->tenant_type ?? 'mahasiswa') === 'mahasiswa' ? '🎓 Mahasiswa' : '💼 Non-Mahasiswa' }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Nama</p>
                                            <p class="text-sm text-gray-800 font-medium truncate">{{ Auth::user()->name }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">TTL</p>
                                            <p class="text-sm text-gray-800 font-medium truncate">{{ $profile->birth_place ?? '-' }}{{ $profile->birth_date ? ', ' . \Carbon\Carbon::parse($profile->birth_date)->format('d/m/Y') : '' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">No. KTP</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $profile->id_card_number ?? '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">No. HP</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $profile->phone ?? '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Email</p>
                                            <p class="text-sm text-gray-800 font-medium truncate">{{ Auth::user()->email }}</p>
                                        </div>
                                        
                                        @if(($profile->tenant_type ?? 'mahasiswa') === 'mahasiswa')
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Universitas</p>
                                                <p class="text-sm text-gray-800 font-medium truncate">{{ $profile->university ?? '-' }}</p>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Fakultas</p>
                                                <p class="text-sm text-gray-800 font-medium truncate">{{ $profile->faculty ?? '-' }}</p>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Jurusan</p>
                                                <p class="text-sm text-gray-800 font-medium truncate">{{ $profile->major ?? '-' }}</p>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Tahun Masuk</p>
                                                <p class="text-sm text-gray-800 font-medium">{{ $profile->enrollment_year ?? '-' }}</p>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">No. KTM</p>
                                                <p class="text-sm text-gray-800 font-medium">{{ $profile->student_card_number ?? '-' }}</p>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 rounded-lg p-3">
                                                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Pekerjaan</p>
                                                <p class="text-sm text-gray-800 font-medium truncate">{{ $profile->occupation ?? '-' }}</p>
                                            </div>
                                        @endif
                                        
                                        <div class="bg-gray-50 rounded-lg p-3 md:col-span-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Alamat</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $profile->address ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <form id="personal-edit" class="hidden" onsubmit="submitPersonalForm(event)">
                                    @csrf
                                    
                                    <!-- Tenant Type Toggle -->
                                    <div class="mb-4">
                                        <label class="text-xs font-semibold text-gray-600 block mb-2">Tipe Penyewa <span class="text-red-500">*</span></label>
                                        <div class="flex gap-2">
                                            <label class="flex-1 cursor-pointer">
                                                <input type="radio" name="tenant_type" value="mahasiswa" class="hidden peer" {{ ($profile->tenant_type ?? 'mahasiswa') === 'mahasiswa' ? 'checked' : '' }} onchange="toggleTenantTypeFields()">
                                                <div class="p-3 border-2 rounded-lg text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition">
                                                    <span class="text-lg">🎓</span>
                                                    <p class="text-sm font-semibold text-gray-700">Mahasiswa</p>
                                                </div>
                                            </label>
                                            <label class="flex-1 cursor-pointer">
                                                <input type="radio" name="tenant_type" value="non_mahasiswa" class="hidden peer" {{ ($profile->tenant_type ?? 'mahasiswa') === 'non_mahasiswa' ? 'checked' : '' }} onchange="toggleTenantTypeFields()">
                                                <div class="p-3 border-2 rounded-lg text-center peer-checked:border-purple-500 peer-checked:bg-purple-50 transition">
                                                    <span class="text-lg">💼</span>
                                                    <p class="text-sm font-semibold text-gray-700">Non-Mahasiswa</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Common Fields -->
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">Nama <span class="text-red-500">*</span></label>
                                            <input type="text" name="name" id="input-name" value="{{ Auth::user()->name }}" placeholder="Nama" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" required>
                                            <p id="err-name" class="text-[10px] text-red-600 mt-1 hidden">Nama tidak boleh mengandung angka.</p>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">Tempat Lahir <span class="text-red-500">*</span></label>
                                            <input type="text" name="birth_place" id="input-birth_place" value="{{ $profile->birth_place ?? '' }}" placeholder="Tempat Lahir" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" required>
                                            <p id="err-birth_place" class="text-[10px] text-red-600 mt-1 hidden">Tempat lahir tidak boleh mengandung angka.</p>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">Tanggal Lahir <span class="text-red-500">*</span></label>
                                            <input type="date" name="birth_date" value="{{ $profile->birth_date ?? '' }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" required>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">No. KTP <span class="text-red-500">*</span></label>
                                            <input type="text" name="id_card_number" value="{{ $profile->id_card_number ?? '' }}" placeholder="16 digit NIK" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" pattern="[0-9]{16}" maxlength="16" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">No. HP <span class="text-red-500">*</span></label>
                                            <input type="tel" name="phone" value="{{ $profile->phone ?? '' }}" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" pattern="[0-9]{10,15}" maxlength="15" inputmode="tel" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">Email</label>
                                            <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed" readonly disabled>
                                            <p class="text-[9px] text-gray-400 mt-1">Email tidak dapat diubah di sini</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Mahasiswa Fields -->
                                    <div id="mahasiswa-fields" class="{{ ($profile->tenant_type ?? 'mahasiswa') === 'non_mahasiswa' ? 'hidden' : '' }}">
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
                                            <div>
                                                <label class="text-[10px] font-semibold text-gray-500 uppercase">Universitas <span class="text-red-500">*</span></label>
                                                <input type="text" name="university" id="input-university" value="{{ $profile->university ?? '' }}" placeholder="Universitas" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                                <p id="err-university" class="text-[10px] text-red-600 mt-1 hidden">Universitas tidak boleh mengandung angka.</p>
                                            </div>
                                            <div>
                                                <label class="text-[10px] font-semibold text-gray-500 uppercase">Fakultas <span class="text-red-500">*</span></label>
                                                <input type="text" name="faculty" id="input-faculty" value="{{ $profile->faculty ?? '' }}" placeholder="Fakultas" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                                <p id="err-faculty" class="text-[10px] text-red-600 mt-1 hidden">Fakultas tidak boleh mengandung angka.</p>
                                            </div>
                                            <div>
                                                <label class="text-[10px] font-semibold text-gray-500 uppercase">Jurusan <span class="text-red-500">*</span></label>
                                                <input type="text" name="major" id="input-major" value="{{ $profile->major ?? '' }}" placeholder="Jurusan" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                                <p id="err-major" class="text-[10px] text-red-600 mt-1 hidden">Jurusan tidak boleh mengandung angka.</p>
                                            </div>
                                            <div>
                                                <label class="text-[10px] font-semibold text-gray-500 uppercase">Tahun Masuk <span class="text-red-500">*</span></label>
                                                <input type="text" name="enrollment_year" value="{{ $profile->enrollment_year ?? '' }}" placeholder="2020" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" pattern="[0-9]{4}" maxlength="4" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            </div>
                                            <div>
                                                <label class="text-[10px] font-semibold text-gray-500 uppercase">No. KTM <span class="text-red-500">*</span></label>
                                                <input type="text" name="student_card_number" value="{{ $profile->student_card_number ?? '' }}" placeholder="Nomor Kartu Mahasiswa" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" maxlength="20" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Non-Mahasiswa Fields -->
                                    <div id="non-mahasiswa-fields" class="{{ ($profile->tenant_type ?? 'mahasiswa') === 'mahasiswa' ? 'hidden' : '' }}">
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
                                            <div>
                                                <label class="text-[10px] font-semibold text-gray-500 uppercase">Pekerjaan <span class="text-red-500">*</span></label>
                                                <input type="text" name="occupation" id="input-occupation" value="{{ $profile->occupation ?? '' }}" placeholder="Pekerjaan" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500">
                                                <p id="err-occupation" class="text-[10px] text-red-600 mt-1 hidden">Pekerjaan tidak boleh mengandung angka.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Address -->
                                    <div class="mb-4">
                                        <label class="text-[10px] font-semibold text-gray-500 uppercase">Alamat <span class="text-red-500">*</span></label>
                                        <textarea name="address" placeholder="Alamat Lengkap" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" rows="2" required>{{ $profile->address ?? '' }}</textarea>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <button type="submit" id="personal-submit-btn" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition">Simpan</button>
                                        <button type="button" onclick="toggleEdit('personal')" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Data Wali Accordion -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <button type="button" onclick="toggleAccordion('wali')" class="w-full flex justify-between items-center p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <span class="font-semibold text-gray-800">Data Wali</span>
                            </div>
                            <svg id="wali-arrow" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div id="wali-content" class="hidden border-t border-gray-100">
                            <div class="p-4">
                                <div class="flex justify-end mb-3">
                                    <button type="button" onclick="toggleEdit('wali')" id="wali-edit-btn" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition">Edit</button>
                                </div>
                                
                                <div id="wali-view">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Nama Wali</p>
                                            <p class="text-sm text-gray-800 font-medium truncate">{{ $profile->guardian_name ?? '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">TTL</p>
                                            <p class="text-sm text-gray-800 font-medium truncate">{{ $profile->guardian_birth_place ?? '-' }}{{ $profile->guardian_birth_date ? ', ' . \Carbon\Carbon::parse($profile->guardian_birth_date)->format('d/m/Y') : '' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Pekerjaan</p>
                                            <p class="text-sm text-gray-800 font-medium truncate">{{ $profile->guardian_occupation ?? '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">No. KTP</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $profile->guardian_id_card_number ?? '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">No. Telp Rumah</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $profile->guardian_home_phone ?? '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">No. HP</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $profile->guardian_phone ?? '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-3 md:col-span-3">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Alamat</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $profile->guardian_address ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <form id="wali-edit" class="hidden" onsubmit="submitGuardianForm(event)">
                                    @csrf
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">Nama Wali <span class="text-red-500">*</span></label>
                                            <input type="text" name="guardian_name" value="{{ $profile->guardian_name ?? '' }}" placeholder="Nama Wali" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" required>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">Tempat Lahir <span class="text-red-500">*</span></label>
                                            <input type="text" name="guardian_birth_place" value="{{ $profile->guardian_birth_place ?? '' }}" placeholder="Tempat Lahir" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" required>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">Tanggal Lahir <span class="text-red-500">*</span></label>
                                            <input type="date" name="guardian_birth_date" value="{{ $profile->guardian_birth_date ?? '' }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" required>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">Pekerjaan <span class="text-red-500">*</span></label>
                                            <input type="text" name="guardian_occupation" value="{{ $profile->guardian_occupation ?? '' }}" placeholder="Pekerjaan" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" required>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">No. KTP <span class="text-red-500">*</span></label>
                                            <input type="text" name="guardian_id_card_number" value="{{ $profile->guardian_id_card_number ?? '' }}" placeholder="16 digit NIK" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" pattern="[0-9]{16}" maxlength="16" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">No. Telp Rumah</label>
                                            <input type="tel" name="guardian_home_phone" value="{{ $profile->guardian_home_phone ?? '' }}" placeholder="021xxxxxxx" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" pattern="[0-9]{7,15}" maxlength="15" inputmode="tel" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-semibold text-gray-500 uppercase">No. HP <span class="text-red-500">*</span></label>
                                            <input type="tel" name="guardian_phone" value="{{ $profile->guardian_phone ?? '' }}" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" pattern="[0-9]{10,15}" maxlength="15" inputmode="tel" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-[10px] font-semibold text-gray-500 uppercase">Alamat <span class="text-red-500">*</span></label>
                                        <textarea name="guardian_address" placeholder="Alamat Lengkap" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500" rows="2" required>{{ $profile->guardian_address ?? '' }}</textarea>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" id="wali-submit-btn" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition">Simpan</button>
                                        <button type="button" onclick="toggleEdit('wali')" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Lampiran Accordion -->
                    @php $documents = $profile->documents ?? []; @endphp
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <button type="button" onclick="toggleAccordion('lampiran')" class="w-full flex justify-between items-center p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <span class="font-semibold text-gray-800">Lampiran Dokumen</span>
                            </div>
                            <svg id="lampiran-arrow" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div id="lampiran-content" class="hidden border-t border-gray-100">
                            <div class="p-4">
                                <div class="flex justify-end mb-3">
                                    <button type="button" onclick="toggleEdit('lampiran')" id="lampiran-edit-btn" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition">Edit</button>
                                </div>
                                
                                <div id="lampiran-view">
                                    @php
                                        $docTypes = [
                                            'ktp' => ['label' => 'KTP', 'required' => true, 'icon' => '🪪'],
                                            'kartu_mahasiswa' => ['label' => 'Kartu Mahasiswa', 'required' => ($profile->tenant_type ?? 'mahasiswa') === 'mahasiswa', 'icon' => '🎓'], 
                                            'ktp_ortu' => ['label' => 'KTP Orang Tua', 'required' => true, 'icon' => '👨‍👩‍👧'],
                                            'kartu_keluarga' => ['label' => 'Kartu Keluarga', 'required' => true, 'icon' => '👨‍👩‍👧‍👦'],
                                            'pas_foto' => ['label' => 'Pas Foto 3x4', 'required' => true, 'icon' => '📷'],
                                            'surat_pernyataan' => ['label' => 'Surat Pernyataan', 'required' => true, 'icon' => '📄']
                                        ];
                                    @endphp
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($docTypes as $key => $doc)
                                        <div class="flex items-center gap-3 p-3 rounded-xl border {{ isset($documents[$key]) ? 'bg-emerald-50/50 border-emerald-200' : ($doc['required'] ? 'bg-red-50/30 border-red-200' : 'bg-gray-50 border-gray-200') }} transition hover:shadow-sm">
                                            {{-- Small Thumbnail or Placeholder --}}
                                            @if(isset($documents[$key]))
                                                @php
                                                    $filePath = $documents[$key];
                                                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    $docUrl = route('tenant-document.view', ['user' => auth()->id(), 'type' => $key]);
                                                @endphp
                                                <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 cursor-pointer border-2 border-emerald-300 hover:border-emerald-500 transition group" onclick="openDocumentModal('{{ $docUrl }}', '{{ $doc['label'] }}')">
                                                    @if($isImage)
                                                        <img src="{{ $docUrl }}" alt="{{ $doc['label'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                                    @else
                                                        <div class="w-full h-full bg-red-100 flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4z"/></svg>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="w-12 h-12 rounded-lg flex-shrink-0 {{ $doc['required'] ? 'bg-red-100' : 'bg-gray-200' }} flex items-center justify-center">
                                                    <span class="text-lg opacity-60">{{ $doc['icon'] }}</span>
                                                </div>
                                            @endif
                                            
                                            {{-- Label and Status --}}
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">
                                                    {{ $doc['label'] }}
                                                    @if($doc['required'] && !isset($documents[$key]))<span class="text-red-500">*</span>@endif
                                                </p>
                                                @if(isset($documents[$key]))
                                                    <p class="text-[10px] text-emerald-600 font-medium flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Klik gambar untuk melihat
                                                    </p>
                                                @else
                                                    <p class="text-[10px] {{ $doc['required'] ? 'text-red-400' : 'text-gray-400' }}">
                                                        {{ $doc['required'] ? 'Wajib diupload' : 'Opsional' }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <form id="lampiran-edit" class="hidden" onsubmit="submitDocumentsForm(event)" enctype="multipart/form-data">
                                    @csrf
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($docTypes as $key => $doc)
                                        <div class="border-2 border-dashed {{ isset($documents[$key]) ? 'border-emerald-300 bg-emerald-50' : ($doc['required'] ? 'border-red-200' : 'border-gray-200') }} rounded-lg p-3 text-center cursor-pointer hover:border-emerald-500 transition file-upload-area" data-input="{{ $key }}">
                                            <input type="file" name="{{ $key }}" id="file-{{ $key }}" class="hidden" accept="image/*,application/pdf">
                                            <p class="text-xs font-medium text-gray-700">{{ $doc['label'] }} @if($doc['required'] && !isset($documents[$key]))<span class="text-red-500">*</span>@endif</p>
                                            @if(isset($documents[$key]))
                                                <p class="text-[10px] text-emerald-600 mt-1 file-name">✓ Sudah diupload</p>
                                            @else
                                                <p class="text-[10px] {{ $doc['required'] ? 'text-red-400' : 'text-gray-400' }} mt-1 file-name">{{ $doc['required'] ? 'Wajib upload' : 'Klik untuk upload' }}</p>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="flex gap-2 mt-4">
                                        <button type="submit" id="lampiran-submit-btn" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition">Simpan</button>
                                        <button type="button" onclick="toggleEdit('lampiran')" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        function toggleAccordion(section) {
            const content = document.getElementById(section + '-content');
            const arrow = document.getElementById(section + '-arrow');
            
            if (content) {
                content.classList.toggle('hidden');
                if (arrow) {
                    arrow.classList.toggle('rotate-180');
                }
            }
        }

        function toggleTenantTypeFields() {
            const tenantType = document.querySelector('input[name="tenant_type"]:checked')?.value || 'mahasiswa';
            const mahasiswaFields = document.getElementById('mahasiswa-fields');
            const nonMahasiswaFields = document.getElementById('non-mahasiswa-fields');
            
            if (tenantType === 'mahasiswa') {
                mahasiswaFields.classList.remove('hidden');
                nonMahasiswaFields.classList.add('hidden');
            } else {
                mahasiswaFields.classList.add('hidden');
                nonMahasiswaFields.classList.remove('hidden');
            }
        }

        async function dismissNotification(id) {
            try {
                const response = await fetch(`/tenant/notification/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Remove notification element with animation
                    const notifEl = document.getElementById('notif-' + id);
                    if (notifEl) {
                        notifEl.style.transition = 'all 0.3s ease';
                        notifEl.style.opacity = '0';
                        notifEl.style.transform = 'translateX(20px)';
                        setTimeout(() => notifEl.remove(), 300);
                    }
                    // Update count badge
                    updateNotifCount();
                } else {
                    showAlert(data.message || 'Gagal menghapus notifikasi', false);
                }
            } catch (error) {
                showAlert('Terjadi kesalahan: ' + error.message, false);
            }
        }

        function updateNotifCount() {
            const notifItems = document.querySelectorAll('[id^="notif-"]:not([style*="display: none"])');
            const badge = document.querySelector('.bg-orange-500.text-white');
            if (badge) {
                const count = notifItems.length - 1; // subtract 1 for removed item
                if (count > 0) {
                    badge.textContent = count;
                } else {
                    badge.remove();
                }
            }
        }

        function switchTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            // Remove active state from all navbar buttons
            document.querySelectorAll('.nav-tab-btn').forEach(el => {
                el.classList.remove('text-emerald-600');
                el.classList.add('text-gray-700');
                // Remove persistent underline
                el.querySelector('span').classList.remove('w-full');
                el.querySelector('span').classList.add('w-0');
            });

            // Show selected tab
            document.getElementById(tab + '-tab').classList.remove('hidden');
            
            // Add active state to selected button
            const activeBtn = document.querySelector(`[data-tab="${tab}"]`);
            activeBtn.classList.remove('text-gray-700');
            activeBtn.classList.add('text-emerald-600');
            // Add persistent underline
            activeBtn.querySelector('span').classList.remove('w-0');
            activeBtn.querySelector('span').classList.add('w-full');
            
            // Update URL without page reload (for persistence)
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        }

        function toggleEdit(section) {
            const viewEl = document.getElementById(section + '-view');
            const editEl = document.getElementById(section + '-edit');
            const editBtn = document.getElementById(section + '-edit-btn');
            
            if (viewEl && editEl) {
                viewEl.classList.toggle('hidden');
                editEl.classList.toggle('hidden');
                
                // Toggle button text
                if (editBtn) {
                    editBtn.textContent = editEl.classList.contains('hidden') ? 'Edit' : 'Batal';
                }
            }
        }

        function showAlert(message, isSuccess = true) {
            const container = document.getElementById('alert-container');
            const alertDiv = document.getElementById('alert-message');
            
            container.classList.remove('hidden');
            alertDiv.className = 'p-4 rounded-lg ' + (isSuccess 
                ? 'bg-green-100 border border-green-400 text-green-700' 
                : 'bg-red-100 border border-red-400 text-red-700');
            alertDiv.innerHTML = message;
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                container.classList.add('hidden');
            }, 5000);
        }

        // Helper function to update personal view without reload
        function updatePersonalView(formData, profile) {
            const viewEl = document.getElementById('personal-view');
            if (!viewEl) return;
            
            // Update the view fields with new data
            const fields = viewEl.querySelectorAll('.bg-gray-50');
            fields.forEach(field => {
                const label = field.querySelector('.uppercase')?.textContent?.trim().toLowerCase();
                const valueEl = field.querySelector('.text-gray-800');
                if (!valueEl) return;
                
                // Map label to form field
                const mappings = {
                    'nama': formData.get('name'),
                    'no. hp': formData.get('phone'),
                    'no. ktp': formData.get('id_card_number'),
                    'universitas': formData.get('university'),
                    'fakultas': formData.get('faculty'),
                    'jurusan': formData.get('major'),
                    'tahun masuk': formData.get('enrollment_year'),
                    'no. ktm': formData.get('student_card_number'),
                    'pekerjaan': formData.get('occupation'),
                    'alamat': formData.get('address'),
                };
                
                Object.keys(mappings).forEach(key => {
                    if (label && label.includes(key) && mappings[key]) {
                        valueEl.textContent = mappings[key];
                    }
                });
                
                // Handle TTL specially
                if (label && label.includes('ttl')) {
                    const birthPlace = formData.get('birth_place');
                    const birthDate = formData.get('birth_date');
                    if (birthPlace && birthDate) {
                        const date = new Date(birthDate);
                        const formatted = date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
                        valueEl.textContent = `${birthPlace}, ${formatted}`;
                    }
                }
            });
            
            // Update tenant type badge
            const tenantType = formData.get('tenant_type');
            const badge = viewEl.querySelector('.rounded-full');
            if (badge) {
                if (tenantType === 'mahasiswa') {
                    badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700';
                    badge.textContent = '🎓 Mahasiswa';
                } else {
                    badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700';
                    badge.textContent = '💼 Non-Mahasiswa';
                }
            }
            
            // Update header card
            const headerSubtext = document.querySelector('.bg-gradient-to-r .text-emerald-100');
            if (headerSubtext && profile) {
                if (tenantType === 'mahasiswa') {
                    headerSubtext.textContent = `🎓 ${profile.university || 'Mahasiswa'} • ${profile.major || '-'}`;
                } else {
                    headerSubtext.textContent = `💼 ${profile.occupation || 'Pekerja'}`;
                }
            }
        }

        // Helper function to update guardian view without reload
        function updateGuardianView(formData, profile) {
            const viewEl = document.getElementById('wali-view');
            if (!viewEl) return;
            
            const fields = viewEl.querySelectorAll('.bg-gray-50');
            fields.forEach(field => {
                const label = field.querySelector('.uppercase')?.textContent?.trim().toLowerCase();
                const valueEl = field.querySelector('.text-gray-800');
                if (!valueEl) return;
                
                const mappings = {
                    'nama wali': formData.get('guardian_name'),
                    'pekerjaan': formData.get('guardian_occupation'),
                    'no. ktp': formData.get('guardian_id_card_number'),
                    'no. telp rumah': formData.get('guardian_home_phone'),
                    'no. hp': formData.get('guardian_phone'),
                    'alamat': formData.get('guardian_address'),
                };
                
                Object.keys(mappings).forEach(key => {
                    if (label && label.includes(key) && mappings[key]) {
                        valueEl.textContent = mappings[key];
                    }
                });
                
                // Handle TTL specially
                if (label && label.includes('ttl')) {
                    const birthPlace = formData.get('guardian_birth_place');
                    const birthDate = formData.get('guardian_birth_date');
                    if (birthPlace && birthDate) {
                        const date = new Date(birthDate);
                        const formatted = date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
                        valueEl.textContent = `${birthPlace}, ${formatted}`;
                    }
                }
            });
        }

        // Helper function to update documents view - reload page staying on profil tab
        function updateDocumentsView(documents) {
            // Redirect to profil tab after saving documents
            setTimeout(() => {
                window.location.href = window.location.pathname + '?tab=profil';
            }, 500);
        }

        async function submitPersonalForm(event) {
            event.preventDefault();
            const form = document.getElementById('personal-edit');
            const submitBtn = document.getElementById('personal-submit-btn');
            const formData = new FormData(form);
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>Menyimpan...';
            
            try {
                const response = await fetch('{{ route("tenant.profile.personal") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message, true);
                    // Update view without reloading
                    updatePersonalView(formData, data.profile);
                    toggleEdit('personal');
                } else {
                    showAlert(data.message || 'Terjadi kesalahan', false);
                }
            } catch (error) {
                showAlert('Terjadi kesalahan: ' + error.message, false);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan';
            }
        }

        async function submitGuardianForm(event) {
            event.preventDefault();
            const form = document.getElementById('wali-edit');
            const submitBtn = document.getElementById('wali-submit-btn');
            const formData = new FormData(form);
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>Menyimpan...';
            
            try {
                const response = await fetch('{{ route("tenant.profile.guardian") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message, true);
                    // Update view without reloading
                    updateGuardianView(formData, data.profile);
                    toggleEdit('wali');
                } else {
                    showAlert(data.message || 'Terjadi kesalahan', false);
                }
            } catch (error) {
                showAlert('Terjadi kesalahan: ' + error.message, false);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan';
            }
        }

        async function submitDocumentsForm(event) {
            event.preventDefault();
            const form = document.getElementById('lampiran-edit');
            const submitBtn = document.getElementById('lampiran-submit-btn');
            const formData = new FormData(form);
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>Mengupload...';
            
            try {
                const response = await fetch('{{ route("tenant.profile.documents") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message, true);
                    // Update view without reloading
                    updateDocumentsView(data.documents);
                    toggleEdit('lampiran');
                } else {
                    showAlert(data.message || 'Terjadi kesalahan', false);
                }
            } catch (error) {
                showAlert('Terjadi kesalahan: ' + error.message, false);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan';
            }
        }

        // Make file upload areas clickable and show filename
        document.querySelectorAll('.file-upload-area').forEach(el => {
            const inputName = el.dataset.input;
            const fileInput = document.getElementById('file-' + inputName);
            
            el.addEventListener('click', function(e) {
                if (e.target !== fileInput) {
                    fileInput.click();
                }
            });
            
            fileInput.addEventListener('change', function() {
                const fileNameEl = el.querySelector('.file-name');
                if (this.files.length > 0) {
                    fileNameEl.textContent = this.files[0].name;
                    el.classList.add('border-emerald-600', 'bg-emerald-50');
                    el.classList.remove('border-gray-300');
                } else {
                    fileNameEl.textContent = 'Click untuk upload';
                    el.classList.remove('border-emerald-600', 'bg-emerald-50');
                    el.classList.add('border-gray-300');
                }
            });
        });

        // Initialize - set active tab on page load based on URL parameter
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || 'transaksi';
            switchTab(tab);
        });

        // Document Lightbox Modal Functions
        function openDocumentModal(src, title) {
            const modal = document.getElementById('documentModal');
            const modalImage = document.getElementById('modalDocumentImage');
            const modalTitle = document.getElementById('modalDocumentTitle');
            const modalFrame = document.getElementById('modalDocumentFrame');
            const downloadBtn = document.getElementById('modalDownloadBtn');
            
            // Check if it's a PDF
            const isPDF = src.toLowerCase().endsWith('.pdf');
            
            if (isPDF) {
                modalImage.classList.add('hidden');
                modalFrame.classList.remove('hidden');
                modalFrame.src = src;
            } else {
                modalFrame.classList.add('hidden');
                modalImage.classList.remove('hidden');
                modalImage.src = src;
                modalImage.alt = title;
            }
            
            modalTitle.textContent = title;
            downloadBtn.href = src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Trigger animation
            setTimeout(() => {
                modal.querySelector('.modal-content').classList.remove('scale-95', 'opacity-0');
                modal.querySelector('.modal-content').classList.add('scale-100', 'opacity-100');
            }, 10);
            
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        function closeDocumentModal() {
            const modal = document.getElementById('documentModal');
            const modalContent = modal.querySelector('.modal-content');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.getElementById('modalDocumentImage').src = '';
                document.getElementById('modalDocumentFrame').src = '';
                // Restore body scroll
                document.body.style.overflow = '';
            }, 200);
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDocumentModal();
            }
        });

        // Profil: field yang hanya boleh diisi huruf (tidak boleh angka)
        const textOnlyFields = [
            { id: 'input-name',        errId: 'err-name' },
            { id: 'input-birth_place', errId: 'err-birth_place' },
            { id: 'input-university',  errId: 'err-university' },
            { id: 'input-faculty',     errId: 'err-faculty' },
            { id: 'input-major',       errId: 'err-major' },
            { id: 'input-occupation',  errId: 'err-occupation' },
        ];

        textOnlyFields.forEach(({ id, errId }) => {
            const el  = document.getElementById(id);
            const err = document.getElementById(errId);
            if (!el || !err) return;

            el.addEventListener('keydown', e => {
                if (e.key >= '0' && e.key <= '9') e.preventDefault();
            });

            el.addEventListener('input', () => {
                const cleaned  = el.value.replace(/[0-9]/g, '');
                const hasDigit = el.value !== cleaned;
                el.value = cleaned;
                err.classList.toggle('hidden', !hasDigit);
            });

            el.addEventListener('blur', () => err.classList.add('hidden'));
        });
    </script>

    <!-- Document Lightbox Modal -->
    <div id="documentModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80 backdrop-blur-sm" onclick="if(event.target === this) closeDocumentModal()">
        <div class="modal-content relative max-w-4xl w-full max-h-[90vh] bg-white rounded-2xl shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                <h3 id="modalDocumentTitle" class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Dokumen
                </h3>
                <button onclick="closeDocumentModal()" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-red-100 flex items-center justify-center transition-colors group">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="p-4 overflow-auto max-h-[calc(90vh-80px)] bg-gray-50 flex items-center justify-center">
                <img id="modalDocumentImage" src="" alt="" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-lg">
                <iframe id="modalDocumentFrame" src="" class="hidden w-full h-[70vh] rounded-lg"></iframe>
            </div>
            
            <!-- Modal Footer -->
            <div class="p-4 border-t border-gray-100 bg-white flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tekan ESC atau klik di luar untuk menutup
                </p>
                <div class="flex gap-2">
                    <a id="modalDownloadBtn" href="" download class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download
                    </a>
                    <button onclick="closeDocumentModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection