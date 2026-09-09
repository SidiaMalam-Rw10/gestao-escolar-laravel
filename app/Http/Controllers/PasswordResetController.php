<?php

namespace App\Http\Controllers;

use App\Mail\RedefinirPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function solicitar()
    {
        return view('auth.forgot-password');
    }

    public function enviarLink(Request $request)
    {
        $request->validate([
            'identificador' => 'required|string|max:150',
        ], [
            'identificador.required' => 'Indique o utilizador ou o email da sua conta.',
        ]);

        $user = User::where('username', trim($request->identificador))
            ->orWhere('email', trim($request->identificador))
            ->first();

        if (!$user || !$user->email) {
            return back()->withErrors(['identificador' => 'Não encontrámos nenhuma conta com esse utilizador ou email.']);
        }

        $token = Password::broker()->createToken($user);
        $resetUrl = $request->getSchemeAndHttpHost() . '/reset-password/' . $token . '?email=' . urlencode($user->email);
        Mail::to($user->email)->send(new RedefinirPassword($user, $resetUrl));

        return back()->with('status', 'Enviámos o link de reposição da palavra-passe para o email da sua conta.');
    }

    public function repor(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function efetuarReposicao(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker()->reset(
            $request->only('token', 'email', 'password', 'password_confirmation'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->primeiro_login = false;
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Palavra-passe reposta com sucesso. Inicie sessão.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}