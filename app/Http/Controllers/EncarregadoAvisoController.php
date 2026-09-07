<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\User;
use Illuminate\Http\Request;

class EncarregadoAvisoController extends Controller
{
    public function marcarLido(Request $request, Aviso $aviso)
    {
        $user = auth()->user();

        // Apenas avisos relevantes para os filhos deste encarregado
        if (!$this->avisoRelevantePara($aviso, $user)) {
            abort(403);
        }

        if (!$aviso->foiLidoPor($user)) {
            $user->avisosLidos()->syncWithoutDetaching([
                $aviso->id => ['lido' => true, 'lido_em' => now()],
            ]);
        }

        return back()->with('success', 'Aviso marcado como lido.');
    }

    private function avisoRelevantePara(Aviso $aviso, User $user): bool
    {
        $perfil = $user->perfilEncarregado;
        if (!$perfil) {
            return false;
        }

        $filhos = $perfil->alunos()->get();
        $turmaIds = $filhos->pluck('turma_id')->filter()->values();
        $filhoIds = $filhos->pluck('id');

        return in_array($aviso->destinatario_tipo, ['todos', 'alunos'])
            || ($aviso->destinatario_tipo === 'turma' && $turmaIds->contains($aviso->turma_id))
            || ($aviso->destinatario_tipo === 'individual' && ($filhoIds->contains($aviso->destinatario_id) || $aviso->destinatario_id === $user->id));
    }
}