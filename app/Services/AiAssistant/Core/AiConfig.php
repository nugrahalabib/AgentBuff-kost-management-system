<?php

namespace App\Services\AiAssistant\Core;

class AiConfig
{
    // Model Configuration - Menggunakan Gemini 3 Pro Preview
    public const MODEL_NAME = 'gemini-3-pro-preview';
    public const FALLBACK_MODEL = 'gemini-2.5-pro';
    
    // Generation Config
    public const MAX_OUTPUT_TOKENS = 8192;
    public const TEMPERATURE = 0.4;
    
    // Context Limits
    public const MAX_HISTORY_MESSAGES = 15;
    public const MAX_TRANSACTION_ROWS = 50;
    
    /**
     * Get API Key from environment
     */
    public static function getApiKey(): string
    {
        return config('services.gemini.api_key') ?: env('GEMINI_API_KEY', '');
    }
}
