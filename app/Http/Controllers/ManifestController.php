<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ManifestController extends Controller
{
    public function index(): JsonResponse
    {
        $nome = (string) (Configuracao::obter('escola.nome', 'No Skola') ?: 'No Skola');

        return response()
            ->json([
                'name' => $nome,
                'short_name' => Str::limit($nome, 12, '…'),
                'description' => 'Sistema de Gestão Escolar No Skola',
                'lang' => 'pt',
                'start_url' => '/',
                'scope' => '/',
                'display' => 'standalone',
                'orientation' => 'portrait',
                'theme_color' => '#0F1311',
                'background_color' => '#0B0F0D',
                'categories' => ['education', 'productivity'],
                'icons' => [
                    ['src' => '/pwa/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                    ['src' => '/pwa/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                    ['src' => '/pwa/maskable-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
                ],
            ], 200, [], JSON_UNESCAPED_SLASHES)
            ->header('Content-Type', 'application/manifest+json');
    }
}