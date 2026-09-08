<?php

namespace App\Http\Controllers;

use App\Models\Pagina;
use App\Models\Atividade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class PaginaController extends Controller
{
    public function index(Request $request)
    {
        $query = Pagina::with('autor')->orderBy('tipo')->orderBy('ordem')->orderBy('id');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $paginas = $query->paginate(15)->withQueryString();
        $contagens = Pagina::selectRaw('tipo, COUNT(*) as total')->groupBy('tipo')->pluck('total', 'tipo');

        return view('paginas.index', compact('paginas', 'contagens'));
    }

    public function create()
    {
        return view('paginas.create');
    }

    public function store(Request $request)
    {
        $this->validar($request);

        $dados = $request->except(['_token', 'imagem', 'video']);
        $dados['criado_por'] = auth()->id();
        $dados['imagem'] = null;
        $dados['video_path'] = null;

        if ($request->hasFile('imagem')) {
            $dados['imagem'] = $request->file('imagem')->store('paginas', 'public');
        }

        if ($request->hasFile('video')) {
            $dados['video_path'] = $request->file('video')->store('paginas', 'public');
        }

        $pagina = Pagina::create($dados);

        Atividade::registar('create', "Criou a página '{$pagina->titulo}'", null, Pagina::class, $pagina->id);

        return redirect()->route('admin.paginas.index')->with('success', 'Página criada com sucesso!');
    }

    public function edit(Pagina $pagina)
    {
        return view('paginas.edit', compact('pagina'));
    }

    public function update(Request $request, Pagina $pagina)
    {
        $this->validar($request);

        $dados = $request->except([
            '_token', '_method', 'imagem', 'video', 'remover_imagem', 'remover_video',
        ]);

        if ($request->boolean('remover_imagem') && $pagina->imagem) {
            Storage::disk('public')->delete($pagina->imagem);
            $dados['imagem'] = null;
        } elseif ($request->hasFile('imagem')) {
            if ($pagina->imagem) {
                Storage::disk('public')->delete($pagina->imagem);
            }
            $dados['imagem'] = $request->file('imagem')->store('paginas', 'public');
        }

        if ($request->boolean('remover_video')) {
            if ($pagina->video_url) {
                $dados['video_url'] = null;
            }
            if ($pagina->video_path) {
                Storage::disk('public')->delete($pagina->video_path);
                $dados['video_path'] = null;
            }
        } elseif ($request->hasFile('video')) {
            if ($pagina->video_path) {
                Storage::disk('public')->delete($pagina->video_path);
            }
            $dados['video_path'] = $request->file('video')->store('paginas', 'public');
        }

        $pagina->update($dados);

        Atividade::registar('update', "Atualizou a página '{$pagina->titulo}'", null, Pagina::class, $pagina->id);

        return redirect()->route('admin.paginas.index')->with('success', 'Página atualizada com sucesso!');
    }

    public function destroy(Pagina $pagina)
    {
        if ($pagina->imagem) {
            Storage::disk('public')->delete($pagina->imagem);
        }

        if ($pagina->video_path) {
            Storage::disk('public')->delete($pagina->video_path);
        }

        $titulo = $pagina->titulo;
        $pagina->delete();

        Atividade::registar('delete', "Eliminou a página '{$titulo}'", null, Pagina::class, $pagina->id);

        return redirect()->route('admin.paginas.index')->with('success', 'Página eliminada com sucesso!');
    }

    private function validar(Request $request): array
    {
        return $request->validate(
            [
                'tipo' => ['required', Rule::in(array_keys(Pagina::TIPOS))],
                'titulo' => 'required|string|max:255',
                'conteudo' => 'required|string',
                'ordem' => 'nullable|integer|min:0',
                'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:131072',
                'video' => 'nullable|file|mimes:mp4,webm,ogg,mov|max:131072',
                'video_url' => 'nullable|url|max:1000',
            ],
            [
                'imagem.uploaded' => 'A imagem não pôde ser enviada. Verifique o tamanho (máx. ' . ini_get('upload_max_filesize') . ') e que o servidor foi reiniciado.',
                'imagem.max' => 'A imagem é demasiado grande (máx. ' . ini_get('upload_max_filesize') . ').',
                'imagem.mimes' => 'A imagem deve ser JPG, PNG, GIF ou WEBP.',
                'video.uploaded' => 'O vídeo não pôde ser enviado. Verifique o tamanho (máx. ' . ini_get('upload_max_filesize') . ') e que o servidor foi reiniciado.',
                'video.max' => 'O vídeo é demasiado grande (máx. ' . ini_get('upload_max_filesize') . ').',
                'video.mimes' => 'O vídeo deve ser MP4, WEBM, OGG ou MOV.',
                'video_url.url' => 'O link de vídeo não é um URL válido.',
            ]
        );
    }

    public function horario()
    {
        return $this->mostrar('horario', 'Horário da Escola', 'Horário');
    }

    public function atividades()
    {
        return $this->mostrar('atividades', 'Atividades da Escola', 'Atividades');
    }

    public function sobre()
    {
        return $this->mostrar('sobre', 'Sobre a Escola', 'Sobre a Escola');
    }

    private function mostrar(string $tipo, string $titulo, string $paginaTitulo)
    {
        $secacoes = Pagina::doTipo($tipo)->with('autor')->get();

        return view('paginas.show', compact('tipo', 'secacoes', 'titulo', 'paginaTitulo'));
    }
}