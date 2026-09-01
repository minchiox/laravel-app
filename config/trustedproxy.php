<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Se in produzione un reverse proxy o una CDN sta davanti all'app,
    | elenca qui i suoi IP/CIDR (o '*' se la topologia lo garantisce, es.
    | proxy sullo stesso host e non raggiungibile dall'esterno). Vuoto =
    | nessun proxy fidato: gli header X-Forwarded-* non vengono considerati,
    | com'era prima di questa impostazione.
    |
    | Letto qui e non con env() direttamente in bootstrap/app.php perche'
    | quel file viene eseguito prima che config:cache decida se caricare
    | .env: un env() li' dentro smetterebbe di funzionare non appena la
    | configurazione fosse cachata in produzione.
    |
    */

    'proxies' => env('TRUSTED_PROXIES'),

];
