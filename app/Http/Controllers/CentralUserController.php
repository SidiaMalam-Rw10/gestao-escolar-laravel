<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CentralUserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(20);

        $total = User::count();
        $ativos = User::where('is_active', true)->count();
        $inativos = $total - $ativos;

        // Dados para o gráfico: utilizadores criados nos últimos 12 meses
        $mesesCurtos = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $series = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->startOfMonth()->subMonths($i);
            $series[$d->format('Y-m')] = 0;
        }
        $porMes = User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total")
            ->where('created_at', '>=', now()->startOfMonth()->subMonths(11))
            ->groupBy('mes')
            ->get();
        foreach ($porMes as $reg) {
            if (isset($series[$reg->mes])) {
                $series[$reg->mes] = (int) $reg->total;
            }
        }
        $labelsGrafico = array_map(fn ($k) => $mesesCurtos[(int) substr($k, 5, 2) - 1], array_keys($series));
        $dadosGrafico = array_values($series);

        return view('central.usuarios.index', compact(
            'usuarios',
            'total',
            'ativos',
            'inativos',
            'labelsGrafico',
            'dadosGrafico'
        ));
    }

    public function create()
    {
        return view('central.usuarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'username' => 'required|string|max:50|regex:/^[a-z0-9._-]+$/i|unique:users,username',
            'email' => 'nullable|email|max:150|unique:users,email',
            'telefone' => 'nullable|string|max:30',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'telefone' => $validated['telefone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'proprietario',
            'roles' => [],
            'is_active' => true,
            'primeiro_login' => true,
        ]);

        return redirect()->route('central.usuarios.index')
            ->with('success', 'Utilizador da plataforma criado com sucesso!');
    }

    public function show(Request $request, User $usuario)
    {
        return view('central.usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        return view('central.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'username' => 'required|string|max:50|regex:/^[a-z0-9._-]+$/i|unique:users,username,' . $usuario->id,
            'email' => 'nullable|email|max:150|unique:users,email,' . $usuario->id,
            'telefone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $usuario->update($validated);

        return redirect()->route('central.usuarios.index')
            ->with('success', 'Utilizador da plataforma atualizado com sucesso!');
    }

    public function toggleAtivo(Request $request, User $usuario)
    {
        if ($usuario->id === $request->user()->id) {
            return redirect()->route('central.usuarios.index')
                ->with('error', 'Não pode desativar a própria conta que está a usar.');
        }

        $usuario->update(['is_active' => ! $usuario->is_active]);

        return redirect()->route('central.usuarios.index')
            ->with('success', $usuario->is_active
                ? 'Utilizador «' . $usuario->name . '» reativado com sucesso!'
                : 'Utilizador «' . $usuario->name . '» desativado com sucesso!');
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($usuario->id === $request->user()->id) {
            return redirect()->route('central.usuarios.index')
                ->with('error', 'Não pode eliminar o próprio utilizador que está a usar.');
        }

        $usuario->delete();

        return redirect()->route('central.usuarios.index')
            ->with('success', 'Utilizador da plataforma eliminado com sucesso!');
    }
}