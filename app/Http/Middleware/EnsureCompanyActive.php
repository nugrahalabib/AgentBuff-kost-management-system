<?php

namespace App\Http\Middleware;

use App\Services\AgentBuffGate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bekukan panel / MCP bila akses AgentBuff milik OWNER-nya lapse. Owner → email
 * dirinya; admin → email owner-nya (User::resolveOwner). Data TIDAK dihapus —
 * hanya akses yang diblokir. Reaktivasi di AgentBuff mengangkat freeze pada cek
 * berikutnya (maksimal seukuran TTL cache gate).
 */
class EnsureCompanyActive
{
    public function __construct(private AgentBuffGate $gate) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->gate->enabled()) {
            return $next($request);
        }

        $user = $request->user();
        $owner = ($user && method_exists($user, 'resolveOwner')) ? $user->resolveOwner() : null;
        $ownerEmail = $owner?->email;

        // Tak bisa resolve owner (user tak biasa) — jangan hard-block; biarkan
        // guard/route lain yang menentukan. Gate ini khusus soal langganan owner.
        if (! $ownerEmail) {
            return $next($request);
        }

        $res = $this->gate->checkEntitlement($ownerEmail);
        if (empty($res['entitled'])) {
            $reason = $res['reason'] ?? 'access_lapsed';
            if ($request->expectsJson() || $request->is('mcp') || $request->is('mcp/*')) {
                return response()->json([
                    'error' => 'company_frozen',
                    'reason' => $reason,
                    'message' => $this->gate->message($reason),
                ], 403);
            }
            abort(403, $this->gate->message($reason));
        }

        return $next($request);
    }
}
