<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Fitur AI Assistant (Gemini) dihapus dari AgentBuff KostCloud.
 * Kontrol AI kini lewat MCP, bukan asisten chat internal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ai_assistant');       // sebelumnya ai_chat_messages
        Schema::dropIfExists('ai_chat_sessions');
    }

    public function down(): void
    {
        // Fitur AI sudah dihapus permanen; tidak dibuat ulang.
    }
};
