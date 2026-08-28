<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificaRuolo
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$ruoli): Response
    {
        if (! $request->user() || ! in_array($request->user()->ruolo, $ruoli, true)) {
            abort(403, 'Non hai i permessi per accedere a questa pagina.');
        }

        return $next($request);
    }
}
