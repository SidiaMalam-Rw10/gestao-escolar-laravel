<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GarantirPrimeiraPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->primeiro_login) {
            if (! $request->routeIs('password.primeira') && ! $request->routeIs('password.primeira.alterar')) {
                return redirect()->route('password.primeira');
            }
        }

        return $next($request);
    }
}