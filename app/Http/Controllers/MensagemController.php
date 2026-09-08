<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Mensagem;
use App\Models\User;
use Illuminate\Http\Request;

class MensagemController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $aba = $request->query('aba', 'entrada');

        if ($aba === 'enviadas') {
            $mensagens = Mensagem::with('remetente', 'destinatarios')
                ->where('remetente_id', $user->id)
                ->latest()
                ->paginate(20)
                ->withQueryString();
        } else {
            $mensagens = Mensagem::with('remetente')
                ->whereHas('destinatarios', function ($q) use ($user) {
                    $q->where('mensagem_destinatarios.destinatario_id', $user->id);
                })
                ->latest()
                ->paginate(20)
                ->withQueryString();
        }

        $naoLidas = $user->mensagensNaoLidas()->count();

        $lidasIds = collect();
        if ($aba === 'entrada') {
            $lidasIds = \Illuminate\Support\Facades\DB::table('mensagem_destinatarios')
                ->where('destinatario_id', $user->id)
                ->whereIn('mensagem_id', $mensagens->pluck('id'))
                ->where('lida', true)
                ->pluck('mensagem_id');
        }

        return view('inbox.index', compact('mensagens', 'aba', 'naoLidas', 'lidasIds'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $destinatarios = User::where('is_active', true)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        $preselect = $request->integer('destinatario') ?: null;
        $assunto = $request->query('assunto', '');

        return view('inbox.create', compact('destinatarios', 'preselect', 'assunto'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destinatarios' => 'required|array|min:1',
            'destinatarios.*' => 'required|exists:users,id',
            'assunto' => 'required|string|max:255',
            'corpo' => 'required|string|max:10000',
        ]);

        if (in_array(auth()->id(), $validated['destinatarios'])) {
            return back()->withErrors(['destinatarios' => 'Não pode enviar mensagem a si próprio.'])->withInput();
        }

        $mensagem = Mensagem::create([
            'remetente_id' => auth()->id(),
            'assunto' => $validated['assunto'],
            'corpo' => $validated['corpo'],
        ]);

        $mensagem->destinatarios()->attach($validated['destinatarios']);

        Atividade::registar('create', "Enviou uma mensagem '{$mensagem->assunto}' (" . count($validated['destinatarios']) . " destinatário(s))", null, Mensagem::class, $mensagem->id);

        return redirect()->route('inbox.index')->with('success', 'Mensagem enviada.');
    }

    public function show(Mensagem $mensagem)
    {
        $user = auth()->user();

        $eDestinatario = $mensagem->destinatarios()->wherePivot('destinatario_id', $user->id)->exists();
        $eRemetente = $mensagem->remetente_id === $user->id;

        if (!$eDestinatario && !$eRemetente) {
            abort(403);
        }

        if ($eDestinatario) {
            $mensagem->destinatarios()->updateExistingPivot($user->id, [
                'lida' => true,
                'lida_em' => now(),
            ]);
        }

        $mensagem->load('remetente', 'destinatarios');

        return view('inbox.show', compact('mensagem', 'eRemetente'));
    }

    public function destroy(Mensagem $mensagem)
    {
        if ($mensagem->remetente_id !== auth()->id()) {
            abort(403);
        }

        $assunto = $mensagem->assunto;
        $id = $mensagem->id;
        $mensagem->delete();

        Atividade::registar('delete', "Eliminou a mensagem '{$assunto}'", null, Mensagem::class, $id);

        return redirect()->route('inbox.index')->with('success', 'Mensagem eliminada.');
    }
}