<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Kelola bearer token MCP (Sanctum) untuk owner/admin. Token dipakai AI agent
 * (Claude Code, Codex, dll) mengakses server MCP AgentBuff KostCloud.
 */
class McpTokenController extends Controller
{
    public function index()
    {
        $tokens = auth()->user()->tokens()->latest()->get(['id', 'name', 'last_used_at', 'created_at']);
        $view = auth()->user()->role === 'admin' ? 'admin.mcp' : 'pemilik-kos.mcp';

        return view($view, [
            'tokens' => $tokens,
            'mcpUrl' => url('/mcp'),
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate(['name' => ['nullable', 'string', 'max:50']]);

        $plain = auth()->user()->createToken($request->input('name') ?: 'AI Agent')->plainTextToken;

        return back()
            ->with('mcp_new_token', $plain)
            ->with('success', 'Bearer token dibuat. Salin sekarang — token hanya ditampilkan sekali!');
    }

    public function revoke($id)
    {
        auth()->user()->tokens()->where('id', $id)->delete();

        return back()->with('success', 'Token dicabut.');
    }
}
