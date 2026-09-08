<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Turma;
use App\Models\Encarregado;
use App\Models\Atividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AlunoController extends Controller
{
    public function index(Request $request)
    {
        $query = User::alunos()->with('turma');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('numero', 'like', "%{$search}%")
                  ->orWhere('telefone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('turma_id')) {
            $query->where('turma_id', $request->turma_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'ativo');
        }

        $alunos = $query->latest()->paginate(15)->withQueryString();
        $turmas = Turma::orderBy('nome_turma')->get();

        $stats = [
            'total' => User::alunos()->count(),
            'ativos' => User::alunos()->ativos()->count(),
            'com_turma' => User::alunos()->whereNotNull('turma_id')->count(),
            'sem_turma' => User::alunos()->whereNull('turma_id')->count(),
        ];

        return view('alunos.index', compact('alunos', 'turmas', 'stats'));
    }

    public function create()
    {
        $turmas = Turma::orderBy('nome_turma')->get();
        $encarregados = Encarregado::orderBy('nome')->get();
        return view('alunos.create', compact('turmas', 'encarregados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'numero' => [
                'nullable', 'string', 'max:50',
                Rule::unique('users', 'numero')->where(fn ($q) => $q->where('turma_id', $request->turma_id)),
            ],
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'genero' => 'nullable|in:M,F',
            'turma_id' => 'nullable|exists:turmas,id',
            'encarregado_id' => 'nullable|exists:encarregados,id',
            'nivel' => 'nullable|string|max:50',
            'ano_lectivo' => 'nullable|integer|min:2020|max:2030',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'aluno';
        $validated['is_active'] = true;

        $aluno = User::create($validated);

        Atividade::registar('create', "Criou o aluno '{$aluno->name}'", null, User::class, $aluno->id, ['username' => $aluno->username, 'turma_id' => $aluno->turma_id]);

        return redirect()->route('admin.alunos.index')->with('success', 'Aluno criado com sucesso!');
    }

    public function show(User $aluno)
    {
        $aluno->load(['turma', 'encarregado', 'notas', 'pagamentos']);
        return view('alunos.show', compact('aluno'));
    }

    public function edit(User $aluno)
    {
        $turmas = Turma::orderBy('nome_turma')->get();
        $encarregados = Encarregado::orderBy('nome')->get();
        return view('alunos.edit', compact('aluno', 'turmas', 'encarregados'));
    }

    public function update(Request $request, User $aluno)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $aluno->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $aluno->id,
            'password' => 'nullable|string|min:6|confirmed',
            'numero' => [
                'nullable', 'string', 'max:50',
                Rule::unique('users', 'numero')->ignore($aluno->id)->where(fn ($q) => $q->where('turma_id', $request->turma_id)),
            ],
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'genero' => 'nullable|in:M,F',
            'turma_id' => 'nullable|exists:turmas,id',
            'encarregado_id' => 'nullable|exists:encarregados,id',
            'nivel' => 'nullable|string|max:50',
            'ano_lectivo' => 'nullable|integer|min:2020|max:2030',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $aluno->update($validated);

        Atividade::registar('update', "Atualizou o aluno '{$aluno->name}'", null, User::class, $aluno->id);

        return redirect()->route('admin.alunos.index')->with('success', 'Aluno atualizado com sucesso!');
    }

    public function destroy(User $aluno)
    {
        $aluno->delete();
        Atividade::registar('delete', "Eliminou o aluno '{$aluno->name}'", null, User::class, $aluno->id);
        return redirect()->route('admin.alunos.index')->with('success', 'Aluno eliminado com sucesso!');
    }

    public function toggleStatus(User $aluno)
    {
        $aluno->update(['is_active' => !$aluno->is_active]);
        $status = $aluno->is_active ? 'ativado' : 'desativado';
        $verbo = $aluno->is_active ? 'Ativou' : 'Desativou';
        Atividade::registar('update', "{$verbo} o aluno '{$aluno->name}'", null, User::class, $aluno->id, ['is_active' => $aluno->is_active]);
        return back()->with('success', "Aluno {$status} com sucesso!");
    }
}
