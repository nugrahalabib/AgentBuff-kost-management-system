<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Database\Eloquent\Relations\Relation::enforceMorphMap([
            'transaction' => \App\Models\Transaksi::class,
            'generated_report' => \App\Models\Laporan::class,
            'user' => \App\Models\User::class,
            'room' => \App\Models\Kamar::class,
        ]);

        // Jumlah lantai kos (dinamis per kos) tersedia sebagai $floorCount di semua
        // view kamar — mengganti hardcode 4 lantai pada dropdown/tab/filter lantai.
        \Illuminate\Support\Facades\View::composer([
            'pemilik-kos.kamar', 'pemilik-kos.kamar-create',
            'admin.kamar', 'admin.kamar-create', 'partials.modal-kamar',
        ], function ($view) {
            $count = 4;
            if (auth()->check()) {
                $owner = auth()->user()->resolveOwner();
                $count = (int) ($owner?->businessSettings?->floor_count ?? 4);
            }
            $view->with('floorCount', max(1, $count));
        });
    }
}
