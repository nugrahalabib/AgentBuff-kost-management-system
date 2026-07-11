# Brief: Port Fitur Baru ke Aplikasi Kos Single-Kos (Kos Mutiara 27)

> **Untuk siapa:** AI agent / developer yang mengerjakan proyek **aplikasi satu kos** (Kos Mutiara 27) —
> BUKAN proyek AgentBuff KostCloud (yang multi-tenant).
> **Sumber fitur:** proyek AgentBuff KostCloud (duplikat yang sudah diubah jadi SaaS multi-tenant).

---

## 0. ATURAN MAIN (WAJIB DIBACA DULU)

1. **JANGAN HAPUS / KURANGI fitur yang sudah ada** di aplikasi single-kos. Semua tetap:
   - ✅ AI Assistant (Gemini) — **tetap ada**.
   - ✅ Reservasi online + akun penyewa + dashboard penyewa — **tetap ada**.
   - ✅ CMS konten landing, kelola akun penyewa — **tetap ada**.
2. **Konteks single-kos = SATU pemilik (owner).** Karena itu, **JANGAN** port hal-hal berikut
   (itu khusus transformasi multi-tenant dan TIDAK relevan di single-kos):
   - ❌ Multi-tenant / mencabut kunci single-owner.
   - ❌ Isolasi data per-owner (semua data memang milik 1 owner).
   - ❌ Landing page SaaS + paket subscription + daftar owner publik.
3. **Yang diporting HANYA fitur ADITIF** (menambah, tidak mengubah alur lama). Daftar di §1.
4. Sebelum menulis kode: **baca dulu struktur controller/route/view aktual** di proyek single-kos
   (nama file bisa beda). Cocokkan pola yang ada, jangan asal tempel.
5. Kerjakan **per fitur**, verifikasi tiap fitur (buat data uji → cek jalan → hapus data uji) sebelum lanjut.
6. **Rawat DOKUMENTASI HIDUP** (lihat §0.b): buat satu file konteks/progres dan **perbarui terus** setiap
   ada perubahan — supaya pekerjaan bisa dilanjutkan kapan pun tanpa kehilangan konteks.

---

## 0.b DOKUMENTASI HIDUP & LOG PROGRES (WAJIB)

Buat **satu file** di repo (mis. `PROYEK-KONTEKS.md` atau `docs/PROGRES.md`) dan **PERBARUI SETIAP KALI**
ada perubahan berarti — jangan menunggu selesai semua. File ini jadi "sumber kebenaran" agar siapa pun
(atau kamu sendiri lain waktu) bisa melanjutkan tanpa kehilangan konteks. Isinya minimal:

