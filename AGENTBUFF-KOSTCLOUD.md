# AgentBuff KostCloud — Konteks & Rencana Proyek

> File ini adalah **sumber kebenaran** untuk arah proyek. Dibaca ulang setiap kali
> perlu mengingat tujuan, keputusan, rencana, dan progres. Diperbarui setiap ada
> perubahan besar.

Terakhir diperbarui: 2026-07-19

---

## 1. Ringkasan & Tujuan

**AgentBuff KostCloud** adalah aplikasi **web manajemen kos internal berbasis
subscription (SaaS multi-tenant)** yang dapat dikendalikan oleh **AI agent melalui
MCP** (Model Context Protocol).

### Dari → Ke

| Aspek | Kondisi Awal (Kos Mutiara 27) | Tujuan (AgentBuff KostCloud) |
|-------|-------------------------------|------------------------------|
| Lingkup | Satu rumah kos saja | Banyak pemilik kos (multi-tenant) |
| Model | Aplikasi tunggal | SaaS komersial berbasis subscription |
| Reservasi | Reservasi online publik oleh penyewa | **Dihapus** — hanya manajemen internal |
| Penyewa | Punya akun, dashboard, booking | **Data internal** yang dikelola owner/admin (tanpa login) |
| Admin | Selalu ada | **Opsional**, dibuat oleh owner bila perlu |
| Owner | Akses terbatas (banyak view-only) | **Akses penuh** (paritas dengan admin) |
| AI | AI Assistant (Gemini) internal | **Dihapus**; diganti kontrol via **MCP** |
| Integrasi AI | — | **MCP server + bearer token** untuk Claude Code, Codex, dll |

---

## 2. Kondisi Awal (hasil pemetaan codebase)

Stack: **Laravel 12 / PHP 8.3**, Blade + Tailwind 3 + Alpine.js + Vite, MySQL,
auth **Laravel Breeze** + Google OAuth (Socialite). Dijalankan via Docker
(lihat `docker-compose.yml`).

### Peran & Otorisasi
- Peran = kolom string `user.role` ∈ {`owner`, `admin`, `tenant`}, default `tenant`.
- Otorisasi via middleware `CheckRole` (`role:owner|admin|tenant`).
  ⚠️ Tidak memeriksa `user.status` (admin nonaktif tetap lolos).
- **Tidak ada Gate/Policy** — otorisasi baris via cek `owner_id` manual di controller.
- Tabel profil 1:1: `admin` (owner_id, position), `penyewa` (data pribadi/dokumen),
  `pemilik_kos` (setting bisnis owner: bank, denda, nama kos).

### Kunci multi-tenant (PENTING)
- **Skema sudah punya `owner_id`** di: `tipe_kamar, kamar, transaksi, pengeluaran,
  financial_reports, laporan, admin, admin_activity_logs, pemilik_kos, content_sections`.
- **Dikunci single-owner**: `User::boot()` melempar `ValidationException` bila ada
  >1 user role `owner`; controller admin memakai `User::where('role','owner')->first()`
  (owner global), ada fallback hardcode `owner_id = 1`.
- **Belum ber-`owner_id`** (perlu ditambah): `penyewa, user, riwayat_penghuni_kamar,
  room_occupancy_histories, room_status_histories, maintenance_requests, bukti_bayar,
  late_payment_fines, notifications`.
- ⚠️ Jebakan: kolom `penyewa_id` (di `transaksi`, dll) FK ke tabel **`user`**, sedangkan
  tabel **`penyewa`** = tenant_profiles (kunci `user_id`). `content_sections.section_key`
  unique global → harus `unique(owner_id, section_key)`.

### Peta area controller
- **Admin** (`app/Http/Controllers/Admin/*`, 9): Dashboard, KamarController (CRUD kamar
  penuh), PenyewaController (verify/unverified/sendReminder/checkout), AccountController
  (akun penyewa), TransactionController (verifyPayment/updateProofStatus/storeManual),
  LaporanController (generate/submit/export), NotificationController, ContentController (CMS).
  · `Admin/RoomController` = duplikat mati (dead code).
- **Owner** (`app/Http/Controllers/PemilikKos/*`, 10): Dashboard, KamarController
  (**view-only**), TipeKamarController (CRUD), PenyewaController (**view-only**),
  TransactionController (verify/destroy), LaporanController (cashflow/expense/export),
  NotificationController, SettingsController, AdminManagementController (kelola admin +
  audit log), **AiAssistantController** (Gemini).
- **Penyewa** (`app/Http/Controllers/Penyewa/*`, 2): BookingController (reservasi/bayar/
  perpanjang/retry/cancel), ProfilController (dashboard/profil/dokumen).

### Fitur yang admin punya tapi owner belum (perlu diporting ke owner)
1. CRUD kamar (create/store/updateStatus/destroy)
2. Siklus penyewa (unverified/verify/sendReminder/checkout)
3. Transaksi manual + updateProofStatus (storeManual/updateProofStatus)
4. Generator laporan (create/generate/submit)
5. Notifikasi lebih lengkap (byCategory/markAsRead)
6. (Opsional) CMS konten & manajemen akun penyewa

