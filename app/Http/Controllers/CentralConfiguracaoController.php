<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CentralConfiguracaoController extends Controller
{
    public function index()
    {
        $valores = [
            'nome' => Configuracao::obter('plataforma.nome', 'MiScool — Gestão Escolar'),
            'email' => Configuracao::obter('plataforma.email', ''),
            'telefone' => Configuracao::obter('plataforma.telefone', ''),
            'endereco' => Configuracao::obter('plataforma.endereco', ''),
            'logotipo' => Configuracao::obter('plataforma.logotipo', null),
            'fundo' => Configuracao::obter('plataforma.fundo', null),
        ];

        return view('central.configuracoes.index', compact('valores'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'telefone' => 'nullable|string|max:30',
            'endereco' => 'nullable|string|max:200',
            'logotipo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'remover_logotipo' => 'nullable|in:0,1',
            'fundo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
            'remover_fundo' => 'nullable|in:0,1',
        ]);

        $this->guardarImagem($request, $validated, 'logotipo', 'plataforma.logotipo', 'remover_logotipo');
        $this->guardarImagem($request, $validated, 'fundo', 'plataforma.fundo', 'remover_fundo');

        $mapa = [
            'nome' => 'plataforma.nome',
            'email' => 'plataforma.email',
            'telefone' => 'plataforma.telefone',
            'endereco' => 'plataforma.endereco',
        ];

        foreach ($mapa as $campo => $chave) {
            Configuracao::updateOrCreate(
                ['chave' => $chave],
                ['valor' => $validated[$campo] ?? '', 'grupo' => 'plataforma']
            );
        }

        Configuracao::limparCache();

        return redirect()->route('central.configuracoes.index')
            ->with('success', 'Configuração da plataforma guardada com sucesso!');
    }

    /**
     * Guarda (ou remove) uma imagem da plataforma — logotipo ou fundo.
     */
    private function guardarImagem(Request $request, array $validated, string $campo, string $chave, string $campoRemover): void
    {
        if ($request->boolean($campoRemover)) {
            $antigo = Configuracao::obter($chave);
            if ($antigo && Storage::disk('public')->exists($antigo)) {
                Storage::disk('public')->delete($antigo);
            }
            Configuracao::updateOrCreate(
                ['chave' => $chave],
                ['valor' => null, 'grupo' => 'plataforma']
            );

            return;
        }

        if ($request->hasFile($campo)) {
            $fl = $request->file($campo);
            \Illuminate\Support\Facades\Log::warning('Upload de imagem da plataforma (' . $campo . ')', [
                'nome' => $fl->getClientOriginalName(),
                'tamanho' => $fl->getSize(),
                'erro' => $fl->getError(),
                'mime_cliente' => $fl->getClientMimeType(),
            ]);

            $antigo = Configuracao::obter($chave);
            $caminho = $fl->store('plataforma', 'public');

            Configuracao::updateOrCreate(
                ['chave' => $chave],
                ['valor' => $caminho, 'grupo' => 'plataforma']
            );

            if ($antigo && Storage::disk('public')->exists($antigo) && $antigo !== $caminho) {
                Storage::disk('public')->delete($antigo);
            }
        }
    }
}