<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\IsStudent;
use App\Http\Middleware\IsTeacher;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Comportamento identico al default: nessun host esplicito, quindi
        // fida solo il dominio in APP_URL e i suoi sottodomini. E' un
        // no-op in ambiente 'local' e durante i test (lo fa gia' la classe
        // base), quindi non serve escluderlo qui.
        $middleware->trustHosts();

        // Nessun proxy fidato di default: se in produzione c'e' un reverse
        // proxy o una CDN davanti all'app, imposta TRUSTED_PROXIES in .env
        // con gli IP/CIDR di quel proxy (o '*' se la topologia lo garantisce).
        // Vedi Project Doc, Step 0, per cosa verificare prima del deploy.
        //
        // L'elenco IP non si legge qui con env(): questo file gira prima che
        // config:cache decida se caricare .env, quindi un env() qui dentro
        // smetterebbe di funzionare non appena la configurazione fosse
        // cachata in produzione. Il valore vive in config/trustedproxy.php,
        // che TrustProxies legge da solo a ogni richiesta.
        $middleware->trustProxies(
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_AWS_ELB,
        );

        // 'auth' e 'guest' hanno gia' un default nel framework: qui puntano
        // alle sottoclassi dell'app perche' Authenticate personalizza
        // redirectTo() e RedirectIfAuthenticated il redirect post-login.
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'isTeacher' => IsTeacher::class,
            'isStudent' => IsStudent::class,
        ]);

        // Password mai troncate dal trim: un'esclusione persa qui
        // significherebbe confrontare una password con spazi tagliati
        // silenziosamente contro l'hash di quella originale.
        $middleware->trimStrings(except: [
            'current_password',
            'password',
            'password_confirmation',
        ]);

        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
