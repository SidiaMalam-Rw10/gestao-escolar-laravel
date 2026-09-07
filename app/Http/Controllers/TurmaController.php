<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Models\User;
use Illuminate\Http\Request;

class TurmaController extends Controller
{
    public function index(Request $request)
    {
        $query = Turma::with(['professorResponsavel']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome_turma', 'like', "%{$search}%")
                  ->orWhere('nivel', 'like', "%{$search}%");
            });
        }

        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        if ($request->filled('ano_lectivo')) {
            $query->where('ano_lectivo', $request->ano_lectivo);
        }

        $turmas = $query->latest()->paginate(15)->withQueryString();

        $niveis = Turma::distinct()->pluck('nivel')->sort()->values();
        $anos = Turma::distinct()->pluck('ano_lectivo')->sort()->values();

        $stats = [
            'total' => Turma::count(),
            'total_alunos' => User::alunos()->whereNotNull('turma_id')->count(),
            'capacidade_total' => Turma::sum('capacidade'),
        ];

        return view('turmas.index', compact('turmas', 'niveis', 'anos', 'stats'));
    }

    public function create()
    {
        $professores = User::professores()->ativos()->orderBy('name')->get();
        return view('turmas.create', compact('professores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_turma' => 'required|string|max:255',
            'nivel' => 'required|string|max:50',
            'periodo' => 'required|in:Manhã,Tarde,Noite',
            'ano_lectivo' => 'required|integer|min:2020|max:2030',
            'professor_responsavel_id' => 'nullable|exists:users,id',
            'capacidade' => 'nullable|integer|min:1|max:100',
        ]);

        Turma::create($validated);

        return redirect()->route('admin.turmas.index')->with('success', 'Turma criada com sucesso!');
    }

    public function show(Turma $turma)
    {
        $turma->load(['professorResponsavel', 'alunos', 'horarios']);
        return view('turmas.show', compact('turma'));
    }

    public function edit(Turma $turma)
    {
        $professores = User::professores()->ativos()->orderBy('name')->get();
        return view('turmas.edit', compact('turma', 'professores'));
    }

    public function update(Request $request, Turma $turma)
    {
        $validated = $request->validate([
            'nome_turma' => 'required|string|max:255',
            'nivel' => 'required|string|max:50',
            'periodo' => 'required|in:Manhã,Tarde,Noite',
            'ano_lectivo' => 'required|integer|min:2020|max:2030',
            'professor_responsavel_id' => 'nullable|exists:users,id',
            'capacidade' => 'nullable|integer|min:1|max:100',
        ]);

        $turma->update($validated);

        return redirect()->route('admin.turmas.index')->with('success', 'Turma atualizada com sucesso!');
    }

    public function destroy(Turma $turma)
    {
        $turma->delete();
        return redirect()->route('admin.turmas.index')->with('success', 'Turma eliminada com sucesso!');
    }
}
