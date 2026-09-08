<?php

namespace App\Http\Middleware;

use App\Models\Escola;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class DeterminarEscola
{
    public function handle(Request $request, Closure $next): Response
    {
        $raiz = (string) parse_url(config('app.url'), PHP_URL_HOST);
        $host = $request->getHost();

        if (str_starts_with($host, 'www.')) {
            $host = mb_substr($host, 4);
        }

        $prefix = '.' . $raiz;

        if ($host === $raiz || $host === 'localhost' || ! Str::endsWith($host, $prefix)) {
            $conn = config('database.default');

            if ((string) config("database.connections.{$conn}.database") !== (string) env('DB_DATABASE')) {
                config(["database.connections.{$conn}.database" => env('DB_DATABASE')]);
                DB::purge($conn);
                DB::setDefaultConnection($conn);
            }

            return $next($request);
        }

        $slug = Str::before($host, $prefix);

        $escola = Escola::where('slug', $slug)->where('ativa', true)->first();

        if (! $escola) {
            abort(404, 'Escola não encontrada ou desativada.');
        }

        $conn = config('database.default');

        config(["database.connections.{$conn}.database" => $escola->nome_bd]);
        DB::purge($conn);
        DB::setDefaultConnection($conn);

        $request->attributes->set('escola_atual', $escola);

        return $next($request);
    }
}