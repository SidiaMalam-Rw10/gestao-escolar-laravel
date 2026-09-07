<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Departamento::withCount('users');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('sigla', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'ativo');
        }

        $departamentos = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Departamento::count(),
            'ativos' => Departamento::where('is_active', true)->count(),
            'total_membros' => \DB::table('user_departamento')->count(),
        ];

        return view('departamentos.index', compact('departamentos', 'stats'));
    }

    public function create()
    {
        return view('departamentos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:departamentos,nome',
            'sigla' => 'nullable|string|max:20',
            'descricao' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = true;

        Departamento::create($validated);

        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento criado com sucesso!');
    }

    public function show(Departamento $departamento)
    {
        $departamento->load(['users' => function ($query) {
            $query->orderByPivot('is_principal', 'desc');
        }]);

        return view('departamentos.show', compact('departamento'));
    }

    public function edit(Departamento $departamento)
    {
        return view('departamentos.edit', compact('departamento'));
    }

    public function update(Request $request, Departamento $departamento)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:departamentos,nome,' . $departamento->id,
            'sigla' => 'nullable|string|max:20',
            'descricao' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $departamento->update($validated);

        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento atualizado com sucesso!');
    }

    public function destroy(Departamento $departamento)
    {
        $departamento->delete();

        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento eliminado com sucesso!');
    }

    public function gerirMembros(Departamento $departamento)
    {
        $departamento->load(['users' => function ($query) {
            $query->orderByPivot('is_principal', 'desc');
        }]);

        $users = User::whereNotIn('id', $departamento->users->pluck('id'))
                     ->where('is_active', true)
                     ->orderBy('name')
                     ->get();

        return view('departamentos.membros', compact('departamento', 'users'));
    }

    public function adicionarMembro(Request $request, Departamento $departamento)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cargo' => 'nullable|string|max:100',
            'regime' => 'required|in:regular,especial,tempo_integral,parcial',
            'is_principal' => 'boolean',
            'ano_lectivo' => 'nullable|integer|min:2020|max:2030',
        ]);

        $validated['departamento_id'] = $departamento->id;
        $validated['is_principal'] = $request->boolean('is_principal');

        if ($validated['is_principal']) {
            \DB::table('user_departamento')
                ->where('user_id', $validated['user_id'])
                ->where('departamento_id', $departamento->id)
                ->update(['is_principal' => false]);
        }

        $departamento->users()->attach($validated['user_id'], [
            'cargo' => $validated['cargo'],
            'regime' => $validated['regime'],
            'is_principal' => $validated['is_principal'],
            'ano_lectivo' => $validated['ano_lectivo'] ?? date('Y'),
        ]);

        return back()->with('success', 'Membro adicionado com sucesso!');
    }

    public function atualizarMembro(Request $request, Departamento $departamento, User $user)
    {
        $validated = $request->validate([
            'cargo' => 'nullable|string|max:100',
            'regime' => 'required|in:regular,especial,tempo_integral,parcial',
            'is_principal' => 'boolean',
        ]);

        $validated['is_principal'] = $request->boolean('is_principal');

        if ($validated['is_principal']) {
            \DB::table('user_departamento')
                ->where('departamento_id', $departamento->id)
                ->where('user_id', '!=', $user->id)
                ->update(['is_principal' => false]);
        }

        $departamento->users()->updateExistingPivot($user->id, [
            'cargo' => $validated['cargo'],
            'regime' => $validated['regime'],
            'is_principal' => $validated['is_principal'],
        ]);

        return back()->with('success', 'Membro atualizado com sucesso!');
    }

    public function removerMembro(Departamento $departamento, User $user)
    {
        $departamento->users()->detach($user->id);

        return back()->with('success', 'Membro removido do departamento!');
    }
}
