<?php

namespace App\Http\Controllers;

use App\Models\PresencaAlunoMarcacao;
use App\Models\Turma;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AuxiliarAlunoPresencaController extends Controller
{
    private const MESES = [
        1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
    ];

    /**
     * Relatório de presenças/faltas dos alunos por turma (apenas leitura).
     */
    public function index(Request $request)
    {
        $mes = (int) $request->query('mes', now()->month);
        $ano = (int) $request->query('ano', now()->year);

        if ($mes < 1) { $mes = 1; }
        if ($mes > 12) { $mes = 12; }

        $turmas = Turma::orderBy('nome_turma')->get();

        $turma = null;
        $alunos = collect();
        $marcacoes = collect();

        if ($request->filled('turma_id')) {
            $turma = Turma::with('professorResponsavel')->find((int) $request->turma_id);

            if ($turma) {
                $alunos = $turma->alunos()->orderBy('name')->get();

                $marcacoes = PresencaAlunoMarcacao::where('turma_id', $turma->id)
                    ->where('mes', $mes)
                    ->where('ano', $ano)
                    ->get()
                    ->groupBy('aluno_id');
            }
        }

        $meses = self::MESES;

        $anosDisponiveis = PresencaAlunoMarcacao::distinct()->orderByDesc('ano')->pluck('ano')->values()->all();
        if (empty($anosDisponiveis)) {
            $anosDisponiveis = [now()->year];
        }

        return view('auxiliar.presencas_alunos.index', compact(
            'turmas', 'turma', 'alunos', 'marcacoes', 'mes', 'ano', 'meses', 'anosDisponiveis'
        ));
    }

    /**
     * PDF com o total de faltas/presenças dos alunos de uma turma.
     */
    public function pdf(Request $request, Turma $turma)
    {
        $mes = $request->filled('mes') ? (int) $request->query('mes') : null;
        $ano = $request->filled('ano') ? (int) $request->query('ano') : now()->year;

        $turma->load('professorResponsavel');
        $alunos = $turma->alunos()->orderBy('name')->get();

        $query = PresencaAlunoMarcacao::where('turma_id', $turma->id)
            ->where('ano', $ano);

        if ($mes) {
            $query->where('mes', $mes);
        }

        $marcacoes = $query->get()->groupBy('aluno_id');

        $linhas = $alunos->map(function ($aluno) use ($marcacoes) {
            $m = $marcacoes->get($aluno->id, collect());

            return [
                'aluno' => $aluno,
                'presencas' => $m->where('estado', 'presente')->count(),
                'faltas' => $m->where('estado', 'falta')->count(),
                'justificadas' => $m->where('estado', 'justificada')->count(),
                'taxa' => ($m->where('estado', 'presente')->count() + $m->where('estado', 'falta')->count()) > 0
                    ? round(($m->where('estado', 'presente')->count() / ($m->where('estado', 'presente')->count() + $m->where('estado', 'falta')->count())) * 100, 1)
                    : null,
            ];
        });

        $totalPresencas = $linhas->sum('presencas');
        $totalFaltas = $linhas->sum('faltas');
        $totalJustificadas = $linhas->sum('justificadas');

        $meses = self::MESES;
        $tituloPeriodo = $mes ? ($meses[$mes] . ' de ' . $ano) : ('Ano ' . $ano);

        $progenitor = auth()->user();

        $pdf = Pdf::loadView('auxiliar.presencas_alunos.pdf', compact(
            'turma', 'alunos', 'linhas',
            'totalPresencas', 'totalFaltas', 'totalJustificadas',
            'meses', 'mes', 'ano', 'tituloPeriodo', 'progenitor'
        ));

        $nomeFicheiro = 'faltas_alunos_' . str_replace(' ', '_', strtolower($turma->nome_turma)) . '_' . ($mes ?? 'ANUAL') . '_' . $ano . '.pdf';

        return $pdf->download($nomeFicheiro);
    }
}