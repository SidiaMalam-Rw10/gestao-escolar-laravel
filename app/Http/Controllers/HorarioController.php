<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Turma;
use App\Models\User;
use App\Models\Atividade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HorarioController extends Controller
{
    public const DIAS = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];

    public const TEMPOS = [1, 2, 3, 4, 5];

    public function index()
    {
        $turmas = Turma::withCount('horarios')->withCount('alunos')->orderBy('nome_turma')->get();
        $professores = User::professores()->withCount('horariosComoProfessor')->orderBy('name')->get();

        return view('horarios.index', compact('turmas', 'professores'));
    }

    public function turma(Turma $turma)
    {
        $horarios = $turma->horarios()->with('professor')->get()
            ->sortBy(fn ($aula) => [$this->indiceDia($aula->dia_semana), $aula->tempo ?? $this->indiceHora($aula->hora_inicio)])
            ->values();
        $professores = User::professores()->orderBy('name')->get();

        return view('horarios.turma', compact('turma', 'horarios', 'professores'));
    }

    public function turmaStore(Request $request, Turma $turma)
    {
        $validated = $this->validarAula($request);

        $professor = User::find($validated['professor_id']);
        if (!$professor || !$professor->isProfessor()) {
            return back()->with('error', 'Selecione um professor válido.')->withInput();
        }

        $turma->horarios()->create($validated);

        Atividade::registar('create', "Adicionou a aula '{$validated['disciplina']}' ao horário da turma '{$turma->nome_turma}' ({$validated['dia_semana']} {$validated['hora_inicio']}-{$validated['hora_fim']})");

        return redirect()->route('admin.horarios.turma', $turma)
            ->with('success', 'Aula adicionada ao horário da turma ' . $turma->nome_turma . '!');
    }

    public function professor(User $professor)
    {
        $horarios = $professor->horariosComoProfessor()->with('turma')->get()
            ->sortBy(fn ($aula) => [$this->indiceDia($aula->dia_semana), $aula->tempo ?? $this->indiceHora($aula->hora_inicio)])
            ->values();
        $turmas = Turma::orderBy('nome_turma')->get();

        return view('horarios.professor', compact('professor', 'horarios', 'turmas'));
    }

    public function professorStore(Request $request, User $professor)
    {
        $validated = $this->validarAula($request, true);
        $validated['professor_id'] = $professor->id;

        $horario = Horario::create($validated);

        Atividade::registar('create', "Adicionou a aula '{$horario->disciplina}' ao horário do professor '{$professor->name}' ({$horario->dia_semana} {$horario->hora_inicio}-{$horario->hora_fim})", null, Horario::class, $horario->id);

        return redirect()->route('admin.horarios.professor', $professor)
            ->with('success', 'Aula adicionada ao horário do(a) ' . $professor->name . '!');
    }

    public function destroy(Horario $horario)
    {
        $aula = $horario->disciplina . ' (' . $horario->dia_semana . ' ' . $horario->hora_inicio . '-' . $horario->hora_fim . ')';
        $horario->delete();
        Atividade::registar('delete', "Removeu a aula '{$aula}' do horário");

        return back()->with('success', 'Aula removida do horário.');
    }

    private function indiceDia(?string $dia): int
    {
        $pos = array_search($dia, static::DIAS);

        return $pos === false ? 99 : $pos;
    }

    private function indiceHora($hora): int
    {
        if (!$hora) {
            return 0;
        }
        $partes = explode(':', $hora->format('H:i'));

        return ((int) $partes[0]) * 60 + ((int) $partes[1]);
    }

    private function validarAula(Request $request, bool $paraProfessor = false): array
    {
        $regras = [
            'dia_semana' => ['required', Rule::in(static::DIAS)],
            'tempo' => ['required', 'integer', Rule::in(static::TEMPOS)],
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
            'disciplina' => 'required|string|max:100',
            'sala' => 'nullable|string|max:40',
        ];

        if ($paraProfessor) {
            $regras['turma_id'] = 'required|exists:turmas,id';
        } else {
            $regras['professor_id'] = 'required|exists:users,id';
        }

        return $request->validate($regras);
    }
}