<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// 1. TAMBAHKAN BARIS INI
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 2. TAMBAHKAN BARIS INI DI DALAM METHOD BOOT
        Paginator::useBootstrap();
    }
}