<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Turma;
use App\Models\User;
use App\Models\Aviso;
use App\Models\Atividade;
use App\Models\Configuracao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    private const MESES = [
        1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
    ];

    /**
     * Lista de pagamentos com filtros.
     */
    public function index(Request $request)
    {
        $query = Pagamento::with(['aluno.turma', 'registrador'])->latest('data_pagamento');

        if ($request->filled('mes')) {
            $query->where('mes', $request->mes);
        }
        if ($request->filled('ano')) {
            $query->where('ano', $request->ano);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('turma_id')) {
            $query->whereHas('aluno', fn ($q) => $q->where('turma_id', $request->turma_id));
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('aluno', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('numero', 'like', "%{$search}%");
            });
        }

        $pagamentos = $query->paginate(20)->withQueryString();

        $ano = (int) ($request->input('ano') ?: now()->year);
        $mes = (int) ($request->input('mes') ?: now()->month);

        $stats = [
            'receitasMes' => Pagamento::where('status', 'pago')->where('mes', $mes)->where('ano', $ano)->sum('valor'),
            'receitasAno' => Pagamento::where('status', 'pago')->where('ano', $ano)->sum('valor'),
            'pendentes' => Pagamento::whereIn('status', ['pendente', 'atrasado'])->count(),
            'pagamentosMes' => Pagamento::where('status', 'pago')->where('mes', $mes)->where('ano', $ano)->count(),
        ];

        $meses = self::MESES;
        $turmas = Turma::orderBy('nome_turma')->get();

        return view('financeiro.pagamentos.index', compact('pagamentos', 'stats', 'meses', 'turmas', 'mes', 'ano'));
    }

    /**
     * Formulário para registar pagamento (recibo).
     */
    public function create()
    {
        $turmas = Turma::orderBy('nome_turma')->with('alunos')->get();
        $alunos = User::alunos()->with('turma')->orderBy('name')->get();
        $meses = self::MESES;
        $ano = now()->year;
        $mes = now()->month;

        return view('financeiro.pagamentos.create', compact('turmas', 'alunos', 'meses', 'ano', 'mes'));
    }

    /**
     * Regista o pagamento (gera recibo + notifica o encarregado).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'aluno_id' => 'required|exists:users,id',
            'mes' => 'required|integer|between:1,12',
            'ano' => 'required|integer|min:2020|max:2030',
            'valor' => 'required|numeric|min:0',
            'data_pagamento' => 'required|date',
            'metodo_pagamento' => 'required|string|max:50',
            'observacoes' => 'nullable|string|max:500',
            'enviar_notificacao' => 'nullable|boolean',
        ]);

        $aluno = User::alunos()->with('encarregado')->findOrFail($validated['aluno_id']);

        $pagamento = Pagamento::create([
            'aluno_id' => $aluno->id,
            'mes' => $validated['mes'],
            'ano' => $validated['ano'],
            'valor' => $validated['valor'],
            'data_pagamento' => $validated['data_pagamento'],
            'status' => 'pago',
            'metodo_pagamento' => $validated['metodo_pagamento'],
            'observacoes' => $validated['observacoes'] ?? null,
            'registrado_por' => auth()->id(),
        ]);

        $pagamento->update([
            'recibo_numero' => 'REC-' . $validated['ano'] . '-' . str_pad($validated['mes'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($pagamento->id, 4, '0', STR_PAD_LEFT),
        ]);
        $pagamento->refresh();

        if ($request->boolean('enviar_notificacao') && $aluno->encarregado?->user_id) {
            $this->notificarEncarregado($aluno, $pagamento);
        }

        Atividade::registar('create', "Registou o pagamento de {$aluno->name} ({$this->MESES[$validated['mes']]}/{$validated['ano']}, " . number_format($validated['valor'], 2, ',', ' ') . " Xof) - recibo {$pagamento->recibo_numero}", null, Pagamento::class, $pagamento->id, ['mes' => $validated['mes'], 'ano' => $validated['ano'], 'valor' => $validated['valor'], 'metodo' => $validated['metodo_pagamento'], 'notificacao' => $request->boolean('enviar_notificacao')]);

        session()->flash('success', 'Pagamento registado com sucesso! Recibo gerado.');

        return redirect()->route('financeiro.pagamentos.recibo', $pagamento);
    }

    /**
     * Vista do recibo (design interativo e moderno).
     */
    public function recibo(Pagamento $pagamento)
    {
        $pagamento->load(['aluno.turma', 'registrador']);

        // Encarregado só do próprio filho
        $this->autorizarRecibo($pagamento);

        $meses = self::MESES;
        $escola = (object) Configuracao::contacto();

        $resumoAno = $this->resumoAnoAluno($pagamento->aluno, $pagamento->ano);

        return view('financeiro.pagamentos.recibo', compact('pagamento', 'meses', 'escola', 'resumoAno'));
    }

    /**
     * PDF do recibo.
     */
    public function reciboPdf(Pagamento $pagamento)
    {
        $pagamento->load(['aluno.turma', 'registrador']);

        $this->autorizarRecibo($pagamento);

        $meses = self::MESES;
        $escola = (object) Configuracao::contacto();

        $resumoAno = $this->resumoAnoAluno($pagamento->aluno, $pagamento->ano);

        $pdf = Pdf::loadView('financeiro.pagamentos.recibo_pdf', compact('pagamento', 'meses', 'escola', 'resumoAno'))
            ->setPaper('a5', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        $nomeFicheiro = 'recibo_' . ($pagamento->recibo_numero ?? 'pagamento') . '.pdf';

        return $pdf->download($nomeFicheiro);
    }

    /**
     * Relatórios financeiros.
     */
    public function relatorios(Request $request)
    {
        $ano = (int) $request->input('ano', now()->year);
        $turmaId = $request->input('turma_id');

        $receitasQuery = Pagamento::where('status', 'pago')->where('ano', $ano);

        $receitasPorMes = (clone $receitasQuery)
            ->selectRaw('mes, SUM(valor) as total, COUNT(*) as quantidade')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        $receitasPorMetodo = (clone $receitasQuery)
            ->selectRaw('metodo_pagamento, SUM(valor) as total, COUNT(*) as quantidade')
            ->groupBy('metodo_pagamento')
            ->orderByDesc('total')
            ->get();

        $alunosQuery = User::alunos()->with(['turma', 'pagamentos' => fn ($q) => $q->where('ano', $ano)]);

        if ($turmaId) {
            $alunosQuery->where('turma_id', $turmaId);
        }

        $meses = self::MESES;
        $turmas = Turma::orderBy('nome_turma')->get();

        $alunos = $alunosQuery->orderBy('name')->get()->map(function (User $aluno) use ($ano, $meses) {
            $propinaMensal = (float) ($aluno->turma?->propina_mensal ?? 0);
            $totalAno = (float) ($aluno->turma?->propina_anual ?? 0);
            $pago = $aluno->pagamentos->where('status', 'pago')->sum('valor');
            $pendente = $aluno->pagamentos->whereIn('status', ['pendente', 'atrasado'])->sum('valor');

            return [
                'aluno' => $aluno,
                'pago' => $pago,
                'pendente' => $pendente,
                'totalAno' => $totalAno,
                'restante' => max($totalAno - $pago, 0),
                'percentagem' => $totalAno > 0 ? round(min(($pago / $totalAno) * 100, 100), 1) : 0,
            ];
        });

        $totais = [
            'receitasAno' => (clone $receitasQuery)->sum('valor'),
            'receitasMes' => Pagamento::where('status', 'pago')->where('ano', $ano)->where('mes', now()->month)->sum('valor'),
            'alunosEmDia' => $alunos->where('restante', 0)->count(),
            'alunosTotal' => $alunos->count(),
        ];

        return view('financeiro.relatorios.index', compact('alunos', 'receitasPorMes', 'receitasPorMetodo', 'meses', 'turmas', 'ano', 'turmaId', 'totais'));
    }

    /**
     * Notifica o encarregado sobre o pagamento (aviso individual).
     */
    private function notificarEncarregado(User $aluno, Pagamento $pagamento): void
    {
        $encarregadoUser = $aluno->encarregado->user;

        Aviso::create([
            'titulo' => 'Pagamento registado · ' . self::MESES[$pagamento->mes] . '/' . $pagamento->ano,
            'mensagem' => 'Recebemos o pagamento de ' . number_format((float) $pagamento->valor, 2, ',', ' ') . ' Xof relativo ao mês de ' . self::MESES[$pagamento->mes] . ' de ' . $pagamento->ano . ' do aluno ' . $aluno->name . '. Recibo: ' . ($pagamento->recibo_numero ?? ''),
            'remetente_id' => auth()->id(),
            'destinatario_tipo' => 'individual',
            'destinatario_id' => $encarregadoUser->id,
        ]);
    }

    /**
     * Autoriza a visualização do recibo (financeiro/admin/diretor, o próprio aluno ou o encarregado).
     */
    private function autorizarRecibo(Pagamento $pagamento): void
    {
        $user = auth()->user();

        if ($user->isFinanceiro() || $user->isAdmin() || $user->isDiretor()) {
            return;
        }

        if ($user->isAluno() && $pagamento->aluno_id === $user->id) {
            return;
        }

        $filhos = $user->perfilEncarregado?->alunos()->pluck('id');

        if ($user->isEncarregado() && $filhos && $filhos->contains($pagamento->aluno_id)) {
            return;
        }

        abort(403, 'Não autorizado a visualizar este recibo.');
    }

    /**
     * Resumo de pagamentos do aluno no ano (pago/restante).
     */
    private function resumoAnoAluno(User $aluno, int $ano): array
    {
        $propinaMensal = (float) ($aluno->turma?->propina_mensal ?? 0);
        $totalAno = (float) ($aluno->turma?->propina_anual ?? 0);

        $pago = $aluno->pagamentos()->where('ano', $ano)->where('status', 'pago')->sum('valor');

        return [
            'totalAno' => $totalAno,
            'pago' => $pago,
            'restante' => max($totalAno - $pago, 0),
            'percentagem' => $totalAno > 0 ? round(min(($pago / $totalAno) * 100, 100), 1) : 0,
        ];
    }
}
