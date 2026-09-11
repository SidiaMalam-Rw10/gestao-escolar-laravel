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

        $ano = now()->year;
        $propinaMensal = (float) ($user->turma?->propina_mensal ?? 0);
        $totalAno = (float) ($user->turma?->propina_anual ?? 0);
        $pagoAno = $pagamentos->where('ano', $ano)->where('status', 'pago')->sum('valor');

        $resumoAno = [
            'totalAno' => $totalAno,
            'pago' => $pagoAno,
            'restante' => max($totalAno - $pagoAno, 0),
            'percentagem' => $totalAno > 0 ? round(min(($pagoAno / $totalAno) * 100, 100), 1) : 0,
        ];

        return view('aluno.pagamentos', compact('pagamentos', 'meses', 'totalPago', 'totalPendente', 'resumoAno', 'ano'));
    }

    public function pwa()
    {
        $user = auth()->user();
        $user->load('turma');

        $turma = $user->turma;
        $anoLetivo = $turma?->ano_lectivo ?? date('Y');

        // Média global (ano letivo) — média da nota (mg) de todas as disciplinas
        $notas = $user->notas()->where('ano_lectivo', $anoLetivo)->get();
        $media = $notas->avg('mg');
        $media = $media !== null ? round((float) $media, 1) : null;

        // Situação financeira (ano civil corrente)
        $pagamentos = $user->pagamentos()->where('ano', date('Y'))->orderByDesc('created_at')->get();
        $divida = $pagamentos->whereIn('status', ['pendente', 'atrasado'])->sum('valor');
        $situacaoFinanceira = $divida > 0 ? 'Pendente' : 'Regularizado';

        // Assiduidade (%) — marcações dos professores no ano
        $marcacoes = $user->presencaAlunoMarcacoes()->where('ano', date('Y'))->get();
        $presentes = $marcacoes->where('estado', 'presente')->count();
        $faltas = $marcacoes->where('estado', 'falta')->count();
        $taxaAssiduidade = ($presentes + $faltas) > 0
            ? round(($presentes / ($presentes + $faltas)) * 100, 1)
            : null;

        // Aulas de hoje
        $diasSemana = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
        $diaHoje = $diasSemana[now()->dayOfWeekIso - 1] ?? 'Segunda';
        $nomeDiaHoje = $diaHoje;
        $aulasHoje = $turma
            ? $turma->horarios()->with('professor')->where('dia_semana', $diaHoje)->orderBy('hora_inicio')->get()
            : collect();

        // Avisos relevantes (sino + lista de atualizações)
        $avisos = $user->avisosRelevantesQuery()->with('remetente')->latest()->take(8)->get();
        $avisosNaoLidos = $avisos->filter(fn ($a) => !$a->foiLidoPor($user))->count();

        // Feed "Últimas Atualizações" — notas, pagamentos e avisos recentes
        $atualizacoes = collect();

        foreach ($notas->sortByDesc('created_at')->take(4) as $n) {
            if (!$n->created_at) {
                continue;
            }
            $atualizacoes->push((object) [
                'tipo' => 'nota',
                'titulo' => 'Nota — ' . $n->disciplina,
                'subtitulo' => ($n->trimestre . 'º Trimestre · ' . $n->created_at->format('d M Y')),
                'valor' => '+ ' . number_format((float) $n->mg, 1),
                'valor_estilo' => 'text-emerald-400',
                'icone_estilo' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                'created_at' => $n->created_at,
            ]);
        }

        foreach ($pagamentos->take(4) as $p) {
            $data = $p->data_pagamento ?? $p->created_at;
            $atualizacoes->push((object) [
                'tipo' => 'pagamento',
                'titulo' => 'Propina de ' . (self::MESES[$p->mes] ?? $p->mes),
                'subtitulo' => ($p->status_label . ' · ' . ($data?->format('d M Y') ?? $p->ano)),
                'valor' => $p->status === 'pago'
                    ? 'Confirmado'
                    : number_format((float) $p->valor, 0, ',', '.') . ' FCFA',
                'valor_estilo' => $p->status === 'pago' ? 'text-emerald-400' : 'text-orange-400',
                'icone_estilo' => $p->status === 'pago'
                    ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400'
                    : 'bg-orange-500/10 border-orange-500/20 text-orange-400',
                'created_at' => $data ?? $p->created_at ?? now(),
            ]);
        }

        foreach ($avisos as $a) {
            $atualizacoes->push((object) [
                'tipo' => 'aviso',
                'titulo' => $a->titulo,
                'subtitulo' => ($a->created_at?->format('d M Y') ?? 'Aviso escolar'),
                'valor' => $a->foiLidoPor($user) ? 'Lido' : 'Novo',
                'valor_estilo' => $a->foiLidoPor($user) ? 'text-neutral-500' : 'text-amber-400',
                'icone_estilo' => $a->foiLidoPor($user)
                    ? 'bg-zinc-800 border-zinc-700 text-neutral-400'
                    : 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                'created_at' => $a->created_at ?? now(),
            ]);
        }

        $atualizacoes = $atualizacoes->sortByDesc('created_at')->take(8)->values();

        // Reutiliza o layout web para o avatar/ligação ao sistema
        return view('aluno.pwa', compact(
            'user', 'turma', 'anoLetivo', 'media', 'divida', 'situacaoFinanceira',
            'taxaAssiduidade', 'aulasHoje', 'diaHoje', 'nomeDiaHoje',
            'avisos', 'avisosNaoLidos', 'atualizacoes'
        ));
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