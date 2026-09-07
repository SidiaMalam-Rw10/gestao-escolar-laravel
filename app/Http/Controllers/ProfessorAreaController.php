<?php

namespace App\Http\Controllers;

class ProfessorAreaController extends Controller
{
    public const DIAS = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];

    public function meuHorario()
    {
        $horarios = auth()->user()->horariosComoProfessor()
            ->with('turma')
            ->get()
            ->sortBy(function ($aula) {
                $pos = array_search($aula->dia_semana, static::DIAS);

                return [$pos === false ? 99 : $pos, $aula->hora_inicio];
            })
            ->values();

        return view('professor.horario', compact('horarios'));
    }

    public function minhasTurmas()
    {
        $aulas = auth()->user()->horariosComoProfessor()->with(['turma.alunos'])->get();
        $turmas = $aulas->pluck('turma')->filter()->unique('id')->sortBy('nome_turma')->values();
        $aulasPorTurma = $aulas->groupBy('turma_id');

        return view('professor.turmas', compact('turmas', 'aulasPorTurma'));
    }
}