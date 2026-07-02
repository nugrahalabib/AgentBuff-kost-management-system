<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAssistant extends Model
{
    protected $table = 'ai_assistant';

    protected $fillable = [
        'ai_chat_session_id',
        'role',
        'content',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'ai_chat_session_id');
    }
}