### AI Assistant (akan dihapus)
- `PemilikKos/AiAssistantController` + `app/Services/AiAssistant/*` (GeminiClient,
  AiConfig, KnowledgeManager, MemoryManager, PromptBuilder).
- View `resources/views/pemilik-kos/ai-assistant.blade.php`; tabel `ai_chat_sessions`,
  `ai_assistant`; config `GEMINI_API_KEY`.

### Reservasi & penyewa (akan dihapus sebagai fitur publik)
- `Penyewa/BookingController`, `Penyewa/ProfilController`, route prefix `tenant`,
  view `penyewa/*` (detail-kamar, booking-form, pembayaran, retry, perpanjang).
- Registrasi publik (`RegisteredUserController`) & Google OAuth **selalu** buat role `tenant`.

### Frontend
- 77 view Blade, 6 layout. Landing = `welcome.blade.php` (~979 baris, khusus
  "Kos Putri Mutiara27", konten dari CMS admin).
- Umumnya responsif (44/77 pakai `sm:/md:/lg:`). **Kelemahan**: sidebar admin/owner
  tidak auto-collapse di mobile (collapse manual); navbar penyewa sembunyikan tab
  tanpa hamburger (area penyewa akan dihapus toh).

---

## 3. Perubahan yang Diminta (8 poin)

1. **Admin opsional** — dibuat hanya bila owner membuatnya. (Infrastruktur sudah ada
   via `AdminManagementController`; perlu pastikan sistem jalan tanpa admin sama sekali.)
2. **Owner = paritas akses admin** — owner bisa CRUD penuh seperti admin; bila ada admin,
   perubahan owner & admin **berbagi data** (via `owner_id` yang sama). Fitur owner yang
   sudah ada tetap dipertahankan.
3. **Hapus AI Assistant.**
4. **Hapus reservasi online + dashboard/fitur penyewa** — cukup manajemen internal untuk
   owner & admin. (Penyewa tetap ada sebagai **data**, bukan akun.)
5. **Landing page informatif** tentang AgentBuff KostCloud (marketing → subscription/daftar).
6. **Frontend responsif** — nyaman di HP.
7. **Bearer token MCP** yang mudah di-generate untuk diberikan ke AI agent (Claude Code,
   Codex, Hermes, OpenClaw).
8. **Semua fitur dapat diakses AI agent via MCP.**

---

## 4. Rencana Bertahap

- **Fase 0 — Dokumentasi & keputusan arsitektur.** ← (file ini)
- **Fase 1 — Fondasi multi-tenant.** Cabut invariant single-owner; alur daftar owner
  (signup subscription); tambah `owner_id` di tabel yang kurang; perbaiki controller admin
  agar resolve owner dari `admin.owner_id` (bukan owner global); `CheckRole` cek `status`;
  field status/subscription akun.
- **Fase 2 — Hapus fitur.** AI Assistant (poin 3); reservasi online + login/dashboard
  penyewa (poin 4) — pertahankan penyewa sebagai data.
- **Fase 3 — Paritas owner = admin (poin 1, 2).** Port fitur admin-eksklusif ke owner;
  pastikan owner & admin berbagi data via tenant yang sama.
- **Fase 4 — Landing page SaaS + alur subscription/daftar (poin 5).**
- **Fase 5 — Responsif mobile (poin 6).** Sidebar off-canvas, dll.
- **Fase 6 — MCP (poin 7, 8).** Bearer token (Sanctum) + MCP server + tools untuk seluruh
  fitur manajemen (kamar, tipe kamar, penyewa, transaksi, pengeluaran, laporan, dsb).
- **Fase 7 — Verifikasi menyeluruh & finalisasi dokumentasi.**

---

## 5. Keputusan Arsitektur

Dikonfirmasi 2026-07-03:

- **Subscription = SIMULASI dulu.** Belum ada logika penagihan/gateway. Cukup
  tampilan paket langganan + alur daftar owner + (opsional) status akun. Enforcement
  ditunda. Landing page menampilkan paket, tombol "Mulai/Berlangganan" mengarah ke
  daftar akun owner.
- **Penyewa = data internal tanpa akun login.** Owner/admin yang meng-input & mengelola
  data penyewa (nama, kamar, sewa, pembayaran) sebagai record. Seluruh alur reservasi/
  booking/registrasi/dashboard penyewa dihapus. Peran `tenant` tidak lagi bisa login.
- **MCP = server HTTP di dalam Laravel.** Endpoint MCP di aplikasi, auth **bearer token
  (Laravel Sanctum)**, memakai paket resmi **Laravel MCP**. Agent cukup diberi URL + token.

