<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\User;
use Illuminate\Http\Request;

class AtividadeController extends Controller
{
    private const TIPOS = [
        'login' => 'Início de sessão',
        'logout' => 'Fim de sessão',
        'create' => 'Criação',
        'update' => 'Atualização',
        'delete' => 'Eliminação',
        'outro' => 'Outro',
    ];

    public function index(Request $request)
    {
        $query = Atividade::with('user')->latest('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('tipo') && array_key_exists($request->tipo, self::TIPOS)) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }

        if ($request->filled('data_de')) {
            $query->whereDate('created_at', '>=', $request->data_de);
        }

        if ($request->filled('data_ate')) {
            $query->whereDate('created_at', '<=', $request->data_ate);
        }

        $atividades = $query->paginate(25)->withQueryString();
        $utilizadores = User::orderBy('name')->get();
        $tipos = self::TIPOS;

        return view('atividades.index', compact('atividades', 'utilizadores', 'tipos'));
    }
}