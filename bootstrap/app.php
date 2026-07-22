<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Apply hardening headers to every response.
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Guest yang butuh auth diarahkan ke landing (modal login), bukan /login lama.
        $middleware->redirectGuestsTo(fn () => route('welcome', ['auth' => 'login']));
        $middleware->redirectUsersTo(fn () => route('dashboard'));

        // Trust the reverse proxy in front of the app (Nginx Proxy Manager, Cloudflare,
        // etc.) so X-Forwarded-Proto / Host headers are respected. Without this, Laravel
        // generates http:// URLs even when the public request was https://, and CSRF
        // / cookie / redirect logic breaks behind the proxy.
        $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR
            | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST
            | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT
            | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
            | \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'log.admin.activity' => \App\Http\Middleware\LogAdminActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
