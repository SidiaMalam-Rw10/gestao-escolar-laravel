<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Atividade;

class PerfilController extends Controller
{
    public function index()
    {
        return view('perfil.index');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if ($request->hasFile('foto')) {
            $f = $request->file('foto');
            \Illuminate\Support\Facades\Log::warning('Upload de foto de perfil', [
                'nome' => $f->getClientOriginalName(),
                'tamanho' => $f->getSize(),
                'erro' => $f->getError(),
                'mime_cliente' => $f->getClientMimeType(),
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'telefone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $fotoAntiga = $user->foto;

        $novosDados = $validated;

        if ($request->hasFile('foto')) {
            $novosDados['foto'] = $request->file('foto')->store('perfil', 'public');

            if ($fotoAntiga && Storage::disk('public')->exists($fotoAntiga) && $fotoAntiga !== $novosDados['foto']) {
                Storage::disk('public')->delete($fotoAntiga);
            }
        } else {
            unset($novosDados['foto']);
        }
        $novosDados['email'] = $request->filled('email') ? $request->input('email') : null;

        $user->update($novosDados);

        Atividade::registar('update', 'Atualizou o seu perfil (Nome, E-mail, Telefone ou Foto)', $user);

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }

    public function password(Request $request)
    {
        $validated = $request->validate([
            'password_atual' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->update(['password' => Hash::make($validated['password']), 'primeiro_login' => false]);

        Atividade::registar('update', 'Alterou a sua palavra-passe', $user);

        return back()->with('success', 'Palavra-passe alterada com sucesso.');
    }

    public function removerFoto()
    {
        $user = auth()->user();

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->update(['foto' => null]);

        Atividade::registar('delete', 'Removeu a foto de perfil', $user);

        return back()->with('success', 'Foto de perfil removida.');
    }
}