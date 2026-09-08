<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Ficheiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FicheiroController extends Controller
{
    public function index(Request $request)
    {
        $query = Ficheiro::with('usuario')->latest();

        if ($request->filled('search')) {
            $termo = $request->search;
            $query->where(function ($q) use ($termo) {
                $q->where('titulo', 'like', "%{$termo}%")
                  ->orWhere('descricao', 'like', "%{$termo}%")
                  ->orWhere('nome_original', 'like', "%{$termo}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $ficheiros = $query->paginate(12)->withQueryString();

        $total = Ficheiro::count();
        $totalTamanho = Ficheiro::sum('tamanho');
        $podeGerir = auth()->user()->can('gerir_ficheiros');

        return view('ficheiros.index', compact('ficheiros', 'total', 'totalTamanho', 'podeGerir'));
    }

    public function create()
    {
        return view('ficheiros.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:150',
            'descricao' => 'nullable|string|max:5000',
            'categoria' => 'required|in:' . implode(',', array_keys(Ficheiro::CATEGORIAS)),
            'ficheiro' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,odt,ods,epub,rtf,jpg,jpeg,png,webp,zip',
        ]);

        $ficheiro = $request->file('ficheiro');
        $caminho = $ficheiro->store('ficheiros', 'public');

        $registo = Ficheiro::create([
            'titulo' => $validated['titulo'],
            'descricao' => $validated['descricao'] ?? null,
            'categoria' => $validated['categoria'],
            'caminho' => $caminho,
            'nome_original' => $ficheiro->getClientOriginalName(),
            'extensao' => strtolower($ficheiro->getClientOriginalExtension()),
            'tamanho' => $ficheiro->getSize(),
            'user_id' => auth()->id(),
        ]);

        Atividade::registar('create', "Carregou o ficheiro '{$registo->titulo}' na biblioteca", null, Ficheiro::class, $registo->id, ['categoria' => $registo->categoria]);

        return redirect()->route('ficheiros.index')->with('success', 'Ficheiro carregado com sucesso.');
    }

    public function download(Ficheiro $ficheiro)
    {
        $caminho = $ficheiro->caminho;

        return Storage::disk('public')->exists($caminho)
            ? response()->download(storage_path('app/public/' . $caminho), $ficheiro->nome_original)
            : abort(404, 'Ficheiro não encontrado no servidor.');
    }

    public function destroy(Ficheiro $ficheiro)
    {
        $titulo = $ficheiro->titulo;
        Storage::disk('public')->delete($ficheiro->caminho);
        $id = $ficheiro->id;
        $ficheiro->delete();

        Atividade::registar('delete', "Removeu o ficheiro '{$titulo}' da biblioteca", null, Ficheiro::class, $id);

        return back()->with('success', 'Ficheiro removido.');
    }
}