### Urutan eksekusi (disesuaikan)
1. Fase 1 — Hapus fitur (AI Assistant + reservasi/login penyewa). Penyewa jadi data.
2. Fase 2 — Fondasi multi-tenant (cabut single-owner, daftar owner, scoping owner_id).
3. Fase 3 — Paritas owner = admin.
4. Fase 4 — Landing SaaS + simulasi subscription + daftar owner.
5. Fase 5 — Responsif mobile.
6. Fase 6 — MCP + bearer token.
7. Fase 7 — Verifikasi & finalisasi.

---

## 6. Log Perubahan

<!-- Fase 3 sedang berjalan -->

- 2026-07-03 — **Fase 3c selesai (item 1): owner CRUD kamar.** `PemilikKos/KamarController`
  + create/store/updateStatus/destroy (scoped `auth()->id()`, guard kepemilikan 403), routes
  `owner.kamar.*`, view `pemilik-kos/kamar-create`, tombol "Tambah Kamar" + aksi status/hapus di
  kartu index. Verifikasi: create→update status→delete OK; owner2 hapus kamar owner1 → 403.
- 2026-07-03 — **Fase 3d selesai (item 2): owner transaksi manual.** `PemilikKos/TransactionController`
  + createManual/storeManual (owner=verifikator final → `verified_by_owner`, sekaligus attach penyewa
  ke kamar via pivot riwayat_penghuni_kamar + update status kamar), route `owner.transaksi.create`/
  `store-manual`, view `pemilik-kos/transaksi-create`, tombol "Tambah Transaksi". Verifikasi: transaksi
  dibuat + penyewa ditempatkan (kamar → occupied).
- 2026-07-03 — **Fase 3e selesai (item 2,3): tambah penyewa + tempatkan ke kamar (owner & admin).**
  Trait bersama `App\Http\Controllers\Concerns\ManagesTenants` (`persistNewTenant` + `availableRoomsForOwner`,
  owner_id via `User::ownerId()`) dipakai owner & admin PenyewaController (+ create/store). Penyewa dibuat
  sebagai user role=tenant TANPA login (password acak, email placeholder bila kosong) + profil `penyewa`
  (owner_id), lalu opsional attach ke kamar (pivot). View partial `partials/penyewa-create-form` + wrapper
  `pemilik-kos/penyewa-create` & `admin/penyewa-create` + tombol "Tambah Penyewa" di kedua index. Route
  `{owner,admin}.penyewa.create/store` (diurut sebelum `{user}`). Fix: `tenant_type` enum hanya
  `mahasiswa|non_mahasiswa`. Verifikasi: owner→penyewa+kamar OK; admin→penyewa owner_id=2 (data terhubung).
- 2026-07-03 — **Fase 3f selesai (item 4): owner generate laporan.** `PemilikKos/LaporanController`
  + create/generate/generateReportFiles (generate HANYA untuk owner login, bukan loop semua owner spt
  admin; `admin_id` = owner id krn kolom NOT NULL; status langsung `sent`). Reuse `ReportExportTrait`
  + `ReportService` + view `pemilik-kos/laporan-pdf`. View `pemilik-kos/laporan-create` + tombol "Buat
  Laporan". Route `owner.laporan.create/generate`. Verifikasi: generate laporan keuangan → PDF dibuat,
  muncul di index owner.
- 2026-07-03 — ✅ **FASE 3 TUNTAS** (paritas owner=admin + cleanup + data terhubung). Semua 8 poin
  review pemilik proyek beres. Berikutnya: Fase 4 (landing SaaS + simulasi subscription), Fase 5
  (responsif mobile), Fase 6 (MCP + bearer token).
  CATATAN teknis yang masih ditunda: isolasi read antar-owner (beberapa query admin/owner masih global,
  aman selama demo terpusat di owner1) — akan dikeraskan bila multi-owner betulan dipakai.
- 2026-07-03 — **Fase 4 selesai (item 5): landing SaaS + simulasi subscription.** `welcome.blade.php`
  ditulis ulang total jadi landing produk (hero, fitur, section MCP/AI agent + contoh bearer token,
  harga simulasi 3 tier Gratis/Pro/Bisnis, CTA daftar) — full HTML + @vite + Alpine (nav hamburger),
  responsif, tema emerald. `WelcomeController` disederhanakan (buang dependensi CMS). Form register
  disesuaikan untuk pemilik kos + field "Nama Kos" (boarding_house_name). Verifikasi: `/` 200 dgn semua
  elemen; daftar owner + Nama Kos → owner dashboard; sitemap & login tetap 200. (Subscription = tampilan
  simulasi saja, belum ada penagihan — sesuai keputusan.)
- 2026-07-03 — **FIX isolasi data multi-tenant.** Owner/admin baru sebelumnya melihat data owner lain
  (query read global). Di-scope ke `owner_id` via `auth()->user()->ownerId()`: OWNER — PenyewaController
  (list+stats via whereHas tenantProfile owner_id; expiring/delinquent via transaksi owner_id;
  availableFloors via kamar owner_id), DashboardController (AdminActivityLog owner_id), AdminManagement
  (daftar admin whereHas adminProfile owner_id). ADMIN — Dashboard/Kamar/Penyewa/Transaksi/Laporan
  di-scope semua query ke ownerId(); KamarController & LaporanController::generate ganti owner-lookup
  global → resolveOwner() (generate laporan tak lagi loop semua owner); PenyewaController::show tambah
  guard kepemilikan (abort 404). Verifikasi: owner BARU = semua halaman 200 & KOSONG (0 data owner1);
  owner1 & admin-nya tetap lihat 5 penyewa/8 transaksi. (penyewa.owner_id dari Fase 2 jadi kunci scoping.)
