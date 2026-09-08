<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Configuracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracaoController extends Controller
{
    private const CAMPOS = [
        'escola' => [
            'nome' => 'nullable|string|max:150',
            'contacto' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'endereco' => 'nullable|string|max:255',
            'ano_letivo' => 'nullable|string|max:20',
            'moeda' => 'nullable|string|max:10',
            'propina_mensal' => 'nullable|numeric|min:0|max:99999999',
        ],
        'periodo' => [
            'matriculas_inicio' => 'nullable|date',
            'matriculas_fim' => 'nullable|date',
            'ferias_inverno_inicio' => 'nullable|date',
            'ferias_inverno_fim' => 'nullable|date',
            'ferias_verao_inicio' => 'nullable|date',
            'ferias_verao_fim' => 'nullable|date',
        ],
        'sistema' => [
            'avisos_obrigar_leitura' => 'nullable|in:0,1',
            'avisos_notificar_email' => 'nullable|in:0,1',
            'pagamentos_lembrete_dias' => 'nullable|integer|min:0|max:90',
            'turmas_limite_alunos' => 'nullable|integer|min:1|max:100',
            'presenca_limiar_faltas' => 'nullable|integer|min:1|max:30',
        ],
    ];

    public function index()
    {
        return view('configuracoes.index', ['configs' => Configuracao::todas()]);
    }

    public function update(Request $request)
    {
        $grupo = $request->input('grupo');

        if (!isset(self::CAMPOS[$grupo])) {
            abort(404, 'Grupo de configurações inválido.');
        }

        // Logotipo da escola (só no grupo escola) — guardado antes do resto
        if ($grupo === 'escola' && $request->hasFile('logotipo')) {
            $fl = $request->file('logotipo');
            \Illuminate\Support\Facades\Log::warning('Upload de logotipo', [
                'nome' => $fl->getClientOriginalName(),
                'tamanho' => $fl->getSize(),
                'erro' => $fl->getError(),
                'mime_cliente' => $fl->getClientMimeType(),
            ]);

            $request->validate(['logotipo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120']);

            $antigo = Configuracao::obter('escola.logotipo');
            $caminho = $request->file('logotipo')->store('escola', 'public');

            Configuracao::updateOrCreate(
                ['chave' => 'escola.logotipo'],
                ['valor' => $caminho, 'grupo' => 'escola']
            );

            if ($antigo && Storage::disk('public')->exists($antigo) && $antigo !== $caminho) {
                Storage::disk('public')->delete($antigo);
            }
        }

        $regras = self::CAMPOS[$grupo];
        $validated = $request->validate($regras);

        foreach ($validated as $campo => $valor) {
            $regra = $regras[$campo];

            if (str_contains($regra, 'in:0,1')) {
                $valor = $request->boolean($campo) ? 1 : 0;
            }

            Configuracao::updateOrCreate(
                ['chave' => $grupo . '.' . $campo],
                ['valor' => ($valor === null || $valor === '') ? null : (string) $valor, 'grupo' => $grupo]
            );
        }

        Configuracao::limparCache();

        Atividade::registar('update', 'Atualizou as configurações (' . ucfirst($grupo) . ')');

        return back()->with('success', 'Configurações guardadas com sucesso.');
    }
}