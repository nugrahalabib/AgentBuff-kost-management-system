# KosMutiara27 — Sistem Manajemen Rumah Kos

Aplikasi web berbasis Laravel untuk pengelolaan rumah kos secara digital. Dibangun sebagai proyek tugas akhir dengan fitur multi-peran, manajemen kamar, transaksi, laporan, dan asisten AI.

## Teknologi yang Digunakan

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Tailwind CSS 3, Alpine.js, Vite
- **Database**: MySQL
- **AI**: Google Gemini API
- **Export**: DomPDF (PDF), PhpSpreadsheet (Excel)

## Fitur Utama

| Fitur | Admin | Pemilik Kos | Penyewa |
|-------|-------|-------------|---------|
| Dashboard & Statistik | ✓ | ✓ | ✓ |
| Manajemen Kamar | ✓ | ✓ | — |
| Manajemen Penyewa | ✓ | ✓ | — |
| Verifikasi Transaksi | ✓ | ✓ | — |
| Reservasi & Sewa Kamar | — | — | ✓ |
| Laporan (PDF & Excel) | ✓ | ✓ | — |
| Asisten AI (Gemini) | — | ✓ | — |
| Manajemen Konten Website | — | ✓ | — |
| Notifikasi | ✓ | ✓ | ✓ |

## Prasyarat

Pastikan perangkat kamu sudah terpasang:

- PHP >= 8.2 (dengan ekstensi: `pdo_mysql`, `mbstring`, `xml`, `zip`, `gd`)
- Composer
- Node.js >= 18 & npm
- MySQL >= 8.0

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/adzkiyaqarina/kost-management-system-KosMutiara27.git
cd kost-management-system-KosMutiara27
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan sesuaikan konfigurasi berikut:

```env
# Konfigurasi Database
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=password_database

# Konfigurasi AI Assistant (lihat panduan di bawah)
GEMINI_API_KEY=isi_dengan_api_key
```

### 4. Siapkan Database

Buat database baru di MySQL, lalu jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

Perintah ini akan membuat semua tabel dan mengisi data awal termasuk akun default.

### 5. Buat Symlink Storage

```bash
php artisan storage:link
```

### 6. Build Asset Frontend

```bash
npm run build
```

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Akses aplikasi di: `http://127.0.0.1:8000`

---

## Akun Default

Setelah menjalankan seeder, akun berikut tersedia untuk pengujian:

| Peran | Email | Password |
|-------|-------|----------|
| Admin | `admin@kosadmin.local` | `password123` |
| Pemilik Kos | `owner1@kosadmin.local` | `password123` |
| Penyewa | `ahmad@example.com` | `password123` |

> **Catatan**: Ganti password akun-akun ini setelah login pertama kali di lingkungan produksi.

---

## Konfigurasi Fitur AI Assistant

Fitur AI Assistant menggunakan Google Gemini API. Ikuti langkah berikut untuk mengaktifkannya:

1. Buka [Google AI Studio](https://aistudio.google.com/)
2. Login dengan akun Google
3. Klik **"Get API Key"** → **"Create API Key"**
4. Salin API key yang dihasilkan
5. Tempel di file `.env`:
   ```env
   GEMINI_API_KEY=your_api_key_here
   ```

> Google AI Studio menyediakan **tier gratis** yang cukup untuk keperluan pengujian dan pengembangan.

Jika `GEMINI_API_KEY` dikosongkan, fitur AI Assistant tidak akan berfungsi namun seluruh fitur lainnya tetap dapat digunakan.

---

## Mode Development

Untuk menjalankan semua service sekaligus (server, queue, log, vite HMR):

```bash
composer dev
```

Untuk menjalankan pengujian:

```bash
composer test
```

---

## Struktur Peran Pengguna

```
Admin
└── Mengelola seluruh sistem (penyewa, transaksi, laporan global)

Pemilik Kos
├── Mengelola kamar & tipe kamar
├── Memverifikasi transaksi pembayaran
├── Mengunduh laporan keuangan & penghuni
├── Mengelola konten halaman utama website
└── Menggunakan AI Assistant untuk analisis data kos

Penyewa
├── Melakukan reservasi kamar
├── Upload bukti pembayaran
└── Melihat riwayat transaksi