- 2026-07-03 — **FITUR tutorial interaktif (onboarding).** driver.js (di-bundle Vite, tanpa CDN).
  Helper `window.KostTour` di `resources/js/app.js` (start/auto via localStorage). Layout owner & admin:
  tombol "Panduan" mengambang (ulang tur) + `@stack('tour')`. Tiap halaman (dashboard/kamar/penyewa/
  transaksi/laporan owner & admin) push tur: dashboard = walkthrough menu sidebar (target via selektor
  `a[href$="/owner/..."]`), halaman lain = highlight tombol Tambah. Auto-jalan sekali per halaman per
  browser (key `tour_done_*`). Verifikasi: elemen tur + KostTour/driver.js ter-bundle & termuat via @vite.
- 2026-07-03 — **Fase 5 selesai (responsif mobile).** Sidebar layout owner & admin diubah jadi
  off-canvas drawer di layar kecil (Alpine `x-data mobileOpen`): default `-translate-x-full`, `fixed
  inset-y-0 z-40`, muncul saat hamburger (topbar, `lg:hidden`) ditekan + backdrop `bg-black/50`;
  di desktop `lg:relative lg:translate-x-0` (tetap seperti semula). Tombol collapse desktop di-hide di
  mobile (`hidden lg:flex`). Transisi transform ditambah ke CSS `#main-sidebar`. Rebrand judul/logo
  "Mutiara27" → "KostCloud". Verifikasi: owner & admin dashboard 200, hamburger + kelas off-canvas ada
  di HTML & CSS build. (Landing sudah responsif dari Fase 4; tabel panel sudah overflow-x-auto.)
- 2026-07-03 — **Fase 6 selesai (item 7 & 8): MCP server + bearer token.** Paket `laravel/mcp` +
  `laravel/sanctum`. User pakai `HasApiTokens`; tabel `personal_access_tokens` (migrasi Sanctum).
  Server `app/Mcp/Servers/KostCloudServer.php` + **10 tools** di `app/Mcp/Tools/` (DashboardSummary,
  ListKamar, ListTipeKamar, CreateKamar, UpdateKamarStatus, ListPenyewa, CreatePenyewa, ListTransaksi,
  CreateTransaksi, GenerateLaporan). Semua tool di-scope ke owner token via trait `InteractsWithOwner`
  (`$request->user()->ownerId()`). Endpoint `routes/ai.php`: `Mcp::web('/mcp', ...)->middleware('auth:sanctum')`.
  UI generate/cabut token: `McpTokenController` + halaman `owner.mcp`/`admin.mcp` (partial `mcp-panel`,
  contoh mcp.json) + nav "MCP / AI Agent". Verifikasi: 401 tanpa token; initialize/tools-list (10);
  tools/call dashboard-summary → data owner1 asli; create-kamar → data nyata + validasi duplikat error;
  generate token via web → tampil sekali + masuk daftar. Contoh koneksi agent:
  `{"mcpServers":{"kostcloud":{"type":"http","url":"<APP_URL>/mcp","headers":{"Authorization":"Bearer <TOKEN>"}}}}`.
- 2026-07-03 — **Fase 7 selesai (finalisasi).** README ditulis ulang untuk AgentBuff KostCloud. Image
  Docker di-**rebuild** (`docker compose up -d --build`) mencakup semua perubahan + dependency baru
  (laravel/mcp, laravel/sanctum, driver.js). Verifikasi DI CONTAINER: `/` `/register` 200; login owner1
  → dashboard 200; `/owner/mcp` 200; endpoint `/mcp` → 401 tanpa token, tools/list = 10 tools,
  tools/call dashboard-summary → data owner1 (8 kamar, 5 penyewa). Token uji dibersihkan.

---

- 2026-07-03 — **Penyesuaian lanjutan (UX).** (1) Form **Tambah Kamar / Tambah Penyewa / Tambah
  Transaksi / Buat Laporan** diubah dari halaman terpisah menjadi **modal pop-up** (pola sama seperti
  "Tambah Tipe Kamar"): partial `partials/modal-{kamar,penyewa,transaksi,laporan}`, helper global
  `window.openModal/closeModal` di app.js (toggle `invisible opacity-0`), tombol index kini
  `openModal('...')`, modal auto-terbuka bila ada error validasi. Index penyewa/transaksi kini mengirim
  data untuk modal ($rooms / $tenants+$rooms). Route/halaman create lama tetap ada sebagai fallback.
  (2) Tur onboarding ditambah untuk halaman **Pengaturan** (jelaskan 3 tab: Harga & Tipe, Pengaturan,
  Profil Pemilik) dan **Notifikasi** (filter kategori + notifikasi urgent). Verifikasi: 4 halaman modal
  200 + modal/tombol ada; create kamar via modal OK + validasi duplikat auto-buka modal; tur pengaturan
  & notifikasi termuat.

