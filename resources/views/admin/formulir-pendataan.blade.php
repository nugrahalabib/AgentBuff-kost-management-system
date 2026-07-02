@extends('layouts.admin')

@section('title', 'Antrian Formulir Pendataan')

@section('content')
<div class="flex flex-col gap-6">
  <!-- Page Heading -->
  <div class="flex flex-wrap justify-between gap-4 mb-3">
    <div class="flex min-w-72 flex-col gap-2">
      <h1 class="text-gray-900 dark:text-emerald-800 text-3xl font-bold tracking-tight">Formulir Pendataan</h1>
      <p class="text-gray-500 dark:text-gray-500 text-base font-normal leading-normal">
        Tinjau dan kelola tanggapan formulir baru dari calon penyewa.
      </p>
    </div>
  </div>

  <!-- Cards List -->
  <div class="flex flex-col gap-4">
    @php
      $cards = [
        [
          'nama' => 'Ahmad Subarjo',
          'telepon' => '0812-3456-7890',
          'email' => 'ahmad.subarjo@example.com',
          'tanggal' => '14 Agustus 2023'
        ],
        [
          'nama' => 'Siti Aminah',
          'telepon' => '0813-9876-5432',
          'email' => 'siti.aminah@example.com',
          'tanggal' => '12 Agustus 2023'
        ],
        [
          'nama' => 'Budi Santoso',
          'telepon' => '0812-1122-3344',
          'email' => 'budi.s@example.com',
          'tanggal' => '11 Agustus 2023'
        ],
        [
          'nama' => 'Rina Marlina',
          'telepon' => '0817-2233-4455',
          'email' => 'rina.marlina@example.com',
          'tanggal' => '10 Agustus 2023'
        ],
        [
          'nama' => 'Dewi Lestari',
          'telepon' => '0819-3344-5566',
          'email' => 'dewi.lestari@example.com',
          'tanggal' => '9 Agustus 2023'
        ],
        [
          'nama' => 'Fajar Pratama',
          'telepon' => '0821-5566-7788',
          'email' => 'fajar.pratama@example.com',
          'tanggal' => '8 Agustus 2023'
        ],
      ];
    @endphp

    @foreach($cards as $card)
    <div class="flex flex-col gap-4 rounded-xl bg-white dark:bg-white p-6 shadow-sm border border-[#dbe6e3] dark:border-transparent">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <p class="text-gray-500 dark:text-emerald-700 text-sm">Nama Calon Penyewa</p>
          <p class="text-gray-900 dark:text-black text-base font-semibold leading-tight">{{ $card['nama'] }}</p>
        </div>
        <div>
          <p class="text-gray-500 dark:text-emerald-700 text-sm">Nomor Telepon</p>
          <p class="text-gray-900 dark:text-black text-base font-medium leading-normal">{{ $card['telepon'] }}</p>
        </div>
        <div>
          <p class="text-gray-500 dark:text-emerald-700 text-sm">Email</p>
          <p class="text-gray-900 dark:text-black text-base font-medium leading-normal">{{ $card['email'] }}</p>
        </div>
        <div>
          <p class="text-gray-500 dark:text-emerald-700 text-sm">Tanggal Mengisi</p>
          <p class="text-gray-900 dark:text-black text-base font-medium leading-normal">{{ $card['tanggal'] }}</p>
        </div>
      </div>
      <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#dbe6e3] dark:border-gray-400">
        <button class="flex min-w-[84px] items-center justify-center gap-2 rounded-lg h-10 px-4 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
          <span class="material-symbols-outlined text-lg">delete</span>
          <span class="truncate">Hapus</span>
        </button>
        <button class="flex min-w-[84px] items-center justify-center gap-2 rounded-lg h-10 px-4 bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition-colors">
          <span class="material-symbols-outlined text-lg">add_circle</span>
          <span class="truncate">Tambahkan</span>
        </button>
      </div>
    </div>
    @endforeach

    <!-- Empty State Card -->
    {{-- <div class="flex flex-col items-center justify-center gap-4 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
      <span class="material-symbols-outlined text-5xl text-gray-400 dark:text-gray-500">upcoming</span>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-500">Tidak ada data baru.</h3>
      <p class="text-gray-500 dark:text-gray-400">Anda sudah melihat semua tanggapan terbaru.</p>
    </div> --}}
  </div>
</div>
@endsection