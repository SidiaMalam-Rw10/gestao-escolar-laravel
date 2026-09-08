<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Turma;
use App\Models\Atividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfessorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::professores();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('disciplina', 'like', "%{$search}%")
                  ->orWhere('telefone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('disciplina')) {
            $query->where('disciplina', 'like', "%{$request->disciplina}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'ativo');
        }

        $professores = $query->latest()->paginate(15)->withQueryString();

        $disciplinas = User::professores()
            ->whereNotNull('disciplina')
            ->distinct()
            ->pluck('disciplina')
            ->sort()
            ->values();

        $stats = [
            'total' => User::professores()->count(),
            'ativos' => User::professores()->ativos()->count(),
            'disciplinas' => $disciplinas->count(),
        ];

        return view('professores.index', compact('professores', 'disciplinas', 'stats'));
    }

    public function create()
    {
        $turmas = Turma::orderBy('nome_turma')->get();
        return view('professores.create', compact('turmas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'numero' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'genero' => 'nullable|in:M,F',
            'disciplina' => 'nullable|string|max:100',
            'ano_lectivo' => 'nullable|integer|min:2020|max:2030',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'professor';
        $validated['is_active'] = true;

        $professor = User::create($validated);

        Atividade::registar('create', "Criou o professor '{$professor->name}'", null, User::class, $professor->id, ['username' => $professor->username, 'disciplina' => $professor->disciplina]);

        return redirect()->route('admin.professores.index')->with('success', 'Professor criado com sucesso!');
    }

    public function show(User $professor)
    {
        $professor->load(['turmasResponsavel', 'horariosComoProfessor']);
        return view('professores.show', compact('professor'));
    }

    public function edit(User $professor)
    {
        $turmas = Turma::orderBy('nome_turma')->get();
        return view('professores.edit', compact('professor', 'turmas'));
    }

    public function update(Request $request, User $professor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $professor->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $professor->id,
            'password' => 'nullable|string|min:6|confirmed',
            'numero' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'genero' => 'nullable|in:M,F',
            'disciplina' => 'nullable|string|max:100',
            'ano_lectivo' => 'nullable|integer|min:2020|max:2030',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $professor->update($validated);

        Atividade::registar('update', "Atualizou o professor '{$professor->name}'", null, User::class, $professor->id);

        return redirect()->route('admin.professores.index')->with('success', 'Professor atualizado com sucesso!');
    }

    public function destroy(User $professor)
    {
        $professor->delete();
        Atividade::registar('delete', "Eliminou o professor '{$professor->name}'", null, User::class, $professor->id);
        return redirect()->route('admin.professores.index')->with('success', 'Professor eliminado com sucesso!');
    }

    public function toggleStatus(User $professor)
    {
        $professor->update(['is_active' => !$professor->is_active]);
        $status = $professor->is_active ? 'ativado' : 'desativado';
        $verbo = $professor->is_active ? 'Ativou' : 'Desativou';
        Atividade::registar('update', "{$verbo} o professor '{$professor->name}'", null, User::class, $professor->id, ['is_active' => $professor->is_active]);
        return back()->with('success', "Professor {$status} com sucesso!");
    }
}