- 2026-07-04 — **FITUR onboarding checklist (owner baru).** Kartu "Panduan Awal" di atas dashboard
  owner: checklist 4 langkah berurutan (1. Buat Tipe Kamar → 2. Tambah Kamar → 3. Tambah Penyewa →
  4. Catat Transaksi) dengan progress bar, langkah berikutnya di-highlight, tiap tombol deep-link
  `?add=1` yang **langsung membuka modal** halaman tujuan. Status tiap langkah **berbasis data nyata**
  (owner_id exists di tipe_kamar/kamar/penyewa/transaksi), bukan localStorage — jadi otomatis tercentang
  saat dibuat & persist lintas device. Kartu hilang saat 3 langkah inti selesai (`$setupComplete` di
  `DashboardController`). Partial `partials/onboarding-owner`. `KostTour.auto()` melewati tur bila `?add=1`
  (agar tak bentrok dgn modal, & tak menandai tur seen). Juga fix sebelumnya: tur di-key per-user
  (`meta user-id`) agar owner baru selalu dapat tur segar meski di browser yang sama.

- 2026-07-11 — **FIX: tipe kamar DOUBLE (kapasitas > 1).** (1) Harga: auto-isi nominal transaksi &
  label kamar kini pakai `$kamar->rent_per_person` (= `price_per_month / 2` utk capacity>1), bukan harga
  penuh — sebelumnya penyewa kamar double kena 2×. (2) Dropdown kamar menampilkan sisa slot
  (mis. "Kamar 900 — Duo (1/2 terisi) · Rp .../org"); controller kirim `withCount('occupants')`.
  Penempatan bertahap sudah benar sejak awal (reuse `hasAvailableSlot()`/`isFull()`): Duo 1 orang →
  status `available`, 2 orang → `occupied`, penuh → hilang dari dropdown. **Pelajaran:** `@if(...)…@endif`
  inline di dalam `@foreach` (dengan tanda kurung setelahnya) bisa menyandung compiler Blade → dihitung
  di blok `@php` saja. **Pelajaran validasi:** `artisan view:cache` TIDAK mengecek sintaks PHP hasil
  compile (lolos meski rusak); validasi benar = **php-lint file di `storage/framework/views/`** atau
  render halamannya.

- 2026-07-19 — **FITUR: Mode Terang/Gelap (theme switch) + konsistensi warna landing.**
  (1) `tailwind.config.js` → `darkMode: 'class'`. Lapisan **CSS override** di `resources/css/app.css`
  (`html.dark .bg-white/.bg-gray-50/.text-gray-*/.border-*`, kontrol form, gradien terang→gelap) memberi
  dark mode **menyeluruh** tanpa menyunting tiap elemen. `window.toggleTheme()/applyTheme()` di `app.js`
  menyimpan preferensi di `localStorage('theme')`; **script anti-FOUC** di `<head>` tiap layout
  (landing/welcome, `layouts/pemilik-kos`, `layouts/admin`) memasang kelas `.dark` sebelum render
  (default ikut `prefers-color-scheme`). Tombol matahari/bulan: nav landing (desktop + menu mobile) &
  topbar owner/admin. (2) **Konsistensi landing** (`welcome.blade.php`): seksi **#mcp** & **footer**
  yang tadinya `bg-gray-900` (gelap) → `bg-gray-50` (terang) agar seragam dgn seksi lain; teks/aksen
  disesuaikan, blok kode terminal (mockup `mcp.json`) tetap gelap. Verifikasi (puppeteer, dev 8001 +
  Docker 8000): toggle membalik tema & **persist** lintas reload/login; landing terang seragam, dark
  cohesive; owner `/owner/dashboard` & admin `/admin/dashboard` punya toggle + dark mode aktif
  (`bodyBg #0f172a`).

## ✅ STATUS: SEMUA FASE SELESAI

Aplikasi telah bertransformasi dari manajemen kos single-tenant + reservasi publik menjadi
**SaaS manajemen kos internal multi-tenant + kontrol AI agent via MCP**. Semua 8 permintaan awal
+ perbaikan isolasi data + tutorial onboarding + responsif mobile + finalisasi Docker: TUNTAS &
terverifikasi. Jalankan: `docker compose up -d --build` → http://localhost:8000.


_(Diisi seiring implementasi tiap fase.)_

- 2026-07-03 — Setup Docker untuk menjalankan aplikasi (Dockerfile, docker-compose,
  entrypoint). SESSION_DRIVER=file (tidak ada tabel sessions).
