<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Turma;
use App\Models\Pagamento;
use App\Models\Contato;
use App\Models\Aviso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Plataforma MiScool (dono) — painel próprio, sem dados de escola
        if ($user->isProprietario()
            && config('database.connections.' . DB::getDefaultConnection() . '.database') === env('DB_DATABASE')
        ) {
            $totalEscolas = \App\Models\Escola::count();
            $escolasAtivas = \App\Models\Escola::where('ativa', true)->count();
            $escolasRecentes = \App\Models\Escola::orderByDesc('id')->take(5)->get();
            $totalUsuariosPlataforma = User::count();

            [$labelsGrafico, $dadosEscolasGrafico] = $this->contagemPorMes(\App\Models\Escola::query());
            [, $dadosUsuariosGrafico] = $this->contagemPorMes(User::query());

            return view('central.dashboard', compact(
                'totalEscolas',
                'escolasAtivas',
                'escolasRecentes',
                'totalUsuariosPlataforma',
                'labelsGrafico',
                'dadosEscolasGrafico',
                'dadosUsuariosGrafico'
            ));
        }

        // Totais gerais visíveis em todos os dashboards
        $data = [
            'user' => $user,
            'totalAlunos' => User::alunos()->count(),
            'totalProfessores' => User::professores()->count(),
            'totalTurmas' => Turma::count(),
            'totalDepartamentos' => \App\Models\Departamento::count(),
        ];

        // Professor (função pode ser principal OU adicional)
        if ($user->isProfessor()) {
            $data['turmasResponsaveis'] = $user->turmasResponsavel;
            $data['horarios'] = $user->horariosComoProfessor()->with('turma')->get();
        }

        // Aluno
        if ($user->isAluno()) {
            $data['notas'] = $user->notas()->where('ano_lectivo', date('Y'))->get();
            $data['pagamentos'] = $user->pagamentos()->where('ano', date('Y'))->get();
            $data['turma'] = $user->turma;
            $data['horarios'] = $user->turma?->horarios ?? collect();

            // Taxa de assiduidade (%) com base nas marcações dos professores no ano atual
            $marcacoes = $user->presencaAlunoMarcacoes()->where('ano', date('Y'))->get();
            $presentes = $marcacoes->where('estado', 'presente')->count();
            $faltas = $marcacoes->where('estado', 'falta')->count();
            $justificadas = $marcacoes->where('estado', 'justificada')->count();
            $data['taxaAssiduidade'] = ($presentes + $faltas) > 0
                ? round(($presentes / ($presentes + $faltas)) * 100, 1)
                : null;
            $data['assiduidade'] = compact('presentes', 'faltas', 'justificadas');

            // Pendências (pagamentos por pagar no ano atual)
            $data['pendencias'] = $data['pagamentos']
                ->whereIn('status', ['pendente', 'atrasado'])
                ->count();
        }

        // Financeiro (gestão financeira: admin, diretor, pctp, proprietario, financeiro)
        if ($user->hasRole('financeiro') || $user->isAdmin() || $user->isDiretor()) {
            $data['receitasMes'] = Pagamento::whereYear('data_pagamento', date('Y'))
                ->where('status', 'pago')
                ->selectRaw('mes, SUM(valor) as total')
                ->groupBy('mes')
                ->pluck('total', 'mes');
        }

        // Gestão
        if ($user->hasRole('admin') || $user->hasRole('diretor') || $user->hasRole('pctp') || $user->hasRole('financeiro') || $user->hasRole('proprietario')) {
            $data['pagamentosPendentes'] = Pagamento::where('status', 'pendente')->count();
        }

        if ($user->hasRole('admin') || $user->hasRole('diretor') || $user->hasRole('pctp') || $user->hasRole('proprietario')) {
            $data['mensagensNaoLidas'] = Contato::where('lido', false)->count();
        }

        // Auxiliar
        if ($user->isAuxiliar()) {
            $data['departamentos'] = $user->departamentos;
        }

        // Encarregado de educação (dashboard = centro de alertas)
        if ($user->isEncarregado()) {
            $perfil = $user->perfilEncarregado;
            $filhos = $perfil ? $perfil->alunos()->with([
                'turma',
                'presencas' => fn ($q) => $q->where('ano', date('Y'))->where('tipo', 'aluno'),
                'pagamentos' => fn ($q) => $q->where('ano', date('Y')),
            ])->get() : collect();
            $data['filhos'] = $filhos;

            $meses = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

            // Alertas de faltas (limiar configurável em Configurações → Sistema)
            $limiarFaltas = (int) \App\Models\Configuracao::obter('sistema.presenca_limiar_faltas', 3);
            $alertasFaltas = collect();
            foreach ($filhos as $filho) {
                foreach ($filho->presencas as $p) {
                    if ($p->faltas >= $limiarFaltas) {
                        $alertasFaltas->push((object) [
                            'filho' => $filho,
                            'mes' => $meses[$p->mes] ?? $p->mes,
                            'faltas' => $p->faltas,
                            'presencas' => $p->presencas,
                            'justificadas' => $p->justificadas,
                        ]);
                    }
                }
            }
            $data['alertasFaltas'] = $alertasFaltas;
            $data['totalFaltasAno'] = collect($filhos)->sum(fn ($f) => $f->presencas->sum('faltas'));

            // Alertas de pagamentos (pendentes/atrasados)
            $alertasPagamentos = collect();
            foreach ($filhos as $filho) {
                foreach ($filho->pagamentos->whereIn('status', ['pendente', 'atrasado']) as $pg) {
                    $alertasPagamentos->push((object) [
                        'filho' => $filho,
                        'mes' => $meses[$pg->mes] ?? $pg->mes,
                        'valor' => $pg->valor,
                        'status' => $pg->status,
                    ]);
                }
            }
            $data['alertasPagamentos'] = $alertasPagamentos;
            $data['dividaTotal'] = collect($filhos)
                ->sum(fn ($f) => $f->pagamentos->whereIn('status', ['pendente', 'atrasado'])->sum('valor'));

            $turmaIds = $filhos->pluck('turma_id')->filter()->values();
            $filhoIds = $filhos->pluck('id');

            $data['avisos'] = Aviso::where(function ($q) use ($turmaIds, $filhoIds) {
                $q->whereIn('destinatario_tipo', ['todos', 'alunos'])
                  ->orWhere(function ($q1) use ($turmaIds) {
                      $q1->where('destinatario_tipo', 'turma')->whereIn('turma_id', $turmaIds);
                  })
                  ->orWhere(function ($q2) use ($filhoIds) {
                      $q2->where('destinatario_tipo', 'individual')->whereIn('destinatario_id', $filhoIds);
                  });
            })->with('remetente', 'turma')->latest()->limit(15)->get();

            $data['avisosNaoLidos'] = $data['avisos']->filter(fn (Aviso $a) => !$a->foiLidoPor($user))->count();
        }

        return view('dashboard', $data);
    }

    /**
     * Contagem de registos criados nos últimos 12 meses, para gráficos.
     *
     * @return array{0: string[], 1: int[]}
     */
    private function contagemPorMes($query): array
    {
        $mesesCurtos = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $series = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->startOfMonth()->subMonths($i);
            $series[$d->format('Y-m')] = 0;
        }
        $porMes = $query->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total")
            ->where('created_at', '>=', now()->startOfMonth()->subMonths(11))
            ->groupBy('mes')
            ->get();
        foreach ($porMes as $reg) {
            if (isset($series[$reg->mes])) {
                $series[$reg->mes] = (int) $reg->total;
            }
        }
        $labels = array_map(fn ($k) => $mesesCurtos[(int) substr($k, 5, 2) - 1], array_keys($series));

        return [$labels, array_values($series)];
    }
}
