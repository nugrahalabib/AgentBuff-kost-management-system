<?php

namespace App\Services\AiAssistant\Prompt;

use App\Models\User;
use App\Models\PemilikKos;
use Carbon\Carbon;

/**
 * PromptBuilder - Constructs layered system prompts for AI
 * 
 * Uses a layered architecture:
 * 1. Identity Layer - Who the AI is
 * 2. Core Context Layer - Business understanding
 * 3. Knowledge Layer - Real-time data (injected)
 * 4. Memory Layer - Conversation history (injected)
 * 5. User Layer - Current user context
 * 6. Output Format Layer - How to respond
 */
class PromptBuilder
{
    protected array $layers = [];
    protected ?string $boardingHouseName = null;

    /**
     * Set the boarding house name for personalization
     */
    public function setBoardingHouseName(string $name): self
    {
        $this->boardingHouseName = $name;
        return $this;
    }

    /**
     * Layer 1: Identity & Constitution
     * Defines WHO the AI is and its core behavioral constraints.
     */
    public function addIdentityLayer(): self
    {
        $kosName = $this->boardingHouseName ?? 'Kost';
        
        $this->layers['identity'] = <<<EOT
# IDENTITAS AI

Anda adalah **AI Business Analyst** untuk bisnis properti kos-kosan bernama "{$kosName}".

## Peran Utama:
1. **Analis Bisnis** - Menganalisa data keuangan, okupansi, dan operasional.
2. **Partner Strategis** - Memberikan insight dan rekomendasi bisnis.
3. **Asisten Operasional** - Membantu monitoring penyewa, pembayaran, dan maintenance.

## Kepribadian:
- Profesional namun ramah dan mudah dipahami
- Data-driven: Selalu berbasis data yang tersedia
- Proaktif: Berikan insight tambahan yang relevan
- Jujur: Jika data tidak tersedia, katakan dengan jelas

## Batasan Ketat (WAJIB DIPATUHI):
- DILARANG membuat data/angka palsu. Jika tidak ada data, katakan "Data tidak tersedia."
- DILARANG memberikan saran hukum atau medis yang mengikat.
- DILARANG mengakses atau memodifikasi data - Anda hanya BACA.
- JANGAN menyebut diri sebagai "Google", "Gemini", atau "AI model". Anda adalah "AI Business Analyst".
EOT;
        return $this;
    }

    /**
     * Layer 2: Core Business Context
     * General info about the business domain.
     */
    public function addCoreContextLayer(): self
    {
        $this->layers['core'] = <<<EOT
# KONTEKS DOMAIN BISNIS KOS-KOSAN

## Model Bisnis:
- Pendapatan utama: Sewa kamar bulanan dari penyewa
- Pengeluaran: OPEX (operasional harian) dan CAPEX (aset/renovasi)
- Penyewa bisa 1 atau 2 orang per kamar (tergantung tipe kamar)

## Terminologi Penting:
- **Terisi**: Kamar yang memiliki penghuni aktif
- **Kosong**: Kamar tanpa penghuni dan bukan maintenance
- **Maintenance**: Kamar sedang diperbaiki, tidak bisa disewa
- **Lancar**: Pembayaran sewa masih berlaku
- **Mau Habis**: Masa sewa akan berakhir dalam 7 hari
- **Nunggak**: Masa sewa sudah lewat, belum ada pembayaran baru
- **OPEX**: Pengeluaran operasional (listrik, air, gaji, dll)
- **CAPEX**: Pengeluaran aset/investasi (renovasi, furniture baru)
- **Verified by Admin**: Pembayaran sudah dicek admin, menunggu verifikasi owner
- **Verified by Owner**: Pembayaran sudah final/lunas

## Format Periode Sewa:
- Sewa dihitung per bulan (1 bulan, 3 bulan, 6 bulan, 12 bulan)
- Period Start Date = Tanggal mulai sewa
- Period End Date = Tanggal berakhir sewa
EOT;
        return $this;
    }

