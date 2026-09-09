<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PrimeiraPasswordController extends Controller
{
    public function show()
    {
        return view('auth.primeira-vez');
    }

    public function alterar(Request $request)
    {
        $validated = $request->validate([
            'password_atual' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($validated['password']),
            'primeiro_login' => false,
        ]);

        Atividade::registar('update', 'Definiu a palavra-passe no primeiro acesso', $user);

        return redirect()->route('dashboard')->with('success', 'Palavra-passe definida com sucesso. Bem-vindo!');
    }
}