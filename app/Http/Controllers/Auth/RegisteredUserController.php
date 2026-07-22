<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Pendaftaran email/password sudah dinonaktifkan.
 * Owner hanya daftar/masuk lewat Google (lihat GoogleController).
 */
class RegisteredUserController extends Controller
{
    public function create(): RedirectResponse
    {
        return redirect()->route('welcome', ['auth' => 'register']);
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('welcome', ['auth' => 'register'])
            ->with('error', 'Pendaftaran hanya tersedia lewat Google.');
    }
}
