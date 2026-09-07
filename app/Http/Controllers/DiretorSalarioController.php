<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DiretorSalarioController extends Controller
{
    private const MESES = [
        1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
    ];

    /**
     * Lista de professores com salários e descontos por faltas.
     */
    public function index(Request $request)
    {
        $mes = (int) $request->query('mes', now()->month);
        $ano = (int) $request->query('ano', now()->year);

        if ($mes < 1) { $mes = 1; }
        if ($mes > 12) { $mes = 12; }

        $professores = User::professores()
            ->orderBy('name')
            ->get()
            ->map(function (User $professor) use ($mes, $ano) {
                $professor->faltas_mes = $professor->faltasProfessor($mes, $ano);
                $professor->desconto_mes = $professor->descontoTotalProfessor($mes, $ano);
                $professor->liquido_mes = $professor->salarioLiquido($mes, $ano);

                return $professor;
            });

        $anosDisponiveis = \App\Models\PresencaMarcacao::distinct()
            ->orderByDesc('ano')
            ->pluck('ano')
            ->values()
            ->all();
        if (empty($anosDisponiveis)) {
            $anosDisponiveis = [now()->year];
        }

        $meses = self::MESES;

        return view('diretor.salarios.index', compact('professores', 'mes', 'ano', 'meses', 'anosDisponiveis'));
    }

    /**
     * Guarda o salário base e o valor descontado por falta de um professor.
     */
    public function update(Request $request, User $professor)
    {
        if (! $professor->isProfessor()) {
            return back()->with('error', 'Esta conta não é de um professor.');
        }

        $validated = $request->validate([
            'salario_base' => 'nullable|numeric|min:0',
            'desconto_por_falta' => 'nullable|numeric|min:0',
        ]);

        $professor->update([
            'salario_base' => $validated['salario_base'] !== null ? $validated['salario_base'] : null,
            'desconto_por_falta' => $validated['desconto_por_falta'] !== null ? $validated['desconto_por_falta'] : null,
        ]);

        return back()->with('success', 'Salário de ' . $professor->name . ' atualizado com sucesso!');
    }
}