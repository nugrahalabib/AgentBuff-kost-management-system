<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gerbang entitlement marketplace AgentBuff (sisi klien).
 *
 * KostCloud dijual sebagai produk di marketplace AgentBuff. Hanya orang yang
 * akses AgentBuff-nya LIVE (langganan OP Buff / trial aktif) DAN yang MEMILIKI
 * produk ini yang boleh menjalankan kos di sini. Modul ini bertanya ke endpoint
 * AgentBuff `/api/partner/entitlement` apakah sebuah email berhak.
 *
 * PRODUCTION-FIT: URL, secret, dan product key semuanya dari env — pindah ke
 * AgentBuff produksi (https://agentbuff.id/...) cukup ganti config, tanpa ubah
 * kode. Fail SAFE:
 *   • gate LOGIN (checkEntitlementStrict) selalu cek fresh + tolak bila ragu —
 *     owner yang lapse tak bisa masuk lagi.
 *   • cek berkala (checkEntitlement, untuk freeze) pakai cache pendek + grace saat
 *     AgentBuff mati sesaat, jadi blip sebentar tak membekukan pelanggan yang bayar,
 *     sedangkan lapse asli membekukan dalam rentang TTL cache.
 * Data TIDAK PERNAH disentuh gate ini — hanya mengizinkan/menolak akses.
 */
class AgentBuffGate
{
    /** detik — cek ulang owner maksimal tiap 10 menit. */
    private const TTL = 600;

    /** detik — saat AgentBuff mati, percayai "entitled" terakhir hingga 1 jam. */
    private const GRACE = 3600;

    private const TIMEOUT = 8;

    public function enabled(): bool
    {
        return ! config('services.agentbuff.gate_disabled')
            && filled(config('services.agentbuff.entitlement_url'))
            && filled(config('services.agentbuff.partner_secret'));
    }

    public function productKey(): string
    {
        return (string) config('services.agentbuff.product_key', 'kostcloud');
    }

    /** Panggil AgentBuff. Return array entitlement, atau null bila tak terjangkau. */
    private function fetch(string $email): ?array
    {
        try {
            $res = Http::timeout(self::TIMEOUT)
                ->withHeaders(['x-partner-secret' => (string) config('services.agentbuff.partner_secret')])
                ->acceptJson()
                ->post((string) config('services.agentbuff.entitlement_url'), [
                    'email' => $email,
                    'productKey' => $this->productKey(),
                ]);
            if (! $res->successful()) {
                return null;
            }
            $data = $res->json();

            return is_array($data) && array_key_exists('entitled', $data) ? $data : null;
        } catch (\Throwable $e) {
            Log::warning('AgentBuff entitlement fetch gagal: '.$e->getMessage());

            return null;
        }
    }

    private function cacheKey(string $email): string
    {
        return 'agentbuff:ent:'.strtolower(trim($email));
    }

    /** Cek berkala (freeze): cache + grace agar blip AgentBuff tak membekukan pembayar. */
    public function checkEntitlement(string $email): array
    {
        if (! $this->enabled()) {
            return ['entitled' => true, 'reason' => 'gate_disabled'];
        }
        $key = $this->cacheKey($email);
        $cached = Cache::get($key);
        if (is_array($cached) && isset($cached['res'], $cached['at']) && (time() - $cached['at']) < self::TTL) {
            return $cached['res'];
        }
        $fresh = $this->fetch(strtolower(trim($email)));
        if ($fresh !== null) {
            Cache::put($key, ['res' => $fresh, 'at' => time()], self::GRACE);

            return $fresh;
        }
        // AgentBuff tak terjangkau — pertahankan owner yang baru saja "entitled" (grace),
        // selain itu tolak (fail closed).
        if (is_array($cached) && ! empty($cached['res']['entitled']) && (time() - $cached['at']) < self::GRACE) {
            return $cached['res'];
        }

        return ['entitled' => false, 'reason' => 'gate_unreachable'];
    }

    /** Gate login: selalu fresh, tanpa grace — owner yang lapse ditolak di pintu. */
    public function checkEntitlementStrict(string $email): array
    {
        if (! $this->enabled()) {
            return ['entitled' => true, 'reason' => 'gate_disabled'];
        }
        $fresh = $this->fetch(strtolower(trim($email)));
        if ($fresh !== null) {
            Cache::put($this->cacheKey($email), ['res' => $fresh, 'at' => time()], self::GRACE);

            return $fresh;
        }

        return ['entitled' => false, 'reason' => 'gate_unreachable'];
    }

    /** Pesan Bahasa untuk tiap reason (ditampilkan ke user). */
    public function message(string $reason): string
    {
        return match ($reason) {
            'not_registered' => 'Email ini belum terdaftar di AgentBuff. Daftar dulu di AgentBuff lalu beli KostCloud di Marketplace.',
            'not_purchased' => 'Kamu belum membeli KostCloud di Marketplace AgentBuff. Beli dulu produknya untuk bisa mengakses.',
            'access_lapsed' => 'Langganan / trial AgentBuff kamu sudah tidak aktif. Aktifkan lagi di AgentBuff untuk melanjutkan. Data kamu tetap tersimpan.',
            'gate_unreachable' => 'Tidak bisa memverifikasi akses ke AgentBuff saat ini. Coba lagi sebentar.',
            default => 'Akses ditolak. Pastikan akun AgentBuff kamu aktif dan sudah membeli KostCloud.',
        };
    }
}
