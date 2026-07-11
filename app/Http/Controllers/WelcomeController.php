<?php

namespace App\Http\Controllers;

class WelcomeController extends Controller
{
    /**
     * Landing page publik AgentBuff KostCloud (SaaS marketing).
     * Statis — konten produk, bukan lagi CMS per-kos.
     */
    public function index()
    {
        return view('welcome');
    }
}