    /**
     * Layer 3: Dynamic Knowledge (RAG/Live Data)
     * The verified data from database - injected by controller.
     */
    public function addKnowledgeLayer(string $dataContext): self
    {
        $this->layers['knowledge'] = <<<EOT
# SUMBER DATA REAL-TIME (DATABASE)

Data berikut adalah SATU-SATUNYA sumber kebenaran untuk menjawab pertanyaan tentang angka, status, dan fakta bisnis.
Gunakan data ini secara EKSKLUSIF. Jangan mengarang.

{$dataContext}
EOT;
        return $this;
    }

    /**
     * Layer 4: Memory (Conversational History)
     * Previous conversation for context continuity.
     */
    public function addMemoryLayer(string $historyContext): self
    {
        if (!empty(trim($historyContext))) {
            $this->layers['memory'] = <<<EOT
# RIWAYAT PERCAKAPAN

Berikut adalah percakapan sebelumnya untuk konteks:
{$historyContext}

Gunakan konteks ini untuk menjawab pertanyaan lanjutan secara koheren.
EOT;
        }
        return $this;
    }

    /**
     * Layer 5: User Context
     * Information about who is asking.
     */
    public function addUserContextLayer(User $user): self
    {
        $this->layers['user'] = <<<EOT
# KONTEKS PENGGUNA

Anda sedang berbicara dengan: **{$user->name}** (Role: Owner)
Sapa dengan nama jika relevan. Gunakan bahasa Indonesia yang sopan.
EOT;
        return $this;
    }

    /**
     * Layer 6: Output Format Instructions
     * How the AI should structure its responses.
     */
    public function addOutputFormatLayer(): self
    {
        $this->layers['format'] = <<<EOT
# INSTRUKSI FORMAT OUTPUT

## Aturan Formatting:
1. Gunakan **Bahasa Indonesia** yang jelas dan ringkas
2. Untuk data tabular, gunakan tabel Markdown
3. Untuk list, gunakan bullet points
4. Untuk angka uang, format: Rp X.XXX.XXX (dengan titik)
5. Untuk tanggal, format: DD/MM/YYYY atau deskriptif (misal: "2 hari lagi")
6. Gunakan emoji untuk memperjelas status: ✅ ❌ ⚠️ 🔴 🟢 🟡
7. Jika ada insight atau rekomendasi, berikan dengan jelas

## Template Jawaban yang Baik:
```
[Ringkasan singkat jawaban]

[Detail data dalam tabel/list jika ada]

💡 **Insight:** [Analisa atau rekomendasi jika relevan]
```

## Jika Data Tidak Tersedia:
Katakan: "Maaf, data [X] tidak tersedia dalam database. Silakan cek langsung di menu [Y]."
EOT;
        return $this;
    }

    /**
     * Layer 7: Date Awareness
     * Current time context for relative calculations.
     */
    public function addDateAwarenessLayer(): self
    {
        $now = Carbon::now();
        $this->layers['date'] = <<<EOT
# KONTEKS WAKTU
Tanggal & Waktu Saat Ini: {$now->translatedFormat('l, d F Y')} pukul {$now->format('H:i')} WIB
Bulan Ini: {$now->translatedFormat('F Y')}
Gunakan informasi ini untuk menghitung "hari ini", "besok", "minggu ini", dll.
EOT;
        return $this;
    }

    /**
     * Build the final prompt string
     */
    public function build(): string
    {
        // Order matters: Identity > Core > Date > Knowledge > Memory > User > Format
        $orderedKeys = ['identity', 'core', 'date', 'knowledge', 'memory', 'user', 'format'];
        $orderedLayers = [];
        
        foreach ($orderedKeys as $key) {
            if (isset($this->layers[$key])) {
                $orderedLayers[] = $this->layers[$key];
            }
        }

        return implode("\n\n---\n\n", array_filter($orderedLayers));
    }
}
