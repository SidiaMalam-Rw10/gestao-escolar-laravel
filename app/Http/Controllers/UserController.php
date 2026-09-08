<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Turma;
use App\Models\Atividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('numero', 'like', "%{$search}%")
                  ->orWhere('telefone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'ativo');
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'ativos' => User::where('is_active', true)->count(),
            'admin' => User::where('role', 'admin')->count(),
            'professores' => User::professores()->count(),
            'alunos' => User::alunos()->count(),
            'auxiliares' => User::where('role', 'auxiliar')->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    public function create()
    {
        $turmas = Turma::all();
        return view('users.create', compact('turmas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,diretor,financeiro,professor,aluno,auxiliar,pctp,encarregado,funcionario,proprietario',
            'roles' => 'nullable|array',
            'roles.*' => 'in:admin,diretor,financeiro,professor,aluno,auxiliar,pctp,encarregado,funcionario,proprietario',
            'numero' => [
                'nullable', 'string', 'max:50',
                Rule::unique('users', 'numero')->where(fn ($q) => $q->where('turma_id', $request->turma_id)),
            ],
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'genero' => 'nullable|in:M,F',
            'disciplina' => 'nullable|string|max:100',
            'salario_base' => 'nullable|numeric|min:0',
            'desconto_por_falta' => 'nullable|numeric|min:0',
            'turma_id' => 'nullable|exists:turmas,id',
            'nivel' => 'nullable|string|max:50',
            'ano_lectivo' => 'nullable|integer|min:2020|max:2030',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;
        $validated['roles'] = $request->input('roles', []);

        $user = User::create($validated);

        Atividade::registar('create', "Criou o utilizador '{$user->name}' ({$user->role})", null, User::class, $user->id, ['username' => $user->username, 'roles' => $user->roles]);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function show(User $user)
    {
        $user->load(['turma', 'encarregado']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $turmas = Turma::all();
        $selectedTurma = $user->turma_id ? $turmas->firstWhere('id', $user->turma_id) : null;
        return view('users.edit', compact('user', 'turmas', 'selectedTurma'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,diretor,financeiro,professor,aluno,auxiliar,pctp,encarregado,funcionario,proprietario',
            'roles' => 'nullable|array',
            'roles.*' => 'in:admin,diretor,financeiro,professor,aluno,auxiliar,pctp,encarregado,funcionario,proprietario',
            'numero' => [
                'nullable', 'string', 'max:50',
                Rule::unique('users', 'numero')->ignore($user->id)->where(fn ($q) => $q->where('turma_id', $request->turma_id)),
            ],
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'genero' => 'nullable|in:M,F',
            'disciplina' => 'nullable|string|max:100',
            'salario_base' => 'nullable|numeric|min:0',
            'desconto_por_falta' => 'nullable|numeric|min:0',
            'turma_id' => 'nullable|exists:turmas,id',
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
        $validated['roles'] = $request->input('roles', []);

        $user->update($validated);

        Atividade::registar('update', "Atualizou o utilizador '{$user->name}'", null, User::class, $user->id, ['username' => $user->username, 'roles' => $user->roles]);

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Não pode eliminar a sua própria conta!');
        }

        $user->delete();

        Atividade::registar('delete', "Eliminou o utilizador '{$user->name}'", null, User::class, $user->id);

        return redirect()->route('admin.users.index')->with('success', 'Usuário eliminado com sucesso!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Não pode desativar a sua própria conta!');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'ativado' : 'desativado';
        $verbo = $user->is_active ? 'Ativou' : 'Desativou';
        Atividade::registar('update', "{$verbo} o utilizador '{$user->name}'", null, User::class, $user->id, ['is_active' => $user->is_active]);
        return back()->with('success', "Usuário {$status} com sucesso!");
    }
}
