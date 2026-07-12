<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CreateKamarTool;
use App\Mcp\Tools\CreatePenyewaTool;
use App\Mcp\Tools\CreateTransaksiTool;
use App\Mcp\Tools\DashboardSummaryTool;
use App\Mcp\Tools\GenerateLaporanTool;
use App\Mcp\Tools\ListKamarTool;
use App\Mcp\Tools\ListPenyewaTool;
use App\Mcp\Tools\ListTipeKamarTool;
use App\Mcp\Tools\ListTransaksiTool;
use App\Mcp\Tools\UpdateKamarStatusTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('AgentBuff KostCloud')]
#[Version('1.0.0')]
#[Instructions(
    'Server MCP untuk manajemen kos AgentBuff KostCloud. Semua tool otomatis di-scope ' .
    'ke kos milik pemilik bearer token (owner/admin) — Anda hanya bisa melihat & mengubah ' .
    'data kos tersebut. Alur umum: pakai list-tipe-kamar & list-kamar sebelum create-kamar; ' .
    'pakai list-penyewa & list-kamar sebelum create-transaksi. create-penyewa hanya mendata ' .
    'penyewa; penyewa baru tidak bisa langsung ditempatkan ke kamar sebelum ada pembayaran, ' .
    'jadi penempatan dilakukan lewat create-transaksi yang mewajibkan pembayaran. ' .
    'dashboard-summary memberi ringkasan cepat. Nilai uang dalam Rupiah.'
)]
class KostCloudServer extends Server
{
    protected array $tools = [
        DashboardSummaryTool::class,
        ListKamarTool::class,
        ListTipeKamarTool::class,
        CreateKamarTool::class,
        UpdateKamarStatusTool::class,
        ListPenyewaTool::class,
        CreatePenyewaTool::class,
        ListTransaksiTool::class,
        CreateTransaksiTool::class,
        GenerateLaporanTool::class,
    ];

    protected array $resources = [];

    protected array $prompts = [];
}