1. **Ringkasan & tujuan** — apa proyek ini & apa yang sedang dicapai (fitur apa yang diporting).
2. **Kondisi awal** — hasil pemetaan struktur codebase (controller/route/view/model yang relevan) sebelum diubah.
3. **Keputusan penting** — pilihan arsitektur/logika + alasannya (mis. "harga kamar double pakai `rent_per_person`").
4. **Rencana bertahap** — daftar fitur + urutan pengerjaan + status (belum / berjalan / selesai).
5. **LOG PERUBAHAN** ← PALING PENTING. Tiap entri: **tanggal + apa yang diubah + file kunci yang disentuh +
   hasil verifikasi** (mis. "2026-07-xx — Owner CRUD kamar: +method create/store/destroy di KamarController,
   route owner.kamar.*, view modal; verifikasi: tambah kamar 200, hapus 200"). Catat juga **bug yang ditemukan
   & cara memperbaikinya**, dan **hal yang ditunda** (biar tidak lupa).

**Kenapa wajib:** proyek besar sering berjalan lintas sesi/orang. Tanpa log ini, mudah lupa apa yang sudah
dikerjakan, mengulang kesalahan yang sama, atau menyisakan pekerjaan setengah jadi tanpa jejak. (Di proyek
sumber, file `AGENTBUFF-KOSTCLOUD.md` menjalankan peran ini — jadikan contoh polanya.)

---

## 1. DAFTAR FITUR YANG DIPORTING (urut prioritas)

| # | Fitur | Tipe |
|---|-------|------|
| A | **Akses pemilik kos = admin** (CRUD kamar, kelola penyewa, transaksi manual, generate laporan) | Inti (prioritas utama) |
| B | **Tambah penyewa manual + tempatkan ke kamar tersedia** (owner & admin) | Fitur baru |
| C | **Form pop-up modal** untuk form Tambah (kamar/penyewa/transaksi/laporan) | UX |
| D | **Tutorial interaktif per halaman** (driver.js) + tombol "Panduan" | UX / onboarding |
| E | **Checklist onboarding berurutan** di dashboard | UX / onboarding |
| F | **Sidebar responsif mobile** (off-canvas + hamburger) | UX |
| G | **MCP server + bearer token** (kendali via AI agent) | Fitur baru (opsional/lanjutan) |

---

## FITUR A — Akses pemilik kos SAMA dengan admin  ⭐ PRIORITAS

### Tujuan
Di aplikasi single-kos, **admin** adalah peran paling lengkap (CRUD kamar penuh, verifikasi/checkout
penyewa, input transaksi manual, generator laporan, dst), sedangkan **owner** banyak yang view-only.
Buat agar **owner bisa melakukan semua yang admin bisa**, sehingga bila tidak ada admin, owner tetap
mandiri. Karena single-owner, **data owner & admin memang satu dan sama** (tidak perlu penyesuaian
"sinkronisasi" — cukup beri owner aksi yang sama).

### Logika & langkah
Untuk tiap kemampuan admin yang belum ada di owner, **port method + route + view + tombol** ke area owner:

1. **CRUD Kamar** (admin punya, owner biasanya view-only):
   - Port `create`, `store`, `updateStatus`, `destroy` dari `Admin/KamarController` ke controller kamar owner.
   - Saat `store`, `owner_id` kamar diisi dari owner (di single-kos: `owner_id` tipe kamar terpilih,
     atau id owner tunggal). Validasi `room_number` unik, `tipe_kamar_id` ada.
   - Tambah route owner (`owner/kamar/create`, POST `owner/kamar`, PATCH status, DELETE),
     view form, dan tombol "Tambah Kamar" di halaman kamar owner.
2. **Siklus penyewa** (`verify`, `checkout`, `sendReminder`) — port dari `Admin/PenyewaController` ke owner.
3. **Transaksi manual** (`storeManual`, `updateProofStatus`) — port dari `Admin/TransactionController` ke owner
   (owner sudah punya `verify`/`destroy` transaksi biasanya; tinggal tambah input manual).
4. **Generator laporan** (`create`, `generate`, `submit`) — port dari `Admin/LaporanController` ke owner.
   Karena single-owner, generate cukup untuk 1 owner (TIDAK perlu loop semua owner).
5. **(Opsional) CMS konten & kelola akun penyewa** — bila ingin owner juga bisa, port juga.

### Prinsip agar logika benar
- Owner meng-scope datanya dengan `auth()->id()` (dia owner tunggal). Admin memakai owner tunggal sistem
  (`User::where('role','owner')->first()`). Keduanya menunjuk owner yang sama → **data otomatis satu**.
- **Reuse logika**, jangan tulis ulang beda: idealnya pindahkan logika inti (mis. penempatan kamar,
  pembuatan file laporan) ke satu tempat (service/trait) yang dipakai admin & owner, supaya tidak
  bercabang dan tidak menimbulkan bug.
- Jangan sentuh alur admin yang sudah jalan — hanya **tambah** padanan di owner.

### Kriteria selesai
- Owner bisa tambah/ubah-status/hapus kamar, tambah/verifikasi transaksi, generate laporan, kelola penyewa —
  sama seperti admin. Perubahan oleh owner terlihat oleh admin dan sebaliknya (karena data sama).

---

## FITUR B — Tambah penyewa manual + tempatkan ke kamar tersedia

### Tujuan
Selain reservasi online oleh penyewa (yang **tetap ada**), owner/admin bisa **mencatat penyewa secara
manual** (mis. penyewa walk-in) dan **langsung menempatkannya ke kamar yang masih kosong** — tanpa alur
reservasi.

### Logika & langkah
1. Form "Tambah Penyewa": nama, email (opsional), no. HP, tipe penyewa, alamat, + **pilih kamar (opsional)**
   dari daftar **kamar yang punya slot kosong**.
2. Saat simpan:
   - Buat `User` role `tenant` (penyewa sebagai data; bila tak perlu login, pakai password acak & email
     placeholder unik) **atau** manfaatkan mekanisme akun penyewa yang sudah ada bila ingin ia bisa login.
   - Buat baris profil penyewa (tabel `penyewa`/tenant_profiles), tautkan `user_id`.
   - Bila kamar dipilih → **tempatkan** memakai mekanisme okupansi yang SUDAH ADA (yang dipakai saat
     verifikasi pembayaran reservasi): `attach` ke pivot `riwayat_penghuni_kamar` (kolom `check_in_date`,
     `check_out_date` NULL = aktif), lalu update status kamar (`occupied` bila penuh).
   - **Cek kapasitas** sebelum menempatkan (tolak bila kamar penuh).
3. Tersedia untuk **owner & admin** (bila fitur A sudah membuat owner setara admin, tinggal 1 flow yang
   dipakai bersama lewat trait/service).

### Prinsip agar logika benar
- **Pakai ulang mekanisme okupansi yang sudah ada** (jangan bikin cara baru menempatkan penyewa) agar
  konsisten dengan alur reservasi. Sumber kebenaran okupansi = pivot `riwayat_penghuni_kamar`.
- Jangan bentrok dengan reservasi online: ini jalur **tambahan**, bukan pengganti.

### ⚠️ WAJIB: tangani TIPE KAMAR DOUBLE (kapasitas > 1)
Kamar bisa punya `capacity > 1` (mis. "Duo" untuk 2 orang). Jangan perlakukan semua kamar sebagai 1 orang.
- **Kelayakan slot pakai kapasitas, bukan status semata**: gunakan `Kamar::hasAvailableSlot()`
  (`jumlah penghuni aktif < capacity`) untuk menentukan kamar bisa diisi. Kamar double dengan 1 penghuni
  **masih tersedia** dan boleh menerima penghuni ke-2.
- **Status kamar setelah menempatkan**: set `occupied` **hanya jika `isFull()`** (semua slot terisi),
  selain itu tetap `available`. Jadi Duo dengan 1 orang tetap `available`, dengan 2 orang baru `occupied`.
- **Cegah penghuni ganda**: sebelum `attach`, cek penyewa itu belum jadi penghuni aktif kamar tsb.
- **HARGA per-orang untuk kamar double** (paling sering terlewat!): untuk `capacity > 1`, biaya sewa
  per penyewa = **`$kamar->rent_per_person`** (di aplikasi ini = `price_per_month / 2`), BUKAN
  `price_per_month` penuh. Saat menghitung nominal transaksi/auto-fill, **pakai `rent_per_person`** agar
  penyewa kamar double tidak dikenai 2× lipat. (Accessor: `Kamar::getRentPerPersonAttribute()`.)
- **UI**: di dropdown pilih kamar, tampilkan sisa slot untuk kamar double, mis. `Kamar 900 — Duo (1/2 terisi)`
  dan harga `/org`, supaya operator tahu kamar itu terisi sebagian. (Kirim `withCount('occupants')` dari
  controller agar tak N+1.)

### Kriteria selesai
- Owner/admin bisa menambah penyewa baru dan (opsional) langsung menaruhnya di kamar kosong; kamar berubah
  jadi terisi; penyewa muncul di daftar penyewa & okupansi seperti penyewa hasil reservasi.

---

## FITUR C — Form Tambah jadi POP-UP MODAL

### Tujuan
Form "Tambah Kamar / Tambah Penyewa / Input Transaksi / Buat Laporan" ditampilkan sebagai **modal pop-up**
(seperti pola modal "Tambah Tipe Kamar" yang sudah ada), bukan pindah halaman.

### Logika & langkah
1. Buat helper global JS (di `resources/js/app.js` atau layout): `openModal(id)` / `closeModal(id)` yang
   toggle kelas `invisible opacity-0` pada elemen modal.
2. Untuk tiap form: buat partial modal berisi form (aksi POST tetap ke route store yang sama), tombol
   "Tambah" pada halaman index memanggil `openModal('...')`.
3. **Auto-buka modal saat validasi gagal**: bila `$errors` terkait form itu ada, jalankan `openModal(...)`
   pada `DOMContentLoaded` supaya user melihat pesan error tanpa kehilangan konteks.
4. Data untuk isi dropdown modal (tipe kamar, daftar kamar kosong, daftar penyewa) dikirim dari controller
   **index** halaman itu.

### Kriteria selesai
- Klik "Tambah X" → modal muncul; submit sukses → tersimpan & redirect; submit gagal → modal terbuka lagi
  dengan error.

---

## FITUR D — Tutorial interaktif per halaman (driver.js)

### Tujuan
Panduan/onboarding interaktif di tiap halaman panel (owner & admin): menyorot elemen & menjelaskan fitur,
otomatis muncul sekali untuk pengguna baru, plus tombol **"Panduan"** untuk memutar ulang.

### Logika & langkah
1. `npm install driver.js`; import di `app.js` + CSS-nya (di-bundle Vite, tanpa CDN).
2. Buat helper `window.KostTour` dengan:
   - `start(steps)` — jalankan tur; **buang step yang elemennya tidak ada** di halaman (agar tak error).
   - `auto(key, steps)` — jalankan sekali (disimpan di `localStorage`).
3. **PENTING — kunci per-user, bukan per-browser:** tambahkan `<meta name="user-id" content="{{ auth()->id() }}">`
   di layout, dan gunakan id itu di kunci localStorage (`tour_done_<userId>_<key>`). Tanpa ini, pengguna
   baru di browser yang sama tak akan dapat tur karena flag akun lain masih ada. **(Ini pelajaran penting —
   jangan pakai kunci per-browser saja.)**
4. Tambahkan tombol "Panduan" mengambang + slot `@stack('tour')` di layout owner & admin. Tiap halaman
   `@push('tour')` mendefinisikan langkah turnya (mis. dashboard = keliling menu sidebar; halaman lain =
   sorot tombol utama). Target elemen boleh pakai selektor `href` (mis. `a[href$="/owner/kamar"]`) agar
   tak perlu banyak edit.
5. Karena single-kos punya halaman ekstra (reservasi, AI Assistant), **buat juga tur untuk halaman-halaman
   itu** (mis. jelaskan alur verifikasi pembayaran, cara pakai AI Assistant).

### Kriteria selesai
- Halaman panel memunculkan tur otomatis sekali untuk user baru; tombol "Panduan" memutar ulang; pengguna
  baru di browser yang sama tetap dapat tur (kunci per-user).

---

## FITUR E — Checklist onboarding berurutan di dashboard

### Tujuan
Untuk owner yang **baru pertama masuk**, tampilkan kartu "Panduan Awal" berisi **langkah berurutan** apa
yang harus disiapkan dulu, agar tidak bolak-balik antar halaman.

### Logika & langkah
1. Di controller dashboard, hitung status tiap langkah **berbasis data nyata** (bukan localStorage), mis.:
   - Sudah ada tipe kamar? sudah ada kamar? sudah ada penyewa? sudah ada transaksi?
   - `$setupComplete` = 3 langkah inti (tipe kamar → kamar → penyewa) sudah ada.
2. View: kartu dengan checklist berurutan (progress bar, langkah berikutnya di-highlight). Tiap tombol
   **deep-link `?add=1`** ke halaman tujuan yang **langsung membuka modal** (nyambung ke Fitur C).
3. Kartu **hilang otomatis** saat setup inti selesai (data-driven → persist lintas device).
4. **Sinkron dengan tur (Fitur D):** saat halaman dibuka dengan `?add=1`, tur per-halaman **jangan** auto-jalan
   (agar tak bentrok dengan modal) dan jangan ditandai "seen". Masukkan juga ringkasan urutan langkah ini
   ke dalam tur dashboard.

### Urutan langkah yang disarankan (sesuaikan dgn single-kos)
`(1) Buat Tipe Kamar → (2) Tambah Kamar → (3) Tambah Penyewa → (4) Catat Transaksi`
Alasan urutan: kamar butuh tipe kamar dulu; penyewa butuh kamar; transaksi butuh penyewa+kamar.

### Kriteria selesai
- Owner baru (data kosong) melihat checklist; tiap langkah tercentang otomatis begitu dibuat; kartu hilang
  saat setup inti selesai; tombol langkah membuka modal yang tepat.

---

## FITUR F — Sidebar responsif mobile (off-canvas)

### Tujuan
Sidebar panel owner/admin yang di HP memakan ruang → ubah jadi **drawer off-canvas** (geser masuk +
backdrop) di layar kecil, tetap normal di desktop.

### Logika & langkah
1. Bungkus shell dengan Alpine `x-data="{ mobileOpen: false }"`.
2. Sidebar: di mobile `fixed inset-y-0 -translate-x-full z-40`, muncul saat `mobileOpen` (`translate-x-0`);
   di desktop `lg:relative lg:translate-x-0`. Tambah backdrop `bg-black/50 lg:hidden`.
3. Tambah tombol **hamburger** di topbar (`lg:hidden`) yang men-set `mobileOpen = true`.
4. Pastikan transisi transform mulus (tambahkan `transform` ke transisi CSS sidebar).

### Kriteria selesai
- Di HP sidebar tersembunyi & muncul lewat hamburger; di desktop tetap seperti biasa.

---

## FITUR G — MCP server + bearer token (kendali via AI agent)  [opsional/lanjutan]

### Tujuan
Memungkinkan **AI agent** (Claude Code, Codex, dll) mengelola kos lewat **MCP** dengan **bearer token**.

### Logika & langkah
1. `composer require laravel/mcp laravel/sanctum`. Publish + migrate tabel `personal_access_tokens`
   (Sanctum), tambahkan trait `HasApiTokens` ke model `User`.
2. Buat MCP Server (`Laravel\Mcp\Server`) + Tools (`Laravel\Mcp\Server\Tool`) untuk fitur manajemen
   (list/tambah kamar, list/tambah penyewa, list/tambah transaksi, generate laporan, ringkasan dashboard).
   Tiap tool mengambil user dari `$request->user()` dan beroperasi pada kos owner tsb.
3. Daftarkan endpoint HTTP di `routes/ai.php`: `Mcp::web('/mcp', ServerClass::class)->middleware('auth:sanctum');`
4. UI: halaman/section untuk **generate token** (`$user->createToken('mcp')->plainTextToken`, tampilkan
   SEKALI) + contoh konfigurasi `mcp.json` untuk agent.
5. Karena single-kos juga punya AI Assistant & reservasi, boleh tambahkan tool terkait bila relevan
   (mis. tool ringkasan reservasi) — tapi mulai dari tool manajemen inti dulu.

### Kriteria selesai
- `POST /mcp` tanpa token → 401; dengan bearer token → `tools/list` mengembalikan daftar tool; `tools/call`
  menjalankan aksi pada data kos.

---

## 2. URUTAN PENGERJAAN YANG DISARANKAN
1. **A** (owner = admin) — fondasi; banyak fitur lain bergantung ke aksi ini.
2. **B** (tambah penyewa → kamar) — pakai mekanisme okupansi yang ada.
3. **C** (modal) — sebelum E, karena checklist deep-link membuka modal.
4. **E** (checklist onboarding) → **D** (tutorial) — onboarding.
5. **F** (responsif) — cepat, kapan saja.
6. **G** (MCP) — paling besar/baru, kerjakan terakhir.

## 3. PENGINGAT LOGIKA (biar tidak salah)
- **Aditif saja**: setiap perubahan menambah, tidak menghapus alur lama (reservasi, AI, akun penyewa tetap).
- **Reuse, jangan duplikasi logika bisnis**: penempatan kamar & pembuatan laporan sebaiknya satu sumber
  (service/trait) dipakai owner & admin.
- **Sumber kebenaran okupansi** = pivot `riwayat_penghuni_kamar` (bukan kolom counter/`current_tenant_id`).
- **Kunci tur per-user** (meta `user-id`), bukan per-browser.
- **Status onboarding berbasis data** (query exists), bukan localStorage.
- Verifikasi tiap fitur end-to-end sebelum lanjut.
