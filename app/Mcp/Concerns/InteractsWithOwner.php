<?php

namespace App\Mcp\Concerns;

use Laravel\Mcp\Request;

/**
 * Menentukan owner (pemilik kos) dari user pemilik bearer token MCP.
 * Owner → dirinya; admin → owner-nya. Semua tool MCP di-scope ke owner ini,
 * sama seperti panel web — jadi AI agent tak bisa menyentuh data kos lain.
 */
trait InteractsWithOwner
{
    protected function ownerId(Request $request): ?int
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'isManager') || ! $user->isManager()) {
            return null;
        }

        return $user->ownerId();
    }
}
