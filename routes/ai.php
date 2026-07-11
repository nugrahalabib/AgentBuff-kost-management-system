<?php

use App\Mcp\Servers\KostCloudServer;
use Laravel\Mcp\Facades\Mcp;

/*
|--------------------------------------------------------------------------
| MCP Server AgentBuff KostCloud
|--------------------------------------------------------------------------
| Endpoint HTTP untuk AI agent (Claude Code, Codex, dll). Autentikasi via
| bearer token Sanctum yang di-generate owner/admin di menu Pengaturan → MCP.
| Semua tool otomatis di-scope ke kos milik pemilik token.
*/
Mcp::web('/mcp', KostCloudServer::class)
    ->middleware(['auth:sanctum']);