- 2026-07-03 — Pemetaan codebase menyeluruh & pembuatan dokumen konteks ini.
- 2026-07-03 — **Fase 1 selesai.** (a) Hapus AI Assistant: controller, `app/Services/AiAssistant/*`,
  view, route owner.ai, nav "Intelligence", model `AiAssistant`/`AiChatSession`, `GEMINI_API_KEY`,
  + migrasi drop tabel `ai_assistant`/`ai_chat_sessions`. (b) Hapus reservasi & login penyewa:
  controller `Penyewa/*`, view `resources/views/penyewa/*`, grup route `tenant`. Method
  `viewPaymentProof` & `viewDocument` **direlokasi** ke `App\Http\Controllers\DocumentController`
  (dipakai owner/admin). `/dashboard` kini hanya owner/admin (peran lain di-logout). Landing
  `welcome.blade.php` di-patch minimal (hapus CTA reservasi) — akan ditulis ulang di Fase 4.
  Verifikasi: `/`, `/login`, `/owner/dashboard` = 200; `/owner/ai-assistant` = 404; 0 route tenant.
- 2026-07-03 — Dev loop lokal: `php artisan serve` (host) → MySQL container (port 3307),
  `.env` lokal DB diarahkan ke 3307 (kostcloud/secret) + SESSION_DRIVER=file. Container `app`
  dihentikan sementara; `db` tetap jalan. Image Docker akan di-rebuild saat finalisasi.
- 2026-07-03 — **Fase 2 selesai (fondasi multi-tenant).** (a) Cabut invariant single-owner di
  `User::boot()`. (b) Tambah helper `User::resolveOwner()` / `ownerId()` / `isManager()` (owner→diri,
  admin→owner pembuatnya via `admin.owner_id`). (c) `CheckRole` kini menolak akun `status=inactive`
  (logout + ke login). (d) `RegisteredUserController::store` → daftar publik membuat akun **owner**
  + baris `pemilik_kos` (nama kos), auto-aktif, redirect owner dashboard. (e) Migrasi
  `penyewa.owner_id` (nullable FK ke user) + backfill dari transaksi/owner pertama; `owner_id`
  ditambahkan ke fillable `Penyewa`. Verifikasi: daftar→owner baru (200, 2 owner), admin nonaktif
  diblokir (302), owner1 lama tetap jalan. **Ditunda ke Fase 3:** ganti `User::where('role','owner')
  ->first()` di controller Admin dengan `auth()->user()->resolveOwner()`; denormalisasi owner_id ke
  tabel turunan (riwayat_penghuni_kamar, notifications, dll) bila diperlukan.
- 2026-07-03 — **Fase 3 (berjalan).** Cleanup: hapus "Data Akun Penyewa" (AccountController,
  route+nav+view, method `PenyewaController@unverified`) & "Kelola Konten" (ContentController,
  route+nav+views `admin/konten*`) dari admin — penyewa tak lagi punya akun. Hapus `Admin/RoomController`
  (dead code). Item 8: ganti resolusi owner global → `auth()->user()->resolveOwner()` di Admin
  Dashboard/Penyewa/Transaction (Kamar menyusul saat port); backfill `admin.owner_id` (admin@kosadmin
  ditautkan ke owner1). Verifikasi: panel admin semua 200; route `admin/konten` & `admin/akun-penyewa`
  → 404. **Sisa Fase 3:** port CRUD kamar + transaksi manual + generate laporan ke owner; fitur
  "tambah penyewa → tempatkan ke kamar tersedia" (owner & admin). CATATAN: read-scoping antar-owner
  (isolasi tenant lintas owner) menyusul — saat ini data terpusat di owner1.
