# AgentBuff KostCloud

Aplikasi web **manajemen kos internal berbasis subscription (SaaS multi-tenant)** yang bisa
dikendalikan oleh **AI agent melalui MCP** (Model Context Protocol). Setiap pemilik kos punya
workspace terpisah untuk mengelola kamar, penyewa, transaksi, dan laporan — tanpa reservasi publik.

> Konteks lengkap proyek, keputusan arsitektur, dan log perubahan ada di
> [AGENTBUFF-KOSTCLOUD.md](AGENTBUFF-KOSTCLOUD.md).

## Teknologi

- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Tailwind CSS 3, Alpine.js, Vite, driver.js (tur onboarding)
- **Database:** MySQL 8
- **Auth:** Google OAuth (owner) + email/password (admin) + Sanctum (MCP)
- **AI/MCP:** Laravel MCP (server HTTP `/mcp`)
- **Export:** DomPDF (PDF), PhpSpreadsheet (Excel)

## Peran

| Peran | Keterangan |
|-------|-----------|
| **Pemilik Kos (owner)** | Daftar & masuk **hanya dengan Google**. Akses penuh: kamar, penyewa, transaksi, laporan, pengaturan, kelola admin, token MCP. |
| **Admin** | Opsional — dibuat oleh owner. Masuk dengan **email & password**. Data tersinkron dengan owner. |
| **Penyewa** | Bukan akun login — hanya **data internal** yang dikelola owner/admin. |

## Menjalankan dengan Docker

Prasyarat: Docker Desktop.

```bash
docker compose up -d --build
```

Buka **http://localhost:8000**. Container menjalankan migrasi + seeder otomatis.

### Akun default (hasil seeder)

| Peran | Cara masuk |
|-------|------------|
| Owner | **Google OAuth** di halaman awal (butuh `GOOGLE_CLIENT_*` di `.env`) |
| Admin | `admin@kosadmin.local` / `password123` |

Atau **daftar owner baru** di halaman awal → **Daftar dengan Google**.

Perintah lain: `docker compose logs -f app`, `docker compose stop`, `docker compose down -v` (reset DB).

## Integrasi AI Agent (MCP)

1. Login owner → menu **MCP / AI Agent** → **Generate Token**.
2. Berikan URL + bearer token ke AI agent (Claude Code, Codex, Hermes, OpenClaw, dll):

```json
{
  "mcpServers": {
    "kostcloud": {
      "type": "http",
      "url": "https://kos.agentbuff.id/mcp",
      "headers": { "Authorization": "Bearer <TOKEN_ANDA>" }
    }
  }
}
```

Untuk lokal Docker, ganti URL menjadi `http://localhost:8000/mcp`.

Tool yang tersedia (semua otomatis di-scope ke kos pemilik token): ringkasan dashboard, daftar/tambah/hapus
kamar, ubah status kamar, daftar/hapus tipe kamar, daftar/tambah/hapus penyewa (+ tempatkan ke kamar),
daftar/catat/hapus transaksi, dan generate laporan.

## Pengembangan lokal (tanpa Docker penuh)

Butuh PHP 8.3, Composer, Node 20, dan MySQL. Jalankan server dev menunjuk ke MySQL container:

```bash
composer install && npm install && npm run build
php artisan migrate --seed
php artisan serve
```
