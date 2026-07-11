{{-- Panel MCP / bearer token. Butuh: $routePrefix ('owner'|'admin'), $tokens, $mcpUrl --}}
<div class="max-w-3xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    {{-- Token baru (ditampilkan sekali) --}}
    @if(session('mcp_new_token'))
        <div class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-5" x-data="{ copied: false }">
            <p class="font-bold text-amber-900 mb-2">🔑 Bearer token baru — salin sekarang, tidak akan ditampilkan lagi!</p>
            <div class="flex items-center gap-2">
                <code class="flex-1 bg-white border border-amber-200 rounded-lg px-3 py-2 text-xs break-all font-mono" x-ref="tok">{{ session('mcp_new_token') }}</code>
                <button type="button"
                    @click="navigator.clipboard.writeText($refs.tok.innerText); copied = true; setTimeout(() => copied = false, 2000)"
                    class="px-3 py-2 rounded-lg bg-amber-600 text-white text-xs font-bold hover:bg-amber-700 whitespace-nowrap">
                    <span x-show="!copied">Salin</span><span x-show="copied" x-cloak>✓ Tersalin</span>
                </button>
            </div>
        </div>
    @endif

    {{-- Info & cara pakai --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-lg text-gray-800">Kontrol via AI Agent (MCP)</h3>
            <p class="text-sm text-gray-500 mt-1">Hubungkan Claude Code, Codex, Hermes, OpenClaw, dll. untuk mengelola kos ini lewat AI.</p>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">URL Server MCP</label>
                <code class="block bg-gray-900 text-emerald-300 rounded-lg px-3 py-2 text-sm font-mono break-all">{{ $mcpUrl }}</code>
            </div>

            <div>
                <p class="text-xs font-bold text-gray-500 uppercase mb-1">Contoh konfigurasi (mcp.json)</p>
                <pre class="bg-gray-900 text-emerald-100 rounded-lg p-4 text-xs overflow-x-auto"><code>{
  "mcpServers": {
    "kostcloud": {
      "type": "http",
      "url": "{{ $mcpUrl }}",
      "headers": { "Authorization": "Bearer &lt;TOKEN_ANDA&gt;" }
    }
  }
}</code></pre>
            </div>

            <form action="{{ route($routePrefix . '.mcp.generate') }}" method="POST" class="flex flex-col sm:flex-row gap-3 pt-2">
                @csrf
                <input type="text" name="name" placeholder="Nama token (mis. Claude Code laptop)" maxlength="50"
                    class="flex-1 px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition text-sm">
                <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-bold text-sm hover:bg-emerald-700 transition whitespace-nowrap">
                    + Generate Token
                </button>
            </form>
        </div>
    </div>

    {{-- Daftar token aktif --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-lg text-gray-800">Token Aktif ({{ $tokens->count() }})</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($tokens as $t)
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <p class="font-bold text-gray-800 text-sm">{{ $t->name }}</p>
                        <p class="text-xs text-gray-400">
                            Dibuat {{ $t->created_at->translatedFormat('d M Y') }} ·
                            {{ $t->last_used_at ? 'terakhir dipakai ' . $t->last_used_at->diffForHumans() : 'belum pernah dipakai' }}
                        </p>
                    </div>
                    <form action="{{ route($routePrefix . '.mcp.revoke', $t->id) }}" method="POST" onsubmit="return confirm('Cabut token ini? AI agent yang memakainya akan kehilangan akses.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-bold px-3 py-2 rounded-lg bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition">Cabut</button>
                    </form>
                </div>
            @empty
                <p class="px-6 py-8 text-center text-sm text-gray-400">Belum ada token. Generate satu untuk mulai menghubungkan AI agent.</p>
            @endforelse
        </div>
    </div>
</div>
