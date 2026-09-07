<?php

namespace App\Http\Controllers;

use App\Models\PresencaMarcacao;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AuxiliarPresencaController extends Controller
{
    private const MESES = [
        1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
    ];

    /**
     * Folha mensal com botões de ação (presente / falta / justificada / desfazer) + PDF por linha.
     */
    public function index(Request $request)
    {
        $mes = (int) $request->query('mes', now()->month);
        $ano = (int) $request->query('ano', now()->year);

        if ($mes < 1) { $mes = 1; }
        if ($mes > 12) { $mes = 12; }

        $meses = self::MESES;

        $professores = User::professores()->orderBy('name')->get();
        $profIds = $professores->pluck('id');

        $marcacoesMes = PresencaMarcacao::whereIn('user_id', $profIds)
            ->where('mes', $mes)
            ->where('ano', $ano)
            ->orderBy('data')
            ->orderBy('hora')
            ->get()
            ->groupBy('user_id');

        $hoje = now()->toDateString();
        $marcacaoHoje = PresencaMarcacao::whereIn('user_id', $profIds)
            ->where('data', $hoje)
            ->latest()
            ->get()
            ->keyBy('user_id');

        $totalPresentesMes = $marcacoesMes->flatten(1)->where('estado', 'presente')->count();
        $totalFaltasMes = $marcacoesMes->flatten(1)->where('estado', 'falta')->count();
        $totalJustificadasMes = $marcacoesMes->flatten(1)->where('estado', 'justificada')->count();
        $marcadosHoje = $marcacaoHoje->count();

        return view('auxiliar.presencas_professores.index', compact(
            'mes', 'ano', 'meses',
            'professores', 'marcacoesMes', 'marcacaoHoje', 'hoje',
            'totalPresentesMes', 'totalFaltasMes', 'totalJustificadasMes', 'marcadosHoje'
        ));
    }

    /**
     * Regista uma marcação (presente / falta / justificada) com data e hora atuais.
     * Ao justificar uma falta, converte a falta existente em falta justificada (deixa de contar como falta).
     */
    public function marcar(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'estado' => 'required|in:presente,falta,justificada',
            'data' => 'required|date',
        ]);

        $data = \Illuminate\Support\Carbon::parse($validated['data']);
        $userId = (int) $validated['user_id'];

        if ($validated['estado'] === 'justificada') {
            $falta = PresencaMarcacao::where('user_id', $userId)
                ->where('estado', 'falta')
                ->where('data', $data->toDateString())
                ->orderByDesc('hora')
                ->first();

            if ($falta) {
                $falta->update([
                    'estado' => 'justificada',
                    'hora' => now()->format('H:i:s'),
                ]);

                return back()->with('success', "Falta de " . now()->format('d/m/Y') . ' convertida em falta justificada. Deixou de contar como falta.');
            }
        }

        PresencaMarcacao::create([
            'user_id' => $userId,
            'estado' => $validated['estado'],
            'data' => $data->toDateString(),
            'hora' => now()->format('H:i:s'),
            'mes' => $data->month,
            'ano' => $data->year,
        ]);

        $label = PresencaMarcacao::ESTADOS[$validated['estado']] ?? $validated['estado'];

        return back()->with('success', "Marcação registada: {$label} em {$data->format('d/m/Y')} às " . now()->format('H:i') . '.');
    }

    /**
     * Desfaz a última marcação do professor.
     */
    public function desfazer(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $ultima = PresencaMarcacao::where('user_id', (int) $validated['user_id'])
            ->latest('data')
            ->latest('hora')
            ->first();

        if (! $ultima) {
            return back()->with('error', 'Não existe nenhuma marcação para desfazer.');
        }

        $data = \Illuminate\Support\Carbon::parse($ultima->data)->format('d/m/Y');
        $ultima->delete();

        return back()->with('success', "Marcação de {$data} removida com sucesso.");
    }

    /**
     * Histórico completo de marcações (data e hora) de cada professor.
     */
    public function historico(Request $request)
    {
        $query = PresencaMarcacao::query()->with('user');

        if ($request->filled('professor')) {
            $query->where('user_id', (int) $request->query('professor'));
        }

        if ($request->filled('mes')) {
            $query->where('mes', (int) $request->query('mes'));
        }

        if ($request->filled('ano')) {
            $query->where('ano', (int) $request->query('ano'));
        }

        $filtroProfessor = $request->filled('professor') ? User::professores()->find((int) $request->query('professor')) : null;

        $marcacoes = $query->orderBy('data', 'desc')->orderBy('hora', 'desc')->get();

        $meses = self::MESES;
        $professores = User::professores()->orderBy('name')->get();

        $agrupados = $marcacoes->count() > 0 ? $marcacoes->groupBy('user_id') : collect();

        $totalPresentes = $marcacoes->where('estado', 'presente')->count();
        $totalFaltas = $marcacoes->where('estado', 'falta')->count();
        $totalJustificadas = $marcacoes->where('estado', 'justificada')->count();
        $diasRegistados = $marcacoes->pluck('data')->unique()->count();

        return view('auxiliar.presencas_professores.historico', compact(
            'marcacoes', 'agrupados', 'meses', 'professores',
            'filtroProfessor', 'totalPresentes', 'totalFaltas', 'totalJustificadas', 'diasRegistados'
        ));
    }

    /**
     * Gera o PDF com o histórico de marcações (data e hora) de um professor.
     */
    public function pdf(Request $request, User $professor)
    {
        $mes = $request->filled('mes') ? (int) $request->query('mes') : null;
        $ano = $request->filled('ano') ? (int) $request->query('ano') : now()->year;

        $query = PresencaMarcacao::where('user_id', $professor->id);

        if ($mes) {
            $query->where('mes', $mes);
        }
        $query->where('ano', $ano);

        $marcacoes = $query->orderBy('data')->orderBy('hora')->get();

        $totalPresentes = $marcacoes->where('estado', 'presente')->count();
        $totalFaltas = $marcacoes->where('estado', 'falta')->count();
        $totalJustificadas = $marcacoes->where('estado', 'justificada')->count();

        $meses = self::MESES;
        $tituloPeriodo = $mes ? ($meses[$mes] . ' de ' . $ano) : ('Ano ' . $ano);

        $progenitor = auth()->user();

        $pdf = Pdf::loadView('auxiliar.presencas_professores.pdf', compact(
            'professor', 'marcacoes',
            'totalPresentes', 'totalFaltas', 'totalJustificadas',
            'meses', 'mes', 'ano', 'tituloPeriodo', 'progenitor'
        ));

        $nomeFicheiro = 'presencas_' . str_replace(' ', '_', strtolower($professor->name)) . '_' . ($mes ?? 'ANUAL') . '_' . $ano . '.pdf';

        return $pdf->download($nomeFicheiro);
    }
}