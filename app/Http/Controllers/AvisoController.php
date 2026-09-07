<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Http\Request;

class AvisoController extends Controller
{
    public function index(Request $request)
    {
        $query = Aviso::with('remetente', 'turma')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('mensagem', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('destinatario_tipo', $request->tipo);
        }

        $avisos = $query->paginate(15)->withQueryString();

        return view('avisos.index', compact('avisos'));
    }

    public function create()
    {
        $turmas = Turma::orderBy('nome_turma')->get();
        $alunos = User::alunos()->orderBy('name')->get();
        $encarregados = User::whereHas('perfilEncarregado')
            ->with(['perfilEncarregado.alunos'])
            ->orderBy('name')
            ->get();
        return view('avisos.create', compact('turmas', 'alunos', 'encarregados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string',
            'destinatario_tipo' => 'required|in:todos,alunos,professores,turma,individual',
            'turma_id' => 'nullable|required_if:destinatario_tipo,turma|exists:turmas,id',
            'destinatario_id' => 'nullable|required_if:destinatario_tipo,individual|exists:users,id',
        ]);

        $validated['remetente_id'] = auth()->id();
        $validated['turma_id'] = $validated['turma_id'] ?? null;
        $validated['destinatario_id'] = $validated['destinatario_id'] ?? null;

        Aviso::create($validated);

        return redirect()->route('admin.avisos.index')->with('success', 'Aviso publicado com sucesso!');
    }

    public function historico()
    {
        $user = auth()->user();

        $avisos = $user->avisosRelevantesQuery()
            ->with('remetente', 'turma', 'destinatario')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $lidosIds = $user->avisosLidos()
            ->whereIn('aviso_id', $avisos->pluck('id'))
            ->pluck('aviso_id');

        return view('avisos.historico', compact('avisos', 'lidosIds'));
    }

    public function marcarLido(Request $request, Aviso $aviso)
    {
        $user = auth()->user();

        if (!$user->avisosRelevantesQuery()->whereKey($aviso->id)->exists()) {
            abort(403);
        }

        if (!$aviso->foiLidoPor($user)) {
            $user->avisosLidos()->syncWithoutDetaching([
                $aviso->id => ['lido' => true, 'lido_em' => now()],
            ]);
        }

        return back()->with('success', 'Aviso marcado como lido.');
    }

    public function destroy(Aviso $aviso)
    {
        $aviso->delete();
        return redirect()->route('admin.avisos.index')->with('success', 'Aviso eliminado com sucesso!');
    }
}