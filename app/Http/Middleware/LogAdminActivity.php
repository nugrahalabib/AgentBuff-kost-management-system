<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\LoggerService;

class LogAdminActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Execute request first
        $response = $next($request);

        // Only log for authenticated admins and GET requests (viewing pages)
        // We exclude AJAX requests usually, but logging "View Modal" via AJAX might be useful too.
        // For "buka halaman", mainly GET.
        
        if (Auth::check() && Auth::user()->role === 'admin' && $request->isMethod('GET')) {
            // Avoid logging asset requests or some utility routes if needed
            // For now log everything under /admin prefix
            
            $path = $request->path();
            
            // Map path to readable name
            $pageName = $this->getPageName($path);
            
            if ($pageName) {
                 LoggerService::log(
                    'access',
                    'Membuka halaman: ' . $pageName,
                    null // No specific model, just page view
                );
            }
        }

        return $response;
    }

    private function getPageName($path)
    {
        if ($path === 'admin/dashboard') return 'Dashboard';
        if (str_contains($path, 'admin/kamar')) return 'Data Kamar';
        if (str_contains($path, 'admin/penyewa')) return 'Data Penyewa';
        if (str_contains($path, 'admin/akun-penyewa')) return 'Akun Penyewa';
        if (str_contains($path, 'admin/transaksi')) return 'Data Transaksi';
        if (str_contains($path, 'admin/laporan')) return 'Laporan';
        if (str_contains($path, 'admin/konten')) return 'Kelola Konten';
        if (str_contains($path, 'admin/notifikasi')) return 'Notifikasi';
        
        return null; // Don't log unknown or minor pages to avoid noise
    }
}
