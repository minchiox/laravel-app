<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        // Senza, un URL assoluto generato dietro un proxy che termina TLS
        // (o comunque non correttamente riconosciuto come https) risulta
        // http://: redirect e link mostrati all'utente su un dominio
        // pubblico non dovrebbero mai proporre lo schema in chiaro.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Trasferito da RouteServiceProvider, rimosso con lo skeleton
        // Kernel-based (Fase 2, Step M2).
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
