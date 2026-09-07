<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Formulário para enviar feedback ou reportar um problema.
     */
    public function criar(Request $request)
    {
        $tipo = in_array($request->query('tipo'), ['feedback', 'problema']) ? $request->query('tipo') : 'feedback';

        return view('feedbacks.criar', compact('tipo'));
    }

    /**
     * Regista um feedback/problema.
     */
    public function guardar(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:feedback,problema',
            'assunto' => 'required|string|max:150',
            'mensagem' => 'required|string|max:4000',
        ]);

        Feedback::create([
            'user_id' => auth()->id(),
            'tipo' => $validated['tipo'],
            'assunto' => $validated['assunto'],
            'mensagem' => $validated['mensagem'],
            'pagina' => $request->input('pagina') ?: null,
        ]);

        $msg = $validated['tipo'] === 'problema'
            ? 'Problema reportado com sucesso! A equipa vai analisar.'
            : 'O seu feedback foi enviado com sucesso! Obrigado pela colaboração.';

        return redirect()->route('feedbacks.meus')->with('success', $msg);
    }

    /**
     * Lista dos feedbacks/problemas enviados pelo utilizador autenticado.
     */
    public function meus()
    {
        $meusFeedbacks = Feedback::with('user')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('feedbacks.meus', compact('meusFeedbacks'));
    }

    /**
     * Gestão: listagem de todos os feedbacks (admin/diretor).
     */
    public function index(Request $request)
    {
        $query = Feedback::with('user')->latest();

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $feedbacks = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Feedback::count(),
            'novos' => Feedback::where('estado', 'novo')->count(),
            'problemas' => Feedback::where('tipo', 'problema')->count(),
            'resolvidos' => Feedback::where('estado', 'resolvido')->count(),
        ];

        return view('feedbacks.index', compact('feedbacks', 'stats'));
    }

    /**
     * Gestão: alterar estado + responder.
     */
    public function atualizar(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'estado' => 'required|in:novo,em_analise,resolvido',
            'resposta' => 'nullable|string|max:4000',
        ]);

        $feedback->estado = $validated['estado'];
        $feedback->resposta = $validated['resposta'] !== null && $validated['resposta'] !== ''
            ? $validated['resposta']
            : null;
        $feedback->respondido_em = $feedback->resposta ? now() : null;
        $feedback->save();

        return back()->with('success', 'Feedback atualizado com sucesso!');
    }

    /**
     * Gestão: eliminar um feedback.
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return back()->with('success', 'Feedback eliminado com sucesso!');
    }
}