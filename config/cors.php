<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // '*' andava bene finche' l'unica rotta era '/api/user' senza credenziali,
    // ma diventa un problema reale il giorno in cui l'API si allarga. Di
    // default si restringe alla sola origine dell'app (frontend e backend
    // sono la stessa applicazione, non serve altro); CORS_ALLOWED_ORIGINS in
    // .env permette di aggiungerne altre, separate da virgola.
    'allowed_origins' => array_values(array_filter(explode(
        ',',
        (string) env('CORS_ALLOWED_ORIGINS', env('APP_URL', ''))
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
