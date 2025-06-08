<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'), // Default guard untuk web (petugas)
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'), // Default password broker untuk petugas
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session", "sanctum" (atau "token" untuk Passport)
    |
    */

    'guards' => [
        'web' => [ // Guard untuk petugas desa (login via web session)
            'driver' => 'session',
            'provider' => 'users', // Menggunakan provider 'users'
        ],

        'sanctum' => [ // Guard untuk API (misalnya untuk aplikasi mobile Flutter)
            'driver' => 'sanctum',
            'provider' => 'masyarakat_auth', // Menggunakan provider 'masyarakat_auth' untuk API masyarakat
                                        // Jika API juga untuk petugas, Anda mungkin perlu guard Sanctum lain
                                        // atau logika tambahan untuk membedakan user dan masyarakat.
                                        // Untuk saat ini, kita fokus pada masyarakat.
        ],
        
        // Anda bisa menambahkan guard 'api_masyarakat' jika ingin lebih eksplisit untuk API masyarakat
        // 'api_masyarakat' => [
        //     'driver' => 'sanctum', // atau 'passport' jika menggunakan Laravel Passport
        //     'provider' => 'masyarakat_auth',
        //     // 'hash' => false, // Jika menggunakan Passport dan token tidak di-hash
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | If you have multiple user tables or models you may configure multiple
    | providers to represent the model / table. These providers may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [ // Provider untuk petugas desa (tabel 'users')
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        'masyarakat_auth' => [ // Provider baru untuk masyarakat (tabel 'masyarakat')
            'driver' => 'eloquent',
            'model' => App\Models\Masyarakat::class, // Pastikan path ke model Masyarakat benar
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    'passwords' => [
        'users' => [ // Password broker untuk petugas (tabel 'users')
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'masyarakat_reset' => [ // Password broker baru untuk masyarakat
            'provider' => 'masyarakat_auth', // Menggunakan provider 'masyarakat_auth'
            'table' => 'masyarakat_password_reset_tokens', // Tabel token reset untuk masyarakat
            'expire' => 60, // Menit
            'throttle' => 60, // Detik
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
