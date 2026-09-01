<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Puntava a RouteServiceProvider::HOME ('/home'), una rotta
                // che qui non e' mai esistita: un utente gia' autenticato
                // che apriva /login riceveva un 404 invece di finire sulla
                // dashboard.
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
