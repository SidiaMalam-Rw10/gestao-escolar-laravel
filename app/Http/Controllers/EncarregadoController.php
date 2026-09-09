<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Encarregado;
use App\Models\Atividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EncarregadoController extends Controller
{
    private function validarEncarregado(Request $request, ?Encarregado $encarregado = null): array
    {
        $rules = [
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'endereco' => 'nullable|string|max:255',
            'genero' => 'nullable|in:M,F',
            'parentesco' => 'nullable|string|max:100',
            'criar_conta' => 'nullable|boolean',
        ];

        // Conta de acesso do encarregado
        $temConta = $encarregado ? (bool) $encarregado->user_id : false;
        if ($request->boolean('criar_conta') || $request->filled('username')) {
            $rules['username'] = [
                'required', 'string', 'max:255',
                Rule::unique('users', 'username')->ignore($temConta ? $encarregado->user_id : null),
            ];
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }

        if ($request->filled('remover_acesso')) {
            $rules['remover_acesso'] = 'nullable|boolean';
        }

        return $request->validate($rules);
    }

    private function criarContaAcesso(Encarregado $encarregado, Request $request): void
    {
        if (!$request->filled('username')) {
            return;
        }

        if ($encarregado->user) {
            return;
        }

        $dados = [
            'name' => $encarregado->nome,
            'username' => $request->username,
            'password' => Hash::make($request->input('password', '')),
            'role' => 'encarregado',
            'email' => $encarregado->email && $this->emailDisponivel($encarregado->email) ? $encarregado->email : null,
            'telefone' => $encarregado->telefone,
            'is_active' => true,
            'primeiro_login' => true,
        ];

        $user = User::create($dados);
        $encarregado->update(['user_id' => $user->id]);
    }

    private function atualizarContaAcesso(Encarregado $encarregado, Request $request): void
    {
        $utilizador = $encarregado->user;

        // Remover acesso
        if ($request->boolean('remover_acesso') && $utilizador) {
            $encarregado->update(['user_id' => null]);
            $utilizador->delete();
            return;
        }

        if (!$request->filled('username')) {
            return;
        }

        if (!$utilizador) {
            $this->criarContaAcesso($encarregado, $request);
            return;
        }

        $dados = ['username' => $request->username];
        if ($request->filled('password')) {
            $dados['password'] = Hash::make($request->password);
        }
        $utilizador->update($dados);
    }

    private function emailDisponivel(?string $email): bool
    {
        if (!$email) {
            return false;
        }
        return !User::where('email', $email)->exists();
    }
    public function index(Request $request)
    {
        $query = Encarregado::query()->withCount('alunos');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('telefone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'com_alunos') {
                $query->has('alunos');
            } elseif ($request->status === 'sem_alunos') {
                $query->doesntHave('alunos');
            }
        }

        $encarregados = $query->orderBy('nome')->paginate(15)->withQueryString();

        $stats = [
            'total' => Encarregado::count(),
            'com_alunos' => Encarregado::has('alunos')->count(),
            'sem_alunos' => Encarregado::doesntHave('alunos')->count(),
        ];

        return view('encarregados.index', compact('encarregados', 'stats'));
    }

    public function create()
    {
        return view('encarregados.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validarEncarregado($request);

        $encarregado = Encarregado::create($validated);
        $this->criarContaAcesso($encarregado, $request);

        Atividade::registar('create', "Criou o encarregado '{$encarregado->nome}'", null, Encarregado::class, $encarregado->id, ['telemovel' => $encarregado->telemovel, 'conta_acesso' => $encarregado->user ? 'sim' : 'nao']);

        $mensagem = $encarregado->user
            ? 'Encarregado de educação criado com conta de acesso!'
            : 'Encarregado de educação criado com sucesso!';

        return redirect()->route('admin.encarregados.show', $encarregado)->with('success', $mensagem);
    }

    public function show(Encarregado $encarregado)
    {
        $encarregado->load(['user', 'alunos.turma']);
        $alunosDisponiveis = User::alunos()->whereNull('encarregado_id')->orderBy('name')->get();
        return view('encarregados.show', compact('encarregado', 'alunosDisponiveis'));
    }

    public function edit(Encarregado $encarregado)
    {
        return view('encarregados.edit', compact('encarregado'));
    }

    public function update(Request $request, Encarregado $encarregado)
    {
        $validated = $this->validarEncarregado($request, $encarregado);

        $encarregado->update($validated);
        $this->atualizarContaAcesso($encarregado, $request);

        Atividade::registar('update', "Atualizou o encarregado '{$encarregado->nome}'", null, Encarregado::class, $encarregado->id, ['remover_acesso' => $request->boolean('remover_acesso')]);

        $mensagem = $request->boolean('remover_acesso')
            ? 'Encarregado atualizado e acesso removido!'
            : 'Encarregado de educação atualizado com sucesso!';

        return redirect()->route('admin.encarregados.show', $encarregado)->with('success', $mensagem);
    }

    public function destroy(Encarregado $encarregado)
    {
        // Desassocia todos os alunos antes de eliminar
        $nome = $encarregado->nome;
        User::where('encarregado_id', $encarregado->id)->update(['encarregado_id' => null]);

        $utilizador = $encarregado->user;
        if ($utilizador) {
            $utilizador->delete(); // cascade remove o encarregado
        } else {
            $encarregado->delete();
        }

        Atividade::registar('delete', "Eliminou o encarregado '{$nome}'", null, Encarregado::class, $encarregado->id);

        return redirect()->route('admin.encarregados.index')->with('success', 'Encarregado de educação eliminado com sucesso!');
    }

    public function associarAluno(Request $request, Encarregado $encarregado)
    {
        $validated = $request->validate([
            'aluno_id' => 'required|exists:users,id',
        ]);

        $aluno = User::findOrFail($validated['aluno_id']);

        if ($aluno->encarregado_id && $aluno->encarregado_id !== $encarregado->id) {
            return back()->withErrors(['aluno_id' => 'Este aluno já tem outro encarregado de educação.']);
        }

        $aluno->update(['encarregado_id' => $encarregado->id]);

        Atividade::registar('update', "Associou o aluno '{$aluno->name}' ao encarregado '{$encarregado->nome}'", null, User::class, $aluno->id, ['encarregado_id' => $encarregado->id]);

        return back()->with('success', 'Aluno associado ao encarregado com sucesso!');
    }

    public function desassociarAluno(Encarregado $encarregado, User $aluno)
    {
        if ($aluno->encarregado_id === $encarregado->id) {
            $aluno->update(['encarregado_id' => null]);
        }

        Atividade::registar('update', "Desassociou o aluno '{$aluno->name}' do encarregado '{$encarregado->nome}'", null, User::class, $aluno->id);

        return back()->with('success', 'Aluno desassociado do encarregado!');
    }

    private function perfilAtual(): ?Encarregado
    {
        return auth()->user()?->perfilEncarregado;
    }

    private function validarFilho(User $aluno): Encarregado
    {
        $perfil = $this->perfilAtual();
        abort_unless($perfil && $perfil->alunos()->whereKey($aluno->id)->exists(), 403, 'Não tem acesso a este aluno.');

        return $perfil;
    }

    public function filhoNotas(User $aluno)
    {
        $perfil = $this->validarFilho($aluno);
        $filhos = $perfil->alunos()->orderBy('name')->get();
        $notas = $aluno->notas()->where('ano_lectivo', date('Y'))->orderBy('trimestre')->orderBy('disciplina')->get();
        $trimestres = $notas->groupBy('trimestre');

        return view('encarregados.filho_notas', compact('aluno', 'filhos', 'notas', 'trimestres'));
    }

    public function filhoHorario(User $aluno)
    {
        $perfil = $this->validarFilho($aluno);
        $filhos = $perfil->alunos()->orderBy('name')->get();
        $dias = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'];
        $aluno->load('turma');
        $horarios = $aluno->turma
            ? $aluno->turma->horarios()->with('professor')->orderByRaw("FIELD(dia_semana, 'Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado','Domingo')")->orderBy('hora_inicio')->get()
            : collect();

        return view('encarregados.filho_horario', compact('aluno', 'filhos', 'horarios', 'dias'));
    }

    public function filhoPagamentos(User $aluno)
    {
        $perfil = $this->validarFilho($aluno);
        $filhos = $perfil->alunos()->orderBy('name')->get();
        $aluno->load('turma');
        $pagamentos = $aluno->pagamentos()->orderBy('ano')->orderBy('mes')->get();
        $meses = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        $totalPago = $pagamentos->where('status', 'pago')->sum('valor');
        $totalPendente = $pagamentos->whereIn('status', ['pendente', 'atrasado'])->sum('valor');

        $ano = now()->year;
        $propinaMensal = (float) ($aluno->turma?->propina_mensal ?? 0);
        $totalAno = (float) ($aluno->turma?->propina_anual ?? 0);
        $pagoAno = $pagamentos->where('ano', $ano)->where('status', 'pago')->sum('valor');

        $resumoAno = [
            'totalAno' => $totalAno,
            'pago' => $pagoAno,
            'restante' => max($totalAno - $pagoAno, 0),
            'percentagem' => $totalAno > 0 ? round(min(($pagoAno / $totalAno) * 100, 100), 1) : 0,
        ];

        return view('encarregados.filho_pagamentos', compact('aluno', 'filhos', 'pagamentos', 'meses', 'totalPago', 'totalPendente', 'resumoAno', 'ano'));
    }

    public function filhoPresencas(User $aluno)
    {
        $perfil = $this->validarFilho($aluno);
        $filhos = $perfil->alunos()->orderBy('name')->get();
        $aluno->load('turma');
        $presencas = $aluno->presencas()->where('tipo', 'aluno')->orderBy('ano')->orderBy('mes')->get();
        $meses = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

        $totalPresencas = $presencas->sum('presencas');
        $totalFaltas = $presencas->sum('faltas');
        $totalJustificadas = $presencas->sum('justificadas');
        $taxaAssiduidade = ($totalPresencas + $totalFaltas) > 0
            ? round(($totalPresencas / ($totalPresencas + $totalFaltas)) * 100, 1)
            : null;

        // Cards mensais por mês/ano (com base nas marcações dos professores)
        $mesFiltro = (int) request()->input('mes');
        $anoFiltro = (int) request()->input('ano') ?: now()->year;

        $anosDisponiveis = \App\Models\PresencaAlunoMarcacao::where('aluno_id', $aluno->id)
            ->distinct()
            ->orderByDesc('ano')
            ->pluck('ano')
            ->values()
            ->all();
        if (empty($anosDisponiveis)) {
            $anosDisponiveis = [now()->year];
        }

        $marcacoesQuery = $aluno->presencaAlunoMarcacoes()
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

        return view('encarregados.filho_presencas', compact(
            'aluno', 'filhos', 'presencas', 'meses',
            'totalPresencas', 'totalFaltas', 'totalJustificadas', 'taxaAssiduidade',
            'cardsMensais', 'mesFiltro', 'anoFiltro', 'anosDisponiveis'
        ));
    }
}