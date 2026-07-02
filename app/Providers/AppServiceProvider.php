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
    }
}
