<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // [PERBAIKAN KUNCI] Mengubah cara inisialisasi Firebase
        // agar sesuai dengan versi terbaru library kreait/laravel-firebase.
        $this->app->singleton(Factory::class, function ($app) {
            // Ambil path ke file service account JSON dari file .env
            // Pastikan Anda memiliki GOOGLE_APPLICATION_CREDENTIALS di .env
            $serviceAccountPath = config('services.firebase.credentials');

            if (!$serviceAccountPath) {
                throw new \Exception('Firebase credentials file path not set in config/services.php or .env file.');
            }

            return (new Factory)->withServiceAccount($serviceAccountPath);
        });

        // Mendaftarkan service 'firebase.messaging'
        $this->app->singleton('firebase.messaging', function ($app) {
            return $app->make(Factory::class)->createMessaging();
        });

        // Mendaftarkan alias untuk Messaging class
        $this->app->alias('firebase.messaging', Messaging::class);
    }
    
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
