@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">Tambah Penyewa</h2>
            <p class="text-sm text-gray-500">Catat penyewa baru & tempatkan ke kamar tersedia.</p>
        </div>
        <a href="{{ route('owner.penyewa') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-bold py-3 px-6 rounded-xl shadow-md transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            KEMBALI
        </a>
    </div>
@endsection

@section('content')
    @include('partials.penyewa-create-form', [
        'storeRoute' => route('owner.penyewa.store'),
        'backRoute' => route('owner.penyewa'),
        'rooms' => $rooms,
    ])
@endsection