- 2026-07-19 — **Batch 10 perbaikan (bug + fitur), diverifikasi via dev-loop lokal (`artisan serve`
  → MySQL Docker :3307) lalu di-deploy ulang ke image Docker.**
  1. **Auto-kompres foto (klien-side).** `resources/js/app.js`: `compressImageFile()` (canvas → JPEG,
     target <1.8MB, sisi ≤1920px, lewati PDF/GIF) + auto-attach pada `input[type=file][data-auto-compress]`
     (nonaktifkan submit saat proses). Dipasang di bukti bayar (owner/admin, halaman+modal), foto+galeri
     tipe kamar, bukti pengeluaran. Cek-ukuran manual admin dibuang. Uji browser (puppeteer): 25MB→1.46MB.
  2. **Hapus penyewa (wajib alasan).** `ManagesTenants::deleteTenant()` terpusat: bersihkan FK RESTRICT
     (transaksi→cascade bukti/log/denda, denda by penyewa, room_occupancy_histories) + file bukti &
     dokumen di disk + notifikasi terkait, lalu recompute status kamar. `destroy()` owner & admin
     (scope owner_id, alasan `required|min:5`). **Admin yang hapus → owner dinotifikasi.** Route
     `owner|admin.penyewa.destroy`; tombol + modal alasan di biodata-penyewa. Uji: tenant sintetis full-relasi
     → hapus bersih tanpa error FK + owner ternotifikasi; alur HTTP validasi alasan.
  3. **Durasi perpanjangan sewa (akumulatif).** Semua jalur (`PemilikKos`/`Admin/TransactionController::
     storeManual`, `CreateTransaksiTool`) dulu hitung `now()->addMonths()` → ganti ke
     `RoomAllocationService::computeRentalPeriod()` yang menyambung dari `period_end_date` terverifikasi
     terakhir penyewa (fallback now). Uji: 2bln lalu 3bln hari-sama = 5 bln (bukan 3).
  4. **Bukti bayar tak bisa dibuka.** Tabrakan nama `openModal(url)` (halaman) vs `window.openModal(id)`
     (app.js). Rename → `openProofModal`/`closeProofModal` di data-transaksi owner & admin.
  5. **Login Google.** Kode sudah lengkap; akun Google BARU kini → role **owner** (+ baris `pemilik_kos`),
     bukan tenant. Plumbing `GOOGLE_CLIENT_ID/SECRET/REDIRECT_URI` disiapkan di `.env`, `.env.example`,
     `docker-compose.yml`, & loop sync `entrypoint.sh` (kosong; tinggal diisi kredensial Google Cloud).
     Tombol muncul otomatis saat client_id terisi (diuji toggle).
  6. **Landing tanpa harga.** Hapus seksi `#harga` (paket langganan simulasi) + anchor nav di `welcome.blade.php`.
  7. **Amankan notifikasi.** `Admin/KamarController` `?? 1` (buang notifikasi kamar-yatim ke user id 1 =
     admin default) → `auth()->user()->ownerId()`. Titik-merah lonceng (admin & owner) dijadikan
     kondisional pada notifikasi belum-dibaca (dulu statis selalu menyala).
  8. **MCP hanya owner.** Hapus nav MCP admin (`layouts/admin`), route `admin.mcp*`, view `admin/mcp.blade.php`;
     `McpTokenController` selalu render view owner. Owner-side & endpoint `/mcp` (sanctum) tetap.
  9. **Dashboard admin disederhanakan.** Buang card + tabel "Tugas Verifikasi" (sisa alur tenant online yang
     sudah dihapus) + var controller mati (`pendingVerifications`, `paymentVerifications`, okupansi/movement).
  10. **Audit pengaturan owner.** 3 tab (Harga&Tipe, Penagihan/Bank, Profil/Password) semua fungsional;
      buang 3 route+method mati (`settings.business/profile/password`, tergantikan `updateAll`); rapikan
      teks yang masih menyebut "tenant".
- 2026-07-19 — **Batch 13 perbaikan lanjutan (bug + fitur), dev-loop lokal→DB Docker lalu rebuild image.**
  1. Modal tambah tipe kamar auto-close saat sukses: `TipeKamarController@store/update` redirect ke URL
     bersih `owner.settings` (bukan `back()` yg bawa `?add=1` → modal buka ulang).
  2. Catatan Duo di bawah kolom harga: `toggleDuoNote()` (plain JS) tampil saat kapasitas=2, dipanggil di
     openAdd/openEdit (pengaturan.blade).
  3. Hapus field "Catatan" di 4 form kamar (modal+create, owner+admin); validasi `notes` nullable → aman.
  4. Hapus kartu "Rekening Pembayaran" di pengaturan + bersihkan `updateBankSettings` (bank refs bisa error
     krn UI tak kirim lagi) + rapikan tour/teks yg salah (denda/rekening).
  5. **Form biodata self-service (fitur besar).** Kolom `penyewa.form_token` (migrasi). Owner/admin klik
     "Buat Link Isi Biodata" (`generateBiodataLink`) → link publik `GET/POST /biodata/{token}` (tanpa login,
     throttled) via `PublicBiodataController` + view `public/biodata-form.blade.php` → penyewa isi data pribadi
     + wali + upload dokumen (disk `local`, `tenant-documents/{penyewaId}`, merge JSON `documents`) → tampil
     otomatis di biodata. Guarded field (`is_verified_by_admin` dll) tak tersentuh. Banner link + salin + share WA.
  6. Sembunyikan email placeholder `@internal.local` di biodata: `User::hasPlaceholderEmail()` → "Email belum diisi".
  7. Blok hapus penyewa bila masih menempati kamar aktif (`occupiedRoom()->exists()`) di 2 `destroy()` → checkout dulu.
  8. Notifikasi transaksi masuk & keluar: `storeExpense` (KELUAR, type `info`), owner `storeManual` & MCP
     `CreateTransaksiTool` (MASUK, type `payment_received`) → notifikasi ke owner. Admin income sudah ada.
  9. Perbaiki tour utama admin (dashboard.blade) + tambah step tegas "Data admin & kos tersinkron"; bump key v2.
  10. Sidebar admin "Penyewa" jadi link langsung `admin.penyewa` (buang dropdown `toggleMenu`).
  11. Dashboard admin diperkaya: kartu Pemasukan/Menunggu/Pengeluaran/Laba Bersih + Okupansi + tabel
      "Transaksi Terbaru" (Admin/DashboardController scoped `ownerId()`).
  12. **Lantai konfigurable per kos.** Kolom `pemilik_kos.floor_count` (migrasi, default 4) + field "Jumlah Lantai"
      di pengaturan (disimpan via `updateBankSettings`). View composer `$floorCount` ke 5 view kamar → semua
      dropdown/tab/JS-cap lantai dinamis; validasi controller max:20 (dropdown yg batasi UX).
  13. Hapus kartu notifikasi dummy statis "Kontrak Segera Habis (Siti Aminah)" + tombol "Ingatkan via WA" di
      admin/notifikasi.blade (mockup lama, bukan data real).
