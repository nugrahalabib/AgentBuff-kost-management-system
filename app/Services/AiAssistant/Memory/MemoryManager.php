<?php

namespace App\Services\AiAssistant\Memory;

use Illuminate\Support\Facades\DB;
use App\Services\AiAssistant\Core\AiConfig;
use Carbon\Carbon;

/**
 * MemoryManager - Handles chat session and message persistence
 */
class MemoryManager
{
    /**
     * Creates a new session for a user
     */
    public function getOrCreateSession(int $userId, ?string $firstMessage = null): int
    {
        // Generate smart title from first message
        $title = $this->generateTitle($firstMessage);
        
        $id = DB::table('ai_chat_sessions')->insertGetId([
            'user_id' => $userId,
            'title' => $title,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return $id;
    }

    /**
     * Generate a smart title from the first message
     */
    protected function generateTitle(?string $message): string
    {
        if (!$message) {
            return 'Percakapan Baru';
        }

        // Clean and truncate
        $clean = trim($message);
        
        // If it's a question, keep the question mark
        if (strlen($clean) <= 40) {
            return $clean;
        }

        // Truncate at word boundary
        $truncated = substr($clean, 0, 37);
        $lastSpace = strrpos($truncated, ' ');
        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }
        
        return $truncated . '...';
    }

    /**
     * Save a message to a session with optional timestamp
     */
    public function saveMessage(int $sessionId, string $role, string $content, ?Carbon $timestamp = null): void
    {
        $now = $timestamp ?? now();
        
        DB::table('ai_assistant')->insert([
            'ai_chat_session_id' => $sessionId,
            'role' => $role,
            'content' => $content,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        // Update session's updated_at for sorting
        DB::table('ai_chat_sessions')
            ->where('id', $sessionId)
            ->update(['updated_at' => $now]);
    }

    /**
     * Get formatted chat history for AI context
     */
    public function getFormattedHistory(int $sessionId, int $limit = 15): string
    {
        // Get the N most recent messages, then reverse for chronological order
        $recentMessages = DB::table('ai_assistant')
            ->where('ai_chat_session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();

        if ($recentMessages->isEmpty()) {
            return '';
        }

        $output = [];
        foreach ($recentMessages as $msg) {
            $sender = $msg->role === 'user' ? 'Owner' : 'AI Assistant';
            $time = Carbon::parse($msg->created_at)->format('H:i');
            $output[] = "[{$time}] {$sender}: {$msg->content}";
        }

        return implode("\n\n", $output);
    }

    /**
     * Get raw messages for a session
     */
    public function getMessages(int $sessionId): \Illuminate\Support\Collection
    {
        return DB::table('ai_assistant')
            ->where('ai_chat_session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Delete all messages in a session
     */
    public function clearSession(int $sessionId): void
    {
        DB::table('ai_assistant')
            ->where('ai_chat_session_id', $sessionId)
            ->delete();
    }

    /**
     * Count messages in a session
     */
    public function countMessages(int $sessionId): int
    {
        return DB::table('ai_assistant')
            ->where('ai_chat_session_id', $sessionId)
            ->count();
    }
}
