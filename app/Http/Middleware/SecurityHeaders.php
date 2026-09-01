<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Nessuno di questi header era presente: la risposta non dava al browser
 * alcuna istruzione difensiva oltre a quelle che il browser assume di
 * default. Da soli non chiudono nessuna falla applicativa, ma riducono
 * l'impatto di quelle che restano (es. nosniff avrebbe impedito a un
 * .svg caricato come avatar di essere interpretato come HTML).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Solo su richieste gia' https: un browser ignora comunque l'header
        // ricevuto in chiaro, ma restare condizionati evita l'header anche
        // in locale dove APP_URL e' http://localhost.
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Report-only: registra le violazioni (visibili nella console del
        // browser) senza bloccare nulla. La app ha ancora script inline nelle
        // view di quiz/esame e carica Bootstrap da jsdelivr: una CSP
        // enforcing oggi romperebbe quelle pagine. Diventa enforcing quando
        // quell'inline JS sara' sostituito dal frontend React (Fase 2, Step F2).
        $response->headers->set('Content-Security-Policy-Report-Only', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.bunny.net",
            "font-src 'self' https://fonts.bunny.net",
            "img-src 'self' data:",
            "connect-src 'self'",
            "frame-ancestors 'none'",
        ]));

        return $response;
    }
}
