<?php

namespace App\Http\Controllers;

class AlunoAreaController extends Controller
{
    private const MESES = [
        1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
    ];

    public function notas()
    {
        $user = auth()->user();
        $notas = $user->notas()->where('ano_lectivo', date('Y'))->orderBy('trimestre')->orderBy('disciplina')->get();
        $trimestres = $notas->groupBy('trimestre');

        return view('aluno.notas', compact('notas', 'trimestres'));
    }

    public function horario()
    {
        $user = auth()->user();
        $user->load('turma');
        $dias = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'];
        $horarios = $user->turma
            ? $user->turma->horarios()->with('professor')->orderByRaw("FIELD(dia_semana, 'Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado','Domingo')")->orderBy('hora_inicio')->get()
            : collect();

        return view('aluno.horario', compact('horarios'));
    }

    public function pagamentos()
    {
        $user = auth()->user();
        $pagamentos = $user->pagamentos()->orderBy('ano')->orderBy('mes')->get();
        $meses = self::MESES;
        $totalPago = $pagamentos->where('status', 'pago')->sum('valor');
        $totalPendente = $pagamentos->whereIn('status', ['pendente', 'atrasado'])->sum('valor');

        return view('aluno.pagamentos', compact('pagamentos', 'meses', 'totalPago', 'totalPendente'));
    }

    public function presencas()
    {
        $user = auth()->user();
        $presencas = $user->presencas()->where('tipo', 'aluno')->orderBy('ano')->orderBy('mes')->get();
        $meses = self::MESES;

        $totalPresencas = $presencas->sum('presencas');
        $totalFaltas = $presencas->sum('faltas');
        $totalJustificadas = $presencas->sum('justificadas');
        $taxaAssiduidade = ($totalPresencas + $totalFaltas) > 0
            ? round(($totalPresencas / ($totalPresencas + $totalFaltas)) * 100, 1)
            : null;

        // Cards mensais por mês/ano (com base nas marcações dos professores)
        $mesFiltro = (int) request()->input('mes');
        $anoFiltro = (int) request()->input('ano') ?: now()->year;

        $anosDisponiveis = \App\Models\PresencaAlunoMarcacao::where('aluno_id', $user->id)
            ->distinct()
            ->orderByDesc('ano')
            ->pluck('ano')
            ->values()
            ->all();
        if (empty($anosDisponiveis)) {
            $anosDisponiveis = [now()->year];
        }

        $marcacoesQuery = $user->presencaAlunoMarcacoes()
            ->where('ano', $anoFiltro);

        $cardsMensais = collect($meses)->map(function ($nomeMes, $numMes) use ($marcacoesQuery, $mesFiltro, $anoFiltro) {
            $q = (clone $marcacoesQuery)->where('mes', $numMes);

            $presencasMes = (clone $q)->where('estado', 'presente')->count();
            $faltasMes = (clone $q)->where('estado', 'falta')->count();
            $justificadasMes = (clone $q)->where('estado', 'justificada')->count();
            $taxaMes = ($presencasMes + $faltasMes) > 0
                ? round(($presencasMes / ($presencasMes + $faltasMes)) * 100, 1)
                : null;

            return [
                'mes' => $numMes,
                'ano' => $anoFiltro,
                'nome' => $nomeMes,
                'presencas' => $presencasMes,
                'faltas' => $faltasMes,
                'justificadas' => $justificadasMes,
                'taxa' => $taxaMes,
                'temRegistos' => $presencasMes + $faltasMes + $justificadasMes > 0,
                'visivel' => $mesFiltro < 1 || $mesFiltro === $numMes,
            ];
        });

        return view('aluno.presencas', compact(
            'presencas', 'meses',
            'totalPresencas', 'totalFaltas', 'totalJustificadas', 'taxaAssiduidade',
            'cardsMensais', 'mesFiltro', 'anoFiltro', 'anosDisponiveis'
        ));
    }
}