- 2026-07-19 — **Batch 7 penyempurnaan (lanjutan biodata, dashboard, pengaturan).**
  1. Urutan kamar: `orderByRaw('CAST(room_number AS UNSIGNED) ASC')` (owner+admin) — nomor menaik murni,
     bukan lantai-dulu (kamar 102 di lt.2 dulu muncul setelah 103).
  2. Tab "Pengaturan" digabung ke tab "Profil Pemilik" → satu tab "Profil & Pengaturan" (kartu Siklus
     Penagihan + Struktur Kos/Jumlah Lantai dipindah, tombol simpan tunggal `updateAll` + floor_count;
     hapus btn-rules/#content-rules, rapikan switchTab & tour).
  3. Catatan kapasitas jadi dinamis: penjelasan tipe Single/Duo muncul sesuai pilihan (bukan lagi teks
     harga-dibagi-dua statis); catatan harga-dibagi-dua tetap di bawah kolom harga saat Duo.
  4. **Owner & admin bisa EDIT biodata penyewa** (bukan cuma generate link): route `penyewa.biodata.edit`
     /`.update`, view `biodata-edit` (layout dinamis) + tombol "Edit Biodata" di halaman biodata.
  5. Tanggal lahir jadi **3 dropdown** (Tgl/Bulan/Tahun; tahun 1940–kini) — mudah pilih tahun; digabung
     jadi date di server.
  6. Field baru di form biodata: **Telp Rumah Wali, NIK Wali, Email penyewa** (email menyasar akun User).
  7. Dashboard admin **tanpa keuangan** (admin tak boleh lihat keuangan kos): buang kartu Pemasukan/
     Pengeluaran/Laba + Transaksi Terbaru → ganti kartu **Okupansi Kos** + **Penyewa Terbaru** (non-uang).
  Refactor DRY: field biodata diekstrak ke `partials/biodata-fields.blade.php` + logika simpan ke trait
  `HandlesBiodataForm` (dipakai form publik & edit owner/admin).
- 2026-07-19 — **Batch 5 penyempurnaan (notifikasi, form modal, biodata, pengaturan).**
  1. Notifikasi ke admin saat OWNER mengubah data (tipe kamar add/edit/hapus, kamar baru, penyewa baru,
     penempatan) via trait `NotifiesAdmins::notifyAdminsOfChange` — hanya saat aktor owner, TANPA nominal
     (admin tak lihat keuangan). Menyasar semua admin milik owner tsb.
  2. Form tambah admin jadi pop-up modal seperti owner: admin "Tambah Penyewa" (dulu link full-page)
     → modal `partials/modal-penyewa` (diparameter `$storeRoute`). Admin kamar & transaksi sudah modal;
     edit biodata sengaja tetap halaman.
  3. FIX error buka link biodata yang sudah terisi: `birth_date`/`guardian_birth_date` di-cast `date` di
     model Penyewa (sebelumnya string → `$bd?->day` error saat merender 3-dropdown tanggal).
  4. Panduan utama owner dilengkapi step **Notifikasi** & **MCP / AI Agent** (+ teks Pengaturan diperbarui),
     bump key tur v2.
  5. Kartu "Siklus Penagihan" & "Struktur Kos" di tab Pengaturan dibuat **sebelahan** (grid 2 kolom).
  Infra: entrypoint Docker kini juga `php artisan view:clear` — compiled-view ikut volume storage, tanpa ini
  blade lama bisa tersaji setelah rebuild image.
- 2026-07-19 — **Batch 2 (form biodata: mobile + upload).**
  1. Form biodata penyewa (partial `biodata-fields`) dibuat mobile-friendly: padding kartu adaptif
     (`p-4 sm:p-6`), 3-dropdown tanggal lebih rapat (`px-2`) agar muat di layar HP; diuji 375px tanpa
     overflow horizontal.
  2. Auto-kompres upload: kompres sisi-klien sebenarnya SUDAH jalan (25MB→~1.5MB), tapi PHP
     `upload_max_filesize` default 2M menolak file besar diam-diam (mis. PDF / bila JS gagal). Naikkan
     via `docker/uploads.ini` (upload 20M / post 25M / memory 256M, di-COPY di Dockerfile). Tambah
     feedback visual "⏳ Mengecilkan foto → ✓ Foto siap (xxx KB)" di app.js saat kompres berjalan.
