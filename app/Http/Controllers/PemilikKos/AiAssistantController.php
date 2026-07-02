<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Services\AiAssistant\Core\GeminiClient;
use App\Services\AiAssistant\Context\KnowledgeManager;
use App\Services\AiAssistant\Memory\MemoryManager;
use App\Services\AiAssistant\Prompt\PromptBuilder;

class AiAssistantController extends Controller
{
    protected $geminiClient;
    protected $knowledgeManager;
    protected $memoryManager;

    public function __construct(
        GeminiClient $geminiClient, 
        KnowledgeManager $knowledgeManager,
        MemoryManager $memoryManager
    )
    {
        $this->geminiClient = $geminiClient;
        $this->knowledgeManager = $knowledgeManager;
        $this->memoryManager = $memoryManager;
    }

    /**
     * Show AI Assistant page
     */
    public function index()
    {
        return view('pemilik-kos.ai-assistant');
    }

    /**
     * Handle chat message
     */
    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000',
                'session_id' => 'nullable|integer'
            ]);

            $userMessage = $request->input('message');
            $userId = Auth::id();
            $user = Auth::user();
            
            // 1. Set Owner Context for KnowledgeManager
            $this->knowledgeManager->setOwner($userId);
            $boardingHouseName = $this->knowledgeManager->getBoardingHouseName();
            
            // 2. Manage Session & Memory
            $sessionId = $request->input('session_id');
            if (!$sessionId) {
                $sessionId = $this->memoryManager->getOrCreateSession($userId, $userMessage);
            }

            // 3. Build Layered Context
            $knowledgeData = $this->knowledgeManager->getAggregatedContext();
            $chatHistory = $this->memoryManager->getFormattedHistory($sessionId);

            $promptBuilder = new PromptBuilder();
            $finalPrompt = $promptBuilder
                ->setBoardingHouseName($boardingHouseName)
                ->addIdentityLayer()
                ->addCoreContextLayer()
                ->addDateAwarenessLayer()
                ->addKnowledgeLayer($knowledgeData)
                ->addMemoryLayer($chatHistory)
                ->addUserContextLayer($user)
                ->addOutputFormatLayer()
                ->build();

            // Append the actual user question
            $finalPrompt .= "\n\n---\n\n# PERTANYAAN PENGGUNA:\n" . $userMessage;

            // 4. Call AI
            $result = $this->geminiClient->generateResponse($finalPrompt);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

            // 5. Save Interaction with timestamps
            $userMsgTime = now();
            $assistantMsgTime = now();
            
            $this->memoryManager->saveMessage($sessionId, 'user', $userMessage, $userMsgTime);
            $this->memoryManager->saveMessage($sessionId, 'assistant', $result['text'], $assistantMsgTime);

            return response()->json([
                'success' => true,
                'response' => $result['text'],
                'session_id' => $sessionId,
                'timestamp' => $assistantMsgTime->toIso8601String()
            ]);

        } catch (\Exception $e) {
            // Log structured data only — never the full stack trace, which may embed
            // request URLs containing API keys or other secrets.
            Log::error('AI Chat error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get all chat sessions for current user
     */
    public function getSessions()
    {
        $userId = Auth::id();
        $sessions = DB::table('ai_chat_sessions')
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->limit(30)
            ->get(['id', 'title', 'created_at', 'updated_at']);

        $sessions->transform(function ($s) {
            $s->time_ago = Carbon::parse($s->updated_at)->diffForHumans();
            $s->created_formatted = Carbon::parse($s->created_at)->format('d M Y H:i');
            return $s;
        });

        return response()->json([
            'success' => true,
            'sessions' => $sessions
        ]);
    }

    /**
     * Get messages for a specific session
     */
    public function getSessionMessages($id)
    {
        $userId = Auth::id();
        
        // Security: Ensure session belongs to user
        $session = DB::table('ai_chat_sessions')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak ditemukan'], 404);
        }

        $messages = DB::table('ai_assistant')
            ->where('ai_chat_session_id', $id)
            ->orderBy('created_at', 'asc')
            ->get(['role', 'content', 'created_at']);

        // Format timestamps
        $messages->transform(function ($m) {
            $m->timestamp = Carbon::parse($m->created_at)->toIso8601String();
            $m->time_ago = Carbon::parse($m->created_at)->diffForHumans();
            return $m;
        });

        return response()->json([
            'success' => true,
            'session' => $session,
            'messages' => $messages
        ]);
    }

    /**
     * Rename a session
     */
    public function renameSession(Request $request, $id)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $userId = Auth::id();

        $updated = DB::table('ai_chat_sessions')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update(['title' => $request->input('title'), 'updated_at' => now()]);

        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak ditemukan'], 404);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Delete a session and its messages
     */
    public function deleteSession($id)
    {
        $userId = Auth::id();

        $session = DB::table('ai_chat_sessions')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak ditemukan'], 404);
        }

        // Delete messages first (cascade)
        DB::table('ai_assistant')
            ->where('ai_chat_session_id', $id)
            ->delete();

        // Delete session
        DB::table('ai_chat_sessions')
            ->where('id', $id)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Health check for AI connection status
     */
    public function healthCheck()
    {
        $apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        
        return response()->json([
            'success' => true,
            'status' => !empty($apiKey) ? 'connected' : 'no_api_key',
            'message' => !empty($apiKey) ? 'AI Ready' : 'API Key belum dikonfigurasi'
        ]);
    }
}
