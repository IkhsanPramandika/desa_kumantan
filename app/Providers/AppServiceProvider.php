<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

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
        Carbon::setLocale('id'); // <-- TAMBAHKAN BARIS INI
    }
}