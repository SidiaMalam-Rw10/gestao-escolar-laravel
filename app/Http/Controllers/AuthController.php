<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Atividade;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $utilizador = \App\Models\User::where('username', $request->username)->first();

        if ($utilizador && !$utilizador->is_active) {
            return back()->withErrors([
                'username' => 'A sua conta está desativada. Contacte a administração.',
            ])->onlyInput('username');
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            Atividade::registar('login', 'Início de sessão', Auth::user());

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Credenciais inválidas.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        Atividade::registar('logout', 'Fim de sessão', $user);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
